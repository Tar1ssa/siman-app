<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Setting;
use App\Models\UnitKerja;
use App\Models\LokasiRuang;
use App\Models\DataInternal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\IdentitasKategori;

class PspController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Generate Dokumen PSP';
        $barang = Barang::orderBy('nama_barang', 'asc')->get();
        $batchNumber = DataInternal::select('batch', 'label')->distinct()->get();
        $unitkerja = UnitKerja::get();
        $lokasiruang = LokasiRuang::get();
        $identitasKategori = IdentitasKategori::with('identitas')->get();

        // return $barang;
        return view('psp.index', compact('title', 'barang', 'batchNumber','unitkerja','lokasiruang', 'identitasKategori'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function downloadPSP(Request $request)
    {
        // Get selected IDs from the request
        $selectedIds = $request->input('selected', []);
        
        // Get lampiran/attachment data
        $lampiran = $request->input('lampiran', []);
        
        // Fetch the selected DataInternal records
        $data = DataInternal::whereIn('id', $selectedIds)->with('barang')->get();

        $biro = Setting::where('key', 'biro')->first()->value ?? '';
        $nip_biro = Setting::where('key', 'nip_biro')->first()->value ?? '';
        
        // Set locale and date
        \Carbon\Carbon::setLocale('id');
        $todayDate = \Carbon\Carbon::now();
        
        // Prepare data for the PDF
        $pdfData = [
            'data' => $data,
            'biro' => $biro,
            'nip_biro' => $nip_biro,
            'lampiran' => $lampiran,
            'tahun' => $todayDate->year,
            'tanggal' => $todayDate->format('d F Y'),
        ];
        
        // Load the PDF view (assuming 'psp.blade.php' is the PDF template)
        $pdf = Pdf::loadView('pdf.psp', $pdfData)
                ->setPaper('A4', 'portrait');

        return $pdf->stream('surat PSP.pdf');
    }
}
