<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name'        => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'posts_per_page'   => 'integer|min:1|max:100',
            'admin_email'      => 'nullable|email'
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('admin.settings.index')
                         ->with('success', 'Settings saved.');
    }
}