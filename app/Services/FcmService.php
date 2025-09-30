<?php

namespace App\Services;

use Google_Client;
use GuzzleHttp\Client;

class FcmService
{
    protected $projectId;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID');
    }

    protected function getAccessToken()
    {
        $client = new Google_Client();
        $client->setAuthConfig(env('GOOGLE_APPLICATION_CREDENTIALS'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $tokenArray = $client->fetchAccessTokenWithAssertion();
        return $tokenArray['access_token'] ?? null;
    }

    public function sendNotification($fcmToken, $title, $body)
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            throw new \Exception('Gagal mendapatkan access token dari Firebase');
        }

        $client = new Client();
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $response = $client->post($url, [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ],
            ]),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
