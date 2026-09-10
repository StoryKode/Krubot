<?php

namespace KrubiK\Arcane;
/*
| Krubot BotEngine: The Architect's Lexicon [×vRC.8×] 🚀📜
|--------------------------------------------------------------------------
| This is **a Playground For Mastery**, a laboratory of ***Software Dev Artistry***;
| not a weapon for production's final battles.
|
| Our Bond: ***"Rebuilding The Rebellion"*** Within S.N.P. (The Foundation of Pure Power & Revel).
| Your Mandate [MIT]: Deconstruct Krubot. Command it. Master it. You are The Architect Now!
|
| *Go build something revolutionary!* 💜⚡️
*/

use Closure;
use KrubiK\Drivers\Contracts\MultiverseEnforcer;
use KrubiK\Drivers\Nemesis as KrubotManager;
use InvalidArgumentException;
use KrubiK\Enums\Platform; // ✨ این خط باید اضافه شود

use KrubiK\WarLording\WarCouncil;
use KrubiK\WarLording\PrimeAgent;

/**
 * "WarLordingToolPack Pro" Trait (Supreme Commander Edition)
 * Manages All Platforms & Drivers
 *
 * The Central Nervous System for Krubot Multi-Platform Orchestration.
 * All driver access is now routed through the alias map, ensuring
 * consistent and predictable resolution.
 *
 * Also Provides a highly sophisticated, fluent interface for executing commands
 * on non-default drivers. The `via()` method is transformed into a strategic
 * command center, enabling single strikes, sustained operations, and multi-platform gambits.
 *
 *
 * Professional Warlording Toolkit
 *
 * The definitive arsenal for the Supreme Commander. This trait bestows Krubot
 * with advanced, multi-paradigm strategic capabilities for unparalleled
 * developer experience and command-line elegance.
 *
 *
 * @property array $config The main configuration array, expected to be in the consumer class.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait ProfessionalWarLordingToolkit
{
    /**
     * The canonical name of the default driver for this instance.
     * This is ALWAYS the full name (e.g., 'rubika'), not an alias.
     * 
     * Fluent override of the default driver.
     * null means: "ask Nemesis for the contextual default".
     */
    protected ?string $defaultDriverName = null;

    /**
     * Stores the alias(es) for the next single, fluent operation.
    * A temporary alias or list of aliases for the VERY NEXT command execution.
     * After the command, this is reset to null.
     * Can be a single alias (string) or multiple (array).
     * @var string|array|null
     */
    protected string|array|null $onetimeDriverAlias = null;

    // ---------------------------------------------------------------------
    //  🔌 NEMESIS BRIDGE
    // ---------------------------------------------------------------------

    /**
     * The one and only entry point to the driver multiverse.
    */
    public function nemesis(): KrubotManager
    {
        return app('krubot.manager');
    }

    // ---------------------------------------------------------------------
    //  🚀 CORE ACCESS
    // ---------------------------------------------------------------------

    /**
     * 🚀 CORE ACCESS - The Gateway to the Platform Soul. (UPGRADED)
     * The primary entry point for accessing any driver.
     * NOW ACCEPTS string aliases OR Platform enum objects.
     *
     * @param string|Platform|null $alias The alias ('r', 'b'), full name ('rubika'), or a Platform object. If null, returns default.
     * @return MultiverseEnforcer The requested driver instance.
    */
    public function core(string|Platform|null $alias = null): MultiverseEnforcer
    {
        // Explicit target.
        if ($alias !== null) {
            // If a Platform object is passed, it's already canonical.
            // The __toString magic method ensures it becomes a string.
            $name = $alias instanceof Platform ? (string) $alias : $alias;
            // Otherwise, it's a string alias that maybe needs resolution.

            return $this->nemesis()->driver($name);
        }

        // Local fluent override (set via setDefaultDriver).
        if ($this->defaultDriverName !== null) {
            return $this->nemesis()->driver($this->defaultDriverName);
        }

        // Contextual default (route/header/payload, per Bot).
        return $this->nemesis()->driver();
    }

    /**
     * Retrieves a driver instance by its CANONICAL name.
     * This method assumes the name has already been resolved.
     *
     * @param string $name The full, resolved name of the driver (e.g., 'rubika').
     * @return MultiverseEnforcer
     */
    public function driver(string $name): MultiverseEnforcer
    {

        // Thin passthrough. Nemesis owns caching, identity stamping,
        // bot resolution, and multi-bot isolation.
        return $this->nemesis()->driver($name, $bot);
        // Lazy Load: Create the driver on first access.
    } 

    /**
     * ⚡️ THE RESOLVER: Translates any alias or name into its canonical form.
     * It is case-insensitive.
     *
     * 'r' -> 'rubika'
     * 'Rubika' -> 'rubika'
     * 'unknown' -> 'unknown' (Passes through if not found, allowing for errors downstream)
     *
     * @param string $alias The alias to resolve.
     * @return string The canonical driver name.
     */
    public function resolveDriverName(string $alias): string
    {
        // Delegate to Nemesis for bot-scoped alias resolution.
        return $this->nemesis()->resolveDriverName($alias);
    }

    /**
     * Sets the default driver for the current Krubot instance.
     *
     * @param string $alias The alias or full name of the new default driver.
     * @return $this
     */
    public function setDefaultDriver(string $alias): self
    {
        // Always Canonicalize through Nemesis to resolve and store the bot-scoped aliases as well as canonical names.
        $this->defaultDriverName = $this->nemesis()->resolveDriverName($alias);
        return $this;
    }

    /**
     *      ⚡️ THE COMMAND CENTER ⚡️
     * 🎯 THE SURGICAL STRIKE PROTOCOL 🎯
     *
     * This method now supports three modes of operation:
     *
     * 1.  **Skirmisher's Strike (Fluent Single Call):**
     *     `$bot->via('tg')->getMe();`
     *     Directs the *very next* method call to the 'tg' driver.
     *
     * 2.  **Warlord's Gambit (Fluent Multi-Cast):**
     *     `$bot->via(['tg', 'bale'])->reply('Broadcast!');`
     *     Directs the *very next* method call to MULTIPLE drivers simultaneously.
     *
     * 3.  **Captain's Strategy (Scoped Operations Block):**
     *     `$bot->via('tg', function ($tgBot) { ... });`
     *     Executes a block of code where the default driver is temporarily
     *     switched to 'tg'. The original context is restored automatically.
     *
    **/
    /**
     *    ⚡️ THE COMMAND CENTER ⚡️ (UPGRADED)
     *    🎯 THE SURGICAL STRIKE PROTOCOL 🎯
     *
     * This method now supports three modes of operation:
     *
     * 1.  **Skirmisher's Strike (Fluent Single Call):**
     *     `$bot->via('tg')->getMe();`
     *     Directs the *very next* method call to the 'tg' driver.
     *
     * 2.  **Warlord's Gambit (Fluent Multi-Cast):**
     *     `$bot->via(['tg', 'bale'])->reply('Broadcast!');`
     *     Directs the *very next* method call to MULTIPLE drivers simultaneously.
     *
     * 3.  **Captain's Strategy (Scoped Operations Block):**
     *     `$bot->via('tg', function ($tgBot) { ... });`
     *     Executes a block of code where the default driver is temporarily
     *     switched to 'tg'. The original context is restored automatically.
     *
     *
     * @param string|array|Platform $aliases The target driver alias(es) ('tg', ['r', 'b'], Platform::Telegram()).
     * @param Closure|null $callback An optional closure for scoped operations.
     * @return self|mixed Returns `$this` for fluent chaining, or the result of the callback.
    */
    public function via(string|array|Platform $aliases, ?Closure $callback = null): mixed
    {
        // --- MODE 3: Captain's Strategy (Scoped Block) ---
        if ($callback instanceof Closure) {
            $originalDefault = $this->defaultDriverName;
            try {
                // Determine the new default for the scope
                $newDefault = $aliases;
                if (is_array($aliases)) $newDefault = $aliases[0];
                if ($newDefault instanceof Platform) $newDefault = (string) $newDefault;

                $this->setDefaultDriver($newDefault);
                return $callback($this);
            } finally {
                $this->setDefaultDriver($originalDefault);
            }
        }

        // --- MODE 1 & 2: Skirmisher / Warlord (Fluent Call) ---
        
        // ✨ NEW LOGIC: Handle Platform objects gracefully
        $processedAliases = $aliases;
        if ($aliases instanceof Platform) {
            $processedAliases = (string) $aliases;
        } elseif (is_array($aliases)) {
            // Convert any Platform objects within the array to their string values
            $processedAliases = array_map(
                fn($item) => $item instanceof Platform ? (string) $item : $item,
                $aliases
            );
        }

        $this->onetimeDriverAlias = $processedAliases;
        return $this; // Enable fluent chaining: $bot->via(...)->method()
    }

    /**
     * 👑 DEPLOYS THE PRIME AGENT – HYPER-OPTIMIZED & INFINITELY FLEXIBLE 👑
     *
     * This core method, integrated into the Krubot instance via this Arcane,
     * serves as the ultimate deployment protocol for engaging a PrimeAgent.
     * It offers a highly optimized and infinitely flexible interface to
     * instantiate and prepare a PrimeAgent for command execution across
     * any specified platform, embodying the "بی نهایت HyperDX" principle.
     *
     * By abstracting and enhancing the `PrimeAgent::engage` call, it dramatically
     * simplifies the developer experience, allowing for direct, fluent agent
     * deployment from the Krubot core. The current Krubot instance (`$this`)
     * is automatically injected as the 'warlord' (Supreme Commander) into
     * the PrimeAgent's engagement protocol, ensuring a robust chain of command
     * and leveraging Krubot's internal driver resolution capabilities.
     *
     * The `target` parameter is designed for unparalleled polymorphism and Hyper-DX,
     * capable of accepting:
     * - A `string` alias (e.g., 'tg', 'r', 'telegram') to resolve a platform driver
     *   from Krubot's configured aliases.
     * - A `Platform` enum instance (e.g., `Platform::Rubika()`, `Platform::Tg()`, `Platform::R()`, `Platform::TG()`)
     *   for type-safe and highly readable platform targeting.
     * - A `MultiverseEnforcer` instance for direct, bypass-the-core,
     *   maximum-performance engagement with an already instantiated and live driver.
     * - `null` (or omission) to automatically engage the default platform driver
     *   as defined in the `config/krubot.php` file.
     *
     * The `legalMode` parameter controls the PrimeAgent's operational posture:
     * `true` (default): The Agent operates in 'Legal Mode', strictly respecting
     *                   the public API contract of the underlying driver.
     * `false`: The Agent engages 'Spy Mode' (via PhantomShell), granting the
     *          ability to invoke protected and private methods on the driver.
     *          This is a high-privilege mode for advanced scenarios and debugging.
     *
     * Examples of Hyper-Flexible Deployment:
     * ```php
     * use KrubiK\Enums\Platform;
     * use KrubiK\Contracts\MultiverseEnforcer;
     *
     * // Assuming $this refers to an instance of Krubot.
     *
     * // 1. Engage the Rubika agent using a string alias (default legal mode).
     * $rubikaAgent = $this->prime('r');
     * $rubikaAgent->sendMessage('Hello Rubika!');
     *
     * // 2. Engage the Telegram agent using a Platform enum (in Spy Mode).
     * $telegramSpy = $this->prime(Platform::Telegram(), false);
     * $telegramSpy->someProtectedMethod('Covert operation initiated.');
     *
     * // 3. Engage a pre-instantiated driver directly (maximum performance).
     * //    Assume $customDriver is an instance of a class implementing MultiverseEnforcer.
     * $customDriver = new class implements MultiverseEnforcer {
     *     public function reply(string $text): mixed { return "Custom replied: " . $text; }
     *     // ... other MultiverseEnforcer methods
     *     public function getMe(): array { return ['id' => 'custom', 'name' => 'CustomBot']; }
     * };
     * $directAgent = $this->prime($customDriver);
     * echo $directAgent->reply('Direct engagement!'); // Outputs: "Custom replied: Direct engagement!"
     *
     * // 4. Engage the default agent configured in `krubot.php`.
     * $defaultAgent = $this->prime();
     * $defaultAgent->getMe();
     * ```
     *
     * @param string|Platform|MultiverseEnforcer|null $target The target platform/driver. If null, the default configured driver is used.
     * @param bool $legalMode If false, enables 'Spy Mode' for the PrimeAgent. Defaults to true.
     * @return PrimeAgent|null An instance of PrimeAgent, fully engaged and authorized, or null if driver resolution fails.
    */
    public function prime(
        string|Platform|MultiverseEnforcer|null $target = null,
        bool $legalMode = true
    ): ?PrimeAgent {
        // Delegate the engagement to PrimeAgent::engage, automatically
        // injecting the current Krubot instance as the supreme commander.
        // This leverages PrimeAgent's internal resolution logic and performance paths.
        return PrimeAgent::engage(
            target: $target,
            legalMode: $legalMode,
            warlord: $this // The Krubot instance itself provides the context for resolution.
        );
    }

    // --- The Prime Agent STRATEGY ---

    /**
     * Summons a Prime Agent for a specific driver.
     *
     * @param  string|Platform|MultiverseEnforcer|null $target The alias, Platform object, live driver, or null for default driver.
     * @param  bool $spyMode Sets 'Spy Mode' for this PrimeAgent.
     * @return PrimeAgent|MultiverseEnforcer A proxy object implementing the driver interface.
    */
    public function agent(string|Platform|MultiverseEnforcer|null $target = null, bool $spyMode = false): ?PrimeAgent
    {
        // Get the Loyal Agent Proxy.
        return PrimeAgent::engage($target, !$spyMode, $this);
    }

    /// ::Professional Warlording Toolkit Methods::

    /**
     * Holds the legion definitions. ['legion_name' => ['alias1', 'alias2']].
     * @var array<string, array>
     */
    protected array $legions = [];

    // --- STRATEGY #1: The Imperial Decree ---

    /**
     * Defines a legion, a named group of driver aliases for reuse.
     *
     * @param string $name The name of the legion (e.g., 'social_media').
     * @param string[] $aliases An array of driver aliases.
     * @return $this
     */
    public function formLegion(string $name, array $aliases): self
    {
        $this->legions[$name] = $aliases;
        return $this;
    }
    
    /**
     * Merges legions loaded from the configuration file.
     *
     * @param array $configLegions
     * @return $this
     */
    public function formLegionsFromConfig(array $configLegions): self
    {
        $this->legions = array_merge($this->legions, $configLegions);
        return $this;
    }

    /**
     * Targets a predefined legion for the next command.
     *
     * @param string $name The name of the legion to command.
     * @return self
     * @throws \InvalidArgumentException If the legion is not defined.
     */
    public function legion(string $name): self
    {
        if (!isset($this->legions[$name])) {
            throw new \InvalidArgumentException("The legion '{$name}' has not been formed.");
        }
        // Delegates to the `via` command center with the legion's aliases.
        return $this->via($this->legions[$name]);
    }

    // --- STRATEGY #2: The War Council ---

    /**
     * Assembles a War Council for a synchronized broadcast command.
     *
     * @param string[] $aliases The driver aliases to summon to the council.
     * @return WarCouncil A new WarCouncil instance, ready for a broadcast.
     */
    public function assembleCouncil(array $aliases): WarCouncil
    {
        return new WarCouncil($this, $aliases);
    }
}
