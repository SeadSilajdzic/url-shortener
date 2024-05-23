<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Http\Requests\UrlRequest;
use Illuminate\Http\JsonResponse;
use App\Classes\SafeBrowsingService;
use Illuminate\Http\RedirectResponse;

class UrlController extends Controller
{
    public function shorten(UrlRequest $request, $subdir = null): JsonResponse
    {
        $data = $request->validated();

        $safeBrowsingService = new SafeBrowsingService();
        $matches = $safeBrowsingService->checkUrl($data['url']);

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
            ['original_url' => $data['url']],
            ['url_hash' =>  $url_hash]
        );

        return response()->json([
            'shortenURL' => route('redirect', $subdir . $shorten->url_hash)
        ]);
    }

    public function redirect($hash): RedirectResponse
    {
        $hash = explode('/', $hash);
        $originalUrl = Url::where('url_hash', last($hash))->firstOrFail()->original_url;
        return redirect()->away($originalUrl);
    }
}
