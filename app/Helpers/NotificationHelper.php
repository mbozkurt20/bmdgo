<?php

namespace App\Helpers;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;

class NotificationHelper
{
    static function add($data)
    {
        $data['admin_id'] = $data['admin_id'] ?? Auth::id();
        \App\Models\Notification::create($data);

        $options = array (
            'cluster' => 'mt1',
            'useTLS' => true
        );

        $pusher = new Pusher (
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );


        $pusher->trigger('notifications-'.Auth::user()->id, 'new-notify-'.$data['admin_id'] ?? Auth::user()->id, $data);
    }
}
