<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Setting;

class SettingsController extends Controller
{
    public function update(Request $request)
    {
        $userId = session('userData') ? session('userData')->account_id : 'System';

        foreach ($request->except('_token') as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                $setting->value = $value;
                $setting->updated_by = $userId;
                $setting->save();
            }
        }

        return redirect()->back();
    }
}
