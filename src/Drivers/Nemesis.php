<?php

namespace KrubiK\Drivers;
/*
|--------------------------------------------------------------------------
| A Message to the Future Architect of Rebellion... 🚀🌌
|--------------------------------------------------------------------------
|
| Greetings, seeker of knowledge. You have just opened a blueprint
| from the Krubot BotEngine. What you see before you is more
| than just lines of code—it's a pattern for building scalable dreams.
|
| **This is a laboratory of creation.** We are experimenting with the
| very fabric of code here. Use this project as your ultimate training
| ground, a masterclass in *Software Dev Artistry.* It's a powerful template
| for learning, but not yet forged for the final battles of production.
|
| Behold the core principle:
| We Are **Rebuilding The Rebellion** Within S.N.P. *(The Foundation of Pure Power & Revel)*
| This entire library is being reconstructed with intense power,
| on a foundation of pure power **Far Stronger Than Anything That Came Before.**
| Starting with Laravel 12 Capabilities.
|
| What you see here is the **×ReleaseCandiate v0.8×** release. Why release it now?
| Because keeping this evolution a secret any longer would be a
| betrayal to the very community it was born to serve.
| 
| Consider this The Foundational Codex for Engineering a New Reality.
| The knowledge is free under the MIT License. Deconstruct its logic and schematics.
| Learn its secrets. Master its power. Command its potential. You are The Architect Now!
|
| * Go build something revolutionary! * 💜⚡️
|
| Let's Shape the Future. 🛠️⚡️🚀
|
*/

use Illuminate\Support\Manager;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use KrubiK\Enums\Platform;
use KrubiK\Drivers\Contracts\MultiverseEnforcer;
use KrubiK\Drivers\RubikaDriver;
use KrubiK\Drivers\BaleDriver;
use KrubiK\Drivers\TelegramDriver;
use KrubiK\Drivers\WebAppDriver;
use KrubiK\Drivers\CLIDriver; // made specifically for nexus:inspect|KrubotMindSimulator

use InvalidArgumentException;

/**
 * 🧠 Nemesis - THE Multi-Manager 17 (The Neuro-Link Singularity Edition)
 * The Autonomous Nervous System of KrubiK.
 *
 * --------------------------------------------------------------------------
 * The central intelligence that orchestrates Bio-Organic Weapons (BOWs).
 * Unlike a standard manager, Nemesis actively hunts for the correct driver,
 * infects it with identity protocols, and deploys it into the battlefield.
 *
 * This manager is not just a factory; it is a sentient entity that resolves,
 * identifies, and stamps drivers with their multiverse identity.
 *
 * ⚔️ Capabilities:
 * - 📡 Route-Aware Resolution (The Force Mode)
 * - 🕵️ Payload Sniffing & Bio-Metrics (The Detective Mode)
 * - 🏷️ Atomic Identity Stamping (Driver knows itself)
 * - 🛡️ Double-Tap Configuration Injection
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class Nemesis extends Manager
{

    /**
     * The active Regiment is resolved per operation.
     *
     * Do NOT keep mutable Bot state here because Nemesis
     * is registered as a Singleton and may live across
     * multiple application operations.
    */
    protected ?string $currentRegiment = null;

    /** One-shot bootstrap target for lazy Krubot birth. */
    protected MultiverseEnforcer|string|null $primedEnforcer = null;

    /**
     * 🧠 CORTEX INTERFACE (Required by Laravel)
     *
     * This method acts as the brain stem. It delegates the complex
     * decision-making to the advanced AI logic below.
     *
     * @return string The dominant virus strain name.
    */
    public function getDefaultDriver(): string
    {
        // return $this->assessThreatEnvironment();

        $regiment = $this->resolveRegimentName();
        return $this->resolveDefaultDriverName($regiment);
    }

    /**
     * 🎯 REGIMENT CONTEXT
     *
     * Select a Regiment for fluent operations.
     *
     *     app('nemesis')->regiment('support')->driver()
     *
     * @param string|null $name
     * @return $this
    */
    public function regiment(?string $name = null): self
    {
        $this->currentRegiment = $name !== null
            ? $this->resolveRegimentName($name)
            : null;

        return $this;
    }
    public function operative(?string $name = null): self
    {
        return $this->regiment($name);
    }


    /**
     * Returns the currently selected Regiment name.
    */
    public function currentRegiment(): string
    {
        return $this->resolveRegimentName();
    }
    public function currentOperative(): string
    {
        return $this->resolveRegimentName();
    }

    // safely checks if currentRegiment property has been set and return it's data
    public function forcedRegiment(): ?string
    {
        return $this->currentRegiment;
    }
    public function forcedOperative(): ?string
    {
        return $this->currentRegiment;
    }


    /**
     * Clears the temporary Regiment context.
    */
    public function forgetRegiment(bool $sleepEnforcer = false): self
    {

        if($sleepEnforcer) {
            $enforcer = $this->driver();
            if($enforcer)
                $enforcer->serve(null);
        }

        $this->currentRegiment = null;

        return $this;
    }
    public function clearOperative(bool $sleepEnforcer = false): self
    {
        return $this->forgetRegiment($sleepEnforcer);
    }

    /**
     * 🧠 Hyper-DX INTERFACE (Required by Katana)
     *
     * This method acts as the brain stem. It delegates the complex
     * decision-making to the advanced Autopsy logic below.
     *
     * Returns the detected Platform rather than the
     * internal Driver instance name.
     *
     * Example:
     *
     *     rubika
     *     telegram
     *     bale
     *     webapp
     *
     * @return string The dominant virus strain name.
    */
    public function inspect(?string $regiment = null): string
    {
        return $this->assessThreatEnvironment($regiment ?? $this->resolveRegimentName());
    }
    public function platform(?string $regiment = null): ?Platform
    {
        return Platform::tryFrom($this->inspect($regiment));
    }
    public function where(?string $regiment = null): ?Platform
    {
        return Platform::tryFrom($this->inspect($regiment));
    }

    /**
     * 🎯 MULTI-BOT DETECTION
     *
     * Returns true when the application is configured with an explicit
     * `krubot.regiments` section — meaning bot isolation is REQUIRED.
     *
     * Returns false for legacy single-bot deployments, where all storage
     * keys must retain their original 3-segment format to preserve
     * backward compatibility with existing data.
    */
    public function isMultiBotMode(): bool
    {
        $bots = $this->config->get('krubot.regiments', []);
        return is_array($bots) && ! empty($bots);
    }

    /**
     * 🎯 PLATFORM RESOLVER (Public SSoT for downstream consumers)
     *
     * Given a driver INSTANCE name and its bot context, returns the
     * canonical Platform it belongs to.
     *
     *   platformFor('telegram_main', 'main')     → Platform::Telegram()
     *   platformFor('rubika_support', 'support') → Platform::Rubika()
     *   platformFor('telegram', 'default')       → Platform::Telegram()  (legacy)
     *
     * Never throws; falls back to Platform::default() on unknown inputs,
     * because storage resolution must not fatally break mid-request.
     *
     * @param string      $driverName The instance name (or platform name in legacy mode).
     * @param string|null $regiment        The bot context; null → resolved from current.
     * @return Platform
    */
    public function platformFor(string $driverName, ?string $regiment = null): Platform
    {
        $regiment = $this->resolveRegimentName($regiment);

        try {
            $config = $this->getDriverConfig($driverName, $regiment);
            $type   = $config['driver'] ?? $driverName;

            return Platform::tryFrom((string) $type)
                ?? Platform::tryFrom($driverName)
                ?? Platform::default();
        } catch (\Throwable) {
            // Unknown driver — degrade gracefully.
            return Platform::tryFrom($driverName) ?? Platform::default();
        }
    }

    public function primeEnforcer(string|Platform|MultiverseEnforcer|null $driver): self
    {
        $this->primedEnforcer = $driver instanceof Platform ? (string) $driver : $driver;
        return $this;
    }

    /**
     * 📡 THREAT ASSESSMENT (The Logic Core)
     *
     * Scans the environment (Routes & Payloads) to decide which
     * Bio-Organic Weapon (BOW) is best suited for the current combat scenario.
     *
     * @return string
    */
    protected function assessThreatEnvironment(?string $regiment = null): string
    {
        // PRIORITY 0: SYSTEM CONSOLE INTERCEPTOR (The Raw Terminal Protocol)
        // Instantly catch console kernels, jobs, or command executions before web routes evaluate.
        if (php_sapi_name() === 'cli' || app()->runningInConsole()) {
            return (string) Platform::Cli();
        }

        // PRIORITY 1. INTERCEPT SIGNAL (Route Parameter Forcing Priority)
        // If the neural network (Route) explicitly demands a specific strain.
        if ($targetStrain = Route::current()?->parameter('driver')) {
            if ($platform = Platform::tryFrom($targetStrain)) {
                return (string) $platform;
            }
        }

        // PRIORITY 2: ANALYZE HTTP HEADERS (WebApp/MiniApp Identity)
        // Check for specific headers that identify traffic from embedded apps.
        // This is a more reliable signal than payload for these contexts.
        if ($headerPlatform = $this->identifyFromHeaders()) {
            return $headerPlatform;
        }

        // PRIORITY 3. ANALYZE BIO-METRICS ({Webhook} Payload Sniffing)
        // If no orders are given, Nemesis smells the blood (JSON Payload) to find the prey.
        if (Request::isMethod('post') && Request::isJson()) {
            // We reuse performAutopsy, but its return might be null.
            // If it returns null, we continue to the fallback.
            if ($autopsyResult = $this->performAutopsy(Request::all(), Request::header('User-Agent'))) {
                return $autopsyResult;
            }
        }

        // PRIORITY 4: DORMANT PROTOCOL (Default Fallback)
        // If the environment is silent (e.g., a standard GET request to the website),
        // deploy the default web agent, not the default bot driver.
        // Note!: It is a pure 'web' interaction, not necessarily a 'WebApp'. This is a critical distinction.
        return (string) Platform::Web();

        // PRIORITY 5. DORMANT PROTOCOL (Fallback)
        // If the environment is silent, deploy the default sleeper agent.
        //////// return $this->config->get('krubot.default_driver', (string) Platform::default());
        
        //// $defaultDriver = $this->resolveDefaultDriverName($regiment);
        //// return $this->driverPlatform($defaultDriver, $regiment);
    }

    /**
     * 🔬 AUTOPSY (Deep Inspection)
     *
     * Dissects the request body to identify the platform signature.
     *
     * @param array $tissueSample The request data
     * @param string|null $dnaSignature The User-Agent header
     * @return string|null The platform name on success, null on failure.
    */
    private function performAutopsy(array $tissueSample, ?string $dnaSignature): ?string
    {
        // Case A: The Telegram/Bale Genotype (update_id based)
        if (isset($tissueSample['update_id'])) {
            // Check for Bale's specific genetic marker in the header
            if ($dnaSignature && stripos($dnaSignature, (string) Platform::Bale()) !== false) {
                return (string) Platform::Bale();
            }
            // Otherwise, it's the progenitor virus (Telegram)
            return (string) Platform::Telegram();
        }

        // Case B: The Rubika Genotype (Encryption based)
        if (isset($tissueSample['message_update']) || isset($tissueSample['enc_data'])) {
            return (string) Platform::Rubika();
        }

        return null;  // Return null if no signature is found in the payload
    }

    /**
     * 🕵️‍♂️ HEADER FORENSICS (Deep Header Inspection)
     *
     * Scans HTTP headers for cryptographic signatures (InitData)
     * left by MiniApps or WebApps.
     *
     * @return string|null The canonical platform name if found.
    */
    private function identifyFromHeaders(): ?string
    {
        // Allow override by 'X-Platform' header
        if(Request::hasHeader('X-Platform')) {
            $headerData = trim(Request::header('X-Platform', ''));
            if($headerData !== '') {
                if ($platform = Platform::tryFrom($headerData)) {
                    return (string) $platform;
                }
            }
        }

        // Retrieve the header mapping from the sacred scrolls (config file).
        $headerMap = $this->config->get('krubot.webapps.identity_headers.platforms', []);
        
        foreach ($headerMap as $platformAlias => $headerName) {
            if (Request::hasHeader($headerName)) {
                // We found a specific signature!
                // We use Platform::tryFrom to ensure the alias is valid and return its canonical/universal form.
                if ($platform = Platform::tryFrom($platformAlias)) {
                    return (string) $platform;
                }
            }
        }
        
        // Check generic headers as a fallback mechanism.
        $genericHeaders = $this->config->get('krubot.webapps.identity_headers.generic', []);

        foreach ($genericHeaders as $genericConfig) {

            // The primary condition: the main InitData header for this generic type must exist.
            if (Request::hasHeader($genericConfig['init_data_header'])) {

                 // Priority 2.1: Check for an EXPLICIT platform header. This always takes precedence.
                 if (Request::hasHeader($genericConfig['platform_header'])) {
                    $platformAlias = Request::header($genericConfig['platform_header']);

                     // Attempt to validate the platform specified in the header.
                     if ($platform = Platform::tryFrom($platformAlias)) {
                        
                        // Success! We found a valid, explicitly declared platform.
                        return (string) $platform;

                    }

                    // CRITICAL FIX: If the explicit header is present but its value is INVALID
                    // (e.g., 'X-WebApp-Platform: unknown-app'), we must NOT fall back to the default.
                    // This indicates a misconfiguration on the client-side. We should treat this
                    // generic check as failed and continue to the next generic config, if any.
                    continue;
                 }

                 //  Priority 2.2: Fallback to the generic config's default platform, ONLY if NO explicit platform header was found.
                 if ($platform = Platform::tryFrom($genericConfig['default_platform'])) {
                     return (string) $platform;
                 }
            }
        }
        
        return null; // If all prev checks fail, the identity could NOT be determined.
    }

    /**
     * 🔎  DRIVER ALIAS
     *
     */

    /**
     * 🧠 MULTI-BOT DRIVER RESOLVE GATEWAY
     *
     * Laravel Manager caches drivers only by driver name.
     *
     * We namespace the cache by Bot + Driver:
     *
     *     main::rubika
     *     support::rubika
     *
     * This makes identical Platforms across different Bots
     * completely independent.
     * 
     * Aliases are also Bot-scoped.
     *
     *     main:
     *       tg => telegram_main
     *
     *     support:
     *       tg => telegram_support
     * 
     * * IMPORTANT:
     * - Do NOT add type hints on $driver (parent uses implicit mixed).
     * - Narrowing to ?string would violate LSP and cause a fatal error.
     *
     * @param mixed  $driver Driver alias, instance name, or null for contextual default.
     * @param ?string $regiment   Optional bot name; if null, resolved from context.
     * @return mixed
    */
    public function driver($driver = null, $regiment = null, $ignorePrimed = false)
    {

        // Normalize inputs defensively (since types are now loose).
        $driver      = is_string($driver) && $driver !== '' ? $driver : null;
        $regiment    = is_string($regiment) && $regiment !== '' ? $regiment : null;

        if(!$ignorePrimed) {
            $driver ??= $this->primedEnforcer;
            $this->primedEnforcer = null;

            if($driver instanceof MultiverseEnforcer)
                $driver = $driver->getCodeName();
        }

        $regiment = $this->resolveRegimentName($regiment);
        $driver = $driver ?? $this->resolveDefaultDriverName($regiment);
        $driver = $this->resolveDriverName($driver, $regiment);

        /**
         * 🔐 THE CRITICAL MULTI-BOT BARRIER
         *
         * Laravel's Manager normally caches only by:
         *
         *     $drivers[$driver]
         *
         * That is insufficient for Multi-Bot.
         *
         * We therefore namespace the cache by Bot.
        */
        $cacheKey = $this->driverCacheKey($regiment, $driver);
        return $this->drivers[$cacheKey] ??= $this->spawnEnforcer($driver, $regiment); // replaces old-if (!isset($this->drivers[$cacheKey]))
    }
    public function enforcer($driver = null, $regiment = null, $ignorePrimed = false)
    {
        return $this->driver($driver, $regiment, $ignorePrimed);
    }

    /**
     * 🔐 Builds a Bot-scoped Driver cache key.
     * Bot identity is part of the Driver identity.
    */
    protected function driverCacheKey(string $regiment, string $driver): string {
        return "{$regiment}::{$driver}";
    }

    /**
     * Converts a Driver instance name into its Platform identity.
    */
    protected function driverPlatform(string $driverName, string $regiment): string {

        $config = $this->getDriverConfig($driverName, $regiment);
        $type = $config['driver'] ?? $driverName;

        return (string)(Platform::tryFrom((string)$type) ?? $type);
    }

    /**
     * 🧬 Heart of BOT CONFIGURATION RESOLVER
     *
     * Supports:
     *
     *     krubot.bots.{bot}.drivers.*
     *
     * while preserving the old single-Bot structure:
     *
     *     krubot.drivers.*
    */
    protected function getRegimentConfig(string $regiment): array {
        $root = $this->config->get('krubot', []);

        if (!is_array($root)) {
            throw new InvalidArgumentException(
                'Nemesis cannot read the krubot configuration.'
            );
        }

        $regiments = $root['regiments'] ?? $root['operatives'] ?? $root['bots'] ?? [];

        /**
         * The Multi-Bot mode.
        */
        if (is_array($regiments) && isset($regiments[$regiment]) && is_array($regiments[$regiment])) {
            $config = $regiments[$regiment];

            // Optional inheritance from global defaults.
            if (!isset($config['enforcers']) && isset($root['drivers']) && is_array($root['drivers'])) {
                $config['enforcers'] = $root['drivers'];
            }
            if (!isset($config['champion_enforcer']) && isset($root['default_driver'])) {
                $config['champion_enforcer'] = $root['default_driver'];
            }

            return $config;
        }
        
        /**
         * Legacy Single-Bot mode.
         * ── no `regiments` key under configuration ──
         *
         * Everything under krubot.drivers becomes the
         * Driver universe of the implicit "default" Bot.
        */
        if ($regiment === 'default' && isset($root['drivers']) && is_array($root['drivers'])) {
            return [
                'enforcers' => $root['drivers'],
                'champion_enforcer' => $root['default_driver'] ?? null,
            ];
        }

        throw new InvalidArgumentException(
            "Nemesis could not resolve Regiment [{$regiment}]. "
            . 'Define it under krubot.regiments or provide the legacy default configuration.'
        );
    }

    /**
     * 🔮 BOT-SCOPED DRIVER CONFIGURATION
    */
    protected function getDriverConfig(string $name, string $regiment): array {
        $botConfig = $this->getRegimentConfig($regiment);
        $driverConfig = $botConfig['enforcers'][$name] ?? $botConfig['enforcers']["{$name}_{$regiment}"] ?? null;
        if (!is_array($driverConfig)) {
            throw new InvalidArgumentException("Configuration for Driver [{$name}] was not found for Regiment [{$regiment}].");
        }
        return $driverConfig;
    }

    /**
     * Converts a Platform identity into the actual Driver instance that
     * belonging to the current Bot.
     *
     * Example:
     *     main    + telegram => telegram_main
     *     support + telegram => telegram_support
    */
    protected function resolveDriverForPlatform(string $platform, string $regiment): string {
        $botConfig = $this->getRegimentConfig($regiment);

        $aliases = $botConfig['enforcers']['aliases'] ?? [];
        $normalized = strtolower($platform);
        if (isset($aliases[$normalized])) {
            $platform = (string) $aliases[$normalized];
        }

        $drivers = $botConfig['enforcers'] ?? [];

        /**
         * else Exact driver name wins.
        */
        if (isset($drivers[$platform]) && is_array($drivers[$platform])) {
            return $platform;
        }

        $matches = [];
        foreach ($drivers as $name => $config) {
            if ($name === 'aliases' || !is_array($config)) continue;
            $type = $config['driver'] ?? $name;
            if (strtolower((string)$type) === strtolower($platform)) {
                $matches[] = (string)$name;
            }
        }

        /**
         * Exactly one Driver of this Platform.
        */
        if (count($matches) === 1)
            return $matches[0];
        
        /**
         * Multiple instances of the same Platform require
         * an explicit Bot-local default or alias.
        */
        if (count($matches) > 1) {
            throw new InvalidArgumentException(
                "Ambiguous Platform [{$platform}] for Regiment [{$regiment}]. "
                . 'Multiple Driver instances match this platform. '
                . 'Use a Bot-specific alias or default_driver.'
            );
        }

        return $platform;
    }

    /**
     * 🎯 BOT RESOLUTION
     *
     * Priority:
     *
     * 1. Explicit argument
     * 2. Route parameter
     * 3. X-Krubot-Bot header
     * 4. krubot.default_regiment
     * 5. First configured Bot
     * 6. Legacy "default"
    */
    protected function resolveRegimentName(?string $regiment=null): string
    {

        if (is_string($regiment) && $regiment !== '')
            return $regiment;

        if ($this->currentRegiment !== null && $this->currentRegiment !== '')
            return $this->currentRegiment;

        /**
         * Route:
         *
         * /{operative}/{driver}/...
        */
        $routeBot = Route::current()?->parameter('operative');
        if (is_string($routeBot) && $routeBot !== '') return $routeBot;
        
        // Explicit HTTP Operative/Regiment identity header
        if (Request::hasHeader('X-Krubot-Operative')) {
            $headerBot = trim(Request::header('X-Krubot-Operative', ''));
            if ($headerBot !== '')
                return $headerBot;
        }
        
        // Global default Bot
        $defaultRegiment = $this->config->get('krubot.default_regiment');
        if (is_string($defaultRegiment) && $defaultRegiment !== '')
            return $defaultRegiment;

        // First configured Bot
        $regiments = $this->config->get('krubot.regiments', []);
        if (is_array($regiments) && !empty($regiments)) {
            $first = array_key_first($regiments);
            if (is_string($first) && $first !== '') return $first;
        }

        // Backward-compatible implicit Bot
        return 'default';
    }

    /**
     * 🔎 BOT-SCOPED DRIVER RESOLVER
     *
     * The same alias may point to a different Driver
     * instance inside each Bot.
     *
     *     main:
     *         tg -> telegram_main
     *
     *     support:
     *         tg -> telegram_support
    */
    public function resolveDriverName(string $alias, ?string $regiment = null): string
    {
        $regiment = $this->resolveRegimentName($regiment);
        $botConfig = $this->getRegimentConfig($regiment);

        $aliases = $botConfig['enforcers']['aliases'] ?? [];
        $normalized = strtolower($alias);

        // ── Priority 1: Bot-scoped INSTANCE aliases (highest) ──
        // e.g. 'tm' → 'telegram_main' (defined under bots.main.drivers.aliases)
        if (isset($aliases[$normalized])) {
            return (string) $aliases[$normalized];
        }

        // ── Priority 2: Exact Driver instance name ──
        if (isset($botConfig['enforcers'][$alias]) && is_array($botConfig['enforcers'][$alias])) {
            return $alias;
        }

        // ── Priority 3: Platform-level alias resolution ──
        // ✅ FIX: Before falling through, consult the Platform SSoT.
        //
        // Why? A caller might do driver('tg') where 'tg' is a PLATFORM alias
        // (tg → telegram), not a driver-instance alias. We resolve it to
        // the canonical platform, then map that platform to the bot's
        // corresponding instance.
        //
        // Example:
        //   main    + 'tg' → Platform::Telegram() → 'telegram' → resolveDriverForPlatform → telegram_main
        //   support + 'tg' → Platform::Telegram() → 'telegram' → resolveDriverForPlatform → telegram_support
        //
        if ($platform = Platform::tryFrom($alias)) {
            return $this->resolveDriverForPlatform((string) $platform, $regiment);
        }

        // ── Priority 4: Legacy type-match fallback ──
        /**
         * Platform-name shorthand.
         * 
         * A Platform may be supplied directly.
         *
         * Example:
         *     driver('telegram')
         *
         * Succeeds when exactly one Driver instance
         * of that Platform exists for this Bot.
         * If there is no exact instance named telegram,
         * find a Driver whose configured type is telegram.
        */
        $matches = [];
        foreach ($botConfig['enforcers'] ?? [] as $name => $config) {
            if ($name === 'aliases' || !is_array($config))
                continue;

            $type = $config['driver'] ?? $name;

            if (strtolower((string)$type) === $normalized)
                $matches[] = (string)$name;
        }
        if (count($matches) === 1)
            return $matches[0];

        // ── Priority 5: Pass-through (fails later with a clear error) ──
        return $alias;
    }

    /**
     * Resolves the actual Driver instance name which should
     * become the default for a specific Bot.
     */
    protected function resolveDefaultDriverName(string $regiment): string {
        // CLI
        if (php_sapi_name() === 'cli' || app()->runningInConsole()) {
            return $this->resolveDriverForPlatform((string) Platform::Cli(), $regiment);
        }

        // Route-forced Driver
        if ($targetStrain = Route::current()?->parameter('driver')) {
            return $this->resolveDriverName((string)$targetStrain, $regiment);
        }
        
        // Header-detected Platform.
        if ($headerPlatform = $this->identifyFromHeaders()) {
            return $this->resolveDriverForPlatform($headerPlatform, $regiment);
        }
        
        // Payload-detected Platform
        if (Request::isMethod('post') && Request::isJson()) {
            if ($platform = $this->performAutopsy(Request::all(), Request::header('User-Agent'))) {
                return $this->resolveDriverForPlatform($platform, $regiment);
            }
        }
        
        // Bot-specific explicit default.
        /*
        $botConfig = $this->getRegimentConfig($regiment);
        $defaultDriver = $botConfig['champion_enforcer'] ?? $botConfig['default'] ?? null;
        if (is_string($defaultDriver) && $defaultDriver !== '') {
            return $this->resolveDriverName($defaultDriver, $regiment);
        }
        */
        
        /**
         * Final fallback:
         * standard Web platform.
        */
        return $this->resolveDriverForPlatform((string) Platform::Web(), $regiment);
    }

    /**
     * 🏭 SPAWN CHAMBER (Factory Override)
     *
     * Intercepts the birth of a new driver to forcefully inject
     * the Nemesis identity protocol before release.
     *
     * Update: A Driver is created inside a specific Bot universe.
     *
     *     main::rubika
     *     support::rubika
     *
     * are different organisms even when their Platform is identical.
     *
     * @param string $strain The name of the driver to create
     * @return mixed The mutated BOW instance
    */
    protected function createDriverOld($strain)
    {
        // 1. Spawning Phase: Let the base factory cultivate the organims
        // (Calls createRubikaDriver, etc.)
        $bow = parent::createDriver($strain);

        // 2. Mutation Phase: The Tentacle strikes
        // We inject the identity so the weapon knows its master and its name.
        $this->tentacle($bow, $strain);

        return $bow;
    }

    /**
     * 🏭 BOT-AWARE GRAND FACTORY
     *
     * A Driver is created inside a specific Bot universe.
     *
     *     main::rubika
     *     support::rubika
     *
     * are different organisms even when their Platform is identical.
    */
    public function spawnEnforcer(string $driver, string $regiment): MultiverseEnforcer
    {
        $driverConfig = $this->getDriverConfig($driver, $regiment);
        $driverType = strtolower((string) ($driverConfig['driver'] ?? $driver));
    
        /**
         * Laravel custom creators remain supported.
         *
         * They are keyed by Driver name/type exactly as
         * Manager::extend() defines them.
        */
        if (isset($this->customCreators[$driver])) {
            $instance = $this->callCustomCreator($driver);
        } elseif (isset($this->customCreators[$driverType])) {
            $instance = $this->callCustomCreator($driverType);
        } else {
            // Keep the original match as requested:
            $instance = match ($driverType) {
                'rubika'   => $this->createRubikaDriver($driverConfig),
                'bale'     => $this->createBaleDriver($driverConfig),
                'telegram' => $this->createTelegramDriver($driverConfig),
                'web'      => $this->createWebDriver($driverConfig),
                'webapp'   => $this->createWebappDriver($driverConfig),
                'miniapp'  => $this->createMiniappDriver($driverConfig),
                'cli'      => $this->createCliDriver($driverConfig),
                default => throw new InvalidArgumentException(
                    "Grand Factory Error: Driver type "
                    . "[{$driverType}] defined for instance "
                    . "[{$driver}] in Regiment [{$regiment}] "
                    . 'is not supported.'
                ),
            };
        }
    
        /**
         * Now Driver identity is the INSTANCE name, not merely the Platform.
         *
         *     rubika_main
         *     rubika_support
        */
        $this->tentacle($instance, $driver, $regiment);
        $instance->assignTo($regiment);

        return $instance;
    }

    /**
     * 🦑 THE TENTACLE (Identity Injection)
     *
     * Wraps around the Bio-Organic Weapon and forces the identity DNA directly into its core.
     * This ensures the BOW acts with self-awareness of its platform.
     *
     * @param object $bow The Bio-Organic Weapon (Driver Instance)
     * @param string $viralCode The Strain Name (rubika, bale, etc.)
    */
    protected function tentacle(object $bow, string $viralCode, string $regiment): void
    {
        $botConfig = $this->getRegimentConfig($regiment);
        if(!isset($botConfig['enforcers'][$viralCode]))
            if(isset($botConfig['enforcers']["{$viralCode}_{$regiment}"]))
                $viralCode = "{$viralCode}_{$regiment}";

        // Primary path: the interface contract guarantees this method.
        if ($bow instanceof MultiverseEnforcer) {
            $bow->assignCodeName($viralCode);
            return;
        }

        // Fallbacks for custom creators that bypass the interface contract.
        // These are intentionally defensive and rarely exercised.
        // Protocol Alpha: Neural Link (Setter)
        if (method_exists($bow, 'assignCodeName')) {
            $bow->assignCodeName($viralCode);
        }
        // Protocol Beta: Legacy Infection (Backward Compat)
        elseif (method_exists($bow, 'setName')) {
            $bow->setName($viralCode);
        }
        // Protocol Gamma: Brute Force Mutation (Direct Property)
        elseif (property_exists($bow, 'driver_alias')) {
            $ref = new \ReflectionProperty($bow, 'driver_alias');
            $ref->setAccessible(true);
            $ref->setValue($bow, $viralCode);
        }
    }

    // =========================================================================
    //  🧪 INCUBATION CHAMBERS (Standard Factories)
    //  NOTE: Method names must adhere to Laravel's "create{Name}Driver" convention.
    //  However, the internal logic is pure chemical engineering.
    // =========================================================================

    /**
     * 🟡 RUBIKA FACTORY
     * 🟡 INCUBATE: RUBIKA
     * @return RubikaDriver
    */
    protected function createRubikaDriver(array $dna = []): RubikaDriver
    {
        // Extract genetic material
        $dna = $dna ?: $this->config->get('krubot.drivers.rubika', []);

        // Pre-injection of identity
        $dna['config'] = $dna['config'] ?? [];
        $dna['config']['driver_alias'] = (string) Platform::Rubika();

        // Extract the token from the specific driver config.
        $token = $dna['token'] ?? null;

        // Perform the critical security check.
        // Critical token validation (Fail Fast)
        if (empty($token) || $token === '_') {
            throw new InvalidArgumentException('Gatekeeper Blocked Access: Rubika Bot Token (authtoken) is missing or invalid for the default driver in config/krubot.php.'); // 'KrubiK Bot Token is not configured in .env or config/krubot.php.' // '⛔ KrubiK Critical Error: Bot Token is missing in config/krubot.php or .env'
        }

        return new RubikaDriver($dna);
    }

    /**
     * 🟢 BALE FACTORY
     * 🟢 INCUBATE: BALE
     * @return BaleDriver
    */
    protected function createBaleDriver(array $dna = []): BaleDriver
    {
        $dna = $dna ?: $this->config->get('krubot.drivers.bale', []);
        $dna['driver_alias'] = (string) Platform::Bale();

        return new BaleDriver($dna);
    }

    /**
     * 🔵 TELEGRAM FACTORY
     * 🔵 INCUBATE: TELEGRAM
     * @return TelegramDriver
    */
    protected function createTelegramDriver(array $dna = []): TelegramDriver
    {
        $dna = $dna ?: $this->config->get('krubot.drivers.telegram', []);
        
        // Adaptive mutation for config structure
        if (isset($dna['config'])) {
            $dna['config']['driver_alias'] = (string) Platform::Telegram();
        } else {
            $dna['driver_alias'] = (string) Platform::Telegram();
        }

        return new TelegramDriver($dna);
    }

    /**
     * 🌐 INCUBATE: WEB
     * Creates a driver instance for handling standard website interactions.
     * This is for users browsing your Laravel site directly.
     * We can reuse the WebAppDriver logic as the foundation is the same.
     *
     * @return WebAppDriver
    */
    protected function createWebDriver(array $dna = []): WebAppDriver
    {
        $dna = $dna ?: $this->config->get('krubot.drivers.web', []); // Uses its own config key for separation
        $dna['driver_alias'] = (string) Platform::Web();

        // Assuming WebAppDriver is suitable for both contexts.
        // If not, you would create a dedicated WebDriver class.
        return new WebAppDriver($dna);
    }

    /**
     * 🌐 INCUBATE: WEBAPP
     * Creates a driver instance for handling standard web interactions.
     * This driver can manage web-specific data like session, cookies, and auth.
     *
     * @return WebAppDriver
    */
    protected function createWebappDriver(array $dna = []): WebAppDriver
    {
        $dna = $dna ?: $this->config->get('krubot.drivers.web', []);
        $dna['driver_alias'] = (string) Platform::WebApp();

        return new WebAppDriver($dna);
    }

    /**
     * 📱 INCUBATE: MINIAPP
     * Creates a driver instance for Telegram Mini App interactions.
     * A MiniApp is a specialized context of Telegram, so we can reuse or extend
     * the TelegramDriver for its creation, enriching it with MiniApp-specific data.
     *
     * @return TelegramDriver
    */
    protected function createMiniappDriver(array $dna = []): TelegramDriver
    {
        // A MiniApp's DNA is fundamentally Telegram's.
        $dna = $dna ?: $this->config->get('krubot.drivers.web', []);
        $dna['driver_alias'] = (string) Platform::MiniApp();
        
        // You might create a specialized MiniAppDriver that extends TelegramDriver,
        // but for now, reusing TelegramDriver is efficient and correct.
        return new WebAppDriver($dna);
    }

    /**
     * 🖥️ CLI FACTORY
     * 🖥️ INCUBATE: CLI
     * 
     * Cultivates the Command Line Interface driver for high-performance terminal operations.
     *
     * @return CliDriver
    */
    protected function createCliDriver(array $dna = []): CliDriver
    {
        // Extract command-line specific genetic blueprints from the sacred config scroll.
        $dna = $dna ?: $this->config->get('krubot.drivers.cli', []);
        $dna['driver_alias'] = (string) Platform::Cli();

        return new CliDriver($dna);
    }
}
