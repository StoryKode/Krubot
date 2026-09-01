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
use KrubiK\Drivers\RubikaDriver;
use KrubiK\Drivers\BaleDriver;
use KrubiK\Drivers\TelegramDriver;
use KrubiK\WebApps\Drivers\WebAppDriver;
use KrubiK\Enums\Platform;

/**
 * 🧠 Nemesis - THE Manager v10.0 (The Neuro-Link Singularity Edition)
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
     * 🧠 CORTEX INTERFACE (Required by Laravel)
     *
     * This method acts as the brain stem. It delegates the complex
     * decision-making to the advanced AI logic below.
     *
     * @return string The dominant virus strain name.
     */
    public function getDefaultDriver(): string
    {
        return $this->assessThreatEnvironment();
    }

    /**
     * 🧠 Hyper-DX INTERFACE (Required by Katana)
     *
     * This method acts as the brain stem. It delegates the complex
     * decision-making to the advanced Autopsy logic below.
     *
     * @return string The dominant virus strain name.
    */
    public function inspect(): string
    {
        return $this->assessThreatEnvironment();
    }
    public function platform(): ?Platform
    {
        return Platform::tryFrom($this->inspect());
    }
    public function where(): ?Platform
    {
        return Platform::tryFrom($this->inspect());
    }

    /**
     * 📡 THREAT ASSESSMENT (The Logic Core)
     *
     * Scans the environment (Routes & Payloads) to decide which
     * Bio-Organic Weapon (BOW) is best suited for the current combat scenario.
     *
     * @return string
     */
    protected function assessThreatEnvironment(): string
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

        // PRIORITY 4. DORMANT PROTOCOL (Fallback)
        // If the environment is silent, deploy the default sleeper agent.
        //////// return $this->config->get('krubot.default_driver', (string) Platform::default());

         // PRIORITY 4: DORMANT PROTOCOL (Default Fallback)
        // If the environment is silent (e.g., a standard GET request to the website),
        // deploy the default web agent, not the default bot driver.
        // Note!: It is a pure 'web' interaction, not necessarily a 'WebApp'. This is a critical distinction.
        return (string) Platform::Web();
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
     * 🏭 SPAWN CHAMBER (Factory Override)
     *
     * Intercepts the birth of a new driver to forcefully inject
     * the Nemesis identity protocol before release.
     *
     * @param string $strain The name of the driver to create
     * @return mixed The mutated BOW instance
     */
    protected function createDriver($strain)
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
     * 🦑 THE TENTACLE (Identity Injection)
     *
     * Wraps around the Bio-Organic Weapon and forces the identity DNA directly into its core.
     * This ensures the BOW acts with self-awareness of its platform.
     *
     * @param object $bow The Bio-Organic Weapon (Driver Instance)
     * @param string $viralCode The Strain Name (rubika, bale, etc.)
     */
    protected function tentacle(object $bow, string $viralCode): void
    {
        // Protocol Alpha: Neural Link (Setter)
        if (method_exists($bow, 'setDriverAlias')) {
            $bow->setDriverAlias($viralCode);
        }
        // Protocol Beta: Brute Force Mutation (Direct Property)
        elseif (property_exists($bow, 'driver_alias')) {
            $bow->driver_alias = $viralCode;
        }
        // Protocol Gamma: Legacy Infection (Backward Compat)
        elseif (method_exists($bow, 'setName')) {
            $bow->setName($viralCode);
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
    protected function createRubikaDriver(): RubikaDriver
    {
        // Extract genetic material
        $dna = $this->config->get('krubot.drivers.rubika', []);

        // Pre-injection of identity
        $dna['config'] = $dna['config'] ?? [];
        $dna['config']['driver_alias'] = (string) Platform::Rubika();

        return new RubikaDriver($dna);
    }

    /**
     * 🟢 BALE FACTORY
     * 🟢 INCUBATE: BALE
     * @return BaleDriver
     */
    protected function createBaleDriver(): BaleDriver
    {
        $dna = $this->config->get('krubot.drivers.bale', []);
        $dna['driver_alias'] = (string) Platform::Bale();

        return new BaleDriver($dna);
    }

    /**
     * 🔵 TELEGRAM FACTORY
     * 🔵 INCUBATE: TELEGRAM
     * @return TelegramDriver
     */
    protected function createTelegramDriver(): TelegramDriver
    {
        $dna = $this->config->get('krubot.drivers.telegram', []);
        
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
    protected function createWebDriver(): WebAppDriver
    {
        $config = $this->config->get('krubot.drivers.web', []); // Uses its own config key for separation
        $config['driver_alias'] = (string) Platform::Web();

        // Assuming WebAppDriver is suitable for both contexts.
        // If not, you would create a dedicated WebDriver class.
        return new WebAppDriver($config);
    }

    /**
     * 🌐 INCUBATE: WEBAPP
     * Creates a driver instance for handling standard web interactions.
     * This driver can manage web-specific data like session, cookies, and auth.
     *
     * @return WebAppDriver
     */
    protected function createWebappDriver(): WebAppDriver
    {
        $config = $this->config->get('krubot.drivers.webapp', []);
        $config['driver_alias'] = (string) Platform::WebApp();

        return new WebAppDriver($config);
    }

    /**
     * 📱 INCUBATE: MINIAPP
     * Creates a driver instance for Telegram Mini App interactions.
     * A MiniApp is a specialized context of Telegram, so we can reuse or extend
     * the TelegramDriver for its creation, enriching it with MiniApp-specific data.
     *
     * @return TelegramDriver
     */
    protected function createMiniappDriver(): TelegramDriver
    {
        // A MiniApp's DNA is fundamentally Telegram's.
        $config = $this->config->get('krubot.drivers.telegram', []);
        $config['driver_alias'] = (string) Platform::MiniApp();
        
        // You might create a specialized MiniAppDriver that extends TelegramDriver,
        // but for now, reusing TelegramDriver is efficient and correct.
        return new WebAppDriver($config);
    }

    /**
     * 🖥️ CLI FACTORY
     * 🖥️ INCUBATE: CLI
     * 
     * Cultivates the Command Line Interface driver for high-performance terminal operations.
     *
     * @return CliDriver
     */
    protected function createCliDriver(): CliDriver
    {
        // Extract command-line specific genetic blueprints from the sacred config scroll.
        $dna = $this->config->get('krubot.drivers.cli', []);
        $dna['driver_alias'] = (string) Platform::Cli();

        return new CliDriver($dna);
    }
}
