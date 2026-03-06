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
        ]);

        Setting::updateOrCreate(
            ['key' => 'admin_phone'],
            ['value' => $request->admin_phone]
        );

        Alert::success('Success', 'Settings updated successfully');
        return redirect()->back();
    }
}
