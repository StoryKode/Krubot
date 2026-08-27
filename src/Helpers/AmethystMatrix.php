<?php

namespace KrubiK\Helpers;

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

use KrubiK\Krubot;
use KrubiK\DTOs\Message;
use Throwable;
use Illuminate\Support\Facades\Log;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Illuminate\Support\Facades\Cache;
use Psr\Log\LogLevel;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use WeakReference;

/**
 * 🔮 The Amethyst Matrix: The All-Seeing Eye of Krubot.
 *
 * She observes the chaos, remembers the patterns, and gazes into the abyss.
 * and operates statically, gaining context from the Warlord at the dawn of each process.
 * A Hyper-DX static facade that bridges the gap between logging, caching, and debugging.
 * 
 * allows you Enable/Disable Entire Logging of Application with a Snap!
 * and much more...
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
 */
final class AmethystMatrix
{
    use Macroable; // ⚡ The ability to evolve and learn new spells dynamically. ⚡

    /** 
     * The Pulse of AmethystMatrix. 
     * Indicates if AmethystMatrix is currently awake and processing the reality.
     * @var bool 
     */
    public static bool $isAwake = false;

    /** @var array The cached configuration for Amethyst. */
    private static array $config = [];

    /** @var Message|null The message that provides context for the current cycle. */
    private static ?Message $messageContext = null;

    /** @var WeakReference|null Save a Reference to the active Warlord instance, to prevent memory leaks. */
    private static ?WeakReference $warlordRef = null;

    /**
     * The Cached Consciousness of AmethystMatrix.
     * Stores a map of all publicly accessible methods for O(1) lookup speed.
     * 
     * @var array<string, int>
    */
    public static array $publicInterface = []; // to cache available methods

    /**
     * Maps domain-specific action verbs to PSR-3 standard logging level constants.
     *
     * By using constants from Psr\Log\LogLevel instead of magic strings,
     * we achieve several critical advantages:
     * 1.  Type Safety: Prevents typos that would lead to silent logical errors.
     * 2.  Maintainability: Centralizes the log level definitions.
     * 3.  IDE Support: Enables autocompletion and static analysis validation.
     * 4.  Readability: The code's intent becomes explicit and self-documenting.
     *
     * Note: The 'vault' key maps to a custom 'store' level, which must be
     * handled separately as it's not part of the PSR-3 standard.
     *
     * @var array<string, string>
     */
    protected static array $logLevelMap = [
        'wail'     => LogLevel::EMERGENCY,
        'scream'   => LogLevel::ALERT,
        'condemn'  => LogLevel::CRITICAL,
        'yell'     => LogLevel::ERROR,
        'prophesy' => LogLevel::WARNING,
        'gaze'     => LogLevel::NOTICE,
        'observe'  => LogLevel::INFO,
        'whisper'  => LogLevel::DEBUG,

        // Custom actions not directly mapped to PSR-3 level constants for logging.
        'remember' => 'store',
        'vault'    => 'store',
    ];

    /**
     * 🔗 SPIRITUAL LINK:
     * Awakens AmethystMatrix for the current request cycle and Connects AmethystMatrix to the living Krubot instance.
     * Called automatically by the 'HasAmethystMatrix' Arcane.
     *
     * This method makes bridge between the Warlord and the Oracle.
    */
    public static function awaken(Krubot $bot, ?Message $message = null): void
    {
        self::$warlordRef = null;
        self::$warlordRef = WeakReference::create($bot);

        // First, achieve enlightenment (load config). Caches it for the request.
        if (empty(self::$config)) {
            self::$config = config('krubot.amethyst', []);
        }
        // Then, open her eyes to the present context.
        self::$messageContext = $message; // It's OK if it's `null` yet

        self::$isAwake = true;
    }

    public static function refresh(?bool $fetchNewMessageFromKrubot = false): bool
    {
        if(self::$isAwake) {

            if($fetchNewMessageFromKrubot)
                self::setWorkingMessage(self::bot()->thisMessage() ?? null); // reload message from bot to context

            return true;
        }

        // --- ⚡ AUTO-AWAKENING PROTOCOL ⚡ ---
        // Before executing ANY spell, we ensure the AmethystMatrix is awake and linked to the Warlord.
        // Checking the boolean property is O(1) and virtually free.
        if (! self::$isAwake) {
            // AmethystMatrix is sleeping! We must jolt it awake using the Warlord instance.
            // We check if 'krubot' is bound to avoid crashing in very early app boot stages.
            if (app()->bound('krubot')) {

                // Summon the Warlord and Force-Awaken the AmethystMatrix.

                $krubot = app('krubot');
                $msg = null;
                if($fetchNewMessageFromKrubot)
                    $msg = $krubot->thisMessage() ?? null;

                self::awaken($krubot, $msg);
            }
        }

        return self::$isAwake;
    }

    /* 
     * Prepares AmethystMatrix for the current request by providing her with context data.
     *
     * @param Message $message The incoming update message.
    */
    public static function setWorkingMessage(?Message $message = null): void
    {
        // =====================================================================
        // STATE INITIALIZATION & OPTIMIZATION
        // =====================================================================        
        // Global State Injection
        self::$messageContext = $message;
    }

    /**
     * ⚡ Helper to find current message in AmethystMatrix Logging Cycle.
    */
    public static function getWorkingMessage(): ?Message
    {
        return self::$messageContext;
    }

    /**
     * 🔮 AWAKEN: Called at the start of a request via Krubot.
     * Sets the context for the current execution cycle.
     * @internal
    */
    public static function boot(Krubot $bot, ?Message $message = null): void
    {
        self::awaken($bot, $message);
    }

    /**
     * 🌑 VOID: Disconnects the link to prevent memory leaks in Swoole/Lazarus.
     *  Wipes memory to prevent data bleeding between requests.
     */
    public static function sleep(): void
    {
        self::$warlordRef = null;
        self::$messageContext = null;
        self::$isAwake = false;
    }

    /**
     * Resets her short-term memory, preparing her for the next vision.
     * CRUCIAL for async environments to prevent context bleeding.
     * @internal
     */
    public static function reset(): void
    {
        self::sleep();
    }

    // --- 👁️ THE SENSES ---

    /**
     * Consults the Oracle to reveal the true form of a variable as a raw string.
     *
     * This method acts as a high-fidelity interface to captures the rich, detailed output of Symfony's VarDumper.
     *
     * Unlike standard dumping which outputs directly to the browser or CLI,
     * The Oracle Captures the introspection into a pristine string buffer.
     * This allows for logging, transmission, or analyzing the variable's
     * quantum state without interrupting the execution flow.
     *
     * @param mixed $variable The entity/variable to be inspected by the Oracle.
     * @return string The raw, text-based revelation of the variable's structure.
     */
    public static function oracle(mixed $variable): string
    {
        // ---------------------------------------------------------------------
        // PHASE 1: Introspection (Cloning)
        // ---------------------------------------------------------------------
        // We utilize the VarCloner to create a detached, safe snapshot of the
        // variable's current state. This allows us to inspect complex structures
        // (including circular references) without altering the original entity.
        $cloner = new VarCloner();
        $clonedData = $cloner->cloneVar($variable);
        // Cloning VarCloner that led to inspect the variable and create an intermediate, abstract representation of its structure and data (a Data object).
        // This separates the act of introspection from the act of presentation.

        // ---------------------------------------------------------------------
        // PHASE 2: Revelation (Dumping)
        // ---------------------------------------------------------------------
        // We open a direct memory stream ('php://memory') to act as a
        // high-speed buffer for the dumper's output.
        $outputStream = fopen('php://memory', 'r+');
        // We direct its output to a temporary in-memory stream. because 'php://memory' is a writeable stream that stores its data in RAM,
        // making it extremely fast for this kind of operation.

        // Initialize the CliDumper. We strictly use the CLI dumper here to
        // ensure the output is pure text (ASCII/ANSI), free from HTML markup,
        // making it perfect for logs or terminal displays.
        $dumper = new CliDumper($outputStream);

        // The Oracle speaks: writing the structure into the memory stream.
        $dumper->dump($clonedData);
        // This method performs the actual formatting.

        // ---------------------------------------------------------------------
        // PHASE 3: Extraction
        // ---------------------------------------------------------------------
        // Rewind the stream pointer to the beginning to read its contents and the prophecy.
        rewind($outputStream);

        // Extract the entire content of the stream into a string variable.
        $revelation = stream_get_contents($outputStream);

        // Close the dimension (stream) to free up memory and system resources.
        fclose($outputStream);

        // Return the captured truth, pristine string representation.
        return $revelation;
    }

    /**
     * 😱 WAIL: [Emergency] Error Handling with Admin Alert ⚠⚠⚠.
     *
     * A cosmic, soul-shattering cry indicating the system's imminent collapse. This is AmethystMatrix's final, desperate signal.
     *
     * [Use Case]: Reserved for apocalyptic, unrecoverable failures (e.g., total database loss) that render the application unusable and demand immediate, god-level intervention.
     * 
    */
    public static function wail(string $terror, ?\Throwable $e = null, ?array $details = []): void
    {
        if(!self::isLevelActive(__FUNCTION__))
            return;

        $context = self::gatherCosmicContext();
        
        if ($e) {
            $context['exception'] = self::exceptionToArray($e);
        }

        $data = array_merge(
            $context,
            $details
        );

        self::alertAdmins($terror, $data);

        self::log(__FUNCTION__, "🔥 [Amethyst::WAIL] 🔥 :: $terror", $data); // self::log('emergency', ...)       
    }

    // HDX :: Laravel Level Convention
    public static function emergency(string $terror, ?\Throwable $e = null): void
    {
        self::wail($terror, $e);
    }

    /**
     * 😱 SCREAM: [Alert] Error Handling with Admin Alert ⚠⚠⚠.
     *
     * A piercing shriek of agony, signaling a critical system component has been compromised and requires immediate action to prevent further corruption.
     * 
     * [Use Case]: A security breach, a compromised payment gateway, or a failing primary server cluster. Action must be taken NOW..
     *
    */
    public static function scream(string $terror, ?\Throwable $e = null): void
    {
        if(!self::isLevelActive(__FUNCTION__))
            return;

        $context = self::gatherCosmicContext();
        
        if ($e) {
            $context['exception'] = self::exceptionToArray($e);
        }

        self::alertAdmins($terror, $context);

        self::log(__FUNCTION__, "🔥 [Amethyst::SCREAM]: $terror", $context);
    }

    // HDX :: Laravel Level Convention
    public static function alert(string $terror, ?\Throwable $e = null): void
    {
        self::scream($terror, $e);
    }
    
    /**
     * Erros a critical problem. [ERROR]
     *
     * A raw, guttural shout of frustration. A core process has failed unexpectedly, but the system, while wounded, may still be partially operational.
     * 
     * [Use Case]: Unhandled exceptions, failed API calls to critical services, significant runtime errors that impact user experience.
     *
    */
    public static function yell(string $event, Throwable|array|null $e = null): void
    {
        if(!self::isLevelActive(__FUNCTION__))
            return;

        $intelligentContext = self::buildIntelligentContext();

        if($e) {
            if ($e instanceof Throwable) {
                $intelligentContext['exception'] = self::exceptionToArray($e);
            }
            else
                $intelligentContext['details'] = $e;
        }

        self::log(__FUNCTION__, $event, $intelligentContext);
    }
    // HDX :: Laravel Level Convention
    public static function error(string $event, Throwable|array|null $context = null): void
    {
        self::yell($event, $context);
    }
    
    /**
     * Condemns a critical failure or exception. [CRITICAL]
     *
     * A grave, judicial pronouncement. A specific subsystem has violated its contract, leading to a critical but contained failure. The AmethystMatrix judges it unworthy.
     * 
     * [Use Case]: A vital queue worker repeatedly crashing on a specific job, a filesystem becoming unwritable. The issue is severe but isolated.
     *
    */
    public static function condemn(string $event, Throwable|array|null $e = null): void
    {
        if(!self::isLevelActive(__FUNCTION__))
            return;

        $intelligentContext = self::buildIntelligentContext();

        if($e) {
            if ($e instanceof Throwable) {
                $intelligentContext['exception'] = self::exceptionToArray($e);
            }
            else
                $intelligentContext['details'] = $e;
        }

        self::log(__FUNCTION__, $event, $intelligentContext);
    }
    // HDX :: Laravel Level Convention
    public static function critical(string $event, Throwable|array $context): void
    {
        self::condemn($event, $context);
    }

    /**
     * Records a prophecy of a future action (e.g., a job dispatch). [INFO]
     *
     * An oracular vision of a potential dark future. The AmethystMatrix foresees a non-fatal issue that, if ignored, will inevitably lead to greater errors.
     * 
     * [Use Case]: Use of deprecated APIs, approaching storage limits, potential logical flaws that haven't yet triggered a full-blown error. Listen to the prophecy.
     *
    */
    public static function prophesy(string $event, array $data = []): void
    {
        if(!self::isLevelActive(__FUNCTION__))
            return;

        self::log(__FUNCTION__, $event, $data);
    }
    // HDX :: Laravel Level Convention
    public static function warning(string $event, array $data = []): void
    {        
        self::prophesy($event, $data);
    }

    /**
     * 🔮 GAZE: Deep Inspection (The 'dd' equivalent without dying).
     * Dumps the variable to the log using his oracle() powers, and adds a special visual marker.
     *
     * The deep, focused stare of AmethystMatrix 🔮, noting an event of significance. Not an error, but a noteworthy milestone in the system's operational saga.
     * 
     * [Use Case]: A user achieving a new high-score, a large financial transaction being successfully processed, a new server joining the cluster. Events that warrant a second look.
     *
    */
    public static function gaze(mixed $anomaly, string $label = 'Anomaly'): void
    {
        if(!self::isLevelActive(__FUNCTION__))
            return;

        Log::channel(self::$config['channel'])->info(
            '#QueenDailyGazes' . PHP_EOL .
            str_repeat('=', 20) . " 🔮 GAZE: $label " . str_repeat('=', 20) . PHP_EOL .
            self::oracle($anomaly) . PHP_EOL .
            str_repeat('=', 50)
        );

        Log::channel(self::$config['channel'])->debug('#QueenDailyGazes', [
            'context' => self::$messageContext,
            'subject' => $anomaly
        ]);

        // A deep, focused look at a potential issue or important state. [NOTICE]
        self::log(__FUNCTION__, "[Amethyst]: 🔮 GAZE Requested ::", []); // $anomaly
    }
    // HDX :: Laravel Level Convention
    public static function notice(mixed $anomaly, string $label = 'Anomaly'): void
    {
        self::gaze($anomaly, $label);
    }

    /**
     * 👁️ OBSERVE: The standard logging mechanism.
     * Uses semantic structured logging.
     *
     * The passive, ambient awareness of AmethystMatrix. It observes the normal, rhythmic pulse of the application's lifeblood.
     * 
     * [Use Case]: User logins, API endpoints being hit, scheduled tasks starting and finishing. The general, healthy heartbeat of the system.
     *
    */
    public static function observe(string $signal, mixed ...$fragments): void
    {
        if(!self::isLevelActive(__FUNCTION__))
            return;

        $context = self::gatherCosmicContext();
        
        // Merge extra fragments into context
        foreach ($fragments as $index => $fragment) {
            $key = is_array($fragment) ? 'data' : "fragment_{$index}";
            $context[$key] = $fragment;
        }

        // General observation of an event. [INFO]
        self::log(__FUNCTION__, $signal, $context);

        // Log::info("🔮 [Amethyst]: $signal", $context);
    }
    // HDX :: Laravel Level Convention
    public static function info(string $signal, mixed ...$fragments): void
    {
        self::observe($signal, ...$fragments);
    }

    /**
     * 🌬️ WHISPER: Low-level debug trace.
     *
     *  The intimate, internal monologue of AmethystMatrix's deepest thoughts. For the developer who leans in close to understand its very essence.
     * 
     * [Use Case]: Highly verbose, step-by-step tracing of algorithms, variable state dumps, granular I/O operations. The language of pure creation and debugging.
     *
    */
    public static function whisper(string $secret, array $details = []): void
    {
        if(!self::isLevelActive(__FUNCTION__))
            return;

        $data = array_merge(
            self::gatherCosmicContext(),
            $details
        );

        // A silent whisper for deep debugging. [DEBUG]
        self::log(__FUNCTION__, $secret, $data);
        
        // Log::debug("💜 [Amethyst::Whisper]: $secret", $data);
    }
    // Laravel Convention
    public static function debug(string $secret, array $details = []): void
    {
        self::whisper($secret, $details);
    }

    /**
     * 🧠 REMEMBER: Short-term memory Smart Caching (Cache wrapper).
     * If inside a request, it tries to use Context. If generic, uses Redis/File Cache.
     *
     *  A foundational incantation that grants AmethystMatrix the power of memory, enabling it to access and persist states via the cache layer.
     * 
     * [Use Case]: Enables/disables AmethystMatrix's ability to use caching for configurations, results, or any form of persistent memory. Disabling this forces AmethystMatrix into a state of perpetual rediscovery.
     *
    */
    public static function vault(string $key, mixed $thought, int $seconds = 3600): void
    {
        if(!self::isLevelActive(__FUNCTION__))
            return;

        // 1. Try Context (Request Scope) - Hyper Fast
        if ($bot = self::bot()) {
             // We use the 'InteractsWithContext' trait methods if available
             if (method_exists($bot, 'setData')) {
                 $bot->setData("amethyst_mem_$key", $thought);
             }
        }

        // 2. Persist to Global Cache (App Scope) Prefixes with 'amethyst:'
        Cache::put("amethyst:$key", $thought, $seconds);

        // Commits a significant event to memory. [NOTICE]
        self::log(__FUNCTION__, $thought, [
            'cache_key' => "amethyst:$key",
            'data_key'  => "amethyst_mem_$key",
            'duration'  => $seconds,
            'value'     => $thought
        ]);
    }
    // HDX :: Laravel Level Convention
    public static function remember(string $key, mixed $thought, int $seconds = 3600): void
    {
        self::vault($key, $thought, $seconds);
    }
    public static function save(string $key, mixed $thought, int $seconds = 3600): void
    {
        self::vault($key, $thought, $seconds);
    }
    /**
     * Alias for setData (UniChatKit 'set' compatibility).
    */
    public static function set(string $key, mixed $thought, int $seconds = 3600): void
    {
        self::vault($key, $thought, $seconds);
    }

    /**
     * 🕯️ RECALL: Retrieve a memory.
    */
    public static function recall(string $key, mixed $default = null): mixed
    {
        // 1. Check Context
        if ($bot = self::bot()) {
            // We use the 'InteractsWithContext' trait methods if available
            if (method_exists($bot, 'getData')) {
                $val = $bot->getData("amethyst_mem_$key", $default);
                if ($val !== $default) return $val;
            }
        }

        // 2. Check Global Cache
        return Cache::get("amethyst:$key", $default);
    }
    /**
     * Alias for getData (UniChatKit 'get' compatibility).
    */
    public static function get(string $key, mixed $default = null): mixed
    {
        return self::recall($key, $default);
    }

    /**
     * [POWER-UP] Retrieves a value from her vault and then removes it.
     * The ultimate tool for "flash messages" or single-use tokens.
    */
    public static function pullData(string $key, mixed $default = null): mixed
    {
        $value = self::recall($key, $default);
        self::forgetData($key);
        return $value;
    }
    public static function pull(string $key, mixed $default = null): mixed { return self::pullData($key, $default); }

    /**
     * [POWER-UP] Removes one or more items from the her vault by key.
    */
    public static function forgetData(string|array $keys): void
    {
        foreach ((array) $keys as $key) {
            Cache::forget("amethyst:$key");
        }
    }
    public static function forget(string|array $keys): void { self::forgetData($keys); }

    // --- INTERNAL MAGICZ ---

    /**
     * Try to retrieve the living Krubot instance from the weak reference.
     */
    protected static function bot(): ?Krubot
    {
        return self::$warlordRef?->get();
    }

    /**
     * Checks if Amethyst is enabled and if the specific logging level is active.
    */
    protected static function isLevelActive(string $levelAlias = '__') : bool
    {
        // return ((!empty(self::$config)) && self::$config['enabled'] && in_array($levelAlias, self::$config['active_spells'] ?? [])); // one-linear magic removed due to weaponize it to $laravelLogLevel

        // REFACTORED & CORRECTED: This logic is now correct and robust.
        if (empty(self::$config)) {
             self::$config = config('krubot.amethyst', []); // Lazy load config
        }

        if(empty(self::$config))
            return false;

        $config = self::$config;

        // Master switch check
        if(!$config['enabled'])
            return false;

        // if (!config('app.debug')) // disable entire system according to laravel state... ? it's up to you.
            // return false;

        // Sar -E- Gardaneh!
        if(! self::refresh()) // $isAwake can't be true, krubot not loaded yet.
            return false;

        // Master switch bypass for internal matrix operations
        if($levelAlias == '__')
            return true;

        $activeSpells = self::$config['active_spells'] ?? [];

        // Checks that Input is a valid spell name / thematic alias (e.g., 'scream') and it's in the active list.
        if(in_array($levelAlias, $activeSpells, true))
            return true;

        // PSR-3 Resolution: Want to Check For 'alert' is Active?
        // Find its key 'scream'.
        // We search for the PSR-3 value in the map to get the Spell key.
        $spellKey = array_search($levelAlias, self::$logLevelMap, true); // 'alert' ==> 'scream'

        // Check if the found Spell key is active
        return $spellKey && in_array($spellKey, $activeSpells, true);
        
        /*
         $laravelLogLevel = self::$logLevelMap[$levelAlias] ?? 'unknown_log_level'; // BUG: if we put 'unknown_log_level', entire log-leveling system will gone f..un :D

        // Check if laravel-style alias itself is in the active_spells array.
        return in_array($laravelLogLevel, $activeSpells);

        /* if(in_array($laravelLogLevel, $activeSpells))
            return true;
        return false; */
    }

    /**
     * The core of her consciousness. All observations pass through here.
     */
    protected static function log(string $levelAlias, string $message, array $context = []): void
    {
        if(!self::isLevelActive($levelAlias))
            return;

        // --- The Alchemical Logic Core ---
        $finalPsrLevel = null;

        // Path 1: Is the alias one of narrative keys? (e.g., 'scream')
        // This is the most direct and efficient check.
        if (array_key_exists($levelAlias, self::$logLevelMap)) {
            $finalPsrLevel = self::$logLevelMap[$levelAlias];
        }
        // Path 2: If not, is the alias a valid PSR-3 level that EXISTS as a VALUE in your map? (e.g., 'alert')
        // This provides the desired flexibility and compatibility.
        elseif (in_array($levelAlias, self::$logLevelMap, true)) {
            // The alias IS the PSR-3 level we need. No translation required.
            $finalPsrLevel = $levelAlias;
        }

        // --- Execution ---

        if ($finalPsrLevel) {
            $payload = self::buildIntelligentContext();
            // User-provided data has higher priority and will overwrite auto-generated context.
            $finalContext = array_merge($payload, $context);

            Log::channel(self::$config['channel'])->{$finalPsrLevel}($message, $finalContext);
        } else {
            // Gracefully handle any completely unknown alias.
            self::warning("AmethystMatrix: Unrecognized log level alias '{$levelAlias}' was used and could not be interpreted.", [
                'attempted_alias' => $levelAlias
            ]);
        }
    }

    /**
     * ✍️ WRITE: Force Write Log Data, And Can't be Toggled in config file.
     *
     * Force-writes a log entry, bypassing the enabled/disabled toggle.
     * This is for critical, non-negotiable logging.
     */
    public static function write(string $message, array $details = []): void
    {
        // if(!self::isLevelActive(__FUNCTION__)) // write() can't be disabled in config !!
        //    return;

        // Sar -E- Gardaneh!
        if(! self::refresh()) // we only check this in write(), because write() wont give premission from  self::isLevelActive      
            return;

        $data = array_merge(
            self::gatherCosmicContext(),
            $details
        );

        Log::channel(self::$config['channel'])->warning('AmethystMatrix::write() Order !! ::: ' .$message, $data);
    }

    protected static function alertAdmins(string $terror, array $context): void
    {
        if(empty(self::$config))
            return;

        // Master Alert-Switch Check
        if(!self::$config['alert_admins_after_critical'])
            return;

        // 🧠 ADMIN TELEPATHY: If a bot instance is alive, notify the Admin immediately.
        if ($bot = self::bot()) {
            // Optional: Telepathic alert to admin via the bot instance if available
            try {
                $bot->sendMessageToAdmins("🔥 **AMETHYST ALERT** 🔥\n\nError: {$terror}\nContext: {$context['request_id']}\n\nPayload:\n" . self::oracle($context));
            } catch (\Throwable $t) {
                // Should not happen, but silence to avoid loop.
            }
        }
    }

    protected static function exceptionToArray(?\Throwable $e = null): array
    {
        if(!$e)
            return []; // Works Safely

        return [
            'msg'   => $e->getMessage(),
            'file'  => $e->getFile(),
            'line'  => $e->getLine(),
            'error' => $e?->getMessage() ?? 'Unknown',
            'trace' => $e->getTraceAsString() ?? null,
        ];
    }

    /**
     * Gathers metadata about the current state (User, Chat, Request ID).
     *
     * Gathers ambient battlefield intelligence automatically.
     */
    protected static function buildIntelligentContext(): array
    {
        $bot = self::bot();

        // if (!$bot || !self::$messageContext) {
        if (!$bot) {
            return [];
        }

        $meta = [
            'request_id' => Str::uuid()->toString(), // Or fetch from Laravel Request
            'timestamp' => now()->toIso8601String(),
        ];

        $options = self::$config['report_context'] ?? [];

        if ($options['driver'] ?? false) $meta['driver'] = $bot->getDriverAlias();
        if ($options['user_id'] ?? false) $meta['user_id'] = $bot->senderId();
        if ($workingMessage = self::getWorkingMessage()) {
            if ($options['chat_id'] ?? false) $meta['chat_id'] = $workingMessage->chat_id ?? $bot->chatId();
            if ($options['sender_id'] ?? false) $meta['sender_id'] = $workingMessage->sender_id;
            if ($options['message_id'] ?? false) $meta['message_id'] = $workingMessage->message_id;
            if ($options['message_text'] ?? false) $meta['text'] = Str::limit($workingMessage->text, ($options['message_text_limit'] ?? 150));
        }
        
        $resolvedRoute = $bot->currentResolvedHandler();
        if ($resolvedRoute) {
            if (($options['route_name'] ?? false) && $resolvedRoute->getName()) {
                $meta['route_name'] = $resolvedRoute->getName();
            }
            if ($options['route_params'] ?? false) {
                 $params = $bot->currentParameters();
                 if(!empty($params))
                    $meta['route_params'] = $params;
            }
            if ($options['route_pattern'] ?? false)
                $meta['route_pattern'] = $resolvedRoute->getPattern();
        }
        
        return $meta;
    }

    protected static function gatherCosmicContext(): array
    {
        return self::buildIntelligentContext();
    }
}
