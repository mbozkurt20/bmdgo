<?php

namespace App\Services;

use Exception;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging;
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
     * @param  string  $body  Notification body
     * @param  array  $data  Additional data to send
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
                ->withData(array_merge([
                    'title' => $title,
                    'message' => $body,
                    'sound' => url('/voices/tehlike.mp3'), // özel ses
                ], $data));

            $this->client->send($message);

            return true;
        } catch (MessagingException|FirebaseException $e) {
            throw new \Exception('Failed to send push notification: '.$e->getMessage());
        }
    }

    /**
     * Send push notifications to multiple devices
     *
     * @param  array  $tokens  Array of FCM device tokens
     * @param  string  $title  Notification title
     * @param  string  $body  Notification body
     * @param  array  $data  Additional data to send
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
                ->withData(array_merge([
                    'title' => $title,
                    'body' => $body,
                ], $data))
                ->withDefaultSounds();

            $report = $this->client->sendMulticast($message, $tokens);

            return [
                'success' => $report->successes()->count(),
                'failure' => $report->failures()->count(),
                'tokens' => [
                    'successful' => $this->getTokensFromReport($report, true),
                    'failed' => $this->getTokensFromReport($report, false),
                ],
            ];
        } catch (MessagingException|FirebaseException $e) {
            throw new Exception('Failed to send bulk notifications: '.$e->getMessage());
        }
    }

    /**
     * Send a notification to a topic
     *
     * @param  string  $topic  Topic name
     * @param  string  $title  Notification title
     * @param  string  $body  Notification body
     * @param  array  $data  Additional data to send
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
                ->withData(array_merge([
                    'title' => $title,
                    'body' => $body,
                ], $data))
                ->withDefaultSounds();

            $this->client->send($message);

            return true;
        } catch (MessagingException|FirebaseException $e) {
            throw new Exception('Failed to send topic notification: '.$e->getMessage());
        }
    }

    /**
     * Subscribe tokens to a topic
     *
     * @param  array  $tokens  Array of FCM device tokens
     * @param  string  $topic  Topic name
     * @return array Array of successful and failed tokens
     *
     * @throws Exception
     */
    public function subscribeToTopic(array $tokens, string $topic): array
    {
        try {
            $result = $this->client->subscribeToTopic($topic, $tokens);

            return [
                'success' => count($result['successCount']),
                'failure' => count($result['failureCount']),
                'tokens' => [
                    'successful' => $result['successCount'],
                    'failed' => $result['failureCount'],
                ],
            ];
        } catch (MessagingException|FirebaseException $e) {
            throw new Exception('Failed to subscribe to topic: '.$e->getMessage());
        }
    }

    /**
     * Unsubscribe tokens from a topic
     *
     * @param  array  $tokens  Array of FCM device tokens
     * @param  string  $topic  Topic name
     * @return array Array of successful and failed tokens
     *
     * @throws Exception
     */
    public function unsubscribeFromTopic(array $tokens, string $topic): array
    {
        try {
            $result = $this->client->unsubscribeFromTopic($topic, $tokens);

            return [
                'success' => count($result['successCount']),
                'failure' => count($result['failureCount']),
                'tokens' => [
                    'successful' => $result['successCount'],
                    'failed' => $result['failureCount'],
                ],
            ];
        } catch (MessagingException|FirebaseException $e) {
            throw new Exception('Failed to unsubscribe from topic: '.$e->getMessage());
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
