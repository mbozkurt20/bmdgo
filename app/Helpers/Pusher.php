<?php

namespace App\Helpers;

class Pusher {

    static function trigger($channel, $event, $data)
    {
        $options = array(
            'cluster' => env('PUSHER_APP_CLUSTER'),  // Örn: 'eu'
            'useTLS' => true
        );

        $pusher = new \Pusher\Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $pusher->trigger($channel, $event, $data);
    }
}
