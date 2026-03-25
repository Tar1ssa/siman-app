<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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

class LockedDataController extends Controller
{
    /**
     * Display locked data dashboard
     */
    public function index()
    {
        $title = 'Locked Data Internal';
        $barang = Barang::orderBy('nama_barang', 'asc')->get();
        $batchNumber = DataInternal::select('batch', 'label')->distinct()->get();
        $unitkerja = UnitKerja::get();
        $lokasiruang = LokasiRuang::get();
        $identitasKategori = IdentitasKategori::with('identitas')->get();

        // return $barang;
        return view('internal.locked', compact('title', 'barang', 'batchNumber','unitkerja','lokasiruang', 'identitasKategori'));
    }
    /**
     * Get datatable data for locked records only
     */
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
                'identitas_id',
                'status',
                'is_requested',
            ])
            ->where('status', 'locked'); // Only show locked data
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
            ->addColumn('status', function ($row) {
                if ($row->status === 'locked') {
                    return '<span class="badge bg-danger">Terkunci</span>';
                } elseif ($row->status === 'unlocked') {
                    return '<span class="badge bg-warning">Dibuka</span>';
                } else {
                    return '<span class="badge bg-secondary">Draft</span>';
                }
            })
            ->addColumn('foto_count', function ($row) {
                return $row->fotoInternals->count();
            })
            ->addColumn('action', function ($row) {
                return view('internal.partials.action_locked', compact('row'))->render();
            })
            // raw numeric for sorting
            ->orderColumn('nilai_aset', 'nilai_aset $1')
            ->orderColumn('nilai_penyusutan', 'nilai_penyusutan $1')
            ->orderColumn('nilai_buku', 'nilai_buku $1')
            ->orderColumn('tgl_perolehan', 'tgl_perolehan $1')
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

                if ($request->filled('isRequestedSearch')) {
                    $query->where('is_requested', $request->isRequestedSearch);
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
            ->rawColumns(['action', 'status', 'identitas', 'foto_barang'])
            ->make(true);
    }
    /**
     * Lock data (Admin only)
     */
    public function lock(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            // Authorization handled by middleware
            $dataInternal = DataInternal::where('id', $id)->lockForUpdate()->firstOrFail();
            if ($dataInternal->status === 'locked') {
                throw new \Exception('Data sudah terkunci.');
            }
            $dataInternal->update(['status' => 'locked']);
            DB::commit();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Data berhasil dikunci']);
            }
            Alert::success('Sukses!', 'Data berhasil dikunci');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            Alert::error('Error!', $e->getMessage());
            return redirect()->back();
        }
    }
    /**
     * Unlock data (Admin only)
     */
    public function unlock(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            // Authorization handled by middleware
            $dataInternal = DataInternal::where('id', $id)->lockForUpdate()->firstOrFail();
            if ($dataInternal->status !== 'locked') {
                throw new \Exception('Data sudah tidak terkunci atau tidak dapat diunlock.');
            }
            $dataInternal->update(['status' => 'unlocked',
                'is_requested' => null, // reset request status on unlock
            ]);
            DB::commit();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Data berhasil dibuka kuncinya']);
            }
            Alert::success('Sukses!', 'Data berhasil dibuka kuncinya');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            Alert::error('Error!', $e->getMessage());
            return redirect()->back();
        }
    }

    public function requestUnlock(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $dataInternal = DataInternal::where('id', $id)->lockForUpdate()->firstOrFail();
            if ($dataInternal->status !== 'locked') {
                throw new \Exception('Data tidak terkunci, tidak perlu request unlock.');
            }
            if ($dataInternal->is_requested == 1) {
                throw new \Exception('Permintaan unlock sudah dikirim sebelumnya.');
            }
            $dataInternal->update(['is_requested' => 1]);
            DB::commit();
            if ($request->ajax()) {
                return response()->json(['success' => 1, 'message' => 'Permintaan pembukaan kunci berhasil dikirim']);
            }
            Alert::success('Sukses!', 'Permintaan pembukaan kunci berhasil dikirim');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            Alert::error('Error!', $e->getMessage());
            return redirect()->back();
        }
    }

    public function rejectRequest(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $dataInternal = DataInternal::where('id', $id)->lockForUpdate()->firstOrFail();
            if ($dataInternal->is_requested != 1) {
                throw new \Exception('Tidak ada permintaan unlock yang aktif.');
            }
            $dataInternal->update(['is_requested' => 0]);
            DB::commit();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Permintaan pembukaan kunci berhasil ditolak']);
            }
            Alert::success('Sukses!', 'Permintaan pembukaan kunci berhasil ditolak');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            Alert::error('Error!', $e->getMessage());
            return redirect()->back();
        }
    }
}
