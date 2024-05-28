<?php

namespace App\Http\Controllers;

use App\Classes\GenerateShortUrlService;
use App\Models\Url;
use App\Http\Requests\UrlRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class UrlController extends Controller
{
    public function shorten(UrlRequest $request, GenerateShortUrlService $shortUrlService): JsonResponse
    {
        $data = $request->validated();

        $response = $shortUrlService->shorten($data['url']);
        if (gettype($response) == 'object') {
            return response()->json($response->original, 400);
        }

        list($subdir, $shorten) = $response;

        return response()->json([
            'shortenURL' => route('redirect', $subdir . $shorten)
        ]);
    }

    public function redirect($hash): RedirectResponse
    {
        $hash = explode('/', $hash);
        $originalUrl = Url::where('url_hash', last($hash))->firstOrFail()->original_url;
        return redirect()->away($originalUrl);
    }
}
