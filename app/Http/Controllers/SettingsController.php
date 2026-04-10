<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Setting;
use App\Traits\LogQueries;
use DB;

class SettingsController extends Controller
{
    use LogQueries;
    public function updateEngineerReminder(Request $request)
    {
        $userId = session('userData') ? session('userData')->account_id : 'System';

        DB::transaction(function () use ($request, $userId) {
            $setting = Setting::where('key', 'engineer_reminder')->whereNull('account_id')->first();

            if ($setting) {
                $setting->value = $request->engineer_reminder;
                $setting->updated_by = $userId;
                $setting->save();

                $this->saveLogs('Updated system setting: engineer_reminder');
            }
        });

        return redirect()->back();
    }

    public function updateYearFilter(Request $request)
    {
        $userId = session('userData') ? session('userData')->account_id : 'System';

        DB::transaction(function () use ($request, $userId) {
            $setting = Setting::where('key', 'year_filter')->where('account_id', $userId)->first();

            if ($setting) {
                $setting->value = $request->year;
                $setting->updated_by = $userId;
                $setting->save();

                $this->saveLogs('Updated user setting: year_filter to ' . $request->year);
            } else {
                Setting::create([
                    'account_id' => $userId,
                    'key' => 'year_filter',
                    'value' => $request->year,
                    'created_by' => $userId,
                    'updated_by' => $userId
                ]);

                $this->saveLogs('Created user setting: year_filter to ' . $request->year);
            }
        });

        return redirect()->back();
    }
}
