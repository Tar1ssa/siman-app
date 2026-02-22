<?php

namespace App\Http\Controllers;

use App\Models\DataInternal;
use App\Models\InvalidData;
use App\Models\simanData;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {


    //     $match = DB::table('data_internals as di')
    //             ->join('barangs as b', 'b.id', '=', 'di.barang_id')
    //             ->leftjoin('unit_kerjas as uk', 'uk.id', '=', 'di.unit_kerja_id')
    //             ->join('siman_data as s', function ($join) {
    //                 $join->on('di.barang_id', '=', 's.barang_id')
    //                     ->on('di.nup', '=', 's.nup')
    //                     ->on('di.nilai_aset', '=', 's.nilai_perolehan')
    //                     ->on('di.tgl_perolehan', '=', 's.tgl_perolehan');
    //             })->join('siman_batches as sb', 'sb.id', '=', 's.import_batch_id')
    //             ->count();

    //     $matchnup = DB::table('data_internals as di')
    //             ->join('barangs as b', 'b.id', '=', 'di.barang_id')
    //             ->leftjoin('unit_kerjas as uk', 'uk.id', '=', 'di.unit_kerja_id')
    //             ->join('siman_data as s', function ($join) {
    //                 $join->on('di.barang_id', '=', 's.barang_id')
    //                     ->on('di.nup', '=', 's.nup')
    //                     ;
    //             })->join('siman_batches as sb', 'sb.id', '=', 's.import_batch_id')
    //             ->where(function ($q) {

    //                 // nilai mismatch
    //                 $q->where(function ($q) {
    //                     $q->whereColumn('di.nilai_aset', '!=', 's.nilai_perolehan')
    //                     ->orWhereNull('di.nilai_aset')
    //                     ->orWhereNull('s.nilai_perolehan');

    public function index(Request $request)
                    {
                        // ...existing code for match, simanOnly, internalOnly, etc...

                        // Unit kerja list for filter
                        $unitKerjaList = \App\Models\UnitKerja::all();
                        $unitKerjaId = $request->input('unit_kerja');

                        // Query for kondisi counts, filtered by unit kerja if selected
                        $query = DataInternal::query();
                        if ($unitKerjaId) {
                            $query->where('unit_kerja_id', $unitKerjaId);
                        }
                        $countBaik = (clone $query)->where('kondisi', 'B')->count();
                        $countRusakRingan = (clone $query)->where('kondisi', 'RR')->count();
                        $countRusakBerat = (clone $query)->where('kondisi', 'RB')->count();

                        // ...existing code for match, simanOnly, internalOnly, etc...
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
                            'unitKerjaList',
                            'countBaik',
                            'countRusakRingan',
                            'countRusakBerat',
                        ));

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
    /**
     * AJAX endpoint for kondisi barang status counts
     */
    public function kondisiBarangStatus(Request $request)
    {
        $unitKerjaId = $request->query('unit_kerja');
        $query = DataInternal::query();
        if ($unitKerjaId) {
            $query->where('unit_kerja_id', $unitKerjaId);
        }
        $countBaik = $query->where('kondisi', 'B')->count();
        $countRusakRingan = DataInternal::when($unitKerjaId, fn($q) => $q->where('unit_kerja_id', $unitKerjaId))->where('kondisi', 'RR')->count();
        $countRusakBerat = DataInternal::when($unitKerjaId, fn($q) => $q->where('unit_kerja_id', $unitKerjaId))->where('kondisi', 'RB')->count();
        return response()->json([
            'countBaik' => number_format($countBaik),
            'countRusakRingan' => number_format($countRusakRingan),
            'countRusakBerat' => number_format($countRusakBerat),
        ]);
    }
}
