<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Level;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'User Management';
        $users = User::with('level', 'unitKerja')->get();
        return view('user.index', compact('users', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah User';
        $levels = Level::all();
        $unitkerjas = UnitKerja::all();
        return view('user.create', compact('title', 'levels', 'unitkerjas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'level_id' => 'required|exists:levels,id',
                'unit_kerja_id' => 'nullable|exists:unit_kerjas,id'
            ];

            $messages = [
                'name.required' => 'Nama User tidak dapat kosong.',
                'email.required' => 'Email tidak dapat kosong.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah terdaftar di database.',
                'password.required' => 'Password tidak dapat kosong.',
                'password.min' => 'Password minimal 6 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak sesuai.',
                'level_id.required' => 'Level user harus dipilih.',
            ];

            $validation = Validator::make($request->all(), $rules, $messages);

            if ($validation->fails()) {
                $errors = $validation->errors();
                Alert::error('Gagal!', $errors->first());
                return redirect()->back()->withErrors($errors)->withInput();
            }

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'level_id' => $request->level_id,
                'unit_kerja_id' => $request->unit_kerja_id ?? null,
            ]);

            Alert::success('Sukses!', 'User berhasil ditambahkan!');
            return redirect()->to('user')->with('Sukses!', 'User berhasil ditambahkan!');
        } catch (\Throwable $th) {
            Alert::error('Gagal!', 'Terjadi kesalahan: ' . $th->getMessage());
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
        $title = 'Edit User';
        $user = User::findOrFail($id);
        $levels = Level::all();
        $unitkerjas = UnitKerja::all();
        return view('user.edit', compact('title', 'user', 'levels', 'unitkerjas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $user = User::where('id', $id)->lockForUpdate()->firstOrFail();

            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'level_id' => 'required|exists:levels,id',
                'unit_kerja_id' => 'nullable|exists:unit_kerjas,id'
            ];

            $messages = [
                'name.required' => 'Nama User tidak dapat kosong.',
                'email.required' => 'Email tidak dapat kosong.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah terdaftar di database.',
                'level_id.required' => 'Level user harus dipilih.',
            ];

            $validation = Validator::make($request->all(), $rules, $messages);

            if ($validation->fails()) {
                $errors = $validation->errors();
                Alert::error('Gagal!', $errors->first());
                return redirect()->back()->withErrors($errors)->withInput();
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->level_id = $request->level_id;
            $user->unit_kerja_id = $request->unit_kerja_id ?? null;

            // Update password hanya jika diisi
            if ($request->filled('password')) {
                $passRules = [
                    'password' => 'string|min:6|confirmed',
                ];
                $passValidation = Validator::make($request->all(), $passRules);

                if ($passValidation->fails()) {
                    Alert::error('Gagal!', 'Password minimal 6 karakter dan konfirmasi harus sesuai.');
                    return redirect()->back()->withInput();
                }

                $user->password = $request->password;
            }

            $user->save();

            Alert::success('Sukses!', 'User berhasil diupdate');
            return redirect()->route('user.index')->with('Sukses!', 'User berhasil diperbarui');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return DB::transaction(function () use ($id) {
            $user = User::where('id', $id)->lockForUpdate()->firstOrFail();
            $userName = $user->name;
            $user->delete();

            Alert::success('Sukses!', $userName . ' berhasil dihapus');
            return redirect()->back()->with('Sukses!', $userName . ' berhasil dihapus!');
        });
    }
}
