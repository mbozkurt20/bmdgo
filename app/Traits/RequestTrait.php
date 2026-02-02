<?php

namespace App\Traits;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


trait RequestTrait
{
    //For emitting Socket IO Messages
    public function sendSocketIONotification($channel, $body)
    {
        try {
            $headers = ['Content-Type' => 'application/json', 'Accept' => 'application/json'];
            $response = Http::withHeaders($headers)->withOptions(['verify'=>false])->post('https://panel.parskurye.net:5000/send-message', [
                'data' => $body,
                'channel' => $channel
            ]);

            return [
                'statusCode' => $response->getStatusCode(),
                'body' => json_decode($response->getBody(), true),
            ];
        } catch (Exception $e) {
            dd($e);
            return ['error' => $e->getMessage()];
        }
    }
}
