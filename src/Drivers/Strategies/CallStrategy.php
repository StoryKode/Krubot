<?php

namespace KrubiK\Drivers\Strategies;

/**
 * Interface CallStrategy
 * Defines the contract for how a Telegram API call should be handled.
 * This allows swapping the behavior of the driver (e.g., direct API call vs. deferred webhook response)
 * without changing the driver's own code.
 */
interface CallStrategy
{
    /**
     * Handle the API method call.
     *
     * @param string $method The name of the Telegram method (e.g., 'sendMessage').
     * @param array<string, mixed> $parameters The parameters for the method.
     * @return mixed The result of the call. This could be a Guzzle response, an array,
     *               or a Responsable object depending on the strategy.
     */
    public function handle(string $method, array $parameters): mixed;
}
