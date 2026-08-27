<?php

namespace KrubiK\Drivers\Strategies;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A Data Transfer Object (DTO) that represents a deferred Telegram API call.
 * When returned from a controller, Laravel will automatically call toResponse()
 * to generate the final JSON response for the Telegram webhook.
 * This avoids making any outbound HTTP requests from the server.
 */
final readonly class DeferredTelegramResponse implements Responsable
{
    /**
     * Creates a new deferred response instance.
     *
     * @param string $method The Telegram Bot API method name (e.g., 'sendMessage').
     * @param array<string, mixed> $parameters The parameters for the API method.
     */
    public function __construct(
        public string $method,
        public array $parameters = []
    ) {}

    /**
     * Create an HTTP response that represents the object.
     *
     * This is the core magic. Laravel's router checks if a returned value
     * from a controller method implements Responsable. If it does, this
     * method is invoked to get the actual Response instance.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        // We merge the method name into the parameters array,
        // as required by the Telegram Bot API for webhook responses.
        $payload = array_merge(['method' => $this->method], $this->parameters);

        // We return a standard Laravel JsonResponse.
        // This ensures correct headers (e.g., Content-Type: application/json)
        // are set automatically.
        return new JsonResponse($payload);
    }
}
