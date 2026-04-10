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

        $yearFrom = $request->input('year_from', $request->input('year', 'All'));
        $yearTo = $request->input('year_to');

        if ($yearFrom === 'All' || empty($yearFrom)) {
            $yearValue = 'All';
        } else {
            $from = (int) $yearFrom;
            $to = $yearTo ? (int) $yearTo : $from;

            if ($to < $from) {
                $tmp = $from;
                $from = $to;
                $to = $tmp;
            }

            $yearValue = ($from === $to) ? (string) $from : ($from . '-' . $to);
        }

        DB::transaction(function () use ($userId, $yearValue) {
            $setting = Setting::where('key', 'year_filter')->where('account_id', $userId)->first();

            if ($setting) {
                $setting->value = $yearValue;
                $setting->updated_by = $userId;
                $setting->save();

                $this->saveLogs('Updated user setting: year_filter to ' . $yearValue);
            } else {
                Setting::create([
                    'account_id' => $userId,
                    'key' => 'year_filter',
                    'value' => $yearValue,
                    'created_by' => $userId,
                    'updated_by' => $userId
                ]);

                $this->saveLogs('Created user setting: year_filter to ' . $yearValue);
            }
        });

        return redirect()->back();
    }
}
