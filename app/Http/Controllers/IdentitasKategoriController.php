<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IdentitasKategori;
use RealRashid\SweetAlert\Facades\Alert;

class IdentitasKategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $identitasKategori = IdentitasKategori::all();
        $title = 'Kategori Identitas';
        return view('identitaskategori.index', compact('identitasKategori', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Kategori Identitas';
        return view('identitaskategori.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:identitas_kategoris,name',
            'slug' => 'required|unique:identitas_kategoris,slug',
        ]);

        IdentitasKategori::create($request->only('name', 'slug'));
        Alert::success('Success', 'Kategori Identitas created successfully');
        return redirect()->route('identitas-kategori.index')->with('success', 'Kategori Identitas created');
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
        $kategoriIdentitas = IdentitasKategori::findOrFail($id);
        $title = 'Edit Kategori Identitas';
        return view('identitaskategori.edit', compact('kategoriIdentitas', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kategoriIdentitas = IdentitasKategori::findOrFail($id);
        $request->validate([
            'name' => 'required|unique:identitas_kategoris,name,' . $kategoriIdentitas->id,
            'slug' => 'required|unique:identitas_kategoris,slug,' . $kategoriIdentitas->id,
        ]);

        $kategoriIdentitas->update($request->only('name', 'slug'));
        Alert::success('Success', 'Kategori Identitas updated successfully');
        return redirect()->route('identitas-kategori.index')->with('success', 'Kategori Identitas updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kategoriIdentitas = IdentitasKategori::findOrFail($id);
        $kategoriIdentitas->delete();
        Alert::success('Success', 'Kategori Identitas deleted successfully');
        return redirect()->route('identitas-kategori.index')->with('success', 'Kategori Identitas deleted');
    }
}
