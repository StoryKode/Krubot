<?php

namespace KrubiK\Drivers\Strategies;

/**
 * Implements the CallStrategy for environments where we must respond directly
 * within the webhook request itself. It does not make any outbound HTTP calls.
 * Instead, it prepares a Responsable DTO that Laravel will automatically
 * convert to the appropriate JSON response for Telegram.
 */
class DeferredWebhookResponseStrategy implements CallStrategy
{
    public function handle(string $method, array $parameters): DeferredTelegramResponse
    {
        // This strategy's job is simple: wrap the call details into our DTO.
        // The DTO itself handles the logic of becoming a JSON response.
        // This keeps the strategy clean and focused on its single responsibility.
        return new DeferredTelegramResponse($method, $parameters);
    }
}
