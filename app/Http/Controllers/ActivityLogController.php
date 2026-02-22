<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;


class ActivityLogController extends Controller
{

    public function index()
    {
        $title = 'Activity Logs';
        $users = User::select('id', 'name')->orderBy('name')->get();
        return view('activity_logs.index', compact('title', 'users'));
    }

    public function show(ActivityLog $activityLog)
    {
        $title = 'Activity Log Detail';
        return view('activity_logs.show', compact('activityLog', 'title'));
    }

    public function datatable(Request $request)
    {
        try {
            $query = ActivityLog::with('user:id,name')->select([
                'id',
                'user_id',
                'method',
                'uri',
                'route_name',
                'route_parameters',
                'status_code',
                'response_content',
                'ip_address',
                'user_agent',
                'created_at',
            ]);

            // Apply filters
            if ($request->filled('method')) {
                $query->where('method', $request->method);
            }

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59'
                ]);
            } elseif ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            } elseif ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('user_id', fn ($row) => $row->user ? $row->user->name : 'Guest')
                ->editColumn('uri', function ($row) {
                    if ($row->uri) {
                        $preview = strlen($row->uri) > 50 ? substr($row->uri, 0, 50) . '...' : $row->uri;
                        return '<span class="text-primary" style="cursor: pointer;" onclick="showModal(\'URI\', ' . htmlspecialchars(json_encode($row->uri)) . ')">' . htmlspecialchars($preview) . '</span>';
                    }
                    return '-';
                })
                ->editColumn('route_parameters', function ($row) {
                    if ($row->route_parameters) {
                        $json = json_encode($row->route_parameters, JSON_PRETTY_PRINT);
                        $preview = strlen($json) > 50 ? substr($json, 0, 50) . '...' : $json;
                        return '<span class="text-primary" style="cursor: pointer;" onclick="showModal(\'Route Parameters\', ' . htmlspecialchars(json_encode($json)) . ')">' . htmlspecialchars($preview) . '</span>';
                    }
                    return '-';
                })
                ->editColumn('response_content', function ($row) {
                    if ($row->response_content) {
                        $preview = strlen($row->response_content) > 50 ? substr($row->response_content, 0, 50) . '...' : $row->response_content;
                        return '<span class="text-primary" style="cursor: pointer;" onclick="showModal(\'Response Content\', ' . htmlspecialchars(json_encode($row->response_content)) . ')">' . htmlspecialchars($preview) . '</span>';
                    }
                    return '-';
                })
                ->editColumn('user_agent', function ($row) {
                    if ($row->user_agent) {
                        $preview = strlen($row->user_agent) > 50 ? substr($row->user_agent, 0, 50) . '...' : $row->user_agent;
                        return '<span class="text-primary" style="cursor: pointer;" onclick="showModal(\'User Agent\', ' . htmlspecialchars(json_encode($row->user_agent)) . ')">' . htmlspecialchars($preview) . '</span>';
                    }
                    return '-';
                })
                ->editColumn('created_at', fn ($row) => $row->created_at->format('Y-m-d H:i:s'))
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('activity-logs.show', $row->id) . '" class="btn btn-sm btn-info">View</a>';
                })
                ->rawColumns(['uri', 'route_parameters', 'response_content', 'user_agent', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cleanup(Request $request)
    {
        try {
            // Use the same logic as the console command
            $days = 1825; // 5 years
            $cutoffDate = Carbon::now()->subDays($days);

            $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} activity log records older than 5 years."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred during cleanup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $filePath = storage_path('app/activity_logs_export.xlsx');

        $writer = new Writer();
        $writer->openToFile($filePath);

        // Create header style
        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(11)
            ->setBackgroundColor(Color::rgb(220, 220, 220));

        // Create data row style
        $dataStyle = new Style();

        // HEADER
        $headerRow = Row::fromValues([
            'No.',
            'User',
            'Method',
            'URI',
            'Route Name',
            'Parameters',
            'Status Code',
            'Response Content',
            'IP Address',
            'User Agent',
            'Timestamp',
        ], $headerStyle);
        $writer->addRow($headerRow);

        // Build query with same filters as datatable
        $query = ActivityLog::with('user:id,name')->select([
            'id',
            'user_id',
            'method',
            'uri',
            'route_name',
            'route_parameters',
            'status_code',
            'response_content',
            'ip_address',
            'user_agent',
            'created_at',
        ]);

        // Apply filters
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get filtered data using chunked processing to avoid memory issues
        $index = 0;
        $query->orderBy('created_at', 'desc')->chunk(1000, function ($logs) use ($writer, $dataStyle, &$index) {
            // DATA ROWS
            foreach ($logs as $log) {
                $index++;

                // Format route parameters
                $routeParams = '';
                if ($log->route_parameters) {
                    $routeParams = json_encode($log->route_parameters, JSON_PRETTY_PRINT);
                }

                // Format response content (truncate if too long)
                $responseContent = $log->response_content;
                if (strlen($responseContent) > 1000) {
                    $responseContent = substr($responseContent, 0, 1000) . '...';
                }

                $dataRow = Row::fromValues([
                    $index,
                    $log->user ? $log->user->name : 'Guest',
                    $log->method,
                    $log->uri,
                    $log->route_name,
                    $routeParams,
                    $log->status_code,
                    $responseContent,
                    $log->ip_address,
                    $log->user_agent,
                    $log->created_at->format('Y-m-d H:i:s'),
                ], $dataStyle);
                $writer->addRow($dataRow);
            }
        });

        $writer->close();

        $filename = 'activity_logs_export';
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $filename .= '_filtered';
        }
        $filename .= '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }
}
