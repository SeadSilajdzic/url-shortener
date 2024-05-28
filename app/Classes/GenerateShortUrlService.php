<?php

namespace App\Classes;

use App\Models\Url;
use Illuminate\Http\JsonResponse;

class GenerateShortUrlService
{
    public function __construct(
        private readonly SafeBrowsingService $safeBrowsingService
    ) {}
    public function shorten(string $url): array|JsonResponse
    {
        $matches = $this->safeBrowsingService->checkUrl($url);

        if (!empty($matches)) {
            // Handle unsafe URL
            return response()->json([
                'error' => [
                    'url' => ['The URL is unsafe.']
                ], 'matches' => $matches], 400);
        }

        $subdir = !empty($subdir) ? $subdir . '/' : '';

        do { // Make sure hash is unique
            $url_hash = substr(sha1(mt_rand()),17,6);
        } while (Url::where('url_hash', $url_hash)->exists());

        $shorten = Url::firstOrCreate(
            ['original_url' => $url],
            ['url_hash' =>  $url_hash]
        );

        return [$subdir, $shorten->url_hash];
    }
}
