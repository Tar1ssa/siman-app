<?php

namespace App\Http\Controllers;

use App\Models\DataInternal;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Cell;
use App\Models\UnitKerja;

class CompareController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $title = 'Tabel Komparasi';
        // $barang = Barang::get();
        $batchInternal = DataInternal::select('batch')
            ->distinct()
            ->orderBy('batch', 'desc')
            ->select('batch', 'label')
            ->get();

        $batchSiman = DB::table('siman_batches')
            ->orderBy('id', 'desc')
            ->select('id','label')
            ->get(); // atau field batch kamu

        return view('compare.index', [
        'status' => $request->get('status'),
        ] ,  compact('title', 'batchInternal', 'batchSiman'));
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



    public function datatable(Request $request)
    {
        $batchInternal = request('batch_internal');
        $batchSiman    = request('batch_siman');

        // $statusIndex = $this->indexStatus;
        $status =  $request->get('status');

        /**
         * MATCH
         */
        $match = DB::table('data_internals as di')
        ->join('barangs as b', 'b.id', '=', 'di.barang_id')
        ->join('siman_data as s', function ($join) {
            $join->on('di.barang_id', '=', 's.barang_id')
                ->on('di.nup', '=', 's.nup')
                ;
        })
        ->join('siman_batches as sb', 'sb.id', '=', 's.import_batch_id')
        ->when($batchInternal, fn ($q) => $q->where('di.batch', $batchInternal))
        ->when($batchSiman, fn ($q) => $q->where('sb.id', $batchSiman))
        ->select(
            'b.kode_barang',
            'b.nama_barang',
            'di.nup',
            'di.merk',
            DB::raw("CONCAT(s.merk, ' - ', s.tipe) AS merktipe"),
            'di.tgl_perolehan as tgl_internal',
            's.tgl_perolehan as tgl_siman',
            'di.nilai_aset as nilai_internal',
            's.nilai_perolehan as nilai_siman',
            DB::raw('(di.nilai_aset - s.nilai_perolehan) as selisih_nilai'),
            DB::raw("
                CASE
                    WHEN di.tgl_perolehan <=> s.tgl_perolehan
                    AND di.nilai_aset <=> s.nilai_perolehan
                        THEN 'MATCH'

                    WHEN di.nilai_aset <=> s.nilai_perolehan
                    AND NOT (di.tgl_perolehan <=> s.tgl_perolehan)
                        THEN 'MATCH_NILAI'

                    WHEN di.tgl_perolehan <=> s.tgl_perolehan
                    AND NOT (di.nilai_aset <=> s.nilai_perolehan)
                        THEN 'MATCH_TGL'

                    WHEN NOT (di.tgl_perolehan <=> s.tgl_perolehan)
                    AND NOT (di.nilai_aset <=> s.nilai_perolehan)
                        THEN 'MATCH_NUP'
                END AS compare_status
            ")
        );





        /**
         * INTERNAL ONLY
         */
        $internalOnly = DB::table('data_internals as di')
        ->join('barangs as b', 'b.id', '=', 'di.barang_id')
        ->leftJoin('siman_data as s', function ($join) {
            $join->on('di.barang_id', '=', 's.barang_id')
                ->on('di.nup', '=', 's.nup');
        })
        ->leftJoin('siman_batches as sb', 'sb.id', '=', 's.import_batch_id')
        ->when($batchInternal, fn ($q) => $q->where('di.batch', $batchInternal))
        ->when($batchSiman, fn ($q) => $q->where('sb.id', $batchSiman))
        ->whereNull('s.id')
        ->select(
            'b.kode_barang',
            'b.nama_barang',
            'di.nup',
            'di.merk',
            DB::raw('NULL AS merktipe'),
            'di.tgl_perolehan as tgl_internal',
            DB::raw('NULL as tgl_siman'),
            'di.nilai_aset as nilai_internal',
            DB::raw('NULL as nilai_siman'),
            DB::raw('NULL as selisih_nilai'),
            DB::raw("'INTERNAL_ONLY' as compare_status")
        );



        /**
         * SIMAN ONLY
         */
        $simanOnly = DB::table('siman_data as s')
        ->join('barangs as b', 'b.id', '=', 's.barang_id')
        ->join('siman_batches as sb', 'sb.id', '=', 's.import_batch_id')
        ->leftJoin('data_internals as di', function ($join) {
            $join->on('di.barang_id', '=', 's.barang_id')
                ->on('di.nup', '=', 's.nup');
        })
        ->when($batchSiman, fn ($q) => $q->where('sb.id', $batchSiman))
        ->when($batchInternal, fn ($q) => $q->where('di.batch', $batchInternal))
        ->whereNull('di.id')
        ->select(
            'b.kode_barang',
            'b.nama_barang',
            's.nup',
            DB::raw('NULL as merk'),
            DB::raw("CONCAT(s.merk, ' - ', s.tipe) AS merktipe"),
            DB::raw('NULL as tgl_internal'),
            's.tgl_perolehan as tgl_siman',
            DB::raw('NULL as nilai_internal'),
            's.nilai_perolehan as nilai_siman',
            DB::raw('NULL as selisih_nilai'),
            DB::raw("'SIMAN_ONLY' as compare_status")
        );


        /**
         * UNION
         */
        $unionQuery = $match
        ->unionAll($internalOnly)
        ->unionAll($simanOnly);

        $finalQuery = DB::query()->fromSub($unionQuery, 'x');

        if (!empty($status)) {
            $finalQuery->where('compare_status', $status);
        }

        return DataTables::of($finalQuery)
        ->editColumn('nilai_internal', fn ($r) =>
        $r->nilai_internal !== null ? number_format($r->nilai_internal, 0, ',', '.') : '-'
        )
        ->editColumn('nilai_siman', fn ($r) =>
            $r->nilai_siman !== null ? number_format($r->nilai_siman, 0, ',', '.') : '-'
        )
        ->editColumn('selisih_nilai', fn ($r) =>
            $r->selisih_nilai !== null
                ? number_format($r->selisih_nilai, 0, ',', '.')
                : '-'
        )
        // raw numeric for sorting
        ->orderColumn('nilai_internal', 'nilai_internal $1')
        ->orderColumn('nilai_siman', 'nilai_siman $1')
        ->orderColumn('selisih_nilai', 'selisih_nilai $1')

        // ->editColumn('compare_status', function ($row) {
        //     return match ($row->compare_status) {
        //         'MATCH' => '<span class="badge bg-success">MATCH</span>',
        //         'INTERNAL_ONLY' => '<span class="badge bg-warning text-dark">INTERNAL ONLY</span>',
        //         'SIMAN_ONLY' => '<span class="badge bg-danger">SIMAN ONLY</span>',
        //     };
        // })
        ->editColumn('compare_status', function ($row) {
            if ($row->compare_status === 'MATCH') {
                if ((float) $row->selisih_nilai === 0.0) {
                    return '<span class="badge bg-success">MATCH</span>';
                }
                return '<span class="badge bg-warning text-dark">MISMATCH NILAI</span>';
            }

            return match ($row->compare_status) {
                'MATCH' =>
                '<span class="badge bg-success">MATCH</span>',

                'MATCH_NILAI' =>
                '<span class="badge bg-dark text-white">MATCH NILAI</span>',

                'MATCH_TGL' =>
                    '<span class="badge bg-info text-dark">MATCH TGL</span>',

                'MATCH_NUP' =>
                    '<span class="badge bg-white text-dark border border-dark">MATCH NUP ONLY</span>',

                'INTERNAL_ONLY' =>
                    '<span class="badge bg-warning text-dark">INTERNAL ONLY</span>',

                'SIMAN_ONLY' =>
                    '<span class="badge bg-danger">SIMAN ONLY</span>',
            };
        })
        ->rawColumns(['compare_status'])
        ->make(true);

    }

    // export MATCH NUP
    // export MATCH
    private function matchNupSheets($batchInternal, $batchSiman)
    {
        $unitKerjas = UnitKerja::select('id', 'name')->get();

        // add virtual unit kerja for NULL
        $unitKerjas->push((object) [
            'id'   => null,
            'name' => 'Tanpa Unit Kerja',
        ]);

        foreach ($unitKerjas as $unitKerja) {

            $rows = DB::table('data_internals as di')
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
                ->when($batchInternal, fn ($q) => $q->where('di.batch', $batchInternal))
                ->when($batchSiman, fn ($q) => $q->where('sb.id', $batchSiman))
                ->when(
                    is_null($unitKerja->id),
                    fn ($q) => $q->whereNull('di.unit_kerja_id'),
                    fn ($q) => $q->where('di.unit_kerja_id', $unitKerja->id)
                )
                ->select(
                    'b.kode_barang',
                    'b.nama_barang',
                    'di.nup',
                    'di.merk',
                    DB::raw("CONCAT('Merk : ',s.merk, ' - ', 'Tipe : ' , s.tipe, ' - ', 'No Polisi : ' , s.no_polisi) AS uraian_siman"),
                    'di.tgl_perolehan as tgl_internal',
                    's.tgl_perolehan as tgl_siman',
                    'di.nilai_aset as nilai_internal',
                    's.nilai_perolehan as nilai_siman',
                    DB::raw('(di.nilai_aset - s.nilai_perolehan) as selisih_nilai'),
                    DB::raw('COALESCE(uk.name, "Tanpa Unit Kerja") as unit_kerja'),
                    'di.penggunaRaw',
                    's.nama_pengguna',
                    's.kode_register',
                    DB::raw("'MATCH' as status")
                )
                ->orderBy('b.kode_barang')
                ->cursor(); //  streaming

            //  IMPORTANT: skip empty sheets
            if ($rows->isEmpty()) {
                continue;
            }

            yield (($unitKerja->id ? $unitKerja->id . '_' : '') . ($unitKerja->name ?: 'Tanpa Unit Kerja')) => $rows;
        }
    }

    public function exportNupMatch(Request $request)
    {
            $batchInternal = $request->batch_internal;
            $batchSiman    = $request->batch_siman;

            $filePath = storage_path('app/MATCH_NUP.xlsx');

            $writer = new Writer();
            $writer->openToFile($filePath);

            $firstSheet = true;

            foreach ($this->matchNupSheets($batchInternal, $batchSiman) as $sheetName => $rows) {

                // For next sheets, create new one
                if (! $firstSheet) {
                    $writer->addNewSheetAndMakeItCurrent();
                }

                $firstSheet = false;

                // Rename sheet safely
                $writer->getCurrentSheet()->setName($this->sanitizeSheetName($sheetName));

                // HEADER
                $writer->addRow(Row::fromValues([
                    'Kode Barang',
                    'Nama Barang',
                    'NUP',
                    'Uraian Internal',
                    'Uraian SIMAN',
                    'Tgl Internal',
                    'Tgl SIMAN',
                    'Nilai Internal',
                    'Nilai SIMAN',
                    'Selisih Nilai',
                    'Unit Kerja',
                    'Pengguna Internal',
                    'Pengguna SIMAN',
                    'Kode Register SIMAN',
                    'Status',
                ]));


                // DATA ROWS (explicit mapping = safe)
                foreach ($rows as $row) {
                    $writer->addRow(Row::fromValues([
                        $this->sanitizeForExcel($row->kode_barang),
                        $this->sanitizeForExcel($row->nama_barang),
                        $this->sanitizeForExcel($row->nup),
                        $this->sanitizeForExcel($row->merk),
                        $this->sanitizeForExcel($row->uraian_siman),
                        $this->sanitizeForExcel($row->tgl_internal),
                        $this->sanitizeForExcel($row->tgl_siman),
                        $this->sanitizeForExcel($row->nilai_internal),
                        $this->sanitizeForExcel($row->nilai_siman),
                        $this->sanitizeForExcel($row->selisih_nilai),
                        $this->sanitizeForExcel($row->unit_kerja),
                        $this->sanitizeForExcel($row->penggunaRaw),
                        $this->sanitizeForExcel($row->nama_pengguna),
                        $this->sanitizeForExcel($row->kode_register),
                        $this->sanitizeForExcel($row->status),
                    ]));
                }
            }

            $writer->close();

            return response()->download($filePath)->deleteFileAfterSend(true);
    }

    // export MATCH
    private function matchSheets($batchInternal, $batchSiman)
    {
        $unitKerjas = UnitKerja::select('id', 'name')->get();

        // add virtual unit kerja for NULL
        $unitKerjas->push((object) [
            'id'   => null,
            'name' => 'Tanpa Unit Kerja',
        ]);

        foreach ($unitKerjas as $unitKerja) {

            $rows = DB::table('data_internals as di')
                ->join('barangs as b', 'b.id', '=', 'di.barang_id')
                ->leftjoin('unit_kerjas as uk', 'uk.id', '=', 'di.unit_kerja_id')
                ->join('siman_data as s', function ($join) {
                    $join->on('di.barang_id', '=', 's.barang_id')
                        ->on('di.nup', '=', 's.nup')
                        ->on('di.nilai_aset', '=', 's.nilai_perolehan')
                        ->on('di.tgl_perolehan', '=', 's.tgl_perolehan');
                })->join('siman_batches as sb', 'sb.id', '=', 's.import_batch_id')
                ->when($batchInternal, fn ($q) => $q->where('di.batch', $batchInternal))
                ->when($batchSiman, fn ($q) => $q->where('sb.id', $batchSiman))
                ->when(
                    is_null($unitKerja->id),
                    fn ($q) => $q->whereNull('di.unit_kerja_id'),
                    fn ($q) => $q->where('di.unit_kerja_id', $unitKerja->id)
                )
                ->select(
                    'b.kode_barang',
                    'b.nama_barang',
                    'di.nup',
                    'di.merk',
                    DB::raw("CONCAT('Merk : ',s.merk, ' - ', 'Tipe : ' , s.tipe, ' - ', 'No Polisi : ' , s.no_polisi) AS uraian_siman"),
                    'di.tgl_perolehan as tgl_internal',
                    's.tgl_perolehan as tgl_siman',
                    'di.nilai_aset as nilai_internal',
                    's.nilai_perolehan as nilai_siman',
                    DB::raw('(di.nilai_aset - s.nilai_perolehan) as selisih_nilai'),
                    DB::raw('COALESCE(uk.name, "Tanpa Unit Kerja") as unit_kerja'),
                    'di.penggunaRaw',
                    's.nama_pengguna',
                    's.kode_register',
                    DB::raw("'MATCH' as status")
                )
                ->orderBy('b.kode_barang')
                ->cursor(); //  streaming

            //  IMPORTANT: skip empty sheets
            if ($rows->isEmpty()) {
                continue;
            }

            yield (($unitKerja->id ? $unitKerja->id . '_' : '') . ($unitKerja->name ?: 'Tanpa Unit Kerja')) => $rows;
        }
    }

    public function exportMatch(Request $request)
    {
            $batchInternal = $request->batch_internal;
            $batchSiman    = $request->batch_siman;

            $filePath = storage_path('app/MATCH.xlsx');

            $writer = new Writer();
            $writer->openToFile($filePath);

            $firstSheet = true;

            foreach ($this->matchSheets($batchInternal, $batchSiman) as $sheetName => $rows) {

                // For next sheets, create new one
                if (! $firstSheet) {
                    $writer->addNewSheetAndMakeItCurrent();
                }

                $firstSheet = false;

                // Rename sheet safely
                $writer->getCurrentSheet()->setName($this->sanitizeSheetName($sheetName));

                // HEADER
                $writer->addRow(Row::fromValues([
                    'Kode Barang',
                    'Nama Barang',
                    'NUP',
                    'Uraian Internal',
                    'Uraian SIMAN',
                    'Tgl Internal',
                    'Tgl SIMAN',
                    'Nilai Internal',
                    'Nilai SIMAN',
                    'Selisih Nilai',
                    'Unit Kerja',
                    'Pengguna Internal',
                    'Pengguna SIMAN',
                    'Kode Register SIMAN',
                    'Status',
                ]));


                // DATA ROWS (explicit mapping = safe)
                foreach ($rows as $row) {
                    $writer->addRow(Row::fromValues([
                        $this->sanitizeForExcel($row->kode_barang),
                        $this->sanitizeForExcel($row->nama_barang),
                        $this->sanitizeForExcel($row->nup),
                        $this->sanitizeForExcel($row->merk),
                        $this->sanitizeForExcel($row->uraian_siman),
                        $this->sanitizeForExcel($row->tgl_internal),
                        $this->sanitizeForExcel($row->tgl_siman),
                        $this->sanitizeForExcel($row->nilai_internal),
                        $this->sanitizeForExcel($row->nilai_siman),
                        $this->sanitizeForExcel($row->selisih_nilai),
                        $this->sanitizeForExcel($row->unit_kerja),
                        $this->sanitizeForExcel($row->penggunaRaw),
                        $this->sanitizeForExcel($row->nama_pengguna),
                        $this->sanitizeForExcel($row->kode_register),
                        $this->sanitizeForExcel($row->status),
                    ]));
                }
            }

            $writer->close();

            return response()->download($filePath)->deleteFileAfterSend(true);
    }

    // export match nilai (mismatch tgl)
    private function matchNilaiSheets($batchInternal, $batchSiman)
    {
        $unitKerjas = UnitKerja::select('id', 'name')->get();

        // add virtual unit kerja for NULL
        $unitKerjas->push((object) [
            'id'   => null,
            'name' => 'Tanpa Unit Kerja',
        ]);

        foreach ($unitKerjas as $unitKerja) {

            $rows = DB::table('data_internals as di')
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
                ->when($batchInternal, fn ($q) => $q->where('di.batch', $batchInternal))
                ->when($batchSiman, fn ($q) => $q->where('sb.id', $batchSiman))
                ->when(
                    is_null($unitKerja->id),
                    fn ($q) => $q->whereNull('di.unit_kerja_id'),
                    fn ($q) => $q->where('di.unit_kerja_id', $unitKerja->id)
                )
                ->select(
                    'b.kode_barang',
                    'b.nama_barang',
                    'di.nup',
                    'di.merk',
                    DB::raw("CONCAT('Merk : ',s.merk, ' - ', 'Tipe : ' , s.tipe, ' - ', 'No Polisi : ' , s.no_polisi) AS uraian_siman"),
                    'di.tgl_perolehan as tgl_internal',
                    's.tgl_perolehan as tgl_siman',
                    'di.nilai_aset as nilai_internal',
                    's.nilai_perolehan as nilai_siman',
                    DB::raw('(di.nilai_aset - s.nilai_perolehan) as selisih_nilai'),
                    DB::raw('COALESCE(uk.name, "Tanpa Unit Kerja") as unit_kerja'),
                    'di.penggunaRaw',
                    's.nama_pengguna',
                    's.kode_register',
                    DB::raw("'MATCH_NILAI' as status")
                )
                ->orderBy('b.kode_barang')
                ->cursor(); //  streaming

            //  IMPORTANT: skip empty sheets
            if ($rows->isEmpty()) {
                continue;
            }

            yield (($unitKerja->id ? $unitKerja->id . '_' : '') . ($unitKerja->name ?: 'Tanpa Unit Kerja')) => $rows;
        }
    }

    public function exportMatchNilai(Request $request)
    {
            $batchInternal = $request->batch_internal;
            $batchSiman    = $request->batch_siman;

            $filePath = storage_path('app/MATCH_NIlAI.xlsx');

            $writer = new Writer();
            $writer->openToFile($filePath);

            $firstSheet = true;

            foreach ($this->matchNilaiSheets($batchInternal, $batchSiman) as $sheetName => $rows) {

                // For next sheets, create new one
                if (! $firstSheet) {
                    $writer->addNewSheetAndMakeItCurrent();
                }

                $firstSheet = false;

                // Rename sheet safely
                $writer->getCurrentSheet()->setName($this->sanitizeSheetName($sheetName));

                // HEADER
                $writer->addRow(Row::fromValues([
                    'Kode Barang',
                    'Nama Barang',
                    'NUP',
                    'Uraian Internal',
                    'Uraian SIMAN',
                    'Tgl Internal',
                    'Tgl SIMAN',
                    'Nilai Internal',
                    'Nilai SIMAN',
                    'Selisih Nilai',
                    'Unit Kerja',
                    'Pengguna Internal',
                    'Pengguna SIMAN',
                    'Kode Register SIMAN',
                    'Status',
                ]));


                // DATA ROWS (explicit mapping = safe)
                foreach ($rows as $row) {
                    $writer->addRow(Row::fromValues([
                        $this->sanitizeForExcel($row->kode_barang),
                        $this->sanitizeForExcel($row->nama_barang),
                        $this->sanitizeForExcel($row->nup),
                        $this->sanitizeForExcel($row->merk),
                        $this->sanitizeForExcel($row->uraian_siman),
                        $this->sanitizeForExcel($row->tgl_internal),
                        $this->sanitizeForExcel($row->tgl_siman),
                        $this->sanitizeForExcel($row->nilai_internal),
                        $this->sanitizeForExcel($row->nilai_siman),
                        $this->sanitizeForExcel($row->selisih_nilai),
                        $this->sanitizeForExcel($row->unit_kerja),
                        $this->sanitizeForExcel($row->penggunaRaw),
                        $this->sanitizeForExcel($row->nama_pengguna),
                        $this->sanitizeForExcel($row->kode_register),
                        $this->sanitizeForExcel($row->status),
                    ]));
                }
            }

            $writer->close();

            return response()->download($filePath)->deleteFileAfterSend(true);
    }

    // export mismatch tgl_perolehan
    private function matchTglSheets($batchInternal, $batchSiman)
    {
        $unitKerjas = UnitKerja::select('id', 'name')->get();

        // add virtual unit kerja for NULL
        $unitKerjas->push((object) [
            'id'   => null,
            'name' => 'Tanpa Unit Kerja',
        ]);

        foreach ($unitKerjas as $unitKerja) {

            $rows = DB::table('data_internals as di')
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
                ->when($batchInternal, fn ($q) => $q->where('di.batch', $batchInternal))
                ->when($batchSiman, fn ($q) => $q->where('sb.id', $batchSiman))
                ->when(
                    is_null($unitKerja->id),
                    fn ($q) => $q->whereNull('di.unit_kerja_id'),
                    fn ($q) => $q->where('di.unit_kerja_id', $unitKerja->id)
                )
                ->select(
                    'b.kode_barang',
                    'b.nama_barang',
                    'di.nup',
                    'di.merk',
                    DB::raw("CONCAT('Merk : ',s.merk, ' - ', 'Tipe : ' , s.tipe, ' - ', 'No Polisi : ' , s.no_polisi) AS uraian_siman"),
                    'di.tgl_perolehan as tgl_internal',
                    's.tgl_perolehan as tgl_siman',
                    'di.nilai_aset as nilai_internal',
                    's.nilai_perolehan as nilai_siman',
                    DB::raw('(di.nilai_aset - s.nilai_perolehan) as selisih_nilai'),
                    DB::raw('COALESCE(uk.name, "Tanpa Unit Kerja") as unit_kerja'),
                    'di.penggunaRaw',
                    's.nama_pengguna',
                    's.kode_register',
                    DB::raw("'MATCH_TGL_MISMATCH' as status")
                )
                ->orderBy('b.kode_barang')
                ->cursor(); //  streaming

            //  IMPORTANT: skip empty sheets
            if ($rows->isEmpty()) {
                continue;
            }

            yield (($unitKerja->id ? $unitKerja->id . '_' : '') . ($unitKerja->name ?: 'Tanpa Unit Kerja')) => $rows;
        }
    }

    public function exportMatchTgl(Request $request)
    {
            $batchInternal = $request->batch_internal;
            $batchSiman    = $request->batch_siman;

            $filePath = storage_path('app/MATCH_tgl.xlsx');

            $writer = new Writer();
            $writer->openToFile($filePath);

            $firstSheet = true;

            foreach ($this->matchTglSheets($batchInternal, $batchSiman) as $sheetName => $rows) {

                // For next sheets, create new one
                if (! $firstSheet) {
                    $writer->addNewSheetAndMakeItCurrent();
                }

                $firstSheet = false;

                // Rename sheet safely
                $writer->getCurrentSheet()->setName($this->sanitizeSheetName($sheetName));

                // HEADER
                $writer->addRow(Row::fromValues([
                    'Kode Barang',
                    'Nama Barang',
                    'NUP',
                    'Uraian Internal',
                    'Uraian SIMAN',
                    'Tgl Internal',
                    'Tgl SIMAN',
                    'Nilai Internal',
                    'Nilai SIMAN',
                    'Selisih Nilai',
                    'Unit Kerja',
                    'Pengguna Internal',
                    'Pengguna SIMAN',
                    'Kode Register SIMAN',
                    'Status',
                ]));


                // DATA ROWS (explicit mapping = safe)
                foreach ($rows as $row) {
                    $writer->addRow(Row::fromValues([
                        $this->sanitizeForExcel($row->kode_barang),
                        $this->sanitizeForExcel($row->nama_barang),
                        $this->sanitizeForExcel($row->nup),
                        $this->sanitizeForExcel($row->merk),
                        $this->sanitizeForExcel($row->uraian_siman),
                        $this->sanitizeForExcel($row->tgl_internal),
                        $this->sanitizeForExcel($row->tgl_siman),
                        $this->sanitizeForExcel($row->nilai_internal),
                        $this->sanitizeForExcel($row->nilai_siman),
                        $this->sanitizeForExcel($row->selisih_nilai),
                        $this->sanitizeForExcel($row->unit_kerja),
                        $this->sanitizeForExcel($row->penggunaRaw),
                        $this->sanitizeForExcel($row->nama_pengguna),
                        $this->sanitizeForExcel($row->kode_register),
                        $this->sanitizeForExcel($row->status),
                    ]));
                }
            }

            $writer->close();

            return response()->download($filePath)->deleteFileAfterSend(true);
    }


    // export SIMAN Only
    private function simanOnlyRows($batchInternal, $batchSiman)
    {
        return DB::table('siman_data as s')
            ->join('barangs as b', 'b.id', '=', 's.barang_id')
            ->join('satkers as sat', 'sat.id', '=', 's.satker_id')
            ->join('bmns as bmn', 'bmn.id', '=', 's.bmn_id')
            ->join('siman_batches as sb', 'sb.id', '=', 's.import_batch_id')
            ->leftJoin('data_internals as di', function ($join) use ($batchInternal) {
                $join->on('di.barang_id', '=', 's.barang_id')
                    ->on('di.nup', '=', 's.nup')
                    ->when($batchInternal, fn ($j) => $j->where('di.batch', $batchInternal));
            })
            ->when($batchSiman, fn ($q) => $q->where('sb.id', $batchSiman))
            ->whereNull('di.id')
            ->select(
                'bmn.name',
                'sat.kode_satker',
                'sat.nama_satker',
                'b.kode_barang',
                'b.nama_barang',
                's.nup',
                's.merk',
                's.tipe',
                's.kondisi',
                's.no_dokumen',
                's.no_BPKP',
                's.no_polisi',
                's.no_sertifikat',
                's.tgl_perolehan',
                's.nilai_perolehan',
                's.nilai_penyusutan',
                's.nilai_buku',
                's.kode_register',
                's.lokasi_ruang',
                's.update_lokasi_ruang',
                's.update_kondisi',
                's.nama_pengguna',
                's.link_dokumentasi',
                DB::raw("'SIMAN_ONLY' as status")
            )
            ->orderBy('b.kode_barang')
            ->cursor(); //  LazyCollection
    }


    public function exportSimanOnly(Request $request)
    {
        $batchInternal = $request->batch_internal;
        $batchSiman    = $request->batch_siman;

        $filePath = storage_path('app/siman_only.xlsx');

        $rows = $this->simanOnlyRows($batchInternal, $batchSiman);

        // Optional guard
        if ($rows->isEmpty()) {
            return back()->with('error', 'Tidak ada data SIMAN ONLY');
        }

        $writer = new Writer();
        $writer->openToFile($filePath);

        // HEADER
        $writer->addRow(Row::fromValues([
            'Jenis BMN',
            'Kode Satker',
            'Nama Satker',
            'Kode Barang',
            'Nama Barang',
            'NUP',
            'Merk',
            'Tipe',
            'Kondisi',
            'No Dokumen',
            'No BPKP',
            'No Polisi',
            'No Sertifikat',
            'Tgl Perolehan',
            'Nilai Perolehan',
            'Nilai Penyusutan',
            'Nilai Buku',
            'Kode Register',
            'Lokasi Ruang',
            'Update Lokasi Ruang',
            'Update Kondisi',
            'Nama Pengguna',
            'Link Dokumentasi',
            'Status',
        ]));

        // DATA (streaming-safe)
        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues([
                $this->sanitizeForExcel($row->name),
                $this->sanitizeForExcel($row->kode_satker),
                $this->sanitizeForExcel($row->nama_satker),
                $this->sanitizeForExcel($row->kode_barang),
                $this->sanitizeForExcel($row->nama_barang),
                $this->sanitizeForExcel($row->nup),
                $this->sanitizeForExcel($row->merk),
                $this->sanitizeForExcel($row->tipe),
                $this->sanitizeForExcel($row->kondisi),
                $this->sanitizeForExcel($row->no_dokumen),
                $this->sanitizeForExcel($row->no_BPKP),
                $this->sanitizeForExcel($row->no_polisi),
                $this->sanitizeForExcel($row->no_sertifikat),
                $this->sanitizeForExcel($row->tgl_perolehan),
                $this->sanitizeForExcel($row->nilai_perolehan),
                $this->sanitizeForExcel($row->nilai_penyusutan),
                $this->sanitizeForExcel($row->nilai_buku),
                $this->sanitizeForExcel($row->kode_register),
                $this->sanitizeForExcel($row->lokasi_ruang),
                $this->sanitizeForExcel($row->update_lokasi_ruang),
                $this->sanitizeForExcel($row->update_kondisi),
                $this->sanitizeForExcel($row->nama_pengguna),
                $this->sanitizeForExcel($row->link_dokumentasi),
                $this->sanitizeForExcel($row->status),
            ]));
        }

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }




    // export internal Only
    private function internalOnlySheets($batchInternal, $batchSiman)
    {
        $unitKerjas = UnitKerja::select('id', 'name')->get();

        // add virtual unit kerja for NULL
        $unitKerjas->push((object) [
            'id'   => null,
            'name' => 'Tanpa Unit Kerja',
        ]);

        $usedSheetNames = [];

        foreach ($unitKerjas as $unitKerja) {
            $rows = DB::table('data_internals as di')
                ->join('barangs as b', 'b.id', '=', 'di.barang_id')
                ->leftJoin('satkers as sat', 'sat.id', '=', 'di.satker_id')
                ->leftJoin('lokasi_ruangs as lokasi', 'lokasi.id', '=', 'di.lokasi_id')
                ->leftjoin('unit_kerjas as uk', 'uk.id', '=', 'di.unit_kerja_id')
                ->leftJoin('siman_data as s', function ($join) use ($batchSiman) {
                    $join->on('di.barang_id', '=', 's.barang_id')
                        ->on(DB::raw('CAST(di.nup AS CHAR)'), '=', 's.nup')
                        ->when($batchSiman, fn ($j) => $j->where('s.import_batch_id', $batchSiman));
                })
                ->whereNull('s.id')
                ->when($batchInternal, fn ($q) => $q->where('di.batch', $batchInternal))
                ->when(
                    is_null($unitKerja->id),
                    fn ($q) => $q->whereNull('di.unit_kerja_id'),
                    fn ($q) => $q->where('di.unit_kerja_id', $unitKerja->id)
                )
                ->select([
                    'sat.kode_satker',
                    'b.kode_barang',
                    'b.nama_barang',
                    'di.nup',
                    'di.merk',
                    'di.jumlah',
                    'di.tgl_perolehan',
                    'di.nilai_aset',
                    'di.nilai_penyusutan',
                    'di.nilai_buku',
                    'di.kondisi',
                    'di.akun_neraca',
                    'di.pembukuan',
                    DB::raw('COALESCE(uk.name, "Tanpa Unit Kerja") as unit_kerja'),
                    'di.penggunaRaw',
                    'lokasi.name as lokasi_ruang',
                    'di.status_inven',
                    'di.update_kondisi',
                    'di.link_dokumentasi',
                    'di.link_lhi',
                    'di.no_bahi',
                    'di.tgl_bahi',
                    DB::raw("'INTERNAL_ONLY' as status"),
                ])
                ->orderBy('b.kode_barang')
                ->cursor();

            if ($rows->isNotEmpty()) {
                $baseSheetName = $this->sanitizeSheetName($unitKerja->name ?: 'Tanpa Unit Kerja');
                $sheetName = $baseSheetName;
                $suffix = 1;
                while (in_array($sheetName, $usedSheetNames)) {
                    $sheetName = $baseSheetName . '_' . $suffix;
                    $suffix++;
                }
                $usedSheetNames[] = $sheetName;
                yield $sheetName => $rows;
            }
        }
    }

    public function exportInternalOnly(Request $request)
    {
        $batchInternal = $request->batch_internal ?? null;
        $batchSiman    = $request->batch_siman ?? null;

        $filePath = storage_path('app/internal_only.xlsx');

        $writer = new Writer();
        $writer->openToFile($filePath);

        $firstSheet = true;
        $sheetNames = [];
        $sheetWritten = false;
        foreach ($this->internalOnlySheets($batchInternal, $batchSiman) as $sheetName => $rows) {
            // Guarantee unique sheet names
            $baseSheetName = $this->sanitizeSheetName($sheetName);
            $uniqueSheetName = $baseSheetName;
            $suffix = 1;
            while (in_array($uniqueSheetName, $sheetNames)) {
                $uniqueSheetName = $baseSheetName . '_' . $suffix;
                $suffix++;
            }
            $sheetNames[] = $uniqueSheetName;

            if (!$firstSheet) {
                $writer->addNewSheetAndMakeItCurrent();
            }
            $firstSheet = false;
            $writer->getCurrentSheet()->setName($uniqueSheetName);
            $writer->addRow(Row::fromValues([
                'Kode Satker',
                'Kode Barang',
                'Nama Barang',
                'NUP',
                'Merk',
                'Jumlah',
                'Tgl Perolehan',
                'Nilai Aset',
                'Nilai Penyusutan',
                'Nilai Buku',
                'Kondisi',
                'Akun Neraca',
                'Pembukuan',
                'Unit Kerja',
                'Pengguna',
                'Lokasi Ruang',
                'Status Inventaris',
                'Update Kondisi',
                'Link Dokumentasi',
                'Link LHI',
                'No BAHI',
                'Tgl BAHI',
                'Status',
            ]));
            $rowCount = 0;
            foreach ($rows as $row) {
                $writer->addRow(Row::fromValues([
                    $this->sanitizeForExcel($row->kode_satker),
                    $this->sanitizeForExcel($row->kode_barang),
                    $this->sanitizeForExcel($row->nama_barang),
                    $this->sanitizeForExcel($row->nup),
                    $this->sanitizeForExcel($row->merk),
                    $this->sanitizeForExcel($row->jumlah),
                    $this->sanitizeForExcel($row->tgl_perolehan),
                    $this->sanitizeForExcel($row->nilai_aset),
                    $this->sanitizeForExcel($row->nilai_penyusutan),
                    $this->sanitizeForExcel($row->nilai_buku),
                    $this->sanitizeForExcel($row->kondisi),
                    $this->sanitizeForExcel($row->akun_neraca),
                    $this->sanitizeForExcel($row->pembukuan),
                    $this->sanitizeForExcel($row->unit_kerja),
                    $this->sanitizeForExcel($row->penggunaRaw),
                    $this->sanitizeForExcel($row->lokasi_ruang),
                    $this->sanitizeForExcel($row->status_inven),
                    $this->sanitizeForExcel($row->update_kondisi),
                    $this->sanitizeForExcel($row->link_dokumentasi),
                    $this->sanitizeForExcel($row->link_lhi),
                    $this->sanitizeForExcel($row->no_bahi),
                    $this->sanitizeForExcel($row->tgl_bahi),
                    $this->sanitizeForExcel($row->status),
                ]));
                $rowCount++;
            }
            if ($rowCount > 0) {
                $sheetWritten = true;
            }
        }
        // If no sheet with data was written, write a dummy sheet
        if (!$sheetWritten) {
            if (!$firstSheet) {
                $writer->addNewSheetAndMakeItCurrent();
            }
            $writer->getCurrentSheet()->setName('No_Data');
            $writer->addRow(Row::fromValues(['No data found for the selected filter.']));
        }
        $writer->close();
        return response()->download($filePath)->deleteFileAfterSend(true);
    }




    private function sanitizeForExcel($value)
    {
        if ($value === null) {
            return '';
        }

        // Convert to string
        $value = (string) $value;

        // Clean invalid UTF-8 sequences
        $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);

        // Remove invisible Unicode characters that can cause Excel XML errors
        $value = preg_replace('/[\x{200B}-\x{200D}]/u', '', $value);

        // Trim whitespace
        $value = trim($value);

        // Remove all non-printable characters except space (32), tab (9), newline (10), carriage return (13)
        $value = preg_replace('/[^\x20-\x7E\x09\x0A\x0D]/u', '', $value);

        // Limit length to prevent Excel issues (Excel has a 32,767 character limit per cell)
        if (strlen($value) > 30000) {
            $value = substr($value, 0, 30000) . '...';
        }

        return $value;
    }

    private function sanitizeSheetName($name)
    {
        // Remove invalid characters for Excel sheet names: keep only letters, numbers, space, underscore, dash
        $name = preg_replace('/[^a-zA-Z0-9 \-_]/', '_', $name);

        // Trim and replace multiple spaces/underscores with single
        $name = preg_replace('/[ _]+/', '_', trim($name));

        // If empty, use default
        if (empty($name)) {
            $name = 'Sheet';
        }

        // Limit to 31 chars
        return substr($name, 0, 31);
    }

}
