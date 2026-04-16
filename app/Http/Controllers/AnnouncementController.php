<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Announcement;
use App\AnnouncementView;
use Carbon\Carbon;
use Auth;
use Session;
use DB;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getActiveAnnouncements()
    {
        $user = Auth::user();
        // Fallback to Session('userData') if user attributes aren't directly on Auth::user
        $roleName = Session::has('userData') && isset(Session::get('userData')->role_name) 
            ? Session::get('userData')->role_name 
            : null;
            
        $accountId = Session::has('userData') && isset(Session::get('userData')->account_id) 
            ? (string) Session::get('userData')->account_id 
            : (string) $user->id;

        // Fetch announcements for tcd_portal that are not expired
        $announcements = Announcement::with('assets')->where('app', 'tcd_portal')
            ->where(function($query) {
                $query->whereNull('expiration_date')
                      ->orWhere('expiration_date', '>=', Carbon::now());
            })
            ->get();

        $activeAnnouncements = [];

        foreach ($announcements as $announcement) {
            // Check if the user has already viewed/acknowledged it
            $hasViewed = AnnouncementView::where('announcement_id', $announcement->id)
                ->where('account_id', $accountId)
                ->exists();

            if ($hasViewed) {
                continue;
            }

            // Check targets JSON for matching role or ID
            $targets = $announcement->targets;
            $isTargeted = false;

            if (is_array($targets)) {
                $targetRoles = $targets['roles'] ?? [];
                $targetIds = $targets['ids'] ?? [];

                if ((!empty($roleName) && in_array($roleName, $targetRoles)) || in_array($accountId, $targetIds)) {
                    $isTargeted = true;
                }
            }

            if ($isTargeted) {
                $activeAnnouncements[] = $announcement;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $activeAnnouncements
        ]);
    }

    public function acknowledge(Request $request)
    {
        $request->validate([
            'announcement_id' => 'required|integer'
        ]);

        $user = Auth::user();
        $accountId = Session::has('userData') && isset(Session::get('userData')->account_id) 
            ? (string) Session::get('userData')->account_id 
            : (string) $user->id;

        $view = AnnouncementView::firstOrCreate(
            [
                'announcement_id' => $request->announcement_id,
                'account_id' => $accountId
            ],
            [
                'viewed_at' => Carbon::now()
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Announcement acknowledged.'
        ]);
    }
}
