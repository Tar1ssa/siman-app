<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Carbon\Carbon;
use App\Models\bmn;
use App\Models\satker;
use App\Models\SimanBatch;
use App\Models\simanData;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SimanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Data Siman';
        $barang = Barang::get();
        $batchNumber = SimanBatch::select('id', 'label')->distinct()->get();
        // $dataSiman->transform(function ($item) {
        //     $item->nilai_perolehan_fmt  = 'Rp. ' . number_format($item->nilai_perolehan, 2, ',', '.');
        //     $item->nilai_penyusutan_fmt = 'Rp. ' . number_format($item->nilai_penyusutan, 2, ',', '.');
        //     $item->nilai_buku_fmt       = 'Rp. ' . number_format($item->nilai_buku, 2, ',', '.');

        //     return $item;
        // });
        return view('simanData.index', compact('title', 'barang', 'batchNumber'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Data SiMAN';
        $bmns = bmn::all();
        $satker = satker::all();
        return view('simanData.create', compact('title', 'bmns', 'satker'));
    }

    // normalize numerical helper function
    private function normalizeNumeric($raw, $fieldName)
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        // Remove everything except digits, dot, minus
        $clean = preg_replace('/[^\d\.\-]/', '', $raw);

        // Handle thousand separators like "10.200.000"
        if (substr_count($clean, '.') > 1 && preg_match('/\.\d{3}$/', $raw)) {
            $clean = str_replace('.', '', $clean);
        }

        if (!is_numeric($clean)) {
            throw new \Exception("Invalid numeric value for {$fieldName}: {$raw}");
        }

        $numeric = $clean + 0; // cast to int/float

        // BIGINT safety check
        if ($numeric > 9223372036854775807) {
            throw new \Exception("Value too large for {$fieldName}: {$numeric}");
        }

        return $numeric;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $allErrors = [];
        $rowNumber = 1;
        $inserted = 0;
        $batchLabel = $request->batch_label;

        DB::beginTransaction();

        try {
            $batch = SimanBatch::create([
                'label'  => $batchLabel,
                'source' => 'csv'
            ]);
            $batchId = $batch->id;

                // handle csv file
            if (!$request->hasFile('csv_file')) {
                return response()->json(['error' => 'File not detected'], 400);
            }

            $file = $request->file('csv_file');

            $data = [];

            if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    $data[] = $row;
                }
                fclose($handle);
            }

            // MAP CSV DATA USING HEADER
            $headers = $data[0];
            $rows    = array_slice($data, 1);

            //  Normalize headers
            $normalizedHeaders = array_map(function ($h) {
                $h = strtolower($h);                     // lower case
                $h = preg_replace('/\s+/', '_', $h);     // spaces → _
                $h = preg_replace('/[^a-z0-9_]/', '', $h); // remove special characters
                return $h;
            }, $headers);

            $mapped = [];

            foreach ($rows as $row) {
                if (count($row) !== count($normalizedHeaders)) continue;

                $mapped[] = array_combine($normalizedHeaders, $row);
            }

            // validation
            $rules = [
                    'jenis_bmn' => 'required',
                    'kode_satker' => 'required',
                    'nama_satker' => 'required',
                    'kode_barang' => 'required',
                    'kode_register' => 'required'
                ];


                $messages = [

                    'jenis_bmn.required' => 'Jenis BMN tidak dapat kosong.',
                    'kode_satker.required' => 'Kode Satker tidak dapat kosong.',
                    'nama_satker.required' => 'Nama Satker tidak dapat kosong.',
                    'kode_register.required' => 'Kode Register tidak dapat kosong.',
                    // 'kode_register.unique' => 'Kode Register sudah terdapat di database.',
                    'kode_barang.required' => 'Kode barang tidak dapat kosong'

                ];

                   //  Process CSV in chunks (memory safe)
            foreach (array_chunk($mapped, 500) as $chunk) {

                foreach ($chunk as $row) {

                    //  Validate one row
                    $validator = Validator::make($row, $rules, $messages);

                    if ($validator->fails()) {
                        $errors = $validator->errors();

                        foreach ($errors->all() as $error) {
                            $allErrors[] = "Baris {$rowNumber}: {$error}";
                        }
                    }

                    $rowNumber++;
                }
            }

            //  If ANY error exists → CANCEL ALL INSERTS
            if (!empty($allErrors)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'errors' => $allErrors
                ], 422);

            }

            //  If EVERYTHING is valid → INSERT ALL

            foreach (array_chunk($mapped, 500) as $chunk) {
                foreach ($chunk as $row) {

                    // CHECK duplicate
                    if (simanData::where('kode_register', $row['kode_register'])->exists()) {
                        $skipped[] = "Kode Register {$row['kode_register']} sudah ada — dilewati";
                        continue; // SKIP this row
                    }

                    // find or create BMN and satker
                    $bmn = Bmn::firstOrCreate(
                        ['name' => $row['jenis_bmn']],
                        ['name' => strtoupper($row['jenis_bmn'])]
                    );
                    $satker = satker::firstOrCreate(
                        ['kode_satker' => $row['kode_satker']],
                        [
                            'kode_satker' => $row['kode_satker'],
                            'nama_satker' => $row['nama_satker']
                        ]
                    );
                    $barang = Barang::firstOrCreate(
                        ['kode_barang' => $row['kode_barang']],
                        [
                            'kode_barang' => $row['kode_barang'],
                            'nama_barang' => $row['nama_barang']
                        ]
                    );

                    // format tanggal perolehan to safe format date
                    if (!empty($row['tanggal_perolehan'])) {
                        try {
                            $perolehan_tgl = Carbon::createFromFormat('n/j/Y', $row['tanggal_perolehan'])
                                ->format('Y-m-d');
                        } catch (\Exception $e) {
                            // Handle invalid date
                            $perolehan_tgl = null;
                        }
                    }

                    $nilaiPerolehan  = $this->normalizeNumeric($row['nilai_perolehan'], 'nilai_perolehan');
                    $nilaiPenyusutan = $this->normalizeNumeric($row['nilai_penyusutan'], 'nilai_penyusutan');
                    $nilaiBuku       = $this->normalizeNumeric($row['nilai_buku'], 'nilai_buku');

                    // normalize no_polisi
                    $raw = $row['no_polisi'];

                    if (!empty($row['no_polisi'])) {
                        try {
                            $raw = $row['no_polisi'];
                            $noPolisi = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $raw)
                    );
                        } catch (\Exception $e) {
                            // Handle invalid date
                            $noPolisi = null;
                        }
                    }


                    $opname = Carbon::now();

                    simanData::create([
                        'bmn_id' => $bmn->id,
                        'satker_id' => $satker->id,
                        'barang_id' => $barang->id,
                        'nup' => $row['nup'],
                        'merk'=> $row['merk'],
                        'tipe'=> $row['tipe'],
                        'kondisi'=> $row['kondisi'],
                        'no_dokumen'=> $row['no_dokumen'],
                        'no_BPKP'=> $row['no_bpkp'],
                        'no_polisi'=> $noPolisi,
                        'no_sertifikat'=> $row['no_sertifikat'],
                        'tgl_perolehan'=> $perolehan_tgl,
                        'nilai_perolehan'=> $nilaiPerolehan,
                        'nilai_penyusutan'=> $nilaiPenyusutan,
                        'nilai_buku'=> $nilaiBuku,
                        'kode_register'=> $row['kode_register'],
                        'lokasi_ruang'=> $row['lokasi_ruang'],
                        'update_lokasi_ruang'=> $row['update_lokasi_ruang'],
                        'update_kondisi'=> $row['kondisi_setelah_inventarisasi_brrrb'],
                        'nama_pengguna'=> $row['nama_pengguna_'],
                        'link_dokumentasi'=> $row['link_foto_dokumentasi'],
                        'opname'=> $opname,
                        'import_batch_id' => $batchId,

                    ]);

                    $inserted++;
                }
             }

             $skippedRows =  (!empty($skipped)) ? count($skipped) : 0;

                DB::commit();
            // Alert::success('Sukses!', 'Import CSV berhasil ditambahkan!');
            // return redirect()->to('siman')->with('Sukses!', 'Data SIMAN berhasil ditambahkan!');
            return response()->json([
                'success' => true,
                'redirect' => route('siman.index'),
                'message' => "Import selesai. {$inserted} data ditambahkan, " . $skippedRows . " dilewati."
            ],200);

            // return $mapped;

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success'  => false,
                'redirect' => null,
                'message'  => 'Import gagal. Terjadi kesalahan: ' . $th->getMessage(),
            ], 500);
        }

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
    public function destroy(string $Id)
    {

    }

    public function destroyBatch(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $Siman = simanData::where('import_batch_id', $request->batch)->delete();
            simanBatch::where('id', $request->batch)->delete();
            Alert::success('Sukses!', 'Data SIMAN berhasil dihapus');
            return redirect()->back()->with('Sukses!', 'Data SIMAN berhasil dihapus!');
        });
    }

    public function datatable(Request $request)
    {
        $query = simanData::query()
            ->with([
                'bmns:id,name',
                'satkers:id,kode_satker,nama_satker',
                'batchId:id,label'
            ])
            ->select([
                'id',
                'bmn_id',
                'satker_id',
                'barang_id',
                'nup',
                'merk',
                'tipe',
                'kondisi',
                'no_dokumen',
                'no_BPKP',
                'no_polisi',
                'no_sertifikat',
                'tgl_perolehan',
                'nilai_perolehan',
                'nilai_penyusutan',
                'nilai_buku',
                'kode_register',
                'lokasi_ruang',
                'update_lokasi_ruang',
                'update_kondisi',
                'nama_pengguna',
                'link_dokumentasi',
                'opname',
                'import_batch_id',

            ]);

        return DataTables::eloquent($query)
            ->addIndexColumn()

            // relations
            ->editColumn('bmn', fn ($row) => $row->bmns->name ?? '-')
            ->editColumn('kode_satker', fn ($row) => $row->satkers->kode_satker ?? '-')
            ->editColumn('nama_satker', fn ($row) => $row->satkers->nama_satker ?? '-')
            ->editColumn('kode_barang', fn ($row) => $row->barang->kode_barang ?? '-')
            ->editColumn('nama_barang', fn ($row) => $row->barang->nama_barang ?? '-')
            ->editColumn('import_batch_id', function ($row) {
                if (!$row->batchId) {
                    return '-';
                }

                return $row->import_batch_id . ' — ' . $row->batchId->label;
            })

            // currency formatting (DISPLAY ONLY)
            ->editColumn('nilai_perolehan', fn ($row) =>
                'Rp. ' . number_format($row->nilai_perolehan, 2, ',', '.')
            )
            ->editColumn('nilai_penyusutan', fn ($row) =>
                'Rp. ' . number_format($row->nilai_penyusutan, 2, ',', '.')
            )
            ->editColumn('nilai_buku', fn ($row) =>
                'Rp. ' . number_format($row->nilai_buku, 2, ',', '.')
            )

            // raw numeric for sorting
            ->orderColumn('nilai_perolehan', 'nilai_perolehan $1')
            ->orderColumn('nilai_penyusutan', 'nilai_penyusutan $1')
            ->orderColumn('nilai_buku', 'nilai_buku $1')
            ->filter(function ($query) use ($request) {

                // kode barang (select)
                if ($request->filled('itemSearch')) {
                    $query->whereHas('barang', function ($q) use ($request) {
                        $q->where('kode_barang', $request->itemSearch);
                    });
                }

                // nup range
                if ($request->filled('nupMin') && $request->filled('nupMax')) {
                    $query->whereRaw('CAST(nup AS UNSIGNED) BETWEEN ? AND ?', [$request->nupMin, $request->nupMax]);
                } elseif ($request->filled('nupMin')) {
                    $query->whereRaw('CAST(nup AS UNSIGNED) >= ?', [$request->nupMin]);
                } elseif ($request->filled('nupMax')) {
                    $query->whereRaw('CAST(nup AS UNSIGNED) <= ?', [$request->nupMax]);
                }

                // tgl_perolehan date range
                if ($request->filled('tglFrom') && $request->filled('tglTo')) {
                    $query->whereBetween('tgl_perolehan', [
                        $request->tglFrom,
                        $request->tglTo
                    ]);
                }

                // only FROM
                elseif ($request->filled('tglFrom')) {
                    $query->whereDate('tgl_perolehan', '>=', $request->tglFrom);
                }

                // only TO
                elseif ($request->filled('tglTo')) {
                    $query->whereDate('tgl_perolehan', '<=', $request->tglTo);
                }

                // kode satker (partial)
                // if ($request->filled('satkerSearch')) {
                //     $query->whereHas('satkers', function ($q) use ($request) {
                //         $q->where('kode_satker', 'LIKE', '%' . $request->satkerSearch . '%');
                //     });
                // }

                // jenis BMN (exacts)
                // if ($request->filled('nupSearch')) {
                //     $query->whereHas('bmns', function ($q) use ($request) {
                //         $q->where('name', $request->bmnSearch);
                //     });
                // }
            })

            ->toJson();
    }
}
