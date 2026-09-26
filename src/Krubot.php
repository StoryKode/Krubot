<?php

namespace KrubiK;

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
| What you see here is the **×ReleaseCandiate v0.9×** release. Why release it now?
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

use KrubiK\DTOs\Message;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use KrubiK\Middlewares\ConversationMiddleware; // Inject Conversations ⚡
use KrubiK\WarLording\CommandOutcomeShifter;   // Pairs with `ResultWrapper`
use KrubiK\Routing\Route; // Import Route Class ⚡
use KrubiK\Drivers\Contracts\MultiverseEnforcer;
use KrubiK\Jobs\HandleDriverUpdate;
use KrubiK\DTOs\UniversalInboundUpdate;
use KrubiK\Arcane\Update; // Update-Marker to be catched in Receive(Singal::***)
use KrubiK\Render\RichMan;
use KrubiK\Render\RenderAura;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionUnionType;
use RuntimeException;
use Throwable;
use Countable;
use Closure;
use Traversable;

use KrubiK\Helpers\AmethystMatrix; // ⚡ Import the Sorceress

use KrubiK\WebApps\DTOs\WebRequest; // ⚡ Our Sacred WebRequest HyperDTO

use KrubiK\Helpers\JackPoint; // Import "JackPoint" - The Tactical EventHook System
use KrubiK\Facades\Opcache;   // ✨ OpCaching came into the game ✨

use KrubiK\Enums\Platform;
use KrubiK\Enums\Signal;
use KrubiK\Attributes\RuleSet; // others delegated to EpicEngine

use KrubiK\Routing\EpicEngine as XRouteR;
use KrubiK\Arcane\{
    InteractsWithContext, // ⚡ Import Context
    InteractsWithApi,
    HasWebInterface,
    VanguardBuilder,
    HasAmethystMatrix,
    ProfessionalWarLordingToolkit,
    SummonsCodeSpyz,
    ResilienceKit,        // provides `resilientRun()`, `resilientLog()`, `resilientIoC()`
    ResultWrapper,        // provides ES-Like `then()`,`catch()`,`finally()`,`throw()` to API-Operations
    HasKeyboards,
    CanSendFluentMessages,
    CanPin,
    CanManageChats,
    CanManageMembers,
    CanInitConversations,
    CanPlayDiceGames,
    PHPRBK_Methods
};

use ReturnTypeWillChange;

/**
 * Krubot: The Double-Miracler Edition ×release-candidate_0.9× (vObsidian-8)
 *
 * A Multi-Platform Orchestrator. This class does not contain any platform-specific API logic yet...
 * But It acts as a router and a proxy, delegating all platform
 * interactions to the appropriate driver.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version self: ×RC.9×
 * @music https://soundcloud.com/boombastixmusic/infected-mushroom-cities-of-the-future-boombastix-spiderage-remix-extended 🎧
 * @license MIT
*/
class Krubot implements Countable // ⚡️✅️⚡️
{
    use Macroable {
        __call as macroCall; // ⚡ Utilizing PHP+Laravel Power: Add methods dynamically at runtime ⚡
    }

    use
    XRouteR,
    HasWebInterface, // Empower Krubot to response & handle Mini-Apps / Web-Apps / Websites
    VanguardBuilder,
    InteractsWithApi,
    SummonsCodeSpyz,
    ResilienceKit,
    ResultWrapper,
    HasKeyboards,
    CanManageChats,
    CanManageMembers,
    CanInitConversations,
    CanSendFluentMessages,
    InteractsWithContext,
    CanPlayDiceGames,
    PHPRBK_Methods,
    CanPin,
    ProfessionalWarLordingToolkit, // Injects core(), prime(), driver(), via(), etc.
    HasAmethystMatrix; // Inject Amethyst Powers ⚡

    /** @var Route[] */
    protected array $routes = []; // Changed to store Route objects
    
    /** @var callable|array|null The global fallback handler if no routes match. */
    protected mixed $fallbackHandler = null;

    /**
     * @var array<string, callable|array> Holds type-specific fallback handlers.
     * e.g., ['video' => [VideoController::class, 'handleFallback']]
    */
    protected array $typeFallbackHandlers = [];

    /**
     * @var array<string, array<int, callable|array>> A temporary registry during nexus integration.
     * e.g., ['video' => [10 => handlerA, 0 => handlerB]]
    */
    protected array $fallbackRegistry = [];
    
    // Routing signal types
    protected const RT_ACTION     = 'action';
    protected const RT_TEXT       = 'text';
    protected const RT_REGEX      = 'regex';
    protected const RT_COMMAND    = 'cmd';
    protected const RT_SIGNAL     = 'signal';  // ✨ NEW {[=__=]}
    protected const RT_INLINE     = 'inline';  // ✨ NEW

    protected const RT_WEB        = 'web';        // ✨ NEW
    protected const RT_WEB_APP    = 'web_app';    // ✨ NEW
    protected const RT_WEB_PAGE   = 'web_page';   // ✨ NEW
    protected const RT_WEB_ACTION = 'web_action'; // ✨ NEW

    protected const RT_NONE    = 'none';

    // The constants RT_WEB_APP_DATA is now deprecated and removed
    // as its logic has been unified into the system differently. (Merged into RT_WEB_ACTION)

    // ⚡ AUTO-LOAD: میدل‌ور مکالمه به صورت پیش‌فرض در اینجا تعریف می‌شود
    // The hardcoded value is removed. It will be loaded from config via constructor.
    protected array $globalMiddlewares = [];

    protected ?Message $currentMessage = null;

    /**
     * Holds the parameters of the currently executing handler.
     * Accessible via currentParameters().
    */
    protected array $currentRouteParams = [];
    
    /**
     * Holds the currently resolved Route object.
    */
    protected ?Route $activeRoute = null;

    /**
     * ⚡ Middleware Aliases Map
     * Allows using short strings like 'auth' instead of full class names.
     * Effective in both Laravel (if registered) and Native PHP modes.
    */
    protected array $middlewareAliases = [];

    /**
     * Stores named routes for O(1) lookup.
     * ['dashboard' => RouteObject, 'login' => RouteObject]
    */
    protected array $namedRoutes = [];

    /**
     * The underlying bot driver (e.g., RubikaDriver, TelegramDriver).
     * @var MultiverseEnforcer
    */
    protected ?MultiverseEnforcer $driver = null;

    /**
     * The Laravel application instance.
     * @var Application
    */
    protected Application $app;

    /**
     * Cache registry of booted Nexus synapses to guarantee idempotency and store registered hook tokens.
     *
     * Format:
     *  - FQCN => true  (when `SomeNexus::synapses()` returned void/completed)
     *  - FQCN => array (when `SomeNexus::synapses()` returned listener IDs/hook tokens)
     *
     * @var array<class-string, bool|array<int|string, mixed>>
    */
    protected array $injectedSynapses = [];
    
    /**
     * The armory of active, instantiated driver instances.
     * @var array<string, MultiverseEnforcer>
    */
    // protected array $drivers = [];

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
    // protected array $driverAliases = []; // 🧹 Clean Slate: No hardcoded values.

    /**
     * Named validation RuleSets available to this Krubot instance.
     *
     * RuleSets are intentionally detached from Routes.
     * They are resolved lazily when a #[Validate(...)] rule is judged.
     *
     * @var array<string, array<int, mixed>>
    */
    protected array $ruleSets = [];

    // Properties for Krubot's core fluent builder
    protected string|RichMan|null $text = null;
    protected ?string $chatId = null;
    protected ?string $replyToMessageId = null;

    /**
     * ⚙️ THE MISSING LINK: Configuration Repository
     * Global configuration passed during instantiation.
     *
     * This property holds the entire configuration array (e.g., contents of krubot.php).
     *
     * @var array
    */
    protected array $pwl_config = [];

    // =========================================================================
    //  🧠 THE SINGULARITY CACHE SYSTEM (O(1) Reflection Manifest)
    // =========================================================================

    /**
     * @var array<string, array{class_attributes: array, methods: array}>
     */
    protected static array $reflectionManifestCache = [];

    /**
     * Scans a class ONCE per application lifecycle. 
     * Extracts ALL class-level and method-level attributes into a highly optimized, 
     * statically cached array. Zero repetitive reflection!
     * 
     * @param class-string $className
     * @return array
     */
    protected function getAttributeManifest(string $className): array
    {
        // ⚡ Cache Hit: O(1) Return. Never reflect the same class twice!
        if (isset(self::$reflectionManifestCache[$className])) {
            return JackPoint::transform('nexus.manifest.cached', self::$reflectionManifestCache[$className], $className);
        }

        $manifest = [
            'class_attributes' => [],
            'methods' => [] // Format: ['methodName' => ['AttributeClass' => [Instance1, Instance2]]]
        ];

        try {
            $reflection = new ReflectionClass($className);

            // 1. Cache Class-Level Attributes
            foreach ($reflection->getAttributes() as $attr) {
                $manifest['class_attributes'][$attr->getName()][] = $attr->newInstance();
            }

            // 2. Cache Method-Level Attributes (Public only)
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $methodAttrs = $method->getAttributes();
                if (empty($methodAttrs)) continue;

                $methodName = $method->getName();
                foreach ($methodAttrs as $attr) {
                    // Supports IS_REPEATABLE by pushing to array
                    $manifest['methods'][$methodName][$attr->getName()][] = $attr->newInstance();
                }
            }

            // Store in static memory for the rest of the execution
            self::$reflectionManifestCache[$className] = $manifest;

        } catch (\ReflectionException $e) {
            AmethystMatrix::error("Manifest Engine: Failed to reflect [{$className}]", ['error' => $e->getMessage()]);
        }

        return JackPoint::transform('nexus.manifest.built', $manifest, $className);
    }

    public function __construct(Application $app, ?MultiverseEnforcer $driver = null, string|array $config = null)
    {
        JackPoint::fire('krubot.awaken.before', $this);

        $this->app = $app;
        $this->driver = $driver;

        // 1.1 Normalize Configuration
        if ($config && is_string($config)) {
            // Legacy support: if only a token is passed, build a basic Rubika config.
            $config = [
                'drivers' => [
                    'rubika' => ['authtoken' => $config]
                ]
            ];
        }

        // 1.2 Store the entire configuration array.
        // This is crucial for lazy-loading drivers later via createDriver().
        $this->pwl_config = $config ?? $this->app['config']->get('krubot', []);

        $this->pwl_config = JackPoint::transform('krubot.boot.config', $this->pwl_config, $this);

        // @Todo: Refine me !
        // 2. Set default driver if specified in config, otherwise it defaults to 'rubika'.
        if (isset($this->pwl_config['default_driver'])) {
            $this->setDefaultDriver($this->pwl_config['default_driver']);
        }

        // 2. Call the legion formation method from the ProfessionalWarlordingToolkit.
        // It will safely access the 'legions' key, defaulting to an empty array if not present.
        if(method_exists($this, 'formLegionsFromConfig'))
            $this->formLegionsFromConfig($this->pwl_config['legions'] ?? []);
        
        // 3. Load middleware configuration
        // $this->globalMiddlewares = $this->pwl_config['middlewares']['globals'] ?? [];
        // $this->middlewareAliases = $this->pwl_config['middlewares']['aliases'] ?? [];

        // Extract the entire 'middlewares' array from the config, with an empty array as a fallback.
        $middlewareConfig = $this->pwl_config['middlewares'] ?? [];

        // [THE CORE CHANGE]
        // We read the 'middlewares.global' key from the provided config array.
        // If it doesn't exist, we fall back to an array containing only the
        // essential ConversationMiddleware as a safety measure.
        $this->globalMiddlewares = $middlewareConfig['globals'] ?? [
            ConversationMiddleware::class
        ];

        // 2. اگر در کانفیگ مقدار نبود → از پیش‌فرض‌های داخلی استفاده کن
        $this->middlewareAliases = $middlewareConfig['aliases'] ?? [
            'auth'     => \App\Http\Middleware\Authenticate::class,
            'admin'    => \App\Http\Middleware\AdminCheck::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ];

        // 🔮 WAKE THE SORCERESS
        // This links the current instance to the static helper.
        $this->awakenAmethystMatrix();
        
        // 🔥 EVENT: krubot.constructed — plugins may now attach listeners.
        JackPoint::fireKrubotAwaken($this, $this->pwl_config);

        JackPoint::fire('krubot.awaken.after', $this, $this->pwl_config);
    }
    
    /*
     *
     * Destructor to ensure we break the link in long-running processes.
     *
   */
    public function __destruct()
    {
    
        // 🔥 EVENT: krubot.destructing — last chance to flush, save, or disconnect.
        JackPoint::fireKrubotSleeping($this);

        $this->pwl_config = JackPoint::transform('krubot.shutdown.config', $this->pwl_config, $this);

        $this->sleepAmethystMatrix();

        JackPoint::fireKrubotSlept($this);
        
    }

    /**
     * 👁️ SENSORY ENGINE: Detects the true nature of the incoming message.
     * This method now acts as a high-level accessor to the powerful Signal::detect() engine.
     * It retrieves the current message context and delegates the detection logic,
     * ensuring perfect consistency with the application-wide Signal standard.
     * Supports multi-platform payloads (Telegram & Rubika standard DTOs).
     * 
     * @param  Message|null $message Optional message object to analyze. Defaults to the current message.
     * @param  bool   $prioritizeEnvelopeDetection If true, the envelope type (e.g., Revision, Callback)
     *                                           will be returned before checking the message content.
     *                                           Default is false (content-first).
     * @return string Returns a Signal constant (e.g., Signal::Photo, Signal::Command, Signal::Void).
    */
    public function detectMessageType(?Message $message = null, bool $prioritizeEnvelopeDetection = false): string
    {
        $msg = $message ?? $this->thisMessage();

        // If there's no message context, return the void signal.
        if (!$msg) {
            return Signal::Void;
        }

        // ⚡ Delegate the entire detection logic to the centralized, optimized,
        // and architecturally-sound Signal::detect() method.
        $detected = Signal::detect($msg, $prioritizeEnvelopeDetection);

        return JackPoint::transformSignalDetect(
            $detected,
            $msg,
            $prioritizeEnvelopeDetection,
            $this
        );
    }

    /**
     * =========================================================================
     *  ⚡️ THE WARLORD'S SUPREME EDICT PROXY (v5.0 - UNIFIED) ⚡️
     * =========================================================================
     *
     * This is the absolute heart of the Krubot Orchestrator. It's the ultimate
     * magic proxy that intelligently routes any non-existent method call based
     * on the strategic context established by the Warlord Toolkits.
     *
     * It masterfully balances forward-looking architecture with backward
     * compatibility, creating a seamless developer experience.
     *
     * ⚔️ HIERARCHY OF COMMAND (EXECUTION PRIORITY):
     * 1.  **MACROABLE COMMANDS:** Checks for runtime-defined methods via Laravel's `Macroable` trait.
     * 2.  **`via()` DIRECTIVES (The Active Campaign):** Intercepts calls when a temporary driver
     *     is selected via `$bot->via(...)`. It distinguishes between single and multi-driver campaigns.
     * 3.  **DEFAULT DELEGATION (The Standing Army):** If no other context is active, the call is
     *     delegated to the configured default driver (e.g., 'rubika').
     *
     * 🔮 RETURN TYPE STRATEGY (THE CORE ARCHITECTURE):
     * - **Single-Target Calls:** (e.g., `$bot->getMe()`, `$bot->via('tg')->getMe()`)
     *   Returns a `CommandOutcomeShifter` object, enabling the powerful "Overlord's Gaze"
     *   chaining strategy via `->then()`. Victory continues the chain; defeat halts it silently.
     *
     * - **Multi-Target Calls (Multi-Cast):** (e.g., `$bot->via(['r', 'b'])->reply(...)`)
     *   Returns a raw `array` of results or exceptions, keyed by driver alias.
     *   This maintains backward compatibility with the "Supreme Commander" edition and provides
     *   a simple, direct report for broadcast operations. This mode does NOT support `->then()` chaining.
     *   For advanced multi-platform orchestration, `assembleCouncil()` is the designated tool.
     *
     * @param string $method The method name being invoked (e.g., 'getMe', 'reply').
     * @param array  $parameters The arguments passed to the method.
     *
     * @return CommandOutcomeShifter|array|mixed The result, wrapped in `CommandOutcomeShifter` for single targets,
     *                                    or a raw array for multi-target campaigns.
    */
    public function __call($method, $parameters)
    {
        // =====================================================================
        // PRIORITY 1: MACROABLE COMMANDS (Laravel's Dynamic Power)
        // =====================================================================
        // First, we honor any runtime extensions added via `Krubot::macro()`.
        if (static::hasMacro($method)) {
            // Execute the macro.
            $result = $this->macroCall($method, $parameters);

            // For consistency with the new architecture, we wrap the macro's result
            // in a CommandOutcomeShifter object. This makes macros chainable with `->then()` too.
            return $this->wrapIfNeeded($result);
        }

        // =====================================================================
        // PRIORITY 2: `via()` DIRECTIVES (The Active Tactical Campaign)
        //   Warlord's `via()` Protocol (One-time override)
        // =====================================================================
        // Check if a temporary driver context has been set by a preceding `via()` call.
        if ($this->onetimeDriverAlias !== null) {
            // Immediately capture and consume the state to prevent it from affecting subsequent, unrelated calls.
            // This is a critical step for maintaining a stateless, predictable fluent interface.
            $aliases = (array) $this->onetimeDriverAlias;
            $this->onetimeDriverAlias = null;

            // --- STRATEGIC FORK: SINGLE-STRIKE vs. MULTI-CAST ---

            // A) MULTI-CAST CAMPAIGN (`via(['r', 'b'])`)
            if (count($aliases) > 1) {
                $results = [];

                // Execute the command on all targeted drivers in the campaign.
                 // Multi-cast assault -> Return array of outcomes (BC preserved)
                foreach ($aliases as $alias) {
                    try {
                        // Resolve and command the driver.
                        $driver = $this->core($alias);
                        $results[$alias] = $driver->{$method}(...$parameters);
                    } catch (\Throwable $e) {
                        // Professional Error Handling: Instead of crashing, we record the failure
                        // in our battle report. The campaign continues with other drivers.
                        $results[$alias] = $e;
                    }
                }

                // BACKWARD COMPATIBILITY GUARANTEE:
                // For multi-cast, return the raw array of results. This preserves the behavior
                // of the "Supreme Commander" edition and avoids breaking changes.
                return $results;
            }

            // B) SINGLE-STRIKE MISSION (`via('tg')`)
            // If there's only one alias, we proceed with the "Overlord's Gaze" strategy.

            // Single, surgical strike -> Return CommandOutcomeShifter for ->then() chaining
            if (count($aliases) === 1) {
                $driver = $this->core(reset($aliases));
                $result = $driver->{$method}(...$parameters);
                return $this->wrapIfNeeded($result); /// CommandOutcomeShifter::execute($this, $result_maker)
            }
            /*try {
                $alias = $aliases[0];
                $result = $this->core($alias)->{$method}(...$parameters);
            } catch (\Throwable $e) {
                // The mission resulted in failure. Capture the exception as the outcome.
                $result = $e;
            }*/
        }

        // =====================================================================
        // PRIORITY 3: DEFAULT DELEGATION (Standard Operating Procedure)
        // =====================================================================
        // If no special context is active, the command is delegated to the default driver.
        // This is the most common execution path.
        try {
            // `core()` without arguments returns the default driver instance.
            $result = $this->core()->{$method}(...$parameters);
        } catch (\Throwable $e) {
            // Even standard operations can fail. We handle it gracefully.
            $result = $e;
        }

        // Wrap the result in a CommandOutcomeShifter, making every standard call chainable.
        // All calls to the default driver are wrapped in CommandOutcomeShifter to enable `->then()` chaining.
        return $this->wrapIfNeeded($result);
    }

    /**
     * ⚡️ THE STATIC COMMAND SPIRE ⚡️
     *
     * The primary static entry point for issuing commands without an instance.
     * It resolves the master Krubot instance from the Laravel container and
     * immediately primes it to use the specified driver for the next action.
     *
     * This provides a beautiful, fluent, and powerful facade-like experience.
     *
     * @param string|array $driverAlias The target driver alias(es).
     * @return static The primed Krubot instance, ready for command chaining.
    */
    public static function you(string|array $driverAlias): static
    {
        // 1. Resolve the singleton from the container.
        // 2. Call the `via()` method to set the target driver.
        // `static` ensures it returns an instance of `Krubot` (or a child class).
        return resolve(static::class)->via($driverAlias);
    }

    // =========================================================================
    // ⚡ ATTRIBUTE REGISTRATION SYSTEM - The Ultimate Reflection Engine ⚡
    //  🌌 NEXUS INTEGRATION SYSTEM (v16.0 - The Singularity Engine)
    // =========================================================================

    /**
     * @var array<string, true> Tracks already integrated Nexuses using a hash map for O(1) lookups.
     * A map of registered Nexuses for O(1) lookups.
     * ['Fully\Qualified\ClassName' => true]
     * 
     * @var array Tracks already integrated Nexuses to prevent double registration.
     * @var array Tracks already integrated Nexuses.
    */
    protected array $integratedNexuses = [];
    /**
     * Returns the list of all fully qualified class names of the Nexuses
     * that have been integrated into this bot instance.
     *
     * @return string[]
    */
    public function getIntegratedNexuses(): array
    {
        return array_keys($this->integratedNexuses);
    }

    // DYNAMIC NEXUS MANAGEMENT API (Galactic Edition) +++

    /**
     * [The Great Purge] Clears all registered routes, named routes, and integrated Nexuses.
     * @return $this
    */
    public function clearNexuses(): self
    {
        $this->routes = []; // Purge all registered route patterns and handlers.
        $this->namedRoutes = []; // Purge all named route references.
        $this->integratedNexuses = []; // Reset the tracking list of integrated Nexuses.
        $this->ruleSets = [];
        $this->injectedSynapses = [];

        JackPoint::fire('nexus.cleared');

        return $this;
    }

    /**
     * Sets one or more Nexuses, optionally replacing all existing ones.
     * This is the primary method for dynamically re-wiring the bot's logic at runtime.
     *
     * @param array<int, string|object> $nexuses An array of Nexus class names or instances.
     * @param bool $replace If true, all previously integrated Nexuses will be cleared first.
     * @return $this
    */
    public function setNexuses(array $nexuses, bool $replace = true): self
    {
        if ($replace) {
            $this->clearNexuses();
        }
        foreach ($nexuses as $nexus) {
            $this->integrateNexus($nexus, false);
        }
        // After all nexuses have been scanned, resolve the priorities for each types.
        $this->prioritizeFallbacks();
        return $this;
    }

    /**
     * Adds a single new Nexus to the existing integrated Nexuses.
     * This provides a fluent interface for incrementally adding logic.
     *
     * @param string|object $nexus The Nexus class name or instance to add.
     * @return $this
    */
    public function addNexus(string|object $nexus): self
    {
        $this->integrateNexus($nexus);
        return $this;
    }

    /**
     * Static RAM Cache for Manifest Data (O(1) Singularity Engine).
     * @var array<string, array>
     */
    protected static array $nexusManifestCache = [];

    /**
     *                  🚀 HYPER NEXUS LOADER
     * 🚀 THE SENTINEL ENGINE (v10.0): The Definitive Nexus Auto-Loader.
     * Automatically registers all Nexus classes in a directory.
     *
     * This ultimate method scans a directory recursively, parsing each PHP file to
     * reliably extract its Fully Qualified Class Name (FQCN) using PHP's native
     * tokenizer. It then immediately integrates the discovered Nexus into the bot's core.
     *
     * It completely supersedes older, fragile methods that relied on PSR-4 path guessing.
     * This engine trusts only the code itself, making it 100% reliable regardless of
     * file structure or namespace conventions.
     *
     * Best used within a Service Provider's `boot` method to automatically
     * discover and activate all Nexus modules at once.
     *
     * @param string $directory The absolute path to the directory containing Nexus classes.
     * @return int The total number of Nexuses that were successfully discovered and integrated.
    */
    public function discoverAndIntegrateNexuses(string $directory): int
    {
        // 1. Pre-flight Check: Ensure the target directory is valid and accessible.
        if (!is_dir($directory)) {
            // AmethystMatrix a warning for the developer. This is a configuration error, not a runtime failure.
            AmethystMatrix::warning("Nexus Discovery Aborted: The specified path is not a valid directory.", [
                'path' => $directory
            ]);
            // Return 0 as no Nexuses were loaded.
            return 0;
        }

        // 2. Initialization: Prepare for the scan.
        $integratedCount = 0;
        // Use native PHP iterators for maximum performance and efficiency. No external dependencies.
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        // GIFT #3 IMPLEMENTATION: The Invisibility Cloak (Laravel Dynamic Version)
        {
            // 1. Fetch the exclusion suffixes directly from Laravel's config system.
            // The second argument `[]` is a default, ensuring it works even if the config is missing.
            $excludeSuffixes = config('krubot.discovery.exclude_suffixes', ['disabled']);

            // 2. Dynamically build the negative lookbehind part of the regex.
            // This is where the magic happens: each suffix becomes a `(?<!\...)` pattern.
            $lookbehinds = array_map(
                fn($suffix) => '(?<!\.' . preg_quote($suffix, '/') . ')',
                $excludeSuffixes
            );

            // 3. Assemble the final, complete regex pattern.
            $regexPattern = '/' . implode('', $lookbehinds) . '\.php$/i';
            // forExample(`['disabled']`)->wouldBe=> '/(?<!\.disabled)\.php$/i'

            // 4. Use the dynamically generated pattern in the RegexIterator.
            // This is now fully config-driven and incredibly flexible.
            $phpFiles = new \RegexIterator($iterator, $regexPattern);
        }
        /// $phpFiles = new \RegexIterator($iterator, '/\.php$/');

        // ✨ STEP 0: CHECK THE MASTER SWITCHS ✨
        // Read the config value just once before the loop for efficiency.
        $opcacheMasterSwitch = config('krubot.cache.opcache.enabled', true);
        $opcacheGlobalRefreshSwitch = config('krubot.cache.opcache.refresh_on_discover', false);

        // ✨ PLUS: LOAD THE ENTIRE STRATEGIC CONFIGURATION ✨
        $forceList = config('krubot.cache.force_refresh', []);
        $excludeList = config('krubot.cache.exclude_from_refresh', []);
        $basePath = base_path() . DIRECTORY_SEPARATOR;

        // 3. The Great Scan: Iterate over every single PHP file found.
        foreach ($phpFiles as $phpFile) {
            /** @var \SplFileInfo $phpFile */
            $realPath = $phpFile->getRealPath();

            $realPath = JackPoint::transform('discovery.file.resolve.path', $realPath, $this);

            // 🔥 EVENT: discovery.file.found — inspect/skip/veto a candidate file.
            $veto = JackPoint::fire('discovery.file.found', $realPath, $this);
            if ($veto === false) continue;

            if($opcacheMasterSwitch) {
                
                // Prepare a relative path for matching against config wildcards.
                $relativePath = Str::after($realPath, $basePath);
                
                // ✨ THE DECISION TREE - The Brain of the Opcache Operation ✨
                $shouldRefresh = false; // Default assumption
                
                // Priority 1: Check the _OPCACHE_ VETO list. If it matches, the decision is final.
                if (Str::is($excludeList, $relativePath)) {
                    $shouldRefresh = false; 
                } else {
                    // Priority 2: Check the OVERRIDE list.
                    if (Str::is($forceList, $relativePath)) {
                        $shouldRefresh = true;
                    } else {
                        // Priority 3: Fallback to the MASTER SWITCH.
                        $shouldRefresh = $opcacheGlobalRefreshSwitch;
                    }
                }

                // ✨ EXECUTE FRESH_NESS BASED ON THE FINAL DECISION ✨
                if ($shouldRefresh) {
                    Opcache::fresh($realPath);
                }
            }
            
            // 4. Extraction: Use the robust helper to parse the file content.
            // We pass the real path to ensure file_get_contents can read it without issues.
            $className = $this->extractFqcnFromFile($realPath);

            // 5. Validation & Integration:
            // Validate FQCN and ensure class is autoloadable before integration.
            // This is a critical two-step check:
            // A) Is the className valid (not null)?
            // B) Does the extracted class actually exist and is it loadable by the autoloader?
            if ($className && class_exists($className)) {
                // If both checks pass, we call the core integration logic.
                // The `integrateNexus` method should handle the reflection and registration.
                // Note!: The `integrateNexus` method now handles duplicate prevention itself.
                $this->integrateNexus($className, false);
                
                // Increment the counter for the final report.
                $integratedCount++;
            }
        }

        // After all nexuses have been scanned, resolve the priorities for each types.
        $this->prioritizeFallbacks();

        // 🔥 EVENT: discovery.completed — full report of what was loaded.
        JackPoint::fire('discovery.completed', $integratedCount, $directory, $this);
        
        // 6. Final Report: Return the count of successfully loaded Nexuses.
        return $integratedCount;
    }

    public function discoverNexusesIn(string $directory): int
    {
        return $this->discoverAndIntegrateNexuses($directory);
    }

    public function addNexusesFromDir(string $directory): int
    {
        return $this->discoverAndIntegrateNexuses($directory);
    }

    public function addCatalystsFromDirectory(string $directory): int
    {
        return $this->discoverAndIntegrateNexuses($directory);
    }

    public function loadNexusesFrom(string $directory): int
    {
        return $this->discoverAndIntegrateNexuses($directory);
    }

    /**
     * [Private Helper] Reliably extracts the Fully Qualified Class Name (FQCN) from a PHP file.
     * using PHP's native tokenizer.
     *
     * This is the heart of The Sentinel Engine. It reads the file's content and uses
     * `token_get_all` to parse PHP's grammatical structure, finding the exact `namespace`
     * and `class` declarations. This approach is immune to filesystem inconsistencies.
     *
     * @param string $filePath The absolute path to the PHP file.
     * @return string|null The FQCN (e.g., "App\KrubiK\Nexus\AdminNexus") or null if not found.
    */
    protected function extractFqcnFromFile(string $filePath): ?string
    {
        // Read the entire file content into memory. For typical class files, this is very fast.
        $content = @file_get_contents($filePath);
        if ($content === false) {
            AmethystMatrix::error("Nexus Discovery: Failed to read file content.", ['path' => $filePath]);
            return null;
        }

        // Use PHP's own parser to break the code into its fundamental components (tokens).
        $tokens = token_get_all($content);

        $namespace = '';
        $class = null;
        $tokenCount = count($tokens);

        // Iterate through the token stream to find our targets: `namespace` and `class`.
        for ($i = 0; $i < $tokenCount; $i++) {
            
            // State 1: Hunting for the `namespace` keyword.
            if (isset($tokens[$i][0]) && $tokens[$i][0] === T_NAMESPACE) {
                // Once found, start collecting all subsequent string and separator parts until a semicolon is hit.
                for ($j = $i + 1; $j < $tokenCount; $j++) {
                    // Semicolon marks the end of the namespace declaration.
                    if ($tokens[$j] === ';') {
                        break;
                    }
                    // We only care about array-based tokens (like T_STRING) that form the namespace path.
                    if (is_array($tokens[$j])) {
                        // Append the value of the token (e.g., "App", "KrubiK", "Nexuses") to our string.
                        $namespace .= $tokens[$j][1];
                    }
                }
            }

            // State 2: Hunting for the `class`, `interface`, or `trait` keyword.
            if (isset($tokens[$i][0]) && in_array($tokens[$i][0], [T_CLASS, T_INTERFACE, T_TRAIT])) {
                // The very next T_STRING token *must* be the name of the class/interface/trait.
                // We scan forward, skipping any whitespace.
                for ($j = $i + 1; $j < $tokenCount; $j++) {
                    if (isset($tokens[$j][0]) && $tokens[$j][0] === T_WHITESPACE) {
                        continue; // Skip whitespace.
                    }
                    
                    // Found it! The first non-whitespace token is the name.
                    if (isset($tokens[$j][0]) && $tokens[$j][0] === T_STRING) {
                        $class = $tokens[$j][1];
                        // We've found everything we need, so we can break out of both loops entirely.
                        break 2;
                    }
                }
            }
        }

        // If a class name was successfully found, construct the FQCN.
        if ($class) {
            // If a namespace was found, prepend it with a backslash. Otherwise, it's a root-namespace class.
            return $namespace ? trim($namespace) . '\\' . $class : $class;
        }

        // If no class definition was found in the file, return null.
        return null;
    }

    /**
     * Boot the JackPoint synapses for a given Nexus class, if it declares one.
     *
     * Rules:
     *  - Only runs once per Nexus FQCN per process (idempotent via $bootedSynapses).
     *  - Supports both `synapses(): void` and `synapses(): array` signatures.
     *  - If synapses() returns an array of [event => callable], registers each pair
     *    via JackPoint::on<Event>() automatically.
     *  - The method is called in a JackPoint-scoped closure so that any
     *    JackPoint::on* /fire* /pipe* calls inside synapses() are fully supported.
    *
    * @param  string|object $nexus  FQCN string or an instantiated Nexus object.
    * @return void
    */
    protected function injectSynapses(string|object $nexus): void
    {
        $nexusClassName = is_object($nexus) ? $nexus::class : $nexus;

        // جلوگیری از اجرای دو‌باره synapses یک Nexus
        if (isset($this->injectedSynapses[$nexusClassName])) {
            return;
        }

        try {
            $reflection = new ReflectionClass($nexusClassName);

            $methodName = 'synapses';

            if (!$reflection->hasMethod($methodName)) {

                $methodName = 'hooks'; // Try for hooks method

                if (!$reflection->hasMethod($methodName))
                    return;
            }

            $method = $reflection->getMethod($methodName);

            // فقط متد public استاتیک یا قابل اجرا روی instance
            if (!$method->isPublic()) {
                return;
            }

            // Only handle public/protected static OR instance methods named synapses()
            // that take zero required parameters.
            if ($method->getNumberOfRequiredParameters() > 0) {
                return;
            }

            // High-Precision Return Type Verification (PHP 8.2+ Union/Intersection/Named safe)
            $returnType = $method->getReturnType();
            if ($returnType !== null) {
                $validReturn = false;
                $typesToCheck = ($returnType instanceof ReflectionUnionType) 
                    ? $returnType->getTypes() 
                    : [$returnType];

                foreach ($typesToCheck as $type) {
                    if ($type instanceof ReflectionNamedType && in_array($type->getName(), ['void', 'mixed', 'array'], true)) {
                        $validReturn = true;
                        break;
                    }
                }

                // بررسی return type: باید void یا array باشد
                if (!$validReturn) {
                    return;
                }
            }

            // 🛑 TACTICAL VETO HOOK: Opportunity to abort injection externally
            if (JackPoint::fire('nexus.synapses.injecting', $nexusClassName, $this) === false) {
                JackPoint::fire('nexus.synapses.vetoed', $nexusClassName);
                return;
            }

            // علامت‌گذاری قبل از اجرا — حتی اگر exception بدهد، دو‌بار اجراء نمی‌شود
            // Mark as booted BEFORE execution to prevent re-entrancy if synapses()
            // itself triggers another bootNexusSynapses() call down the chain.
            $this->injectedSynapses[$nexusClassName] = true;

            $injectMySynapses = function () use($nexusClassName, $method, $nexus)
            {
                // اجرا: اگر static باشد بدون instance، وگرنه instance می‌سازیم
                // $nexusInstance = $this->app->make($nexusClassName);
                $nexusInstance = is_object($nexus) ? $nexus : ($method->isStatic() ? null : (new ReflectionClass($nexusClassName))->newInstanceWithoutConstructor());

                $result   = $method->isStatic()
                ? $nexusClassName::synapses()
                : $method->invoke($nexusInstance);

                // If synapses() returned an array.
                if (is_array($result) && !empty($result)) {
                    $registeredHooks = [];

                    // Flat array of pre-registered listener tokens/IDs
                    if(array_is_list($result)) {
                        $this->injectedSynapses[$nexusClassName] = $result;
                        $registeredHooks = $result;
                    }
                    else {
                        // Associative array: ['event.name' => callable]
                        foreach ($result as $event => $listener) {
                            if (is_string($event) && is_callable($listener)) {
                                $registeredHooks []= JackPoint::{'on' . ucfirst($event)}($listener);
                            }
                        }
                    }
                    
                    // ⚡ Hook Loaded Event (Stage complete)
                    JackPoint::fire('nexus.synapses.loaded', $nexusClassName, $registeredHooks, $this);
                }
            };

            $scopedRegister = config('krubot.extensions.scoped-register', false);

            if($scopedRegister) {

                // Execute inside a JackPoint scope so all JackPoint::* calls
                // made inside synapses() are valid and fully supported.
                JackPoint::scopedExecute($nexusClassName, $injectMySynapses);

            }
            else {

                // بدون scope wrapper ـ JackPoint همچنان کار می‌کند، فقط scope ایزوله نخواهد بود
                $injectMySynapses();
            }

            // Stage: Injection fully realized
            JackPoint::fire('nexus.synapses.injected', $nexusClassName, $this);

        } catch (\ReflectionException $e) {
            AmethystMatrix::warning("Synapses Runner: Could not reflect [{$nexusClassName}]", [
                'error' => $e->getMessage()
            ]);
            JackPoint::fire('nexus.synapses.failed', $nexusClassName, $e);
        } catch (\Throwable $e) {
            AmethystMatrix::error("Synapses Runner: Failed executing synapses() on [{$nexusClassName}]", [
                'error' => $e->getMessage()
            ]);
            JackPoint::fire('nexus.synapses.failed', $nexusClassName, $e);
        }
    }

    /**
     * Fulfills the \Countable contract to get the number of registered routes.
     *
     * This implementation enables the intuitive use of PHP's native `count()`
     * function directly on a Krubot instance (e.g., `count($krubot)`).
     * It's a significant Developer Experience (DX) enhancement, making the
     * object behave like a standard, countable collection in this context.
     *    
     * @return int Total number of routes you've beautifully crafted.
    */
    #[\ReturnTypeWillChange]
    public function count()
    {
        // Delegate count operation to the internal routes array.
        // This is an O(1) operation for PHP arrays.
        return count($this->routes);
    }


    // =========================================================================
    //  ⚡ UNI_CHAT_KIT ⚡ STYLED ROUTING & MIDDLEWARE
    // =========================================================================

    /**
     * Add a global middleware that runs on every update.
    */
    public function middleware(string|array $middleware): self
    {
        $middleware = JackPoint::transformMiddlewareRegister($middleware, $this);

        if (is_array($middleware)) {
            $this->globalMiddlewares = array_merge($this->globalMiddlewares, $middleware);
        } else {
            $this->globalMiddlewares[] = $middleware;
        }
        return $this;
    }

    /**
     * Define a Command Route (Auto-prepends '/').
     * Updated to accept Attributes (Middlewares/Guards).
     * Usage: $bot->onCommand('start', [Controller::class, 'method']);
    */
    public function onCommand(string $command, array|callable $handler, array $attributes = []): Route
    {
        // Handle parameterized commands like 'buy {item}' -> '/buy {item}'
        // Or simple commands 'start' -> '/start'
        if (!str_starts_with($command, '/')) {
            $command = '/' . $command;
        }
        return $this->addRoute($command, $handler, self::RT_COMMAND, $attributes);
    }

    /**
     * Define a Text Route (Exact match, Regex, or Parameterized).
     * Usage: $bot->onText('Hello', ...); OR $bot->onText('/^Hi$/i', ...);
    */
    public function onText(string $pattern, array|callable $handler, array $attributes = []): Route
    {
        return $this->addRoute($pattern, $handler, self::RT_TEXT, $attributes);
    }

    /**
     * Define a RegEx Route
     * Usage: $bot->onText('Hello', ...); OR $bot->onText('/^Hi$/i', ...);
    */
    public function onRegEx(string $pattern, array|callable $handler, array $attributes = []): Route
    {
        // Logic Check: Auto-Slash Wrapper
        // If the user forgot delimiters (e.g. 'hello'), we wrap it: '/hello/'
        // And Checks if pattern matches standard regex format: /.../flags
        // ^\/       : Starts with /
        // .*        : Content
        // \/        : Ends with /
        // [a-zA-Z]* : Followed ONLY by letters (modifiers like 'i', 'm')
        // $         : End of string
        if (!preg_match('/^\/.*\/[a-zA-Z]*$/', $pattern))
            $pattern = '/' . $pattern . '/';

        return $this->onText($pattern, $handler, self::RT_REGEX, $attributes);
    }

    /**
     * Define a Callback/Button Route.
     * The Ultimate Magic for independent Glass Buttons.
     * Usage: $bot->onButton('remove_item', [Controller::class, 'method']);
     */
    public function onButton(string $payload, array|callable $handler, array $attributes = []): Route
    {
        // ⚡ We prefix button payloads with 'CBK::' internally.
        // This prevents collisions with regular user text like "remove_item".
        return $this->addRoute('CBK::' . $payload, $handler, self::RT_ACTION, $attributes);
    }

    /**
     * Register callback action route.
     * Example: $bot->onAction('remove', [CartNexus::class, 'remove']);
    */
    public function onAction(string $action, array|callable $handler, array $attributes = []): Route
    {
        // Internal namespaced pattern to avoid collision with normal text
        return $this->addRoute('CBK::' . $action, $handler, self::RT_ACTION, $attributes);
    }

    /**
     * Define an Inline Query Route.
     * Handles three modes:
     * 1. null pattern: Matches any inline query (catch-all).
     * 2. Regex pattern: (e.g., '/item\s+(.+)/') for advanced matching.
     * 3. Prefix pattern: (e.g., 'article:') if it contains ':' or doesn't start with '/'.
     *
     * @param string|null $pattern The pattern to match against the inline query text.
     * @param array|callable $handler The controller method or closure to execute.
     * @param array $attributes Additional attributes for the route.
     * @return Route The created route instance.
    */
    public function onInlineQuery(?string $pattern, array|callable $handler, array $attributes = []): Route
    {
        // This variable will hold the final pattern used for matching.
        $processedPattern = $pattern;

        // Case 1: Catch-all for any inline query. We use a special internal constant.
        if ($pattern === null) {
            $processedPattern = '__ANY__'; // A unique string to signify a catch-all route
        }
        // Case 2: It's already a valid Regex.
        elseif (preg_match('/^\/.*\/[a-zA-Z]*$/', $pattern)) {
            $processedPattern = $pattern; // No change needed
        }
        // Case 3: It's a prefix filter. We convert it to a non-capturing, case-insensitive regex.
        // This is much faster and more reliable than str_starts_with during dispatch.
        else {
             // preg_quote escapes any special regex characters in the user's prefix string.
            $processedPattern = '/^' . preg_quote($pattern, '/') . '/i';
        }

        return $this->addRoute($processedPattern, $handler, self::RT_INLINE, $attributes);
    }

    /**
     * ⚡ THE DX DREAM: Define a Sensory Route for specific content types.
     * Supports single types ('photo') or Arrays of types (['photo', 'video', 'document']).
     * 
     * @param string|array<string> $types e.g. 'photo', 'video', 'voice'
     * @param array|callable $handler The logic to execute
     * @param array $attributes Route configurations
     * @return Route|array<Route> Returns a Route object or array of Routes if multiple types provided.
    */
    public function onType(string|array $types, array|callable $handler, array $attributes = []): Route|array
    {
        // 🚀 FATALITY: Array support for ultimate DX (e.g., bot->onType(['photo', 'video', Signal::Geo], ...))
        if (is_array($types)) {
            $createdRoutes = [];
            foreach ($types as $type) {

                // ✨ 1A. INTELLIGENCE: For each type in the array, classify its nature.
                $isEnvelope = Signal::isEnvelopeFrequency($type);

                // ✨ 2A. ENRICHMENT: Prepare the final attributes with the strategy flag.
                $finalAttributes = $attributes + [
                    '_signal_class' => $isEnvelope 
                ];

                // Internal namespaced pattern 'TYPE::photo' to avoid collision with normal text
                $createdRoutes[] = $this->addRoute('TYPE::' . strtolower($type), $handler, self::RT_SIGNAL, $finalAttributes);
            }
            return $createdRoutes;
        }

        // ✨ 1B. INTELLIGENCE: Classify the nature of the single type.
        $isEnvelope = Signal::isEnvelopeFrequency($types);

        // ✨ 2B. ENRICHMENT: Prepare the final attributes with the strategy flag.
        $finalAttributes = $attributes + [
            '_signal_class' => $isEnvelope, // The crucial flag is now stored!
        ];

        // Single Type Registration
        // ✨ 3. PERSISTENCE: Register the route with the enriched attributes.
        // The Route object now permanently holds the correct detection strategy.
        return $this->addRoute('TYPE::' . strtolower($types), $handler, self::RT_SIGNAL, $finalAttributes);
    }

    /**
     * Registers the main entry point for a Web Application.
     * This is typically linked to the 'index' or 'handle' method of a Nexus.
     *
     * @param string $path The unique dot-notation path for the WebApp (e.g., 'game.dashboard').
     * @param ?array|string $methods The allowed HTTP methods (e.g., 'POST' or ['GET', 'POST']).
     * @param array|callable $handler The resolved handler, pointing to [ClassName::class, 'index' or 'handle'].
     * @param array $attributes Optional attributes, including HTTP methods.
     * @return Route The created route instance.
     */
    public function onWebApp(string $path, array|callable $handler, array|string $methods = null, array $attributes = []): Route
    {
        // Store HTTP methods in the route's attributes for the web gateway to use.
        ///$attributes['http_methods'] = (array) $methods;
        // We store the allowed HTTP methods directly in the route's attributes for the dispatcher to use.
        if($methods)
            $attributes['http_methods'] = is_string($methods) ? [$methods] : $methods;

        // Use a distinct internal prefix to identify these root routes.
        return $this->addRoute('WAPP::' . $path, $handler, self::RT_WEB_APP, $attributes);
    }

    /**
     * Registers a handler for a specific Web Action path.
     * 💎 Define a Web App Action Route.
     *
     * @param string $path The unique dot-notation path for the action (e.g., 'game.dashboard.order_vip').
     * @param ?array|string $methods The allowed HTTP methods (e.g., 'POST' or ['GET', 'POST']).
     * @param array|callable $handler The handler to be executed.
     * @param array $attributes Optional attributes for the route.
     * @return Route The created route instance.
     */
    public function onWebAction(string $path, array|callable $handler, array|string $methods = null, array $attributes = []): Route
    {
        // Store HTTP methods in the route's attributes for the web gateway to use.
        ///$attributes['http_methods'] = (array) $methods;
        // We store the allowed HTTP methods directly in the route's attributes for the dispatcher to use.
        if($methods)
            $attributes['http_methods'] = is_string($methods) ? [$methods] : $methods;
        return $this->addRoute('WACT::' . $path, $handler, self::RT_WEB_ACTION, $attributes);
    }

    /**
     * Registers a handler for a specific Web Page path.
     *
     * @param string $path The unique dot-notation path for the page (e.g., 'game.dashboard.show_vip').
     * @param array|callable $handler The handler to be executed.
     * @param array $attributes Optional attributes for the route.
     * @return Route The created route instance.
     */
    public function onWebPage(string $path, array|callable $handler, array $attributes = []): Route
    {
        // We use an internal prefix to avoid collisions with other route types.
        return $this->addRoute('WAPP::' . $path, $handler, self::RT_WEB_PAGE, $attributes);
    }

    /**
     * Resolves a potentially relative attribute name against a class-level prefix.
     *
     * @param string $name The name from the method attribute (e.g., '.show_product').
     * @param string|null $prefix The name from the class attribute (e.g., 'game.dashboard').
     * @return string The fully resolved name (e.g., 'game.dashboard.show_product').
     */
    protected function _resolveRelativePathName(?string $name, ?string $prefix): string
    {
        if ($name === null) {
            return null;
        }

        // Check if the name is intended to be relative. 
        // If the name starts with '.', it's relative to the prefix.
        if (str_starts_with($name, '.')) {
            // If a class-level prefix exists, the method name is a child of it.
            // The method's identity is completed by its parent's identity.
            if ($prefix) {
                // Concatenate prefix and the name(without the redundant dots).
                return rtrim($prefix, '.') . '.' . ltrim($name, '.');
            }
            
            // If no prefix exists, the name stands on its own, but the '.' is just a convention.
            // It asserts its identity independently.
            return ltrim($name, '.');
        }
        
        // Otherwise, it's an absolute name, defining its own complete path in the universe of routes.
        return $name;
    }

    /**
     * Internal method to create and store Route.
    */
    protected function addRoute(string $pattern, mixed $handler, string $routeType, array $attributes = []): Route
    {
        // Apply Group Attributes (Prefix, Middlewares)
        $attrs = array_merge($this->getGroupAttributes(), $attributes);

        $definition = JackPoint::transformRouteAdding([
            'pattern'    => $pattern,
            'handler'    => $handler,
            'type'       => $routeType,
            'attrs'      => $attrs,
            'attributes' => $attrs,
        ], $this);

        if($definition) {
            $pattern    = (string) ($definition['pattern'] ?? $pattern);
            $handler    = $definition['handler'] ?? $handler;
            $routeType  = (string) ($definition['type'] ?? $routeType);
            $attrs      = (array) ($definition['attributes'] ?? $definition['attrs'] ?? $attrs);        
        }
        
        // Handle Prefix
        if (isset($attrs['prefix'])) {
            // Logic to prepend prefix. If regex, it's complex, assuming simple string or simple regex start.
            // Simple command implementation:
            if (str_starts_with($pattern, '/')) {
                 $cleanPattern = substr($pattern, 1);
                 $pattern = '/' . $attrs['prefix'] . '/' . $cleanPattern;
            }
        }

        // Create the Route Object (Class Signature #1)
        /// $route = new Route($pattern, $handler, $attrs); ///

        // Create the Route Object (Class Signature #2)
        $route = new Route($pattern, $handler, $attrs); //|// , $registrar

        // [THE UPGRADE] The Route becomes self-aware of its type upon birth.
        $route->type = $routeType;
        
        // Store in routes array
        $this->routes[$pattern] = $route;

        // ⚡ NAME REGISTRAR BRIDGE:
        // We pass a name string to the Route object via $attrs['route_name']. When $route->name('xyz') is called,
        // this closure fires and registers the route in our fast lookup table ($this->namedRoutes).

        if(isset($attributes['route_name']))
            $this->namedRoutes[$attributes['route_name']] = $route;
        
        // Track for group chaining ($bot->group()->middleware())
        $this->registerRouteToGroup($route);

        // 🔥 EVENT: route.added — post-registration, e.g., audit / docs generator.
        JackPoint::fireRouteAdded($route, $this);
        
        return $route;
    }

    /**
     * بازگرداندن تمام مسیرهای ثبت‌شده به همراه جزئیات کامل
     *
     * @return array
    */
    public function getRoutes(): array
    {
        $result = [];
        foreach ($this->routes as $pattern => $route) {
            if ($route instanceof Route) {
                $result[] = [
                    'pattern'      => $pattern,
                    'type'         => $route->type,
                    'action'       => $route->getAction(),
                    'middleware'   => $route->getMiddlewareStack(),
                    'platforms'    => $route->getPlatforms(),
                    'guards'       => $route->getGuards(),
                    'forceJoin'    => $route->forceJoinChannels ?? [],
                    'name'         => $route->getName(),
                    'parameters'   => $route->pathParameters ?? [],
                    // 'http_methods' => $route->getAttribute('http_methods', []),
                    'attrs'        => $route->attributes,
                    'autoEnrich'   => $route->autoEnrichPattern ?? false,
                    'accessPolicy' => method_exists($route, 'getAccessPolicy') ? $route->getAccessPolicy() : null,
                ];
            } else {
                // برای سازگاری با ساختار قدیمی (آرایه‌ای)
                $result[] = [
                    'pattern'    => $pattern,
                    'type'       => $route['attributes']['_route_type'] ?? 'unknown',
                    'action'     => $route['action'] ?? null,
                    'middleware' => $route['attributes']['middleware'] ?? [],
                ];
            }
        }
        return $result;
    }

    // =========================================================================
    //  ⚡ HELPER METHODS (UniChatKit Parity)
    // =========================================================================

    /**
     * Get the parameters of the target handler.
    */
    public function currentParameters(): array
    {
        return $this->currentRouteParams;
    }

    /**
     * Get the current resolved Route object.
    */
    public function activeRoute(): ?Route
    {
        return $this->activeRoute;
    }

    /**
     * Resolves the current operational platform from the central RenderAura.
     * Nemesis is responsible for populating this context prior to routing.
     *
     * @return string The canonical name of the platform (e.g., 'telegram', 'web', 'rubika').
     * @throws \Illuminate\Contracts\Container\BindingResolutionException If RenderAura is not bound.
    */
    public function resolveCurrentPlatform(): string
    {
        // -----------------------------------------------------------------
        // 🧠 IDENTITY RESOLUTION SOURCE (The RenderAura queen)
        // We query the application's single source of truth for the current platform context.
        // This relies on an upstream process (Request-Scoped Singleton)
        // -----------------------------------------------------------------

        /** @var RenderAura $renderAura */
        $renderAura = resolve(RenderAura::class);

        // We expect the platform to be always set. If it's not, it's an
        // exceptional state. We cast to string to ensure type safety.
        // The default can be 'unknown' or you can let it throw an error if null.
        return (string) ($renderAura->platform ?? 'unknown');
    }

    /**
     * UniChatKit-Compatible 'hears' method.
     * 
     * It automatically detects if the pattern is an "Unwrapped Regex" 
     * and wraps it properly before passing it to the main Router.
     * 
     * Features:
     * 1. Supports UniChatKit Params: 'call {name}'
     * 2. Supports Full Regex: '/^([0-9]+)$/i'
     * 3. Supports Unwrapped Regex: '([0-9]+)' -> Auto-converted to '/^([0-9]+)$/iu'
     * 4. Supports Case-Insensitive Text: 'hi' -> Auto-converted to '/^hi$/iu'
     * 
     * @param string $pattern
     * @param array|callable|string $handler
    */
    public function hears(string $pattern, array|callable|string $handler): self
    {
        // 1. CASE: Parameterized Command (Native Krubot Feature)
        // e.g. "call me {name}"
        // We pass this directly because onText handles {param} conversion internally.
        if (str_contains($pattern, '{') && str_contains($pattern, '}')) {
            $this->onText($pattern, $handler);
            return $this;
        }

        // 2. CASE: Explicit Regex (Already wrapped)
        // e.g. "/^hi$/i" or "/hello/"
        // Check if it starts with "/" and implies a regex structure
        if (str_starts_with($pattern, '/') && preg_match('/\/[a-z]*$/', $pattern)) {
            $this->onText($pattern, $handler);
            return $this;
        }

        // 3. CASE: "Unwrapped Regex" or "Simple Text" (The UniChatKit Magic)
        // User wrote: '([0-9]+)' OR 'Hi'
        // Problem: onText would treat '([0-9]+)' as a literal string (Type C).
        // Solution: Wrap it!
        // We add Start(^) and End($) anchors + Case Insensitive (i) + Unicode (u) flags.
        // This makes 'Hi' match 'hi', 'HI' (just like UniChatKit)
        // And makes '([0-9]+)' work as a Regex.
        
        $wrappedPattern = '/^' . $pattern . '$/iu';
        
        $this->onText($wrappedPattern, $handler);
        return $this;
    }

    // Add this Method (The fallBack Setter)
    /**
     * Define a Fallback method.
     * Gets called if NO other "hears", "onText", or "onCommand" routes match.
    */
    public function fallback(callable|array|string $handler): self
    {
        $this->fallbackHandler = JackPoint::transformFallbackRegister($handler, null, $this);
        return $this;
    }

    /**
     * Programmatically define a type-specific fallback handler.
     * This method registers a handler that will be considered alongside those from #[FallbackOn] attributes.
     * The final handler is chosen based on priority at the end of the integration phase.
     *
     * @param string|string[] $types The message type(s) to handle (e.g., 'video', or ['photo', 'sticker']).
     * @param callable|array $handler The function or [class, method] array to execute.
     * @param int $priority Higher numbers have greater priority.
     * @return self For a fluent interface.
    */
    public function fallbackOn(string|array $types, callable|array $handler, int $priority = 0): self
    {
        // First, normalize the input types into a simple, flat array.
        $targetTypes = is_array($types) ? $types : [$types];

        // Now, register each type in our temporary registry with its given priority.
        // This doesn't overwrite, it just adds another candidate for the final decision.
        foreach ($targetTypes as $type) {
            // PRE-EMPTIVE STRIKE: Prevent registration of the void signal.
            if ($type === Signal::Void) {
                // Throw a very explicit exception. This is a developer error, not a runtime issue.
                throw new \InvalidArgumentException(
                    "Registering a fallback handler for the 'unknown' type (Signal::Void) is architecturally forbidden. " .
                    "Use the global fallback() method for catch-all scenarios."
                );
            }
            $this->fallbackRegistry[$type][$priority] = JackPoint::transformFallbackRegister(
                $handler, $type, $this
            );
        }

        // Returning $this allows for method chaining, e.g., $bot->fallbackOn(...)->fallback(...);
        return $this;
    }

    // ✨ NEW: FINALIZE FALLBACK PRIORITIES
    protected function prioritizeFallbacks(): void
    {
        // After all nexuses have been scanned, resolve the priorities.
        foreach ($this->fallbackRegistry as $type => $handlers) {
            krsort($handlers); // Sort handlers by priority (key) in descending order.
            $this->typeFallbackHandlers[$type] = reset($handlers); // Get the first element (highest priority).
        }
        $this->fallbackRegistry = []; // Clear the temporary registry.
    }

    // =========================================================================
    //  ⚡ CORE EXECUTION LOGIC (THE BRAIN)
    // =========================================================================

    /**
    // TODO: parent::ina_code...
     * ⚡ Override run() to inject our advanced router logic.
     * Used mostly for Polling or simple webhook scripts.
    */
    public function run(): void
    {
        $vancore = $this->core();
        // We register a SINGLE master handler in the parent Vanguard Core
        // This intercepts everything and passes it to our Router Logic
        $vancore->onMessage(null, function ($bot, Message $message) {
            $this->processUpdate($message);
        });

        // Start the engine
        $vancore->run();
    }

    /**
     * =========================================================================
     *  ⚡ ON-DEMAND POLLING TRIGGER вҡЎ  (Sovereign Edition v6.0)
     * =========================================================================
     *
     * Fetches all pending updates via 'getUpdates' and dispatches them
     * to the Queue Architecture using the "Driver Identity Protocol".
     * Ideal for Cron Jobs or webhook-less environments.
     *
     * Features:
     * 1. Auto-Detects Driver Identity (Bale/Rubika/etc).
     * 2. Forges Toxic DTOs strictly.
     * 3. Dispatches to HandleDriverUpdate to prevent Cross-Wiring.
     *
     * @return array Status report.
    */
    public function processPendingUpdates(): array
    {
        /// $token = (string) $this->forceGetProperty('token');
        /// $url = "https://botapi.rubika.ir/v3/{$token}/getUpdates";

        // =====================================================================
        // PHASE 1: FETCH DATA (THE EYES)
        // =====================================================================
        // We use the our fresh API client to fetch updates.
        $apiResponse = $this->pulseApi('getUpdates');

        // Check for 'data' key wrapper (Rubika Standard)
        $data = $apiResponse['data'] ?? [];

        if (empty($data['updates'])) {
            return ['status' => 'no-updates', 'count' => 0];
        }

        // =====================================================================
        // PHASE 2: IDENTITY RECOVERY (THE SOUL)
        // =====================================================================
        // 🕵️ CRITICAL: Who am I?
        // We extract the 'driver_alias' injected by Nemesis (Current KrubotManager).
        // If missing (Legacy Mode), we fallback to 'rubika'.
        $currentIdentity = $this->driver->driver_alias ?? 'rubika';

        // =====================================================================
        // PHASE 3: PROCESSING LOOP (THE HANDS)
        // =====================================================================
        $queuedCount = 0;

        foreach ($data['updates'] as $updateRaw) {
            try {
                // A) ⚗️ ALCHEMY: FORGE THE DTO
                // We wrap the raw array into a strict DTO using the 'forge' factory.
                // Strategy: We wrap it in ['update' => ...] to match the DTO's expectation.
                $dto = UniversalInboundUpdate::forge(['update' => $updateRaw]);

                // B) 🚀 DISPATCH: SEND TO QUEUE
                // We pass the DTO AND the Identity ($currentIdentity).
                // This ensures the Job spawns the CORRECT driver to reply.
                HandleDriverUpdate::dispatch($dto, $currentIdentity);

                $queuedCount++;

            } catch (\Throwable $e) {
                // Log and continue (Circuit Breaker)
                if (class_exists(AmethystMatrix::class)) {
                    AmethystMatrix::error("🔥 Fetch Loop Error [{$currentIdentity}]: " . $e->getMessage());
                }
                continue;
            }

            /// ///// OLD LEGACY METHOD (DIRECT DISPATCH WITHOUT IDENTITY) /////
            /// dispatch(new HandleRubikaUpdate($updateRaw));
        }

        return [
            'status' => 'ok',
            'queued' => $queuedCount,
            'driver' => $currentIdentity
        ];
    }

    /**
     * Resolves the Chat ID priority;
     * from argument or builder context.
     * Priority:
     * 1. Passed argument ($chatId)
     * 2. Internal state ($this->chat_id) set via chat('ID')
     * 
     * @param string|null $chatId
     * @return string
     * @throws \InvalidArgumentException If no Chat ID is determined.
    */
    protected function resolveChatId(?string $chatId = null): string
    {
        // تلاش برای دریافت از آرگومان یا متد chatId() کلاس والد
        $realChatId = $chatId ?? $this->chatId();

        // 1. Return explicit argument if present
        if ($chatId !== null) {
            return $chatId;
        }

        // 2. Return internal state (chained method style: $bot->chat('ID')->sendDice())
        // Assuming the main Bot class has a public or protected $chat_id property
        if (!empty($this->chat_id)) {
            return $this->chat_id;
        }

        // 3. Fail safely, may be too soon for alert
        throw new \InvalidArgumentException(
            "Target Chat ID is missing. Use ->chat('ID') or pass \$chatId as an argument."
            . PHP_EOL .
            "Chat ID is required via argument or builder ->chat()"
        );
    }

    /**
     * Resolves a dynamic, potentially translatable message string.
     * This is the generic implementation of the Commander's HyperDX message pattern.
     *
     * @param null|string $message The raw message string (e.g., 'Hello', '::key|fallback').
     * @param string $default The default message or translation key if $message is null.
     * @return string The final, resolved message.
    */
    protected function resolveAndTranslateMessage(?string $message, string $default): string
    {
        // Use the route-specific message if provided, otherwise fall back to the default config key/string.
        $messageText = $message ?? $default;

        // If it's not a translation key, return it as is.
        if (!str_starts_with($messageText, '::')) {
            return $messageText;
        }
        
        // It's a translation key. Let the alchemy begin.
        $keyAndFallback = substr($messageText, 2);

        // SUPER-CHARGED TRANSLATION LOGIC WITH FALLBACK
        if (str_contains($keyAndFallback, '|')) {
            // Explode with a limit of 2 to protect fallbacks that might contain '|'.
            [$translationKey, $fallbackMessage] = explode('|', $keyAndFallback, 2);
            $translated = __($translationKey);

            // Laravel's __() helper returns the key if no translation is found. We use this feature.
            return ($translated === $translationKey) ? $fallbackMessage : $translated;
        }
        
        // No fallback provided, It's a simple translation key.
        return __($keyAndFallback);
    }

    /**
     * The Alchemist's Forge: A Platform-Aware Button Factory.
     * This method dynamically crafts interactive join buttons based on the current platform context.
     * It's the core of our dynamic, multi-platform ForceJoin experience.
     *
     * @param array<string|int> $channels The raw list of channel identifiers from the Route.
     * @return array<\KrubiK\Keyboard\PowerButton> An array of fully-formed PowerButton objects.
     */
    protected function createPlatformAwareJoinButtons(array $channels): array
    {
        // First, ask the Oracle for our current reality.
        $platform = Platform::tryFrom($this->resolveCurrentPlatform());
        if(!$platform)
            return []; // Platfrom Not Detected!

        $buttons = [];

        // The Alchemist's mapping of Rubika prefixes to human-readable types.
        // As per your architectural revelation.
        $rubikaMentionTypes = ['g' => 'گروه', 'c' => 'کانال'];

        foreach ($channels as $channelId) {
            $button = null; // Reset for each iteration

            // The Grand Match: We shape reality based on the current platform.
            switch (true) {
                case $platform->matches('tg, bale'):
                    $label = 'ورود به کانال'; // Translatable base
                    if (is_numeric($channelId) && str_starts_with((string)$channelId, '-100')) {
                        // Telegram Private Channel Logic: e.g., -100123456789
                        $cleanId = substr((string)$channelId, 4);
                        $url = 'https://t.me/c/' . $cleanId;
                        $label = 'ورود به کانال خصوصی'; // More specific translatable
                    } else {
                        // Telegram Public Channel/User Logic: e.g., @KrubiK
                        $cleanId = ltrim((string)$channelId, '@');
                        $url = 'https://t.me/' . $cleanId;
                        $label = "عضویت در @" . $cleanId; // Translatable
                    }
                    $button = PowerButton::link("⬅️ " . $label, $url);
                    break;

                case $platform->matches('rubika'):
                    // As you brilliantly pointed out, all IDs are strings.
                    // We use the prefix to divine the entity type.
                    $prefix = substr((string)$channelId, 0, 1);
                    
                    // We only care about joinable entities: Channels ('c') and Groups ('g').
                    if (array_key_exists($prefix, $rubikaMentionTypes)) {
                        $entityType = $rubikaMentionTypes[$prefix]; // "کانال" or "گروه"
                        $label = "عضویت در {$entityType}"; // e.g., "عضویت در کانال"
                        
                        // Rubika uses an in-app linking scheme, not a standard web URL.
                        // This creates the correct deep link for the Rubika client.
                        $url = 'rubika://join/' . $channelId; 

                        $button = PowerButton::link("⬅️ " . $label, $url);
                    }
                    // If it's a 'u' (User) or 'b' (Bot), we can't "join" it. We ignore it.
                    break;
                
                // Future-proofing: Add cases for 'web', 'bale', 'eitaa', etc.
                // case 'web':
                //     // For a web platform, maybe the link is a standard URL.
                //     if (filter_var($channelId, FILTER_VALIDATE_URL)) {
                //         $label = "Visit Page";
                //         $button = PowerButton::link("⬅️ " . $label, $channelId);
                //     }
                //     break;

                default:
                    // If the platform is unknown or doesn't support joining, we do nothing.
                    // This prevents errors and ensures graceful degradation.
                    AmethystMatrix::prophesy('ForceJoin Button Creation', 'Unsupported or unknown platform for ForceJoin.', [
                        'platform' => $platform,
                        'channel_id' => $channelId
                    ]);
                    break;
            }

            if ($button) {
                $buttons[] = $button;
            }
        }

        return $buttons;
    }
    
    protected function resolveRoutingSignal(Message $message): array
    {
        $tuple = $this->resolveRoutingSignalCore($message);

        $transformed = JackPoint::transformRoutingResolve(
            $tuple,
            $message,
            $this
        );

        return (is_array($transformed) && count($transformed) === 5)
            ? array_values($transformed)
            : $tuple;
    }
    /**
     * Resolve the primary routing signal AND pre-compute all sensory data.
     * This is the unified "Sensory Command Center" of the engine.
     * It determines the primary signal type/payload and also provides the pre-computed
     * envelope and content signals to the main processing loop, eliminating redundant calculations.
     *
     * @return array{
     *   0: string, // routingType (e.g., self::RT_TEXT, self::RT_SIGNAL)
     *   1: string, // routingPayload (e.g., '/start', 'TYPE::photo')
     *   2: array<string,mixed>, // actionParams
     *   3: string, // envelopeSignal (pre-computed)
     *   4: string  // contentSignal (pre-computed)
     * }
     *
     * Resolves the primary routing signal from a Message object.
     * This is the polymorphic radar of the engine.
     * Priority: WebAction > WebApp > Callback > Text
    */
    protected function resolveRoutingSignalCore(Message $message): array
    {

        // =========================================================================
        // 🧠 PRE-COMPUTE SIGNALS (BEFORE THE ROUTE-MATCHING LOOP)
        // =========================================================================
        // We awaken the Sensory Engine only ONCE for both possible strategies.
        // This is the core of our hyper-performance optimization.
        $envelopeSignal = $this->detectMessageType($message, true);  // : Envelope-First Strategy :
        $contentSignal  = $this->detectMessageType($message, false); // : Content-First Strategy :

        // PRIORITY 1: Direct Web Request (from fetch/ajax to QuantumGateway)
        // This is the most explicit signal. It comes from a direct HTTP call to our web endpoints.
        if ($message->web_request && $message->web_request instanceof WebRequest) {
            $webRequest = $message->web_request;
            
            // HERE IS THE MAGIC: The routing type is a *generic web signal*.
            // The router's 'Finder' phase will then try to match the path against
            // all registered WebApp, WebPage, and WebAction routes. We don't decide here.
            // We provide the raw path as the payload.
            // Parameters (from JSON body) are passed for the handler.

            // 🔥 ادغام query و body (body اولویت دارد)
            $params = array_merge($webRequest->query->all(), $webRequest->body->all());

            return [self::RT_WEB, $webRequest->path, $params, $envelopeSignal, $contentSignal];

            // We return a generic RT_WEB signal. The router's "Finder" phase will then
            // be responsible for matching the provided path against all registered web route
            // types (WebApp, WebPage, WebAction). This decouples signal detection from route matching.

        }

        // PRIORITY 2: Data from a launched WebApp (e.g., from Telegram.WebApp.sendData)
        // This comes through the standard bot webhook, not a direct web endpoint.
        if (isset($message->web_app_data['data'])) {
            $dataPayload = $message->web_app_data['data'];

            [$action, $params] = $this->parseActionPayload($message->web_app_data['data']);
            // If the parser returns a valid action...
            if ($action !== null) {
                // ...we classify it as an RT_ACTION signal and apply the 'WACT::'
                // namespace to route it to handlers defined with #[WebAction].
                return [self::RT_ACTION, 'WACT::' . $action, $params, $envelopeSignal, $contentSignal];
            }

            // If web_app_data['data'] present, but parsing fails, treat it as an invalid action.
            return [self::RT_ACTION, '', [], $envelopeSignal, $contentSignal];
        }

        // PRIORITY 3: Callback action (next highest priority)
        // Fast path: direct normalized property
        $buttonId = $message->button_id ?? null;

        if (is_string($buttonId) && $buttonId !== '') {
            [$action, $params] = $this->parseActionPayload($buttonId);

            // If action payload is valid, namespace it to avoid collision with text routes
            if ($action !== null) {
                return [self::RT_ACTION, 'CBK::' . $action, $params, $envelopeSignal, $contentSignal];
            }

            // Invalid callback payload: still return action type with empty routing target
            // so the caller can decide strict fallback behavior.
            return [self::RT_ACTION, '', [], $envelopeSignal, $contentSignal];
        }

        // PRIORITY 4: Detect Inline Query Signal ✨ NEW
        if (isset($message->inline_query)) {
            return [
                self::RT_INLINE,      // The new Route Type for the signal
                $message->inline_query->query, // The payload is the query text itself
                [],                          // No action parameters initially
                $envelopeSignal, $contentSignal
            ];
        }

        // ✨✨✨ START: INTEGRATED SENSORY LOGIC ✨✨✨
        // Use the Signal Sensory Engine to detect message type.
        // =========================================================================
        // ✨ PRIORITY 5: DUAL-STRATEGY SENSORY ROUTING ✨
        // =========================================================================
        // This is the new, intelligent core. It correctly prioritizes envelope
        // events over simple content.

        // A) A meaningful Envelope event was detected. This is our primary signal.
        // An envelope signal is "meaningful" if it's different from the content signal,
        // indicating a specific event wrapper like 'edited_message' or 'poll_answer'.
        if ($envelopeSignal !== $contentSignal && $envelopeSignal !== Signal::Void) {
            $routingType = self::RT_SIGNAL;
            $routingPayload = 'TYPE::' . $envelopeSignal;
        
        // B) No specific envelope, so we use the Content signal.
        } elseif ($contentSignal !== Signal::Void) {
            if ($contentSignal === Signal::Text || $contentSignal === Signal::Command) {
                // It's a standard text message. Route as RT_TEXT for command/regex matching.
                $routingType = self::RT_TEXT;
                $routingPayload = $message->text ?? '';
            } else {
                // It's a media or other content type. Route as RT_SIGNAL for sensory matching.
                $routingType = self::RT_SIGNAL;
                $routingPayload = 'TYPE::' . $contentSignal;
            }
        }
        // ✨✨✨ END: INTEGRATED SENSORY LOGIC ✨✨✨
        
        // fallback - No recognizable or usable routing signal
        else {
            $routingType = self::RT_NONE;
            $routingPayload = '';
        }

        // Return the final resolved signal along with the pre-computed sensory data.
        return [$routingType, $routingPayload, [], $envelopeSignal, $contentSignal];
    }

    protected function parseActionPayload(string $payload): array
    {
        $parsed = $this->parseActionPayloadCore($payload);

        $transformed = JackPoint::transformParseAction($parsed, $payload, $this);

        return (is_array($transformed) && count($transformed) === 2)
            ? array_values($transformed)
            : $parsed;
    }
    /**
     * Unified callback payload parser (strict + flexible).
     *
     * Supported:
     * - "remove"
     * - "remove?id=123"
     * - "remove|id=123&sku=A1"
     * - "remove:123"                  => ['id' => '123']
     * - "remove:id=12,foo=bar"        => ['id' => '12', 'foo' => 'bar']
     *
     * Returns:
     * - [actionName, params] on success
     * - [null, []] on invalid payload
     *
     * @return array{0:?string,1:array<string,mixed>}
    */
    protected function parseActionPayloadCore(string $payload): array
    {
        $payload = trim($payload);

        // Hard guard (abuse protection)
        if ($payload === '' || strlen($payload) > 512) {
            return [null, []];
        }

        // --------------------------------------------------------------------
        // [NEW] Modern JSON Payload Strategy (Priority 1)
        // --------------------------------------------------------------------
        // Check if the payload looks like a JSON object.
        if (str_starts_with($payload, '{') && str_ends_with($payload, '}')) {
            $decoded = json_decode($payload, true);

            // If JSON is valid and contains an 'action' key...
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['action'])) {
                $actionName = $decoded['action'];
                // Remove 'CBK,WACT,WAPP,TYPE::' if it exists to avoid double prefixing later
                $actionName = str_replace(['CBK::', 'WACT::', 'WAPP::', 'TYPE::'], '', $actionName);               
                
                $params = $decoded;
                unset($params['action']); // The rest of the array becomes parameters, Keep only real data in params
                
                // The action name is returned as-is (e.g., "CBK::remove").
                // The remaining key-value pairs are the parameters (e.g., ['id' => 112]).
                return [$actionName, $params];
            }
        }

        // --------------------------------------------------------------------
        // [LEGACY] String-based Payload Strategy (Fallback)
        // --------------------------------------------------------------------
        // If it's not a valid JSON action, fall back to the old string parsing logic.
        // This ensures backward compatibility with older button formats.
        
        // Action name policy: strict whitelist
        $isValidAction = static fn(string $a): bool =>
            (bool) preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]{0,63}$/', $a);

        // Param key policy
        $isValidKey = static fn(string $k): bool =>
            (bool) preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]{0,63}$/', $k);

        // Param value policy
        $isValidVal = static fn(string $v): bool => strlen($v) <= 128;

        // Helper sanitizer for parsed arrays
        $sanitize = static function (array $raw) use ($isValidKey, $isValidVal): array {
            $out = [];
            foreach ($raw as $k => $v) {
                $k = trim((string) $k);
                if (!$isValidKey($k)) continue;

                if (is_array($v)) {
                    // Flatten one level to avoid parse_str array abuse
                    $v = implode(',', array_map(static fn($x) => (string)$x, $v));
                } else {
                    $v = trim((string) $v);
                }

                if (!$isValidVal($v)) continue;
                $out[$k] = $v;
            }
            return $out;
        };

        // A) plain action: "remove"
        if ($isValidAction($payload)) {
            return [$payload, []];
        }

        // B) query style: "remove?id=123"
        if (str_contains($payload, '?')) {
            [$action, $query] = explode('?', $payload, 2);
            $action = trim($action);
            if (!$isValidAction($action)) return [null, []];

            parse_str($query, $params);
            return [$action, is_array($params) ? $sanitize($params) : []];
        }

        // C) pipe-query style: "remove|id=123&sku=A1"
        if (str_contains($payload, '|')) {
            [$action, $query] = explode('|', $payload, 2);
            $action = trim($action);
            if (!$isValidAction($action)) return [null, []];

            parse_str($query, $params);
            return [$action, is_array($params) ? $sanitize($params) : []];
        }

        // D) colon style #1: "remove:123" => id=123
        // D) colon style #2: "remove:id=12,foo=bar"
        if (str_contains($payload, ':')) {
            [$action, $tail] = explode(':', $payload, 2);
            $action = trim($action);
            $tail = trim($tail);

            if (!$isValidAction($action)) return [null, []];
            if ($tail === '') return [$action, []];

            // If looks like key=value list
            if (str_contains($tail, '=')) {
                $params = [];
                foreach (explode(',', $tail) as $pair) {
                    $kv = explode('=', $pair, 2);
                    if (count($kv) !== 2) continue;

                    $k = trim($kv[0]);
                    $v = trim($kv[1]);

                    if (!$isValidKey($k) || !$isValidVal($v)) continue;
                    $params[$k] = $v;
                }
                return [$action, $params];
            }

            // Otherwise map as id
            if ($isValidVal($tail)) {
                return [$action, ['id' => $tail]];
            }

            return [null, []];
        }

        // Unknown format
        return [null, []];
    }

    protected function validateRouteInput(Route|array $route): bool
    {
        $routeAttributes = $route instanceof Route
            ? $route->attributes
            : ($route['attributes'] ?? []);

        $rules = $routeAttributes['_validation'] ?? [];

        // ⚡ ZERO-COST PATH
        if (empty($rules)) {
            return true;
        }

        $data = $this->currentRouteParams;

        // ---------------------------------------------------------------
        // 🌟 WILDCARD VALIDATION
        // #[Validate('*', 'required|min:3')]
        //
        // Validate every currently available top-level input parameter.
        //
        // The wildcard RuleSet is expanded once, then applied to every top-level input parameters of methods.
        // ---------------------------------------------------------------
        if (isset($rules['*'])) {
            $wildcardRules = is_array($rules['*'])
                ? $rules['*']
                : [$rules['*']];

            // Resolve RuleSets used by the wildcard.
            $wildcardRules = RuleSet::expand(
                $wildcardRules,
                $this->ruleSets
            );

            unset($rules['*']);

            foreach (array_keys($data) as $parameter) {
                $rules[$parameter] = array_merge(
                    $wildcardRules,
                    $rules[$parameter] ?? []
                );
            }
        }

        // ---------------------------------------------------------------
        // 📚 RESOLVE RuleSets PER PARAMETER
        //
        // Example:
        //
        // 'username' => ['required', 'rs:userName']
        //
        // becomes:
        //
        // 'username' => [
        //     'required',
        //     'required',
        //     'string',
        //     'min:3',
        // ]
        // ---------------------------------------------------------------
        foreach ($rules as $parameter => $parameterRules) {
            $parameterRules = is_array($parameterRules)
                ? $parameterRules
                : [$parameterRules];

            $rules[$parameter] = RuleSet::expand(
                $parameterRules,
                $this->ruleSets
            );
        }

        // ---------------------------------------------------------------
        // ⚖️ LARAVEL = THE JUDGE
        // ---------------------------------------------------------------
        $validator = Validator::make(
            $data,
            $rules
        );

        if ($validator->passes()) {
            return true;
        }

        $errors = $validator->errors();

        // ---------------------------------------------------------------
        // DECIDE THE RESPONSE REALM
        // ---------------------------------------------------------------
        $routeType = $route instanceof Route
            ? $route->type
            : ($routeAttributes['_route_type'] ?? null);

        $isWebResponse = in_array(
            $routeType,
            [
                self::RT_WEB,
                self::RT_WEB_APP,
                self::RT_WEB_PAGE,
                self::RT_WEB_ACTION,
            ],
            true
        );

        // ---------------------------------------------------------------
        // WEB WORLD → HTTP JSON
        // ---------------------------------------------------------------
        if ($isWebResponse) {
            $this->response(
                response()->json([
                    'message' => 'The given data was invalid.',
                    'errors'  => $errors->toArray(),
                ], 422)
            );

            return false;
        }

        // ---------------------------------------------------------------
        // BOT WORLD → REPLY + SEND
        // ---------------------------------------------------------------
        $this->reply(
            $errors->first()
        )->send();

        return false;
    }

    /**
     * ⚡ THE ULTIMATE DISPATCHER v5.4
     * Dispatches the route using Laravel's Service Container (App::call) or Native PHP.
     *
     * 💎 Capabilities (Merged & Enhanced):
     * 1. **Full Dependency Injection**: Injects Bot, Message, and Type-Hinted classes.
     * 2. **Smart Route Params**: Maps URL params like `{id}` directly to method arguments `$id`.
     * 3. **Context Awareness**: Injects data shared via `set()`/`setData()` into the method (Laravel only).
     * 4. **Robust Resolution**: Handles `[Class, Method]`, `'Class@Method'`, Closures, and Invokables.
     * 5. **Native Fallback**: Highly optimized fallback for non-Laravel environments.
     *
     * @param mixed $action The handler to execute (Closure, [Class, 'Method'], 'Class@Method', Invokable, etc.).
     * @param Message $message The incoming message object.
     * @param array $routeParams Captured parameters from the route pattern.
     * @return mixed Result of the executed action.
    */
    protected function callAction(mixed $action, ?Message $message = null, array $routeParams = []): mixed
    {
        // --- PHASE 0: SAFE MESSAGE RESOLUTION ---
        // Sacred Fallback: If no message is provided or explicitly passed as null, resolve from internal state
        $message ??= $this->thisMessage();

        // --- PHASE 1: RESOLVE CALLABLE & TARGET REFLECTION ---
        // This phase remains unchanged, its purpose is to identify the target action.
        $instance = null;
        $reflection = null;

        if (is_string($action) && str_contains($action, '@')) {
            $action = explode('@', $action, 2);
        }

        if (is_array($action) && isset($action[0], $action[1])) {
            $className = $action[0];
            $methodName = $action[1];
            $instance = is_object($className) 
                ? $className 
                : (function_exists('app') ? app($className) : new $className());
            $reflection = new ReflectionMethod($instance, $methodName);
        } elseif ($action instanceof Closure || is_callable($action)) {
            $instance = is_object($action) && !$action instanceof Closure ? $action : null;
            $reflection = new ReflectionFunction($action instanceof Closure ? $action : Closure::fromCallable($action));
        } else {
            throw new RuntimeException("Krubot Architect Error: Invalid action handler provided.");
        }

        // --- PHASE 2: ALCHEMICAL PAYLOAD & CONTEXT MERGING ---
        // This phase also remains unchanged, preparing the raw data for resolution.
        $contextData = property_exists($this, 'contextData') ? $this->contextData : [];

        $aliases = [
            // String Aliases for legacy or simple access
            'bot'          => $this,
            // 💎 ALCHEMICAL ALIASING: The Bridge to Legacy Dimensions
            'message'      => $message,

            // This single line solves the BindingResolutionException for legacy methods that use the parameter name `$msg` instead of type-hinting `Message $message`.
            'msg'          => $message,

            // Class Type-Hints (Enable: public function handle(Krubot $bot, Message $msg))
            self::class    => $this,
            static::class  => $this,
            Krubot::class  => $this,
            Message::class => $message, // Inject KrubiK\DTOs\Message

            // Activate Update-Marker by One Move !
            Update::class  => ((object) ($message->heart?->coreData ?? []))
        ];

        $payloadData = array_merge($aliases, $contextData, $routeParams); // $aliases < $cotext_data; makes it totally alive and injectable via get()/set() methods
        $extraInjects = [$this, $message];

        // 🟢 PATCH: Add support for Call Route with assoc-array $params
        // 🔥 INSERT THIS LINE HERE 🔥
        //     to Manually Inject the entire parameters array into a key named 'params'.
        $payloadData['params'] = $routeParams;

        // --- PHASE 3: DELEGATE TO THE UNIFIED INVOCATION ENGINE ---
        // The call now goes to the refactored invocation engine.
        return $this->invokeWithAutoWiring(
            method: $reflection,
            targetInstance: $instance,
            payloadData: $payloadData,
            extraInjects: $extraInjects
        );
    }

    /**
     * ⚡ The Universal Auto-Wirer v3.6.
     * 🚀 THE METAPHYSICAL AUTO-WIRING & INVOCATION ENGINE (REMASTERED)
     *
     * Resolves dependencies globally. Strictly types payload parameters to match method signatures.
     * This method is now a pure orchestrator, delegating resolution and execution.
     *
     * @param ReflectionMethod|ReflectionFunction $method The reflection of the target action.
     * @param ?object $targetInstance The instance of the class for method calls. (null if static/closure).
     * @param array $payloadData Payload for NAME-based injection.
     * @param array $extraInjects Payload for TYPE-based injection.
     * @return mixed The result of the invocation.
    */
    public function invokeWithAutoWiring(
        ReflectionMethod|ReflectionFunction $method,
        ?object $targetInstance = null,
        array $payloadData,
        array $extraInjects = []
    ): mixed {
        // Always run the sacred resolution logic first to gather all "blessings".

        // ⚡ THE DX FATALITY: Delegate to our Centralized Metaphysical Auto-Wirer!
        // --- THE UNIFIED DEPENDENCY RESOLUTION ---
        //
        // No more dumb call_user_func_array. No more guessing positional parameters.
        // We strictly type-cast and inject intelligently!
        // This returns an associative array of [parameterName => resolvedValue].
        $resolvedDependencies = $this->_resolveActionDependencies($method, $payloadData, $extraInjects);

        // --- STEP 2: CHOOSE THE EXECUTION PATH ---
        // Decide whether to use Laravel's powerful container or the native invoker.
        if (function_exists('app')) {
            // --- ROYAL ROAD: LEVERAGE LARAVEL'S IoC CONTAINER ---
            // The sacred payload is passed to Laravel's `call` method.
            // Laravel will use our pre-resolved parameters and will *also* resolve
            // any other dependencies (like Services, Repositories, Request object)
            // from its own container. This is the desired synergy!

            if ($method instanceof ReflectionMethod) {
                $callable = $targetInstance 
                    ? [$targetInstance, $method->getName()] 
                    : $method->getDeclaringClass()->getName() . '@' . $method->getName();
            } else {
                $callable = $method->getClosure();
            }
            
            // Let the cosmic forces of Laravel's IoC combine with our metaphysical payload.
            return app()->call($callable, $resolvedDependencies);
        }

        // --- RESILIENT PATH: NATIVE INVOCATION ---
        // If Laravel isn't present, use the native PHP invoker.
        // We must convert the associative array to a simple ordered array for invokeArgs.
        $orderedDependencies = array_values($resolvedDependencies);
        
        return $method->invokeArgs($targetInstance, $orderedDependencies);
    }

    /**
     * --- THE UNIFIED DEPENDENCY RESOLUTION ---
     *        THE SACRED SANCTUM 🏛️
     * 
     * Resolve all parameters of a reflected method into a concrete argument array.
     * 
     * This is the new, dedicated heart of our auto-wiring logic. It *always* runs.
     * It honors all sacred priorities and forges the final, definitive argument payload.
     *
     * Priority ladder (first match wins, continues to next parameter):
     *
     *   P1 · ExtraInjects  — caller-supplied objects matched by type (e.g. Answer DTOs)
     *   P2 · Core pillars  — Krubot/self, Message, Update — always available, zero cost
     *   P3 · JackPoint DI  — plugin injectors via JackPoint::injectParam()
     *                        (type-keyed first, name-keyed as fallback — scope-aware)
     *   P4 · Payload name  — $payloadData[$name] with automatic scalar casting
     *   P5 · Default value — parameter default from the method signature
     *   P6 · Nullable      — explicit null (object types deferred to app()->call())
     *
     * @param ReflectionMethod|ReflectionFunction $method The reflection of the target action.
     * @param array $payloadData The merged context and route data. (Parameters extracted from Route Regex or Action Payload.)
     * @param array $extraInjects Core objects for type-based injection, if requested. (like Answer DTOs)
     * @return array An associative array of [parameterName => resolvedValue].
     * @throws RuntimeException If a catastrophic dependency failure occurs, or a required parameter cannot be resolved.
    */
    protected function _resolveActionDependencies(
        ReflectionMethod|ReflectionFunction $method,
        array $payloadData,
        array $extraInjects = [],
        bool $discoverOnly = false // To be Utilized in The-:: enrichRoutePatternAndParams()
    ): array {

        if ($discoverOnly) {

            // نکته!!! این بلاک کد نمی‌گوید: «چطور این پارامتر را تزریق کنیم؟»
            // بلکه می‌گوید: «آیا این پارامتر چیزی است که Route باید از URL دریافت کند یا چیزی است که مکانیزم دیگری باید تأمینش کند؟»

            $routeParameters = [];
        
            foreach ($method->getParameters() as $parameter) {
                $name = $parameter->getName();
                $type = $parameter->getType();
        
                // Typed objects belong to DI, not route-space 🚫
                if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                    continue;
                }
        
                // Union/Intersection: enter route-space only if a builtin branch exists 🔀
                if (
                    $type instanceof ReflectionUnionType ||
                    $type instanceof ReflectionIntersectionType
                ) {
                    $hasBuiltin = false;
        
                    foreach ($type->getTypes() as $typePart) {
                        if ($typePart->isBuiltin()) {
                            $hasBuiltin = true; // One BuiltinType Enough for us!
                            break;
                        }
                    }
        
                    // Pure object compositions are dependency-only declarations 🛑
                    if (!$hasBuiltin) {
                        continue;
                    }
                }

                // بعد از اینکه پارامترهای واضحاً DI-Based را بر اساس Type آنها حذف کردیم :::        
                
                // مثلاً ممکن است چیزی مثل: public function show($userId) داشته باشی
                // & JackPoint از روی paramName/Attribute/Context تصمیم بگیرد :: این پارامتر توسط پلاگین inject شود
                if (JackPoint::injectParam($parameter, $payloadData, $this, $method) !== null) {
                    continue; // JackPoint Plugins gets the final veto before route exposure ⚡.
                }
        
                // Register the parameter and mark whether the route requires it 🧭.
                $routeParameters[$name] = !$parameter->isDefaultValueAvailable();
            }
        
            return $routeParameters;
        }

        // This is the logic you cherished, now enshrined in its own method.
        $dependencies = [];
        foreach ($method->getParameters() as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();
            $typeName = ($type instanceof ReflectionNamedType) ? $type->getName() : null;

            // PRIORITY 1: High-Priority Contextual Injects (from $extraInjects by Type)
            $injected = false;
            if ($typeName && !$type->isBuiltin()) {
                foreach ($extraInjects as $inject) {
                    if (is_object($inject) && ($typeName === get_class($inject) || is_subclass_of($inject, $typeName))) {
                        $dependencies[$name] = $inject;
                        $injected = true;
                        break;
                    }
                }
            }
            if ($injected) continue;

            // PRIORITY 2: SACRED CORE INJECTIONS (The Unshakable Pillars by Type)
            // Note: We use a switch for clarity and potential future expansion.
            switch ($typeName) {
                case self::class:
                case static::class:
                case Krubot::class: // Assuming Krubot is the only King.
                    $dependencies[$name] = $this;
                    continue 2; // continue the outer foreach loop
                case Message::class:
                    $dependencies[$name] = $payloadData['msg']; // Directly use the prepared message
                    continue 2;

                // Activate Update-Marker by Two Move ! DRY-Prob-lemz...
                case Update::class:
                    $dependencies[$name] = (object) ($payloadData['msg']->heart?->coreData ?? []);
                    continue 2;
            }

            // ── PRIORITY 3: JackPoint plugin DI — scope-aware, competing resolvers. ─────────────
            // injectParam() probes TYPE injectors first, NAME injectors as fallback.
            // Returns null when no plugin claims the parameter → we fall through.
            $injected = JackPoint::injectParam($parameter, $payloadData, $this, $method);
            if ($injected !== null) {
                $dependencies[$name] = $injected;
                continue;
            }

            // PRIORITY 4: Payload Data Injection (Implicit Model Binding & Scalar Casting)
            if (array_key_exists($name, $payloadData)) {
                $val = $payloadData[$name];

                // ⚡ SUPERCHARGED MODEL BINDING ⚡
                // اگر نوع پارامتر یک مدل Eloquent است و مقدار پاس داده شده یک شناسه (ID) است
                if ($typeName && !$type->isBuiltin() && is_subclass_of($typeName, \Illuminate\Database\Eloquent\Model::class)) {
                    
                    // اگر از قبل آبجکت مدل است، مستقیماً تزریق کن (Zero-cost)
                    if ($val instanceof $typeName) {
                        $dependencies[$name] = $val;
                        continue;
                    }

                    // اگر مقدار یک رشته/عدد است (مثل '12' از روت وب)
                    if (is_scalar($val) && $val !== '') {
                        
                        // 🔥 STATIC CACHE MEMORY 🔥
                        // حل دغدغه شما: متادیتای کلاس (مثل اسم PrimaryKey) فقط یک بار در طول کل چرخه عمر پردازش می‌شود.
                        static $modelMetaCache = [];
                        if (!isset($modelMetaCache[$typeName])) {
                            $dummy = new $typeName;

                            // Ultra-fast static caching of Model metadata (Primary Key & Capabilities) to save memory/CPU
                            $modelMetaCache[$typeName] = [
                                'routeKeyName' => method_exists($dummy, 'getRouteKeyName') ? $dummy->getRouteKeyName() : $dummy->getKeyName(),
                                'hasCustomResolve' => method_exists($dummy, 'resolveRouteBinding'),
                            ];
                        }

                        $meta = $modelMetaCache[$typeName];
                        $resolvedModel = null;

                        // واکشی از دیتابیس بر اساس شناسه کش شده
                        if ($meta['hasCustomResolve']) {
                            $resolvedModel = (new $typeName)->resolveRouteBinding($val);
                        } else {
                            /** @var \Illuminate\Database\Eloquent\Model $typeName */
                            $resolvedModel = $typeName::where($meta['routeKeyName'], $val)->first();
                        }

                        // اگر در دیتابیس پیدا شد، تزریق کن
                        // Inject resolved model in params
                        if ($resolvedModel) {
                            $dependencies[$name] = $resolvedModel;
                            continue;
                        }

                        // اگر پیدا نشد اما در پارامتر علامت سوال (?GamifyTimeRange) داشت، null بگذار
                        if ($parameter->allowsNull()) {
                            $dependencies[$name] = null;
                            continue;
                        }

                        // در غیر این صورت پرتاب ارور استاندارد لاراول (تبدیل به 404 در وب)
                        // Trigger proper 404 response for web or fail-gracefully for Bots
                        throw (new ModelNotFoundException)->setModel($typeName, [$val]);
                    }
                }

                // کستینگ اتوماتیک برای نوع‌های پایه (int, string, bool)
                // Automatic type casting for scalar types based on reflection.
                if ($type instanceof ReflectionNamedType && $type->isBuiltin()) {
                    $val = match ($type->getName()) {
                        'int'    => (int) $val,
                        'bool'   => filter_var($val, FILTER_VALIDATE_BOOLEAN),
                        'float'  => (float) $val,
                        'string' => (string) $val,
                        'array'  => is_array($val) ? $val : (json_decode((string) $val, true) ?? [(string) $val]),
                        default  => $val,
                    };
                }
                
                $dependencies[$name] = $val;
                continue;
            }

            // PRIORITY 5: Safe Fallbacks (Default Values & Nullables)
            if ($parameter->isDefaultValueAvailable()) {
                $dependencies[$name] = $parameter->getDefaultValue();
                continue;
            }
            if ($parameter->allowsNull()) {
                if ($typeName && !$type->isBuiltin()) {
                    continue; // Skip — let app()->call() handle this
                }
                $dependencies[$name] = null;
                continue;
            }

            // If all else fails, the universe cannot provide, and we must report it.
            throw new RuntimeException("Krubot Architect Error: Cannot resolve parameter [\${$name}] for [{$method->getName()}]. The cosmic energies are misaligned.");
        }

        return $dependencies;
    }

    // =========================================================================
    //  MERGED FEATURES & HELPERS
    // =========================================================================

    /**
     * ⚡ Helper to get the current message in Nexuses without passing it.
    */
    public function thisMessage(): ?Message
    {
        return $this->currentMessage;
    }

    public function findMessageId(): ?string
    {
        return $this->thisMessage()?->message_id ?? null;
    }

    public function findRepliedMessageId(): ?string
    {
        return $this->thisMessage()?->reply_to_message_id ?? null;
    }

    public function chatId()
    {
        return $this->thisMessage()?->chat_id ?? null;
    }

    public function text(): string
    {
        return $this->thisMessage()?->text ?? '';
    }

    public function user(): array
    {
        return [
            'id' => $this->thisMessage()?->sender_id ?? null,
            'username' => $this->thisMessage()?->user_name ?? null,
            'first_name' => $this->thisMessage()?->first_name ?? null        
        ];
    }

    /**
     * Download file from message to Laravel Storage.
    */
    public function downloadTo(string $fileId, string $path, string $disk = 'local'): bool
    {
        try {
            $url = $this->getFile($fileId);
            if (!$url) return false;

            $content = @file_get_contents($url);
            if ($content === false) return false;

            return Storage::disk($disk)->put($path, $content);
        } catch (\Throwable $e) {
            AmethystMatrix::error("Krubot Download Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if current message contains a file.
    */
    public function hasFile(): bool
    {
        $raw = $this->getUpdate();
        $newMsg = raw['update']['new_message'] ?? $raw['message'] ?? [];
        return isset($newMsg['file_inline']) || isset($newMsg['file_attachment']);
    }

    // =========================================================================
    //  ⚡ MERGED FROM Latest BotLaraGKTR (New Helper Methods)
    // =========================================================================

    /**
     * Shortcut to get just the Sender ID string.
    */
    public function senderId(): ?string
    {
        $id = $this->user()['id'] ?? null;
        return JackPoint::transform('resolve.identity.id', $id, $this->user(), $this);
    }

    /**
     * Shortcut to get just the Sender ID string.
    */
    public function who(): ?string
    {
        return $this->senderId();
    }

    /**
     * Get the cleaned text content (trimmed).
    */
    public function cleanText(): string
    {
        $text = trim($this->text());
        return JackPoint::transform('resolve.clean.text', $text, $this->text(), $this);
    }

    /**
     * Resolve the authoritative admin ID list for the current context.
     *
     * Priority (highest → lowest):
     * 1. AdminIds attribute on the matched Route (class + method OR-merged by scanner)
     * 2. Classic driver-specific config / .env fallback
     *
     * This is a pure override mechanism — it does NOT block execution.
     * It only changes who is considered "admin" for isAdmin() and broadcast helpers.
     *
     * @return list<string>
    */
    public function admin_ids(): array
    {
        // 1. Hyper-DX Attribute Override (zero config, zero JackPoint)
        if (
            $this->activeRoute &&
            $this->activeRoute instanceof Route &&
            !empty($this->activeRoute->adminIds)
        )
            return $this->activeRoute->adminIds;

        // 2. Classic config path (legacy + multi-driver support)
        $driver = $this->resolveTargetDriver();

        return config(
            "krubot.drivers.{$driver}.admin_ids",  // get admin ids for current platform
            [env('RUBIKA_ADMIN_GUID')]
        );
    }

    /**
     * Check if the update is from the Admin defined in .env
     * Add RUBIKA_ADMIN_GUID=... to your .env file.
    */
    public function isAdmin(?string $userId = null): bool
    {
        $adminGuids = $this->admin_ids();

        $adminGuids = JackPoint::transform('identity.admin.list', $adminGuids, $this);

        $senderId = $userId ?? $this->senderId(); // we checking for who ?!

        $isAdmin = $senderId && in_array($senderId, $adminGuids);
        
        return (bool) JackPoint::transform('identity.admin.check', $isAdmin, $senderId, $adminGuids, $this);
    }

    /**
     * Edit the current message immediately (Useful for updating Bot's own menus).
    */
    public function sendMessageToAdmins(string $text): array
    {
        $configAdminGuids = $this->admin_ids();

        $adminGuids    = JackPoint::transform('admin.broadcast.targets', $configAdminGuids, $text, $this);
        $resolvedText  = JackPoint::transform('admin.broadcast.text', $text, $adminGuids, $this);

        $verdict = JackPoint::fire('admin.broadcast.started', $adminGuids, $resolvedText, $configAdminGuids, $text, $this);
        if($verdict === false)
            return [];

        $result = [];
        foreach ($adminGuids as $admin_id) {
            $result []= $this->to($admin_id, $resolvedText);
        }

        JackPoint::fire('admin.broadcast.completed', $result, $adminGuids, $resolvedText, $this);

        return $result;
    }

    /**
     * Quick check if message matches a pattern (Exact or Regex).
     * Useful inside handlers for sub-logic.
    */
    public function matches(string $pattern): bool
    {
        $text = $this->cleanText();

        $pattern = JackPoint::transform('text.match.pattern', $pattern, $text, $this);

        // Exact match
        if ($text === $pattern) return true;

        // Regex match check (heuristic: starts/ends with /)
        if (str_starts_with($pattern, '/') && str_ends_with($pattern, '/')) {
             return (bool) preg_match($pattern, $text);
        }

        return false;
    }

    /**
     * Dual-purpose accessor for the currently active action.
     *
     *  - Called with no arguments  → returns the current value  (GETTER).
     *  - Called with any argument  → assigns the new value and
     *                                returns `$this` for chaining (SETTER).
     *
     * `func_num_args()` is used instead of a `null` check on purpose:
     * it allows `null` to be written explicitly without being mistaken
     * for a read request — a subtlety most naive implementations miss.
     *
     * @param  Route  $action  Optional payload to persist. Omit to read.
     * @return mixed|static    The stored action on read, `$this` on write.
     *
     * @example
     *   $warlord->currentAction();            // → current Route|null
     *   $warlord->currentAction($action);     // → $warlord  (chainable)
    */
    public function currentAction(Route $action = null): mixed
    {
        // ── GETTER ────────────────────────────────────────────────
        // Zero arguments means the caller only wants the truth.
        if (func_num_args() === 0) {
            return $this->activeRoute;
        }

        // ── SETTER ────────────────────────────────────────────────
        // Anything else is a write. Persist it, then hand back $this
        // so the call site can keep flowing (fluent API, jQuery vibes).
        $this->activeRoute = $action;

        return $this;
    }

    /**
     * Evolve Krubot by integrating a new set of traits and abilities.
     * This is a semantic alias for the mixin() method provided by the Macroable trait.
     * It allows for a more thematic and expressive way to add new capabilities.
     *
     * @param  object|string  $evolutionaryMatrix The class or object containing the new abilities.
     * @param  bool  $replace Replace conflicting abilities. Defaults to true.
     * @return void
     *
     * @throws \ReflectionException
     */
    public static function evolve($evolutionaryMatrix, bool $replace = true): void
    {
        JackPoint::fire('krubot.evolve.before', $evolutionaryMatrix, $replace);

        // This method Directly calls the Macroable::mixin() method that is inherited from the injected Macroable trait.
        // The power lies in its expressive and thematic name.
        static::mixin($evolutionaryMatrix, $replace);

        JackPoint::fire('krubot.evolve.after', $evolutionaryMatrix, $replace);
    }

    // پیاده‌سازی Krubot::for(): تک‌تیراندازِ خارج از متن
    // در حال حاضر، Krubot (ستون فقرات) معمولاً به آپدیت‌های دریافتی از وب‌هوک وابسته است تا بداند chat_id چیست. با متد استاتیک for، ما یک Instance جدید می‌سازیم و هدف را دستی به آن تزریق می‌کنیم.
    /**
     * Creates a targeted instance of Krubot for a specific user or chat.
     * This allows sending messages outside of the webhook request cycle (e.g., in Jobs or Console Commands).
     *
     * @param string $targetGuid The GUID of the user or group to target.
     * @return static
    */
    public static function for(string $targetGuid): static
    {
        // Resolve a fresh instance from the Laravel Service Container
        // This ensures all Traits and Dependencies are injected correctly.
        $instance = app(static::class); // Note! not compatible with singleton, will fix in ×v1×

        // Manually hydrate the internal state for the target
        // Assuming 'chatId' and 'userId' properties exist or are managed via a fluent setter.
        // Based on the architecture, we might need to expose a way to set these.
        
        // Injecting the target into the context
        $instance->forceContext($targetGuid);

        // 🔥 EVENT: krubot.spawned — plugins may hydrate extra context for off-cycle bots.
        JackPoint::fireKrubotSpawned($instance, $targetGuid);

        return JackPoint::transform('krubot.instance.ready', $instance, $targetGuid);
    }

    /**
     * Internal helper to force-set the context.
     * (Add this if specific setters don't exist in your Traits)
    */
    protected function forceContext(string $guid): void
    {

        $old_chat_id = $this->chat_id;
        $old_user_id = $this->user_id;

        $guid = JackPoint::transform('context.force', $guid, $this);

        $verdict = JackPoint::fire('context.forcing', $guid, $old_chat_id, $old_user_id, $this);
        if($verdict === false)
            return;

        // We set the chat ID as the primary target
        $this->chat_id = $guid;
        
        // If the GUID starts with 'u', it's a user, so we map it there too.
        if (str_starts_with($guid, 'u')) {
            $this->user_id = $guid;
        }

        JackPoint::fire('context.forced', $guid, $this);
    }

}
