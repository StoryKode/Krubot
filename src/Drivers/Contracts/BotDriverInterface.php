<?php

namespace KrubiK\Drivers\Contracts;

/**
 * The foundational contract for all bot drivers.
 *
 * This interface defines the essential methods a driver must implement to interact
 * with its specific messaging platform's API. It ensures that Krubot can
 * communicate with any driver in a standardized way.
 */
interface BotDriverInterface extends FluentDriverInterface, MultiverseEnforcer
{
}
