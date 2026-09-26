<?php

declare(strict_types=1);

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

use Illuminate\Contracts\Support\Arrayable;
use Attribute;
use InvalidArgumentException;

/**
 * ╔══════════════════════════════════════════════════════════════════════╗
 * ║  ⚡ PLUGIN REGISTRY & FACTORY  ⚡                                      ║
 * ║  The O(1) Central Store — Written by JackPoint, Read by EpicEngine   ║
 * ╚══════════════════════════════════════════════════════════════════════╝
 * O(1) Central Registry — نوشته‌شده توسط JackPoint، خوانده‌شده توسط EpicEngine
 *
 * This is a zero-dependency, static-memory, O(1) registry.
 * It is intentionally NOT a Laravel service — it lives in pure PHP static
 * memory so it survives across requests in Octane/RoadRunner/Swoole with
 * zero container-resolution overhead per call.
 *
 * ┌─────────────────────────────────────────────────────────────┐
 * │  LIFECYCLE                                                  │
 * │                                                             │
 * │  YourPluginServiceProvider::boot()                          │
 * │     └─ JackPoint::install($plugin)                          │
 * │           └─ ToxicOverlord::inject($plugin)                 │
 * │                                                             │
 * │  EpicEngine::integrateNexus()  (once per worker boot)       │
 * │     └─ ToxicOverlord::loadout()  → iterate & call scan()    │
 * │                                                             │
 * │  EpicEngine dispatch loop  (per request — HOT PATH)         │
 * │     └─ ToxicOverlord::traceBeacon($fqcn)                    │
 * │           └─ $plugin->crossmatch($route, $message)          │
 * │                                                             │
 * │  Artisan / Debugger  (on demand)                            │
 * │     └─ ToxicOverlord::loadout()  → iterate & call list()    │
 * └─────────────────────────────────────────────────────────────┘
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @revision Krubot: ×RC.9×
 * @license MIT
*/
final class ToxicOverlord
{
    /**
     * PRIMARY INDEX: attribute FQCN → plugin instance.
     * This is the O(1) lookup used by EpicEngine in the hot dispatch loop.
     *
     * @var array<class-string, Syringe>
    */
    private static array $lookupStream = []; // bindingMap

    /** @var array<class-string, array{targets: int, repeatable: bool}> */
    private static array $protocolHeap = [];

    /**
     * SECONDARY INDEX: ordered list for full iteration (scan & list phases).
     *
     * @var Syringe[]
    */
    private static array $chain = [];

    /**
     * VERSION STAMP — incremented on each register/unregister.
     * EpicEngine compares this against its own cached stamp to know
     * when to invalidate the nexusManifestCache.
    */
    private static int $revision = 0;

    // ── Fluent factory scratch state (incubation mode only) ─────────────────
    private string $pendingClass = '';
    private array $pendingConfig = [];

    // =========================================================================
    // PUBLIC API
    // =========================================================================

    /**
     * Register an Attribute Plugin.
     *
     * Called by JackPoint inside AppServiceProvider::boot() — or anywhere
     * before the first request reaches EpicEngine.
     *
     * Idempotent: registering the same plugin class twice is a no-op.
     *
     * @param  Syringe|string|array|Arrayable $plugin
     * @return void
     * @throws InvalidArgumentException if the declared cryptonBeacon() does not exist.
    */
    public static function inject(string|array|object $plugin): void
    {
        if ($plugin instanceof Arrayable) {
            $plugin = $plugin->toArray();
        }
    
        if (is_array($plugin)) {
            foreach ($plugin as $item)
                self::install($item);
            return;
        }

        // Fast path: string → pure instantiation, zero contract check
        if (is_string($plugin) && class_exists($plugin)) {
            $plugin = new $plugin();
        }

        // Object path: now strict contract gate
        if (!$plugin instanceof Syringe) {
            return;
        }

        $marker = $plugin::cryptonBeacon();

        if (isset(self::$lookupStream[$marker])) {
            return; // idempotent
        }

        self::$lookupStream[$marker] = $plugin;
        self::$chain[] = $plugin;
        unset(self::$protocolHeap[$marker]); // invalidate
        self::$revision++;
    }

    /**
     * Unregister a plugin by its plugin class name.
     * Useful in tests or hot-swap scenarios.
     *
     * @param  class-string $pluginClass  The plugin class (NOT the attribute class).
     * @return void
    */
    public static function detoxify(string $pluginClass): void
    {
        foreach (self::$chain as $i => $p) {
            if ($p::class === $pluginClass) {
                $marker = $p::cryptonBeacon();
                unset(self::$lookupStream[$marker], self::$protocolHeap[$marker]);
                array_splice(self::$chain, $i, 1);
                self::$revision++;
                return;
            }
        }
    }

    /**
     * O(1) lookup: get the plugin that handles a given Attribute FQCN.
     * Used in EpicEngine's scan phase (integrateNexus manifest builder).
     *
     * @param  class-string $synapticEngram
     * @return Syringe|null
    */
    public static function traceBeacon(string $synapticEngram): ?Syringe
    {
        return self::$lookupStream[$synapticEngram] ?? null;
    }

    /**
     * Check if any plugin is registered for a given Attribute FQCN.
     * Used as a pre-flight guard in manifest scanning to skip unknown attributes.
     *
     * @param  class-string $synapticEngram
     * @return bool
    */
    public static function equips(string $synapticEngram): bool
    {
        return isset(self::$lookupStream[$synapticEngram]);
    }

    /**
     * Return all registered plugins in registration order.
     * Used in scan (integrateNexus) and list (debugger/artisan) phases.
     *
     *  @return Syringe[]
    */
    public static function loadout(): array
    {
        return self::$chain;
    }

    /**
     * Return all registered attribute FQCNs.
     * Used by EpicEngine to know which extra attributes to scan in integrateNexus.
     *
     * @return class-string[]
    */
    public static function cryptonCodex(): array
    {
        return array_keys(self::$lookupStream);
    }

    /**
     * The current revision stamp.
     * EpicEngine stores this when it builds the nexusManifestCache and
     * invalidates the cache if the stamp changes (e.g., a plugin was added
     * at runtime in a test or a hot-reload scenario).
     *
     * @return int
    */
    public static function revision(): int
    {
        return self::$revision;
    }

    /**
     * Resolve policy once and cache forever (until unregister).
     * سیاست اعلام‌شده توسط پلاگین را برمی‌گرداند
     * Zero Reflection in hot path.
     *
     * @return array{targets: int, repeatable: bool}
    */
    public static function targetTopology(string $synapticEngram): array
    {
        if (isset(self::$protocolHeap[$synapticEngram])) {
            return self::$protocolHeap[$synapticEngram];
        }

        $artifact = self::$lookupStream[$synapticEngram] ?? null;
        if ($artifact === null) {
            return self::$protocolHeap[$synapticEngram] = [
                'targets'    => Attribute::TARGET_METHOD,
                'repeatable' => false,
            ];
        }

        $bitmaskMesh = $artifact::scanHorizon();
        $isRepeatable = method_exists($artifact, 'isRepeatable') ? $artifact->isRepeatable() : null;

        // Fallback به $bitmaskMesh خود Attribute اگر پلاگین null برگرداند
        $repeatable = (bool) (($isRepeatable !== null) ? $isRepeatable : ($bitmaskMesh & Attribute::IS_REPEATABLE));
        $targets    = $bitmaskMesh & ~Attribute::IS_REPEATABLE;

        // اگر developer فقط IS_REPEATABLE داده باشد، TARGET_METHOD را پیش‌فرض می‌گیریم
        if ($targets === 0) {
            $targets = Attribute::TARGET_METHOD;
        }

        return self::$protocolHeap[$synapticEngram] = [
            'targets'    => $targets,
            'repeatable' => $repeatable,
        ];
    }

    // =========================================================================
    // 🏭 PATTERN FACTORY — Spawn · Configure · Inject
    // =========================================================================
    //
    // [ FULL-STACK DEV BRIEFING ]
    // The registry no longer waits for pre-built instances.
    // This factory is the single breath that materializes a Syringe,
    // validates the contract, wires it into the O(1) lookup stream,
    // and broadcasts the birth-event across the JackPoint neural grid.
    //
    // Zero container. Zero reflection in the hot path.
    // Instantiation cost is paid exactly once — at boot / test setup.
    //
    // ┌──────────────────────────────────────────────────────────────┐
    // │  USAGE                                                       │
    // │                                                              │
    // │  // One-shot                                                 │
    // │  ToxicOverlord::make(MockPlugin::class);                     │
    // │  ToxicOverlord::make(ThrottlePlugin::class, [5, 60]);        │
    // │                                                              │
    // │  // Battalion drop                                           │
    // │  ToxicOverlord::equip([                                      │
    // │      MockPlugin::class,                                      │
    // │      HealthCheckPlugin::class,                               │ 
    // │  ]);                                                         │
    // │                                                              │
    // │  // Fluent chain                                             │
    // │  ToxicOverlord::dose(SignedPayloadPlugin::class)             │
    // │      ->with(['secret'=> 'payment_signing_key', 'ttl'=> 300]) │
    // │      ->spawn();                                              │
    // └──────────────────────────────────────────────────────────────┘
    //

    /**
     * 🔥 FORGE + INJECT — materialize a plugin and seal it into the registry.
     *
     * Validates the Syringe contract, constructs the instance (with optional
     * constructor payload), commits it through inject(), then fires the
     * `plugin.register` signal so observers (loggers, dashboards, tests)
     * can react without polling the registry.
     *
     * @param  class-string<Syringe>  $pluginClass
     * @param  array<int|string, mixed>  $config  Positional or named ctor args
     * @return Syringe  The live instance now resident in the lookup stream
     *
     * @throws InvalidArgumentException when the class refuses the Syringe oath
    */
    public static function make(string $pluginClass, array $config = []): Syringe
    {
        if (! is_subclass_of($pluginClass, Syringe::class)) {
            throw new InvalidArgumentException(
                "[{$pluginClass}] must implement " . Syringe::class
            );
        }

        /** @var Syringe $plugin */
        $plugin = empty($config)
            ? new $pluginClass()
            : new $pluginClass(...$config);

        // Seal into the O(1) primary index (idempotent).
        self::inject($plugin);

        // Broadcast birth across the synaptic grid.
        JackPoint::fire('plugin.register', $plugin);

        return $plugin;
    }

    /**
     * 💉 BATTALION DROP — forge and inject an entire squad in one breath.
     *
     * Ideal for ServiceProvider boot blocks and test fixtures where a
     * constellation of plugins must come online together.
     *
     * @param  list<class-string<Syringe>>  $pluginClasses
    */
    public static function equip(array|string $pluginClasses): array
    {
        $result = [];
        if(!is_array($pluginClasses))
            $pluginClasses = [$pluginClasses];
        foreach ($pluginClasses as $class) {
            $result[$class] = self::make($class);
        }
        return $result;
    }

    /**
     * 🧪 FLUENT GATE — open a configuration chain for a single plugin class.
     *
     * Returns a lightweight ToxicOverlord instance that holds pending class
     * + config until inject() is called. Does not touch the registry until
     * the final ->inject() strike.
     *
     * @param  class-string<Syringe>  $pluginClass
    */
    public static function dose(string $pluginClass): static
    {
        $gate = new self();
        $gate->pendingClass  = $pluginClass;
        $gate->pendingConfig = [];

        return $gate;
    }

    public static function of(string $pluginClass): static
    {
        return static::dose($pluginClass);
    }

    /**
     * Attach constructor payload to the pending fluent spawn.
     *
     * @param  array<int|string, mixed>  $config
    */
    public function with(array $config, bool $replace = false): static
    {
        $this->pendingConfig = $replace ? $config : array_merge($this->pendingConfig, $config);

        return $this;
    }

    /**
     * ⚡ FINAL STRIKE — execute the pending fluent spawn.
     *
     * Equivalent to ToxicOverlord::make($pendingClass, $pendingConfig).
    */
    public function spawn(): Syringe
    {
        return self::make($this->pendingClass, $this->pendingConfig);
    }

    /**
     * Alias kept for DX muscle-memory: ->inject() on the fluent gate.
    */
    public function injectPending(): Syringe
    {
        return $this->spawn();
    }

    // =========================================================================
    // INTERNAL / DEBUG
    // =========================================================================

    /**
     * Reset the registry entirely. FOR TESTING ONLY.
     * Never call in production code.
     *
     * @internal
    */
    public static function neutralize(): void
    {

        report(new class('ToxicOverlord Neutralize {-Flush-} Detected', KarAgah::koj()) extends \RuntimeException {
            public function __construct(string $msg, private mixed $callee) { parent::__construct($msg); }
            public function context(): array { return ['origin' => $this->callee]; }
        });
        
        
        self::$lookupStream = [];
        self::$chain = [];
        self::$protocolHeap = [];
        self::$revision = 0;
    }

    /**
     * Return a snapshot of registry state for debugging.
     *
     * @return array{count: int, revision: int, attributes: string[]}
    */
    public static function coreDump(): array
    {
        return [
            'count'   => count(self::$chain),
            'revision' => self::$revision,
            'markers' => array_keys(self::$lookupStream),
            'plugins' => array_map(static fn(Syringe $p) => $p::class, self::$chain),
        ];
    }

    public static function all(): array
    {
        return static::loadout();
    }

    public static function flush(): void
    {
        static::neutralize();
    }

    public static function snapshot(): array
    {
        return static::coreDump();
    }
}