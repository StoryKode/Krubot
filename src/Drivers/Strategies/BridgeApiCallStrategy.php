<?php

namespace KrubiK\Drivers\Strategies;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Implements the CallStrategy for making direct, real-time HTTP requests to the Telegram Bot API.
 * This is the standard strategy for environments where outbound requests are allowed.
 */
class BridgeApiCallStrategy implements CallStrategy
{
    public function __construct(
        private readonly string $bridgeBaseUri,
        private readonly string $bridgeSecret,
        private readonly string $botToken,
    ) {}

    public function handle(string $method, array $parameters): Response
    {
        $url = rtrim($this->bridgeBaseUri, '/') . "/bot{$this->botToken}/{$method}";

        $url = "{$this->apiBaseUri}/bot{$this->botToken}/{$method}";

        // For performance, we use Laravel's excellent Http client.
        // It's faster and more memory-efficient than Guzzle directly for simple calls.

        return
            Http::
            withHeaders(['X-Bridge-Auth' =>$this->bridgeSecret])
            ->post($url, $parameters);
    }
}
