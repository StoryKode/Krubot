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

use KrubiK\DTOs\Message;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Arrayable;
use KrubiK\Attributes\Name;
use KrubiK\Attributes\Action;
use KrubiK\Attributes\Middleware;
use KrubiK\Attributes\OnCommand;
use KrubiK\Attributes\OnText;
use KrubiK\Attributes\OnRegEx;
use KrubiK\Attributes\Receive;
use KrubiK\Attributes\When;
use KrubiK\Attributes\OnInlineQuery;
use KrubiK\Attributes\Fallback;
use KrubiK\Attributes\FallbackOn;
use KrubiK\Attributes\ForceJoin;
use KrubiK\Middlewares\ConversationMiddleware; // ⚡ Import Middleware
use KrubiK\WarLording\CommandOutcomeShifter;
use KrubiK\Router\Route; // ⚡ Import Route Class
use Illuminate\Support\Facades\Route as LaravelRoute; // ⚡ Import Laravel'z Route Class
use KrubiK\Drivers\Contracts\MultiverseEnforcer;
use KrubiK\Jobs\HandleDriverUpdate;
use KrubiK\DTOs\UniversalInboundUpdate;
use KrubiK\Arcane\Update; // Update-Marker to be catched in Receive(Singal::***)
use KrubiK\Render\RenderAura;
use KrubiK\Enums\Platform;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionNamedType;
use RuntimeException;
use Throwable;
use Countable;
use Closure;
use Traversable;

use KrubiK\Helpers\AmethystMatrix; // ⚡ Import the Sorceress

use KrubiK\WebApps\DTOs\WebRequest; // ⚡ Our Sacred WebRequest HyperDTO

use KrubiK\Facades\Opcache; // ✨ OpCaching came into the game ✨

use KrubiK\Arcane\InteractsWithContext; // ⚡ Import Context
use KrubiK\Arcane\InteractsWithApi;
use KrubiK\Arcane\HasWebInterface;
use KrubiK\Arcane\VanguardBuilder;
use KrubiK\Arcane\HasAmethystMatrix;
use KrubiK\Arcane\HasCommandGroups;
use KrubiK\Arcane\AdvancedRouting;
use KrubiK\Arcane\ProfessionalWarLordingToolkit;
use KrubiK\Arcane\SummonsCodeSpyz;
use KrubiK\Arcane\HasKeyboards;
use KrubiK\Arcane\CanSendFluentMessages;
use KrubiK\Arcane\CanPin;
use KrubiK\Arcane\CanManageChats;
use KrubiK\Arcane\CanManageMembers;
use KrubiK\Arcane\CanInitConversations;
use KrubiK\Arcane\CanPlayDiceGames;
use KrubiK\Arcane\PHPRBK_Methods;

use ReturnTypeWillChange;

/**
 * Krubot: The Miracler Edition ×release-candidate_0.8× (vObsidian-7)
 *
 * A Multi-Platform Orchestrator. This class does not contain any platform-specific API logic yet...
 * But It acts as a router and a proxy, delegating all platform
 * interactions to the appropriate driver.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version self: ×RC.8×
 * @music https://soundcloud.com/boombastixmusic/infected-mushroom-cities-of-the-future-boombastix-spiderage-remix-extended 🎧
 * @license MIT
*/
class Krubot implements Countable // ⚡️✅️⚡️
{
    use Macroable {
        __call as macroCall; // ⚡ Utilizing PHP+Laravel Power: Add methods dynamically at runtime
    }

    use VanguardBuilder;
    use InteractsWithContext;
    use InteractsWithApi;
    use HasWebInterface; // Empower Krubot to response & handle Mini-Apps / Web-Apps / Websites
    use AdvancedRouting;
    use SummonsCodeSpyz;
    use HasCommandGroups;
    use ProfessionalWarLordingToolkit; // Injects core(), prime(), driver(), via(), etc.
    use HasKeyboards;
    use CanSendFluentMessages;
    use CanPin;
    use CanManageChats;
    use CanManageMembers;
    use CanInitConversations;
    use CanPlayDiceGames;
    use PHPRBK_Methods;

    use HasAmethystMatrix; // ⚡ Inject Amethyst Powers

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
    private array $fallbackRegistry = [];
    
    // Routing signal types
    private const RT_ACTION     = 'action';
    private const RT_TEXT       = 'text';
    private const RT_REGEX      = 'regex';
    private const RT_COMMAND    = 'cmd';
    private const RT_TYPE       = 'type';    // ✨ NEW
    private const RT_INLINE     = 'inline';  // ✨ NEW

    private const RT_WEB        = 'web';   // ✨ NEW
    private const RT_WEB_APP    = 'web_app';   // ✨ NEW
    private const RT_WEB_PAGE   = 'web_page';   // ✨ NEW
    private const RT_WEB_ACTION = 'web_action'; // ✨ NEW

    private const RT_NONE    = 'none';

    // The constants RT_WEB_APP_DATA is now deprecated and removed
    // as its logic has been unified into the system differently.

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
    protected ?Route $currentResolvedHandler = null;

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
    protected MultiverseEnforcer $driver;

    /**
     * The Laravel application instance.
     * @var Application
    */
    protected Application $app;

    // =========================================================================
    //  🧠 THE SINGULARITY CACHE SYSTEM (O(1) Reflection Manifest)
    // =========================================================================

    /**
     * @var array<string, array{class_attributes: array, methods: array}>
     */
    private static array $reflectionManifestCache = [];

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
            return self::$reflectionManifestCache[$className];
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

        return $manifest;
    }


    // Properties for Krubot's core fluent builder
    protected ?string $text = null;
    protected ?string $chatId = null;
    protected ?string $replyToMessageId = null;

    /**
     * Global configuration passed during instantiation.
     * @var array
    */
    protected array $pwl_config = [];

    public function __construct(Application $app, MultiverseEnforcer $driver, string|array $config = null)
    {
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
    }

    public function __TheOldConstruct(string $token, array $config = [])
    {
        // Removed for LLM DeAmbiguousiaty...
    }
    
    /*
     *
     * Destructor to ensure we break the link in long-running processes.
     *
   */
    public function __destruct()
    {
        $this->sleepAmethystMatrix();
    }

    /* *
     * ⚡️ MAGIC PROXY TO THE DEFAULT DRIVER ⚡️
     *
     * Any method call that doesn't exist on Krubot (e.g., `reply`, `sendMessage`, `getMe`)
     * is automatically delegated to the CURRENT DEFAULT DRIVER.
     *
     * To use a non-default driver, you MUST explicitly use `core('alias')`.
     *
     * @param string $method The method name being called.
     * @param array $parameters The arguments for the method.
     * @return mixed
     * /
    public function __call($method, $parameters)
    {
        // Removed for LLM DeAmbiguousiaty...
    }
    // Note!
    // All methods like `integrateNexus`, `onCommand`, `onText`, `go`, `processUpdate`,
    // and `callAction` remain here. They form the "brain" of the application
    // and are platform-agnostic. The final action, like sending a message,
    // is done inside a handler by calling `$bot->reply()` or `$bot->core('tg')->say()`.

    /**
     * ⚡️ THE ULTIMATE MAGIC PROXY (Supreme Commander Edition) ⚡️
     *
     * This proxy is the heart of the Warlord. It intelligently routes method
     * calls based on the context set by the `via()` command center.
     *
     * @param string $method The method name.
     * @param array $parameters The method arguments.
     * @return mixed
     * /
    public function __call($method, $parameters)
    {
        // Removed for LLM DeAmbiguousiaty...
    } */

    /**
     * --------------------------------------------------------------------------
     * ⚙️ Global Outcome Wrapping Control
     * --------------------------------------------------------------------------
     * This property acts as a global switch to control whether method
     * call results are wrapped in a CommandOutcomeShifter object.
     *
     * @var bool Defaults to `false` to disable ``->then()_chaining`` by default.
    */
    public bool $wrapsInOutcomeShifter = false;

    /**
     * Globally disables the CommandOutcomeShifter wrapping mechanism.
     *
     * After calling this, all subsequent bot method calls will return the raw
     * result from the driver (e.g., an array, an int, or an exception).
     * This will disable the ->then() chaining capability.
     *
     * @return void
    */
    public function disableOutcomeWrapping(): void
    {
        $this->wrapsInOutcomeShifter = false;
    }
    public function DisableESPromiseMode(): void // switch to Normal Method Chaining
    {
        $this->wrapsInOutcomeShifter = false;
    }
    /**
     * Globally enables the CommandOutcomeShifter wrapping mechanism (default behavior).
     *
     * After calling this, all subsequent bot method calls will wrap their
     * results in a CommandOutcomeShifter object, enabling ->then() chaining.
     *
     * @return void
    */
    public function enableOutcomeWrapping(): void
    {
        $this->wrapsInOutcomeShifter = true;
    }
    public function EnableESPromiseMode(): void // switch to ES-Promises Like Chaining
    {
        $this->wrapsInOutcomeShifter = true;
    }
    /**
     * Globally toggles the CommandOutcomeShifter wrapping mechanism (default behavior).
     *
     * After calling this, all subsequent bot method calls will wrap their
     * results in a CommandOutcomeShifter object, enabling ->then() chaining.
     *
     * @return void
    */
    public function toggleOutcomeWrapping(): void
    {
        $this->wrapsInOutcomeShifter = !$this->wrapsInOutcomeShifter;
    }
    public function toggleESPromises(): void // toggle ECMASciprt_Like-Promises Chaining state
    {
        $this->wrapsInOutcomeShifter = !$this->wrapsInOutcomeShifter;
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
        return Signal::detect($msg, $prioritizeEnvelopeDetection);
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
            return $this->wrapsInOutcomeShifter
                ? (new CommandOutcomeShifter($this, $result))
                : $result;
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

            // Single, surgical strike -> Return CommandOutcomeShifter for ->then()
            if (count($aliases) === 1) {
                $result_maker = fn() => $driver->{$method}(...$parameters);
                $driver = $this->core(reset($aliases));
                return $this->wrapsInOutcomeShifter
                    ? CommandOutcomeShifter::execute($this, $result_maker)
                    : $result_maker();
            }
            /*try {
                $alias = $aliases[0];
                $result = $this->core($alias)->{$method}(...$parameters);
            } catch (\Throwable $e) {
                // The mission resulted in failure. Capture the exception as the outcome.
                $result = $e;
            }*/

            // PRIORITY 3: Default Driver Execution
            // All calls to the default driver are wrapped in CommandOutcomeShifter.
            $result_maker = fn() => $this->core()->{$method}(...$parameters);
            return $this->wrapsInOutcomeShifter
                ? CommandOutcomeShifter::execute($this, $result_maker)
                : $result_maker();

            // Wrap the single result in a CommandOutcomeShifter to enable `->then()` chaining.
            // return new CommandOutcomeShifter($this, $result);
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
        return $this->wrapsInOutcomeShifter
            ? (new CommandOutcomeShifter($this, $result))
            : $result;
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
    private array $integratedNexuses = [];
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
    private static array $nexusManifestCache = [];

    /**
     * Scans a "Nexus" (Controller/Logic Class) using the O(1) Manifest Engine and integrates it.
     * Rewritten for PHP 8.2.30 with Extreme DX & Zero Redundant Reflection.
     * This is the master reflection engine that automatically discovers and registers Routes,
     * injects Middlewares, and prepares handlers for execution.
     *
     * 💎 FUSED POWERS (v7.0 + v8.0 + v9.0 Ultimate + O(1) Manifest):
     * 1.  **Class-Level Middlewares:** Processes `#[Middleware(...)]` on the Nexus class itself.
     * 2.  **Method-Level Middlewares:** Processes `#[Middleware(...)]` on individual action methods.
     * 3.  **Smart Stack Assembly:** Merges middlewares with Nexus-level running BEFORE method-level.
     * 4.  **Fluent Route Configuration:** Leverages the full power of the Route object for chaining.
     * 5.  **Named Route Recognition:** Automatically detects `#[Name('...')]` for use with the `go()` method.
     * 6.  **Full DI Compatibility:** Prepares handlers for seamless execution via Laravel's Service Container.
     * 7.  **Robust Error Handling:** Provides precise, context-aware error logging on reflection failure.
     *
     * 📜 ویژگی‌های سینگولاریتی (v9.0 Ultimate):
     * - **پشتیبانی کامل از Middleware در دو سطح:** ابتدا میدل‌ورهای تعریف شده روی خودِ کلاس (Nexus) را استخراج می‌کند و سپس میدل‌ورهای روی متد را به آن اضافه می‌کند.
     * - **ادغام هوشمند (Smart Merging):** این دو آرایه را با هم ترکیب می‌کند (اول کلاس، بعد متد) تا ترتیب اجرا دقیقاً همانطور که انتظار می‌رود باشد.
     * - **یکپارچه‌سازی روان (Fluent Integration):** از خروجی متدهای onCommand و onText (که آبجکت Route هستند) استفاده کرده و میدل‌ورها و نام‌ها را مستقیماً با متدهای `->middleware()` و `->name()` به آن‌ها تزریق می‌کند.
     * - **مدیریت خطای مستحکم:** مدیریت خطای دقیق در صورت وجود نداشتن کلاس یا بروز مشکلات در حین Reflection.
     *
     * 🔮 Supported Attributes:
     * - #[OnCommand('/cmd')]
     * - #[OnText('Exact Text')]
     * - #[OnText('/Exact Text/i')]
     * - #[OnRegEx('/pattern/i')]
     * - #[OnRegEx('pattern')] // Auto-wraps to: '/pattern/'
     * - #[Middleware(['auth', 'log', Admin::class])]
     * - #[Name('my.route.name')]
     * - #[Action('button_payload')]
     * - #[Receive(Signal::Checkout)]
     * - #[Receive([Signal::Sticker, 'animation'])]
     * - #[OnInlineQuery]
     * - #[OnInlineQuery('/item\s+(.+)/')]
     * - #[OnInlineQuery('article:')]
     * + Many More Attributes... ✨️
     *
     * @param object|string $nexus The Nexus instance or its fully qualified class name to scan.
     * @return void
     */
    public function integrateNexus(object|string $nexus, bool $isSingleNexus = true): void
    {
        // 1. Resolve the Nexus Class Name efficiently
        $className = is_string($nexus) ? $nexus : get_class($nexus);

        /*
         * [LEGACY LOGIC REMOVED FOR PERFORMANCE - O(n) Array Scan]
         * if (in_array($className, $this->integratedNexuses, true)) { return; }
        */

        // [VIPER'S GIFT] O(1) performance for duplicate checks. Vastly superior to O(n) in_array.
        if (isset($this->integratedNexuses[$className])) {
            return;
        }

        //  Commander K. Order: Cache the default web access policy once to avoid
        //  repeated 'config()' calls inside the loops.
        if (WebApp::$systemDefaultAccessPolicy === null) {
            WebApp::$systemDefaultAccessPolicy = config('krubot.webapps.access_policy', 'strict');
        }

        try {
            // 🧠 The Magic: Get everything instantly! Build the Manifest ONCE per worker lifecycle.
            if (!isset(self::$nexusManifestCache[$className])) {
                $reflection = new ReflectionClass($className);
                if (!$reflection->isInstantiable()) return;

                $manifest = ['class_attributes' => [], 'methods' => []];

                // Cache Class-Level Attributes
                $manifest['class_attributes'] = [
                    Name::class       => $reflection->getAttributes(Name::class),
                    Middleware::class => $reflection->getAttributes(Middleware::class),
                    WebApp::class     => $reflection->getAttributes(WebApp::class),         // ✨ NEW
                    RestrictTo::class => $reflection->getAttributes(RestrictTo::class),     // ✨ NEW
                    ForceJoin::class  => $reflection->getAttributes(ForceJoin::class),
                ];

                // Cache Method-Level Attributes
                foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    $manifest['methods'][$method->getName()] = [
                        Middleware::class => $method->getAttributes(Middleware::class),
                        Name::class       => $method->getAttributes(Name::class),
                        OnCommand::class  => $method->getAttributes(OnCommand::class),
                        OnText::class     => $method->getAttributes(OnText::class),
                        OnRegEx::class    => $method->getAttributes(OnRegEx::class),
                        Receive::class    => $method->getAttributes(Receive::class),
                        When::class       => $method->getAttributes(When::class), // , \Attribute::IS_REPEATABLE
                        ForceJoin::class  => $method->getAttributes(ForceJoin::class),
                        Fallback::class   => $method->getAttributes(Fallback::class),      // ✨ NEW: Scan for the global fallback
                        FallbackOn::class => $method->getAttributes(FallbackOn::class),    // ✨ NEW: Scan for type-specific fallbacks
                        Action::class     => $method->getAttributes(Action::class),
                        WebApp::class     => $method->getAttributes(WebApp::class),      // ✨ NEW
                        WebPage::class    => $method->getAttributes(WebPage::class),     // ✨ NEW
                        WebAction::class  => $method->getAttributes(WebAction::class),   // ✨ NEW
                        RestrictTo::class => $method->getAttributes(RestrictTo::class),  // ✨ NEW,
                        OnInlineQuery::class => $method->getAttributes(OnInlineQuery::class)
                    ];
                }
                self::$nexusManifestCache[$className] = $manifest;
            }

            $manifest = self::$nexusManifestCache[$className];

            // -----------------------------------------------------------------
            // PHASE A: Extract Nexus-Level Middlewares (Global for this Nexus)
            // -----------------------------------------------------------------
            $nexusMiddlewares = [];
            foreach ($manifest['class_attributes'][Middleware::class] ?? [] as $cmWAttr) {
                // Merge supports multiple attributes: #[Middleware('A')] #[Middleware('B')]
                $nexusMiddlewares = array_merge($nexusMiddlewares, $cmWAttr->newInstance()->middlewares);
            }

            // ✨ NEW: Extract WebApp base name
            $webAppPrefix = isset($manifest['class_attributes'][WebApp::class][0]) ?
                $manifest['class_attributes'][WebApp::class][0]->newInstance()->name
            :
                null;

            // ✨ NEW LOGIC: Extract the class-level Name prefix for relative route naming.
            $nexusNamePrefix = isset($manifest['class_attributes'][Name::class][0]) ?
                $manifest['class_attributes'][Name::class][0]->newInstance()->name
            :
                null;

            // ✨ THE AMBASSADOR'S REPORT (CLASS-LEVEL) ✨
            // The Architect reads the class-level decrees from the RestrictTo ambassador.
            $nexusPlatformRestrictions = [];
            if (isset($manifest['class_attributes'][RestrictTo::class][0])) {
                /** @var \KrubiK\Attributes\RestrictTo $instance */
                $instance = $manifest['class_attributes'][RestrictTo::class][0]->newInstance();
                // The attribute itself resolves aliases and legions from the config file.
                $nexusPlatformRestrictions = array_merge($nexusPlatformRestrictions, $instance->getResolvedPlatforms());
            }

            // Ensure the final merged list from all class-level attributes is unique.
            // array_values is used to reset array keys for clean, predictable results.
            if (!empty($nexusPlatformRestrictions)) {
                $nexusPlatformRestrictions = array_values(array_unique($nexusPlatformRestrictions));
            }

            // ✨ THE CONDUIT'S CALLING (CLASS-LEVEL) ✨
            // The scanner now listens for the unifying call of ForceJoin at the Nexus level.
            $nexusForceJoinChannels = [];
            $nexusForceJoinFailMessage = null; // <- Variable to hold CLASS-level message
            foreach (($manifest['class_attributes'][ForceJoin::class] ?? []) as $forceJoinAttr) {

                // ForceJoin attribute is designed to be IS_REPEATABLE. We merge channels from all instances.
                // The attribute's constructor has already unified and cleaned its own data.
                $nexusForceJoinChannels = array_merge(
                    $nexusForceJoinChannels,
                    $forceJoinAttr->newInstance()->channels
                );

                // If a fail message is set, it overrides any previous one found at the CLASS level.
                if ($instance->failMessage !== null) {
                    $nexusForceJoinFailMessage = $instance->failMessage;
                }

            }
            // Final purification at the class level to handle overlaps between multiple attributes.
            if (!empty($nexusForceJoinChannels)) {
                $nexusForceJoinChannels = array_values(array_unique($nexusForceJoinChannels));
            }

            if ($webAppPrefix) {
                $webAppHandler = null;

                // Look for method names index() || handle() in a WebApp()Nexus

                if (isset($manifest['methods']['index'])) {
                    $webAppHandler = [$className, 'index'];
                } elseif (isset($manifest['methods']['handle'])) {
                    $webAppHandler = [$className, 'handle'];
                }

                if ($webAppHandler) {
                    $webAppAttrInstance = $manifest['class_attributes'][WebApp::class][0]->newInstance();
                    $finalPath = $this->_resolveRelativePathName($webAppAttrInstance->path ?? $webAppAttrInstance->name, null); // WebApp is top-level
                    
                    $route = $this->onWebApp(
                        $finalPath, 
                        $webAppHandler, 
                        $webAppAttrInstance->methods
                    );

                    // ✨ DECREE OF ENRICHMENT: Transfer the developer's choice to the Route object.
                    $route->autoEnrichPattern = $webAppAttrInstance->autoEnrich;
            
                    // Apply name if it exists, relative to nexus prefix
                    $finalName = $webAppAttrInstance->name ? $this->_resolveRelativePathName($webAppAttrInstance->name, $nexusNamePrefix) : null;
                    if ($finalName)
                        $route->name($finalName);
            
                    // We need to re-create a temporary configure closure here or refactor.
                    // For simplicity, let's configure it directly.
                    $route->middleware(array_merge($nexusMiddlewares)); // Add method-specific if handler method has middleware
                    if (!empty($nexusPlatformRestrictions)) {
                        $route->platforms($nexusPlatformRestrictions);
                    }

                    $classAccessPolicy = $webAppAttrInstance->accessPolicy ?? config('krubot.webapps.access_policy', 'strict');
                    if (method_exists($route, 'accessPolicy')) {
                        $route->accessPolicy($classAccessPolicy);
                    }

                    $this->handlerToRouteMap[implode('::', $webAppHandler)] = $route;
                    $webPathKey = substr($route->getPattern(), strpos($route->getPattern(), '::') + 2);
                    $this->webPathToRouteMap[$webPathKey] = $route;
                }
            }

            // PHASE B & C: Process Methods using the Manifest
            // We iterate over the pre-built manifest array containing ONLY methods with Attributes.
            foreach ($manifest['methods'] as $methodName => $attributesMap) {
                
                // -------------------------------------------------------------
                // PHASE B: Extract Action-Level Middlewares
                // -------------------------------------------------------------
                $methodMiddlewares = [];
                foreach ($attributesMap[Middleware::class] ?? [] as $mWAttr) {
                    // Merge supports multiple attributes: #[Middleware('A')] #[Middleware('B')]
                    $methodMiddlewares = array_merge($methodMiddlewares, $mWAttr->newInstance()->middlewares);
                }

                // -------------------------------------------------------------
                // PHASE C: The Architect's Decree - Consolidate Restrictions & Middlewares (The Stack Assembly)
                // -------------------------------------------------------------
                
                // Middlewares are a simple merge (Union).
                $finalMiddlewareStack = array_merge($nexusMiddlewares, $methodMiddlewares);

                // Perform intra-level merge (Union) for method restrictions
                /*
                $methodPlatformRestrictions = [];
                foreach ($attributesMap[RestrictTo::class] ?? [] as $attr) {
                    $methodPlatformRestrictions = array_merge($methodPlatformRestrictions, $attr->newInstance()->getPlatforms());
                }
                */                
                // ✨ THE AMBASSADOR'S REPORT (METHOD-LEVEL) ✨
                $methodPlatformRestrictions = [];
                if (isset($attributesMap[RestrictTo::class][0])) {
                    /** @var \KrubiK\Attributes\RestrictTo $instance */
                    $instance = $attributesMap[RestrictTo::class][0]->newInstance();
                    $methodPlatformRestrictions = array_merge($methodPlatformRestrictions, $instance->getResolvedPlatforms());
                }
                $methodPlatformRestrictions = array_values(array_unique($methodPlatformRestrictions));

                // ✨ THE CORRECT LOGIC ✨
                // The policy is a simple, powerful, optimistic MERGE (Union|OR).
                // We combine both lists and then find the unique values.
                $finalPlatformRestrictions = array_merge(
                    $nexusPlatformRestrictions,
                    $methodPlatformRestrictions
                );
                // Platform Guards follow the nuanced merging policy.
                $finalPlatformRestrictions = array_values(array_unique($finalPlatformRestrictions));
                // Ensure final array has clean keys.

                // ✨ NEW: Determine the Access Policy for this route
                // For now, we pull from global config. Later this can be Enhanced with an #[AccessPolicy] attribute.
                /// $finalAccessPolicy = config('krubot.webapps.access_policy', 'strict');

                // ==========================================================
                // === ⚡️ NEW LOGIC: PRE-COMPILE #[When] GUARDS ⚡️ ===
                // ==========================================================
                $whenGuardInstances = [];
                foreach ($attributesMap[When::class] ?? [] as $whenAttrReflection) {
                    // newInstance() is fast because our When constructor is optimized.
                    $whenGuardInstances[] = $whenAttrReflection->newInstance();
                }

                // ✨ THE CONDUIT'S FOCUS (METHOD-LEVEL & FINAL MERGE) ✨
                // Now we listen for the specific call of ForceJoin on the method itself.
                $methodForceJoinChannels = [];
                $methodForceJoinFailMessage = null; // <- Variable to hold METHOD-level message
                foreach ($attributesMap[ForceJoin::class] ?? [] as $forceJoinAttr) {
                    $methodForceJoinChannels = array_merge(
                        $methodForceJoinChannels,
                        $forceJoinAttr->newInstance()->channels
                    );

                    // The method's message is king. If set, it's the one we'll use.
                    if ($instance->failMessage !== null) {
                        $methodForceJoinFailMessage = $instance->failMessage;
                    }
                }

                // The final, sacred union: Class-level and Method-level channels are merged.
                // This creates the definitive list of channels for this specific route.
                $finalForceJoinChannels = array_merge(
                    $nexusForceJoinChannels,
                    $methodForceJoinChannels
                );
                $finalForceJoinChannels = array_values(array_unique($finalForceJoinChannels));

                // --- THE PRECEDENCE RULING ---
                // The final message is the method's message. If it's null, we use the class's message.
                $finalForceJoinFailMessage = $methodForceJoinFailMessage ?? $nexusForceJoinFailMessage;

                $handlerCallback = [$className, $methodName];

                // -------------------------------------------------------------
                // PHASE D: Route Identification & Configuration
                // -------------------------------------------------------------
                /// $routeName = isset($attributesMap[Name::class][0]) ? $attributesMap[Name::class][0]->newInstance()->name : null;

                // ✨ NEW LOGIC: Resolve the final route name using the new relative logic.
                $rawRouteName = isset($attributesMap[Name::class][0]) 
                    ? $attributesMap[Name::class][0]->newInstance()->name 
                    : null;                
                // If a name attribute exists, resolve it. Otherwise dont waste your power, it's null head.
                $routeName = $rawRouteName ? $this->_resolveRelativePathName($rawRouteName, $nexusNamePrefix) : null;

                // Step D.1: Dynamic Parameter Discovery & Pattern Enrichment Closure 🧠
                $enrichRoutePatternAndParams = function(Route $route) use ($className, $methodName) {
                    if (!$route) return;

                    $pattern = $route->getPattern();
                    
                    try {
                        $reflectionMethod = new ReflectionMethod($className, $methodName);
                        $requiredParamsToAppend = [];
                        $allPathParams = [];

                        // Match placeholders already declared in the route pattern (e.g. {productId} or {productId?})
                        preg_match_all('/\{([a-zA-Z0-9_]+)\??\}/', $pattern, $matches);
                        $existingPlaceholders = $matches[1] ?? [];

                        foreach ($reflectionMethod->getParameters() as $param) {
                            $paramName = $param->getName();
                            $paramType = $param->getType();

                            // Skip dependency-injected system classes (e.g. Krubot, Request)
                            if ($paramType instanceof ReflectionNamedType && !$paramType->isBuiltin()) {
                                continue;
                            }

                            // Handle union/intersection types of classes (skip if no primitive types are present)
                            if ($paramType instanceof ReflectionUnionType || $paramType instanceof ReflectionIntersectionType) {
                                $hasBuiltin = false;
                                foreach ($paramType->getTypes() as $type) {
                                    if ($type->isBuiltin()) {
                                        $hasBuiltin = true;
                                        break;
                                    }
                                }
                                if (!$hasBuiltin) {
                                    continue;
                                }
                            }

                            $allPathParams[] = $paramName;

                            if (!in_array($paramName, $existingPlaceholders, true)) {
                                // Only auto-append to path if the parameter is required (no default value)
                                if (!$param->isDefaultValueAvailable()) {
                                    $requiredParamsToAppend[] = $paramName;
                                }
                            }
                        }

                        // Append required implicit parameters to the pattern
                        if (!empty($requiredParamsToAppend)) {
                            $pattern = rtrim($pattern, '/');
                            foreach ($requiredParamsToAppend as $reqPam) {
                                $pattern .= '/{' . $reqPam . '}';
                                $existingPlaceholders[] = $reqPam;
                            }
                            $route->pattern = $pattern;
                        }

                        // Save identified path parameters onto the Route instance
                        $route->pathParameters = array_values(array_unique(array_merge($existingPlaceholders, $allPathParams)));

                    } catch (ReflectionException $e) {
                        // Fallback: extract placeholders from pattern directly if Reflection fails
                        preg_match_all('/\{([a-zA-Z0-9_]+)\??\}/', $pattern, $matches);
                        $route->pathParameters = $matches[1] ?? [];
                    }
                };

                // Step D.2: The Configuration Helper Closure 🛠 //To-Do:: Support PlatformRestricion Here
                $_configureRoute = function (?Route $route, ?string $accessPolicy) use ($routeName, $finalMiddlewareStack, $finalPlatformRestrictions, $whenGuardInstances, $finalForceJoinChannels, $finalForceJoinFailMessage, $enrichRoutePatternAndParams, $handlerCallback) {
                    if (!$route) return;

                    // [THE BRAIN] enrichmentation central decision point. Clean, simple, and powerful.
                    if (in_array($route->type, [self::RT_WEB_APP, self::RT_WEB_PAGE, self::RT_WEB_ACTION], true)) {

                        // Dynamically discover parameter needs and enrich the Route Pattern for Route registration
                        /// $enrichRoutePatternAndParams($route); // Apply enrichment HERE

                        // 🔥 THE NEW CENTRAL DECISION POINT 🔥
                        // Instead of checking the route type, we check the explicit `autoEnrichment` flag.
                        // This is the core of the new architecture: the developer's intent, carried from
                        // the attribute, directly controls the "magic" of route modification.
                        if ($route->autoEnrichPattern === true) {
                            $enrichRoutePatternAndParams($route);
                        } else {
                            // If enrichment is disabled, we still need to detect existing placeholders.
                            preg_match_all('/\{([a-zA-Z0-9_]+)\??\}/', $route->getPattern(), $matches);
                            $route->pathParameters = $matches[1] ?? [];
                        }

                        // ✨ NEW: Apply access policy passed as an argument, if the route object supports it.
                        if (method_exists($route, 'accessPolicy')) {
                            // Use the specific sent policy for THIS route, or fall back to system default

                            // ✨ OPTIMIZED: Use the pre-cached static property as the fallback.
                            // This avoids hitting the config system for every single web route.

                            $policyToApply = $accessPolicy ?? WebApp::$systemDefaultAccessPolicy;
                            $route
                                ->accessPolicy($policyToApply);

                        }

                        /// 3. REGISTER & BRIDGE using the FINAL pattern
                        /// $this->_registerAndBridgeHttpRoute($route, $httpMethods); // OBSOLETE, NOT NEEDED

                    }

                    if ($routeName) $route->name($routeName);
                    if (!empty($finalMiddlewareStack)) $route->middleware($finalMiddlewareStack);

                    // Only apply platform restrictions if the final merged list is not empty.
                    // An empty list means no restrictions were specified anywhere, so it's universally available in WarLord Grade.
                    if (!empty($finalPlatformRestrictions)) {
                        $route->platforms($finalPlatformRestrictions);
                    }

                    // Attach the pre-compiled guards to the Route object.
                    if (!empty($whenGuardInstances)) {
                        $route->guards($whenGuardInstances);
                    }

                    // ✨♥️ THE UNIFIED ENERGY IS CHANNELED ♥️✨
                    // We now endow the Route object with the final list of ForceJoin channels.
                    // The Dispatcher will later access this property to perform its magic.
                    if (!empty($finalForceJoinChannels)) {
                        $route->forceJoinChannels = $finalForceJoinChannels; // + ✨ این خط، انرژی را به مسیر تزریق می‌کند

                        // --- THE FINAL ASSIGNMENT ---
                        // We now burn the final message string onto the Route object itself.
                        $route->forceJoinMessage = $finalForceJoinFailMessage;

                    }

                    // Map Class::method key to the Route instance
                    if (is_array($handlerCallback) && count($handlerCallback) === 2) {
                        $handlerKey = $handlerCallback[0] . '::' . $handlerCallback[1];
                        $this->handlerToRouteMap[$handlerKey] = $route;
                    }

                    // Populate type-specific fast-lookup maps
                    switch ($route->type) {
                        case self::RT_COMMAND:
                            $this->commandToRouteMap[trim($route->getPattern(), '/')] = $route;
                            break;
                        case self::RT_WEB_APP:
                        case self::RT_WEB_PAGE:
                        case self::RT_WEB_ACTION:
                            // The pattern for web routes is prefixed, e.g., 'WAPP::game.dashboard'
                            $webPathKey = substr($route->getPattern(), strpos($route->getPattern(), '::') + 2);
                            $this->webPathToRouteMap[$webPathKey] = $route;
                            break;
                    }
                };

                // -------------------------------------------------------------
                // PHASE E: Attribute-Based Route Registration (Optimized Manifest Loop)
                // -------------------------------------------------------------

                /* 
                 * [LEGACY LOGIC REMOVED FOR PERFORMANCE - Heavy Reflection calls in Loop]
                 * foreach ($method->getAttributes(OnCommand::class) as $attribute) { ... }
                 * foreach ($method->getAttributes(OnText::class) as $attribute) { ... }
                 */

                foreach ($attributesMap[OnCommand::class] ?? [] as $attr) {
                    $_configureRoute($this->onCommand($attr->newInstance()->command, $handlerCallback));
                }

                foreach ($attributesMap[OnText::class] ?? [] as $attr) {
                    $_configureRoute($this->onText($attr->newInstance()->pattern, $handlerCallback));
                }

                foreach ($attributesMap[OnRegEx::class] ?? [] as $attr) {
                    $pattern = $attr->newInstance()->pattern;
                    if (!preg_match('/^\/.*\/[a-zA-Z]*$/', $pattern)) {
                        $pattern = '/' . $pattern . '/';
                    }
                    $_configureRoute($this->onText($pattern, $handlerCallback));
                }

                // Handle #[Receive] Attribute 👁️
                foreach ($attributesMap[Receive::class] ?? [] as $instance) {
                    // Extract the types (it can be string or array in the Attribute)
                    $targetTypes = $instance->frequency; 
                    
                    // The onType method natively supports both string and array returns
                    $resultingRoutes = $this->onType($targetTypes, $handlerCallback);
                    
                    // If it returned an array of Routes (multi-type), configure all of them
                    if (is_array($resultingRoutes)) {
                        foreach ($resultingRoutes as $r) $_configureRoute($r);
                    } else {
                        $_configureRoute($resultingRoutes);
                    }
                }

                // ✨ NEW: Handle #[OnInlineQuery] Attribute ⚡️
                foreach ($attributesMap[OnInlineQuery::class] ?? [] as $attr) {
                    /** @var \KrubiK\Attributes\OnInlineQuery $instance */
                    $instance = $attr->newInstance();
                    // We call our new, intelligent public method.
                    // This keeps the logic centralized and the scanner clean.
                    $_configureRoute($this->onInlineQuery($instance->pattern, $handlerCallback));
                }

                // ✨ NEW: Handle #[Fallback] Attribute (Global)
                // This attribute does not create a route, it registers a special handler.
                if (isset($attributesMap[Fallback::class][0])) {
                    // The last detected Fallback handler wins.
                    // Consider adding a warning if this is set more than once.
                    
                    // Centralize the logic and makes the scanner's job simpler.
                    $this->fallback($handlerCallback);
                }

                // ✨ NEW: Handle #[FallbackOn] Attribute (Type-Specific)
                foreach ($attributesMap[FallbackOn::class] ?? [] as $attr) {
                    /** @var \KrubiK\Attributes\FallbackOn $instance */
                    $instance = $attr->newInstance();

                    /// Update ✨ Register with priority instead of blind overwriting
                    /// foreach($instance->types as $type) $this->typeFallbackHandlers[$type] = $handlerCallback;

                    // Delegate the fallbacks registration and priority logic to the dedicated helper.
                    // This is the epitome of clean architecture.
                    $this->fallbackOn(
                        $instance->types, 
                        $handlerCallback, 
                        $instance->priority
                    );
                }

                foreach ($attributesMap[Action::class] ?? [] as $attr) {
                    // ⚡ [FIXED]: Action uses 'name' not 'command'.
                    $_configureRoute($this->onAction($attr->newInstance()->name, $handlerCallback)); 
                }

                // ✨ NEW: Handle #[WebApp] Attribute (IS_REPEATABLE / multi-url mapping)
                foreach ($attributesMap[WebApp::class] ?? [] as $attrInstance) {
                    /** @var \App\Attributes\WebPage $instance */
                    $instance = $attrInstance->newInstance();
                    $path = $instance->path;
                    $methods = $instance->methods;

                    $route = $this->onWebApp($path, $handlerCallback, $methods);

                    // ✨ DECREE OF ENRICHMENT: Transfer the flag from attribute to Route instance.
                    $route->autoEnrichPattern = $instance->autoEnrich;

                    // Read policy from the specific attribute data and pass it to the _configureRoute closure
                    $routeSpecificAccessPolicy = $instance->getAccessPolicy();
                    $_configureRoute($route, $routeSpecificAccessPolicy);
                }

                // ✨ NEW: Handle Register WebPage Routes (IS_REPEATABLE / multi-url mapping)
                foreach ($attributesMap[WebPage::class] ?? [] as $attr) {
                    /** @var \App\Attributes\WebPage $instance */
                    $instance = $attr->newInstance();
                    $finalPath = $this->_resolveRelativePathName($instance->path ?? $instance->name, $webAppPrefix); // Smart Path Resolution: Prepend prefix if path is relative (starts with '.')
                    
                    $route = $this->onWebPage($finalPath, $handlerCallback, ['methods' => $instance->methods]);

                    // ✨ DECREE OF ENRICHMENT: Transfer the flag from attribute to Route instance.
                    $route->autoEnrichPattern = $instance->autoEnrich;

                    // Apply name if it exists, relative to nexus prefix
                    $finalName = $instance->name ? $this->_resolveRelativePathName($instance->name, $nexusNamePrefix) : null;
                    if ($finalName)
                        $route->name($finalName);

                    // Read policy from the specific attribute data and pass it to the _configureRoute closure
                    $routeSpecificAccessPolicy = $instance->getAccessPolicy();
                    $_configureRoute($route, $routeSpecificAccessPolicy);
                }

                // ✨ NEW: Handle Register WebAction Routes (IS_REPEATABLE / multi-url mapping)
                foreach ($attributesMap[WebAction::class] ?? [] as $attr) {
                    /** @var \KrubiK\Attributes\WebAction $instance */
                    $instance = $attr->newInstance();
                    $finalPath = $this->_resolveRelativePathName($instance->getName(), $webAppPrefix); // Smart Path Resolution: Prepend prefix if path is relative (starts with '.')
                    
                    $route = $this->onWebAction($finalPath, $handlerCallback, $instance->getMethods(), ['description' => $instance->getDescription()]);

                    // ✨ DECREE OF ENRICHMENT: Transfer the flag from attribute to Route instance.
                    $route->autoEnrichPattern = $instance->autoEnrich;

                    // Apply name if it exists, relative to nexus prefix
                    $finalName = $this->_resolveRelativePathName($instance->getName(), $nexusNamePrefix);
                    if ($finalName)
                        $route->name($finalName);

                    // Read policy from the specific attribute data and pass it to the _configureRoute closure
                    $routeSpecificAccessPolicy = $instance->getAccessPolicy();
                    $_configureRoute($route, $routeSpecificAccessPolicy);
                }
            }

            // ONLY After all nexuses have been scanned, resolve the priorities.
            if($isSingleNexus)
                $this->prioritizeFallbacks();

            // [CRITICAL FIX] Mark as integrated *after* successful processing.
            $this->integratedNexuses[$className] = true;

        } catch (\ReflectionException $e) {
            // Critical Error Handling:
            AmethystMatrix::yell("Nexus Integration Failed: The Singularity Engine encountered a critical reflection error.", [
                'nexus_target' => $className,
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
    }

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
        $opcacheMasterSwitch = config('krubot.cache.opcache_enabled', true);
        $opcacheGlobalRefreshSwitch = config('krubot.cache.opcache_refresh_on_discover', false);

        // ✨ PLUS: LOAD THE ENTIRE STRATEGIC CONFIGURATION ✨
        $forceList = config('krubot.cache.force_refresh', []);
        $excludeList = config('krubot.cache.exclude_from_refresh', []);
        $basePath = base_path() . DIRECTORY_SEPARATOR;

        // 3. The Great Scan: Iterate over every single PHP file found.
        foreach ($phpFiles as $phpFile) {
            /** @var \SplFileInfo $phpFile */
            $realPath = $phpFile->getRealPath();

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
    private function extractFqcnFromFile(string $filePath): ?string
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
        return $this->addRoute($command, $handler, $attributes + ['_route_type' => self::RT_COMMAND]);
    }

    /**
     * Define a Text Route (Exact match, Regex, or Parameterized).
     * Usage: $bot->onText('Hello', ...); OR $bot->onText('/^Hi$/i', ...);
    */
    public function onText(string $pattern, array|callable $handler, array $attributes = []): Route
    {
        return $this->addRoute($pattern, $handler, $attributes + ['_route_type' => self::RT_TEXT]);
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

        return $this->onText($pattern, $handler, $attributes + ['_route_type' => self::RT_REGEX]);
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
        return $this->addRoute('CBK::' . $payload, $handler, $attributes + ['_route_type' => self::RT_ACTION]);
    }

    /**
     * Register callback action route.
     * Example: $bot->onAction('remove', [CartNexus::class, 'remove']);
    */
    public function onAction(string $action, array|callable $handler, array $attributes = []): Route
    {
        // Internal namespaced pattern to avoid collision with normal text
        return $this->addRoute('CBK::' . $action, $handler, $attributes + ['_route_type' => self::RT_ACTION]);
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

        return $this->addRoute($processedPattern, $handler, $attributes + ['_route_type' => self::RT_INLINE]);
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
                    '_route_type' => self::RT_TYPE,
                    '_signal_class' => $isEnvelope 
                ];

                // Internal namespaced pattern 'TYPE::photo' to avoid collision with normal text
                $createdRoutes[] = $this->addRoute('TYPE::' . strtolower($type), $handler, $finalAttributes);
            }
            return $createdRoutes;
        }

        // ✨ 1B. INTELLIGENCE: Classify the nature of the single type.
        $isEnvelope = Signal::isEnvelopeFrequency($types);

        // ✨ 2B. ENRICHMENT: Prepare the final attributes with the strategy flag.
        $finalAttributes = $attributes + [
            '_route_type' => self::RT_TYPE,
            '_signal_class' => $isEnvelope, // The crucial flag is now stored!
        ];

        // Single Type Registration
        // ✨ 3. PERSISTENCE: Register the route with the enriched attributes.
        // The Route object now permanently holds the correct detection strategy.
        return $this->addRoute('TYPE::' . strtolower($types), $handler, $finalAttributes);
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
        return $this->addRoute('WAPP::' . $path, $handler, $attributes + ['_route_type' => self::RT_WEB_APP]);
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
        return $this->addRoute('WACT::' . $path, $handler, $attributes + ['_route_type' => self::RT_WEB_ACTION]);
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
        return $this->addRoute('WAPP::' . $path, $handler, $attributes + ['_route_type' => self::RT_WEB_PAGE]);
    }

    /**
     * Resolves a potentially relative attribute name against a class-level prefix.
     *
     * @param string $name The name from the method attribute (e.g., '.show_product').
     * @param string|null $prefix The name from the class attribute (e.g., 'game.dashboard').
     * @return string The fully resolved name (e.g., 'game.dashboard.show_product').
     */
    private function _resolveRelativePathName(?string $name, ?string $prefix): string
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
                // Concatenate prefix and the name (without the leading dot).
                return $prefix . ltrim($name, '.');
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
    protected function addRoute(string $pattern, mixed $handler, array $attributes = []): Route
    {
        // Apply Group Attributes (Prefix, Middlewares)
        $attrs = $this->getGroupAttributes();
        
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
        
        // Store in routes array
        $this->routes[$pattern] = $route;

        // ⚡ NAME REGISTRAR BRIDGE:
        // We pass a name string to the Route object via $attrs['route_name']. When $route->name('xyz') is called,
        // this closure fires and registers the route in our fast lookup table ($this->namedRoutes).

        if(isset($attributes['route_name']))
            $this->namedRoutes[$attributes['route_name']] = $route;
        
        // Track for group chaining ($bot->group()->middleware())
        $this->registerRouteToGroup($route);
        
        return $route;
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
    public function currentResolvedHandler(): ?Route
    {
        return $this->currentResolvedHandler;
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
        $this->fallbackHandler = $handler;
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
            $this->fallbackRegistry[$type][$priority] = $handler;
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

    // Add this new method to your Krubot.php class
    /**
     * =========================================================================
     *  вҡЎ ON-DEMAND POLLING TRIGGER
     * =========================================================================
     *
     * Fetches all pending updates from the Rubika API via 'getUpdates'
     * and processes each one sequentially through the main routing engine.
     * Ideal for Cron Jobs or webhook-less environments.
     *

     * @return array With The number of messages processed.
    * /
    public function processPendingUpdatesOld(): array
    {
        // Removed for LLM DeAmbiguousiaty...
    } */

    /**
     * =========================================================================
     *  ⚡ ON-DEMAND POLLING TRIGGER (Sovereign Edition v6.0)
     * =========================================================================
     *
     * Fetches all pending updates via 'getUpdates' and dispatches them
     * to the Queue Architecture using the "Driver Identity Protocol".
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
        // We use the driver's internal API client to fetch updates.
        $apiResponse = $this->newApiRequest('getUpdates');

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

        // =====================================================================
        // 🏛️ MUSEUM OF LEGACY CODE (SYNC MODE ARCHIVE)
        // =====================================================================
        // The code below is the OLD Synchronous way (blocking).
        // Kept for reference or emergency fallback debugging.

        // Removed for LLM DeAmbiguousiaty...
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
     * Resolve the primary routing signal AND pre-compute all sensory data.
     * This is the unified "Sensory Command Center" of the engine.
     * It determines the primary signal type/payload and also provides the pre-computed
     * envelope and content signals to the main processing loop, eliminating redundant calculations.
     *
     * @return array{
     *   0: string, // routingType (e.g., self::RT_TEXT, self::RT_TYPE)
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
    private function resolveRoutingSignal(Message $message): array
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

            return [self::RT_WEB, $webRequest->path, $webRequest->body->all(), $envelopeSignal, $contentSignal];

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
            $routingType = self::RT_TYPE;
            $routingPayload = 'TYPE::' . $envelopeSignal;
        
        // B) No specific envelope, so we use the Content signal.
        } elseif ($contentSignal !== Signal::Void) {
            if ($contentSignal === Signal::Text) {
                // It's a standard text message. Route as RT_TEXT for command/regex matching.
                $routingType = self::RT_TEXT;
                $routingPayload = $message->text ?? '';
            } else {
                // It's a media or other content type. Route as RT_TYPE for sensory matching.
                $routingType = self::RT_TYPE;
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
    private function parseActionPayload(string $payload): array
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

    /**
     * Resolves a dynamic, potentially translatable message string.
     * This is the generic implementation of the Commander's HyperDX message pattern.
     *
     * @param null|string $message The raw message string (e.g., 'Hello', '::key|fallback').
     * @param string $default The default message or translation key if $message is null.
     * @return string The final, resolved message.
    */
    private function resolveAndTranslateMessage(?string $message, string $default): string
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
    private function createPlatformAwareJoinButtons(array $channels): array
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

    private const JUDGE_RESULT_PARDON = 1;
    private const JUDGE_RESULT_HALT = 2;
    private const JUDGE_NOT_FOUND = 3;

    /**
     * The Supreme Judge Tribunal.
     * Centralizes the logic for finding, summoning, and interpreting a custom "Judge" method.
     *
     * @param Route $route The context route.
     * @param null|string $judgeDirective The message string, potentially starting with '.' to signify a Judge.
     * @param array $customPayload The specific evidence payload for this case (e.g., channels or guard info).
     * @return int Returns JUDGE_RESULT_PARDON on pardon, JUDGE_RESULT_HALT on halt, JUDGE_NOT_FOUND if no Judge was invoked.
    */
    private function tryInvokeJudge(Route $route, ?string $judgeDirective, array $customPayload = []): int
    {
        // If there's no directive or it doesn't start with the Judge sigil, court is not in session.
        if ($judgeDirective === null || !str_starts_with($judgeDirective, '.')) {  // Changed to DOT Notation ;)
            return self::JUDGE_NOT_FOUND; // Case dismissed, proceed to default sentencing.
        }

        // --- THE JUDGE'S CHAMBERS (NEW PARADIGM) ---
        $methodName = substr($judgeDirective, 1);
        $action = $route->getAction();

        // X-1-X :: EXTRACT THE CONTROLLER'S BLUEPRINT from the Route's action
        $controllerClass = null;
        if (is_array($action) && is_string($action[0])) {
            $controllerClass = $action[0];
        } elseif (is_string($action) && str_contains($action, '@')) {
            $controllerClass = explode('@', $action, 2)[0];
        }

        // We can only summon a Judge if it resides within a class-based controller.
        // A Closure route has no class context for the Judge to exist in.
        if (!$controllerClass) {
            return self::JUDGE_NOT_FOUND;
        }

        // X-2-X :: SUMMON THE CONTROLLER INSTANCE for this judgment.
        // We follow the sacred rule: use Laravel's container if available, otherwise new.
        $controllerInstance = function_exists('app') ? app($controllerClass) : new $controllerClass();

        // X-3-X :: VERIFY THE JUDGE'S EXISTENCE on the summoned controller.
        // Does the designated Judge (custom method) exist in the current Nexus?
        if (method_exists($controllerInstance, $methodName)) {

            // --- THE JUDGEMENT ---
            // We summon the Judge, pass it the evidence (required channels),
            // and capture its final, binding verdict.
            /// $verdict = $controllerInstance->{$methodName}($route->forceJoinChannels);

            // X-4-X :: Forge the Reflection of the Judge's method.
            // This is the key to unlocking the auto-wiring engine.
            $reflection = new \ReflectionMethod($controllerInstance, $methodName);

            // The base payload, always available to any Judge.
            // We pass the standard context, making the handler a first-class citizen.
            $basePayload = [
                'bot'     => $this,
                'message' => $this->thisMessage(),
                'msg'     => $this->thisMessage(),
            ];

            // X-5-X :: Prepare the payload for the KRUBOT-DI engine.
            // We provide not just the custom data, but also the context of the current request.
            // THE CRITICAL EVIDENCE injected via $customPayload:
            // Any developer's custom handler can now type-hint forExample:: `array $requiredChannels`.
            // Merge the specific evidence with the standard context.
            $finalPayload = array_merge($customPayload, $basePayload);
            
            // X-6-X :: Invoke the Judge using the framework's own sacred engine.
            // This is no longer a simple call; it's a DI-powered invocation.
            $verdict = $this->invokeWithAutoWiring(
                method: $reflection,
                targetInstance: $controllerInstance, // Correctly using the summoned instance.
                payloadData: $finalPayload,
                extraInjects: [$this, $this->thisMessage()]
            );

            // Return the final verdict: true for pardon, false for halt.
            return ($verdict === true) ? self::JUDGE_RESULT_PARDON : self::JUDGE_RESULT_HALT;
        }

        // The specified Judge was not found on the controller.
        return self::JUDGE_NOT_FOUND;
    }

    /**
     * Evaluates the #[When] guards for a given route candidate.
     * This is the new gatekeeper, called INSIDE the main routing loop.
     * It enables "continue-on-fail" logic.
     *
     * @param Route $route The route object to check.
     * @param Message $message The current message context.
     * @return bool Returns true if all guards pass, false otherwise.
    */
    protected function evaluateRouteGuards(Route $route, Message $message): bool
    {
        // === HYPER-PERFORMANCE PATH (NO REFLECTION) ===
        $whenGuards = $route->getGuards();

        // If there are no guards, the way is clear.
        // If there are no #[When] attributes, this entire logic block is skipped instantly.
        // Zero performance cost for unguarded methods.
        if (empty($whenGuards)) {
            return true;
        }
        
        // --- The Guardian Logic ---
        // Optimization: Fetch the UserStorage driver instance only once.
        $userStorage = $this->userStorage();

        // Micro-cache: If a method has multiple attributes for the same state key,
        // (e.g., #[When('>level', 10)] and #[When('<level', 50)]),
        // we hit the storage only ONCE for that key per request.
        $stateKeyCache = []; // Cache is now localized to this check

        foreach ($whenGuards as $when) {
            /** @var \KrubiK\Attributes\When $when */
            // newInstance() is fast now, thanks to our optimized, non-reflection constructor.
            $conditionMet = false;
            $key = $when->stateKey;
            
            // Use the per-request cache to avoid redundant storage hits (Cache-First).
            if (!array_key_exists($key, $stateKeyCache)) {
                $exists = $userStorage->has($key);
                $stateKeyCache[$key] = [
                    'exists' => $exists,
                    'value'  => $exists ? $userStorage->get($key) : null,
                ];
            }
            
            $stateExists = $stateKeyCache[$key]['exists'];
            $actualValue = $stateKeyCache[$key]['value'];

            // This logic is designed based on our strict, predictable `When` attribute rules.
            if (!$when->hasExpectedValue) {
                // Case: #[When('state')] -> Pure existence check.
                // The state must exist AND must not have been flushed to null.
                $conditionMet = ($stateExists && $actualValue !== null);
            } else {

                // Case: Attribute has an expectedValue, e.g., #[When('state', 123)] or #[When('>level', 10)]
                $expectedValue = $when->expectedValue;

                switch ($when->operator) {
                    case '=':
                        // Passes if the actual value is strictly equal to the expected one.
                        // This correctly handles #[When('state', null, 'msg')] because
                        // a non-existent state's actualValue is null, so null === null passes.
                        $conditionMet = ($actualValue === $expectedValue);
                        break;
    
                    case '!':
                        // Passes if the actual value is NOT equal.
                        // If the state doesn't exist, its value is null, which is not equal
                        // to any non-null expected value, so the condition correctly passes.
                        $conditionMet = ($actualValue !== $expectedValue);
                        break;
    
                    case '>':
                        // Type-safe numeric comparison. Prevents errors and weird PHP type juggling.
                        $conditionMet = $stateExists && is_numeric($actualValue) && is_numeric($expectedValue) && ($actualValue > $expectedValue);
                        break;
    
                    case '<':
                        // Type-safe numeric comparison.
                        $conditionMet = $stateExists && is_numeric($actualValue) && is_numeric($expectedValue) && ($actualValue < $expectedValue);
                        break;
    
                    case '~': // IN array
                        // Type-safe "in_array" check. Fails safely if developer provides a non-array.
                        $conditionMet = $stateExists && is_array($expectedValue) && in_array($actualValue, $expectedValue, true);
                        break;
    
                    case '×': // NOT IN array
                        // Type-safe "not in_array" check.
                        $conditionMet = $stateExists && is_array($expectedValue) && !in_array($actualValue, $expectedValue, true);
                        break;
                }

            }

            if (!$conditionMet) {

                // A condition was not met. We must stop and potentially send a message.
                $failMessage = $when->failMessage;

                // --- THE JUDGE'S SUMMONS (NOW CENTRALIZED) ---

                if ($failMessage !== null) {

                    $payload = [
                        'guard'       => $when,
                        'stateKey'    => $when->stateKey,
                        'actualValue' => $actualValue,
                    ];
                    $verdict = $this->tryInvokeJudge($route, $failMessage, $payload);

                    if ($verdict === self::JUDGE_RESULT_PARDON)
                        // CLEMENCY! The Judge overrode the failure. Check the next guard.
                        continue;

                    // Guard failed, Request NOT Pardoned by the Judge.
                    // Check if we need to send a message.
                    if ($verdict === self::JUDGE_NOT_FOUND) {

                        // --- THE STANDARD RESPONSE ---
                        // This block executes if no Judge was summoned, or the Judge method didn't exist.
                        // Use the centralized helper to resolve the message. No more F** DRY!
                        $messageText = trim($this->resolveAndTranslateMessage(
                            $failMessage, 
                            "Access denied." // A fallback default, though it will rarely be used.
                        ));
                        
                        if (!empty($messageText)) {
                            $this->reply($messageText)->send();
                        }
                    }
                }

                // Signal failure to the routing loop.
                return false;
            }
        }

        // All guardians have reported success. The way is clear.
        return true;
    }

    /**
     * The ForceJoin Gatekeeper - ALCHEMIST EDITION.
     * This final form transforms the denial message into a fully interactive, user-friendly guide.
     * It uses the PowerButton architecture to create clickable, full-width inline buttons for each
     * required channel, turning a restriction into an elegant call-to-action.
     *
     * @param Route $route The modern Route object being checked.
     * @return bool Returns true if access is granted, false otherwise.
     */
    protected function handleForceJoinGuard(Route $route): bool
    {
        // O(1) Performance Check.
        if (empty($route->forceJoinChannels)) {
            return true;
        }

        $userId = $this->senderId();
        if (!$userId) return false;

        $allowedStatuses = ['creator', 'administrator', 'member'];
        $accessGranted = true; // Assume loyalty until proven otherwise.

        // --- PHASE 1: THE FAST GUARD (Performance) ---
        // We check loyalty with ruthless efficiency. The moment one failure is found, we stop.
        foreach ($route->forceJoinChannels as $channelId) {
            $cacheKey = "forcejoin:{$userId}:in:{$channelId}";

            // Check Amethyst memory first (The Just Caching).
            if (AmethystMatrix::recall($cacheKey) === true) {
                continue; // Loyalty confirmed from cache. Check next channel.
            }

            // Not in cache, we must verify with the source.
            try {
                $status = $this->core()->getChatMember($channelId, $userId)['result']['status'] ?? 'left';
                if (in_array($status, $allowedStatuses, true)) {
                    // Loyalty confirmed. Remember this success for 5 Minutes.
                    AmethystMatrix::vault($cacheKey, true, 300);
                } else {
                    // FAILURE DETECTED!
                    $accessGranted = false;
                    break; // <-- THE DIVINE COMMAND! Halt all further checks.
                }
            } catch (\Throwable $e) {
                AmethystMatrix::error('ForceJoin Guard API error.', ['user_id' => $userId, 'channel_id' => $channelId, 'error' => $e->getMessage()]);
                $accessGranted = false;
                break; // <-- On error, makes failure and HALT.
            }
        }

        // --- PHASE 2: THE ALCHEMIST'S RESPONSE (Interactive Honesty) ---
        // If access is still granted after all channels loop, it means the user is a member of all.
        if ($accessGranted) {
            return true;
        }

        // --- THE JUDGE'S CHAMBERS (NOW CENTRALIZED) ---
        $payload = [
            'requiredChannels' => $route->forceJoinChannels,
            'channels'         => $route->forceJoinChannels,
        ];
        $verdict = $this->tryInvokeJudge($route, $route->forceJoinMessage, $payload);

        // VERDICT ANALYSIS: Does the Judge grant clemency?
        // A strict check for JUDGE_RESULT_PARDON is paramount. Only an explicit JUDGE_RESULT_PARDON
        // constitutes an override to proceed.
        if ($verdict === self::JUDGE_RESULT_PARDON) {
            // Clemency granted. The guard stands down. The request shall pass.
            return true;
        }
        
        // If the verdict was JUDGE_RESULT_HALT,
        // the judgement is to HALT. The guard's original duty is upheld.
        if($verdict === self::JUDGE_RESULT_HALT)
            return false;

        // if $verdict === JUDGE_NOT_FOUND, so It should Render It's Deafult Messages Now ;)

        // If we are here, it means access was denied and Judge Not accept his Defense/Submissions.
        // Now, we provide the FULL and HONEST guide.
        // We will use the ORIGINAL `$route->forceJoinChannels` list to build the message,
        // ensuring the user gets the complete picture in one go.

        // --- STANDARD RESPONSE (If no "Judge" was summoned or the request lacks a special recommendation) ---

        // 1. Forge the PowerButtons using our new Platform-Aware Factory.
        // This single line replaces the entire complex array_map block.
        $buttons = $this->createPlatformAwareJoinButtons($route->forceJoinChannels);

        // If no valid buttons could be created for this platform, don't send an empty keyboard.
        if (empty($buttons)) {

            // Fallback message to a simple text for platforms with no joinable buttons
            $fallbackMessage = $this->resolveAndTranslateMessage(
                $route->forceJoinMessage, // Still respect the custom message
                '::krubot.errors.force_join_text_only|برای ادامه، عضویت در کانال‌های مورد نیاز الزامی است.'
            );

            $this->reply($fallbackMessage)->send();
            return false;
        }

        // Resolve the master denial text using our new, powerful resolver.
        $denialMessageText = $this->resolveAndTranslateMessage(
            $route->forceJoinMessage,
            // The default value is now a translation key itself, following the same pattern.
            '::krubot.messages.force_join_denial|برای ادامه، عضویت در تمام کانال‌های زیر الزامی است. پس از عضویت، دوباره تلاش کنید:'
            // Try to get the master text from config/lang files.
        );

        // 3. Send the final, powerful message with the interactive keyboard.
        $this->reply($denialMessageText)
            ->keyboard(
                Keyboard::make()
                    ->buttons($buttons)
                    ->inline() // Command: Make it "شیشه‌ای" (Inline)
                    ->chunk(1)  // Command: Ensure each button width is 100%
            )
            ->send();

        // Signal the final failure.
        return false;
    }


    // End Deprecation _ Area
    // Welcome to New PowerFUL...
    /**
     * =========================================================================
     *  ⚡ THE ULTRA-POWERFUL ROUTING ENGINE v12.0 (MULTI-VERSE ULTIMATE CONSOLIDATED)
     * =========================================================================
     * 
     * The definitive "Brain" of KrubiK.
     * 
     * 💎 PERFORMANCE ARCHITECTURE:
     * 1. Normalization on-the-fly: Detects Route Object vs Array ONCE via `$isSmartRoute`.
     * 2. Early Guards: Checks 'recipient' restrictions BEFORE expensive Regex engines.
     * 3. Smart Matching: Exact Match (O(1)) -> Param Match (Fast String Search) -> Regex (Power).
     * 4. Intelligent Assembly: Delegates middleware logic to Route Class #2 if available.
     * 5. Dual-Pipeline: Laravel Pipeline (Preferred) -> Native Robust Fallback (with Aliases).
     * 6. Now fully aware of the 4th Dimension: Glass Buttons (Callbacks) and Action-based Conversational Routing.
     * 
     * @param Message $message The incoming update message.
    */
    public function processUpdate(Message $message): void
    {
        // =====================================================================
        // PHASE 0: STATE INITIALIZATION & OPTIMIZATION
        // =====================================================================
        
        // 1. Global State Injection
        $this->currentMessage = $message;
        
        // 2. Primitive Extraction (Memory Optimization)
        // Extract text once to avoid repeated property access. Ensure string type.
        /// $text = $message->text ?? '';
        
        // 3. Reset Request State (Lazarus/Swoole/RoadRunner Compatibility)
        // Crucial for long-running processes to prevent data leakage between requests.
        $this->currentRouteParams = [];
        $this->currentResolvedHandler = null;

        $this->resetContextData(); // 🌋 THE ASYNC GUARDIAN: WIPE THE SLATE CLEAN! [bot->get() && bot->set() data]
        $this->tunnelAmethyst($message); // We Can Auto-Fill it by $this->currentMessage, but not now!

        // =========================================================================
        // 🧠 SENSORY PRE-COMPUTATION (ONCE AND FOR ALL ROUTES)
        // THE UNIFIED SIGNAL RESOLUTION ⚡️
        // =========================================================================
        // The call now unpacks 5 values. `resolveRoutingSignal` is now the
        // Single Source of Truth for Every Signal detections.
        [$routingType, $routingPayload, $actionParams, $envelopeSignal, $contentSignal] = $this->resolveRoutingSignal($message);

        // Early exit if no signal and no fallback handler exists.
        if ($routingType === self::RT_NONE && !$this->fallbackHandler) {
            $this->tunnelAmethyst();
            return;
        }

        $text = $routingPayload  ?? ''; // Fill $text from resolvedSignal

        // =====================================================================
        // PHASE 1: THE FINDER (MATCHING LOOP)
        // =====================================================================
        
        $matchedRoute = null;
        $finalRouteParams = [];
        $isSmartRoute = false; // Optimization Flag
        $isSmartRouteCandidate = false;

        // The core of "The Great Filter". Defines which route types are valid for each signal.
        $allowedMatches = [
            // A text signal can match text, command, or regex routes.
            self::RT_TEXT         => [self::RT_TEXT, self::RT_COMMAND, self::RT_REGEX],

            // AN ACTION SIGNAL (from a callback_button OR web_app_data) can match a
            // standard button Action or a WebAction. THIS IS OUR UNIFIED HIGHWAY.
            self::RT_ACTION       => [self::RT_ACTION, self::RT_WEB_ACTION],

            // ✨ NEW: An Inline Query signal can ONLY match an Inline Query route.
            self::RT_INLINE       => [self::RT_INLINE],

            // A message type signal (photo, video) matches type routes.
            self::RT_TYPE         => [self::RT_TYPE],

            // A direct Web Request signal can match a WebPage or a WebAction.
            // This is for direct browser/AJAX calls to Laravel.
            self::RT_WEB          => [self::RT_WEB_APP, self::RT_WEB_PAGE, self::RT_WEB_ACTION],  // A generic  WebApp signal can match ANY web route type        

            /// self::RT_WEB_APP_DATA => [self::RT_WEB_ACTION], // But Data from JS `[TG||Bl].WebApp.sendData()` should trigger a WebAction
            /// self::RT_WEB_ACTION   => [self::RT_WEB_ACTION],
        ];

        // This line finds the valid route types for the given signal.
        $validRouteTypesForSignal = $allowedMatches[$routingType] ?? [];

        /*
        if(empty($validRouteTypesForSignal)) {
            // If the signal type doesn't map to any valid route types, we can potentially exit early.
            // However, the conversation interceptor logic below might still need to run, so we proceed.
        }
        */

        // ⚔️ RESOLVE CURRENT PLATFORM ONCE - BEFORE THE LOOP ⚔️
        // This value is constant for the entire request lifecycle.
        $currentPlatform = $this->resolveCurrentPlatform();

        // Iterate through all registered routes to find the FIRST match.
        foreach ($this->routes as $pattern => $routeItem) {

            // --- A) PRE-COMPUTATION & ATTRIBUTE EXTRACTION ---
            $isSmartRouteCandidate = ($routeItem instanceof Route);
            $attributes = $isSmartRouteCandidate ? $routeItem->getAttributes() : ($routeItem['attributes'] ?? []);
            $routeTypeAttribute = $isSmartRouteCandidate ? $routeItem->type : ($attributes['_route_type'] ?? null);
            $routeTypeAttribute ??= self::RT_TEXT;

            // ⚡️ GREAT FILTER ⚡️
            // If the route's type is not in the list of valid types for the current signal, skip it instantly.
            if (!in_array($routeTypeAttribute, $validRouteTypesForSignal, true)) {
                continue;
            }
            
            // --- A) NORMALIZATION & TYPE DETECTION ---
            // We determine the route type HERE to avoid `instanceof` checks in the critical execution path later.
            
            if (is_object($routeItem) && method_exists($routeItem, 'getPlatforms')) {
                // MODERN: Route Object (Class #2)
                // We call getAttributes() to handle Guard checks.
                // $attributes = method_exists($routeItem, 'getAttributes') ? $routeItem->getAttributes() : [];
                $isSmartRouteCandidate = true; 
            } elseif (is_array($routeItem)) {
                // LEGACY: Array Structure ['action' => ..., 'attributes' => ...]
                $attributes = $routeItem['attributes'] ?? [];
                $isSmartRouteCandidate = false;
            } else {
                // RAW: Callable fallback
                $attributes = [];
                $isSmartRouteCandidate = false;
            }

            // --- B) SECURITY GUARDS (PRE-REGEX OPTIMIZATION) ---
            // strict conditions checked BEFORE running expensive Regex engine.

            // 🛡️ ADVANCED GATES 🛡️
            // This checks only runs on modern Route objects that support these features.
            if ($isSmartRouteCandidate) {

                // 🛡️ GATE 1: THE PLATFORM GUARD (CRITICAL ADDITION) 🛡️
                // This is where we enforce #[RestrictTo] attributes.
                if(!$routeItem->isAllowedOn($currentPlatform))
                    // ❌ اجازه عبور نداری! به روت بعدی برو.
                    continue; // SILENTLY DENY. The user on the wrong platform should not know this route exists.

                // ✨🛡️ GATE 2: THE FORCEJOIN GUARD (THE DIVINE WILL) 🛡️✨
                // BEFORE any other logic, we ensure the user has pledged their allegiance by joining the required channels.
                if(!$this->handleForceJoinGuard($routeItem)) {
                    // Access is denied. The guard has already informed the user.
                    // We must halt all further processing for THIS request.
                    // We clear the amethyst tunnel and exit the entire processUpdate method.
                    $this->tunnelAmethyst();
                    return;
                }
            }
            
            // GATE 3: Recipient / Channel Restriction
            if (!empty($attributes['recipient'])) {
                $allowedRecipients = (array) $attributes['recipient'];
                $currentChatId = $this->chatId();
                $currentSenderId = $this->senderId();
                
                // Logic: Must match EITHER the ChatID OR the SenderID.
                if (!in_array($currentChatId, $allowedRecipients) && !in_array($currentSenderId, $allowedRecipients)) {
                    continue; // Skip this route immediately
                }
            }
            
            // GATE 3: Driver/Platform Restriction (Future Proofing)
            // if (!empty($attributes['driver']) && $attributes['driver'] !== 'rubika') { continue; }

            // --- C) PATTERN MATCHING ENGINE ---
            // ⚡ Now matching against $routingTarget instead of just $text
            $isMatch = false;
            $matches = [];

            // Strategy 1: Exact String Match (Fastest - O(1))
            if ($text === $pattern) {
                $isMatch = true;
            }
            // STRATEGY 2: [NEW] PARAMETERIZED WEB PATH MATCHER (CURLY BRACE NOTATION)
            // It runs ONLY for web signals on patterns that contain in-url parameters.
            // It is now the primary engine for WebApp/WebAction routes.
            elseif (
                $routingType === self::RT_WEB &&            // Only for web requests
                method_exists($this, 'demystifyWebPath') && // if HasWebInterface Trait is Loaded
                str_contains($pattern, '{')                 // Only for patterns with potential parameters
            ) {
                // We delegate the complex matching logic to a new, dedicated helper method.
                // This keeps the main loop clean and readable.
                [$isMatch, $matches] = $this->demystifyWebPath($pattern, $text);
            }
            // Strategy 3: NEW ✨ INLINE QUERY MATCH (HYPER-OPTIMIZED) ✨
            // This block will only be evaluated if the Great Filter passed an RT_INLINE_QUERY signal.
            elseif ($routeTypeAttribute === self::RT_INLINE) {
                // Case A: Catch-all route. Matches any inline query.
                if ($pattern === '__ANY__') {
                    $isMatch = true;
                }
                // Case B: Regex/Prefix match. We already converted prefixes to regex in the `onInlineQuery` method.
                // We trust the pattern is a valid regex here.
                elseif (@preg_match($pattern, $text, $m)) {
                    $isMatch = true;
                    // Extract capture groups for parameter injection.
                    // Slicing off the full match at index 0.
                    $matches = array_slice($m, 1);
                }
            }
            // 👁️ Strategy 4: SENSORY TYPE MATCH (TRUE O(1) HYPER-PERFORMANCE) ⚡
            // Evaluates if the route is a Type route and matches our pre-calculated $detectedMediaType
            elseif (str_starts_with($pattern, 'TYPE::')) {
                // Determine actual type defined in route, e.g., 'TYPE::photo' -> 'photo'
                $expectedType = substr($pattern, 6); 

                // 1. Get the strategy flag injected into the Route during definition in onType().
                // This is the "Strategy-Aware Route" Concept in Action.
                $useEnvelopeStrategy = $route->getAttribute('_signal_class', false);

                // 2. Select the correct, pre-computed signal based on the route's own preference.
                $detectedMediaType = $useEnvelopeStrategy ? $envelopeSignal : $contentSignal;
                
                /// 👁️ Re-Awaken the Sensory Engine: Detect the physical type of this message.
                /// $detectedMediaType = $this->detectMessageType($message); // This is now done in resolveRoutingSignal
                
                // 3. Perform a lightning-fast comparison. No more detectMessageType() calls here.
                if ($expectedType === $detectedMediaType) {
                    $isMatch = true;
                }
            }
            // Strategy 5: Parameterized Match (e.g., "/cmd {param}")
            // Optimization: `str_contains` is significantly faster than `preg_match` for pre-check.
            elseif (str_contains($pattern, '{') && str_contains($pattern, '}')) {
                // Escape literals, then convert {param} to Named Group (?<param>.*?)
                // We strictly expect Start(^) and End($) anchors.
                $safePattern = preg_quote($pattern, '/');
                $regex = '/^' . preg_replace('/\\\{(\w+)\\\}/', '(?<$1>.*?)', $safePattern) . '$/iu';
                
                if (preg_match($regex, $text, $m)) {
                    $isMatch = true;
                    // Filter to keep ONLY named string keys for Dependency Injection
                    $matches = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                }
            }
            // Strategy 6: Explicit Regex Match (Power User)
            // Heuristic: Starts/Ends with slash "/" and length > 2 (to avoid empty "//")
            elseif (str_starts_with($pattern, '/') && str_ends_with($pattern, '/') && strlen($pattern) > 2) {
                if (preg_match($pattern, $text, $m)) {
                    $isMatch = true;
                    // Extract named groups if exist, otherwise use positional matches (slicing off full match)
                    $named = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                    $matches = !empty($named) ? $named : array_slice($m, 1);
                }
            } 

            // --- D) MATCH CONFIRMATION ---
            if ($isMatch) {

                // A pattern match was found. NOW, we consult the guards.
                // This check only applies to modern, "smart" Route objects.
                // We assume legacy routes do not have guards.
                if ($isSmartRouteCandidate && ($routeItem instanceof Route)) {

                    // Call our new gatekeeper.
                    if ($this->evaluateRouteGuards($routeItem, $message)) {
                        // ✅ GUARDS PASSED! This is our winner.
                        // Lock in the route and break the loop.
                        $matchedRoute = $routeItem;
                        $finalRouteParams = $matches;
                        $isSmartRoute = true; // Confirmed smart route
                        break; // FIRST *VALID* MATCH WINS - Break the loop.
                    } else {
                        // ❌ GUARDS FAILED!
                        // The user's strategy in action: Silently ignore this match.
                        // Continue the loop to search for the next candidate.
                        continue;
                    }

                } else {
                    // This is a legacy route (array or simple callable) without guards.
                    // A pattern match is enough.
                    $matchedRoute = $routeItem;
                    $finalRouteParams = $matches;
                    $isSmartRoute = false;
                    break; // FIRST MATCH WINS - Break the loop.
                }
        }
        }

        // Merge action params (from button payload) with route params
        // Route params take precedence only if same key appears later:
        // choose your policy. Here: action params first, route params overwrite.
        $finalRouteParams = array_merge($actionParams, $finalRouteParams);
                
        $middlewareStack = [];
        $finalHandler = null;

        // =====================================================================
        // PHASE 2: THE COMPILER (STACK ASSEMBLY)
        // =====================================================================

        if ($matchedRoute) {

            // PATH 2-1: HAPPY PATH - A GLOBAL ROUTE WAS SUCCESSFULLY MATCHED
            // Route exists. Compile the handler and its middleware stack here.

            // 1. Save Context for Middleware Inspection
            $this->currentResolvedHandler = $matchedRoute; // is_object($matchedRoute) && $matchedRoute instanceof Route ? $matchedRoute : null;
            $this->currentRouteParams = $finalRouteParams;

            // 2. Assemble Handler & Middleware Stack
            // We leverage the `$isSmartRoute` flag computed in Phase 1.

            if ($isSmartRoute && $matchedRoute instanceof Route) {
                // === MODERN PATH (Route Class #2) ===
                // Delegate logic to the Route object. It knows how to merge Global + Local
                // and handle 'skipGlobalMiddlewares' intelligently.
                
                $finalHandler = $matchedRoute->getAction();
                $middlewareStack = $matchedRoute->getMiddlewareStack($this->globalMiddlewares);
                
            } else {
                // === LEGACY PATH (Backwards Compatibility) ===
                // Manual extraction and merging.
                
                $routeItemArr = is_array($matchedRoute) ? $matchedRoute : ['action' => $matchedRoute];
                $finalHandler = $routeItemArr['action'] ?? null;
                
                $attrs = $routeItemArr['attributes'] ?? [];
                $routeMiddlewares = $attrs['middleware'] ?? [];
                if (!is_array($routeMiddlewares)) $routeMiddlewares = [$routeMiddlewares];
                
                // Simulate 'withoutGlobalMiddleware' manually for arrays
                if (($attrs['withoutGlobalMiddleware'] ?? false) === true) {
                    $middlewareStack = $routeMiddlewares;
                } else {
                    // Standard Order: Global (Outer) -> Local (Inner)
                    $middlewareStack = array_merge($this->globalMiddlewares, $routeMiddlewares);
                }
            }

        }

        // NO specific route was matched. Time to check for fallbacks.
        else {

            // PATH 2-2: NO GLOBAL ROUTE MATCHED. NOW WE INVESTIGATE WHY.
            // No route matched. Handle interceptors, actions, and fallback scenarios here.

            // Keep parameters available for any downstream interceptors or fallback handlers
            $this->currentRouteParams = $finalRouteParams;

            // An ORPHANED BUTTON CLICK was detected. Force it into the pipeline
            // so ConversationMiddleware can check if it belongs to an active conversation.
            if ($routingType === self::RT_ACTION) {
                // Never drop callback actions directly.
                // The Magic Interceptor must run to let ConversationMiddleware catch it.
                // Force global middleware pipeline so ConversationMiddleware can consume #[Action].
                $finalHandler = static function () use ($message) {
                    // intentional no-op
                    // This handler ideally never runs if ConversationMiddleware consumes action and does its job.
                    // ConversationMiddleware, can stop the flow here.
                    // It's a safety net logger.

                    AmethystMatrix::warning(
                        "Orphaned Callback Triggered: No global route caught this. ConversationMiddleware will now inspect.", 
                        ['details' => $message, 'payload' => $message->button_id ?? 'N/A']
                    );
                };
                // Force the global stack which includes ConversationMiddleware.
                $middlewareStack = $this->globalMiddlewares;

                // then continue to pipeline execution branch
            }

            else {

                // Any other unmatched message. Check for our new fallback system.
                // Prioritize Type-Specific Fallbacks first.

                 // PHASE 2.5: DUAL-DETECTION FALLBACK RESOLUTION
                // Executed ONLY if no specific route matched. This logic respects the developer's intent
                // by checking for fallbacks against both Content-first and Envelope-first detection strategies.

                $finalHandler = null;

                // Step 1: Detect with standard priority (Content-first).
                // This is the most common use case, e.g., fallback for any 'photo' or 'sticker'.
                $contentFirstType = $this->detectMessageType($message, false); // $prioritizeEnvelopeDetection = false
                if ($contentFirstType !== Signal::Void && isset($this->typeFallbackHandlers[$contentFirstType])) {
                    $finalHandler = $this->typeFallbackHandlers[$contentFirstType];
                }

                // Step 2: If no match, re-detect with inverted priority (Envelope-first).
                // This catches fallbacks for events like 'edited_message' or 'callback_query'
                // even if the content-first detection identified something else (e.g., 'text' inside an edit).
                if ($finalHandler === null) {
                    $envelopeFirstType = $this->detectMessageType($message, true); // $prioritizeEnvelopeDetection = true
                    // We also check if the detected type is different from the first pass to avoid redundant lookups.
                    if ($envelopeFirstType !== Signal::Void &&
                        $envelopeFirstType !== $contentFirstType &&
                        isset($this->typeFallbackHandlers[$envelopeFirstType]))
                    {
                        $finalHandler = $this->typeFallbackHandlers[$envelopeFirstType];
                    }
                }

                // Now, resolve the final handler based on the dual-detection results.
                if ($finalHandler) {
                    // A type-specific handler was found through one of the strategies.
                    $middlewareStack = $this->globalMiddlewares;
                }
                // Step 3: Global Fallback as the ultimate safety net.
                // This runs if neither Content-first nor Envelope-first detection inspected a specific fallback.
                elseif ($this->fallbackHandler) {
                    $finalHandler = $this->fallbackHandler;
                    // Run Global Middlewares to ensure logging/security even on 404s.
                    $middlewareStack = $this->globalMiddlewares;
                }
                // Step 4: Absolute Dead End. No route, no fallback.
                else { // not found ?
                    // # Dead End #
                    $this->tunnelAmethyst(null); // clear AmethystMatrix working message entry; Then::
                    return; // End of the line. — No PIPELINE RUNNER needed for nothin!
                }
            }

        }

        // =====================================================================
        // PHASE 3: THE RUNNER (PIPELINE EXECUTION)
        // =====================================================================
        
        // The final destination closure that executes the current matching handler.
        $destination = function ($bot) use ($finalHandler, $message, $finalRouteParams) {

            // Execute the action with dependency injection or parameters, retrieving the raw output of destianation method.
            $actionResult = $this->callAction($finalHandler, $message, $finalRouteParams);

            if(method_exists($this, 'response')) // if HasWebInterface Trait is Loaded,
                $this->response($actionResult); // Standardize the raw output into a clean HTTP Response object immediately, save it into ?$finalResponse.

            return $actionResult;
        };

        // OPTION A: LARAVEL PIPELINE (The Gold Standard)
        // Used when running inside a Laravel Application (Artisan/Http).
        if (class_exists(\Illuminate\Pipeline\Pipeline::class) && function_exists('app')) {
            app(\Illuminate\Pipeline\Pipeline::class)
                ->send($this)
                ->through($middlewareStack)
                ->then($destination);
        } 
        // OPTION B: NATIVE ROBUST FALLBACK (The Heavy Lifter)
        // Used for standalone scripts or lightweight setups. 
        // Enhanced to support Aliases, Invokables, and Standard Middleware methods.
        else {
            $pipeline = array_reduce(
                array_reverse($middlewareStack),
                function ($next, $middleware) {
                    return function ($bot) use ($next, $middleware) {
                        
                        // --- 1. Resolve Aliases ---
                        // Check if 'auth' maps to 'App\Middleware\Auth::class'
                        if (is_string($middleware) && property_exists($this, 'middlewareAliases')) {
                            if (isset($this->middlewareAliases[$middleware])) {
                                $middleware = $this->middlewareAliases[$middleware];
                            }
                        }

                        // --- 2. Instantiate & Execute ---
                        
                        // TYPE I: String Class Name
                        if (is_string($middleware) && class_exists($middleware)) {
                            $instance = new $middleware;
                            
                            // Prefer 'handle' method (Laravel Standard)
                            if (method_exists($instance, 'handle')) {
                                return $instance->handle($bot, $next);
                            } 
                            // Fallback to '__invoke' (Modern/Slim Standard)
                            elseif (is_callable($instance)) {
                                return $instance($bot, $next);
                            }
                            
                            // Strict Failure if un-executable class is passed
                            throw new \RuntimeException("Middleware [$middleware] is not executable (missing handle/__invoke).");
                        }
                        
                        // TYPE II: Closure Middleware
                        if ($middleware instanceof \Closure) {
                             return $middleware($bot, $next);
                        }
                        
                        // TYPE III: Object Instance
                        if (is_object($middleware)) {
                            if (method_exists($middleware, 'handle')) {
                                return $middleware->handle($bot, $next);
                            } elseif (is_callable($middleware)) {
                                return $middleware($bot, $next);
                            }
                        }

                        // Safety Net: Pass through if middleware is invalid/unrecognized
                        return $next($bot);
                    };
                },
                $destination
            );

            // Ignite the Native Pipeline
            $pipeline($this);
        }

        $this->tunnelAmethyst(); // short syntax for `$this->tunnelAmethyst(null)` ; clears AmethystMatrix working message entry.
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
        } elseif ($action instanceof \Closure || is_callable($action)) {
            $instance = is_object($action) && !$action instanceof \Closure ? $action : null;
            $reflection = new ReflectionFunction($action instanceof \Closure ? $action : \Closure::fromCallable($action));
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
     * This is the new, dedicated heart of our auto-wiring logic. It *always* runs.
     * It honors all sacred priorities and forges the final, definitive argument payload.
     *
     * @param ReflectionMethod|ReflectionFunction $method The reflection of the target action.
     * @param array $payloadData The merged context and route data. (Parameters extracted from Route Regex or Action Payload.)
     * @param array $extraInjects Core objects for type-based injection, if requested. (like Answer DTOs)
     * @return array An associative array of [parameterName => resolvedValue].
     * @throws RuntimeException If a catastrophic dependency failure occurs, or a required parameter cannot be resolved.
     */
    private function _resolveActionDependencies(
        ReflectionMethod|ReflectionFunction $method,
        array $payloadData,
        array $extraInjects = []
    ): array {
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

            // PRIORITY 3: Payload Data Injection by Name (The Metaphysical Cast)
            if (array_key_exists($name, $payloadData)) {
                $val = $payloadData[$name];
                // Automatic type casting for scalar types based on reflection.
                if ($type instanceof ReflectionNamedType && $type->isBuiltin()) {
                    $val = match ($type->getName()) {
                        'int'    => (int) $val,
                        'bool'   => filter_var($val, FILTER_VALIDATE_BOOLEAN),
                        'float'  => (float) $val,
                        'string' => (string) $val,
                        'array'  => (array) $val,
                        default  => $val,
                    };
                }
                $dependencies[$name] = $val;
                continue;
            }

            // PRIORITY 4: Safe Fallbacks (Default Values & Nullables)
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

    /**
     * 🛡️ The Divine Shield of Resilience (resilientCall/rescueResult method) 🛡️
     * 🛡️ The Archangel's Aegis Protocol v4.0 (resilientRun Remastered) 🛡️
     *
     * This method imbues Krubot with a divine shield, allowing it to gracefully
     * Executes a given callback, gracefully catching any exceptions thrown within it.
     * 
     * Acts as a metaphysical force-field, allowing Krubot to continue his mission 
     * even when unforeseen turbulences arise, ensuring a seamless
     * user experience and robust system operation.
     *
     * It integrates deeply with AmethystMatrix for intelligent logging and
     * offers a flexible custom exception handler, embodying the ultimate HyperDX.
     *
     * @param callable $op          The risky operation to execute.
     * @param mixed   $def      TThe value to return if an exception is caught. Defaults to null.
     * @param Closure|null $exceptionHandler Optional. A custom callback to handle the caught exception.
     *                                   It receives: `function(Throwable $e, Krubot $bot): mixed` as arguments.
     *                                   If this handler returns a non-null value, that value (from exceptionHandler) will be used
     *                                   instead of the `defaultValue`. This is where `$handleException($e, $this);`
     *                                   concept finds its ultimate expression.
     * @param null|bool    $useLaravelContainer If true, the callback will be executed via `App::call()`,
     *                                   enabling automatic dependency injection for its parameters. Defaults to false for maximum performance.
     * @param null|bool    $logExceptions      Optional. Whether to log the exception via AmethystMatrix. Defaults to null.
     * @return mixed The result of the callback, the default value, or the result of the customExceptionHandler.
    */
    public function resilientRun(
        callable $op,
        mixed $def = null,
        ?Closure $exceptionHandler = null,
        ?bool $useLaravelContainer = null, // ⚡️ NEW: Control Laravel IoC container usage
        ?bool $logExceptions = null         // Changed to nullable for dynamic fallback
    ): mixed {

        // ⚡ HyperDX Logic: Harmonizing explicit call parameters with Krubot's internal configuration.
        // If the method parameter is explicitly provided (not null), it takes precedence.
        // Otherwise, Krubot consults its internal 'bRCUseLaravelContainer' and 'bRCLogException' states.
        $bUseLaravelContainer = $useLaravelContainer ?? $this->bRCUseLaravelContainer;
        $bLogException = $logExceptions ?? $this->bRCLogException;

        // rename variables
        $callback = &$op;
        $defaultValue = &$def;

        try {
            // Attempt to execute the sacred operation.
            if ($bUseLaravelContainer && function_exists('app')) {
                // ⚡️ Laravel Container Power: Invoke the callback using App::call()
                // This enables automatic dependency injection for the callback's parameters.
                // The Krubot instance ($this) is always available as a bound instance in the container.
                // For optimal flexibility, we pass an array of parameters, ensuring Krubot is available
                // for injection if the callback requests it.

                // Engage the full metaphysical auto-wiring engine.
                $reflection = new ReflectionFunction(Closure::fromCallable($callback));

                // The payload remains simple here as the engine will resolve the rest.
                $payloadData = ['bot' => $this, 'message' => $this->thisMessage(), 'msg' => $this->thisMessage()];
                $extraInjects = [$this, $this->thisMessage()];
                
                return $this->invokeWithAutoWiring(
                    method: $reflection,
                    payloadData: $payloadData,
                    extraInjects: $extraInjects
                    // No forceNative flag needed, as we are in the correct 'if' block.
                );
            } else {
                // --- PATH OF THE SWIFT BLADE (NATIVE PHP) ---
                // No reflection, no overhead. A direct, lightning-fast invocation.
                // Default execution: Directly invoke the callback
                return $callback($this);
                // Pass Krubot instance to the callback for context
            }
        } catch (Throwable $e) {
            // A disturbance in the force detected!
            // Engage the AmethystMatrix for cosmic record-keeping and activate The Divine Shield.

            // 1. 🔮 AmethystMatrix Logging (The Oracle's Chronicle)
            if ($bLogException && class_exists(AmethystMatrix::class)) {
                AmethystMatrix::yell(
                    "Krubot Divine Shield: An unexpected anomaly occurred during a protected operation.",
                    [
                        'error_message' => $e->getMessage(),
                        'error_code'    => $e->getCode(),
                        'file'          => $e->getFile(),
                        'line'          => $e->getLine(),
                        'trace'         => $e->getTraceAsString(),
                        'default_value' => $defaultValue,
                        'operation_context' => 'rescue_attempt',
                        // ⚡ HyperDX: Auto-inject relevant Krubot context for deeper insights
                        'bot_context'   => [
                            'chat_id'       => $this->chatId(),
                            'sender_id'     => $this->senderId(),
                            'message_id'    => $this->findMessageId(),
                            'message_text'  => $this->text(),
                            'driver_alias'  => $this->getDriverAlias()
                        ]
                    ]
                );
            }

            // 2. ⚡ Custom Exception Handler (The Warlord's Decree)
            // If a custom handler is provided, invoke it. This is where the
            // `$customHandlerResult = $handleException($e, $this);` concept comes to life.
            if ($exceptionHandler instanceof Closure) {

                $customHandlerResult = null;               
                // ⚜️ The handler's execution path mirrors the main operation's path. ⚜️
                if ($bUseLaravelContainer && function_exists('app')) {
                    // ⚡️ NEW: Hyper-Laravel Container Power for exception handler
                    // The handler also gets full auto-wiring power.
                    $handlerReflection = new ReflectionFunction($exceptionHandler);

                    // The payload for the handler includes the exception itself.
                    $handlerPayload = [
                        'bot' => $this, 
                        'message' => $this->thisMessage(),
                        'msg' => $this->thisMessage(),
                        'e' => $e,
                        'exception' => $e,
                        Throwable::class => $e
                    ];
                    // Pass the exception and Krubot instance explicitly, allowing D-I via Laravel|invokeWithAutoWiring() for other params.
                    $handlerExtraInjects = [$this, $this->thisMessage(), $e];

                    $customHandlerResult = $this->invokeWithAutoWiring(
                        method: $handlerReflection,
                        payloadData: $handlerPayload,
                        extraInjects: $handlerExtraInjects
                    );

                } else {

                    // --- PATH OF THE SWIFT BLADE (NATIVE PHP) ---
                    // Direct, fast, and simple invocation for the handler.
                    // Pass the exception and the current Krubot instance to the custom handler
                    $customHandlerResult = $exceptionHandler($e, $this);

                }

                // If the handler returned a non-null value, it is the new decree so takes precedence.
                if ($customHandlerResult !== null) {
                    return $customHandlerResult;
                }
            }

            // 3. ✨ Return Default Value (The Graceful Retreat)
            // If no custom handler or if it returned null, fall back to the default value.
            return $defaultValue;
        }
    }
    /**
     * 🔮 Configures the IoC Container (Laravel App::call) usage for resilientCall.
     *
     * This method allows you to dynamically control whether subsequent calls to `resilientCall`
     * will leverage Laravel's service container for dependency injection within callbacks
     * and exception handlers by default. It's a powerful lever for performance optimization and
     * architectural flexibility, embodying the HyperDX principle of granular control over
     * Krubot's operational parameters.
     *
     * @param bool $useLaravelContainer If true, `resilientCall` will attempt to use `App::call()`
     *                                  by default. If false, it will directly invoke callbacks.
     *                                  Defaults to `true` to enable IoC by default when calling this setter.
     * @return Krubot Returns the current Krubot instance for method chaining,
     *                allowing for fluent configuration of Krubot's metaphysical state.
     */
    public function resilientIoC(bool $useLaravelContainer = true): self
    {
        // ⚡️ Setting the default behavior for IoC container usage across all resilient operations.
        // This affects resilientCall when its `$useLaravelContainer` parameter is null,
        // providing a central control point for Krubot's dependency resolution strategy.
        $this->bRCUseLaravelContainer = $useLaravelContainer;
        return $this; // 🚀 Chainable for fluent configuration, aligning with ECMA2026 paradigms.
    }

    /**
     * 📡 Configures exception logging via AmethystMatrix for resilientCall.
     *
     * @param bool $logExceptions If true, exceptions will be logged by default. If false, they will be
     *                           silently handled without AmethystMatrix intervention.
     *                           Defaults to `true` to enable logging by default when calling this setter.
     * @return Krubot Returns the current Krubot instance for method chaining,
     *                facilitating a fluid configuration experience.
     */
    public function resilientLog(bool $logExceptions = true): self
    {
        // 📡 Setting the default behavior for exception logging across all resilient operations.
        // This affects resilientCall when its `$logExceptions` parameter is null,
        // granting Krubot the power to decide its level of self-reporting.
        $this->bRCLogException = $logExceptions;
        return $this; // 🚀 Chainable for fluent configuration.
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
     * Helper to send message without reply (Say), and without auto-send.
    */
    public function say(string $text): static
    {
        if (!$this->chatId())
            return $this;
        $this->chat($this->chatId())->message($text);
        return $this;
    }

    /**
     * Helper to reply to the current message.
     * Automatically sets replyTo ID if available.
    */
    public function reply(string $text): static
    {
        if (!$this->chatId()) {
            $this->message($text);
            return $this;
        }
        
        $builder = $this->chat($this->chatId());
        
        if ($msgId = $this->findMessageId()) {
            $builder->replyTo($msgId);
        }
        
        $builder->message($text);
        return $this;
    }

    /**
     * Helper to edit a specific message.
    */
    public function modify(string $messageId, string $newText): static
    {
        if (!$this->chatId()) return $this;

        $this->chat($this->chatId())
             ->messageId($messageId)
             ->message($newText);
             
        return $this;
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
        return $this->user()['id'] ?? null;
    }

    /**
     * Shortcut to get just the Sender ID string.
    */
    public function who(): ?string
    {
        return $this->user()['id'] ?? null;
    }

    /**
     * Get the cleaned text content (trimmed).
    */
    public function cleanText(): string
    {
        return trim($this->text());
    }

    /**
     * Check if the update is from the Admin defined in .env
     * Add RUBIKA_ADMIN_GUID=... to your .env file.
    */
    public function isAdmin(?string $userId = null): bool
    {
        $adminGuids = config('krubot.drivers.'.$this->getDriverAlias().' .admin_ids', [env('RUBIKA_ADMIN_GUID')]); // get admin ids for current platform
        $senderId = $userId ?? $this->senderId(); // we checking for who ?!
        
        return $senderId && in_array($senderId, $adminGuids);
    }

    /**
     * Send a message to a SPECIFIC target (User/Group GUID) directly.
    */
    public function to(string $targetChatId, string $text): array
    {
        return $this->chat($targetChatId)
            ->message($text)
            ->send();
    }

    /**
     * Delete the current message immediately.
    */
    public function deleteCurrent(): array
    {
        if (!$this->chatId() || !$this->findMessageId()) {
            return ['status' => 'ERROR', 'message' => 'No context available'];
        }
        
        return $this->chat($this->chatId())
            ->messageId($this->findMessageId())
            ->sendDelete();
    }

    /**
     * Edit the current message immediately (Useful for updating Bot's own menus).
    */
    public function editCurrent(string $newText): array
    {
        if (!$this->chatId() || !$this->findMessageId()) {
            return ['status' => 'ERROR', 'message' => 'No context available'];
        }

        return $this->chat($this->chatId())
            ->messageId($this->findMessageId())
            ->message($newText)
            ->editMessage();
    }

    /**
     * Edit the current message immediately (Useful for updating Bot's own menus).
    */
    public function sendMessageToAdmins(string $text): array
    {
        $adminGuids = config('krubot.drivers.'.$this->getDriverAlias().' .admin_ids', [env('RUBIKA_ADMIN_GUID')]); // get admin ids for current platform
        $result = [];
        foreach ($adminGuids as $admin_id) {
            $result []= $this->to($admin_id, $text);
        }
        return $result;
    }

    /**
     * Sets, updates, or flushes a user's current state(s) for Narrative Programming.
     * This is the primary "write" method for the #[When] attribute's "read" logic.
     *
     * It's designed to be a fluent, intuitive, and powerful interface for state management.
     * Supports single key/value, batch operations (via array, Arrayable, or Traversable),
     * and null-based deletion within batches.
     *
     * @param string|array|Arrayable|Traversable $stateKey The key for the state (e.g., 'level')
     *                                                     OR an associative data structure of states to set or flush.
     *                                                     e.g., collect(['level' => 10, 'class' => 'Mage', 'old_quest' => null])
     *                                                     In this example, 'level' and 'class' are set, and 'old_quest' is forgotten.
     * @param mixed $value The value to associate with the state if $stateKey is a string.
     *                     - Provide a value (string, int, array, etc.) to set it.
     *                     - Provide NO value (or true) to set a simple existence flag.
     *                     - Provide NULL to completely flush/delete the state.
     *                     This parameter is IGNORED if $stateKey is a batch data structure.
     * @return self Returns the bot instance for method chaining ($this).
    */
    public function now(string|array|Arrayable|Traversable $stateKey, mixed $value = true): self
    {
        // Case 1: Batch Operation (The most flexible path)
        // We check if the input is a data structure intended for batch processing.
        if (is_array($stateKey) || $stateKey instanceof Arrayable || $stateKey instanceof Traversable) {
            
            // --- Data Normalization ---
            // The goal here is to convert any acceptable input type into a standard PHP array
            // so the rest of the logic can work with it consistently.
            $batchData = [];
            if ($stateKey instanceof Arrayable) {
                // Priority 1: If the object explicitly follows Laravel's Arrayable contract,
                // we honor it by calling the toArray() method. This is the most reliable way
                // for objects like Illuminate\Support\Collection.
                $batchData = $stateKey->toArray();
            } elseif (is_array($stateKey)) {
                // Priority 2: A simple, plain array. No conversion needed.
                $batchData = $stateKey;
            } elseif ($stateKey instanceof Traversable) {
                // Priority 3 (Fallback): For any other iterable object (like a custom iterator),
                // we convert it to an array. Collections would also be caught here if not for the
                // Arrayable check above, but checking Arrayable first is more explicit.
                $batchData = iterator_to_array($stateKey);
            }

            // --- The Separation Logic ---
            // Now that we have a guaranteed array ($batchData), we can process it.
            // We iterate through the batch data once and separate operations into two groups:
            // 1. dataToSet: for keys that need a value.
            // 2. keysToForget: for keys whose value is explicitly null, signaling deletion.
            // We separate keys for setting/updating from keys for deletion.
            $dataToSet = [];
            $keysToForget = [];
            foreach ($batchData as $key => $val) {
                if ($val === null) {
                    $keysToForget[] = $key;
                } else {
                    $dataToSet[$key] = $val;
                }
            }

            // Step 1: Perform the batch update/set operation if there's anything to set.
            // This is efficient as it calls `put` (and subsequently `save`) only once for all updates.
            if (!empty($dataToSet)) {
                $this->userStorage()->put($dataToSet);
            }

            // Step 2: Perform deletions.
            // Instead of a loop, we now make a single, efficient, {SRP|SoC}-Based call.
            if (!empty($keysToForget)) {
                $this->userStorage()->forget($keysToForget);
            }

        } else {
            // Case 2: Single Key/Value Operation (Original Logic)
            // This path remains for single, direct state modifications.
            if ($value === null) {
                // e.g., $bot->now('is_registering', null); -> Deletes the state.
                $this->userStorage()->forget($stateKey);
            } else {
                // e.g., $bot->now('is_admin'); or $bot->now('level', 99); -> Sets the state.
                $this->userStorage()->put($stateKey, $value);
            }
        }

        // Always return $this to maintain the beautiful fluent API.
        // e.g., $bot->now('level', 10)->now('class', 'Mage')->reply('You are now a Level 10 Mage!')->send();
        return $this;
    }
    

    /**
     * Quick check if message matches a pattern (Exact or Regex).
     * Useful inside handlers for sub-logic.
    */
    public function matches(string $pattern): bool
    {
        $text = $this->cleanText();

        // Exact match
        if ($text === $pattern) return true;

        // Regex match check (heuristic: starts/ends with /)
        if (str_starts_with($pattern, '/') && str_ends_with($pattern, '/')) {
             return (bool) preg_match($pattern, $text);
        }

        return false;
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
        // This method Directly calls the Macroable::mixin() method that is inherited from the injected Macroable trait.
        // The power lies in its expressive and thematic name.
        static::mixin($evolutionaryMatrix, $replace);
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

        return $instance;
    }

    /**
     * Internal helper to force-set the context.
     * (Add this if specific setters don't exist in your Traits)
    */
    protected function forceContext(string $guid): void
    {
        // We set the chat ID as the primary target
        $this->chat_id = $guid;
        
        // If the GUID starts with 'u', it's a user, so we map it there too.
        if (str_starts_with($guid, 'u')) {
            $this->user_id = $guid;
        }
    }

}
