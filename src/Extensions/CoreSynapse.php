<?php

namespace KrubiK\Extensions;
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

use Closure;
use Generator;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use UnexpectedValueException;

/**
|--------------------------------------------------------------------------
| Extensions CoreSynapse  [×vRC.9 — Hyper Event Engine×]
|--------------------------------------------------------------------------
| Inspired by Game Hook Systems, re-architected for PHP Mastery.
| 
| Now with true Event Synapses.
 *
 * CoreSynapse — xvRC.9 "Ultra-Hyper-DX"
 *
 * Scoped priority event bus + DI injector chain.
 * Zero dependencies. Drop in as a trait on any static class.
 *
 * Scope key format  : "<scope>\0<event>"  (null-byte; never appears in user strings)
 * Priority ladder   : BEFORE(0) → EARLY(10) → NORMAL(50) → LATE(90) → AFTER(100)
 * Fire semantics    : first non-null return short-circuits (was broken in xRC.9)
 * Injectors         : TYPE wins over NAME; within each registry highest-priority first
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait CoreSynapse
{
    /**
     * 🧠 THE GATEKEEPER — null = asleep (not yet resolved).
     *
     * This acts as the brain for the entire extension system. 
     * Once it picks a side (true/false), it locks in to save CPU cycles.
     * 
     * If you change the battlefield parameters, call checkOrders() 
     * to wake it up and force a re-evaluation.
     *
     * @var bool|null
    */
    protected static ?bool $allowedToIntervention = null;

    // ╔═════════════════════════════════════════════════════╗
    // ║  HIJACK THE JACK ENGINE ! Watch • Veto • Override   ║
    // ╚═════════════════════════════════════════════════════╝
    /**
     * 🚧 RE-ENTRANCY GUARD — blocks recursive synapsesOn() resolution.
     *
     * A listener on `__jack:synapsesOn.decide` may itself call fire(),
     * which reads the gate again — before the memo has been written.
     * When true, synapsesOn() short-circuits to `true` (safe default)
     * instead of recursing into an infinite loop.
     *
     * @var bool
    */
    protected static bool $synapsesResolving = false;

    /**
     * 🌐 ENGINE SCOPE — reserved namespace for internal lifecycle hooks.
     *
     * Every hook fired by the engine itself lives under `__jack:*`.
     * This scope is session-proof: tenant switches and `setSessionScope()`
     * calls never touch it, so plugin hooks stay bound regardless of
     * which tenant is currently active.
     *
     * @var string
    */
    public const ENGINE_SCOPE = '__jack';

    // =====================================================================
    // 🔌 EVENT SYNAPSE REGISTRY
    // Layout: [ eventName => [ [priority, id, callable], ... ] ]
    // =====================================================================

    /** @var array<string, array<int, array{0:int, 1:string|int, 2:callable}>> */
    protected static array $eventSynapses = [];

    /** @var array<string, array<int, array{0:int, 1:callable}>> */
    protected static array $paramTypeInjectors = [];

    /** @var array<string, array<int, array{0:int, 1:callable}>> */
    protected static array $paramNameInjectors = [];

    /**
     * Per-key Dirty map — event synapses need a lazy sort before the next read.
     * Now only event buckets that received a new listener, will re-sorted on the next read.
     *
     * @var array<string, bool>
    */
    protected static array $synapsesDirty = [];
    /** Per-key dirty map — injector keys are dirty. */
    protected static array $injectorsDirty = [];

    // =====================================================================
    // 🧬 TRANSFORMER SYNAPSES — WordPress-style value pipelines
    // Layout: [ "scope\0event" => [ [priority, id, transformer], ... ] ]
    //
    // Difference from `fire()`:
    //   • fire()      → first non-null SHORT-CIRCUITS.
    //   • transform() → value FLOWS THROUGH every transformer in priority
    //                   order; a non-null return REPLACES the value; null
    //                   is a graceful PASSTHROUGH (value unchanged).
    // =====================================================================

    /** @var array<string, array<int, array{0:int, 1:string|int, 2:callable}>> */
    protected static array $pipes = [];

    /**
     * 🪢 Once-Pipe Registry — same hardened shape as $onceListenerMap,
     * but for the value-pipeline layer.
     *
     * @var array<string, array<string|int, array{wrapper: callable, original: callable}>>
    */
    protected static array $oncePipeMap = [];

    /**
     * Per-key dirty map for the transformer registry.
     *
     * Mirrors $injectorsDirty: only the affected scope\0event key is marked,
     * so the lazy sort pass re-sorts O(k) buckets instead of O(n) every time.
     *
     * @var array<string, bool>
    */
    protected static array $pipesDirty = [];

    /**
     * Dedicated once-listener registry — replaces the bare $onceListenerMap.
     *
     * Stores the ORIGINAL unwrapped callable alongside its auto-removal wrapper
     * so that (a) we can correctly detect duplicates before registering, and
     * (b) off() / clear() can clean up without wrapper-identity tricks.
     *
     * Layout:
     *   array<
     *     string,          // resolved scope\0event key
     *     array<
     *       string|int,    // listener id
     *       array{
     *         wrapper:  callable,   // the self-removing closure passed to on()
     *         original: callable,   // the original callable supplied by the caller
     *       }
     *     >
     *   >
     *
     * @var array<string, array<string|int, array{wrapper: callable, original: callable}>>
    */
    protected static array $onceListenerMap = [];

    // Priority ladder constants (WordPress-inspired)
    public const PRIORITY_BEFORE = 0;
    public const PRIORITY_EARLY  = 10;
    public const PRIORITY_NORMAL = 50;
    public const PRIORITY_LATE   = 90;
    public const PRIORITY_AFTER  = 100;
    
    // Semantic Aliases 🎯 (Hyper-DX & developer-friendly)
    public const PRIORITY_CRITICAL  = self::PRIORITY_BEFORE; // 0   - 🚨 Absolute highest urgency. Executes first, no exceptions!
    public const PRIORITY_HIGH      = self::PRIORITY_EARLY;  // 10  - ⚡ High importance. Runs just before standard operations.
    public const PRIORITY_STANDARD  = self::PRIORITY_NORMAL; // 50  - ⚖️ The default baseline. Perfect for everyday, standard tasks.
    public const PRIORITY_LOW       = self::PRIORITY_LATE;   // 90  - ☕ Low priority. Defers execution until core tasks are done.
    public const PRIORITY_MINOR     = self::PRIORITY_AFTER;  // 100 - 💤 Absolute lowest priority. Runs last, ideal for background cleanup.

    /** Default scope when no `scope:` prefix is present. */
    public const DEFAULT_SCOPE = 'krubot';

    /** Internal scope/event separator — null byte, collision-safe. C++ Memorial... ;) */
    private const SCOPE_SEP = "\0";

    /** Active session scope — plugins may call setSessionScope('admin') at request time. => (null) = clears to default */
    protected static ?string $sessionScope = null;

    // ╔══════════════════════════════════════════════════════════════════════╗
    // ║  🏛️ JUDGE REGISTRY — narrative boolean "extendable" verdict pipelines ║
    // ║              (with NeonWarp (junctionz) integration)                 ║
    // ╚══════════════════════════════════════════════════════════════════════╝
    // Layout mirrors $eventSynapses: [ resolvedKey => [ [priority, id, juror] ] ]
    //
    // Difference from existing primitives:
    //   fire()      → first non-null SHORT-CIRCUITS (value, not bool)
    //   transform() → value FLOWS THROUGH every transformer (mutation)
    //   judge()     → boolean DELIBERATION with three conviction modes:
    //                   default     → stop at first false (fail-fast)
    //                   accord      → all run, count votes, majority wins
    //                   influential → all run, weight by priority, majority wins
    //
    // NeonWarp integration:
    //   resolveLinkedEventKey() mirrors resolveLinkedEventKey() so that
    //   soft-links (junction / symlink / temporaryJunction) are honoured
    //   transparently. Virtual-event guard matches the on() guard.
    // =====================================================================

    // ── Judgment modes ────────────────────────────────────────────────────
    public const VMODE_PIPELINE     = 'pipeline';     // fail-fast (Default)
    public const VMODE_ACCORD       = 'accord';       // flat majority (more listeners answered true)
    public const VMODE_INFLUENTIAL  = 'influential';  // weighted majority (more*000 listeners answered true)

    /** @var array<string, array<int, array{0:int, 1:string|int, 2:callable}>> */
    protected static array $conclaves = [];
    /** Per-key dirty flag — mirrors $pipesDirty; only affected keys will be re-sorted. */
    protected static array $conclavesDirty = [];

    // ╔═══════════════════════════════════════════════════════════════════════╗
    // ║                                                                       ║
    // ║  🎛️ THE INTERVENTION "Toggling" LAYER ; Read • Invalidate • Command    ║
    // ║                                                                       ║
    // ║  One memoized boolean governs every hot path. synapsesOn() reads it,  ║
    // ║  checkOrders() drops it, turn() writes both memo + config in one move.║
    // ║  Dark mode is indistinguishable from "empty registry" — by design.    ║
    // ╚═══════════════════════════════════════════════════════════════════════╝

    /**
     * 🔁 checkOrders() — drop the memoized verdict.
     *
     * Wire this into test setUp/tearDown, tenant switches, or any place
     * that hot-patches `krubot.extensions.enabled` at runtime. Cheap:
     * one assignment, next call re-reads config.
    */
    public static function checkOrders(): void
    {
        $prev = static::$allowedToIntervention;
        static::fireEngineHook('refresh.before', $prev);

        if (static::judge(static::engineHook('invalidate.intervention'), [$prev], static::VMODE_INFLUENTIAL) === false) {
            return;
        }

        static::$allowedToIntervention = null;
        static::fireEngineHook('refresh.after', $prev);
    }

    /**
     * 🛡️ synapsesOn() — the boolean that governs every hot path.
     *
     * Design contract:
     *   • true  ⇒ engine is live; normal synaptic behavior.
     *   • false ⇒ engine is dark; fire/transform/inject return raw input.
     *
     * The `function_exists('config')` guard keeps this trait boot-safe in
     * bare-PHP contexts (unit tests, CLI benchmarks) where Laravel's helper
     * stack isn't loaded — the trait degrades to "enabled" by default.
     *
     * Call JackPoint::checkOrders(); after any runtime config mutation.
    */
    protected static function synapsesOn(): bool
    {
        return static::$allowedToIntervention ??= (bool) (
            \function_exists('config')
                ? \config('krubot.extensions.enabled', true)
                : true
        );
    }
    public static function synapsesUp(): bool
    {

        if (static::$synapsesResolving) {
            return true;   // re-entrancy safe-default
        }

        static::$synapsesResolving = true;

        try {
            $prev = static::$allowedToIntervention;
            static::fireEngineHook('health.resolving', $prev);

            // 🌱 Seed — config is the baseline truth.
            $resolved = static::synapsesOn();

            // 🧬 Every plugin may reshape the verdict.
            //    Priority order, all transformers run, last write wins.
            //    Return null = passthrough (leave the seed untouched).
            $resolved = (bool) static::transform(
                static::engineHook('health.state'),
                $resolved,
                static::$allowedToIntervention, $prev
            );
            
            static::fireEngineHook('health.resolved', $resolved, $prev);

            return $resolved;

        } finally {
            static::$synapsesResolving = false;
        }
    }

    /**
     * 🔁 turn() — Execute the order. Intervene, or stand down.
     *
     * Flips the master switch in ONE move and syncs BOTH layers of truth:
     *   1. The process-local memo ($allowedToIntervention) — instant, zero-alloc.
     *   2. The Laravel config key (krubot.extensions.enabled) — global, durable.
     *
     * Accepts int OR bool — because the matrix doesn't care about your typing:
     *   JackPoint::turn(1)      // intervene
     *   JackPoint::turn(0)      // stand down
     *   JackPoint::turn(true)   // intervene
     *   JackPoint::turn(false)  // stand down
     *
     * Returns the resolved boolean so tests can assert in a single line:
     *   assert(JackPoint::turn(false) === false);
     *
     * On the NEXT call to synapsesOn(), the memo answers immediately —
     * no config read, no function_exists() check, just a property lookup.
     *
     * @return bool  The new engine state (true = live, false = dark).
    */
    public static function turn(bool|int $state): bool
    {
        // Normalize: int is truthy-by-zero-convention, bool passes through.
        $on = \is_int($state) ? ($state > 0) : $state;

        $prev = static::$allowedToIntervention;

        static::fireEngineHook('turn.before', $on, $prev, $state);

        // 🧬 what does the target become?
        //    Every layer runs (priority order); last non-null wins.
        //    null = passthrough → seed ($on) survives untouched.
        $on = (bool) static::transform(
            static::engineHook('turn.command'),
            $on,

            // payload...
            $prev,
            $state
        );

        // 🏛️ The conclave convenes: may this transition proceed?
        //    No jurors staged  → sovereign passes unopposed (true).
        //    Pipeline mode     → first "false" return of any juror, veto short-circuits.
        $permitted = static::judge(
            static::engineHook('turn.allow'),
            ['current' => $prev, 'new' => $on, 'input' => $state],
        );

        // Motion denied → the gate stays exactly as it was.
        if (!$permitted) {
            return $prev ?? false;
        }

        // ── Hot-swap the memo. Next synapsesOn() reads this verbatim.
        static::$allowedToIntervention = $on;

        // ── Persist to config array in memory; so any other reader stays in sync.
        //    Guarded — bare-PHP tests / CLI benchmarks may not have config().
        if (\function_exists('config')) {
            \config(['krubot.extensions.enabled' => $on]);
        }

        static::fireEngineHook('turn.after', $on, $prev, $state);

        return $on;
    }

    /**
     * ⚡ turnOn() — Sugar. Flip the switch to LIVE.
     *
     * Equivalent to JackPoint::turn(true). Symmetric with turnOff().
     * Fires the engine back up: fire/transform/inject resume normal dispatch.
    */
    public static function turnOn(): bool
    {
        return static::turn(true);
    }

    /**
     * 🌑 turnOff() — Sugar. Flip the switch to DARK.
     *
     * Equivalent to JackPoint::turn(false). Every hot path short-circuits:
     *   • fire family → null / [] / [] / []
     *   • transform family → seed value untouched
     *   • injectParam → null (unclaimed)
    */
    public static function turnOff(): bool
    {
        return static::turn(false);
    }
 
    // ╔═══════════════════════════════════════════════════════════════════════╗
    // ║                                                                       ║
    // ║  🔌 THE LISTENER HOOKZ LAYER — Bind • Prioritize • Disarm             ║
    // ║                                                                       ║
    // ║  Every synapse enters through on(), its once-variant self-destructs   ║
    // ║  in a finally block. Scope is resolved at write-time, priority is     ║
    // ║  resolved lazily at fire-time. Registration is always warm — the      ║
    // ║  gate silences dispatch, never the registry.                          ║
    // ╚═══════════════════════════════════════════════════════════════════════╝

    /**
     * Register a listener on an event.
     * 
     * @param string          $event     Event name (e.g. 'bot.process', 'message.received')
     *                                   — default scope or explicit "scope:event".
     * @param callable        $listener  fn(...$payload): mixed
     * @param int             $priority  Lower = earlier (0 = before, 100 = after)
     * @param string|int|null $id        Optional unique ID for precise removal
     *                                   defaults to spl_object_id.
     * @return string|int      The explicit or resolved id.
    */
    public static function on(
        string $event,
        callable $listener,
        int $priority = self::PRIORITY_NORMAL,
        string|int|null $id = null
    ): string|int {
       
        if (method_exists(static::class, 'isVirtual') && static::isVirtual($event)) {
            throw new \LogicException("Cannot attach listener directly to virtual event '{$event}'. Use a junction.");
        }
        
        $id ??= static::generateListenerId($listener); // fallback unique id
        $key = static::resolveEventKey($event);
        static::$eventSynapses[$key][] = [$priority, $id, $listener];
        static::$synapsesDirty[$key] = true;

        return $id;
    }

    /**
     * Sugar: register before all normal listeners.
    */
    public static function onBefore(string $event, callable $listener, string|int|null $id = null): string|int
    {
        return static::on($event, $listener, self::PRIORITY_BEFORE, $id);
    }

    /**
     * Sugar: register After! all normal listeners.
    */
    public static function onAfter(string $event, callable $listener, string|int|null $id = null): string|int
    {
        return static::on($event, $listener, self::PRIORITY_AFTER, $id);
    }

    /**
     * 🪄 ONE-SHOT SYNAPSE — register a listener that disarms itself.
     *
     * The wrapper erases its own synapse in a `finally` block, guaranteeing
     * disposal even if the listener throws. All tracking flows through
     * removeSynapse() so $onceListenerMap stays in lockstep with $eventSynapses.
     *
     * @param  string          $event     Event name (explicit "scope:event" honoured)
     * @param  callable        $listener  fn(...$payload): mixed
     * @param  int             $priority  Lower = earlier
     * @param  string|int|null $id        Optional stable id (auto-generated if null)
     * @return string|int      The resolved listener id.
    */
    public static function once(
        string $event,
        callable $listener,
        int $priority = self::PRIORITY_NORMAL,
        string|int|null $id = null
    ): string|int {

        $id ??= static::generateListenerId($listener); // fallback unique id
        $resolvedKey = static::resolveEventKey($event);

        // ── Duplicate guard: never stack two wrappers on the same id.
        if (isset(static::$onceListenerMap[$resolvedKey][$id])) {
            static::removeSynapse($resolvedKey, $id);
        }

        // Self-disarming wrapper — captures the RESOLVED key so disposal
        // survives session-scope mutations between now and fire-time.
        $onceWrapper = static function (...$args) use ($resolvedKey, $listener, $id) {
            try {
                return $listener(...$args);
            } finally {
                static::removeSynapse($resolvedKey, $id);
            }
        };

        // ── Hardened metadata: keep BOTH identities.
        static::$onceListenerMap[$resolvedKey][$id] = [
            'wrapper'  => $onceWrapper,
            'original' => $listener,
        ];

        return static::on($event, $onceWrapper, $priority, $id);
    }

    /**
     * 🪄 Sugar: one-shot BEFORE the normal priority band.
    */
    public static function onceBefore(
        string $event,
        callable $listener,
        string|int|null $id = null
    ): string|int {
        return static::once($event, $listener, self::PRIORITY_BEFORE, $id);
    }

    /**
     * 🪄 Sugar: one-shot AFTER the normal priority band.
    */
    public static function onceAfter(
        string $event,
        callable $listener,
        string|int|null $id = null
    ): string|int {
        return static::once($event, $listener, self::PRIORITY_AFTER, $id);
    }

    /**
     * Fire an event. First listener that returns non-null short-circuits.
     * short-circuits on and returns the FIRST non-null result.
     *
     * @return mixed  The first non-null listener result, or null if none fired.
    */
    public static function fire(string $event, mixed ...$payload): mixed
    {
        // 🚦 Engine dark ⇒ indistinguishable from "no listeners".
        // Caller cannot tell whether the bus was silent or powered down.
        if (!static::synapsesOn()) {
            return null;
        }

        static::ensureSynapsesSorted();

        $key = static::resolveLinkedEventKey($event);

        if (empty(static::$eventSynapses[$key])) {
            return null;
        }

        foreach (static::$eventSynapses[$key] as [, , $listener]) {
            $result = $listener(...$payload);
            if ($result !== null) {
                return $result;   // ← short-circuit the First non-null. Here you can control that Last non-null returned, simpler than you think...
            }
        }

        return null;
    }

    /**
     * Fire and collect ALL non-null results (useful for filters/pipelines).
     *
     * @return list<mixed>
    */
    public static function fireAll(string $event, mixed ...$payload): array
    {
        // 🚦 Engine dark ⇒ empty result set (same as empty registry).
        if (!static::synapsesOn()) {
            return [];
        }

        static::ensureSynapsesSorted();

        $key     = static::resolveLinkedEventKey($event);
        $results = [];

        if (empty(static::$eventSynapses[$key])) {
            return $results;
        }

        foreach (static::$eventSynapses[$key] ?? [] as [, , $listener]) {
            $result = $listener(...$payload);
            if ($result !== null) {
                $results[] = $result;
            }
        }

        return $results;
    }

    /**
     * Fire and collect ALL return values (including nulls), keyed by listener id.
     *
     * @return array<string|int, mixed>
    */
    public static function fireMap(string $event, mixed ...$payload): array
    {
        // 🚦 Engine dark ⇒ empty map (id ⇒ result pairs vanish entirely).
        if (!static::synapsesOn()) {
            return [];
        }

        static::ensureSynapsesSorted();

        $key     = static::resolveLinkedEventKey($event);
        $results = [];

        if (empty(static::$eventSynapses[$key])) {
            return $results;
        }

        foreach (static::$eventSynapses[$key] ?? [] as [, $id, $listener]) {
            $results[$id] = $listener(...$payload);
        }

        return $results;
    }

    /**
     * Check if any listeners exist for an event.
    */
    public static function hasListeners(string $event): bool
    {
        return !empty(static::$eventSynapses[static::resolveLinkedEventKey($event)]);
        // return !empty(static::$eventSynapses[static::resolveScopeKey($event)]);
    }

    /**
     * Count of listeners registered for an event.
    */
    public static function listenerCount(string $event): int
    {
        return count(static::$eventSynapses[static::resolveLinkedEventKey($event)] ?? []);
    }

    /**
     * 🧹 Remove a listener by ID (precise) or by exact callable reference.
     *
     * Accepts either a string/int id or the original callable; callables are
     * hashed via generateListenerId() so `off($e, $fn)` mirrors `on($e, $fn)`.
     * Scope-resolution matches the registration path exactly.
    */
    public static function off(string $event, string|int|callable $idOrCallable): void
    {
        $key = static::resolveEventKey($event);

        $resolvedId = is_callable($idOrCallable)
            ? static::generateListenerId($idOrCallable)
            : $idOrCallable;

        static::removeSynapse($key, $resolvedId);
    }

    /** 🧹 Alias for off() — Remove a listener by the id returned from on(). */
    public static function offById(string $event, string|int $id): void
    {
        static::off($event, $id);
    }

    /**
     * 🔪 Low-level synapse excision — operates on a RESOLVED key.
     *
     * Used by both `off()` (public contract) and the once-wrapper's internal
     * disposal path. Guarantees three invariants:
     *   1. $eventSynapses[$key] is array_values()-normalised after filter.
     *   2. Empty event buckets are unslashed from the registry.
     *   3. $onceListenerMap is synchronised (no ghost closures).
     *
     * @param  string     $resolvedKey  Internal "scope\0event" key.
     * @param  string|int $id           Target listener id.
    */
    protected static function removeSynapse(string $resolvedKey, string|int $id): void
    {
        // ── Event synapse sweep
        if (isset(static::$eventSynapses[$resolvedKey])) {
            static::$eventSynapses[$resolvedKey] = array_values(
                array_filter(
                    static::$eventSynapses[$resolvedKey],
                    static fn(array $synapse): bool => $synapse[1] !== $id,
                ),
            );

            if (static::$eventSynapses[$resolvedKey] === []) {
                unset(static::$eventSynapses[$resolvedKey]);
            }
        }

        // ── Once-map synchronisation
        if (isset(static::$onceListenerMap[$resolvedKey][$id])) {
            unset(static::$onceListenerMap[$resolvedKey][$id]);
            if (static::$onceListenerMap[$resolvedKey] === []) {
                unset(static::$onceListenerMap[$resolvedKey]);
            }
        }
    }

    /**
     * 🧹 Clear all listeners of an event — or the entire three-tier registry.
     *
     *   clear()                    → wipe events + injectors + pipes.
     *   clear('nexus.integrating') → wipe krubot:nexus.integrating.
     *   clear('admin:')            → wipe EVERY event in the `admin` scope.
     *   clear('admin:*')           → alias for the above.
    */
    public static function clear(?string $event = null): void
    {
        if ($event === null) {
            static::$eventSynapses       = [];
            static::$paramTypeInjectors  = [];
            static::$paramNameInjectors  = [];
            static::$onceListenerMap     = [];
            static::$pipes               = [];
            static::$oncePipeMap         = [];
            static::$synapsesDirty       = [];
            static::$pipesDirty          = [];
            static::$injectorsDirty      = [];

            // 🏛️ Judge registry — the same wipe.
            static::$conclaves           = [];
            static::$conclavesDirty      = [];

            // ← اضافه کن (اگر NeonWarp هم use شده):
            if (method_exists(static::class, 'clearLinks'))
                static::clearLinks();

            return;
        }

        $matcher = static::compileEventMatcher($event);

        // Exact Match Purge ⚡ O(1)
        if (is_string($matcher)) {
            unset(
                static::$eventSynapses[$matcher],
                static::$onceListenerMap[$matcher],
                static::$conclaves[$matcher],
                static::$conclavesDirty[$matcher],
                static::$synapsesDirty[$matcher]  // ← hygiene
            );
            static::clearPipes($matcher, true); // Delegate pipe clearing
            return;
        }

        // Wildcard/Regex Purge ($matcher is a Closure) 🔍 O(N)
        foreach (array_keys(static::$eventSynapses) as $k) {
            if ($matcher($k)) {
                unset(
                    static::$eventSynapses[$k],
                    static::$synapsesDirty[$k]
                );
            }
        }

        foreach (array_keys(static::$onceListenerMap) as $k) {
            if ($matcher($k)) {
                unset(static::$onceListenerMap[$k]);
            }
        }

        foreach (array_keys(static::$conclaves) as $k) {
            if ($matcher($k)) {
                unset(
                    static::$conclaves[$k],
                    static::$conclavesDirty[$k]
                );
            }
        }

        // Pipes share the same scope contract — purge in tandem.
        static::clearPipes($matcher, true);
    }

    // ─── Session Scope API ────────────────────────────────────────────────────────

    /** Plugins may call setSessionScope('admin') at request time. null = default_scope. */
    public static function setSessionScope(?string $scope = null): void
    {
        static::$sessionScope = $scope !== '' ? ($scope ?? static::DEFAULT_SCOPE) : static::DEFAULT_SCOPE;
    }

    public static function currentSessionScope(): string
    {
        return static::$sessionScope ?? static::DEFAULT_SCOPE;
    }

    /**
     * Every scope that has at least one registered event.
     *
     * @return list<string>
    */
    public static function activeScopes(): array
    {
        $scopes = [];

        foreach (array_keys(static::$eventSynapses) as $key) {
            [$scope] = explode(static::SCOPE_SEP, $key, 2);
            $scopes[$scope] = true;
        }

        return array_keys($scopes);
    }

    /**
     * ─── scopedExecute ─────
     * Executes the synaptic operation within the designated operational boundary.
     * 
     * If $scope is non-null and differs from the current session scope,
     * run $callback inside that scope; otherwise call directly.
    */
    public static function scopedExecute(?string $scope, callable $callback): mixed
    {
        if ($scope === null || $scope === static::currentSessionScope()) {
            return $callback();
        }

        return static::withinScope($scope, $callback);
    }

    /**
     * Temporarily sets the session scope, runs the callback, then restores the previous scope.
     *
     * @param  string    $scope
     * @param  callable  $callback
     * @return mixed
    */
    protected static function withinScope(string $scope, callable $callback): mixed
    {
        $previous = static::$sessionScope;

        try {
            static::setSessionScope($scope);
            return $callback();
        } finally {
            static::$sessionScope = $previous;
        }
    }

    // =====================================================================
    // 🧠 SCOPE NORMALIZATION — the heart of the upgrade
    // =====================================================================

    /**
     * Split "scope:event" → [scope, event].
     * - Missing ':'        → default scope.
     * - Empty prefix ":ev"  → default scope (defensive).
     * - Only the FIRST ':' splits; later colons stay in the event name.
     *
     * @return array{0:string, 1:string}
    */
    protected static function normalizeEventKey(string $key): array
    {
        if (!str_contains($key, ':')) {
            return [static::DEFAULT_SCOPE, $key];
        }
        [$scope, $event] = explode(':', $key, 2);
        return [$scope !== '' ? $scope : static::DEFAULT_SCOPE, $event];
    }

    /** Compose the flat internal key from already-split parts. */
    protected static function scopeKey(string $scope, string $event): string
    {
        return $scope . self::SCOPE_SEP . $event;
    }

    /** One-shot: public key → flat internal key (O(1) after explode). */
    protected static function resolveScopeKey(string $key): string
    {
        [$scope, $event] = static::normalizeEventKey($key);
        return $scope . self::SCOPE_SEP . $event;
    }

    /**
     * Build the internal storage key for an event.
     * Explicit "scope:event" is honoured; bare names get the session scope.
    */
    protected static function resolveEventKey(string $event): string
    {
        if (str_contains($event, ':')) {

            [$scope, $name] = explode(':', $event, 2);
            // Empty prefix (":event") → default scope; taught by normalizeEventKey.
            $scope = $scope !== '' ? $scope : static::DEFAULT_SCOPE;
            
            return $scope . static::SCOPE_SEP . $name;
        }

        return static::currentSessionScope() . static::SCOPE_SEP . $event;
    }

    /**
     * Build the internal storage key for an injector.
     * Explicit "scope:key" is honoured; bare keys get the session scope.
    */
    protected static function resolveInjectorKey(string $key): string
    {
        if (str_contains($key, ':')) {

            [$scope, $name] = explode(':', $key, 2);
            // Same empty-prefix guard as resolveEventKey / normalizeEventKey.
            $scope = $scope !== '' ? $scope : static::DEFAULT_SCOPE;

            return $scope . static::SCOPE_SEP . $name;

        }

        return static::currentSessionScope() . static::SCOPE_SEP . $key;
    }

    /**
     * 🔗 Junction-aware event key resolver.
     *
     * وقتی SynapticJunctions وصل باشه، soft-links قبل از هر registry-lookup
     * حل میشن. Hard-links شفافاند (reference-bound storage) و شیمی نمیخوان.
     *
     * Zero-overhead path: یک method_exists() که opcache در اولین call کش میکنه.
    */
    protected static function resolveLinkedEventKey(string $event): string
    {
        $key = static::resolveEventKey($event);

        // 🔥 Ultra-fast: only when SynapticJunctions is composed on this class.
        if (method_exists(static::class, 'resolveFinalTarget')) {
            return static::resolveFinalTarget($key);
        }

        return $key;
    }

    /**
     * ✨ Pretty-print an internal flat key back to its human form.
     *
     * Converts "scope\0event" → "scope:event". Keys without a null-byte
     * separator (defensive path) are returned verbatim, so this method is
     * safe to feed even an already-pretty key without double-mangling.
     *
     * Used by snapshot(), registrySnapshot(), and any introspection surface
     * that needs to expose readable labels without exposing the internal
     * null-byte separator to consumers.
     *
     * @param  string $flatKey  Internal "scope\0event" key.
     * @return string           Readable "scope:event" label.
    */
    protected static function prettyKey(string $flatKey): string
    {
        $sep = strpos($flatKey, static::SCOPE_SEP);

        return $sep === false
            ? $flatKey
            : substr($flatKey, 0, $sep) . ':' . substr($flatKey, $sep + 1);
    }

    /**
     * 🌍 Public target resolver (Returns the developer-friendly parsed key)
     * 🗺️ Decode the map for the Architect.
    */
    public static function resolveTarget(string $event): string
    {
        $key   = static::resolveEventKey($event);

        $final = method_exists(static::class, 'resolveFinalTarget')
        ?
            static::resolveFinalTarget($key)
        :
            $key;
        
        return static::prettyKey($final);
    }

    // =====================================================================
    // 🪄 SCOPE-AWARE HELPERS (DX sugar)
    // =====================================================================

    /**
     * Fire every listener registered in a given scope, regardless of event.
     * Results are keyed by the internal "scope\0event" key.
     * Great for teardown / broadcast / audit — NOT for hot paths.
     *
     * @return array<string, array<int, mixed>>
    */
    public static function fireScope(string $scope, mixed ...$payload): array
    {
        // 🚦 Engine dark ⇒ scope broadcast is indistinguishable from empty.
        if (!static::synapsesOn()) {
            return [];
        }

        static::ensureSynapsesSorted();

        $prefix  = $scope . self::SCOPE_SEP;
        $results = [];

        foreach (static::$eventSynapses as $key => $listeners) {
            if (!str_starts_with($key, $prefix)) continue;
            foreach ($listeners as [, , $listener]) {
                $results[$key][] = $listener(...$payload);
            }
        }
        return $results;
    }

    /**
     * List all registered event keys a given scope or the current scope.
     *
     * @return list<string>  Bare event names, without the scope prefix.
    */
    public static function eventsInScope(?string $scope = null): array
    {
        $scope  = $scope ?? static::currentSessionScope();
        $prefix = $scope . static::SCOPE_SEP;
        $len    = strlen($prefix);
        $events = [];

        foreach (array_keys(static::$eventSynapses) as $key) {
            if (str_starts_with($key, $prefix)) {
                $events[] = substr($key, $len);
            }
        }

        return $events;
    }

    // =========================================================================
    // Getter / traversal / iterator API
    // =========================================================================

    /**
     * Return all registered listeners for a specific event — or, when $event is
     * omitted, a "scope:event" keyed map of the WHOLE registry (xRC.10 behavior).
     *
     * Per-event entries are: ['priority' => int, 'id' => string, 'listener' => callable]
     *
     * @param  string|null $event  Event name (scope prefix honoured).
     * @return list<array{priority:int, id:string, listener:callable}>
     *          | array<string, list<array{id:string|int, priority:int}>>
    */
    public static function allEvents(?string $event = null): array
    {
        if ($event !== null) {
            static::ensureSynapsesSorted();

            $key = static::resolveEventKey($event);
            $out = [];

            foreach (static::$eventSynapses[$key] ?? [] as [$priority, $id, $listener]) {
                $out[] = ['priority' => $priority, 'id' => $id, 'listener' => $listener];
            }

            return $out;
        }

        // xRC.9 behavior: full-registry map keyed "scope:event".
        $out = [];

        foreach (static::$eventSynapses as $key => $entries) {
            [$scope, $name] = explode(static::SCOPE_SEP, $key, 2);
            $out["{$scope}:{$name}"] = array_map(
                static fn(array $e): array => ['id' => $e[1], 'priority' => $e[0]],
                $entries,
            );
        }

        return $out;
    }

    /**
     * All listener entries for a given event.
     *
     * Each item: ['id' => string|int, 'priority' => int, 'listener' => callable]
     *
     * @return list<array{id:string|int, priority:int, listener:callable}>
    */
    public static function listenersFor(string $event): array
    {
        static::ensureSynapsesSorted();

        $key     = static::resolveEventKey($event);
        $entries = static::$eventSynapses[$key] ?? [];

        return array_map(
            static fn(array $e): array => [
                'id'       => $e[1],
                'priority' => $e[0],
                'listener' => $e[2],
            ],
            $entries,
        );
    }

    /**
     * Return all type-injectors visible in the current (or given) scope.
     *
     * Each entry: ['key' => string, 'priority' => int, 'resolver' => callable]
     *
     * @param  string|null $scope  Explicit scope; session/default used when null.
     * @return list<array{key:string, priority:int, resolver:callable}>
    */
    public static function allTypeInjectors(?string $scope = null): array
    {
        $scope  = $scope ?? static::$sessionScope ?? static::DEFAULT_SCOPE;
        $prefix = $scope . static::SCOPE_SEP;
        $out    = [];

        foreach (static::$paramTypeInjectors as $k => $entries) {
            if (!str_starts_with($k, $prefix)) {
                continue;
            }
            $typeKey = substr($k, strlen($prefix));
            foreach ($entries as [$priority, $resolver]) {
                $out[] = ['key' => $typeKey, 'priority' => $priority, 'resolver' => $resolver];
            }
        }

        return $out;
    }

    /**
     * Return all name-injectors visible in the current (or given) scope.
     *
     * Each entry: ['key' => string, 'priority' => int, 'resolver' => callable]
     *
     * @param  string|null $scope  Explicit scope; session/default used when null.
     * @return list<array{key:string, priority:int, resolver:callable}>
    */
    public static function allNameInjectors(?string $scope = null): array
    {
        $scope  = $scope ?? static::$sessionScope ?? static::DEFAULT_SCOPE;
        $prefix = $scope . static::SCOPE_SEP;
        $out    = [];

        foreach (static::$paramNameInjectors as $k => $entries) {
            if (!str_starts_with($k, $prefix)) {
                continue;
            }
            $nameKey = substr($k, strlen($prefix));
            foreach ($entries as [$priority, $resolver]) {
                $out[] = ['key' => $nameKey, 'priority' => $priority, 'resolver' => $resolver];
            }
        }

        return $out;
    }

    /**
     * Count listeners registered for an event.
     *
     * @param string $event Event name.
    */
    public static function eventCount(string $event): int
    {
        $key = static::resolveEventKey($event);
        return count(static::$eventSynapses[$key] ?? []);
    }

    /**
     * Return every distinct scope that has at least one event registered.
     *
     * @return list<string>
    */
    public static function registeredScopes(): array
    {
        $scopes = [];

        foreach (array_keys(static::$eventSynapses) as $key) {
            $sep = strpos($key, static::SCOPE_SEP);
            if ($sep !== false) {
                $scopes[substr($key, 0, $sep)] = true;
            }
        }

        return array_keys($scopes);
    }

    /**
     * Lazy generator — iterate over listeners for an event without building a
     * full array copy.  Yields associative arrays identical to allEvents($event).
     *
     * Usage:
     *   foreach (MyClass::iterateListeners('bot.process') as $entry) { ... }
     *
     * @param  string $event
     * @return \Generator<int, array{priority:int, id:string, listener:callable}>
    */
    public static function iterateListeners(string $event): Generator
    {
        static::ensureSynapsesSorted();

        $key = static::resolveEventKey($event);

        foreach (static::$eventSynapses[$key] ?? [] as [$priority, $id, $listener]) {
            yield ['priority' => $priority, 'id' => $id, 'listener' => $listener];
        }
    }

    /**
     * Iterate over every event in a scope, yielding event => listeners —
     * zero overhead for scopes with many events.
     *
     * @return \Generator<string, list<array{id:string|int, priority:int, listener:callable}>>
    */
    public static function traverseScope(?string $scope = null): Generator
    {
        static::ensureSynapsesSorted();

        $scope  = $scope ?? static::currentSessionScope();
        $prefix = $scope . static::SCOPE_SEP;
        $len    = strlen($prefix);

        foreach (static::$eventSynapses as $key => $entries) {
            if (!str_starts_with($key, $prefix)) {
                continue;
            }

            $event = substr($key, $len);
            yield $event => array_map(
                static fn(array $e): array => [
                    'id'       => $e[1],
                    'priority' => $e[0],
                    'listener' => $e[2],
                ],
                $entries,
            );
        }
    }

    /**
     * Snapshot of the full event registry — useful for debugging & serialization.
     *
     * Returns: ['scope' => ['event' => [['id'=>…, 'priority'=>…], …]]]
     *
     * @return array<string, array<string, list<array{id:string|int, priority:int}>>>
    */
    public static function registrySnapshot(): array
    {
        $out = [];

        foreach (static::$eventSynapses as $key => $entries) {
            [$scope, $event] = explode(static::SCOPE_SEP, $key, 2);

            foreach ($entries as [$priority, $id]) {
                $out[$scope][$event][] = ['id' => $id, 'priority' => $priority];
            }
        }

        return $out;
    }

    // =====================================================================
    // 🔌 INJECTOR API (scope-aware — mirrors the event synapse contract)
    // =====================================================================
    // Same rules as events:
    //   • "UniversalIdentity"        → krubot scope (default)
    //   • "admin:UniversalIdentity"  → admin scope
    //   • Only the FIRST ':' splits; later colons stay in the key.
    //   • Internal key = "{$scope}\0{$key}" — flat map, O(1) lookup.
    // =====================================================================

    /**
     * Register a resolver keyed by a parameter's TYPE (class/interface).
     * Signature: fn(ReflectionParameter $p, array $payload, Krubot $bot): mixed
     * Return null to yield to the next resolver at the same key.
     *
     * Scope-aware: pass "admin:SomeType" to isolate the resolver to the admin scope.
     * 
     * @param  string   $typeKey   "FQCN" or "scope:FQCN"
     * @param  callable $resolver  fn(ReflectionParameter, array $payload, object $bot, ...): mixed
     * @param  int      $priority
    */
    public static function injectParamType(
        string $typeKey,
        callable $resolver,
        int $priority = self::PRIORITY_NORMAL
    ): void {
        $key = static::resolveInjectorKey($typeKey);
        static::$paramTypeInjectors[$key][] = [$priority, $resolver];
        // Per-key flag
        static::$injectorsDirty[$key] = true;
    }

    /**
     * Register a resolver keyed by a parameter's NAME (e.g. 'msg', 'user', 'ctx').
     * Scope-aware: pass "admin:user" to isolate the resolver to the admin scope.
    */
    public static function injectParamName(
        string $nameKey,
        callable $resolver,
        int $priority = self::PRIORITY_NORMAL
    ): void {
        $key = static::resolveInjectorKey($nameKey);
        static::$paramNameInjectors[$key][] = [$priority, $resolver];
        // Per-key flag
        static::$injectorsDirty[$key] = true;
    }

    /**
     * Unregister all resolvers for a type.
     *   forgetParamType('UniversalIdentity')        → krubot scope only.
     *   forgetParamType('admin:UniversalIdentity')  → admin scope only.
     *   forgetParamType('admin:')                   → every type in admin.
     *   forgetParamType('admin:*')                  → same as above.
    */
    public static function forgetParamType(string $typeKey): void
    {
        // Raw public key: purgeInjectorKey must still see "admin:" / "admin:*".
        static::purgeInjectorKey(static::$paramTypeInjectors, $typeKey); /// static::resolveInjectorKey
    }

    /** Same contract for name injectors. */
    public static function forgetParamName(string $nameKey): void
    {
        // Raw public key: purgeInjectorKey must still see "admin:" / "admin:*".
        static::purgeInjectorKey(static::$paramNameInjectors, $nameKey); /// static::resolveInjectorKey
    }

    /**
     * Shared purge helper — handles exact key OR scope-wide wipe.
     *
     * @param array<string, array> $registry (by reference)
    */
    protected static function purgeInjectorKey(array &$registry, string $publicKey): void
    {
        if (isset($registry[$publicKey])) {
            unset($registry[$publicKey]);
            unset(static::$injectorsDirty[$publicKey]);
            return;
        }

        // Scope-wide purge: key ends with SCOPE_SEP (yields eg: "admin\0")
        $prefix = rtrim($publicKey, static::SCOPE_SEP) . static::SCOPE_SEP;
        foreach (array_keys($registry) as $k) {
            if (str_starts_with($k, $prefix)) {
                unset($registry[$k], static::$injectorsDirty[$k]);
            }
        }

        return;

        // Scope-wide wipe — "admin:" or "admin:*"
        if (str_ends_with($publicKey, ':') || str_ends_with($publicKey, ':*')) {
            $scope  = rtrim(rtrim($publicKey, '*'), ':');
            $prefix = $scope . self::SCOPE_SEP;
            foreach ($registry as $k => $_) {
                if (str_starts_with($k, $prefix)) unset($registry[$k]);
            }
            return;
        }

        unset($registry[static::resolveScopeKey($publicKey)]);
    }

    // =====================================================================
    // 🔎 INJECTOR INTROSPECTION (mirrors eventsInScope)
    // =====================================================================

    /** List every type key registered under a scope. */
    public static function typesInScope(string $scope): array
    {
        return static::injectorKeysInScope(static::$paramTypeInjectors, $scope);
    }

    /** List every name key registered under a scope. */
    public static function namesInScope(string $scope): array
    {
        return static::injectorKeysInScope(static::$paramNameInjectors, $scope);
    }

    /** @deprecated Shared scope-filter walker for introspection. */
    protected static function keysInScope(array $registry, string $scope): array
    {
        $prefix = $scope . self::SCOPE_SEP;
        $out    = [];
        foreach ($registry as $key => $_) {
            if (str_starts_with($key, $prefix)) {
                $out[] = substr($key, strlen($prefix));
            }
        }
        return $out;
    }

    /** @internal Shared scope-filter walker for introspection. */
    protected static function injectorKeysInScope(array $registry, ?string $scope): array
    {
        $scope  = $scope ?? static::currentSessionScope();
        $prefix = $scope . static::SCOPE_SEP;
        $len    = strlen($prefix);
        $keys   = [];

        foreach (array_keys($registry) as $k) {
            if (str_starts_with($k, $prefix)) {
                $keys[] = substr($k, $len);
            }
        }

        return $keys;
    }    
    
    /**
     * Cascade list for a public key: session scope first, then default scope.
     * If $publicKey already contains ':', honor it as an explicit "scope:key".
     * Returns the ordered list of internal flat keys to probe.
     *
     * @deprecated this is the bkp-compatible wrapper; now the engine itself probes via injectorKeyCandidates() (see injectParam).
     *
     * @param array<string, list<array>> $registry
     * @return string[]
    */
    protected static function injectorScopesFor(array $registry, string $publicKey): array
    {
        // Explicit "scope:key" — trust the caller.
        if (str_contains($publicKey, ':')) {
            $k = static::resolveInjectorKey($publicKey);
            return isset($registry[$k]) ? [$k] : [];
        }


        $keys = [];
        foreach (static::injectorKeyCandidates(static::currentSessionScope(), $publicKey) as $k) {
            if (isset($registry[$k])) {
                $keys[] = $k;
            }
        }
        return $keys;


        // Otherwise: [session] → [default] cascade.
        $session = static::currentSessionScope();
        $scopes  = $session === static::DEFAULT_SCOPE
            ? [static::DEFAULT_SCOPE]
            : [$session, static::DEFAULT_SCOPE];

        $keys = [];
        foreach ($scopes as $scope) {
            $k = $scope . self::SCOPE_SEP . $publicKey;
            if (isset($registry[$k])) $keys[] = $k;
        }
        return $keys;
    }

    /**
     * Ordered list of registry keys to probe for a given lookup name.
     * Probes: [session scope] → [default scope]  (deduped)
     *
     * @return list<string>
    */
    protected static function injectorKeyCandidates(string $scope, string $name): array
    {
        $keys   = [];
        $keys[] = $scope . static::SCOPE_SEP . $name;

        $default = static::DEFAULT_SCOPE . static::SCOPE_SEP . $name;
        if ($default !== $keys[0]) {
            $keys[] = $default;
        }

        return $keys;
    }

    /**
     * 🔌 ONE-SHOT PLUGIN DI TIER — the whole P2.5 + P2.6 block in one call.
     *
     * Resolution order (both competitive, first non-null wins):
     *   1. TYPE injectors — keyed by the parameter's class/interface.
     *   2. NAME injectors — keyed by the parameter's name ('user', 'ctx', ...).
     *
     * Scope cascade for every key:  [session scope] → [default scope].
     *
     * Contract (mirrors the resolver itself):
     *   • null   → no resolver claimed the parameter. Caller continues.
     *   • mixed  → first non-null result. Caller uses it.
     *
     * Zero overhead when no injectors exist (single empty() check).
     * Fires `injector.resolved` internally on success.
     * 
     * Attempt to inject a name injectors are the fallback.
     *
     * @return mixed  Resolved value, or null if unclaimed.
    */
    public static function injectParam(
        ReflectionParameter $parameter,
        array $payloadData,
        Krubot $warlord,
        ReflectionMethod|ReflectionFunction $method
    ): mixed {

        // 🚦 Engine dark ⇒ "no resolver claimed this parameter".
        // Caller falls through to its own default resolution path.
        if (!static::synapsesOn()) {
            return null;
        }

        // ── FAST-PATH: nothing registered anywhere → zero-cost bail.
        if (empty(static::$paramTypeInjectors) && empty(static::$paramNameInjectors)) {
            return null;
        }

        static::ensureSynapsesSorted(); // one pass, dirty-flag guarded
        $scope = static::currentSessionScope();

        // ── Phase A-: TYPE injectors (class/interface-keyed).
        $type = $parameter->getType();
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            $typeName = $type->getName();

            if ($typeName !== '') {
                foreach (static::injectorKeyCandidates($scope, $typeName) as $key) { /// static::injectorScopesFor(static::$paramTypeInjectors, $typeName) as $key
                    foreach (static::$paramTypeInjectors[$key] ?? [] as [, $resolver]) {
                        $result = $resolver($parameter, $payloadData, $warlord, $method);
                        if ($result !== null) {
                            static::fire('injector.resolved', $parameter, $typeName, $scope);
                            return $result;
                        }
                    }
                }
            }
        }

        // ── Phase B-: NAME injectors (parameter-name-keyed).
        $name = $parameter->getName();

        if ($name !== '') {
            foreach (static::injectorKeyCandidates($scope, $name) as $key) { /// static::injectorScopesFor(static::$paramNameInjectors, $name) as $key
                foreach (static::$paramNameInjectors[$key] ?? [] as [, $resolver]) {
                    $result = $resolver($parameter, $payloadData, $warlord, $method);
                    if ($result !== null) {
                        static::fire('injector.resolved', $parameter, null, $scope);
                        return $result;
                    }
                }
            }
        }

        return null;
    }

    // =====================================================================
    // 🧬 TRANSFORMER SYNAPSES API
    // =====================================================================
    // Contract:
    //   • Pipe signature → fn(mixed $value, mixed ...$context): mixed
    //   • Non-null return       → REPLACES the carried value.
    //   • Null return / void    → graceful PASSTHROUGH (value unchanged).
    //   • Priority order        → LOWER fires FIRST (WordPress-style ladder).
    //   • Scope-aware           → "admin:content.render" honoured verbatim.
    //   • Session cascade       → bare names resolve against current scope.
    // =====================================================================

    /**
     * 🧬 Register a transformer on a value-pipeline event.
     *
     * This is the canonical name; `addPipe()` is a WordPress-style
     * alias for the same operation. See addPipe() for the full DX
     * contract and usage examples.
     *
     * @param  string          $event       Event name (scope prefix honoured)
     * @param  callable        $transformer fn($value, ...$ctx): mixed
     * @param  int             $priority    Lower = earlier in the pipe
     * @param  string|int|null $id          Optional stable id
     * @return string|int      The resolved transformer id.
    */
    public static function onPipe(
        string $event,
        callable $transformer,
        int $priority = self::PRIORITY_NORMAL,
        string|int|null $id = null
    ): string|int {

        // ── NeonWarp virtual-event guard
        if (method_exists(static::class, 'isVirtual') && static::isVirtual($event)) {
            throw new \LogicException(
                "Cannot attach a transformer directly to virtual event '{$event}'. Use a junction."
            );
        }

        $id ??= static::generateListenerId($transformer);

        $key = static::resolveEventKey($event);
        static::$pipes[$key][] = [$priority, $id, $transformer];
        static::$pipesDirty[$key] = true;   // ← per-key, not global

        return $id;
    }

    /** 🧬 Sugar: transformer that runs before the normal priority band. */
    public static function onPipeBefore(
        string $event,
        callable $transformer,
        string|int|null $id = null
    ): string|int {
        return static::onPipe($event, $transformer, self::PRIORITY_BEFORE, $id);
    }

    /** 🧬 Sugar: transformer that runs after the normal priority band. */
    public static function onPipeAfter(
        string $event,
        callable $transformer,
        string|int|null $id = null
    ): string|int {
        return static::onPipe($event, $transformer, self::PRIORITY_AFTER, $id);
    }
    
    /** ⚡ Hyper-DX value-flow aliases, semantic and cyberpunk. */
    public static function reshape(
        string $event,
        callable $reshaper,
        int $priority = self::PRIORITY_NORMAL,
        string|int|null $id = null
    ): string|int {
        return static::onPipe($event, $reshaper, $priority, $id);
    }
    
    /** Cyber-Tech alias of onPipe(). */
    public static function phase(
        string $event,
        callable $phaser,
        int $priority = self::PRIORITY_NORMAL,
        string|int|null $id = null
    ): string|int {
        return static::onPipe($event, $phaser, $priority, $id);
    }

    /** Register a pre-flow transformer using the reshape vocabulary. */
    public static function reshapeBefore(
        string $event,
        callable $reshaper,
        string|int|null $id = null
    ): string|int {
        return static::onPipeBefore($event, $reshaper, $id);
    }

    /** Register a post-flow transformer using the reshape vocabulary. */
    public static function reshapeAfter(
        string $event,
        callable $reshaper,
        string|int|null $id = null
    ): string|int {
        return static::onPipeAfter($event, $reshaper, $id);
    }

    /** Register a pre-flow transformer using the phase vocabulary. */
    public static function phaseBefore(
        string $event,
        callable $phaser,
        string|int|null $id = null
    ): string|int {
        return static::onPipeBefore($event, $phaser, $id);
    }

    /** Register a post-flow transformer using the phase vocabulary. */
    public static function phaseAfter(
        string $event,
        callable $phaser,
        string|int|null $id = null
    ): string|int {
        return static::onPipeAfter($event, $phaser, $id);
    }

    /**
     * 🧬 THE PIPELINE — push a value through every registered transformer.
     *
     * Zero-cost fast path when the registry is empty. Sorting is lazy and
     * shared with the event layer via ensureSynapsesSorted().
     *
     * Example:
     *   $html = JackPoint::transform('content.render', $html, $post, $bot);
     *
     * @param  string $event     Pipeline name (scope prefix honoured)
     * @param  mixed  $value     The seed value carried through the pipe
     * @param  mixed  ...$context Extra args handed to every transformer
     * @return mixed  The mutated value, or the original if nothing claimed it.
    */
    public static function transform(string $event, mixed $value, mixed ...$context): mixed
    {
        // 🚦 Engine dark ⇒ seed value flows through untouched.
        // Semantically identical to "no pipes registered" — the caller
        // receives `$value` verbatim and cannot observe the engine state.
        if (!static::synapsesOn()) {
            return $value;
        }

        static::ensureSynapsesSorted();

        $key = static::resolveLinkedEventKey($event);

        if (empty(static::$pipes[$key])) {
            return $value;                     // ← Zero-overhead passthrough
        }

        foreach (static::$pipes[$key] as [, , $transformer]) {
            $result = $transformer($value, ...$context);

            // null = graceful passthrough; anything else replaces the value.
            if ($result !== null) {
                $value = $result;
            }
        }

        return $value;
    }

    /**
     * 🧬 ONE-SHOT TRANSFORMER — self-disarming after the first pipe pass.
    */
    public static function oncePipe(
        string $event,
        callable $transformer,
        int $priority = self::PRIORITY_NORMAL,
        string|int|null $id = null
    ): string|int {

        $id ??= static::generateListenerId($transformer);
        $resolvedKey = static::resolveEventKey($event);

        // ── Duplicate guard.
        if (isset(static::$oncePipeMap[$resolvedKey][$id])) {
            static::removePipe($resolvedKey, $id);
        }

        $onceWrapper = static function (mixed $value, mixed ...$ctx) use ($resolvedKey, $transformer, $id) {
            try {
                return $transformer($value, ...$ctx);
            } finally {
                static::removePipe($resolvedKey, $id);
            }
        };

        static::$oncePipeMap[$resolvedKey][$id] = [
            'wrapper'  => $onceWrapper,
            'original' => $transformer,
        ];

        return static::onPipe($event, $onceWrapper, $priority, $id);
    }

    /** 🧬 Sugar: one-shot before the normal band. */
    public static function oncePipeBefore(
        string $event,
        callable $transformer,
        string|int|null $id = null
    ): string|int {
        return static::oncePipe($event, $transformer, self::PRIORITY_BEFORE, $id);
    }

    /** 🧬 Sugar: one-shot after the normal band. */
    public static function oncePipeAfter(
        string $event,
        callable $transformer,
        string|int|null $id = null
    ): string|int {
        return static::oncePipe($event, $transformer, self::PRIORITY_AFTER, $id);
    }

    /**
     * 🧬 Remove a transformer by ID or by exact callable reference.
    */
    public static function offPipe(string $event, string|int|callable $idOrCallable): void
    {
        $key = static::resolveEventKey($event);

        $resolvedId = is_callable($idOrCallable)
            ? static::generateListenerId($idOrCallable)
            : $idOrCallable;

        static::removePipe($key, $resolvedId);
    }

    /**
     * 🧬 Low-level transformer excision — mirrors removeSynapse().
     *
     * @param  string     $resolvedKey  Internal "scope\0event" key.
     * @param  string|int $id           Target transformer id.
    */
    protected static function removePipe(string $resolvedKey, string|int $id): void
    {
        if (isset(static::$pipes[$resolvedKey])) {
            static::$pipes[$resolvedKey] = array_values(
                array_filter(
                    static::$pipes[$resolvedKey],
                    static fn(array $t): bool => $t[1] !== $id,
                ),
            );

            if (static::$pipes[$resolvedKey] === []) {
                unset(static::$pipes[$resolvedKey]);
            }
        }

        if (isset(static::$oncePipeMap[$resolvedKey][$id])) {
            unset(static::$oncePipeMap[$resolvedKey][$id]);
            if (static::$oncePipeMap[$resolvedKey] === []) {
                unset(static::$oncePipeMap[$resolvedKey]);
            }
        }
    }

    /** 🧬 Does this pipeline have at least one transformer registered? */
    public static function hasPipes(string $event): bool
    {
        return !empty(static::$pipes[static::resolveLinkedEventKey($event)]);
    }

    /** 🧬 Count of pipes registered on a pipeline. */
    public static function pipesCount(string $event): int
    {
        return count(static::$pipes[static::resolveLinkedEventKey($event)] ?? []);
    }

    /**
     * 🧬 Introspect a pipeline — ordered entries for debugging & serialization.
     *
     * @return list<array{id:string|int, priority:int, transformer:callable}>
    */
    public static function pipesFor(string $event): array
    {
        static::ensureSynapsesSorted();

        $key     = static::resolveLinkedEventKey($event);
        $entries = static::$pipes[$key] ?? [];

        return array_map(
            static fn(array $t): array => [
                'id'          => $t[1],
                'priority'    => $t[0],
                'transformer' => $t[2],
            ],
            $entries,
        );
    }

    /**
     * 🧬 Return every registered transformer, grouped by "scope:event".
     *
     * Mirrors allTypeInjectors() / allNameInjectors() so that tooling can
     * treat listeners, injectors, and pipes with one uniform shape.
     *
     * @return array<string, list<array{id:string|int, priority:int, transformer:callable}>>
    */
    public static function allPipes(): array
    {
        $result = [];

        /*
        foreach (array_keys(static::$pipes) as $key) {
            // Rebuild the human-readable "scope:event" label from the flat key.
            [$scope, $name] = explode(static::SCOPE_SEP, $key, 2) + [1 => ''];
            $readableKey    = "{$scope}:{$name}";
            // Feed the READABLE key back through pipesFor() so its
            // own resolveEventKey() round-trips cleanly (no double prefix).
            $result[$readableKey] = static::pipesFor($readableKey);
        }
        */

        // fix🔧: pipesFor() inside a loop caused O(n) key-resolution + sort checks per entry;
        // Read the sorted registry directly 🔧 instead.
        foreach (array_keys(static::$eventPipes) as $key) {
            // trigger lazy sort once per key, not once per outer call
            if (!empty(static::$pipesDirty[$key])) {
                usort(static::$eventPipes[$key], static::synapseComparator());
                unset(static::$pipesDirty[$key]);
            }
            foreach (static::$eventPipes[$key] as $pipe) {
                $result[] = $pipe;
            }
        }

        return $result;
    }

    /**
     * 🧬 List every event name that has at least one transformer in a scope.
     *
     * Analogous to eventsInScope() / typesInScope() / namesInScope() — the
     * fourth pillar of the uniform introspection API.
     *
     * @param  string|null $scope  Explicit scope; session/default when null.
     * @return list<string>        Bare event names (scope prefix stripped).
    */
    public static function pipesInScope(?string $scope = null): array
    {
        $scope  = $scope ?? static::currentSessionScope();
        $prefix = $scope . static::SCOPE_SEP;
        $len    = strlen($prefix);
        $events = [];

        foreach (array_keys(static::$pipes) as $key) {
            if (str_starts_with($key, $prefix)) {
                $events[] = substr($key, $len);
            }
        }

        return $events;
    }

    /**
     * 🧹 Clear pipes — mirrors clear() semantics.
     *
     *   clearPipes()                   → wipe the whole pipeline registry.
     *   clearPipes('content.render')   → wipe krubot:content.render.
     *   clearPipes(':content.render')  → wipe content.render in all scopes.
     *   clearPipes('*:content.render') → wipe content.render in all scopes.
     *   clearPipes('admin:')           → wipe EVERY pipeline in `admin` scope.
     *   clearPipes('admin:*')          → alias for the above.
     *   clearPipes('adm?:render')      → use SQL-style LIKE matching (adm%.render).
     *   clearPipes('admin:?render')    → use SQL-style LIKE matching (admin:%render).
    */
    public static function clearPipes(string|Closure|null $event = null, bool $eventIsMatcher = false): void
    {
        if ($event === null) {
            static::$pipes        = [];
            static::$oncePipeMap  = [];
            static::$pipesDirty   = [];
            return;
        }

        // Prevent recompile the matcher
        $matcher = $eventIsMatcher ? $event : static::compileEventMatcher($event);

        // Exact Match Purge ⚡ O(1)
        if (is_string($matcher)) {
            unset(
                static::$pipes[$matcher],
                static::$oncePipeMap[$matcher],
                static::$pipesDirty[$matcher]
            );
            return;
        }

        // Wildcard/Regex Purge ($matcher is a Closure) 🔍 O(N)
        foreach (array_keys(static::$pipes) as $k) {
            // پاک‌سازی `$pipes` و `$pipesDirty` بر اساس کلیدهای پایپ‌لاین اصلی
            if ($matcher($k)) {
                unset(
                    static::$pipes[$k],
                    static::$pipesDirty[$k]
                );
            }
        }

        foreach (array_keys(static::$oncePipeMap) as $k) {
            if ($matcher($k)) {
                unset(static::$oncePipeMap[$k]);
            }
        }

    }

    // =====================================================================
    // 🧬 WORDPRESS-STYLE ALIASES — identical behavior, familiar ergonomics
    // =====================================================================

    // =====================================================================
    // 🎬 ACTION API — WordPress-style aliases on top of event synapses
    // =====================================================================

    /**
     * 🎬 WP add_action().
     *
     * Alias of on(). Registers an action listener.
    */
    public static function addAction(
        string $event,
        callable $listener,
        int $priority = self::PRIORITY_NORMAL,
        string|int|null $id = null
    ): string|int {
        return static::on($event, $listener, $priority, $id);
    }

    /** 🎬 WP add_action() before normal band. */
    public static function addActionBefore(
        string $event,
        callable $listener,
        string|int|null $id = null
    ): string|int {
        return static::onBefore($event, $listener, $id);
    }

    /** 🎬 WP add_action() after normal band. */
    public static function addActionAfter(
        string $event,
        callable $listener,
        string|int|null $id = null
    ): string|int {
        return static::onAfter($event, $listener, $id);
    }

    /** 🎬 WP once action. */
    public static function onceAction(
        string $event,
        callable $listener,
        int $priority = self::PRIORITY_NORMAL,
        string|int|null $id = null
    ): string|int {
        return static::once($event, $listener, $priority, $id);
    }

    /** 🎬 WP once action before normal band. */
    public static function onceActionBefore(
        string $event,
        callable $listener,
        string|int|null $id = null
    ): string|int {
        return static::onceBefore($event, $listener, $id);
    }

    /** 🎬 WP once action after normal band. */
    public static function onceActionAfter(
        string $event,
        callable $listener,
        string|int|null $id = null
    ): string|int {
        return static::onceAfter($event, $listener, $id);
    }

    /**
     * 🎬 WP do_action().
     *
     * Executes ALL listeners for the action, ignoring return values.
     * WordPress actions are fire-and-forget; this uses fireAll() so
     * every listener runs (no short-circuit).
    */
    public static function doAction(string $event, mixed ...$payload): void
    {
        static::fireAll($event, ...$payload);
    }

    /**
     * 🎬 WP remove_action().
     *
     * Removes a listener by id or exact callable.
     * $priority is accepted for signature compatibility only; this engine
     * removes by id/callable regardless of priority.
    */
    public static function removeAction(
        string $event,
        string|int|callable $idOrCallable,
        int $priority = self::PRIORITY_NORMAL
    ): void {
        static::off($event, $idOrCallable);
    }

    /** 🎬 WP remove_action() by explicit id. */
    public static function removeActionById(string $event, string|int $id): void
    {
        static::offById($event, $id);
    }

    /**
     * 🎬 WP has_action().
     *
     * If $idOrCallable is provided, checks if that specific listener is
     * registered. Otherwise checks if any listener exists.
    */
    public static function hasAction(
        string $event,
        string|int|callable|false $idOrCallable = false
    ): bool {
        if ($idOrCallable === false) {
            return static::hasListeners($event);
        }

        $resolvedId = is_callable($idOrCallable)
            ? static::generateListenerId($idOrCallable)
            : $idOrCallable;

        foreach (static::listenersFor($event) as $entry) {
            if ($entry['id'] === $resolvedId) {
                return true;
            }
        }

        return false;
    }

    /**
     * 🎬 WP remove_all_actions().
     *
     * Clears all listeners for an action, or the entire registry if
     * $event is null. Delegates to clear(), which also purges pipes
     * and once-maps for the same key.
    */
    public static function removeAllActions(?string $event = null): void
    {
        static::clear($event);
    }

    /** 🎬 Alias of removeAllActions(). */
    public static function clearActions(?string $event = null): void
    {
        static::clear($event);
    }

    /** 🎬 Count of listeners for an action. */
    public static function actionCount(string $event): int
    {
        return static::listenerCount($event);
    }

    /** 🎬 List all actions in a scope. */
    public static function actionsInScope(?string $scope = null): array
    {
        return static::eventsInScope($scope);
    }

    /** 🎬 Full action registry snapshot. */
    public static function allActions(?string $event = null): array
    {
        return static::allEvents($event);
    }

    // =====================================================================
    // 🎬 FILTERS API — WordPress-style aliases on top of pipe transformers
    // =====================================================================

    /**
     * 🧬 WP-style alias of onPipe().
     *
     * ── DX contract ──────────────────────────────────────────────────────
     *   JackPoint::addFilter('content.publish', function (mixed $value, mixed ...$ctx): mixed {
     *       return strtoupper((string) $value);
     *   });
     *
     *   $out = JackPoint::applyFilters('content.publish', $raw, $post);
     * ─────────────────────────────────────────────────────────────────────
    */
    public static function addFilter(
        string $event,
        callable $transformer,
        int $priority = self::PRIORITY_NORMAL,
        string|int|null $id = null
    ): string|int {
        return static::onPipe($event, $transformer, $priority, $id);
    }

    /** 🧬 WP-style alias of offPipe(). */
    public static function removeFilter(string $event, string|int|callable $idOrCallable): void
    {
        static::offPipe($event, $idOrCallable);
    }

    /**
     * 🧬 WP-style alias of transform().
     *
     * Runs $value through the full pipeline for $event; each transformer
     * receives the running value and MUST return the next one. No
     * short-circuit, no null-break — a real pipeline.
    */
    public static function applyFilters(string $event, mixed $value, mixed ...$context): mixed
    {
        return static::transform($event, $value, ...$context);
    }

    /** 🧬 WP-style alias of clearFilters(). */
    public static function clearFilters(?string $event = null): void
    {
        static::clearPipes($event);
    }

    public static function forgetFilters(?string $event = null): void
    {
        static::clearPipes($event);
    }

    /**
     * 🏛️ THE VERDICT ENGINE — run a narrative boolean deliberation pipeline.
     *
     * Three conviction modes — selected explicitly via the $mode argument:
     *
     *   ── Pipeline (default) ─────────────────────────────────────────────
     *   Runs jurors in priority order and STOPS at the first false verdict.
     *   Mirrors fire()'s short-circuit semantics, but in the boolean domain.
     *   Null = abstain; true = continue; false = immediate rejection.
     *
     *   ── Accord ─────────────────────────────────────────────────────────
     *   Runs ALL jurors. Each non-abstaining juror casts ONE equal vote.
     *   True majority wins; ties go to true (benefit of the doubt).
     *
     *   ── Influential ────────────────────────────────────────────────────
     *   Runs ALL jurors. Each non-abstaining juror casts a WEIGHTED vote.
     *
     *   ★ Expert nuance: the engine's priority ladder is INVERTED.
     *     PRIORITY_BEFORE (0) = "most urgent" = EARLIEST execution.
     *     A naïve `weight = $priority` would crown PRIORITY_AFTER (100)
     *     as the most authoritative — the exact opposite of intent.
     *
     *     Correct inversion: Weight = (PRIORITY_AFTER + 1) − $priority
     *       PRIORITY_CRITICAL (0)   → 101  ← most authoritative
     *       PRIORITY_HIGH     (10)  →  91
     *       PRIORITY_STANDARD (50)  →  51
     *       PRIORITY_LOW      (90)  →  11
     *       PRIORITY_MINOR    (100) →  1  ← least authoritative
     *
     *     Custom priorities outside [0..100] are clamped into the
     *     [CRITICAL..MINOR] domain so no juror ever contributes zero
     *     or negative weight.
     *
     *   NeonWarp: resolveLinkedEventKey() walks the soft-link graph before
     *   looking up the juror registry — junction() / symlink() / virtual()
     *   are all honoured transparently, identical to how fire() works.
     *
     * @param  string  $event    Verdict pipeline name (scope prefix honoured).
     * @param  array   $payload  Associative payload forwarded to every juror.
     *                           Mandatory — pass `[]` if the jury needs none.
     * @param  string  $mode     One of self::MODE_* — default = VMODE_PIPELINE.
     *
     * @return bool    true  = passes
     *                 false = rejected
     *                 true  when no jurors are staged (benefit of the doubt).
     *
     * @throws UnexpectedValueException  When a juror returns a non-bool, non-null value.
     */
    public static function judge(
        string $event,
        array $payload,
        string $verdictMode = self::VMODE_PIPELINE,
    ): bool {

        // 🚦 Engine dark ⇒ (bare) acceptation result.
        if (!static::synapsesOn()) {
            return true;
        }

        static::ensureSynapsesSorted();

        // ── NeonWarp: walk soft-links to find the real pipeline key.
        $key    = static::resolveLinkedEventKey($event);
        $stages = static::$conclaves[$key] ?? [];

        if ($stages === []) {
            return true;  // No jury → sovereign passes unopposed.
        }

        // Snapshot the stage list so registry mutations inside a juror cannot
        // alter the current verdict cycle.
        $stages = array_values($stages);

        // ─── PIPELINE: fail-fast on first false ───────────────────────────
        if ($verdictMode === self::VMODE_PIPELINE) {
            foreach ($stages as [, $id, $juror]) {
                $verdict = $juror($payload);

                if ($verdict !== null && !is_bool($verdict)) {
                    throw new UnexpectedValueException(
                        "Judge juror '{$id}' on '{$event}' must return bool|null; "
                        . get_debug_type($verdict) . ' returned.'
                    );
                }

                if ($verdict === false) {
                    return false;  // ← short-circuit; remaining jurors never run.
                }
                // true → continue · null → abstain, continue
            }

            return true;
        }

        // ─── DELIBERATE modes: run every juror, tally non-abstentions ─────
        $trueWeight  = 0.0;
        $falseWeight = 0.0;
        $weighted    = $verdictMode === self::VMODE_INFLUENTIAL;

        foreach ($stages as [$priority, $id, $juror]) {
            $verdict = $juror($payload);

            if ($verdict !== null && !is_bool($verdict)) {
                throw new UnexpectedValueException(
                    "Judge juror '{$id}' on '{$event}' must return bool|null; "
                    . get_debug_type($verdict) . ' returned.'
                );
            }

            if ($verdict === null) {
                continue;  // Abstain: excluded from both tallies.
            }

            if (!$weighted) {
                // ── Accord: flat count — every non-abstaining juror = 1 vote.
                $verdict ? ++$trueWeight : ++$falseWeight;
                continue;
            }

            // ── Influential: priority-inverted weight (see docblock above).
            // Priority is an execution-order axis, so invert it for authority.
            // Clamp custom priorities into the defined [BEFORE..AFTER] domain.
            $effectivePriority = max(
                self::PRIORITY_BEFORE,
                min(self::PRIORITY_AFTER, $priority),
            );

            $weight = (float) ((self::PRIORITY_AFTER + 1) - $effectivePriority);

            $verdict
                ? ($trueWeight += $weight)
                : ($falseWeight += $weight);
        }

        // True majority wins; ties go to true (benefit of the doubt).
        // No non-abstaining votes ⇒ benefit of the doubt, same as no jury.
        return $trueWeight >= $falseWeight;
    }

    /**
     * 🏛️ Summon a juror to the sealed Conclave of a verdict pipeline.
     *
     * Semantic alias for on() — identical wiring, separate sanctuary,
     * purpose-coded name. Keeps the judge() pipelines isolated behind closed 
     * doors, far away from the noisy fire()/fireAll() event synapses.
     *
     * NeonWarp: Virtual events block direct conclave summons, matching on()'s guard.
     *
     * Juror contract (The Rules of the Chamber):
     *   fn(mixed ...$payload): bool|null
     *   • true  → Casts a WHITE stone / passes
     *   • false → Casts a BLACK stone / fails
     *   • null  → Abstains (remains silent, excluded from ALL tally modes)
     *
     * @param  string          $event    Event name (scope prefix honoured)
     * @param  callable        $juror    fn(...$payload): bool|null
     * @param  int             $priority Lower = sits closer to the head of the table + HEAVIER weight in VMODE_INFLUENTIAL
     * @param  string|int|null $id       Optional stable id (auto-generated if null)
     * @return string|int      The resolved juror id within the chamber.
    */
    public static function conclave(
        string $event,
        callable $juror,
        int $priority = self::PRIORITY_NORMAL,
        string|int|null $id = null
    ): string|int {

        // ── NeonWarp virtual-event guard (mirrors on()'s guard exactly).
        if (method_exists(static::class, 'isVirtual') && static::isVirtual($event)) {
            throw new \LogicException(
                "Cannot summon a juror directly to the conclave of virtual event '{$event}'. Use a junction."
            );
        }

        $id  ??= static::generateListenerId($juror);
        $key   = static::resolveEventKey($event);

        static::$conclaves[$key][]    = [$priority, $id, $juror];
        static::$conclavesDirty[$key] = true;

        return $id;
    }

    /** 🏛️ Sugar: Seat a high-ranking juror who speaks BEFORE the normal assembly. */
    public static function conclaveBefore(
        string $event,
        callable $juror,
        string|int|null $id = null
    ): string|int {
        return static::conclave($event, $juror, self::PRIORITY_BEFORE, $id);
    }

    /** 🏛️ Sugar: Seat a shadow juror whose verdict is heard AFTER the main assembly. */
    public static function conclaveAfter(
        string $event,
        callable $juror,
        string|int|null $id = null
    ): string|int {
        return static::conclave($event, $juror, self::PRIORITY_AFTER, $id);
    }

    /**
     * 🏛️ Banish a juror from the judging assembly by ID or exact callable reference.
     *
     * Mirrors off() — identical contract, operates on the secluded $conclaves.
     * Strips them of their voting rights and casts them out of the chamber.
     */
    public static function excommunicate(string $event, string|int|callable $idOrJuror): void
    {
        $key = static::resolveEventKey($event);

        $resolvedId = is_callable($idOrJuror)
            ? static::generateListenerId($idOrJuror)
            : $idOrJuror;

        if (!isset(static::$conclaves[$key])) {
            return;
        }

        static::$conclaves[$key] = array_values(
            array_filter(
                static::$conclaves[$key],
                static fn(array $s): bool => $s[1] !== $resolvedId,
            ),
        );

        if (static::$conclaves[$key] === []) {
            unset(static::$conclaves[$key]);
        }
    }

    /** 🏛️ Tally the exact number of robed jurors currently seated in the conclave. */
    public static function conclaveCount(string $event): int
    {
        return count(static::$conclaves[static::resolveEventKey($event)] ?? []);
    }

    /** 🏛️ Check if the chamber is occupied. Returns true if ANY jurors have answered the summons. */
    public static function hasConclaves(string $event): bool
    {
        return !empty(static::$conclaves[static::resolveEventKey($event)]);
    }

        /**
     * 🏛️ Alias for conclave(). Summons a juror to the verdict pipeline.
     * Keeps the original stage nomenclature for rapid wiring.
     *
     * @param  string          $event    Event name
     * @param  callable        $juror    fn(...$payload): bool|null
     * @param  int             $priority Execution weight (lower = earlier)
     * @param  string|int|null $id       Optional stable id
     * @return string|int
    */
    public static function stage(
        string $event,
        callable $juror,
        int $priority = self::PRIORITY_NORMAL,
        string|int|null $id = null
    ): string|int {
        return static::conclave($event, $juror, $priority, $id);
    }

    /**
     * 🏛️ Alias for conclaveBefore(). Seats a juror BEFORE the normal assembly.
     *
     * @param  string          $event    Event name
     * @param  callable        $juror    fn(...$payload): bool|null
     * @param  string|int|null $id       Optional stable id
     * @return string|int
    */
    public static function stageBefore(
        string $event,
        callable $juror,
        string|int|null $id = null
    ): string|int {
        return static::conclaveBefore($event, $juror, $id);
    }

    /**
     * 🏛️ Alias for conclaveAfter(). Seats a juror AFTER the main assembly.
     *
     * @param  string          $event    Event name
     * @param  callable        $juror    fn(...$payload): bool|null
     * @param  string|int|null $id       Optional stable id
     * @return string|int
    */
    public static function stageAfter(
        string $event,
        callable $juror,
        string|int|null $id = null
    ): string|int {
        return static::conclaveAfter($event, $juror, $id);
    }

    /**
     * 🏛️ Alias for excommunicate(). Banishes a staged juror from the pipeline.
     *
     * @param  string               $event      Event name
     * @param  string|int|callable  $idOrJuror  Juror ID or exact callable
     * @return void
    */
    public static function unStage(string $event, string|int|callable $idOrJuror): void
    {
        static::excommunicate($event, $idOrJuror);
    }

    // ╔════════════════════════════════════════════════════════════════╗
    // ║  🎛️ ENGINE HIJACK LAYER — lifecycle hooks for the event engine  ║
    // ╚════════════════════════════════════════════════════════════════╝
    /**
     * 🎛️ engineHook() — resolve "prefix.phase" → "__jack:method.phase".
     *
     *   engineHook('turn.command')         → "__jack:turn.command"
     *   engineHook('health.state')         → "__jack:health.state"
     *   engineHook('turn.before')          → "__jack:turn.before"
    */
    public static function engineHook(string $key): string
    {
        [$method, $phase] = explode('.', $key, 2) + [1 => ''];
        return self::ENGINE_SCOPE . ':' . $method . '.' . $phase;
    }
    /**
     * 🔥 fireEngineHook() — raw dispatcher for engine internals.
     *
     * Bypasses synapsesOn() circular refrence — otherwise we'd recurse on the gate we're hooking.
     * Reads the raw registry; the synapsesOn() gate never blocks this path.
     *
     * @return mixed  First non-null result, or null.
    */
    protected static function fireEngineHook(string $event, mixed ...$payload): mixed
    {
        if (empty(static::$eventSynapses)) {
            return null;
        }

        if(!str_contains($event, ':'))
            $event = static::engineHook($event);

        static::ensureSynapsesSorted();

        foreach (static::$eventSynapses[static::resolveLinkedEventKey($event)] ?? [] as [, , $l]) {
            $r = $l(...$payload);
            if ($r !== null) {
                return $r;
            }
        }

        return null;
    }

    // ─── Internals ────────────────────────────────────────────────────────────

    /**
     * Generate a stable string ID for a listener.
     *
     * - Closures get a unique ID via spl_object_id wrapped in a prefix so it
     *   cannot collide with a user-supplied string.
     * - Named functions and static methods get a deterministic string.
     * - Instance methods encode the object's spl_object_id.
     *
     * NOTE: on() still defaults to spl_object_id (xRC.10 logic preserved);
     * pass static::generateListenerId($fn) as the $id argument to opt in.
    */
    protected static function generateListenerId(callable $listener): string
    {
        if ($listener instanceof Closure) {
            return '__closure#' . spl_object_id($listener);
        }

        if (is_string($listener)) {
            return $listener; // e.g. "MyClass::method" or "functionName"
        }

        if (is_array($listener)) {
            [$target, $method] = $listener;
            $objId = is_object($target) ? spl_object_id($target) : $target;
            return $objId . '::' . $method;
        }

        return '__callable#' . spl_object_id((object) $listener);
    }

    /**
     * 🧠 Compiles an event pattern into either an exact key string (for O(1) unset)
     * or a highly optimized matching Closure (for O(n) filtering).
     *
     * @return Closure(string): bool | string - $matcherMethod
    */
    protected static function compileEventMatcher(string $event): Closure|string
    {
        // Wildcard Regex Matching (only engaged if "?" is present in string)
        if (str_contains($event, '?')) {

            // تبدیل الگوی کاربر به یک الگوی Regex
            // الف) کاراکترهای خاص Regex را در رشته ورودی escape میکنیم
            $pattern = preg_quote($event, '/');

            // ب) علامت '?' که توسط کاربر وارد شده را به معادل Regex آن (.*) تبدیل می‌کنیم
            $pattern = str_replace('\?', '.*', $pattern);


            $separatorRegex = preg_quote(static::SCOPE_SEP, '/');

            // ج) مدیریت الگوهای خاص Scope و Event
            if (str_ends_with($event, ':') || str_ends_with($event, ':*')) {

                // 'admin?:*' -> 'admin?.' -> regex for 'admin.*\0.*'
                $pattern = rtrim(rtrim($pattern, '\*'), '\:') . $separatorRegex . '.*';

            }
            elseif (str_starts_with($event, ':') || str_starts_with($event, '*:')) {

                // '*:?render' -> '.*:?render' -> regex for '.*\0.*render'
                $pattern = '.*' . $separatorRegex . ltrim(ltrim($pattern, '\*'), '\:');

            }
            else {

                // 'scope?:event' -> 'scope?event' -> regex for 'scope.*\0event'
                $pattern = str_replace('\:', $separatorRegex, $pattern);

            }

            // پاک‌سازی با استفاده از Regex

            $finalRegex = '/^' . $pattern . '$/';
            return static fn(string $k): bool => preg_match($finalRegex, $k) === 1;
        }

        // ────── Fast Checks Section, no Regex ──────
        // ===========================================

        // 2. Scope-wide purge; "admin:" or "admin:*"
        if (str_ends_with($event, ':') || str_ends_with($event, ':*')) {

            // Suffix trim, not a character mask: rtrim(..., ':*') would strip
            // any trailing ':' or '*' independently.
            $scope  = rtrim(rtrim($event, '*'), ':');
            $prefix = $scope . static::SCOPE_SEP;

            return static fn(string $k): bool => str_starts_with($k, $prefix);
        }

        // 3. Event-wide purge (Ends with event) — e.g., ":render" or "*:render"
        if (str_starts_with($event, ':') || str_starts_with($event, '*:')) {

            $eventType = ltrim(ltrim($event, '*'), ':');
            $suffix    = static::SCOPE_SEP . $eventType;

            return static fn(string $k): bool => str_ends_with($k, $suffix);
        }

        // 4. Exact match (Returns String for ⚡ O(1) access)
        return static::resolveEventKey($event);
    }

    /**
     * 🔀 Sort event synapses, pipes, and dirty injector buckets.
     *
     * Lazy + dirty-flagged: no work is done until a read actually needs order.
     * All three registries share one comparator (lower priority = earlier run).
     *
     * Pipe strategy: per-key dirty flags — only the buckets that
     * received a new transformer since the last pass are re-sorted. This
     * keeps the cost O(k log k) per dirty bucket instead of O(n log n).
    */
    protected static function ensureSynapsesSorted(): void
    {
        // Shared comparator taught from ensureSorted(): lower int = earlier run.
        $cmp = static fn(array $a, array $b): int => $a[0] <=> $b[0];

        // ── Event synapses (per-key dirty flag — one event bucket will Not remake or resort all)
        foreach (static::$synapsesDirty as $key => $dirty) {
            if (!$dirty) {
                continue;
            }
            if (isset(static::$eventSynapses[$key])) {
                usort(static::$eventSynapses[$key], $cmp);
            }
            static::$synapsesDirty[$key] = false;
        }

        // ── Pipe synapses (per-key dirty)
        foreach (static::$pipesDirty as $key => $dirty) {
            if (!$dirty) {
                continue;
            }
            if (isset(static::$pipes[$key])) {
                usort(static::$pipes[$key], $cmp);
            }
            static::$pipesDirty[$key] = false;
        }

        // ── Per-key injector buckets (only the dirty ones)
        foreach (static::$injectorsDirty as $key => $dirty) {
            if (!$dirty) {
                continue;
            }

            if (isset(static::$paramTypeInjectors[$key])) {
                usort(static::$paramTypeInjectors[$key], $cmp);
            }

            if (isset(static::$paramNameInjectors[$key])) {
                usort(static::$paramNameInjectors[$key], $cmp);
            }

            static::$injectorsDirty[$key] = false;
        }

        // ── Lazy O(k log k) sorting of dirty judge buckets.
        foreach (static::$conclavesDirty as $key => $dirty) {
            if (!$dirty) {
                continue;
            }

            if (isset(static::$conclaves[$key])) {
                usort(static::$conclaves[$key], $cmp);
            }

            static::$conclavesDirty[$key] = false;
        }
    }

}
