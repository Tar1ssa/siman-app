<?php

namespace App\Http\Controllers;

use App\Models\satker;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class satkerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Satker data';
        $Satkers = satker::get();
        return view('satker.index', compact('Satkers', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $title = 'Tambah Satker';
        return view('satker.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $rules = [
                'nama_satker' => 'required|unique:satkers,nama_satker',
                'kode_satker' => 'required|unique:satkers,kode_satker'
            ];

            $messages = [
                'nama_satker.required' => 'Nama Satker tidak dapat kosong.',
                'nama_satker.unique' => 'Satker sudah terdapat di database.',
                'kode_satker.required' => 'Kode Satker tidak dapat kosong.',
                'kode_satker.unique' => 'Kode Satker sudah terdapat di database.',
            ];

            $validation = Validator::make($request->all(), $rules, $messages);

            if ($validation->fails()) {
                $errors = $validation->errors();

                // Ambil pesan error spesifik untuk password jika ada
                if ($errors->has('kode_satker')) {
                    Alert::error('Gagal!', $errors->first('kode_satker'));
                }
                if ($errors->has('nama_satker')) {
                    Alert::error('Gagal!', $errors->first('nama_satker'));
                }
                return redirect()->back()->withErrors($errors)->withInput();
            }


            Satker::create([
                'kode_satker' => $request->kode_satker,
                'nama_satker' => $request->nama_satker
            ]);
            Alert::success('Sukses!', 'Satker berhasil ditambahkan!');
            return redirect()->to('satker')->with('Sukses!', 'Satker berhasil ditambahkan!');
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
        $title = 'Edit Satker';
        $Satker = Satker::find($id);
        // return $Satker;
        return view('Satker.edit', compact('title', 'Satker'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rules = [
                'nama_satker' => 'required',
                'kode_satker' => 'required'
            ];

            $messages = [
                'nama_satker.required' => 'Nama Satker tidak dapat kosong.',
                'kode_satker.required' => 'Nomor Satker tidak dapat kosong.',
            ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            $errors = $validation->errors();

            // Ambil pesan error spesifik untuk password jika ada
            if ($errors->has('kode_satker')) {
                    Alert::error('Gagal!', $errors->first('kode_satker'));
            }
            if ($errors->has('nama_satker')) {
                    Alert::error('Gagal!', $errors->first('nama_satker'));
            } else {
                Alert::error('Gagal!', 'Terjadi kesalahan validasi. Silakan periksa kembali.');
            }

            return redirect()->back()->withErrors($errors)->withInput();
        }

        $Satker = Satker::findOrFail($id);
        $Satker->nama_satker = $request->nama_satker;
        $Satker->kode_satker = $request->kode_satker;

        $Satker->save();

        Alert::success('Sukses!', 'Satker berhasil diupdate');
        return redirect()->route('satker.index')->with('Sukses!', 'Satker berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Satker = Satker::find($id);
        $Satker->delete();
        Alert::success('Sukses!', 'Satker berhasil dihapus');
        return redirect()->back()->with('Sukses!', 'Satker berhasil dihapus!');
    }
}
