<?php

namespace App\Http\Controllers;

use App\Models\bmn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class BmnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'BMN data';
        $BMNs = bmn::get();
        return view('bmn.index', compact('BMNs', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Jenis BMN';
        return view('bmn.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         try {

            $rules = [
                'name' => 'required|unique:bmns,name',
            ];

            $messages = [
                'name.required' => 'Nama tidak dapat kosong.',
                'name.unique' => 'Nama sudah terdapat di database'
            ];

            $validation = Validator::make($request->all(), $rules, $messages);

            if ($validation->fails()) {
                $errors = $validation->errors();

                // Ambil pesan error spesifik untuk password jika ada
                if ($errors->has('name')) {
                    Alert::error('Gagal!', $errors->first('name'));
                }
                return redirect()->back()->withErrors($errors)->withInput();
            }

            $uppercase = strtoupper($request->name);


            bmn::create([
                'name' => $uppercase,
            ]);
            Alert::success('Sukses!', 'Jenis BMN berhasil ditambahkan!');
            return redirect()->to('bmn')->with('Sukses!', 'Jenis BMN berhasil ditambahkan!');
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
        $title = 'Edit Jenis BMN';
        $BMN = bmn::find($id);
        // return $BMN;
        return view('bmn.edit', compact('title', 'BMN'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $rules = [
                'name' => 'required',
            ];

            $messages = [
                'name.required' => 'Nama tidak dapat kosong.',
            ];

            $validation = Validator::make($request->all(), $rules, $messages);

            if ($validation->fails()) {
                $errors = $validation->errors();

                // Ambil pesan error spesifik untuk password jika ada
                if ($errors->has('name')) {
                    Alert::error('Gagal!', $errors->first('name'));
                } else {
                    Alert::error('Gagal!', 'Terjadi kesalahan validasi. Silakan periksa kembali.');
                }

                return redirect()->back()->withErrors($errors)->withInput();
            }

            $bmn = bmn::where('id', $id)->lockForUpdate()->firstOrFail();
            $bmn->name = $request->name;

            $bmn->save();

            Alert::success('Sukses!', 'jenis bmn berhasil diupdate');
            return redirect()->route('bmn.index')->with('Sukses!', 'jenis bmn berhasil diperbarui');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return DB::transaction(function () use ($id) {
            $bmn = bmn::where('id', $id)->lockForUpdate()->first();
            if ($bmn) {
                $bmn->delete();
            }
            Alert::success('Sukses!', 'bmn berhasil dihapus');
            return redirect()->back()->with('Sukses!', 'bmn berhasil dihapus!');
        });
    }
}
