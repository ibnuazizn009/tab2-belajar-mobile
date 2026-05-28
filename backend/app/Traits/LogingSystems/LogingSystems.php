<?php

namespace App\Traits\LogingSystems;

use Carbon\Carbon;
use App;

trait LogingSystems
{
    /**
     * Log activity to the LogingUsers table.
     *
     * @param string $activity
     * @param \Illuminate\Http\Request $request
     * @param mixed|null $data
     */
    // public function logActivity(string $activity, $request, $data = null)
    // {
    //     return App\Models\Master\LogingUsers::create([
    //         'users_id' => auth()->id(),
    //         'ipaddress' => $request->ip(),
    //         'browser' => $request->userAgent(),
    //         'activity' => $activity,
    //         'url' => $request->fullUrl(),
    //         'method' => $request->method(),
    //         'keterangan' => $activity . ' successfully. Data: ' . $data,
    //         'device' => $request->header('User-Agent'),
    //         'tgllogin' => Carbon::now()->toDateTimeString(),
    //         'waktulogin' => Carbon::now()->toTimeString(),
    //     ]);
    // }
}
