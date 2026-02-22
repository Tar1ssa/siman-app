<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Barang;
use App\Models\satker;
use App\Models\UnitKerja;
use App\Models\InvalidData;
use App\Models\DataInternal;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


class invalidController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Data invalid';
        $barang = Barang::get();
        $batchNumber = InvalidData::select('batch', 'label')->distinct()->get();
        $unitkerja = UnitKerja::get();
        $satker = satker::get();
        return view('invalid.index', compact('title', 'barang', 'satker','batchNumber','unitkerja'));
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


    // normalize numerical helper function
    private function normalizeNumeric($raw, $fieldName)
    {
        $humanEmptyPatterns = [
                        '',
                        '-',
                        '--',
                        '.',
                        'n/a',
                        'na',
                        'null',
                    ];

        $raw = is_string($raw) ? trim($raw) : $raw;

        if (
            $raw === null ||
            $raw === '' ||
            preg_match('/^[\-\–\—\−\.\s]+$/u', $raw) || // only dashes, dots, spaces
            in_array(strtolower($raw), ['n/a', 'na', 'null'], true)
        ) {
            return 0;
        }


        // Remove everything except digits, dot, minus
        $clean = preg_replace('/[^\d\.\-]/', '', $raw);

        // Handle thousand separators like "10.200.000"
        if (substr_count($clean, '.') > 1 && preg_match('/\.\d{3}$/', $raw)) {
            $clean = str_replace('.', '', $clean);
        }

        if (!is_numeric($clean)) {
            // $numeric = 0;
            throw new \Exception("Invalid numeric value for {$fieldName}: {$raw}");
        }

        $numeric = $clean + 0; // cast to int/float

        // BIGINT safety check
        if ($numeric > 9223372036854775807) {
            throw new \Exception("Value too large for {$fieldName}: {$numeric}");
        }

        return $numeric;
    }

    function normalizeCsvDateToYMD(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);

        // Normalize separators (., -, space → /)
        $value = preg_replace('/[.\-\s]/', '/', $value);

        $formats = [
            'n/j/Y',   // 3/15/2023
            'j/n/Y',   // 15/3/2023
            'm/d/Y',   // 03/15/2023
            'd/m/Y',   // 15/03/2023
            'Y/m/d',   // 2023/03/15
            'Y/d/m',
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value)
                    ->format('Y-m-d'); //  unified output
            } catch (\Exception $e) {
                // try next format
            }
        }

        // Last resort (auto-parse)
        try {
            return Carbon::parse($value)->format('d/m/Y');
        } catch (\Exception $e) {
            return null;
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $edit = InvalidData::where('id',$id)->first();
                    $tglBAHI = $this->normalizeCsvDateToYMD($request->tgl_bahi);

                    $perolehan_tgl = $this->normalizeCsvDateToYMD($request->tgl_perolehan);

                    $rules = [
                        'barang_id' => 'required',
                        'satker_id' => 'required',
                        'nup' => 'required|numeric'
                    ];

                    $messages = [
                        'barang_id.required' => 'Kode barang tidak dapat kosong.',
                        'satker_id.required' => 'Kode satker tidak dapat kosong.',
                        'nup.required' => 'NUP tidak dapat kosong.',
                        'nup.numeric' => 'NUP harus berupa angka',
                    ];

                    $validation = Validator::make($request->all(), $rules, $messages);

                    if ($validation->fails()) {
                        $errors = $validation->errors();

                        // Ambil pesan error spesifik untuk password jika ada
                        if ($errors->has('barang_id')) {
                            Alert::error('Gagal!', $errors->first('barang_id'));
                        }
                        if ($errors->has('satker_id')) {
                            Alert::error('Gagal!', $errors->first('satker_id'));
                        }
                        if ($errors->has('nup')) {
                            Alert::error('Gagal!', $errors->first('nup'));
                        }
                        return redirect()->back()->withErrors($errors);
                    }


                    $nilaiPerolehan  = $this->normalizeNumeric($request->nilai_aset, 'nilai_aset');
                    $nilaiPenyusutan = $this->normalizeNumeric($request->nilai_penyusutan, 'nilai_penyusutan');
                    $nilaiBuku       = $this->normalizeNumeric($request->nilai_buku, 'nilai_buku');


                    $statusInven = strtoupper($request->status_inven);

                   $internal = DataInternal::create([
                        // 'bmn_id' => $bmn->id,
                        'satker_id' => $request->satker_id,
                        'barang_id' => $request->barang_id,
                        'nup' => $request->nup,
                        'tgl_perolehan'=> $perolehan_tgl,
                        'merkRaw'=> $request->merk,
                        // 'merk'=> $row['merk'],
                        // 'tipe'=> $row['tipe'],
                        'jumlah'=> $request->jumlah,
                        'nilai_aset'=> $nilaiPerolehan,
                        'nilai_penyusutan'=> $nilaiPenyusutan,
                        'nilai_buku'=> $nilaiBuku,
                        'kondisi'=> $request->kondisi,
                        'akun_neraca'=> $request->akun_neraca,
                        'pembukuan'=> $request->pembukuan,
                        'unit_kerja'=> $request->unit_kerja,
                        'pengguna'=> $request->pengguna,
                        'status_inven'=> $statusInven,
                        'update_kondisi'=> $request->update_kondisi,
                        'link_dokumentasi'=> $request->link_dokumentasi,
                        'link_lhi'=> $request->link_lhi,
                        'no_bahi'=> $request->no_bahi,
                        'tgl_bahi'=> $tglBAHI,
                        // 'lokasi_ruang'=> $row['lokasi_ruang'],
                        // 'update_lokasi_ruang'=> $row['update_lokasi_ruang'],
                        // 'opname'=> $opname,
                        'batch' => $request->batch,
                        'label' => $request->label
                    ]);

                    if (! $internal || ! $internal->exists) {
                        throw new \Exception('Create internal failed');
                    }

                    // OPTIONAL: hapus invalid
                    $edit->delete();

            DB::commit();

            Alert::success('Sukses!', 'Data valid dan berhasil ditambahkan!');
            return redirect()->to('internal')->with('success', 'Data berhasil ditambahkan!');
        } catch (\Throwable $th) {
                DB::rollBack();

                $edit->update([
                    'satker_id' =>$request->satker_id,
                    'barang_id' => $request->barang_id,
                    'nup' => $request->nup,
                    'tgl_perolehan' => $request->tgl_perolehan,
                    'merkRaw' => $request->merk,
                    'kondisi' => $request->kondisi,
                    'jumlah' => $request->jumlah,
                    'akun_neraca' => $request->akun_neraca,
                    'pembukuan' => $request->pembukuan,
                    'nilai_aset' => $request->nilai_aset,
                    'nilai_penyusutan' => $request->nilai_penyusutan,
                    'nilai_buku' => $request->nilai_buku,
                    'unit_kerja'=> $request->unit_kerja,
                    'pengguna'=> $request->pengguna,
                    'status_inven'=> $request->status_inven,
                    'update_kondisi'=> $request->update_kondisi,
                    'link_dokumentasi'=> $request->link_dokumentasi,
                    'link_lhi'=> $request->link_lhi,
                    'no_bahi'=> $request->no_bahi,
                    'tgl_bahi'=> $request->tgl_bahi,
                ]);
                Alert::error('Gagal!', $th->getMessage(),   'Sebagian tersimpan',
                'Gagal masuk Internal, InvalidData diperbarui');
                return back();

        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         try {
            DB::transaction(function () use ($id) {

                $data = InvalidData::findOrFail($id);

                // OPTIONAL: cleanup relations/files here
                // Storage::delete($siman->link_dokumentasi);

                $data->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ], 200);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyBatch(Request $request)
    {
        $Data = InvalidData::where('batch', $request->batch)->delete();
        Alert::success('Sukses!', 'Data Invalid berhasil dihapus');
        return redirect()->back()->with('Sukses!', 'Data Invalid berhasil dihapus!');
    }

    public function datatable(Request $request)
    {
        $query = InvalidData::query()
        ->with([
            'satkers:id,kode_satker',
            'unitKerja:id,name',
            'barang:id,kode_barang,nama_barang'
        ])
        ->select([
            'id',
            'satker_id',
            'barang_id',
            'nup',
            'tgl_perolehan',
            'merkRaw',
            'merk',
            'tipe',
            'jumlah',
            'nilai_aset',
            'nilai_penyusutan',
            'nilai_buku',
            'kondisi',
            'akun_neraca',
            'pembukuan',
            'unit_kerja_id',
            'pengguna',
            'lokasi_ruang',
            'status_inven',
            'update_kondisi',
            'link_dokumentasi',
            'link_lhi',
            'no_bahi',
            'tgl_bahi',
            'description',
            'batch',
            'label'
        ]);

    return DataTables::eloquent($query)
        ->addIndexColumn()

        ->editColumn('kode_satker', fn ($row) => $row->satkers->kode_satker ?? '-')
        ->editColumn('kode_barang', fn ($row) => $row->barang->kode_barang ?? '-')
        ->editColumn('nama_barang', fn ($row) => $row->barang->nama_barang ?? '-')
        ->editColumn('unit_kerja_id', fn ($row) => $row->unitKerja->name ?? '-')

        ->editColumn('batch', function ($row) {
            return $row->batch
                ? $row->batch . ' — ' . $row->label
                : '-';
        })

        ->editColumn('nilai_aset', fn ($row) =>
            'Rp. ' . number_format($row->nilai_aset, 2, ',', '.')
        )
        ->editColumn('nilai_penyusutan', fn ($row) =>
            'Rp. ' . number_format($row->nilai_penyusutan, 2, ',', '.')
        )
        ->editColumn('nilai_buku', fn ($row) =>
            'Rp. ' . number_format($row->nilai_buku, 2, ',', '.')
        )

        ->orderColumn('nilai_aset', 'nilai_aset $direction')
        ->orderColumn('nilai_penyusutan', 'nilai_penyusutan $direction')
        ->orderColumn('nilai_buku', 'nilai_buku $direction')

        ->addColumn('action', function ($row) {
            return view('invalid.partials.action', compact('row'))->render();
        })

        ->filter(function ($query) use ($request) {

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



            if ($request->filled('itemSearch')) {
                $query->whereHas('barang', function ($q) use ($request) {
                    $q->where('kode_barang', $request->itemSearch);
                });
            }

            if ($request->filled('unitSearch')) {
                    $query->whereHas('unitKerja', function ($q) use ($request) {
                        $q->where('id', $request->unitSearch);
                    });
                }

            if ($search = $request->input('search.value')) {
                    $query->where(function ($q) use ($search) {
                        $q
                        ->orWhere('invalid_data.merkRaw', 'like', "%{$search}%")
                        ->orWhere('invalid_data.jumlah', 'like', "%{$search}%")
                        ->orWhere('invalid_data.nilai_aset', 'like', "%{$search}%")
                        ->orWhere('invalid_data.pengguna', 'like', "%{$search}%")
                        ->orWhere('invalid_data.akun_neraca', 'like', "%{$search}%")
                        ->orWhere('invalid_data.pembukuan', 'like', "%{$search}%")
                        ->orWhere('invalid_data.pembukuan', 'like', "%{$search}%")
                        ->orWhere('invalid_data.label', 'like', "%{$search}%")
                        ->orWhere('invalid_data.tipe', 'like', "%{$search}%");
                    });
                }
        })

        ->rawColumns(['action'])
        ->make(true);
    }

    private function invalidSheets()
    {
        $unitKerjas = UnitKerja::select('id', 'name')->get();

        // add virtual unit kerja for NULL
        $unitKerjas->push((object) [
            'id'   => null,
            'name' => 'Tanpa Unit Kerja',
        ]);

        foreach ($unitKerjas as $unitKerja) {

            $rows = DB::table('invalid_data as di')
                ->leftjoin('barangs as b', 'b.id', '=', 'di.barang_id')
                ->leftjoin('satkers as sat', 'sat.id', '=', 'di.satker_id')
                ->leftjoin('unit_kerjas as uk', 'uk.id', '=', 'di.unit_kerja_id')
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
                    'di.merkRaw',
                    'di.jumlah',
                    'di.tgl_perolehan',
                    'di.nilai_aset',
                    'di.nilai_penyusutan',
                    'di.nilai_buku',
                    'di.kondisi',
                    'di.akun_neraca',
                    'di.pembukuan',
                    DB::raw('COALESCE(uk.name, "Tanpa Unit Kerja") as unit_kerja'),
                    'di.pengguna',
                    'di.lokasi_ruang',
                    'di.status_inven',
                    'di.update_kondisi',
                    'di.link_dokumentasi',
                    'di.link_lhi',
                    'di.no_bahi',
                    'di.tgl_bahi',
                    'di.description',
                    DB::raw("'INVALID' as status"),
                ])
                ->orderBy('b.kode_barang')
                ->cursor(); //  streaming

            //  IMPORTANT: skip empty sheets
            if ($rows->isEmpty()) {
                continue;
            }

            yield (($unitKerja->id ? $unitKerja->id . '_' : '') . ($unitKerja->name ?: 'Tanpa Unit Kerja')) => $rows;
        }
    }

    public function exportInvalid()
    {


            $filePath = storage_path('app/invalid data.xlsx');

            $writer = new Writer();
            $writer->openToFile($filePath);

            $firstSheet = true;

            foreach ($this->invalidSheets() as $sheetName => $rows) {

                // For next sheets, create new one
                if (! $firstSheet) {
                    $writer->addNewSheetAndMakeItCurrent();
                }

                $firstSheet = false;

                // Rename sheet safely
                $writer->getCurrentSheet()->setName($this->sanitizeSheetName($sheetName));

                // HEADER
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
                    'Alasan',
                    'Status',
                ]));

                // DATA ROWS (explicit mapping = safe)
                foreach ($rows as $row) {
                    $writer->addRow(Row::fromValues([
                        $this->sanitizeForExcel($row->kode_satker),
                        $this->sanitizeForExcel($row->kode_barang),
                        $this->sanitizeForExcel($row->nama_barang),
                        $this->sanitizeForExcel($row->nup),
                        $this->sanitizeForExcel($row->merkRaw),
                        $this->sanitizeForExcel($row->jumlah),
                        $this->sanitizeForExcel($row->tgl_perolehan),
                        $this->sanitizeForExcel($row->nilai_aset),
                        $this->sanitizeForExcel($row->nilai_penyusutan),
                        $this->sanitizeForExcel($row->nilai_buku),
                        $this->sanitizeForExcel($row->kondisi),
                        $this->sanitizeForExcel($row->akun_neraca),
                        $this->sanitizeForExcel($row->pembukuan),
                        $this->sanitizeForExcel($row->unit_kerja),
                        $this->sanitizeForExcel($row->pengguna),
                        $this->sanitizeForExcel($row->lokasi_ruang),
                        $this->sanitizeForExcel($row->status_inven),
                        $this->sanitizeForExcel($row->update_kondisi),
                        $this->sanitizeForExcel($row->link_dokumentasi),
                        $this->sanitizeForExcel($row->link_lhi),
                        $this->sanitizeForExcel($row->no_bahi),
                        $this->sanitizeForExcel($row->tgl_bahi),
                        $this->sanitizeForExcel($row->description),
                        $this->sanitizeForExcel($row->status),
                    ]));
                }
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
