<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class JobController extends Controller
{
    function index()
    {
        if (request('key') !== config('app.cron_key')) {
            abort(403);
        }
        Artisan::call('queue:work --stop-when-empty');
    }

    function schedule()
    {
        if (request('key') !== config('app.schedule_key')) {
            abort(403);
        }
        Artisan::call('schedule:run --stop-when-empty');
    }
}
