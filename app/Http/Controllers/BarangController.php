<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Barang data';
        $Barangs = Barang::get();
        return view('barang.index', compact('Barangs', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Barang';
        return view('barang.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         try {

            $rules = [
                'nama_barang' => 'required|unique:barangs,nama_barang',
                'kode_barang' => 'required|unique:barangs,kode_barang'
            ];

            $messages = [
                'nama_barang.required' => 'Nama Barang tidak dapat kosong.',
                'nama_barang.unique' => 'Barang sudah terdapat di database.',
                'kode_barang.required' => 'Kode Barang tidak dapat kosong.',
                'kode_barang.unique' => 'Kode Barang sudah terdapat di database.',
            ];

            $validation = Validator::make($request->all(), $rules, $messages);

            if ($validation->fails()) {
                $errors = $validation->errors();

                // Ambil pesan error spesifik untuk password jika ada
                if ($errors->has('kode_Barang')) {
                    Alert::error('Gagal!', $errors->first('kode_barang'));
                }
                if ($errors->has('nama_barang')) {
                    Alert::error('Gagal!', $errors->first('nama_barang'));
                }
                return redirect()->back()->withErrors($errors)->withInput();
            }


            Barang::create([
                'kode_barang' => $request->kode_barang,
                'nama_barang' => $request->nama_barang
            ]);
            Alert::success('Sukses!', 'Barang berhasil ditambahkan!');
            return redirect()->to('barang')->with('Sukses!', 'Barang berhasil ditambahkan!');
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
        $title = 'Edit Barang';
        $barang = Barang::find($id);
        // return $Barang;
        return view('barang.edit', compact('title', 'barang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rules = [
                'nama_barang' => 'required',
                'kode_barang' => 'required'
            ];

            $messages = [
                'nama_barang.required' => 'Nama Barang tidak dapat kosong.',
                'kode_barang.required' => 'Kode Barang tidak dapat kosong.',
            ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            $errors = $validation->errors();

            // Ambil pesan error spesifik untuk password jika ada
            if ($errors->has('kode_barang')) {
                    Alert::error('Gagal!', $errors->first('kode_barang'));
            }
            if ($errors->has('nama_barang')) {
                    Alert::error('Gagal!', $errors->first('nama_barang'));
            } else {
                Alert::error('Gagal!', 'Terjadi kesalahan validasi. Silakan periksa kembali.');
            }

            return redirect()->back()->withErrors($errors)->withInput();
        }

        $Barang = Barang::findOrFail($id);
        $Barang->nama_barang = $request->nama_barang;
        $Barang->kode_barang = $request->kode_barang;

        $Barang->save();

        Alert::success('Sukses!', 'Barang berhasil diupdate');
        return redirect()->route('barang.index')->with('Sukses!', 'Barang berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Barang = Barang::find($id);
        $Barang->delete();
        Alert::success('Sukses!', 'Barang berhasil dihapus');
        return redirect()->back()->with('Sukses!', 'Barang berhasil dihapus!');
    }
}
