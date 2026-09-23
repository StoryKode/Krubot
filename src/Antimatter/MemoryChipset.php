<?php

namespace KrubiK\Antimatter;

/**
 *          MemoryChipset 💾 :: The Rogue Ghost Trait
 *
 * Provides a dynamic storage layer for immutable or read-only host objects.
 * The trait delegates all virtual property access to the external
 * ChronoStasis, allowing runtime state to be associated with an object
 * without modifying its native property layout.
 * 
 * It tricks the locked host into believing it has dynamic, high-speed Memory (RAM). 
 * But These phantom sectors Don't really exist on the host's native motherboard—they are 
 * projected entirely from the external ChronoStasisMatrix. You are effectively 
 * jacking a USB shadow-drive into a frozen construct, granting it unlimited expansion.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.9×
 * @license MIT
*/
trait MemoryChipset
{
    /**
     * [ HOT-SWAP INJECTION PROTOCOL ]
     * Intercepts writes to dynamic properties that are not handled by the host.
     * Instead of mutating the host object directly, the payload is persisted in
     * the external Matrix and associated with the specified virtual sector.
     *
     * Example: $cyborg->neural_implant = 'combat-mode-engaged';
    */
    public function __set(string $sector, mixed $payload): void
    {
        Noxia::engrave($this, $sector, $payload);
    }

    /**
     * [ GHOST DATA EXTRACTION ]
     * Resolves reads for virtual properties that are not physically defined on
     * the host object. The value is retrieved from the external Matrix and
     * returned transparently to the caller.
     *
     * Example: echo $cyborg->neural_implant;
    */
    public function __get(string $sector): mixed
    {
        return Noxia::excavate($this, $sector);
    }

    /**
     * [ STEALTH PING / SECTOR SCAN ]
     * Checks whether the requested virtual sector currently contains a value
     * in the external Matrix. This allows callers to use standard property
     * existence checks without accessing the host's native state.
     *
     * Example: if (isset($cyborg->neural_implant)) { ... }
    */
    public function __isset(string $sector): bool
    {
        return Noxia::probe($this, $sector);
    }

    /**
     * [ FLATLINE / SECTOR WIPE ]
     * Intercepts unsets for virtual/dynamic properties and forwards the request
     * to the external Matrix. The targeted sector is purged so subsequent reads
     * behave as if the value never existed.
     *
     * Example: unset($cyborg->neural_implant);
    */
    public function __unset(string $sector): void
    {
        Noxia::evaporate($this, $sector);
    }
}
