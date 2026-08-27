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
use InvalidArgumentException;
use KrubiK\Enums\Platform; // ✨ این خط باید اضافه شود
use KrubiK\Drivers\RubikaDriver;
use KrubiK\Drivers\BaleDriver;
use KrubiK\Drivers\TelegramDriver;

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
     * The armory of active, instantiated driver instances.
     * @var array<string, MultiverseEnforcer>
     */
    protected array $drivers = [];

    /**
     * ⚡️ THE SINGLE SOURCE OF TRUTH for driver identification (Dynamic).
     * Now hydrated from config('krubot.drivers.aliases').
     * 
     * Maps user-friendly aliases to canonical driver names.
     * The map is "self-aware": 'rubika' also points to 'rubika'.
     *
     * @var array<string, string>
     *
     */
    protected array $driverAliases = []; // 🧹 Clean Slate: No hardcoded values.

    /**
     * The canonical name of the default driver for this instance.
     * This is ALWAYS the full name (e.g., 'rubika'), not an alias.
     */
    protected string $defaultDriverName = 'rubika';

    /**
     * Stores the alias(es) for the next single, fluent operation.
    * A temporary alias or list of aliases for the VERY NEXT command execution.
     * After the command, this is reset to null.
     * Can be a single alias (string) or multiple (array).
     * @var string|array|null
     */
    protected string|array|null $onetimeDriverAlias = null;

    /**
     * 🚀 CORE ACCESS: The Gateway to the Platform Soul.
     * The primary entry point for accessing any driver.
     *
     * @param string|null $alias The alias ('r', 'b') or full name ('rubika'). If null, returns the default driver.
     * @return MultiverseEnforcer The requested driver instance.
     * /
    public function coreOld(?string $alias = null): MultiverseEnforcer
    {
        // Removed for LLM DeAmbiguousiaty...
    }
    **/

    /**
     * 🚀 CORE ACCESS: The Gateway to the Platform Soul. (UPGRADED)
     * The primary entry point for accessing any driver.
     * NOW ACCEPTS string aliases OR Platform enum objects.
     *
     * @param string|Platform|null $alias The alias ('r', 'b'), full name ('rubika'), or a Platform object. If null, returns default.
     * @return MultiverseEnforcer The requested driver instance.
    */
    public function core(string|Platform|null $alias = null): MultiverseEnforcer
    {
        $driverName = $this->defaultDriverName; // Start with default

        if ($alias !== null) {
            // If a Platform object is passed, it's already canonical.
            // The __toString magic method ensures it becomes a string.
            if ($alias instanceof Platform) {
                $driverName = (string) $alias;
            } 
            // Otherwise, it's a string alias that needs resolution.
            else {
                $driverName = $this->resolveDriverName($alias);
            }
        }

        return $this->driver($driverName);
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
        // 1. Check instance cache first.
        if (isset($this->drivers[$name])) {
            return $this->drivers[$name];
        }

        // 2. Lazy Load: Create the driver on first access.
        $driverInstance = $this->createDriver($name);
        $this->drivers[$name] = $driverInstance;

        return $driverInstance;
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
        return $this->driverAliases[strtolower($alias)] ?? $alias;
    }

    /**
     * Sets the default driver for the current Krubot instance.
     *
     * @param string $alias The alias or full name of the new default driver.
     * @return $this
     */
    public function setDefaultDriver(string $alias): self
    {
        // Always resolve and store the canonical name.
        $this->defaultDriverName = $this->resolveDriverName($alias);
        return $this;
    }

    /**
     * Dynamically registers a new alias for a driver.
     *
     * @param string $alias The short alias (e.g., 's').
     * @param string $canonicalName The full driver name (e.g., 'soroush').
     * @return $this
     */
    public function addCoreAlias(string $alias, string $canonicalName): self
    {
        $this->driverAliases[strtolower($alias)] = $canonicalName;
        // Also add self-awareness for the new canonical name
        $this->driverAliases[strtolower($canonicalName)] = $canonicalName;
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

    /**
     * =========================================================================
     *  ⚡️ THE TRIUMVIRATE: Oracle, Factory, Gatekeeper ⚡️
     * =========================================================================
     */

    /**
     * ⚙️ THE MISSING LINK: Configuration Repository
     * This property holds the entire configuration array (e.g., contents of krubot.php).
     * It is defined here to ensure the Trait is self-contained.
     *
     * @var array
     */
    protected array $pwl_config = [];

    /**
     * 🔧 CONFIG INJECTOR
     * Since Traits cannot have constructors in the traditional sense without conflict,
     * call this method from your Host Class constructor.
     *
     * @param array $config The full configuration array.
     * @return $this
     */
    public function setConfig(array $config): self
    {
        $this->pwl_config = $config;

        // 💧 HYDRATION PROTOCOL: Load Aliases from Config
        // We look for 'drivers' -> 'aliases'
        if (isset($config['drivers']['aliases']) && is_array($config['drivers']['aliases'])) {
            $this->driverAliases = $config['drivers']['aliases'];
        } else {
            // Fallback / Warning protocol could go here if needed.
            // For now, we trust the Supreme Commander's config file.
        }

        return $this;
    }   

    /**
     * 🔮 THE ORACLE (ACCESS GATE)
     * Retrieves the specific configuration array for a single driver.
     * This method acts as the "Access Gate", confirming that a valid config
     * exists for the requested driver name.
     *
     * @param string|null $name The canonical name of the driver (e.g., 'rubika').
     * @return array The configuration array for the requested driver.
     * @throws InvalidArgumentException If the configuration is missing.
     */
    protected function getDriverConfig(?string $name = null): array
    {
        // If no name is provided, use the instance's default driver name.
        $driverName = $name ?? $this->defaultDriverName;

        // Fetch config: $this->pwl_config['drivers']['rubika']
        // This is the direct mapping: Key 'rubika' => Driver Config.
        $driverConfig = $this->pwl_config['drivers'][$driverName] ?? null;
        // 🔥 NOW SAFE: $this->pwl_config is defined in this Trait.

        if ($driverConfig === null) {
            throw new InvalidArgumentException("Oracle Misread: Configuration for driver '{$driverName}' not found under the 'drivers' key in your config file.");
        }

        return $driverConfig;
    }

    /**
     * 🛡️ THE GATEKEEPER (نگهبان دروازه) (Updated for v3.0 Architecture / Logic Transplanted from KrubotServiceProvider Code)
     * Handles the instantiation of the primary Krubot (Rubika) instance
     * with strict token validation and full config injection for advanced features.
     * 
     * Handles the creation of the primary Krubot (Rubika) instance with
     * strict token validation and full config injection.
     *
     * @param array $driverConfig The specific configuration array for the Rubika driver.
     * @return \KrubiK\Krubot The fully instantiated and validated core bot.
     * @throws InvalidArgumentException If the token is invalid.
     * 
     * وظیفه این متد دیگر ساختن کل Krubot نیست.
     * وظیفه آن ساختن و اعتبارسنجی امنیتی "درایور روبیکا" است.
     *
     * @param array $driverConfig تنظیمات خاص درایور روبیکا
     * @return RubikaDriver خروجی دیگر Krubot نیست، بلکه درایور است!
     * @throws InvalidArgumentException If the token is invalid.
    */
    protected function instantiateRubikaDriver(array $driverConfig): RubikaDriver
    {
        // 1. Extract the token from the specific driver config.
        $token = $driverConfig['token'] ?? null;

        // 2. Perform the critical security check.
        // Critical token validation (Fail Fast)
        if (empty($token) || $token === '_') {
            throw new InvalidArgumentException('Gatekeeper Blocked Access: Rubika Bot Token (authtoken) is missing or invalid for the default driver in config/krubot.php.'); // 'KrubiK Bot Token is not configured in .env or config/krubot.php.' // '⛔ KrubiK Critical Error: Bot Token is missing in config/krubot.php or .env'
        }

        // 3. Instantiate Krubot.
        //    - Pass the validated token.
        //    - Pass the ENTIRE config object ($this->pwl_config) to ensure features
        //      like Legion Loading and Nexus Discovery have access to all settings.

        // ✅✅✅ THIS IS THE CRITICAL LINE THAT ENABLES LEGION LOADING ✅✅✅
        //-// return new \KrubiK\Krubot($token, $this->pwl_config);

        // این کلاس RubikaDriver است که از VanguardCore ارث‌بری کرده و توکن را در سازنده خود مدیریت می‌کند.
        return new RubikaDriver($driverConfig);

        // 3. Instantiate Krubot.
        // We pass the ENTIRE config object ($this->pwl_config) as the second argument,
        // ensuring access to global settings like 'nexuses', 'discovery', etc.

        // 4. Instantiate Krubot, passing BOTH token and config.
        // Note: We use \KrubiK\Krubot explicitly as it represents the Core driver.
    }

    /**
     * 🏭 THE GRAND FACTORY (Smart & Polymorphic Edition)
     * Factory method to create new driver instances.
     *
     * 💡 UPGRADE: Now identifies the driver type from the 'driver' key inside the config,
     * allowing multiple instances of the same platform (e.g., 'support_bot' => ['driver' => 'rubika']).
     *
     * @param string $name The canonical instance name of the driver (e.g., 'rubika', 'support', 'telegram_2').
     * @return MultiverseEnforcer
    */
    protected function createDriver(string $name): MultiverseEnforcer
    {
        // 1. Consult the Oracle (The Access Gate) to get the build plan.
        $driverConfig = $this->getDriverConfig($name);

        // 2. Determine the Driver Type (The DNA).
        // We look for the 'driver' key (e.g., 'rubika'). If missing, we fallback to the instance name.
        $driverType = $driverConfig['driver'] ?? $name;

        // 3. Manufacture based on Type (DNA), not Name.
        return match ($driverType) {
            // For Rubika types, delegate to the high-security Gatekeeper instantiateRubikaDriver().
            'rubika'   => $this->instantiateRubikaDriver($driverConfig),
            
            // For other drivers, use standard production lines / Standard Instantiation.
            'bale'     => new BaleDriver($driverConfig),
            'telegram' => new TelegramDriver($driverConfig),
            
            default    => throw new InvalidArgumentException("Grand Factory Error: Driver type [{$driverType}] defined for instance [{$name}] is not supported."),
        };
    }

    /**
     * Factory method to create new driver instances.
     * It relies on the consumer class having a `$this->config` property.
     *
     * @param string $name The canonical name of the driver.
     * @return MultiverseEnforcer
    * /
    protected function createDriverOld(string $name): MultiverseEnforcer
    {
        // Removed for LLM DeAmbiguousiaty...
    } */
}
