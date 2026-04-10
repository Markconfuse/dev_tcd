<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Setting;
use App\Traits\LogQueries;
use DB;

class SettingsController extends Controller
{
    use LogQueries;
    public function update(Request $request)
    {
        $userId = session('userData') ? session('userData')->account_id : 'System';

        DB::transaction(function () use ($request, $userId) {
            foreach ($request->except('_token') as $key => $value) {
                $setting = Setting::where('key', $key)->first();

                if ($setting) {
                    $setting->value = $value;
                    $setting->updated_by = $userId;
                    $setting->save();

                    $this->saveLogs('Updated system setting: ' . $key);
                }
            }
        });

        return redirect()->back();
    }
}
