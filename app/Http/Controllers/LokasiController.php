<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use App\Models\LokasiRuang;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class LokasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'lokasi data';
        $lokasi = LokasiRuang::get();
        $unitKerja = UnitKerja::get();
        return view('lokasi.index', compact('lokasi', 'title', 'unitKerja'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah lokasi';
        $unitkerja = UnitKerja::get();

        return view('lokasi.create', compact('title', 'unitkerja'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         try {

            $rules = [
                'unitkerja_id' => 'required|exists:unit_kerjas,id',
                'name' => 'required'
            ];

            $messages = [
                'name.required' => 'Nama lokasi tidak dapat kosong.',
                'unitkerja_id.required' => 'Unit Kerja tidak dapat kosong.',
                'unitkerja_id.exists' => 'Unit Kerja yang dipilih tidak valid.'
            ];

            $validation = Validator::make($request->all(), $rules, $messages);

            if ($validation->fails()) {
                $errors = $validation->errors();

                // Ambil pesan error spesifik untuk password jika ada
                if ($errors->has('name')) {
                    Alert::error('Gagal!', $errors->first('name'));
                }
                if ($errors->has('unitkerja_id')) {
                    Alert::error('Gagal!', $errors->first('unitkerja_id'));
                }
                return redirect()->back()->withErrors($errors)->withInput();
            }


            LokasiRuang::create([
                'unit_kerja_id' => $request->unitkerja_id,
                'name' => $request->name
            ]);
            Alert::success('Sukses!', 'lokasi berhasil ditambahkan!');
            return redirect()->to('lokasi')->with('Sukses!', 'lokasi berhasil ditambahkan!');
        }
        // catch (ValidationException $e) {
        //     return redirect()->back()->withErrors($e->validator)->withInput();
        // }
        catch (\Throwable $th) {
            Alert::error('Gagal!', 'Terjadi kesalahan: ' . ($th));
            return redirect()->back()->withInput();
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
        $title = 'Edit lokasi';
        $lokasi = LokasiRuang::find($id);
        $unitkerja = UnitKerja::get();
        // return $lokasi;
        return view('lokasi.edit', compact('title', 'lokasi', 'unitkerja'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rules = [
            'name' => 'required',
            'unitkerja_id' => 'required|exists:unit_kerjas,id'
            ];

            $messages = [
                'name.required' => 'Nama lokasi tidak dapat kosong.',
                'unitkerja_id.required' => 'Unit Kerja tidak dapat kosong.',
                'unitkerja_id.exists' => 'Unit Kerja yang dipilih tidak valid.'
            ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            $errors = $validation->errors();

            // Ambil pesan error spesifik untuk password jika ada
            if ($errors->has('name')) {
                    Alert::error('Gagal!', $errors->first('name'));
            }
            if ($errors->has('unitkerja_id')) {
                    Alert::error('Gagal!', $errors->first('unitkerja_id'));
            } else {
                Alert::error('Gagal!', 'Terjadi kesalahan validasi. Silakan periksa kembali.');
            }

            return redirect()->back()->withErrors($errors)->withInput();
        }

        $lokasi = LokasiRuang::findOrFail($id);
        $lokasi->name = $request->name;
        $lokasi->unit_kerja_id = $request->unitkerja_id;

        $lokasi->save();

        Alert::success('Sukses!', 'lokasi berhasil diupdate');
        return redirect()->route('lokasi.index')->with('Sukses!', 'lokasi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lokasi = LokasiRuang::find($id);
        $lokasi->delete();
        Alert::success('Sukses!', 'lokasi berhasil dihapus');
        return redirect()->back()->with('Sukses!', 'lokasi berhasil dihapus!');
    }
}
