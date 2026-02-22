<?php

namespace App\Http\Controllers;

use App\Models\UnitTeknis;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class UnitTeknisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Unit Teknis data';
        $UnitTeknises = UnitTeknis::get();
        return view('unitteknis.index', compact('UnitTeknises', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Unit Teknis';
        return view('unitteknis.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|unique:unit_teknis,name',
                'slug' => 'required|unique:unit_teknis,slug'
            ];

            $messages = [
                'name.required' => 'Nama Unit Teknis tidak dapat kosong.',
                'name.unique' => 'Nama Unit Teknis sudah terdapat di database.',
                'slug.required' => 'Slug tidak dapat kosong.',
                'slug.unique' => 'Slug sudah terdapat di database.',
            ];

            $validation = Validator::make($request->all(), $rules, $messages);

            if ($validation->fails()) {
                $errors = $validation->errors();

                if ($errors->has('slug')) {
                    Alert::error('Gagal!', $errors->first('slug'));
                }
                if ($errors->has('name')) {
                    Alert::error('Gagal!', $errors->first('name'));
                }
                return redirect()->back()->withErrors($errors)->withInput();
            }

            UnitTeknis::create([
                'name' => $request->name,
                'slug' => $request->slug
            ]);

            Alert::success('Sukses!', 'Unit Teknis berhasil ditambahkan!');
            return redirect()->to('unitteknis')->with('Sukses!', 'Unit Teknis berhasil ditambahkan!');
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
        $title = 'Edit Unit Teknis';
        $unitteknis = UnitTeknis::find($id);
        return view('unitteknis.edit', compact('title', 'unitteknis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rules = [
            'name' => 'required',
            'slug' => 'required'
        ];

        $messages = [
            'name.required' => 'Nama Unit Teknis tidak dapat kosong.',
            'slug.required' => 'Slug tidak dapat kosong.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            $errors = $validation->errors();

            if ($errors->has('slug')) {
                Alert::error('Gagal!', $errors->first('slug'));
            }
            if ($errors->has('name')) {
                Alert::error('Gagal!', $errors->first('name'));
            } else {
                Alert::error('Gagal!', 'Terjadi kesalahan validasi. Silakan periksa kembali.');
            }

            return redirect()->back()->withErrors($errors)->withInput();
        }

        $UnitTeknis = UnitTeknis::findOrFail($id);
        $UnitTeknis->name = $request->name;
        $UnitTeknis->slug = $request->slug;
        $UnitTeknis->save();

        Alert::success('Sukses!', 'Unit Teknis berhasil diupdate');
        return redirect()->route('unitteknis.index')->with('Sukses!', 'Unit Teknis berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $UnitTeknis = UnitTeknis::find($id);
        $UnitTeknis->delete();
        Alert::success('Sukses!', 'Unit Teknis berhasil dihapus');
        return redirect()->back()->with('Sukses!', 'Unit Teknis berhasil dihapus!');
    }
}
