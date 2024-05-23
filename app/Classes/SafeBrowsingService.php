<?php

namespace App\Classes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class SafeBrowsingService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://safebrowsing.googleapis.com/',
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function checkUrl($url)
    {
        $apiKey = config('app.google_safe_browsing_api_key');

        // Construct the request payload
        $payload = [
            'client' => [
                'clientId' => 'url-shortener',
                'clientVersion' => '0.1',
            ],
            'threatInfo' => [
                'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING', 'UNWANTED_SOFTWARE', 'POTENTIALLY_HARMFUL_APPLICATION'],
                'platformTypes' => ['ANY_PLATFORM'],
                'threatEntryTypes' => ['URL'],
                'threatEntries' => [
                    ['url' => $url],
                ],
            ],
        ];

        $jsonPayload = json_encode($payload);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://safebrowsing.googleapis.com/v4/threatMatches:find?key=$apiKey");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            throw new \Exception("cURL Error: $err");
        }

        $data = json_decode($response, true);

        // Check if the URL matches any threats
        $matches = isset($data['matches']) ? $data['matches'] : [];

        return $matches;
    }
}
