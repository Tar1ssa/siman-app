<?php

namespace App\Http\Controllers;

use App\Models\Atribut;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AtributController extends Controller
{
    public function index()
    {
        $title = 'Atribut';
        $atributs = Atribut::withCount('identitas')->get();
        return view('atribut.index', compact('atributs', 'title'));
    }

    public function create()
    {
        $title = 'Tambah Atribut';
        return view('atribut.create', compact('title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|alpha_dash|unique:atributs,key',
            'label' => 'required',
            'data_type' => 'required|in:string,number,date',
        ]);

        Atribut::create($request->only('key', 'label', 'data_type'));
        Alert::success('Success', 'Atribut created successfully');
        return redirect()->route('atribut.index')->with('success', 'Atribut created');
    }

    public function edit(Atribut $atribut)
    {
        $title = 'Edit Atribut';
        return view('atribut.edit', compact('atribut', 'title'));
    }

    public function update(Request $request, Atribut $atribut)
    {
        $request->validate([
            'key' => 'required|alpha_dash|unique:atributs,key,' . $atribut->id,
            'label' => 'required',
            'data_type' => 'required|in:string,number,date',
        ]);

        $atribut->update($request->only('key', 'label', 'data_type'));
        alert::success('Success', 'Atribut updated successfully');
        return redirect()->route('atribut.index')->with('success', 'Atribut updated');
    }

    public function destroy(Atribut $atribut)
    {
        if ($atribut->identitas()->exists()) {
            return back()->with('error', 'Attribute is used by categories');
        }
        
        $atribut->delete();
        alert::success('Success', 'Atribut deleted successfully');
        return back()->with('success', 'Atribut deleted');
    }
}
