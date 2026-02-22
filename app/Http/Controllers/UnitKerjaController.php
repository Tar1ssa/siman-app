<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class UnitKerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Unit Kerja data';
        $UnitKerjas = UnitKerja::get();
        return view('unitkerja.index', compact('UnitKerjas', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Unit Kerja';
        return view('unitkerja.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|unique:unit_kerjas,name',
                'nameId' => 'required|unique:unit_kerjas,nameId'
            ];

            $messages = [
                'name.required' => 'Nama Unit Kerja tidak dapat kosong.',
                'name.unique' => 'Nama Unit Kerja sudah terdapat di database.',
                'nameId.required' => 'Kode Unit Kerja tidak dapat kosong.',
                'nameId.unique' => 'Kode Unit Kerja sudah terdapat di database.',
            ];

            $validation = Validator::make($request->all(), $rules, $messages);

            if ($validation->fails()) {
                $errors = $validation->errors();

                if ($errors->has('nameId')) {
                    Alert::error('Gagal!', $errors->first('nameId'));
                }
                if ($errors->has('name')) {
                    Alert::error('Gagal!', $errors->first('name'));
                }
                return redirect()->back()->withErrors($errors)->withInput();
            }

            UnitKerja::create([
                'name' => $request->name,
                'nameId' => $request->nameId
            ]);

            Alert::success('Sukses!', 'Unit Kerja berhasil ditambahkan!');
            return redirect()->to('unitkerja')->with('Sukses!', 'Unit Kerja berhasil ditambahkan!');
        } catch (\Throwable $th) {
            Alert::error('Gagal!', 'Terjadi kesalahan: ' . ($th));
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Edit Unit Kerja';
        $unitkerja = UnitKerja::find($id);
        return view('unitkerja.edit', compact('title', 'unitkerja'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rules = [
            'name' => 'required',
            'nameId' => 'required'
        ];

        $messages = [
            'name.required' => 'Nama Unit Kerja tidak dapat kosong.',
            'nameId.required' => 'Kode Unit Kerja tidak dapat kosong.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            $errors = $validation->errors();

            if ($errors->has('nameId')) {
                Alert::error('Gagal!', $errors->first('nameId'));
            }
            if ($errors->has('name')) {
                Alert::error('Gagal!', $errors->first('name'));
            } else {
                Alert::error('Gagal!', 'Terjadi kesalahan validasi. Silakan periksa kembali.');
            }

            return redirect()->back()->withErrors($errors)->withInput();
        }

        $UnitKerja = UnitKerja::findOrFail($id);
        $UnitKerja->name = $request->name;
        $UnitKerja->nameId = $request->nameId;
        $UnitKerja->save();

        Alert::success('Sukses!', 'Unit Kerja berhasil diupdate');
        return redirect()->route('unitkerja.index')->with('Sukses!', 'Unit Kerja berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $UnitKerja = UnitKerja::find($id);
        $UnitKerja->delete();
        Alert::success('Sukses!', 'Unit Kerja berhasil dihapus');
        return redirect()->back()->with('Sukses!', 'Unit Kerja berhasil dihapus!');
    }
}
