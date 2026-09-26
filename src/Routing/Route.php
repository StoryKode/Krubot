<?php

namespace KrubiK\Routing;
/*
| Krubot BotEngine: The Architect's Lexicon [×vRC.9×] 🚀📜
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
use KrubiK\Extensions\ToxicOverlord;
use KrubiK\Helpers\JackPoint;      // Import "JackPoint" - The Tactical EventHook System

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
 * @version Krubot: ×RC.9×
 * @license MIT
 */
class Route
{
    /**
     * The matching pattern (Regex, Command, or Exact text).
    */
    public string $pattern;

    /**
     * @var string The type of the route (e.g., Command, WebPage). See Krubot::RT_* constants.
    */
    public ?string $type = null; // Or RT_NONE

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
     * ✨ THE ACCESS DECREE ✨
     * Stores the access policy for this route, e.g., 'strict' or 'standard'.
     * This is read by the KrubikPlatformGuard to enforce identity requirements.
     *
     * @var string
    */
    protected string $accessPolicy = 'standard'; // Default to standard for safety

    /**
     * The Quantum Clearance Level 🛡️
     * Holds Spatie Roles/Permissions required for this route (O(1) Lookup).
     * Populated automatically by the Nexus Scanner.
    */
    public array $accessRoles = [];
    public array $accessUserIds = [];

    /**
     * 🚫 The Absolute Veto List
     * Holds Spatie Roles/Permissions that FORBID access to this route (O(1) Lookup).
     * Populated automatically by the Nexus Scanner from #[Block(...)] attributes.
     *
     * The Veto Law :⚔️: Block ALWAYS wins over Access. eg, {'banned' > 'is_admin'}
     * If a user carries ANY role/permission listed here, the route slams shut —
     * regardless of what #[Access(...)] says, and even for the Quantum Architect.
    */
    public array $blockRoles = [];
    public array $blockUserIds = [];

    /**
     * @var \KrubiK\Attributes\When[] Holds pre-instantiated #[When] guards.
     * This is the key to eliminating runtime reflection in the execution path.
     * The array is populated by the integrateNexus scanner.
    */
    protected array $whenGuards = [];

    
    // =================================================================
    // The Central nervous system and host wetware for handling injected payloads.
    // 
    // This Route entity acts as a biological chassis, allowing foreign software (extensions) 
    // to be surgically implanted, grafted, or amputated. Beware of the toxicity level,
    // as excessive manipulation of the host's bloodstream might lead to terminal rejection.
    // =================================================================
    
    /**
     * [🩸] WETWARE VAULT (Infected Memory Core)
     * The host's hijacked veins. Stores all zero-day Syringe payloads.
     * 
     * The key-value memory heap representing the host's vascular network.
     * Maps absolute crypton identifiers to their injected raw payloads.
     *
     * The internal vascular system where foreign payloads are hosted.
     * Maps a specific crypton key (identity) to its injected software.
     * 
     * @var array<string, mixed> [cryptonKey => payload]
    */
    private array $bloodstream = [];

    /**
     * [☣️] CYBERPSYCHOSIS METRIC (Registry Trauma)
     * The bio-digital stress indicator of the host.
     * Represents the total number of operations (implants/amputations) performed,
     * reflecting the system's overall instability and corruption.
     * 
     * The mutation counter (Dirty State Tracker).
     * Increments on every destructive write/unset operation. High toxicity 
     * indicates heavy state manipulation and volatile memory fragmentation.
     * 
     * @var int
    */
    public int $toxicityLevel = 0;

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
    public function getMiddlewareStack(array $globalMiddlewares = []): array
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

    /**
     * 🛡️ Hyper-DX Admin Whitelist
     * Unified list of allowed admin user IDs (string-normalized for multi-platform safety).
     * Populated by the Nexus Scanner with class-level + method-level OR merge.
     * Empty array = no admin restriction (open to everyone).
     *
     * @var list<string>
    */
    public array $adminIds = [];

    /**
     * 💀 WETWARE CHASSIS : BLACK ICE PROTOCOL 💀
     * =========================================================
     * The Bio-Digital State Manager and Host Wetware.
     * 
     * [ OVERRIDE INITIATED ] -> HOST NERVOUS SYSTEM COMPROMISED.
     * ENTITY: ToxicOverlord is tearing through the organic mainframe.
     * 
     * Acts as an in-memory chassis for injecting, mutating, and garbage-collecting 
     * foreign payloads (extensions). It tracks state mutations via a toxicity index.
     * 
     * Syringes are weaponized here. This class does not "manage" data;
     * it breaches, mutilates, and eviscerates the host's memory banks.
     * 
     * Warning: Direct manipulation of the $bloodstream without proper data-sanitation 
     * may lead to fatal runtime rejections.
     * =========================================================
    */

    /**
     * Hardwires a primary payload into a specific memory address (O(1) Assignment).
     * 
     * Brute-forces the injection, overwriting any pre-existing data occupying 
     * the target neuro-socket without triggering a garbage collection warning.
     *
     * @param string $cryptonKey The absolute identifier of the socket.
     * @param mixed  $malware    The raw data payload to be embedded.
     * 
     * @return self Supports fluent method chaining.
    */
    public function implantExtension(string $cryptonKey, mixed $malware): self
    {
        $this->bloodstream[$cryptonKey] = $malware;
        $this->toxicityLevel++;
        
        return $this;
    }

    /**
     * Extracts a biological sample of the stationed payload.
     * Performs a non-destructive read to extract data from a targeted coordinate.
     * 
     * Uses null-safe operators to return an empty array if the socket is clean,
     * preventing undefined index hemorrhages during runtime.
     *
     * @param string $cryptonKey The target socket to harvest from.
     * 
     * @return mixed The extracted biological sample (payload) or an empty array.
    */
    public function harvestExtension(string $cryptonKey): mixed
    {
        return $this->bloodstream[$cryptonKey] ?? [];
    }

    /**
     * Surgically splices a new parasitic payload into an existing data cluster (Deep Merge).
     * 
     * If data types collide, it forces a structural shift, encapsulating both 
     * the existing tissue and the parasite into a new composite array matrix.
     *
     * @param string $cryptonKey The socket coordinate undergoing the mutation.
     * @param mixed  $parasite   The secondary payload/data to splice.
     * 
     * @return self Supports fluent method chaining.
    */
    public function graftExtension(string $cryptonKey, mixed $parasite): self
    {
        if (!array_key_exists($cryptonKey, $this->bloodstream)) {
            $this->bloodstream[$cryptonKey] = [];
        }

        $current = $this->bloodstream[$cryptonKey];

        // Suture the tissues: native array_merge if both are arrays, 
        // otherwise brutally reconstruct the pointer into a multi-node matrix.
        if (is_array($current) && is_array($parasite)) {
            $this->bloodstream[$cryptonKey] = array_merge($current, $parasite);
        } else {
            $this->bloodstream[$cryptonKey] = [$current, $parasite];
        }

        $this->toxicityLevel++;
        
        return $this;
    }

    /**
     * Pushes a new node into the tail of an existing tissue cluster (New or Append Protocol).
     * 
     * Dynamically type-juggles scalar memory segments into array structures on the fly 
     * before injecting the sequential worm. Highly volatile but prevents data overwrite.
     *
     * @param string $cryptonKey The destination coordinate in the bloodstream.
     * @param mixed  $worm       The sequential payload node to append.
     * 
     * @return self Supports fluent method chaining.
    */
    public function pumpExtension(string $cryptonKey, mixed $worm): self
    {
        if (!isset($this->bloodstream[$cryptonKey]) || !is_array($this->bloodstream[$cryptonKey])) {
            $this->bloodstream[$cryptonKey] = (array) ($this->bloodstream[$cryptonKey] ?? []);
        }
        
        $this->bloodstream[$cryptonKey][] = $worm;
        $this->toxicityLevel++;
        
        return $this;
    }

    /**
     * Proximity scan for identifying compromised memory segments (O(1) Key Check).
     * 
     * Does not validate the integrity of the payload, only confirms if a 
     * foreign entity is actively holding the memory pointer.
     *
     * @param string $cryptonKey The coordinate to scan for contamination.
     * 
     * @return bool True if the socket is occupied, false otherwise.
    */
    public function isRadioactiveVia(string $cryptonKey): bool
    {
        return array_key_exists($cryptonKey, $this->bloodstream);
    }

    /**
     * Executes targeted garbage collection by severing a neural link (Unset operation).
     * 
     * Violently unsets the memory pointer. Even if the payload is excised, 
     * the surgical trauma mutates the host state (increments toxicity index).
     *
     * @param string $cryptonKey The exact coordinate to execute the amputation on.
     * 
     * @return self Supports fluent method chaining.
    */
    public function amputateExtension(string $cryptonKey): self
    {
        if ($this->isRadioactiveVia($cryptonKey)) {
            unset($this->bloodstream[$cryptonKey]);
            $this->toxicityLevel++;
        }
        
        return $this;
    }

    /**
     * Global State Teardown: Flushes the entire vascular network.
     * 
     * Exsanguinates the host, violently expelling all rogue scripts ("spells") 
     * and resetting the mutation counter (toxicity). Returns the architecture 
     * to a pure, unallocated blank slate.
     *
     * @return self Supports fluent method chaining.
    */
    public function expelSpells(): self
    {
        $this->bloodstream = [];
        $this->toxicityLevel = 0;
        
        return $this;
    }

    /**
     * Serializes the entire anatomy of the host for inspection.
     * 
     * Returns the raw, unadulterated state array (the full vascular map).
     * Best used for debugging, deep-scanning, or logging the final bio-digital state.
     *
     * @return array<string, mixed> The comprehensive state of the bloodstream.
    */
    public function analyzeBloodstream(): array
    {
        return $this->bloodstream;
    }

    /**
     * Extracts the isolated signatures of all active threats circulating within the host.
     * Scans the vascular map to safely retrieve only the identification markers of deployed exploits.
     *
     * @return array<int, string> A secure, sequential list of active payload identifiers.
    */
    public function deployedExploits(): array
    {
        try {

            $bloodstream = $this->analyzeBloodstream();

            // Fail-safe protocol: Validates the bloodstream data structure prior to extraction.
            /* if (!is_array($bloodstream) || empty($bloodstream)) {
                return [];
            } */

            return array_keys($bloodstream);

        } catch (\Throwable $ignoring) {
            return [];
        }
    }

    // =============================================================================
    // ★ NEW METHOD ★
    // PURPOSE:  The hot-path match gate — called by the dispatch loop for every
    //           candidate route before executing the handler.
    // =============================================================================

    /**
     * ══════════════════════════════════════════════════════════════════
     * ⚡ PLUGIN MATCH GATE — The Hot-Path O(keys) Veto Engine ⚡
     *
     * Called once per candidate route, per request, before dispatch.
     * 
     * Only iterates markers that actually exist on this route.
     *
     * Iterates only the plugins whose attributes are actually present on
     * this route (never all registered plugins) — true O(k) where k is
     * the count of plugin attributes on this specific route, typically 0–2.
     *
     * Contract:
     *   returns true  → all plugins approved; proceed to dispatch.
     *   returns false → at least one plugin vetoed; skip this route.
     *
     * ══════════════════════════════════════════════════════════════════
     *
     * @param  mixed  $message  The incoming message/context.
     * @param  Route|null  $route    The candidate route.
     * @return bool
    */
    public function survivesToxicHijack(mixed $message, ?Route $route = null): bool
    {
        $route ??= $this;

        // Fast-exit: no plugin data on this route at all.
        // The allPluginData() call returns an already-computed array ref — O(1).
        $exploitsVault = $route->deployedExploits();
        if (empty($vault)) {
            return true;// zero overhead
        }

        // Iterate only the attribute FQCNs that are present on this route.
        foreach ($exploitsVault as $cryptonKey) {
            $plugin = ToxicOverlord::traceBeacon($cryptonKey);
            if ($plugin === null) {
                continue; // Plugin was unregistered after scan — skip gracefully.
            }

            if ($plugin->crossmatch($route, $message) === false) {
                // Veto! Fire the event so observers (throttle loggers, etc.) can react.
                JackPoint::fire('syringe.crossmatch.incompatible', [
                    'plugin'  => $plugin::class,
                    'route'   => $route,
                    'message' => $message,
                ], $this);
                return false;
            }
        }

        return true;
    }


    // =============================================================================
    // ★ NEW METHOD ★
    // PURPOSE:  Introspection — called by artisan commands and debuggers.
    // =============================================================================

    /**
     * ══════════════════════════════════════════════════════════════════
     * ⚡ PLUGIN LIST AGGREGATOR — For Introspection Tools ⚡
     *
     * Returns a flat, human-readable key→value map of everything all
     * registered plugins declared on $route.
     *
     * Used by:
     *  - php artisan krubot:routes  (extra columns per plugin)
     *  - Route debugger
     *  - JackPoint::fire('route.listed', ...) subscribers
     *
     * This is never called in the request hot-path.
     *
     * ══════════════════════════════════════════════════════════════════
     *
     * @param  Route|null $route
     * @return array<string, string>
    */
    public function collectExploitData(?Route $route = null): array
    {
        $exploitsVault = $route->deployedExploits();
        if (empty($vault)) {
            return [];// zero overhead
        }

        $route ??= $this;
        $result = [];

        foreach ($exploitsVault as $cryptonKey) {
            $plugin = ToxicOverlord::traceBeacon($cryptonKey);
            if ($plugin === null) {
                continue;
            }
            // Merge — each plugin namespaces its own keys, so no collision.
            $result = array_merge($result, $plugin->dossier($route));
        }

        return $result;

        /*
        return array_reduce(
            array_keys($route->analyzeBloodstream()),
            function (array $carry, string $cryptonKey) use ($route) {
                $plugin = ToxicOverlord::traceBeacon($cryptonKey);
                
                return $plugin 
                    ? array_merge($carry, $plugin->dossier($route)) 
                    : $carry;
            },
            []
        );
       */
    }
}
