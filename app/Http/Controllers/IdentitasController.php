<?php

namespace App\Http\Controllers;

use App\Models\Atribut;
use App\Models\Identitas;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class IdentitasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Identitas';
        $identitas = Identitas::withCount('atribut')->get();
        return view('identitas.index', compact('identitas', 'title'));
    }   

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $atribut = Atribut::all();
        $title = 'Tambah Identitas';
        return view('identitas.create', compact('atribut', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:identitas,name',
            'slug' => 'required|unique:identitas,slug',
        ]);

        $identitas = Identitas::create($request->only('name', 'slug'));

        $this->syncAttributes($identitas, $request);
        Alert::success('Success', 'Identitas created successfully');
        return redirect()->route('identitas.index')->with('success', 'Identitas created');
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
        $identitas = Identitas::findOrFail($id);
        $identitas->load('atribut');
        $atribut = Atribut::all();
        $title = 'Edit Identitas';
        return view('identitas.edit', compact('identitas', 'atribut', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $identitas = Identitas::findOrFail($id);
        $request->validate([
            'name' => 'required|unique:identitas,name,' . $identitas->id,
            'slug' => 'required|unique:identitas,slug,' . $identitas->id,
        ]);

        $identitas->update($request->only('name', 'slug'));
        $this->syncAttributes($identitas, $request);
        Alert::success('Success', 'Identitas updated successfully');    
        return redirect()->route('identitas.index')->with('success', 'Identitas updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $identitas = Identitas::findOrFail($id);
        $identitas->delete();
        Alert::success('Success', 'Identitas deleted successfully');
        return back()->with('success', 'Identitas deleted');
    }

    public function atributByIdentitas(Identitas $identitas)
    {
        return $identitas->atribut()->get()->map(fn ($attr) => [
            'id' => $attr->id,
            'label' => $attr->label,
            'key' => $attr->key,
            'type' => $attr->data_type,
            'required' => $attr->pivot->is_required,
            'placeholder' => $attr->pivot->placeholder,
            'help_text' => $attr->pivot->help_text,
        ]);
    }

    private function syncAttributes(Identitas $identitas, Request $request)
    {
        $syncData = [];

        foreach ($request->input('atribut', []) as $attrId => $data) {

            
            if (!isset($data['enabled'])) {
                continue;
            }

            $syncData[$attrId] = [
                'is_required' => isset($data['is_required']),
                'sort_order' => $data['sort_order'] ?? 0,
                'placeholder' => $data['placeholder'] ?? null,
                'help_text' => $data['help_text'] ?? null,
            ];
        }

        $identitas->atribut()->sync($syncData);
    }

}
