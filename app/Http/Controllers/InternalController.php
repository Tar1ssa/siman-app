<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\bmn;
use App\Models\Barang;
use App\Models\satker;
use App\Models\Pengguna;
use App\Models\Identitas;
use App\Models\UnitKerja;
use App\Models\DataAtribut;
use App\Models\InvalidData;
use App\Models\LokasiRuang;
use App\Models\DataInternal;
use App\Models\FotoInternal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InternalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Data Internal';
        $barang = Barang::get();
        $batchNumber = DataInternal::select('batch', 'label')->distinct()->get();
        $unitkerja = UnitKerja::get();
        $lokasiruang = LokasiRuang::get();

        return view('internal.index', compact('title', 'barang', 'batchNumber','unitkerja','lokasiruang'));
    }

    /**s
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Data internal';
        $bmns = bmn::all();
        $satker = satker::all();
        return view('internal.create', compact('title', 'bmns', 'satker'));
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $allErrors = [];
        $rowNumber = 1;
        $inserted = 0;
        $invalidRows = [];
        $invalidCount = 0;
        $skipped = [];
        $seenCombinations = [];
        $batchLabel = $request->batch_label;

        DB::beginTransaction();

        try {

            $batch = DataInternal::max('batch') ? DataInternal::max('batch') : 0;
            $batchId = $batch + 1;

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
                $h = preg_replace('/\s+/', '', $h);     // spaces → _
                $h = preg_replace('/[^a-z0-9_]/', '', $h); // remove special characters
                return $h;
            }, $headers);

            $mapped = [];

            foreach ($rows as $row) {
                if (count($row) !== count($normalizedHeaders)) {
                    continue;
                }

                $row = array_combine($normalizedHeaders, $row);
                $reasons = [];

                $humanEmptyPatterns = ['', '-', '--', '.', 'n/a', 'na', 'null'];

                // --- NUP ---
                $nup = trim($row['nup'] ?? '');
                if ($nup === '' || !preg_match('/^\d+$/', $nup)) {
                    $reasons[] = 'NUP kosong atau bukan angka';
                }

                // --- KODE / URAIAN ---
                $kode = trim($row['kodebarang'] ?? '');
                $uraian = trim($row['uraianbarang'] ?? '');

                $kodeEmpty = $kode === '' || in_array(strtolower($kode), $humanEmptyPatterns, true);
                $uraianEmpty = $uraian === '' || in_array(strtolower($uraian), $humanEmptyPatterns, true);

                if ($kodeEmpty && $uraianEmpty) {
                    $reasons[] = 'Kode barang dan uraian barang kosong';
                }

                // --- DUPLICATE ---
                if (!$kodeEmpty && $nup !== '') {
                    $key = $kode . '|' . $nup;
                    $findDuplicate = DataInternal::whereHas('barang', function ($q) use ($row){
                            $q->where('kode_barang', 'LIKE', $row['kodebarang']);
                        })->where('nup', $row['nup'])->exists();

                    if (isset($seenCombinations[$key]) || $findDuplicate) {
                        $reasons[] = 'Duplikasi kodebarang + NUP '.', '. 'Kode/Nama Barang : ' . $row['kodebarang'] . ' - ' . $row['uraianbarang'] . ' ' . 'NUP :' . $row['nup'];
                    } else {
                        $seenCombinations[$key] = true;
                    }
                }

                if ($reasons) {
                    $invalidRows[] = [
                        'row' => $row,
                        'reasons' => $reasons,
                    ];
                    continue;
                }

                $mapped[] = $row;
            }

            // foreach ($rows as $row) {
            //         if (count($row) !== count($normalizedHeaders)) continue;

            //         $mapped[] = array_combine($normalizedHeaders, $row);
            //         $mapped = array_values(array_filter($mapped, function ($row) use (&$invalidRows, &$seenCombinations) {

            //         $reasons = [];

            //         // Common human-empty patterns
            //         $humanEmptyPatterns = ['', '-', '--', '.', 'n/a', 'na', 'null'];

            //         // ---- nup check ----
            //         $nup = isset($row['nup']) ? trim($row['nup']) : '';
            //         $nupLower = strtolower($nup);

            //         $nupIsHumanEmpty = $nup === '' || in_array($nupLower, $humanEmptyPatterns, true);
            //         $nupIsNumeric = !$nupIsHumanEmpty && ctype_digit($nup);

            //         if (!$nupIsNumeric) {
            //             $reasons[] = 'NUP is empty or not numeric';
            //         }

            //         // ---- kodebarang check ----
            //         $kodebarang = isset($row['kodebarang']) ? trim($row['kodebarang']) : '';
            //         $kodeLower  = strtolower($kodebarang);

            //         $kodeIsHumanEmpty = $kodebarang === '' || in_array($kodeLower, $humanEmptyPatterns, true);
            //         $kodeIsMeaningful = !$kodeIsHumanEmpty && ctype_digit($kodebarang);

            //         // ---- uraianbarang check ----
            //         $uraian = isset($row['uraianbarang']) ? trim($row['uraianbarang']) : '';
            //         $uraianLower = strtolower($uraian);

            //         $uraianIsEmpty = $uraian === '' || in_array($uraianLower, $humanEmptyPatterns, true);

            //         if (!$kodeIsMeaningful && $uraianIsEmpty) {
            //             $reasons[] = 'Both kodebarang and uraianbarang are empty or invalid';
            //         }

            //         // ---- DUPLICATE CHECK (kodebarang + nup) ----
            //         if ($kodeIsMeaningful && $nupIsNumeric) {
            //             $comboKey = $kodebarang . '|' . $nup;

            //             if (isset($seenCombinations[$comboKey])) {
            //                 $reasons[] = 'Duplicate combination of kodebarang and NUP' . $row['kodebarang'] . '-' . $row['uraianbarang'] . 'NUP:' . $row['nup'];
            //             } else {
            //                 $seenCombinations[$comboKey] = true;
            //             }
            //         }

            //         // ---- FINAL DECISION ----
            //         if (!empty($reasons)) {
            //             $invalidRows[] = [
            //                 'row'     => $row,
            //                 'reasons' => $reasons,
            //             ];
            //             return false;
            //         }

            //         return true;
            //     }));


            // }




            // $sortedRows = collect($mapped)
            //             ->map(function ($row) {
            //                 // normalize CSV empty values
            //                 if (!isset($row['nup']) || $row['nup'] === '') {
            //                     $row['nup'] = null;
            //                 }
            //                 return $row;
            //             })
            //             ->sortBy(function ($row) {
            //                 return $row['nup'] === null
            //                     ? -1
            //                     : (int) $row['nup'];
            //             })
            //             ->values()
            //             ->toArray(); // back to array


            // validation
            $rules = [
                    'kodesatker' => 'required',
                    'kodebarang' => 'required',

                ];


                $messages = [
                    'kodesatker.required' => 'Kode Satker tidak dapat kosong.',
                    'kodebarang.required' => 'Kode Barang tidak dapat kosong'

                ];

                    // Process CSV in chunks (memory safe)
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

                    $tglBAHI = $this->normalizeCsvDateToYMD($row['tanggalbahiberitaacarahasilinven']);

                    $perolehan_tgl = $this->normalizeCsvDateToYMD($row['tahunperolehan']);

                    // CHECK duplicate
                    if (DataInternal::whereHas('barang', function ($q) use ($row) {
                            $q->where('kode_barang', $row['kodebarang']);
                        })
                        ->where('nup', $row['nup'])
                        ->where('tgl_perolehan', $perolehan_tgl)
                        ->exists()) {
                        $skipped[] = "Data dengan {$row['kodebarang']} dengan nup {$row['nup']} sudah ada — dilewati";
                        continue; // SKIP this row
                    }

                    // find or create BMN and satker
                    // $bmn = Bmn::firstOrCreate(
                    //     ['name' => $row['jenis_bmn']],
                    //     ['name' => strtoupper($row['jenis_bmn'])]
                    // );
                    $satker = satker::firstOrCreate(
                        ['kode_satker' => $row['kodesatker']],
                        [
                            'kode_satker' => $row['kodesatker'],
                            // 'nama_satker' => $row['nama_satker']
                        ]
                    );

                    $unitKerjaId = null;

                    $rawUnitKerja = $row['unitkerja'] ?? null;

                    if (!empty($rawUnitKerja) && trim($rawUnitKerja) !== '') {

                        $normalized = strtolower($rawUnitKerja);
                        $normalized = preg_replace('/\s+/', '', $normalized);
                        $normalized = preg_replace('/[^a-z0-9_]/', '', $normalized);

                        // Prevent empty result after cleaning
                        if ($normalized !== '') {

                            $unitKerja = UnitKerja::firstOrCreate(
                                ['nameId' => $normalized],
                                [
                                    'name'   => $rawUnitKerja,
                                    'nameId' => $normalized,
                                ]
                            );

                            $unitKerjaId = $unitKerja->id;
                        }
                    }

                    if ($row['kodebarang'] !== '' && ctype_digit($row['kodebarang'])) {
                        //  Valid numeric kode_barang
                        $barang = Barang::firstOrCreate(
                            ['kode_barang' => $row['kodebarang']],
                            ['nama_barang' => $row['uraianbarang']]
                        );
                    } else {
                        //  kode_barang invalid → fallback to nama_barang
                        $barang = Barang::firstOrCreate(
                            ['nama_barang' => $row['uraianbarang']],
                            ['kode_barang' => null]
                        );
                    }


                    $nilaiPerolehan  = $this->normalizeNumeric($row['nilaiaset'], 'nilaiaset');
                    $nilaiPenyusutan = $this->normalizeNumeric($row['akumulasipenyusutan'], 'akumulasipenyusutan');
                    $nilaiBuku       = $this->normalizeNumeric($row['nilaibuku'], 'nilaibuku');


                    $statusInven = strtoupper($row['sudahinvenataubelum']);


                    $opname = Carbon::now();

                    $insertData = DataInternal::create([
                        // 'bmn_id' => $bmn->id,
                        'satker_id' => $satker->id,
                        'barang_id' => $barang->id,
                        'nup' => isset($row['nup'])
                                ? (int) str_replace(',', '', $row['nup'])
                                : null,
                        'tgl_perolehan'=> $perolehan_tgl,
                        // 'merkRaw'=> $row['merktype'],
                        'merk'=> $row['merktype'],
                        'tipe'=> $row['merktype'],
                        'jumlah'=> isset($row['jumlah'])
                                    ? (int) str_replace(',', '', $row['jumlah'])
                                    : null,
                        'nilai_aset'=> $nilaiPerolehan,
                        'nilai_penyusutan'=> $nilaiPenyusutan,
                        'nilai_buku'=> $nilaiBuku,
                        'kondisi'=> $row['kondisi'],
                        'akun_neraca'=> $row['akunneraca'],
                        'pembukuan'=> $row['pembukuan'],
                        'unit_kerja_id'=> $unitKerjaId,
                        'penggunaRaw'=> $row['ruanganpengguna'],
                        'status_inven'=> $statusInven,
                        'update_kondisi'=> $row['kondisisetelahinven'],
                        'link_dokumentasi'=> $row['linkfoto'],
                        'link_lhi'=> $row['linkkelengkapanlhi'],
                        'no_bahi'=> $row['nomorbahiberitaacarahasilinven'],
                        'tgl_bahi'=> $tglBAHI,
                        // 'lokasi_ruang'=> $row['lokasi_ruang'],
                        // 'update_lokasi_ruang'=> $row['update_lokasi_ruang'],
                        // 'opname'=> $opname,
                        'batch' => $batchId,
                        'label' => $batchLabel
                    ]);


                    $inserted++;
                }
             }

            //  insert invalid rows
            foreach ($invalidRows as $keyInvalid) {

                $row = $keyInvalid['row']; // this is the CSV row

                $invUnitKerjaId = null;

                    $invRawUnitKerja = $row['unitkerja'] ?? null;

                    if (!empty($invRawUnitKerja) && trim($invRawUnitKerja) !== '') {

                        $normalized = strtolower($invRawUnitKerja);
                        $normalized = preg_replace('/\s+/', '', $normalized);
                        $normalized = preg_replace('/[^a-z0-9_]/', '', $normalized);

                        // Prevent empty result after cleaning
                        if ($normalized !== '') {

                            $invUnitKerja = UnitKerja::firstOrCreate(
                                ['nameId' => $normalized],
                                [
                                    'name'   => $invRawUnitKerja,
                                    'nameId' => $normalized,
                                ]
                            );

                            $invUnitKerjaId = $invUnitKerja->id;
                        }
                    }


                // ---- reuse your existing parsers / normalizers ----
                $perolehan_tgl    = $this->normalizeCsvDateToYMD($row['tglperolehan'] ?? null);
                $tglBAHI          = $this->normalizeCsvDateToYMD($row['tglbahiberitaacarahasilinven'] ?? null);

                $nilaiPerolehan   = $this->normalizeNumeric($row['nilaiaset'] ?? null, 'nilai_aset');
                $nilaiPenyusutan  = $this->normalizeNumeric($row['nilaipenyusutan'] ?? null, 'nilai_penyusutan');
                $nilaiBuku        = $this->normalizeNumeric($row['nilaibuku'] ?? null, 'nilai_buku');

                $statusInven = strtoupper($row['sudahinvenataubelum']);

                // ---- INSERT INTO invaliddata ----
                InvalidData::create([
                    'nup'                   => $row['nup'] ?? null,
                    'tgl_perolehan'         => $perolehan_tgl,
                    'merkRaw'               => $row['merktype'] ?? null,
                    'jumlah'                => isset($row['jumlah'])
                                                ? (int) str_replace(',', '', $row['jumlah'])
                                                : null,
                    'nilai_aset'            => $nilaiPerolehan,
                    'nilai_penyusutan'      => $nilaiPenyusutan,
                    'nilai_buku'            => $nilaiBuku,
                    'kondisi'               => $row['kondisi'] ?? null,
                    'akun_neraca'           => $row['akunneraca'] ?? null,
                    'pembukuan'             => $row['pembukuan'] ?? null,
                    'unit_kerja_id'            => $invUnitKerjaId ?? null,
                    'penggunaRaw'              => $row['ruanganpengguna'] ?? null,
                    'status_inven'          => $statusInven,
                    'update_kondisi'        => $row['kondisisetelahinven'] ?? null,
                    'link_dokumentasi'      => $row['linkfoto'] ?? null,
                    'link_lhi'              => $row['linkkelengkapanlhi'] ?? null,
                    'no_bahi'               => $row['nomorbahiberitaacarahasilinven'] ?? null,
                    'tgl_bahi'              => $tglBAHI,
                    'batch'                 => $batchId,
                    'label'                 => $batchLabel,
                    'description'       => implode('; ', $keyInvalid['reasons']),
                ]);
            }

            $invalidCount = count($invalidRows);

                DB::commit();


            return response()->json([
                'success' => true,
                'redirect' => route('internal.index'),
                'message'  => "Import selesai. {$inserted} data ditambahkan, "
               . count($skipped) . " dilewati, {$invalidCount} data invalid"
            ],200);

            // return $mapped;

        } catch (\Throwable $th) {
            DB::rollBack();

            // Alert::error('Gagal!', 'Terjadi kesalahan: ' . ($th))->persistent(true);
            // return redirect()->back()->withInput();

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
        $internal = DataInternal::with([
            // 'bmns',
            'satkers',
            'barang',
            'unitKerja',
            // 'penggunas',
            'lokasiRuang',
            'identitas', 
            'dataAtribut.atribut'
        ])->findOrFail($id);
        $identitas = Identitas::get();
        $internalImages = FotoInternal::where('data_internal_id', $id)->get();
        $satker = satker::all();
        $title = 'Show Data Internal';
        $barang = Barang::get();
        $lokasi = LokasiRuang::get();
        $unitkerja = UnitKerja::get();
        return view('internal.view', compact('internal', 'satker', 'title', 'barang', 'unitkerja', 'internalImages', 'lokasi','identitas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $internal = DataInternal::with([
            // 'bmns',
            'satkers',
            'barang',
            'unitKerja',
            // 'penggunas',
            'lokasiRuang',
            'identitas.atribut', 
            'dataAtribut'
        ])->findOrFail($id);
        $identitas = Identitas::get();
        $internalImages = FotoInternal::where('data_internal_id', $id)->get();
        $satker = satker::all();
        $title = 'Edit Data Internal';
        $barang = Barang::get();
        $lokasi = LokasiRuang::get();
        $unitkerja = UnitKerja::get();
        $dataAtribut = $internal->dataAtribut->keyBy('atributs_id');
        return view('internal.edit', compact('internal', 'satker', 'title', 'barang', 'unitkerja', 'internalImages', 'lokasi', 'identitas', 'dataAtribut'));
    }

    public function addImage(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240', // max 10MB
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:500',
            ]);

            /** If checked → true, if not sent → false */
            $isCover = $request->has('isCover');

            /** Optional: ensure only ONE cover per data_internal */
            if ($isCover) {
                FotoInternal::where('data_internal_id', $request->internal_id)
                    ->update(['is_cover' => false]);
            }

            $dataInternalId = DataInternal::findOrFail($request->internal_id);
            $file = $request->file('image');
            $filename = $file->store('foto_internals', 'public');

            $path = Storage::url($filename);
            FotoInternal::create([
                'data_internal_id' => $dataInternalId->id,
                'filename' => $filename,
                'path' => $path,
                'title' => $validated['title'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_cover' => $isCover,
            ]);
            DB::commit();
            Alert::success('Sukses', 'Gambar berhasil ditambahkan.');
            return redirect()->back()->with('success', 'Gambar berhasil ditambahkan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::error('Gagal', 'Gagal menambahkan gambar: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan gambar: ' . $th->getMessage());
        }
    }

    public function updateImage(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:500',
            ]);

            /** If checked → true, if not sent → false */
            $isCover = $request->has('isCover');

            $imageId = $id;
            $image = FotoInternal::findOrFail($imageId);

            /** Optional: ensure only ONE cover per data_internal */
            if ($isCover) {
                FotoInternal::where('data_internal_id', $image->data_internal_id)
                    ->update(['is_cover' => false]);
            }

            $image->title = $validated['title'] ?? null;
            $image->description = $validated['description'] ?? null;
            $image->is_cover = $isCover ? true : false;
            $image->save();
            DB::commit();
            Alert::success('Sukses', 'Gambar berhasil diperbarui.');
            return redirect()->back()->with('success', 'Gambar berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::error('Gagal', 'Gagal memperbarui gambar: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui gambar: ' . $th->getMessage());
        }
    }

    public function imageDestroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $imageId = $request->id;
            $image = FotoInternal::findOrFail($imageId);

            // Delete the image file from storage
            if (Storage::disk('public')->exists($image->filename)) {
                Storage::disk('public')->delete($image->filename);
            }

            // Delete the database record
            $image->delete();
            DB::commit();
            Alert::success('Sukses', 'Gambar berhasil dihapus.');
            return redirect()->back()->with('success', 'Gambar berhasil dihapus.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::error('Gagal', 'Gagal menghapus gambar: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Invalid image ID.');
        }


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                // add your validation rules here
                'satker_id' => 'required|exists:satkers,id',
                'barang_id' => 'required|exists:barangs,id',
                'unitkerja_id' => 'required|exists:unit_kerjas,id',
                'lokasi_id' => 'required|exists:lokasi_ruangs,id',
                'tgl_perolehan' => 'required|date',
                'merk' => 'nullable|string',
                'tipe' => 'nullable|string',
                'jumlah' => 'required|integer|min:1',
                'nilai_perolehan' => 'required|numeric',
                'kondisi' => 'required|in:B,RR,RB',
                'akun_neraca' => 'nullable|string',
                'pembukuan' => 'nullable|string',
                'link_dokumentasi' => 'nullable|url',
                'profileImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'name' => 'nullable|string',
                'address' => 'nullable|string',
            ]);

            $identitas = identitas::with('atribut')->findOrFail($request->identitas_id);


            $dataInternal = DataInternal::findOrFail($id);

            if ($request->hasFile('profileImage')) {
                $file = $request->file('profileImage');
                $filename = $file->store('profile_images', 'public');
                $validated['profileImage'] = $filename;
                $path = Storage::url($filename);

                // Optionally, delete the old image file if exists
                if ($dataInternal->profile_image && Storage::disk('public')->exists($dataInternal->profile_image)) {
                    Storage::disk('public')->delete($dataInternal->profile_image);
                }

            }

            $nupMax = DataInternal::where('barang_id', $validated['barang_id'])->max('nup');
            $nup = $nupMax ? $nupMax + 1 : 1;
            $dataInternal->identitas_id = $identitas->id;
            $dataInternal->satker_id = $validated['satker_id'];
            $dataInternal->barang_id = $validated['barang_id'];
            $dataInternal->unit_kerja_id = $validated['unitkerja_id'];
            $dataInternal->lokasi_id = $validated['lokasi_id'];
            $dataInternal->nup = $nup;
            $dataInternal->tgl_perolehan = $validated['tgl_perolehan'];
            $dataInternal->merk = $validated['merk'];
            $dataInternal->tipe = $validated['tipe'];
            $dataInternal->jumlah = $validated['jumlah'];
            $dataInternal->nilai_aset = $validated['nilai_perolehan'];
            $dataInternal->kondisi = $validated['kondisi'];
            $dataInternal->akun_neraca = $validated['akun_neraca'];
            $dataInternal->pembukuan = $validated['pembukuan'];
            $dataInternal->link_dokumentasi = $validated['link_dokumentasi'];
            $dataInternal->nama_pengguna = $validated['name'];
            $dataInternal->alamat_pengguna = $validated['address'];
            if (isset($validated['profileImage'])) {
                $dataInternal->profile_image = $filename;
                $dataInternal->profile_image_path = $path;
            }

            $activeAttributeIds = $identitas->atribut->pluck('id')->toArray();
            // Remove DataAtribut entries that are no longer relevant
            DataAtribut::where('data_internal_id', $dataInternal->id)
                ->whereNotIn('atributs_id', $activeAttributeIds)
                ->delete();

            foreach ($identitas->atribut as $attr) {

                $value = $request->atribut[$attr->id] ?? null;

                if ($attr->pivot->is_required && blank($value)) {
                    throw ValidationException::withMessages([
                        "atribut.{$attr->id}" => "{$attr->label} is required"
                    ]);
                }

                $payload = match ($attr->data_type) {
                    'number' => ['value_integer' => $value],
                    'date'   => ['value_date' => $value],
                    default  => ['value_string' => $value],
                };

                DataAtribut::updateOrCreate(
                    [
                        'data_internal_id' => $dataInternal->id,
                        'atributs_id' => $attr->id,
                    ],
                    $payload
                );
            }

            $dataInternal->save();
            DB::commit();
            Alert::success('Sukses!', 'Data Internal berhasil diupdate');
            return redirect()->route('internal.show', $dataInternal->id)->with('Sukses!', 'Data Internal berhasil diupdate!');
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::error('Gagal!', 'Terjadi kesalahan: ' . ($th->getMessage()))->persistent(true);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $Data = DataInternal::findOrFail($id);
            if ($Data->profile_image && Storage::disk('public')->exists($Data->profile_image)) {
                Storage::disk('public')->delete($Data->profile_image);
            }
            $images = FotoInternal::where('data_internal_id', $id)->get();
            foreach ($images as $image) {
                // Delete the image file from storage
                if (Storage::disk('public')->exists($image->filename)) {
                    Storage::disk('public')->delete($image->filename);
                    }
                    // Delete the database record
                    $image->delete();
            }
            $Data->delete();
            DB::commit();

            // if ($request->ajax()) {
            //     }
            Alert::success('Sukses!', $Data->barang->nama_barang . ' - ' . $Data->barang->nup . ' - ' .'Data Internal berhasil dihapus');
            // return response()->json(['success' => true, 'message' => 'Data Internal berhasil dihapus!']);
            return redirect()->back()->with('Sukses!', $Data->barang->nama_barang . ' - ' . $Data->barang->nup . ' - ' .'Data Internal berhasil dihapus!');
        }
        catch (\Throwable $th) {
            DB::rollBack();
            Alert::error('Gagal!', 'Terjadi kesalahan: ' . ($th->getMessage()))->persistent(true);
            // return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $th->getMessage()], 500);
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $th->getMessage());

        }

    }

    public function destroyBatch(Request $request)
    {
        $Data = DataInternal::where('batch', $request->batch)->delete();
        Alert::success('Sukses!', $Data. ' - ' . 'Data Internal berhasil dihapus');
        return redirect()->back()->with('Sukses!', $Data. ' - ' . 'Data Internal berhasil dihapus!');
    }

    public function datatable(Request $request)
    {
        $query = DataInternal::query()
            ->with([
                // 'bmns:id,name',
                'satkers:id,kode_satker',
                'barang:id,kode_barang,nama_barang',
                'unitKerja:id,name,nameId',
                // 'penggunas:id,data_internal_id,nama_pengguna,alamat_pengguna',
                'lokasiRuang:id,name'
            ])
            ->select([
                'id',
                'satker_id',
                'barang_id',
                'nup',
                'tgl_perolehan',
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
                'penggunaRaw',
                'nama_pengguna',
                'alamat_pengguna',
                'lokasi_id',
                'status_inven',
                'update_kondisi',
                'link_dokumentasi',
                'link_lhi',
                'no_bahi',
                'tgl_bahi',
                'batch',
                'label'
            ]);

        return DataTables::eloquent($query)
            ->addIndexColumn()

            // relations
            // ->editColumn('bmn', fn ($row) => $row->bmns->name ?? '-')
            ->editColumn('kode_satker', fn ($row) => $row->satkers ? $row->satkers->kode_satker : '-')
            // ->editColumn('nama_satker', fn ($row) => $row->satkers->nama_satker ?? '-')
            ->editColumn('kode_barang', fn ($row) => $row->barang ? $row->barang->kode_barang : '-')
            ->editColumn('nama_barang', fn ($row) => $row->barang ? $row->barang->nama_barang : '-')
            ->editColumn('unit_kerja_id', fn ($row) => $row->unitKerja ? $row->unitKerja->name : '-')
            // ->editColumn('nama_pengguna', fn ($row) => $row->penggunas ? $row->penggunas->nama_pengguna : '-')
            // ->editColumn('alamat_pengguna', fn ($row) => $row->penggunas ? $row->penggunas->alamat_pengguna : '-')
            ->editColumn('lokasi_id', fn ($row) => $row->lokasiRuang ? $row->lokasiRuang->name : '-')
            // ->editColumn('tgl_perolehan', function ($row) {
            //     return $row->tgl_perolehan
            //         ? \Carbon\Carbon::parse($row->tgl_perolehan)->format('Y-m-d')
            //         : '-';
            // })

            ->editColumn('batch', function ($row) {
                if (!$row->batch && !$row->label) {
                    return '-';
                }

                return $row->batch . ' — ' . $row->label;
            })
            // currency formatting (DISPLAY ONLY)
            ->editColumn('nilai_aset', fn ($row) =>
                'Rp. ' . number_format($row->nilai_aset, 2, ',', '.')
            )
            ->editColumn('nilai_penyusutan', fn ($row) =>
                'Rp. ' . number_format($row->nilai_penyusutan, 2, ',', '.')
            )
            ->editColumn('nilai_buku', fn ($row) =>
                'Rp. ' . number_format($row->nilai_buku, 2, ',', '.')
            )
            // ->addColumn('action', function ($row) {
            //     return view('internal.partials.action', compact('row'))->render();
            // })

            // raw numeric for sorting
            ->orderColumn('nilai_aset', 'nilai_aset $1')
            ->orderColumn('nilai_penyusutan', 'nilai_penyusutan $1')
            ->orderColumn('nilai_buku', 'nilai_buku $1')
            // ->orderColumn('tgl_perolehan', 'tgl_perolehan $1')

            // ->filter(function ($query) use ($request) {



                // kode satker (partial)
                // if ($request->filled('satkerSearch')) {
                //     $query->whereHas('satkers', function ($q) use ($request) {
                //         $q->where('kodesatker', 'LIKE', '%' . $request->satkerSearch . '%');
                //     });
                // }

                // jenis BMN (exacts)
                // if ($request->filled('nupSearch')) {
                //     $query->whereHas('bmns', function ($q) use ($request) {
                //         $q->where('name', $request->bmnSearch);
                //     });
                // }
            // })
             ->filter(function ($query) use ($request) {
                // kode barang (select)
                if ($request->filled('itemSearch')) {
                    $query->whereHas('barang', function ($q) use ($request) {
                        $q->where('kode_barang', $request->itemSearch);
                    });
                }

                // pengguna (partial)
                // if ($request->filled('penggunaSearch')) {
                //     $query->whereHas('pengguna', function ($q) use ($request) {
                //         $q->where('nama_pengguna', $request->penggunaSearch . '%');
                //     });
                // }

                // lokasi (partial)
                if ($request->filled('lokasiSearch')) {
                    $query->whereHas('lokasiRuang', function ($q) use ($request) {
                        $q->where('id', $request->lokasiSearch);
                    });
                }

                // unit kerja select
                if ($request->filled('unitSearch')) {
                    $query->whereHas('unitKerja', function ($q) use ($request) {
                        $q->where('id', $request->unitSearch);
                    });
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


                if ($search = $request->input('search.value')) {
                    $query->where(function ($q) use ($search) {
                        $q
                        ->orWhere('data_internals.merk', 'like', "%{$search}%")
                        ->orWhere('data_internals.jumlah', 'like', "%{$search}%")
                        ->orWhere('data_internals.nilai_aset', 'like', "%{$search}%")
                        ->orWhere('data_internals.penggunaRaw', 'like', "%{$search}%")
                        ->orWhere('data_internals.akun_neraca', 'like', "%{$search}%")
                        ->orWhere('data_internals.pembukuan', 'like', "%{$search}%")
                        // ->orWhere('data_internals.unit_kerja', 'like', "%{$search}%")
                        ->orWhere('data_internals.nama_pengguna', 'like', "%{$search}%")
                        ->orWhere('data_internals.alamat_pengguna', 'like', "%{$search}%")
                        ->orWhere('data_internals.label', 'like', "%{$search}%")
                        ->orWhere('data_internals.tipe', 'like', "%{$search}%");
                    });
                }
            })
            // ->rawColumns(['action'])
            ->make(true);
    }


    public function make() {
        $title = 'Tambah Data Internal';
        $barang = Barang::get();
        $unitkerja = UnitKerja::get();
        $satker = satker::get();
        $lokasi = LokasiRuang::with('unitKerja')->get();
        $identitas = Identitas::all();
        return view('internal.make', compact('title','barang','unitkerja','satker','lokasi','identitas'));


    }

    public function insert(Request $request) {
        DB::beginTransaction();
        try {
            $identitas = Identitas::with('atribut')->findOrFail($request->identitas_id);

            // Soft validation
            foreach ($identitas->atribut as $attr) {
                if ($attr->pivot->is_required &&
                    empty($request->atribut[$attr->id] ?? null)) {
                    throw ValidationException::withMessages([
                        $attr->key => "{$attr->label} is required"
                    ]);
                }
            }

            // Validate the request
            $validated = $request->validate([
                'satker_id' => 'required|exists:satkers,id',
                'barang_id' => 'required|exists:barangs,id',
                'unitkerja_id' => 'required|exists:unit_kerjas,id',
                'lokasi_id' => 'required|exists:lokasi_ruangs,id',
                'tgl_perolehan' => 'required|date',
                'merk' => 'nullable|string',
                'tipe' => 'nullable|string',
                'jumlah' => 'required|integer|min:1',
                'nilai_perolehan' => 'required|numeric',
                'kondisi' => 'required|in:B,RR,RB',
                'akun_neraca' => 'nullable|string',
                'pembukuan' => 'nullable|string',
                'link_dokumentasi' => 'nullable|url',
                'profileImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'name' => 'nullable|string',
                'address' => 'nullable|string',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'titles' => 'nullable|array',
                'titles.*' => 'nullable|string',
                'descriptions' => 'nullable|array',
                'descriptions.*' => 'nullable|string',
                'isCover' => 'nullable|array',
                'isCover.*' => 'boolean',
            ]);

            // Handle profile image upload if provided
            $profileImagePath = null;
            $profileImageUrl = null;
            if ($request->hasFile('profileImage')) {
                $profileImagePath = $request->file('profileImage')->store('profile_images', 'public');
                $profileImageUrl = Storage::url($profileImagePath);
            }

            $nupMax = DataInternal::where('barang_id', $validated['barang_id'])->max('nup');
            $nup = $nupMax ? $nupMax + 1 : 1;

            // Create the DataInternal record
            $dataInternal = DataInternal::create([
                'satker_id' => $validated['satker_id'],
                'barang_id' => $validated['barang_id'],
                'lokasi_id' => $validated['lokasi_id'],
                'identitas_id' => $identitas->id,
                'nup' => $nup,
                'unit_kerja_id' => $validated['unitkerja_id'],
                'tgl_perolehan' => $validated['tgl_perolehan'],
                'merk' => $validated['merk'],
                'tipe' => $validated['tipe'],
                'jumlah' => $validated['jumlah'],
                'nilai_aset' => $validated['nilai_perolehan'],
                'kondisi' => $validated['kondisi'],
                'akun_neraca' => $validated['akun_neraca'],
                'pembukuan' => $validated['pembukuan'],
                'link_dokumentasi' => $validated['link_dokumentasi'],
                // 'penggunaRaw' => $validated['name'],
                'profile_image' => $profileImagePath,
                'profile_image_path' => $profileImageUrl,
                'nama_pengguna' => $validated['name'],
                'alamat_pengguna' => $validated['address'],
                // 'batch' => (DataInternal::max('batch') ?? 0) + 1,
                'label' => 'Manual Entry',
            ]);

            // Handle multiple images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $filename = $file->store('foto_internals', 'public');
                    $path = Storage::url($filename);
                    FotoInternal::create([
                        'data_internal_id' => $dataInternal->id,
                        'filename' => $filename,
                        'path' => $path,
                        'title' => $request->input("titles.{$index}", ''),
                        'description' => $request->input("descriptions.{$index}", ''),
                        'is_cover' => $request->input("isCover.{$index}", false),
                    ]);
                }
            }

            foreach ($identitas->atribut as $attr) {
            $value = $request->atribut[$attr->id] ?? null;
            if ($value === null) continue;

            $payload = match ($attr->data_type) {
                'number' => ['value_integer' => $value],
                'date'   => ['value_date' => $value],
                default  => ['value_string' => $value],
            };

            $dataInternal->dataAtribut()->create([
                'data_internal_id' => $dataInternal->id,
                'atributs_id' => $attr->id,
                ...$payload
            ]);
            }

            DB::commit();

            if ($request->ajax()) {
                Alert::success('Success', 'Data Internal berhasil ditambahkan');
                return response()->json(['success' => true, 'redirect' => route('internal.index')]);
            }
            Alert::success('Success', 'Data Internal berhasil ditambahkan');
            return redirect()->route('internal.index');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            throw $e;
        }
    }
}
