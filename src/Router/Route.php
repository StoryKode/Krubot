<?php

namespace KrubiK\Router;
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
use KrubiK\Enums\Platform;
//// use Illuminate\Routing\Route as LaravelRoute; // Import the Laravel Route // OBSOLETE, NOT NEEDED

/**
 * Class Route
 *
 * The definitive, consolidated Route object for the KrubiK Routing Engine.
 * 
 * Capabilities:
 * 1. Hybrid Middleware Management: Supports generic attributes array AND dedicated middleware stack.
 * 2. Smart Global Skipping: Allows skipping ALL global middlewares or specific classes.
 * 3. Named Routes (Registrar Bridge): Automatically updates the main Router index when named.
 * 4. Tagging System: For grouping and retrieving routes.
 * 5. Fluent Interface: Fully chainable methods.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
 */
class Route
{
    /**
     * The matching pattern (Regex, Command, or Exact text).
     */
    public string $pattern;

    /**
     * @var int The type of the route (e.g., Command, WebPage). See Router::RT_* constants.
     */
    public ?int $type = null; // Or RT_NONE

    /**
     * The handler action (Controller array, Closure, or Invokable class string).
     */
    public mixed $action;

    /**
     * General attributes container (recipients, custom drivers, etc.).
     * Note: Middleware definitions are extracted from here to $middlewares for performance,
     * but other metadata remains here.
     */
    public array $attributes = [];

    /**
     * Dedicated middleware stack for this specific route.
     * Optimized for array operations (push/merge) and execution pipeline.
     */
    protected array $middlewares = [];

    /**
     * The list of platforms this route is restricted to.
     * An empty array signifies that the route is available on ALL platforms (no restrictions).
     * Optimized for O(1) checks during route dispatch.
     *
     * @var string[]
    */
    protected array $platforms = [];

    /**
     * Tags for categorizing routes (e.g., 'auth', 'admin-panel', 'payment').
     */
    protected array $tags = [];

    /**
     * If true, ALL global middlewares defined in the Bot are ignored for this route.
     */
    protected bool $skipAllGlobalMiddlewares = false;

    /**
     * A list of SPECIFIC global middleware classes to skip.
     * Allows fine-grained control (e.g., keep 'Log' but skip 'Auth').
     */
    protected array $skippedGlobalMiddlewares = [];

    /**
     * Name of the route (if assigned).
     */
    protected ?string $name = null;

    /**
     * The callback to register this route's name back to the main Krubot instance.
     * This creates a bridge between the Route object and the central Router index.
     * This is "hidden" from public export/serialization usually.
     */
    protected ?Closure $nameRegistrar = null;

    /**
     * 🔥 THE NEW SOURCE OF WISDOM
     * Holds the names of parameters extracted from the route's pattern.
     * e.g., for '/product/{id}/variant/{variantId?}', this will be ['id', 'variantId'].
     * This is the key to intelligent URL generation.
     *
     * @var string[]
    */
    public array $pathParameters = [];

    /**
     * ✨ THE ENRICHMENT DECREE ✨
     * If true, the routing engine will automatically append required, non-injected parameters
     * from the handler method's signature to the route's URI pattern.
     * This flag provides explicit, developer-driven control over "magic" route modifications.
     * It is set by the corresponding Attribute (e.g., WebApp, WebPage).
     * @var bool
     */
    public bool $autoEnrichPattern = false;

    /**
     * ✨ THE BRIDGE BETWEEN WORLDS ✨
     * For web routes (WebApp, WebPage, WebAction), this property will hold the
     * actual instance of the Illuminate\Routing\Route object created by Laravel's router.
     * This allows integrateNexus to "bake" metadata directly onto it for the
     * HTTP middleware layer (like KrubikPlatformGuard) to consume.
     * For non-web routes (commands, text), this will remain null.
     *
     * @var LaravelRoute|null
    */
    /// public ?LaravelRoute $laravelRoute = null; // OBSOLETE, NOT NEEDED

    /**
     * ✨ THE ACCESS DECREE ✨
     * Stores the access policy for this route, e.g., 'strict' or 'standard'.
     * This is read by the KrubikPlatformGuard to enforce identity requirements.
     *
     * @var string
    */
    protected string $accessPolicy = 'standard'; // Default to standard for safety

    /**
     * @var \KrubiK\Attributes\When[] Holds pre-instantiated #[When] guards.
     * This is the key to eliminating runtime reflection in the execution path.
     * The array is populated by the integrateNexus scanner.
     */
    protected array $whenGuards = [];

    /**
     * Route constructor.
     *
     * @param string $pattern The matching pattern (e.g., '/start', '/^hi$/i').
     * @param mixed $action The handler (Closure, [Class, Method], or 'Class@Method').
     * @param array $attributes Metadata (e.g., ['recipient' => 123, 'middleware' => 'auth']).
     * @param Closure|null $nameRegistrar Secret closure to register named routes in the main Router.
     */
    public function __construct(
        string $pattern,
        mixed $action,
        array $attributes = [],
        ?Closure $nameRegistrar = null
    ) {
        $this->pattern = $pattern;
        $this->action = $action;
        $this->nameRegistrar = $nameRegistrar;

        // 1. Extract Middleware for optimized handling
        // We move 'middleware' out of the generic attributes array into the dedicated property
        // to ensure type safety and easier merging later.
        if (isset($attributes['middleware'])) {
            $this->middleware($attributes['middleware']);
            unset($attributes['middleware']);
        }

        // 2. Handle 'withoutGlobalMiddleware' attribute (Compatibility Layer)
        // If the user passed ['withoutGlobalMiddleware' => true/array] in the array definition.
        if (isset($attributes['withoutGlobalMiddleware'])) {
            $val = $attributes['withoutGlobalMiddleware'];
            if ($val === true) {
                $this->skipAllGlobalMiddlewares = true;
            } elseif (is_array($val) || is_string($val)) {
                $this->skipGlobalMiddlewares((array) $val);
            }
            unset($attributes['withoutGlobalMiddleware']);
        }

        // 3. Handle 'as' attribute (Legacy naming)
        if (isset($attributes['as'])) {
            $this->name($attributes['as']);
            // We keep 'as' in attributes for backward compatibility if needed
        }

        // 4. Store remaining attributes (recipients, drivers, limits, etc.)
        $this->attributes = array_merge($this->attributes, $attributes);

        // [THE UPGRADE] The Route becomes self-aware of its type upon birth.
        if (isset($attributes['_route_type'])) {
            $this->type = $attributes['_route_type'];
        }

        // ✨ THE Path_AWAKEN ✨
        // The Route object now analyzes itself upon creation.
        $this->extractPathParameters();
    }

    /**
     * Set a name for the route and register it in the main Router.
     * Usage: ->name('dashboard.index')
     */
    public function name(string $name): self
    {
        $this->name = $name;
        $this->attributes['as'] = $name; // Sync for legacy access

        // Communicate back to the Bot/Router to index this route by name
        if ($this->nameRegistrar) {
            ($this->nameRegistrar)($name, $this);
        }

        return $this;
    }

    /**
     * Get the assigned name of the route.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get the assigned Pattern of the route.
     */
    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    /**
     * Tag this route for later retrieval or grouping logic.
     * Usage: ->tag('admin')
     */
    public function tag(string $tag): self
    {
        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
        return $this;
    }

    /**
     * Check if the route has a specific tag.
     */
    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags);
    }

    /**
     * Add middleware(s) to this specific route.
     * Supports Chaining: ->middleware(A::class)->middleware(B::class)
     * Supports Arrays: ->middleware([A::class, B::class])
     */
    public function middleware(string|array|callable $middleware): self
    {
        $middlewares = is_array($middleware) ? $middleware : [$middleware];
        
        // Merge using array unpacking (Fast & Clean)
        $this->middlewares = [...$this->middlewares, ...$middlewares];
        
        return $this;
    }

    /**
     * Configure skipping of global middlewares.
     * 
     * - If called with no args or empty array: Skips ALL globals.
     * - If called with class names: Skips only those specific globals.
     * 
     * @param array|string $middlewares Class names to skip (optional)
     */
    public function skipGlobalMiddlewares(array|string $middlewares = []): self
    {
        $middlewares = is_array($middlewares) ? $middlewares : [$middlewares];

        if (empty($middlewares)) {
            $this->skipAllGlobalMiddlewares = true;
        } else {
            // Merge new skips with existing skips
            $this->skippedGlobalMiddlewares = array_merge(
                $this->skippedGlobalMiddlewares, 
                $middlewares
            );
        }
        return $this;
    }

    /**
     * Alias for skipGlobalMiddlewares (Laravel style naming).
     */
    public function withoutMiddleware(array|string $middlewares = []): self
    {
        return $this->skipGlobalMiddlewares($middlewares);
    }

    /**
     * Get the action handler.
     */
    public function getAction(): mixed
    {
        return $this->action;
    }

    /**
     * Get all attributes.
     * Automatically injects the current middleware stack into the returned array
     * to ensure consumers of this method (like processUpdate) see the full picture.
     */
    public function getAttributes(): array
    {
        return array_merge($this->attributes, [
            'middleware' => $this->middlewares,
            'withoutGlobalMiddleware' => $this->skipAllGlobalMiddlewares ? true : $this->skippedGlobalMiddlewares
        ]);
    }

    /**
     * THE CORE LOGIC: Compute the final executable middleware stack.
     * 
     * Merges global middlewares with local ones, respecting all skip logic.
     * This is the brain of the middleware resolution.
     * 
     * @param array $globalMiddlewares The list of middlewares defined globally in the Bot.
     * @return array The final ordered list of middlewares to execute.
     */
    public function getMiddlewareStack(array $globalMiddlewares): array
    {
        // 1. Process Globals
        $globalsToRun = [];

        if (!$this->skipAllGlobalMiddlewares) {
            if (empty($this->skippedGlobalMiddlewares)) {
                // Optimization: If no specific skips, use all globals directly
                $globalsToRun = $globalMiddlewares;
            } else {
                // Filter out specific globals
                foreach ($globalMiddlewares as $gm) {
                    // We check if the class name exists in the skipped list
                    if (!in_array($gm, $this->skippedGlobalMiddlewares)) {
                        $globalsToRun[] = $gm;
                    }
                }
            }
        }

        // 2. Merge: Global (First) -> Local Route Middlewares (Second)
        // This ensures globals run first (outer layer), then route specifics (inner layer).
        return array_merge($globalsToRun, $this->middlewares);
    }

    /**
     * 🔥 CRITICAL METHOD FOR processUpdate v5.1
     * Merges Global + Local middlewares correctly.
     */
    public function getMiddlewareStack_v51(array $globalMiddlewares): array
    {
        // 1. Process Globals
        $globalsToRun = [];

        if (!$this->skipAllGlobalMiddlewares) {
            if (empty($this->skippedGlobalMiddlewares)) {
                $globalsToRun = $globalMiddlewares;
            } else {
                foreach ($globalMiddlewares as $gm) {
                    if (!in_array($gm, $this->skippedGlobalMiddlewares)) {
                        $globalsToRun[] = $gm;
                    }
                }
            }
        }

        // 2. Merge: Global (Outer) -> Local (Inner)
        return array_merge($globalsToRun, $this->middlewares);
    }

    /**
     * ✨ HELPER METHOD
     * Analyzes the route's pattern and extracts all parameter placeholders.
     * This method runs only once during the object's lifecycle, ensuring peak performance.
    */
    private function extractPathParameters(): void
    {
        // This regex is greedy and finds all occurrences of {param} or {param?}.
        // It correctly handles alphanumeric and underscore characters in parameter names.
        preg_match_all('/\{([a-zA-Z0-9_]+)\??\}/', $this->pattern, $matches);
        
        if (!empty($matches[1])) {
            $this->pathParameters = $matches[1];
        }
    }

    /**
     * Restrict this route to specific platforms.
     * If this method is never called, the route is available on all platforms.
     *
     * Supports chaining: ->platforms('telegram')->platforms('bale') // Bug: '*' makes him forget his prev-memory
     * Supports arrays: ->platforms(['telegram', 'bale'])
     *
     * @param string|Platform|array<int, string|Platform> $platforms A single platform name or an array of platform names.
     * @return self
    */
    public function platforms(string|array|Platform $platforms): self
    {

        // 1. Ensure the input is an array for consistent processing.
        $rawPlatforms = is_array($platforms) ? $platforms : [$platforms];

        // 2. Normalize every item into its canonical string value.
        $normalizedPlatforms = [];
        foreach ($rawPlatforms as $platform) {
            // Thanks to the Stringable interface on the Platform enum,
            // we can cast both strings and Platform objects to a string uniformly.
            // We also enforce lowercase for canonical storage.
            if (is_string($platform) || $platform instanceof \Stringable) {

                $platformStr = strtolower((string) $platform);

                // Wild-card Support
                if($platformStr === '*') {
                    $this->platforms = [];
                    return $this;
                }

                $normalizedPlatforms[] = $platformStr;
            }
            // Note: We silently ignore any invalid types passed in the array.
        }

        // 3. Merge new platforms with existing ones and ensure absolute uniqueness.
        if (!empty($normalizedPlatforms)) {
            $this->platforms = array_values(array_unique([...$this->platforms, ...$normalizedPlatforms]));
        }

        $newPlatforms = is_array($platforms) ? $platforms : [$platforms];
        
        // Merge new platforms with existing ones and ensure absolute uniqueness.
        // Using the spread operator is modern, clean, and fast.
        $this->platforms = array_values(array_unique([...$this->platforms, ...$newPlatforms]));
        
        return $this;
    }

    /**
     * Get the list of allowed platforms for this route.
     *
     * @return string[] An array of platform names. Returns an empty array if not restricted.
    */
    public function getPlatforms(): array
    {
        return $this->platforms;
    }

    /**
     * 🔥 HYPER-PERFORMANT CHECKER
     * Checks if this route is allowed to run on a given platform.
     * This is the core logic the router's dispatcher will use.
     *
     * @param string $platform The platform name to check (e.g., 'telegram', 'rubika').
     * @return bool True if the route is allowed, false otherwise.
     */
    public function isAllowedOn(string $platform): bool
    {
        // The Covenant: An empty `platforms` array means NO restrictions. The route is universal.
        if (empty($this->platforms)) {
            return true;
        }

        // Otherwise, the platform must explicitly be in the allowed list.
        // `in_array` is highly optimized for this exact use case.
        return in_array($platform, $this->platforms, true);
    }

    /**
     * Sets the access policy for this route.
     * 'strict': Requires an authenticated user (isGuest must be false).
     * 'standard': Allows both guests and authenticated users.
     *
     * @param string $policy The policy name ('strict' or 'standard').
     * @return self
    */
    public function accessPolicy(string $policy): self
    {
        $this->accessPolicy = ($policy === 'strict') ? 'strict' : 'standard';
        return $this;
    }

    /**
     * Gets the access policy for this route.
     *
     * @return string
    */
    public function getAccessPolicy(): string
    {
        return $this->accessPolicy;
    }

    /**
     * Attaches pre-scanned guard attributes to this route.
     * This method is used internally by the Nexus scanner.
     *
     * @param \KrubiK\Attributes\When[] $guards An array of When attribute instances.
     * @return self
    */
    public function guards(array $guards): self
    {
        $this->whenGuards = $guards;
        return $this;
    }

    /**
     * Retrieves the attached guard attributes.
     * Used by the Dispatcher's execution engine.
     *
     * @return \KrubiK\Attributes\When[]
    */
    public function getGuards(): array
    {
        return $this->whenGuards;
    }

    /**
     * Holds the unified list of channel identifiers declared by ForceJoin attributes.
     * This sacred data will be used by the dispatcher to channel the message.
     *
     * @var string[]
    */
    public array $forceJoinChannels = [];

    /**
     * 🔥 THE JUDGE'S DECREE
     * Holds the custom failure message or Judge method name from the ForceJoin attribute.
     * This is populated by the integrateNexus scanner, respecting translate-over-raw && method-over-class precedence.
     * @var null|string
    */
    public ?string $forceJoinMessage = null;
}
