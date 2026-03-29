<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
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
        $request->validate([
            'admin_phone' => 'required|string|max:20',
            'biro' => 'required|string|max:40',
            'nip_biro' => 'required|string|max:40',
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

        Alert::success('Success', 'Settings updated successfully');
        return redirect()->back();
    }
}
