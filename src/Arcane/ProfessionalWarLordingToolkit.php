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
    
    /**
     * The preferred Regiment to be resolved per operation.
    */
    protected ?string $defaultRegiment = null;

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

        if ($this->onetimeDriverAlias !== null) {
            $target = is_array($this->onetimeDriverAlias)
                ? $this->onetimeDriverAlias[0]
                : $this->onetimeDriverAlias;
    
            $this->onetimeDriverAlias = null; // یک‌بار مصرف
            return $this->nemesis()->driver($target);
        }

        // Local fluent override (set via setDefaultDriver).
        if ($this->defaultDriverName !== null) {
            return $this->nemesis()->driver($this->defaultDriverName);
        }

        // Contextual default (route/header/payload, per Bot).
        return $this->nemesis()->driver();
    }

    /**
     * ⚡️ THE ULTIMATE POLYVALENT ENFORCER (Hyper-DX Driver Gateway)
     * 
     * - Mode 1 [Getter]: No arguments -> Returns current active ?MultiverseEnforcer.
     * - Mode 2 [Explicit Null Reset]: Explicit null -> Nullifies active driver and returns $this (WarLord) for parent chaining.
     * - Mode 3 [Instance Injector]: MultiverseEnforcer instance -> Binds the instance, nullifies old, and returns $this (WarLord) for parent chaining.
     * - Mode 4 [String Setter]: Driver name/alias string -> Switches driver and returns MultiverseEnforcer for driver chaining.
     * 
     * Try It's jQuery+ API:
     *
     *     $warlord->enforcer();                   // Getter → null|MultiverseEnforcer instance if there is an active one.
     *     $warlord->enforcer('telegram');         // Setter → null|MultiverseEnforcer instance if found
     *     $warlord->enforcer('telegram', 'main'); // Setter → null|MultiverseEnforcer instance if found
     *     $warlord->enforcer($driverInstance);    // Setter → $this ($krubotInstance)
     *     $warlord->enforcer(null);               // Explicit Flush → $this ($krubotInstance)
     *
     * Getter is detected by argument count, so:
     *
     *     >enforcer() !== >enforcer(null)
     *
     * @param string|MultiverseEnforcer|null $name Driver name, alias, instance, or explicit null.
     * @param string|null $regiment Optional regiment scope for multi-bot isolation.
     * @return MultiverseEnforcer|self|null
    */
    public function enforcer(string|MultiverseEnforcer|null $name = null, ?string $regiment = null): MultiverseEnforcer|self|null
    {
        // MODE 1: GETTER (No arguments passed)
        if (func_num_args() === 0) {
            return $this->driver;
        }

        // Clean up previous driver server binding if it exists
        $cleanupPrevious = function () {
            if ($this->driver) {
                $this->driver->serve(null);
            }
        };

        // MODE 2: EXPLICIT NULL RESET (Setter with explicit null)
        if ($name === null) {

            $cleanupPrevious();
            $this->driver = null;

            return $this;
        }

        // MODE 3: INSTANCE INJECTOR (Passed an existing MultiverseEnforcer instance)
        if ($name && $name instanceof MultiverseEnforcer) {

            // prevent redundant assigns
            if($name === $this->driver)
                return $this;

            $cleanupPrevious();
            $this->driver = $name;
            $this->driver->serve($this);
            $this->setCurrentDriver($name);

            // if($regiment)
                // $this->regiment($regiment, true);

            return $this;
        }

        // MODE 2: STRING SETTER (Driver name or alias lookup)
        /** @var \KrubiK\Drivers\Nemesis $nemesis */
        $nemesis = $this->nemesis();
        $regiment ??= $this->defaultRegiment;

        $canonicalName = $nemesis->resolveDriverName($name, $regiment);

        // 4. INSTANTIATION: Fetch/initialize the target driver instance.
        // Attempt to fetch the driver from Nemesis. 
        // If Nemesis throws InvalidArgumentException (e.g. invalid driver name/spawn failure),
        // the catch block intercepts it, keeping the current active driver state pristine and safe.
        $driver = null;
        try {           
            $driver = $nemesis->enforcer($canonicalName, $regiment);
        } catch (InvalidArgumentException $e) {
            // State remains untouched; rethrow or enhance the error context if needed
            // throw $e;
            // throw new InvalidArgumentException(sprintf('KrubiK Agency Error: Target Enforcer [%s] (resolved from driver alias: [%s]) could not be initialized or is invalid.', $canonicalName, $name));
            $driver = null;
        }

        if($driver) { // && $driver instanceof MultiverseEnforcer

            $cleanupPrevious();
            $this->driver = $driver;
            
            $this->driver->serve($this);
            $this->setCurrentDriver($driver);

            // if($regiment)
                // $this->regiment($regiment, true);
        }
        

        return $driver; // ?MultiverseEnforcer
    }

    /**
     * Alias for enforcer() for absolute linguistic flexibility.
     *
     * @param string|MultiverseEnforcer|null $name Driver name, alias, instance, or explicit null.
     * @param string|null $regiment Optional regiment scope for multi-bot isolation.
     * @return MultiverseEnforcer|self|null
    */
    public function driver(string|MultiverseEnforcer|null $name = null, ?string $regiment = null): MultiverseEnforcer|self|null
    {
        return (func_num_args() === 0) ? $this->enforcer() : $this->enforcer($name, $regiment);
    }

    /**
     * 🔎 FIND ENFORCER — Non-Fluent Lookup Gateway
     *
     * Resolves an Enforcer using the exact same Name + Regiment contract,
     * but never mutates the active Enforcer on this object.
     *
     *     $bot->findEnforcer('telegram');
     *     $bot->findEnforcer('telegram', 'main');
     *     $bot->findEnforcer(); // active Enforcer, if already bound
     *
     * Returns null when the requested Enforcer cannot be resolved.
     *
     * @param string $name
     * @param string|null $regiment
     * @return MultiverseEnforcer|null
    */
    public function findEnforcer(string $name = null, ?string $regiment = null): ?MultiverseEnforcer
    {

        // 🧠 Nemesis owns alias resolution, Regiment isolation and caching.
        try {
            return $this->nemesis()->driver($name, $regiment ?? $this->defaultRegiment);
        } catch (InvalidArgumentException $ignoring) {
            // 🛡️ "find" semantics: missing/invalid targets resolve to null.
            return null;
        }
    }

    /**
     * ⚡️ THE ULTIMATE REGIMENT VALVET GATEWAY (jQuery-Style Fluent Getter/Setter & Chainable)
     * 
     * Manages the active regiment/operative scope for multi-bot isolation with maximum performance.
     * 
     * - Mode 1 [Getter]: No arguments -> Returns current active ?string regiment scope.
     * - Mode 2 [Explicit Null Reset]: Explicit null -> Resets active regiment to null and returns $this for chaining.
     * - Mode 3 [String Setter]: Regiment string -> Updates regiment only if it differs from the current one (Zero-redundancy optimization), 
     *   then returns $this for fluent method chaining.
     * 
     * Try It's jQuery+ API:
     *
     *     $warlord->regiment();         // Getter → null|string (current active regiment)
     *     $warlord->regiment('main');   // Setter → $this ($krubotInstance) [Skipped if already 'main']
     *     $warlord->regiment(null);     // Explicit Flush → $this ($krubotInstance)
     *
     * @param string|null $regiment Target regiment scope, explicit null, or none for getter mode.
     * @return string|self|null
    */
    public function regiment(?string $regiment = null, bool $preventCircular = false): string|self|null
    {
        /** @var \KrubiK\Drivers\Nemesis $nemesis */
        $nemesis = $this->nemesis();

        // MODE 1: GETTER (No arguments passed)
        if (func_num_args() === 0) {
            // Return local current regiment if set, otherwise fallback to Nemesis query.
            return $this->defaultRegiment ?? $nemesis->currentRegiment();
        }

        // MODE 2: EXPLICIT NULL RESET (Setter with explicit null parameter)
        if ($regiment === null) {
            if ($this->defaultRegiment !== null) {
                $this->defaultRegiment = null;
            }
            return $this;
        }

        // MODE 3: STRING SETTER (Zero-Redundancy Performance Optimization)

        // Check if the incoming regiment is identical to the current one to prevent redundant state mutations.
        $currentEnforcer = $this->enforcer();
        if($currentEnforcer && ($currentEnforcer->regiment() == $regiment))
            return $this;            

        // if ($this->defaultRegiment !== $regiment)
        $this->defaultRegiment = $regiment;

        // Safe-try to refresh Enforcer
        try {
            if(!$preventCircular)
                $this->enforcer(
                    $nemesis->enforcer(null, $regiment, true) // null → ask nemesis | true → $ignorePrimed
                );
        } catch (InvalidArgumentException $ignoring) {}

        // Return $this for fluent parent chaining
        return $this;
    }

    /**
     * Alias for regiment() for absolute linguistic flexibility.
     * 
     * @param string|null $regiment
     * @return string|self|null
    */
    public function operative(?string $regiment = null, bool $preventCircular = false): string|self|null
    {
        return (func_num_args() === 0) ? $this->regiment() : $this->regiment($regiment);
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

    /*───────────────────────────────────────────────────────────────
    |  ⚡ Fine-DX Aura Listener (Krubot Language)
    |  Mutable Singleton • Zero Ceremony • Maximum Signal Clarity
    ───────────────────────────────────────────────────────────────*/

    /**
     * Internal Aura-listening state.
     *
     * true  = Krubot actively listens to RenderAura changes
     * false = Krubot ignores Aura synchronization signals
     *
     * This is intentionally mutable: Warlord owns the live listening state
     * while RenderAura remains an immutable execution-context snapshot.
     */
    protected bool $listensAura = false;

    /**
     * jQuery-style dual accessor for the Aura-listening state.
     *
     * Getter:
     *   $krubot->listensAura()
     *
     * Setter:
     *   $krubot->listensAura(true)
     *
     * No argument returns the current state.
     * Passing a boolean updates the state and returns $this for fluent chaining.
     *
     * @param  bool  $value
     * @return self|bool
     */
    public function listensAura(bool $value = true): self|bool
    {
        // ⚡ Zero-argument form = pure state read.
        if (func_num_args() === 0) {
            return $this->listensAura;
        }

        // 🚀 One-argument form = mutate listener state and stay fluent.
        $this->listensAura = $value;

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
