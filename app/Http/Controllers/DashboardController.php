<?php

namespace App\Http\Controllers;

use App\Models\DataInternal;
use App\Models\InvalidData;
use App\Models\simanData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        $match = DB::table('data_internals as di')
                ->join('barangs as b', 'b.id', '=', 'di.barang_id')
                ->leftjoin('unit_kerjas as uk', 'uk.id', '=', 'di.unit_kerja_id')
                ->join('siman_data as s', function ($join) {
                    $join->on('di.barang_id', '=', 's.barang_id')
                        ->on('di.nup', '=', 's.nup')
                        ->on('di.nilai_aset', '=', 's.nilai_perolehan')
                        ->on('di.tgl_perolehan', '=', 's.tgl_perolehan');
                })->join('siman_batches as sb', 'sb.id', '=', 's.import_batch_id')
                ->count();

        $matchnup = DB::table('data_internals as di')
                ->join('barangs as b', 'b.id', '=', 'di.barang_id')
                ->leftjoin('unit_kerjas as uk', 'uk.id', '=', 'di.unit_kerja_id')
                ->join('siman_data as s', function ($join) {
                    $join->on('di.barang_id', '=', 's.barang_id')
                        ->on('di.nup', '=', 's.nup')
                        ;
                })->join('siman_batches as sb', 'sb.id', '=', 's.import_batch_id')
                ->where(function ($q) {

                    // nilai mismatch
                    $q->where(function ($q) {
                        $q->whereColumn('di.nilai_aset', '!=', 's.nilai_perolehan')
                        ->orWhereNull('di.nilai_aset')
                        ->orWhereNull('s.nilai_perolehan');
                    })

                    // AND tgl mismatch
                    ->where(function ($q) {
                        $q->whereColumn('di.tgl_perolehan', '!=', 's.tgl_perolehan')
                        ->orWhereNull('di.tgl_perolehan')
                        ->orWhereNull('s.tgl_perolehan');
                    });

                })
                ->count();

        $matchnilai = DB::table('data_internals as di')
                ->join('barangs as b', 'b.id', '=', 'di.barang_id')
                ->leftjoin('unit_kerjas as uk', 'uk.id', '=', 'di.unit_kerja_id')
                ->join('siman_data as s', function ($join) {
                    $join->on('di.barang_id', '=', 's.barang_id')
                        ->on('di.nup', '=', 's.nup')
                        ->on('di.nilai_aset', '=', 's.nilai_perolehan');
                })->join('siman_batches as sb', 'sb.id', '=', 's.import_batch_id')
                ->where(function ($q) {
                    $q->whereColumn('di.tgl_perolehan', '!=', 's.tgl_perolehan')
                    ->orWhereNull('di.tgl_perolehan')
                    ->orWhereNull('s.tgl_perolehan');
                })
        ->count();

        $matchtgl = DB::table('data_internals as di')
                ->join('barangs as b', 'b.id', '=', 'di.barang_id')
                ->leftjoin('unit_kerjas as uk', 'uk.id', '=', 'di.unit_kerja_id')
                ->join('siman_data as s', function ($join) {
                    $join->on('di.barang_id', '=', 's.barang_id')
                        ->on('di.nup', '=', 's.nup')
                        ->on('di.tgl_perolehan', '=', 's.tgl_perolehan')
                        ;
                })->join('siman_batches as sb', 'sb.id', '=', 's.import_batch_id')
                ->where(function ($q) {
                    $q->whereColumn('di.nilai_aset', '!=', 's.nilai_perolehan')
                    ->orWhereNull('di.nilai_aset')
                    ->orWhereNull('s.nilai_perolehan');
                })
        ->count();

        /**
         * INTERNAL ONLY
         */
        $internalOnly = DB::table('data_internals as di')
                ->join('barangs as b', 'b.id', '=', 'di.barang_id')
                ->join('satkers as sat', 'sat.id', '=', 'di.satker_id')
                ->leftjoin('unit_kerjas as uk', 'uk.id', '=', 'di.unit_kerja_id')
                ->leftJoin('siman_data as s', function ($join) {
                    $join->on('di.barang_id', '=', 's.barang_id')
                        ->on('di.nup', '=', 's.nup');
                })
                ->whereNull('s.id')
        ->count();



        /**
         * SIMAN ONLY
         */
        $simanOnly = DB::table('siman_data as s')
            ->join('barangs as b', 'b.id', '=', 's.barang_id')
            ->join('satkers as sat', 'sat.id', '=', 's.satker_id')
            ->join('bmns as bmn', 'bmn.id', '=', 's.bmn_id')
            ->join('siman_batches as sb', 'sb.id', '=', 's.import_batch_id')
            ->leftJoin('data_internals as di', function ($join) {
                $join->on('di.barang_id', '=', 's.barang_id')
                    ->on('di.nup', '=', 's.nup');
            })
            // ->when($batchSiman, fn ($q) => $q->where('sb.id', $batchSiman))
            // ->when($batchInternal, fn ($q) => $q->where('di.batch', $batchInternal))
            ->whereNull('di.id')
        ->count();

        $title = 'Dashboard';
        $simanCount = simanData::count();
        $internalCount = DataInternal::count();
        $invalidCount = InvalidData::count();

        return view('dashboard.index',
        compact(
            'match',
            'simanOnly',
            'internalOnly',
            'title',
            'simanCount',
            'internalCount',
            'invalidCount',
            'matchnup',
            'matchtgl',
            'matchnilai',
        ));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
