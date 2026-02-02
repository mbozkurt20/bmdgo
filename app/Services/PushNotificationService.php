<?php

namespace App\Services;

use Exception;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class PushNotificationService
{
    private Messaging $client;

    public function __construct()
    {
        $this->client = Firebase::messaging();
    }

    /**
     * Send a push notification to a specific device
     *
     * @param  string  $token  FCM device token
     * @param  string  $title  Notification title
     * @param  string  $body   Notification body
     * @param  array   $data   Additional data to send
     *
     * @throws Exception
     */
    public function sendNotification(string $token, string $title, string $body, array $data = []): bool
    {
        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(
                    Notification::create($title, $body)
                )
                ->withAndroidConfig(AndroidConfig::fromArray([
                    'priority' => 'HIGH',
                    'notification' => [
                        'sound' => 'alarm', // android/app/src/main/res/raw/alarm.mp3
                        'channel_id' => 'high_importance_channel',
                        'notification_priority' => 'PRIORITY_MAX',
                        'default_sound' => false,
                    ],
                ]))
                ->withApnsConfig(ApnsConfig::fromArray([
                    'headers' => [
                        'apns-priority' => '10',
                        'apns-push-type' => 'alert',
                    ],
                    'payload' => [
                        'aps' => [
                            'sound' => 'alarm.mp3', // Xcode bundle içindeki alarm.mp3
                            'mutable-content' => 1,
                            'content-available' => 1,
                        ],
                    ],
                ]));

            // Eğer ek veri gönderilmek istenirse (genelde boş array gelir)
            if (!empty($data)) {
                $message = $message->withData($data);
            }

            $this->client->send($message);
            return true;

        } catch (MessagingException | FirebaseException $e) {
            throw new \Exception('Bildirim gönderme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Send push notifications to multiple devices
     *
     * @param  array   $tokens  Array of FCM device tokens
     * @param  string  $title   Notification title
     * @param  string  $body    Notification body
     * @param  array   $data    Additional data to send
     * @return array Array of successful and failed tokens
     *
     * @throws Exception
     */
    public function sendBulkNotifications(array $tokens, string $title, string $body, array $data = []): array
    {
        try {
            $message = CloudMessage::new()
                ->withNotification(
                    Notification::create($title, $body)
                )
                ->withAndroidConfig(AndroidConfig::fromArray([
                    'priority' => 'HIGH',
                    'notification' => [
                        'sound' => 'alarm',
                        'channel_id' => 'high_importance_channel',
                    ],
                ]))
                ->withApnsConfig(ApnsConfig::fromArray([
                    'payload' => [
                        'aps' => [
                            'sound' => 'alarm.mp3',
                        ],
                    ],
                ]));

            if (!empty($data)) {
                $message = $message->withData($data);
            }

            $report = $this->client->sendMulticast($message, $tokens);

            return [
                'success' => $report->successes()->count(),
                'failure' => $report->failures()->count(),
                'tokens' => [
                    'successful' => $this->getTokensFromReport($report, true),
                    'failed' => $this->getTokensFromReport($report, false),
                ],
            ];
        } catch (MessagingException | FirebaseException $e) {
            throw new Exception('Bulk bildirim hatası: ' . $e->getMessage());
        }
    }

    /**
     * Send a notification to a topic
     *
     * @param  string  $topic  Topic name
     * @param  string  $title  Notification title
     * @param  string  $body   Notification body
     * @param  array   $data   Additional data to send
     *
     * @throws Exception
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        try {
            $message = CloudMessage::new()
                ->toTopic($topic)
                ->withNotification(
                    Notification::create($title, $body)
                )
                ->withAndroidConfig(AndroidConfig::fromArray([
                    'priority' => 'HIGH',
                    'notification' => [
                        'sound' => 'alarm',
                        'channel_id' => 'high_importance_channel',
                    ],
                ]))
                ->withApnsConfig(ApnsConfig::fromArray([
                    'payload' => [
                        'aps' => [
                            'sound' => 'alarm.mp3',
                        ],
                    ],
                ]));

            if (!empty($data)) {
                $message = $message->withData($data);
            }

            $this->client->send($message);

            return true;
        } catch (MessagingException | FirebaseException $e) {
            throw new Exception('Topic bildirim hatası: ' . $e->getMessage());
        }
    }

    /**
     * Subscribe tokens to a topic
     */
    public function subscribeToTopic(array $tokens, string $topic): array
    {
        try {
            $result = $this->client->subscribeToTopic($topic, $tokens);
            return [
                'success' => $result->successes()->count(),
                'failure' => $result->failures()->count(),
            ];
        } catch (MessagingException | FirebaseException $e) {
            throw new Exception('Topic abonelik hatası: ' . $e->getMessage());
        }
    }

    /**
     * Unsubscribe tokens from a topic
     */
    public function unsubscribeFromTopic(array $tokens, string $topic): array
    {
        try {
            $result = $this->client->unsubscribeFromTopic($topic, $tokens);
            return [
                'success' => $result->successes()->count(),
                'failure' => $result->failures()->count(),
            ];
        } catch (MessagingException | FirebaseException $e) {
            throw new Exception('Topic abonelik iptal hatası: ' . $e->getMessage());
        }
    }

    /**
     * Extract tokens from a MulticastSendReport based on success/failure
     *
     * @param  bool  $successful  Whether to get successful or failed tokens
     */
    private function getTokensFromReport(MulticastSendReport $report, bool $successful): array
    {
        $tokens = [];
        $items = $successful ? $report->successes() : $report->failures();

        foreach ($items as $item) {
            $tokens[] = $item->target()->value();
        }

        return $tokens;
    }
}
