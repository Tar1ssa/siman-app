<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\bmn;
use App\Models\Barang;
use App\Models\satker;
use App\Models\Pengguna;
use App\Models\Identitas;
use App\Models\UnitKerja;
use App\Models\UnitTeknis;
use App\Models\DataAtribut;
use App\Models\InvalidData;
use App\Models\LokasiRuang;
use Illuminate\Support\Str;
use App\Models\DataInternal;
use App\Models\FotoInternal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\IdentitasKategori;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;

class InternalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Data Internal';
        $barang = Barang::orderBy('nama_barang', 'asc')->get();
        $batchNumber = DataInternal::select('batch', 'label')->distinct()->get();
        $unitkerja = UnitKerja::get();
        $lokasiruang = LokasiRuang::get();
        $identitasKategori = IdentitasKategori::with('identitas')->get();

        // return $barang;
        return view('internal.index', compact('title', 'barang', 'batchNumber','unitkerja','lokasiruang', 'identitasKategori'));
    }

    /**
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
        $skippedCount = 0;
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
                        $reasons[] = 'Duplikasi kodebarang + NUP dalam CSV'.', '. 'Kode/Nama Barang : ' . $row['kodebarang'] . ' - ' . $row['uraianbarang'] . ' ' . 'NUP :' . $row['nup'];
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
                $message = implode('; ', array_slice($allErrors, 0, 10));
                if (count($allErrors) > 10) {
                    $message .= ' (+' . (count($allErrors) - 10) . ' more)';
                }
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors' => $allErrors
                ], 422);

            }

            //  If EVERYTHING is valid → INSERT ALL

            foreach (array_chunk($mapped, 500) as $chunk) {
                foreach ($chunk as $row) {
                        $tglBAHI = $this->normalizeCsvDateToYMD($row['tanggalbahiberitaacarahasilinven']);

                        $perolehan_tgl = $this->normalizeCsvDateToYMD($row['tahunperolehan']);

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
                'message'  => 'Import selesai. Jumlah data invalid: ' . $invalidCount
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
        $internal = DataInternal::with([
            // 'bmns',
            'satkers',
            'barang',
            'unitKerja',
            // 'penggunas',
            'lokasiRuang',
            'identitas.identitasKategori',
            'dataAtribut.atribut'
        ])->findOrFail($id);
        $identitas = Identitas::get() ?? collect();
        $internalImages = FotoInternal::where('data_internal_id', $id)->get() ?? collect();
        $satker = satker::all() ?? collect();
        $title = 'Show Data Internal';
        $barang = Barang::get() ?? collect();
        $identitasKategori = IdentitasKategori::get() ?? collect();
        $lokasi = LokasiRuang::get() ?? collect();
        $unitkerja = UnitKerja::get() ?? collect();
        $unitteknis = UnitTeknis::get() ?? collect();
        return view('internal.view', compact('internal', 'satker', 'title', 'barang', 'unitkerja', 'internalImages', 'lokasi','identitas', 'unitteknis', 'identitasKategori'));
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
            'identitas.identitasKategori',
            'dataAtribut'
        ])->findOrFail($id);

        // Check if user can access this data based on unitKerja
        if (!$this->canAccessDataInternal($internal)) {
            Alert::error('Error', 'Anda tidak memiliki akses.');
            return redirect()->route('internal.index');
        }

        $identitas = $internal->identitas ? Identitas::where('kategori_id', $internal->identitas->kategori_id)->get() : collect();
        $internalImages = FotoInternal::where('data_internal_id', $id)->get() ?? collect();
        $satker = satker::all() ?? collect();
        $title = 'Edit Data Internal';
        $barang = Barang::get() ?? collect();
        $user = Auth::user();

        if (strtolower($user->level->level_name) === 'administrator') {
            $unitkerja = UnitKerja::all();
            $lokasi = LokasiRuang::with('unitKerja')->get();
        } else {
            $unitkerja = UnitKerja::where('id', $user->unit_kerja_id)->get();
            $lokasi = LokasiRuang::with('unitKerja')->where('unit_kerja_id', $user->unit_kerja_id)->get();
        }

        $unitteknis = UnitTeknis::get() ?? collect();
        $identitasKategori = IdentitasKategori::get() ?? collect();
        $dataAtribut = $internal->dataAtribut ? $internal->dataAtribut->keyBy('atributs_id') : collect();
        return view('internal.edit', compact('internal', 'satker', 'title', 'barang', 'unitkerja', 'internalImages', 'lokasi', 'identitas', 'dataAtribut','unitteknis', 'identitasKategori'));
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


            $dataInternalId = DataInternal::with('unitKerja')->findOrFail($request->internal_id);

            // Check if user can access this data based on unitKerja
            if (!$this->canAccessDataInternal($dataInternalId)) {
                throw new \Exception('Anda tidak memiliki akses.');
            }

            $file = $request->file('image');
            $unitKerjaName = Str::slug($dataInternalId->unitKerja->name ?? 'tanpa-unit-kerja');
            $barangName = Str::slug($dataInternalId->barang->nama_barang ?? 'tanpa-nama-barang');
            $nup = $dataInternalId->nup ?? 'nonup';
            $filename = $file->storeAs(
                    'foto_internals/'. $unitKerjaName,
                    "internal-{$barangName}-{$nup}-{$dataInternalId->merk}-{$dataInternalId->tipe}-{$validated['title']}-". time() .Str::uuid()."." .$file->getClientOriginalExtension() ,
                    'public'
                );

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
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal menambahkan gambar: ' . $th->getMessage()], 500);
            } else {
                Alert::error('Gagal', 'Gagal menambahkan gambar: ' . $th->getMessage());
                return redirect()->back()->with('error', 'Gagal menambahkan gambar: ' . $th->getMessage());
            }
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

            // Check if user can access the parent DataInternal
            $dataInternal = DataInternal::findOrFail($image->data_internal_id);
            if (!$this->canAccessDataInternal($dataInternal)) {
                throw new \Exception('Anda tidak memiliki akses.');
            }

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
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal memperbarui gambar: ' . $th->getMessage()], 500);
            } else {
                Alert::error('Gagal', 'Gagal memperbarui gambar: ' . $th->getMessage());
                return redirect()->back()->with('error', 'Gagal memperbarui gambar: ' . $th->getMessage());
            }
        }
    }

    public function imageDestroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $imageId = $request->id;
            $image = FotoInternal::findOrFail($imageId);

            // Check if user can access the parent DataInternal
            $dataInternal = DataInternal::findOrFail($image->data_internal_id);
            if (!$this->canAccessDataInternal($dataInternal)) {
                throw new \Exception('Anda tidak memiliki akses.');
            }

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
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus gambar: ' . $th->getMessage()], 500);
            } else {
                Alert::error('Gagal', 'Gagal menghapus gambar: ' . $th->getMessage());
                return redirect()->back()->with('error', 'Invalid image ID.');
            }
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
                'pembukuan' => 'nullable|string',
                'link_dokumentasi' => 'nullable|url',
                'profileImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'name' => 'nullable|string',
                'pengguna_unitkerja_id' => 'nullable|exists:unit_kerjas,id',
                'unit_teknis_id' => 'nullable|exists:unit_teknis,id',
                'nip_pengguna' => 'nullable|string',
                'jabatan_pengguna' => 'nullable|string',
                'alamat_pengguna' => 'nullable|string',
                'nama_pihak_pertama' => 'nullable|string',
                'nip_pihak_pertama' => 'nullable|string',
                'jabatan_pihak_pertama' => 'nullable|string',
                'alamat_pihak_pertama' => 'nullable|string',

            ]);

            $identitas = identitas::with('atribut')->findOrFail($request->identitas_id);


            $dataInternal = DataInternal::findOrFail($id);

            // Check if user can access this data based on unitKerja
            if (!$this->canAccessDataInternal($dataInternal)) {
                throw new \Exception('Anda tidak memiliki akses.');
            }

            if ($request->hasFile('profileImage')) {
                $file = $request->file('profileImage');
                // Use the new unit kerja name from the form, not the old one from database
                $newUnitKerja = UnitKerja::findOrFail($validated['unitkerja_id']);
                $unitKerjaName = Str::slug($newUnitKerja->name ?? 'tanpa-unit-kerja');
                $filename = $file->storeAs("profile_images/{$unitKerjaName}", "profile-image-{$validated['merk']}-{$validated['tipe']}-{$validated['name']}". time().Str::uuid() .".". $request->file('profileImage')->getClientOriginalExtension(), 'public');
                $validated['profileImage'] = $filename;
                $path = Storage::url($filename);

                // Optionally, delete the old image file if exists
                if ($dataInternal->profile_image && Storage::disk('public')->exists($dataInternal->profile_image)) {
                    Storage::disk('public')->delete($dataInternal->profile_image);
                }

            }

            // Check if unit_kerja_id is changing and handle existing profile image
            $unitKerjaChanged = $dataInternal->unit_kerja_id != $validated['unitkerja_id'];
            if ($unitKerjaChanged && $dataInternal->profile_image) {
                // Unit kerja is changing and there's an existing profile image
                $oldUnitKerja = $dataInternal->unitKerja;
                $newUnitKerja = UnitKerja::findOrFail($validated['unitkerja_id']);

                $oldUnitKerjaName = Str::slug($oldUnitKerja->name ?? 'tanpa-unit-kerja');
                $newUnitKerjaName = Str::slug($newUnitKerja->name ?? 'tanpa-unit-kerja');

                // Only move if the folder names are different
                if ($oldUnitKerjaName !== $newUnitKerjaName) {
                    $oldPath = $dataInternal->profile_image;
                    $newPath = str_replace("profile_images/{$oldUnitKerjaName}", "profile_images/{$newUnitKerjaName}", $oldPath);

                    // Move the file in storage
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->move($oldPath, $newPath);
                        $dataInternal->profile_image = $newPath;
                        $dataInternal->profile_image_path = Storage::url($newPath);
                    }
                }
            }



            $nupMax = DataInternal::where('barang_id', $validated['barang_id'])->max('nup');
            $nup = $nupMax ? $nupMax + 1 : 1;
            $dataInternal->identitas_id = $identitas->id;
            $dataInternal->satker_id = $validated['satker_id'];
            $dataInternal->barang_id = $validated['barang_id'];
            $dataInternal->unit_kerja_id = $validated['unitkerja_id'];
            $dataInternal->pengguna_unitkerja_id = $validated['pengguna_unitkerja_id'] ?: null;
            $dataInternal->unit_teknis_id = $validated['unit_teknis_id'] ?: null;
            $dataInternal->lokasi_id = $validated['lokasi_id'];
            $dataInternal->nup = $nup;
            $dataInternal->tgl_perolehan = $validated['tgl_perolehan'];
            $dataInternal->merk = $validated['merk']?? null;
            $dataInternal->tipe = $validated['tipe']?? null;
            $dataInternal->jumlah = $validated['jumlah'];
            $dataInternal->nilai_aset = $validated['nilai_perolehan'];
            $dataInternal->kondisi = $validated['kondisi'];
            $dataInternal->pembukuan = $validated['pembukuan'] ?? null;
            $dataInternal->link_dokumentasi = $validated['link_dokumentasi'] ?? null;
            $dataInternal->nama_pengguna = $validated['name']?? null;
            $dataInternal->nip_pengguna = $validated['nip_pengguna']?? null;
            $dataInternal->jabatan_pengguna = $validated['jabatan_pengguna']?? null;
            $dataInternal->alamat_pengguna = $validated['alamat_pengguna']?? null;
            $dataInternal->nama_pihak_pertama = $validated['nama_pihak_pertama']?? null;
            $dataInternal->nip_pihak_pertama = $validated['nip_pihak_pertama']?? null;
            $dataInternal->jabatan_pihak_pertama = $validated['jabatan_pihak_pertama']?? null;
            $dataInternal->alamat_pihak_pertama = $validated['alamat_pihak_pertama']?? null;


            if (isset($validated['profileImage'])) {
                $dataInternal->profile_image = $filename;
                $dataInternal->profile_image_path = $path;
            }

            // Handle FotoInternal images when unit_kerja_id changes
            if ($unitKerjaChanged) {
                Log::info("Unit kerja changed for data_internal {$dataInternal->id}, handling foto internal images");
                $newUnitKerja = UnitKerja::findOrFail($validated['unitkerja_id']);
                $newUnitKerjaName = Str::slug($newUnitKerja->name ?? 'tanpa-unit-kerja');

                $fotoInternals = FotoInternal::where('data_internal_id', $dataInternal->id)->get();
                Log::info("Found {$fotoInternals->count()} foto internal records to move");

                foreach ($fotoInternals as $fotoInternal) {
                    $oldPath = $fotoInternal->filename;

                    // Extract the unit kerja folder name from the existing path
                    // Path structure: foto_internals/unit-kerja-name/filename
                    $pathParts = explode('/', $oldPath);
                    if (count($pathParts) >= 3 && $pathParts[0] === 'foto_internals') {
                        $oldUnitKerjaNameFromPath = $pathParts[1]; // The folder name after foto_internals/

                        // Only move if the folder names are different
                        if ($oldUnitKerjaNameFromPath !== $newUnitKerjaName) {
                            $newPath = str_replace("foto_internals/{$oldUnitKerjaNameFromPath}", "foto_internals/{$newUnitKerjaName}", $oldPath);

                            Log::info("Moving foto internal from {$oldPath} to {$newPath}");

                            // Move the file in storage
                            if (Storage::disk('public')->exists($oldPath)) {
                                Storage::disk('public')->move($oldPath, $newPath);
                                $fotoInternal->filename = $newPath;
                                $fotoInternal->path = Storage::url($newPath);
                                $fotoInternal->save();
                                Log::info("Successfully moved foto internal {$fotoInternal->id}");
                            } else {
                                // Log warning if file doesn't exist
                                Log::warning("FotoInternal file not found: {$oldPath}");
                            }
                        } else {
                            Log::info("Unit kerja folder names are the same ({$oldUnitKerjaNameFromPath}), no need to move file");
                        }
                    } else {
                        Log::warning("Unexpected foto internal path format: {$oldPath}");
                    }
                }
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




            // return redirect()
            //     ->route('internal.show', $dataInternal->id)
            //     ->with('success', 'Data berhasil diupdate');
            if ($request->ajax()) {
                    // Alert::success('Success', 'Data Internal berhasil diupdate!');
                    return response()->json([
                    'success' => true,
                    'id' => $dataInternal->id,
                    'redirect' => route('internal.show', $dataInternal->id)
                    ]);
                    }

            Alert::success('Sukses!', 'Data Internal berhasil diupdate');
            return redirect()
                ->route('internal.show', $dataInternal->id)
                ->with('success', 'Data berhasil diupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $th->getMessage()], 500);
            } else {
                Alert::error('Gagal!', 'Terjadi kesalahan: ' . ($th->getMessage()))->persistent(true);
                return redirect()->back()->withInput();
            }
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

            // Check if user can access this data based on unitKerja
            if (!$this->canAccessDataInternal($Data)) {
                throw new \Exception('Anda tidak memiliki akses.');
            }

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

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Data Internal berhasil dihapus!'], 200);
            } else {
                Alert::success('Sukses!', $Data->barang->nama_barang . ' - ' . $Data->barang->nup . ' - ' .'Data Internal berhasil dihapus');
                return redirect()->back()->with('success', $Data->barang->nama_barang . ' - ' . $Data->barang->nup . ' - ' .'Data Internal berhasil dihapus!');
            }
        }
        catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $th->getMessage()], 500);
            } else {
                Alert::error('Gagal!', 'Terjadi kesalahan: ' . ($th->getMessage()))->persistent(true);
                return redirect()->back()->with('error', 'Gagal menghapus data: ' . $th->getMessage());
            }
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
                'lokasiRuang:id,name',
                'identitas.identitasKategori',
                'dataAtribut.atribut',
                'fotoInternals' => function($query) {
                    $query->where('is_cover', true);
                }
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
                'pembukuan',
                'unit_kerja_id',
                'penggunaRaw',
                'nama_pengguna',
                'akun_neraca',
                'lokasi_id',
                'status_inven',
                'update_kondisi',
                'link_dokumentasi',
                'link_lhi',
                'no_bahi',
                'tgl_bahi',
                'batch',
                'label',
                'identitas_id'
            ]);

        return DataTables::eloquent($query)
            ->addIndexColumn()

            // relations
            // ->editColumn('bmn', fn ($row) => $row->bmns->name ?? '-')
            ->editColumn('kode_satker', fn ($row) => $row->satkers ? $row->satkers->kode_satker : '-')
            // ->editColumn('nama_satker', fn ($row) => $row->satkers->nama_satker ?? '-')
            ->editColumn('kode_barang', fn ($row) => $row->barang ? $row->barang->kode_barang : '-')
            ->editColumn('nama_barang', fn ($row) => $row->barang ? $row->barang->nama_barang : '-')
            ->editColumn('foto_barang', function ($row) {
                $coverImage = $row->fotoInternals->first();
                if ($coverImage) {
                    return '<img src="' . asset($coverImage->path) . '" alt="Foto Barang" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;" onclick="openImageModal(\'' . asset($coverImage->path) . '\', \'' . ($coverImage->title ?? 'Foto Barang') . '\')">';
                }
                return '<span class="text-muted">-</span>';
            })
            ->editColumn('unit_kerja_id', fn ($row) => $row->unitKerja ? $row->unitKerja->name : '-')
            // ->editColumn('nama_pengguna', fn ($row) => $row->penggunas ? $row->penggunas->nama_pengguna : '-')
            ->editColumn('lokasi_id', fn ($row) => $row->lokasiRuang ? $row->lokasiRuang->name : '-')
            ->editColumn('identitas', function ($row) {
                if (!$row->identitas) {
                    return '-';
                }

                $category = $row->identitas->identitasKategori ? $row->identitas->identitasKategori->name : 'No Category';
                $identitasName = $row->identitas->name;

                $attributes = '';
                if ($row->dataAtribut && $row->dataAtribut->count() > 0) {
                    $attributes = '<br><small>';
                    foreach ($row->dataAtribut as $attr) {
                        $value = $attr->value_string ?? $attr->value_integer ?? $attr->value_date ?? '-';
                        $attributes .= $attr->atribut->label . ': ' . $value . '<br>';
                    }
                    $attributes .= '</small>';
                }

                return $category . ' - ' . $identitasName . $attributes;
            })
            ->editColumn('tgl_perolehan', function ($row) {
                return $row->tgl_perolehan
                    ? \Carbon\Carbon::parse($row->tgl_perolehan)->format('Y-m-d')
                    : '-';
            })

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
            ->addColumn('action', function ($row) {
                return view('internal.partials.action', compact('row'))->render();
            })

            // raw numeric for sorting
            ->orderColumn('nilai_aset', 'nilai_aset $1')
            ->orderColumn('nilai_penyusutan', 'nilai_penyusutan $1')
            ->orderColumn('nilai_buku', 'nilai_buku $1')
            ->orderColumn('tgl_perolehan', 'tgl_perolehan $1')

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

                // kategori identitas filter
                if ($request->filled('kategoriIdentitasSearch')) {
                    $query->whereHas('identitas.identitasKategori', function ($q) use ($request) {
                        $q->where('id', $request->kategoriIdentitasSearch);
                    });
                }

                // identitas filter
                if ($request->filled('identitasSearch')) {
                    $query->whereHas('identitas', function ($q) use ($request) {
                        $q->where('id', $request->identitasSearch);
                    });
                }

                // NUP range filter
                if ($request->filled('nupMin') && $request->filled('nupMax')) {
                    $query->whereBetween('nup', [$request->nupMin, $request->nupMax]);
                } elseif ($request->filled('nupMin')) {
                    $query->where('nup', '>=', $request->nupMin);
                } elseif ($request->filled('nupMax')) {
                    $query->where('nup', '<=', $request->nupMax);
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
                        ->orWhere('data_internals.label', 'like', "%{$search}%")
                        ->orWhere('data_internals.tipe', 'like', "%{$search}%");
                    });
                }
            })
            ->rawColumns(['action', 'identitas', 'foto_barang'])
            ->make(true);
    }


    public function make() {
        $title = 'Tambah Data Internal';
        $barang = Barang::get();
        $user = Auth::user();

        if (strtolower($user->level->level_name) === 'administrator') {
            $unitkerja = UnitKerja::all();
            $lokasi = LokasiRuang::with('unitKerja')->get();
        } else {
            $unitkerja = UnitKerja::where('id', $user->unit_kerja_id)->get();
            $lokasi = LokasiRuang::with('unitKerja')->where('unit_kerja_id', $user->unit_kerja_id)->get();
        }
        $unitteknis = UnitTeknis::get();
        $satker = satker::get();
        $identitasKategori = IdentitasKategori::with('identitas')->get();
        return view('internal.make', compact('title', 'barang', 'unitkerja', 'satker', 'lokasi', 'identitasKategori', 'unitteknis'));
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
                        "atribut.{$attr->id}" => "{$attr->label} is required"
                    ]);
                }
            }

            // Validate the request
            $validated = $request->validate([
                'satker_id' => 'required|exists:satkers,id',
                'barang_id' => 'required|exists:barangs,id',
                'unitkerja_id' => 'required|exists:unit_kerjas,id',
                'lokasi_id' => 'required|exists:lokasi_ruangs,id',
                'pengguna_unitkerja_id' => 'nullable|exists:unit_kerjas,id',
                'unit_teknis_id' => 'nullable|exists:unit_teknis,id',
                'tgl_perolehan' => 'required|date',
                'merk' => 'nullable|string',
                'tipe' => 'nullable|string',
                'jumlah' => 'required|integer|min:1',
                'nilai_perolehan' => 'required|numeric',
                'kondisi' => 'required|in:B,RR,RB',
                'pembukuan' => 'nullable|string',
                'link_dokumentasi' => 'nullable|url',
                'profileImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'name' => 'nullable|string',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'titles' => 'nullable|array',
                'titles.*' => 'nullable|string',
                'descriptions' => 'nullable|array',
                'descriptions.*' => 'nullable|string',
                'isCover' => 'nullable|array',
                'isCover.*' => 'boolean',
                'nip_pengguna' => 'nullable|string',
                'jabatan_pengguna' => 'nullable|string',
                'alamat_pengguna' => 'nullable|string',
                'nama_pihak_pertama' => 'nullable|string',
                'nip_pihak_pertama' => 'nullable|string',
                'jabatan_pihak_pertama' => 'nullable|string',
                'alamat_pihak_pertama' => 'nullable|string',
            ]);

            // Handle profile image upload if provided
            $profileImagePath = null;
            $profileImageUrl = null;
            if ($request->hasFile('profileImage')) {
                $unitKerjaId = UnitKerja::findOrFail($validated['unitkerja_id']);
                $unitKerjaName = Str::slug($unitKerjaId->name ?? 'tanpa-unit-kerja');

                $profileImagePath = $request->file('profileImage')->storeAs('profile_images/'. $unitKerjaName, "profile-image-{$validated['merk']}-{$validated['tipe']}-{$validated['name']}". time().Str::uuid() .".". $request->file('profileImage')->getClientOriginalExtension(), 'public');
                $profileImageUrl = Storage::url($profileImagePath);
            }

            $nupMax = DataInternal::where('barang_id', $validated['barang_id'])->max('nup');
            $nup = $nupMax ? $nupMax + 1 : 1;

            // Convert empty strings to null for nullable integer fields
            $validated['pengguna_unitkerja_id'] = $validated['pengguna_unitkerja_id'] ?: null;
            $validated['unit_teknis_id'] = $validated['unit_teknis_id'] ?: null;


            // Create the DataInternal record
            $dataInternal = DataInternal::create([
                'satker_id' => $validated['satker_id'],
                'barang_id' => $validated['barang_id'],
                'lokasi_id' => $validated['lokasi_id'] ?? null,
                'pengguna_unitkerja_id' => $validated['pengguna_unitkerja_id'] ?? null,
                'unit_teknis_id' => $validated['unit_teknis_id'] ?? null,
                'identitas_id' => $identitas->id,
                'nup' => $nup,
                'unit_kerja_id' => $validated['unitkerja_id'] ?? null,
                'tgl_perolehan' => $validated['tgl_perolehan'] ?? null,
                'merk' => $validated['merk'] ?? null,
                'tipe' => $validated['tipe'] ?? null,
                'jumlah' => $validated['jumlah'] ?? null,
                'nilai_aset' => $validated['nilai_perolehan'] ?? null,
                'kondisi' => $validated['kondisi'] ?? null,
                'pembukuan' => $validated['pembukuan'] ?? null,
                'link_dokumentasi' => $validated['link_dokumentasi'] ?? null,
                // 'penggunaRaw' => $validated['name'],
                'profile_image' => $profileImagePath,
                'profile_image_path' => $profileImageUrl,
                'nama_pengguna' => $validated['name'] ?? null,
                'nip_pengguna' => $validated['nip_pengguna']?? null,
                'jabatan_pengguna' => $validated['jabatan_pengguna']?? null,
                'alamat_pengguna' => $validated['alamat_pengguna']?? null,
                'nama_pihak_pertama' => $validated['nama_pihak_pertama']?? null,
                'nip_pihak_pertama' => $validated['nip_pihak_pertama']?? null,
                'jabatan_pihak_pertama' => $validated['jabatan_pihak_pertama']?? null,
                'alamat_pihak_pertama' => $validated['alamat_pihak_pertama']?? null,
                // 'batch' => (DataInternal::max('batch') ?? 0) + 1,
                'label' => 'Manual Entry',

            ]);

            // Handle multiple images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $dataInternalId = DataInternal::with('unitKerja')->findOrFail($dataInternal->id);
                    $unitKerjaName = Str::slug($dataInternalId->unitKerja->name ?? 'tanpa-unit-kerja');
                    $barangName = Str::slug($dataInternalId->barang->nama_barang ?? 'tanpa-nama-barang');
                    $filename = $file->storeAs(
                            'foto_internals/'. $unitKerjaName,
                            "internal-{$barangName}-{$nup}-{$validated['merk']}-{$validated['tipe']}-{$validated['titles'][$index]}-"
                            . time() .Str::uuid()."." .$file->getClientOriginalExtension(),
                            'public'
                        );
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

            // Alert::success('Success', 'Data Internal berhasil ditambahkan');
            return response()->json(['success' => true, 'id' => $dataInternal->id, 'redirect' => route('internal.index')], 200);
            // if ($request->ajax()) {
            // }
            // Alert::success('Success', 'Data Internal berhasil ditambahkan');
            // return redirect()->route('internal.index');
        } catch (ValidationException $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            throw $e;
        }
    }

    public function downloadBast($id)
    {
        \Carbon\Carbon::setLocale('id');

        $data = DataInternal::with(
                                'barang',
                                'identitas',
                                'dataAtribut.atribut'
                                )->findOrFail($id);

        // build detailed keterangan: include identitas name and all atribut label:value pairs
        $parts = [];
        if ($data->identitas) {
            $parts[] = $data->identitas->name;
        }

        foreach ($data->dataAtribut as $da) {
            $label = $da->atribut->label ?? null;
            $value = null;
            if (!is_null($da->value_string) && $da->value_string !== '') $value = $da->value_string;
            elseif (!is_null($da->value_integer) && $da->value_integer !== '') $value = $da->value_integer;
            elseif (!is_null($da->value_date)) {
                try {
                    $value = \Carbon\Carbon::parse($da->value_date)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $value = (string) $da->value_date;
                }
            }

            if ($label && $value !== null && $value !== '') {
                $parts[] = $label . ': ' . $value;
            }
        }

        $keterangan = count($parts) ? implode('; ', $parts) : '-';

        $tgl = \Carbon\Carbon::now();
        $todayDate = \Carbon\Carbon::now();

        $pdf = Pdf::loadView('pdf.bast', [
            'barang' => $data->merk . ' ' . $data->tipe,
            'merk' => $data->merk,
            'spesifikasi' => $data->tipe,
            'jumlah' => $data->jumlah,
            'kondisi' => $data->kondisi,
            'tahun_perolehan' => $tgl->year,
            'keterangan' => $keterangan,

            // Tanggal di halaman utama
            'hari' => $todayDate->translatedFormat('l'),
            'tanggal' => $todayDate->day,
            'bulan' => $todayDate->translatedFormat('F'),
            'tahun' => $todayDate->year,

            // Pihak Kedua (dari DataInternal)
            'pihak_kedua_nama' => $data->nama_pengguna,
            'pihak_kedua_nip' => $data->nip_pengguna ?? '',
            'pihak_kedua_jabatan' => $data->jabatan_pengguna ?? '',
            'pihak_kedua_alamat' => $data->alamat_pengguna ?? '',

            // Pihak Pertama (sesuaikan dengan sistemmu)
            'pihak_pertama_nama' => $data->nama_pihak_pertama ?? '',
            'pihak_pertama_nip' => $data->nip_pihak_pertama ?? '',
            'pihak_pertama_jabatan' => $data->jabatan_pihak_pertama ?? '',
            'pihak_pertama_alamat' => $data->alamat_pihak_pertama ?? '',
        ]);

        return $pdf->stream("BAST-{$data->barang->nama_barang}-{$data->nup}-{$data->merk}-{$data->tipe}.pdf");
    }

    public function kategoriIdentitas($kategoriId)
    {
        $identitas = Identitas::where('kategori_id', $kategoriId)->get();
        return response()->json($identitas, 200);
    }

    /**
     * Check if the authenticated user can access the given DataInternal record
     * Administrators can access all records
     * Regular users can only access records from their own unitKerja
     */
    private function canAccessDataInternal(DataInternal $internal)
    {
        $user = Auth::user();

        // Administrators can access all data
        if (strtolower($user->level->level_name ?? '') === 'administrator') {
            return true;
        }

        // If user has no unitKerja assigned, deny access
        if (!$user->unitKerja) {
            return false;
        }

        // Check if user's unitKerja matches the data's unitKerja
        return $user->unitKerja->id === $internal->unit_kerja_id;
    }

    public function exportAll(Request $request)
    {
        $filePath = storage_path('app/internal_all.xlsx');

        $writer = new Writer();
        $writer->openToFile($filePath);

        $firstSheet = true;

        foreach ($this->internalAllSheets() as $sheetName => $rows) {

            // For next sheets, create new one
            if (!$firstSheet) {
                $writer->addNewSheetAndMakeItCurrent();
            }

            $firstSheet = false;

            // Rename sheet safely
            $writer->getCurrentSheet()->setName($this->sanitizeSheetName($sheetName));

            // Create header style
            $headerStyle = (new Style())
                ->setFontBold()
                ->setFontSize(11)
                ->setBackgroundColor(Color::rgb(220, 220, 220));

            // Set column widths (approximate character widths)
            $columnWidths = [
                15, // Kode Satker
                15, // Kode Barang
                30, // Nama Barang
                10, // NUP
                20, // Merk
                10, // Jumlah
                15, // Tgl Perolehan
                15, // Nilai Aset
                15, // Nilai Penyusutan
                15, // Nilai Buku
                10, // Kondisi
                15, // Akun Neraca
                15, // Pembukuan
                20, // Unit Kerja
                25, // Pengguna
                20, // Lokasi Ruang
                40, // Identitas
                15, // Status Inventaris
                20, // Update Kondisi
                25, // Link Dokumentasi
                25, // Link LHI
                15, // No BAHI
                15, // Tgl BAHI
            ];

            // Create data row style
            $dataStyle = new Style();

            // HEADER
            $headerRow = Row::fromValues([
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
                'Identitas',
                'Status Inventaris',
                'Update Kondisi',
                'Link Dokumentasi',
                'Link LHI',
                'No BAHI',
                'Tgl BAHI',
            ], $headerStyle);
            $writer->addRow($headerRow);

            // DATA ROWS
            foreach ($rows as $row) {
                // Format identitas with attributes
                $identitasFormatted = '';
                if ($row->identitas) {
                    $identitasFormatted = $row->kategori_identitas . ' - ' . $row->identitas;

                    // Get attributes for this data internal record
                    if ($row->identitas_id) {
                        $attributes = DB::table('data_atributs as da')
                            ->join('atributs as a', 'a.id', '=', 'da.atributs_id')
                            ->where('da.data_internal_id', $row->data_internal_id)
                            ->select('a.label', 'da.value_string', 'da.value_integer', 'da.value_date')
                            ->get();

                        if ($attributes->count() > 0) {
                            $identitasFormatted .= "\n";
                            foreach ($attributes as $attr) {
                                $value = $attr->value_string ?? $attr->value_integer ?? $attr->value_date ?? '-';
                                $identitasFormatted .= $attr->label . ': ' . $value . "\n";
                            }
                            $identitasFormatted = trim($identitasFormatted);
                        }
                    }
                }

                $dataRow = Row::fromValues([
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
                    $this->sanitizeForExcel($identitasFormatted),
                    $this->sanitizeForExcel($row->status_inven),
                    $this->sanitizeForExcel($row->update_kondisi),
                    $this->sanitizeForExcel($row->link_dokumentasi),
                    $this->sanitizeForExcel($row->link_lhi),
                    $this->sanitizeForExcel($row->no_bahi),
                    $this->sanitizeForExcel($row->tgl_bahi),
                ], $dataStyle);
                $writer->addRow($dataRow);
            }
        }

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    private function internalAllSheets()
    {
        $unitKerjas = UnitKerja::select('id', 'name')->get();

        // add virtual unit kerja for NULL
        $unitKerjas->push((object) [
            'id'   => null,
            'name' => 'Tanpa Unit Kerja',
        ]);

        $usedNames = [];

        foreach ($unitKerjas as $unitKerja) {
            $baseName = $unitKerja->name;
            // Sanitize sheet name: remove invalid characters and limit length
            $baseName = preg_replace('/[\/\\\?\*\[\]]/', '', $baseName); // Remove invalid chars
            $baseName = trim($baseName);
            if (empty($baseName)) {
                $baseName = 'Sheet';
            }
            // Limit sheet name to 31 characters (Excel limit)
            if (strlen($baseName) > 31) {
                $baseName = substr($baseName, 0, 31);
            }
            $sheetName = $baseName;

            // Ensure unique sheet name
            $counter = 1;
            while (in_array($sheetName, $usedNames)) {
                $suffix = ' (' . $counter . ')';
                $availableLength = 31 - strlen($suffix);
                $truncatedName = substr($baseName, 0, $availableLength);
                $sheetName = $truncatedName . $suffix;
                $counter++;
            }
            $usedNames[] = $sheetName;

            $rows = DB::table('data_internals as di')
                ->join('barangs as b', 'b.id', '=', 'di.barang_id')
                ->join('satkers as sat', 'sat.id', '=', 'di.satker_id')
                ->leftjoin('lokasi_ruangs as lokasi', 'lokasi.id', '=', 'di.lokasi_id')
                ->leftjoin('unit_kerjas as uk', 'uk.id', '=', 'di.unit_kerja_id')
                ->leftjoin('identitas as ident', 'ident.id', '=', 'di.identitas_id')
                ->leftjoin('identitas_kategoris as ik', 'ik.id', '=', 'ident.kategori_id')
                ->when(
                    is_null($unitKerja->id),
                    fn ($q) => $q->whereNull('di.unit_kerja_id'),
                    fn ($q) => $q->where('di.unit_kerja_id', $unitKerja->id)
                )
                ->select([
                    'di.id as data_internal_id',
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
                    DB::raw('COALESCE(ik.name, "No Category") as kategori_identitas'),
                    'ident.name as identitas',
                    'di.identitas_id',
                    'di.status_inven',
                    'di.update_kondisi',
                    'di.link_dokumentasi',
                    'di.link_lhi',
                    'di.no_bahi',
                    'di.tgl_bahi',
                ])
                ->orderBy('b.kode_barang')
                ->cursor(); // streaming

            // Skip empty sheets
            if ($rows->isEmpty()) {
                continue;
            }

            yield $sheetName => $rows;
        }
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
