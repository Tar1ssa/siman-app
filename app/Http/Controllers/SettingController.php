<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class SettingController extends Controller
{
    public function index()
    {
        $title = 'Settings';
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings.index', compact('title', 'settings'));
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {


            $request->validate([
                'admin_phone' => 'string|max:20',
                'biro' => 'string|max:40',
                'nip_biro' => 'string|max:40',
            ]);

            Setting::updateOrCreate(
                ['key' => 'admin_phone'],
                ['value' => $request->admin_phone]
            );

            Setting::updateOrCreate(
                ['key' => 'biro'],
                ['value' => $request->biro]
            );

            Setting::updateOrCreate(
                ['key' => 'nip_biro'],
                ['value' => $request->nip_biro]
            );

            Setting::updateOrCreate(
                ['key' => 'kepada'],
                ['value' => $request->kepada]
            );

            Setting::updateOrCreate(
                ['key' => 'jabatan'],
                ['value' => $request->jabatan]
            );

            Setting::updateOrCreate(
                ['key' => 'lokasi'],
                ['value' => $request->lokasi]
            );

            DB::commit();
            Alert::success('Success', 'Settings updated successfully');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::error('Error', 'Failed to update settings: ' . $th->getMessage());
            return redirect()->back();
        }

    }
}
