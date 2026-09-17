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

use KrubiK\Extensions\CoreSynapse;
use KrubiK\Extensions\NeonWarp;
use BadMethodCallException;

/**
 * ╔═══════════════════════════════════════════════════════════════════════════╗
 * ║  K R U B I K   C Y B E R C I T A D E L  ---  JACKPOINT EVENT-HOOKING CORE ║
 * ╚═══════════════════════════════════════════════════════════════════════════╝
 * 
 * [SYSTEM OVERRIDE]: Neural Jack initialized. Direct synaptic link established.
 * 
 * JackPoint stands as the forbidden interface at the edge of the grid—a silent, 
 * uninstantiable monolith bridging the raw pulse of the Krubot matrix with 
 * autonomous event-driven architecture. No constructors shall breach this threshold; 
 * only static resonance flows through its veins.
 * 
 * [HYPER-DX EXPANSION]: Dynamic magic dispatch online. The matrix now bends 
 * to fluid string-keys and custom invocation patterns ($class::{'on.render'}(...)).
 * ┌─────────────────────────────────────────────────────┐
 * │  LISTENERS   on / onBefore / onAfter                │
 * │              once / onceBefore / onceAfter          │
 * │              off / offById                          │
 * │  EMISSION    fire / fireAll / fireMap / fireScope   │
 * │  INJECTORS   registerParamTypeInjector              │
 * │              registerParamNameInjector              │
 * │              forgetParamType / forgetParamName      │
 * │  PIPES       onPipe / onPipeBefore / onPipeAfter    │
 * │              oncePipe / oncePipeBefore/oncePipeAfter│
 * │              offPipe / transform                    │
 * │  WP-ALIASES  addFilter / removeFilter               │
 * │              applyFilters / clearFilters            │
 * └─────────────────────────────────────────────────────┘
 *
 * Magic dispatcher — any verb above + PascalCase event path:
 *
 *   JackPoint::onKrubotAwaken(fn() => ...)
 *   JackPoint::reshapeContentRender(fn($v) => trim($v))
 *   JackPoint::phaseContentRender(fn($v) => trim($v))
 *   JackPoint::oncePipeContentRender_Admin(fn($v) => $v)
 *   JackPoint::applyFiltersContentRender_Admin($raw, $post)   // alias for `transform***()`
 *   JackPoint::transformResolveIdentityId($id, $this->user(), $this) // hooks `$bot->senderId()`
 *   JackPoint::fireAllKrubotAwaken($payload)
 * 
 *   JackPoint::stageContentPublishable(fn($p) => $p['words'] >= 50);
 *   JackPoint::isContentPublishable(['author' => 'A', 'spam' => false, 'words' => 60])
 *
 * Scope qualifiers (all four forms are equivalent):
 *   JackPoint::onKrubotAwaken_Admin(...)   // underscore suffix
 *   JackPoint::{'on.krubot.awaken:admin'}(...)  // colon suffix
 *   JackPoint::{'onAdmin:KrubotAwaken'}(...)    // colon prefix
 *   JackPoint::{'on.admin:krubot.awaken'}(...)  // colon prefix
 * 
 * Warning: Unauthorized connection attempts during a high-octane HyperDX sync 
 * will trigger immediate protocol purging. Plug in, fire the payload, and 
 * disappear into the neon shadows.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class JackPoint
{
    /**
     * ⚡ [SYNAPSE CORE INJECTION]
     * -------------------------------------------------------------------------
     * Merging the raw nervous system of the KrubiK matrix into this static monolith.
     * Here is where the interface plugs directly into the event-driven grid, 
     * activating the event listeners, parameter injectors, and multi-bot scopes 
     * with zero-latency reflection power.
     * -------------------------------------------------------------------------
    */
    use CoreSynapse;

    /**
     * 🌌 [ NEON-WARP PROTOCOL :: THE OS-LEVEL EVENT KERNEL ]
     * -------------------------------------------------------------------------
     * Bringing the raw power of low-level filesystem architecture (Junctions, 
     * Symlinks, and HardLinks) straight into the JackPoint event-space. 
     * NeonWarp bends reality by letting multiple event nodes share the exact 
     * same memory pool (Zero-Allocation HardLinks) or dynamically rerouting 
     * signals via Ghost Symlinks. It’s an entire OS-level routing engine, 
     * built purely to hijack and orchestrate the Hooks at light-speed.
     * 
     * You are wielding kernel-level logic inside your application layer.
    */
    use NeonWarp;

    /**
     * Private constructor to prevent instantiation. 
     * You don't "create" a JackPoint; you plug into it.
    */
    private function __construct()
    {
        // Absolute isolation. No instances allowed in the matrix.
    }

    // ─── Magic action registry ────────────────────────────────────────────────
    // Ordered longest-first so preg alternation is first-match-wins.

    /** @var list<string> */
    private const MAGIC_ACTIONS = [
        'onceActionBefore',
        'onceActionAfter',
        'oncePipeBefore',
        'oncePipeAfter',
        'reshapeBefore',
        'reshapeAfter',
        'removeActionById',      // ← must appear before `removeAction` to prevent ignoring by prefix-shadow
        'removeAllActions',
        'addActionBefore',      'phaseBefore',  'stageBefore',
        'addActionAfter',       'phaseAfter',   'stageAfter',
        'onPipeBefore',         'applyFilters', 'removeFilter',
        'onPipeAfter',          'offPipe',
        'onceBefore',           'stage',
        'onceAfter',            'fireScope',    'onBefore',  'addFilter',
        'oncePipe',             'transform',    'hasAction',
        'onAfter',              'fireMap',      'addAction',
        'reshape',              'phase',        'fireAll',      'doAction',
        'onPipe',               'fire',         'onceAction',
        'once',                 'removeAction', 'clearActions',
        'off',
        'on',
        'is',
    ];

    /** Compiled regex — built once, reused on every dispatch. */
    private static ?string $magicPattern = null;

    //  Verb aliases 🔀 — magic verb → real engine method.
    private const MAGIC_VERB_ALIASES = [
        'is' => 'judge',
    ];

    // =====================================================================
    // Bonus-DX: Advanced Neural Magic Dispatcher (on, off, fire)
    // =====================================================================
    /**
     * Intercepts static calls to dynamically route signals across the Synapse network.
     * Supports CamelCase_Scope and Dot.Notation:Scope patterns for precision injection.
     * Intercepts dynamic static method calls across the grid with zero friction.
     * 
     * Allows: 
     * - JackPoint::{'on.krubot.awaken'}(fn() => ...)
     * - JackPoint::{'on.admin:krubot.awaken'}(fn() => ...)
     * - JackPoint::{'on.krubot.awaken:admin'}(fn() => ...)
     * - JackPoint::onKrubotAwaken(fn() => ...)
     * - JackPoint::fireKrubotAwaken(...)
     * - JackPoint::fireKrubotAwaken_Admin(...)
     * 
     * @param string $method      The invoked static method name (e.g., 'onKrubotAwaken', 'fire.system.boot')
     * @param array  $arguments   Arguments passed to the underlying synapse handlers
     * @return mixed
     * @throws BadMethodCallException If the dispatch pattern doesn't match valid synapse protocols
    */
    public static function __callStatic(string $method, array $arguments)
    {
        // Build the compiled pattern lazily (once per process).
        self::$magicPattern ??= '/^(' . implode('|', self::MAGIC_ACTIONS) . ')(.+)$/i';

        // 1. [SYNAPSE ACTION CAPTURE]: Identify the core action prefix (on, off, fire, onBefore, onAfter)
        if (preg_match(self::$magicPattern, $method, $matches)) {

            $rawPayload = $matches[2];          // everything after the verb
            $verb = lcfirst($matches[1]);     // 'on', 'onBefore', etc.
            
            // 🏛️ Alias resolution — verbs like `is` routed to differently-named
            //    engine methods. Verbs absent from the map pass through unchanged,
            //    so the fast path stays a single isset() test.
            $action     = self::MAGIC_VERB_ALIASES[$verb] ?? $verb;

            // 2. [SYNAPSE DECODING]: Extract scope and normalized event
            [$scope, $event] = static::parseMagicRoute($rawPayload);

            // 3. [SYNAPSE BINDING/DISCONNECT/PULSE]: Execute the requested payload within the boundary
            return static::scopedExecute($scope, fn() => static::{$action}($event, ...$arguments));
        }

        throw new BadMethodCallException("Synapse Matrix Error: Signal path [{$method}] not found in the JackPoint grid. Protocol violation detected.");
    }

    /**
     * ─── Route parser ───
     * Parses raw camelCase or dot-notation segments into standard dot-separated event keys.
     * Normalizes incoming signal routes by decoupling Scope from Event identifiers.
     *
     * Rules (in order of priority):
     *   1. Colon `:` → RIGHT side is scope (event:scope OR scope:event, detected by position)
     *   2. Last underscore `_` → everything after it is scope
     *   3. No separator → pure CamelCase event, no scope
     *   Dot `.` is NEVER a scope separator — it is always part of the event path.
     *
     * Examples:
     *   'KrubotAwaken'          → [null,    'nemesis.failure']
     *   'KrubotAwaken_Admin'    → ['admin', 'nemesis.failure']
     *   '.krubot.awaken'        → [null,    'nemesis.failure']
     *   '.admin:krubot.awaken'  → ['admin', 'nemesis.failure']
     *   '.krubot.awaken:admin'  → ['admin', 'nemesis.failure']
     *
     * @param  string $raw  Raw segment following the action prefix (on/off/fire)
     * @return array{0: string|null, 1: string}  [scope|null, normalized-event]
    */
    protected static function parseMagicRoute(string $raw): array
    {
        // Strip decorative leading dot (e.g. ".krubot.awaken" → "nemesis.failure")
        $raw = ltrim($raw, '.');

        $scope     = null;
        $eventPart = $raw;

        // ── Priority 1: Explicit colon separator ─────────────────────────────
        // Supports both "event:scope" and "scope:event" by detecting which side
        // looks like a scope (no dots, single word) vs an event path.
        if (str_contains($raw, ':')) {
            $colon = strrpos($raw, ':');
            $left  = substr($raw, 0, $colon);
            $right = substr($raw, $colon + 1);

            if ($right !== '' && !str_contains($right, ':')) {
                // Determine which side is the scope:
                // A scope is a single plain word (no dots). An event may contain dots.
                $rightIsScope = !str_contains($right, '.') && preg_match('/^\w+$/u', $right);
                $leftIsScope  = !str_contains($left,  '.') && preg_match('/^\w+$/u', $left);

                if ($rightIsScope) {
                    // "nemesis.failure:admin"  → scope=admin,  event=nemesis.failure
                    // "KrubotAwaken:admin"   → scope=admin,  event=KrubotAwaken
                    $scope     = strtolower($right);
                    $eventPart = $left;
                } elseif ($leftIsScope) {
                    // ".admin:krubot.awaken" → scope=admin,  event=nemesis.failure
                    $scope     = strtolower($left);
                    $eventPart = $right;
                }
                // If ambiguous (neither has dots), right-side wins (more natural: "event:scope")
                elseif ($right !== '') {
                    $scope     = strtolower($right);
                    $eventPart = $left;
                }
            }
        }
        // ── Priority 2: Last underscore as implicit scope separator ──────────
        // "KrubotAwaken_Admin" → scope=admin, event part="KrubotAwaken"
        // "My_Deep_Event_Admin"  → scope=admin, event part="My_Deep_Event"
        elseif (str_contains($raw, '_')) {
            $pos  = strrpos($raw, '_');
            $tail = substr($raw, $pos + 1);

            // Tail must be a plain non-empty word to qualify as a scope identifier
            if ($tail !== '' && !str_contains($tail, '.') && preg_match('/^\w+$/u', $tail)) {
                $scope     = strtolower($tail);
                $eventPart = substr($raw, 0, $pos);
            }
            // Otherwise the underscore is part of the event name — leave scope null
        }

        // ── Normalise event segment(s) to dot.notation ────────────────────────
        $event = self::toDotNotation(ltrim($eventPart, '.'));

        return [$scope ?: null, $event];
    }

    /**
     * Convert PascalCase / dot-separated / mixed segments to lowercase dot.notation.
     *
     * 'KrubotAwaken'     → 'nemesis.failure'
     * 'content.render'     → 'content.render'   (already normalised)
     * 'ContentRender'      → 'content.render'
    */
    protected static function toDotNotation(string $raw): string
    {
        // Split on existing dots first
        $segments = array_filter(
            explode('.', $raw),
            static fn(string $s): bool => $s !== '',
        );

        $normalized = array_map(
            static function (string $seg): string {
                // Already lowercase → fast path, no expansion needed
                if ($seg === strtolower($seg)) {
                    return $seg;
                }
                // PascalCase/camelCase → dot.notation
                // "KrubotAwaken" → "nemesis.failure"
                // "renderSidebar"  → "render.sidebar"
                return strtolower(
                    ltrim(preg_replace('/(?<!^)([A-Z])/u', '.$1', $seg), '.')
                );
            },
            array_values($segments),
        );

        return implode('.', $normalized) ?: strtolower($raw) ?: 'unknown';
    }

    /**
     * 📸 FULL ENGINE SNAPSHOT — read-only, serialisation-safe, debug-ready.
     * 
     * Return a read-only snapshot of the entire engine state.
     * Useful for debugging, serialization, or test assertions.
     *
     * Flushes the lazy-sort pass across every registry (events, pipes,
     * injectors) so the materialised lists reflect the exact order the
     * engine will use at dispatch time. Callables are dropped from the
     * result; only priority + id survive — enough for deterministic test
     * assertions and JSON-friendly debugging.
     *
     * Uses array_combine() over parallel array_map() projections, which
     * is measurably faster than a foreach-with-mutation loop for the sizes
     * this registry typically reaches — and reads like a declarative
     * blueprint instead of an imperative chore.
     *
     * @return array{
     *   session_scope:    string|null,
     *   events:           array<"scope:event", list<array{priority:int, id:string|int}>>,
     *   pipes:            array<"scope:event", list<array{priority:int, id:string|int}>>,
     *   once_listeners:   array<"scope:event", list<string|int>>,
     *   once_pipes:       array<"scope:event", list<string|int>>,
     *   type_injectors:   array<"scope:PType", list<int>>,
     *   name_injectors:   array<"scope:PName", list<int>>,
     * }
    */
    public static function snapshot(): array
    {
        static::ensureSynapsesSorted();

        // ── Projection helpers ────

        /** Materialise [priority, id, callable] triplets → readable maps. */
        $projectEntries = static fn(array $registry): array => array_combine(
            array_map(
                static fn(string $k): string => static::prettyKey($k),
                array_keys($registry),
            ),
            array_map(
                static fn(array $entries): array => array_map(
                    static fn(array $e): array => [
                        'priority' => $e[0],
                        'id'       => $e[1],
                    ],
                    $entries,
                ),
                $registry,
            ),
        );

        /** Once-maps (id → {wrapper, original}) → id-only summaries. */
        $projectOnceIds = static fn(array $onceMap): array => array_combine(
            array_map(
                static fn(string $k): string => static::prettyKey($k),
                array_keys($onceMap),
            ),
            array_map(
                static fn(array $ids): array => array_keys($ids),
                $onceMap,
            ),
        );

        /** Injector registries [priority, resolver] → priority-only lists. */
        $projectInjectorPriorities = static fn(array $registry): array => array_combine(
            array_map(
                static fn(string $k): string => static::prettyKey($k),
                array_keys($registry),
            ),
            array_map(
                static fn(array $entries): array => array_column($entries, 0),
                $registry,
            ),
        );

        return [
            'session_scope'  => static::$sessionScope,
            'events'         => $projectEntries(static::$eventSynapses),
            'pipes'          => $projectEntries(static::$pipes),
            'once_listeners' => $projectOnceIds(static::$onceListenerMap),
            'once_pipes'     => $projectOnceIds(static::$oncePipeMap),
            'type_injectors' => $projectInjectorPriorities(static::$paramTypeInjectors),
            'name_injectors' => $projectInjectorPriorities(static::$paramNameInjectors),
            'internal_links' => method_exists(static::class, 'allLinks') ? static::allLinks() : []
        ];
    }
}