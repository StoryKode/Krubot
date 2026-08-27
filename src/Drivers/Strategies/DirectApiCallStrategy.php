<?php

namespace App\Services\Bot\Telegram\Strategies;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Implements the CallStrategy for making direct, real-time HTTP requests to the Telegram Bot API.
 * This is the standard strategy for environments where outbound requests are allowed.
 */
class DirectApiCallStrategy implements CallStrategy
{
    public function __construct(
        private readonly string $apiBaseUri,
        private readonly string $botToken
    ) {}

    public function handle(string $method, array $parameters): Response
    {
        $url = "{$this->apiBaseUri}/bot{$this->botToken}/{$method}";

        // For performance, we use Laravel's excellent Http client.
        // It's faster and more memory-efficient than Guzzle directly for simple calls.
        return Http::post($url, $parameters);
    }
}
