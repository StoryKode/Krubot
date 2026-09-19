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

use ReflectionClass;
use ReflectionException;
use Illuminate\Support\Traits\Macroable;

/**
 *                    ***_KarAgah 🕵️‍♂️🛰💻 v1.0_***
 * 
 * Cyber-Telemetry Matrix & The Eternal Inspector 👁️ of KrubiK
 *         Zero-Overhead Origin Locator && Smart-Mutation Singleton
 * 
 * It took me a full day from idea the bare-morning to finish at v1.0 in the sleep-time.
 * Hope you like it :)
 *                      -Toy MaKer
 *
 * KarAgah serves as the "Recording Angel" of the BlackWire infrastructure. 
 * Operating as a phantom Detective, he seamlessly binds him to the core memory,
 * watching thousands of event synapses and hooks with relentless O(1) velocity.
 * He interrogates the stack-trace in real-time, slicing through framework noise 
 * to pinpoint the exact origin of any execution without a single drop of I/O bleed.
 * When the cockpit HUD is summoned, the Inspector is already waiting to testify.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class KarAgah
{
    use Macroable; // Utilizing PHP+Laravel Power ⚡, KarAgah is Expandable by your Strategies;

    /**
     * 🎯 Target Acquired (Sector 0)
     * External user-land asset. Fully exposed to the Detective's main HUD.
    */
    protected const SECTOR_TARGET = 0;

    /**
     * 👻 Phantom Protocol (Sector 1)
     * Stealth engine internals (e.g., KrubiK). Hidden from the HUD, but secretly recorded in the Shadow Matrix.
    */
    protected const SECTOR_PHANTOM = 1;

    /**
     * 🕳️ Absolute Void (Sector 2)
     * Underlying framework (e.g., Illuminate). Permanently obliterated and vaporized from all records.
    */
    protected const SECTOR_VOID = 2;
    
    /**
    * 🕳️ The Absolute Void (Permanent Invisibility Cloak)
    * 
    * A configurable matrix of namespace prefixes that the Detective will completely ignore.
    * By default, Laravel's core engine ('Illuminate') is rendered invisible,
    * slicing through framework noise so only pure application frames hit the Report.
    * 
    * @warning:
    * Namespaces defined here are completely obliterated from observe. They never enter the Logs,
    * and they never enter the Shadow Matrix. (e.g., The underlying framework).
    * 
    * @var list<string>
    */
    protected static array $voidNamespaces = [
        'Illuminate', // Easily hide Laravel Core
        // 'Symfony', // You can add more here...
    ];

    /**
    * 🥷 The Phantom Protocols (Declassifiable Stealth)
    * 
    * Namespaces here are hidden by default, but secretly recorded in the Shadow Matrix.
    * If a pure-core hook occurs, these frames are declassified to resolve the paradox.
    * 
    * Additional stealth zones are loaded via config.
    * 
    * @var list<string>
    */
    protected static array $stealthNamespaces = [];

    /**
     * Origin HUD master switch ⚡
     * Registration-time only — never touch this on the dispatch burn.
     * Flip false in prod if you don't need file:line maps in the cockpit.
    */
    protected static bool $recordOrigins = true;

    /**
     * Backtrace window (frames) 🔍. Config can widen it; we never let it go unbounded.
     * IGNORE_ARGS is non-negotiable: we want coordinates, not the 4MB payload you closed over.
    */
    protected static int $originTraceDepth = 24;

    /**
     * Max EXTERNAL frames stored per listener 🛑
     * Full 24-frame chains × thousands of synapses = silent RAM leak. Cap it.
    */
    protected static int $originChainLimit = 8;

    /**
     * Process-local skip index & O(1) Hash Map 🧠 Built once. Never Reflection.
     * Combines dynamic directory learning with instant basename lookups.
     *
     * @var array{
     *     booted: bool,
     *     basenames: array<string, true>,
     *     dirs: list<string>,
     *     self: class-string,
     *     ns: string
     * }|null
    */
    protected ?array $matrixTopology = null;

    /** 🪦 Shared disabled payload — one allocation for the whole process. Do not mutate. */
    protected ?array $originDisabledSentinel = null;

    /**
     * 🎯 The dynamically bound target host class (FQCN) resolved during instantiation.
    */
    protected string $resolvedHostClass;

    /**
     * 🕵️‍♂️ The Singleton Inspector Instance.
     * Keeps the O(1) I/O speed while allowing static DX calls like `KarAgah::koj()`.
    */
    protected static ?self $inspector = null;    

    /**
     * Bootstrap the Telemetry Instance.
     *
     * @param string|object|null $targetHost Accepts a Fully Qualified Class Name (string) or an Object to be discovered.
    */
    public function __construct(string|object|null $targetHost = null)
    {
        $target = $targetHost ?? $this;
        $this->resolvedHostClass = is_object($target) ? get_class($target) : (string) $target;
    }

    /**
     * 🕷️ The Symbiote Matrix (jQuery-DX Getter/Setter/Vanish)
     * 
     * Polymorphic routing for the Inspector's state with Smart Mutation.
     * - morph()         => GET: Returns the active Inspector (auto-boots if missing).
     * - morph(null)     => VANISH: Purges the Inspector from memory (detaches from host).
     * - morph(KarAgah)  => OVERRIDE: Assimilates a ready-made Inspector instance.
     * - morph($host)    => MUTATE/REBOOT: Smart-checks DNA. Binds a fresh Inspector ONLY if the host differs.
     * 
     * @param string|object|null $target 
    */
    public static function morph(mixed $target = null): ?self
    {
        // 1. GETTER (No args): Return active Inspector or boot a default one.
        if (func_num_args() === 0) {
            return self::$inspector ??= new self();
        }

        // 2. VANISH: Explicit null passed -> Purge memory (Detach).
        if ($target === null) {
            self::vanish();
            return null;
        }

        // 3. OVERRIDE: An existing Inspector instance is passed -> Assimilate.
        if ($target instanceof self) {
            self::vanish();
            return self::$inspector = $target;
        }

        // 4. SMART MUTATION CHECK 🧠: Extract the incoming host's DNA.
        $incomingHostDNA = is_object($target) ? get_class($target) : (string) $target;

        // If the Symbiote is already alive and attached to this exact host, lock performance at $O(1)$.
        if (self::$inspector !== null && self::$inspector->resolvedHostClass === $incomingHostDNA) {
            return self::$inspector;
        }

        // 5. REBOOT: String (Class FQCN) or Object passed -> DNA mismatch detected -> Purge the old entity, Spawn & bind to the new host.
        self::vanish();
        return self::$inspector = new self($target);
    }

    /**
     * 🎭 The Symbiote Catalyst (Alias for morph)
     * 
     * Syntactic sugar to instantly spawn, retrieve, or mutate the Inspector matrix.
     * Conceptually signifies "becoming" or seamlessly transitioning into a new state.
     * 
     * @param string|object|null $target 
    */
    public static function sho(mixed $target = null): ?self
    {
        return func_num_args() === 0 ? self::morph() : self::morph($target);
    }

    /**
     * 👁️ The Identity Matrix (Read-Only Probe)
     * 
     * Silently penetrates the BlackWire infrastructure to reveal the active Inspector.
     * Unlike `morph()`, this is a pure O(1) non-mutating getter. It does not spawn, 
     * assimilate, or reboot the Symbiote. Use this to check the Detective's pulse 
     * without triggering a boot sequence.
     * 
     * @return self|null The currently bound Inspector entity, or null if dormant.
    */
    public static function identity(): ?self
    {
        return self::$inspector;
    }

    /**
     * 👤 The Interrogation Ping (Alias for identity)
     * 
     * Syntactic sugar for `identify()`. Derived from the native query "Who goes there?" (Kii?).
     * Flashes the active Symbiote matrix onto the HUD without altering its host DNA.
     * Usage: KarAgah::kii() // Means "Reveal your identity, Detective!"
     * 
     * @return self|null The active Inspector entity.
    */
    public static function kii(): ?self
    {
        return self::$inspector;
    }

    /**
     * The Symbiote Purge (Memory Flush)
     * 
     * Completely neutralizes the active Inspector entity and forces Garbage Collection.
     * Absolute necessity for daemonized architectures (Swoole, Octane, RoadRunner) 
     * to prevent memory leaks and ensure a pristine matrix between worker lifecycle resets.
     * 
     * @return void
    */
    public static function vanish(): void
    {
        self::$inspector = null;
    }

    /**
     * 🦇 The Inspector's Hibernation Gateway
     * 
     * Syntactic sugar for `vanish()`. Instantly detaches the Symbiote 
     * and puts the matrix to sleep. Clean, fast, and terminal.
     * Usage: KarAgah::kho() // Means Sleep Detective!
     * 
     * @return void
    */
    public static function kho(): void
    {
        self::vanish();
    }

    /**
     * 🕵️‍♂️ The Detective's Primary Origin Locator.
     * Uses a Inspector Singleton to ensure scandir() runs exactly ONCE per request.
     * 
     * @param int|null $depth Target backtrace depth to inspect.
     * @param string|object|null $targetHost Contextual host to bind the Symbiote to (null means "no change").
    */
    public static function where(?int $depth = null, string|object|null $targetHost = null): array
    {
        // Mutate ONLY if a distinct host is explicitly provided.
        // If $targetHost is null (omitted or explicit), fallback to the GETTER logic.
        if ($targetHost !== null) {
            self::morph($targetHost);
        } else {
            self::morph(); // Getter: Boot default or use existing (No vanish the $inspector!)
        }
        
        return self::$inspector->traceOrigin($depth);
    }

    /**
     * 🕵️‍♂️ The Detective's DX Gateway (Alias for `where`).
     * Usage: KarAgah::koj()
    */
    public static function koj(?int $depth = null, string|object|null $targetHost = null): array
    {
        return self::where($depth, $targetHost);
    }

    /**
     * Public Gateway:
     * 📍 Capture the external registration origin + a short call chain.
     *
     * 100% Backward Compatible Gateway.
     * Skips internal Engine frames dynamically with O(1) speed.
     * Exposes 'BlackWire' unconditionally as per core directive.
     *
     * @return array{
     *     primary: array{file: string, line: int, short: string, function: ?string, class: ?string, captured: bool},
     *     chain: list<array{file: string, line: int, short: string, function: ?string, class: ?string, captured: bool}>
     * }
    */
    public function traceOrigin(?int $depth = null): array
    {
        // 🚀 Early ejection for maximum dispatch speed
        if (!self::$recordOrigins) {
            return $this->originDisabledSentinel();
        }

        // Second check post-boot
        if(!self::ammunitize()) {
            return $this->originDisabledSentinel();
        }

        $depth = $depth ?? self::$originTraceDepth;
        if ($depth < 2) {
            $depth = 2;
        }

        // 🕵️‍♂️ The Detective enters the stack. IGNORE_ARGS is critical for memory safety.
        $trace = \debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $depth);
        $traceCount = count($trace);
        $originAtlas = $this->classifyBaseEvidences();
        $limit = self::$originChainLimit > 0 ? self::$originChainLimit : 8;
        $chain = [];
        $shadowMatrix = []; // 🥷 Initiate The Shadow Matrix for Phantom Protocols

        for ($i = 0; $i < $traceCount; $i++) {
            $frame = $trace[$i];
            $file  = $frame['file'] ?? '';

            // ── Ghost frame: no file, but has a class ──
            // Happens when a method is invoked via ReflectionMethod::invoke(), call_user_func(), etc.
            // In this branch $class IS the owner of the function being executed, so it is safe to
            // pass it as the authoritative class hint to investigateEnigma().
            if ($file === '') {
                $ghostClass  = $frame['class']    ?? null;
                $syntheticFn = $frame['function'] ?? null;

                if ($ghostClass === null) {
                    continue; // C-level internal function, skip
                }

                // Fast-path: KarAgah's own namespace — drop immediately
                if ($ghostClass === $originAtlas['self'] || str_starts_with($ghostClass, $originAtlas['ns'])) {
                    continue;
                }

                // Resolve the real file that contains this class
                $resolvedFile = '';
                $rc           = null;

                try {
                    $rc           = new ReflectionClass($ghostClass);
                    $resolvedFile = $rc->getFileName() ?: '';
                } catch (ReflectionException) {}

                if ($resolvedFile === '') {
                    // آخرین راه حل: هیچ فایلی پیدا نشد

                    // Unresolvable synthetic frame (built-in, eval'd, etc.)
                    // For ghost frames we DO know the class, so we can classify by class alone.

                    $isVoid = false;
                    foreach ($originAtlas['void'] as $voidNs) {
                        if (str_starts_with($ghostClass, $voidNs)) { $isVoid = true; break; }
                    }
                    if ($isVoid || $ghostClass === $originAtlas['self'] || str_starts_with($ghostClass, $originAtlas['ns'])) {
                        continue;
                    }
                    $isPhantom = false;
                    foreach ($originAtlas['stealth'] as $stealthNs) {
                        if (str_starts_with($ghostClass, $stealthNs)) { $isPhantom = true; break; }
                    }
                    if ($isPhantom) {
                        $shadowMatrix[] = [
                            'file'     => 'reflection-invoked',
                            'line'     => 0,
                            'function' => is_string($syntheticFn) ? self::decryptFunctionSignature($syntheticFn) : $syntheticFn,
                            'class'    => $ghostClass,
                            'short'    => $ghostClass . '@reflection',
                        ];
                        continue; // Process next frame
                    }
                    // هیچ فایلی پیدا نشد → فقط یک فریم synthetic بگذار و ادامه بده
                    // (دیگر break نمی‌کنیم تا chain بقیه فریم‌ها را هم جمع کند)
                    $chain[] = [
                        'file'     => 'reflection-invoked',
                        'line'     => 0,
                        'function' => is_string($syntheticFn) ? self::decryptFunctionSignature($syntheticFn) : $syntheticFn,
                        'class'    => $ghostClass,
                        'short'    => $ghostClass . '@reflection',
                    ];
                    if (count($chain) >= $limit) {
                        break; // Chain limit reached, aborting deeper trace
                    }
                    continue; // برو سراغ فریم بعدی
                }

                // File resolved — now classify it.
                // Pass $ghostClass as the authoritative $class parameter because the file
                // genuinely belongs to this class (unlike normal frames where class is the CALLEE).
                $ghostSector = $this->investigateEnigma($resolvedFile, $ghostClass, $originAtlas);

                if ($ghostSector === self::SECTOR_VOID) {
                    continue;
                }

                // Determine exact call-site line by scanning the already-captured $trace
                $normResolved = str_replace('\\', '/', $resolvedFile);
                
                // خط واقعی فراخوانی را از خودِ $trace پیدا کن (نه debug_backtrace تازه)
                $callSiteLine = 0;

                // Look forward first (most common)
                // اول به جلو نگاه کن (بعد از فریم فعلی)
                for ($j = $i + 1; $j < $traceCount; $j++) {
                    $t = $trace[$j] ?? null;
                    if (!$t || !isset($t['file'])) continue;
                    if (str_replace('\\', '/', $t['file']) === $normResolved) {
                        $callSiteLine = (int) ($t['line'] ?? 0);
                        break;
                    }
                }
                // Fallback: look backward (rare)
                // Fallback: اگر پیدا نشد، به عقب هم نگاه کن (نادر)
                if ($callSiteLine === 0) {
                    for ($j = 0; $j < $i; $j++) {
                        $t = $trace[$j];
                        if (!$t || !isset($t['file'])) continue;
                        if (str_replace('\\', '/', $t['file']) === $normResolved) {
                            $callSiteLine = (int) ($t['line'] ?? 0);
                            break;
                        }
                    }
                }
                // Last fallback: Method start line
                if ($callSiteLine === 0 && $syntheticFn !== null && $rc !== null) {
                    try {
                        $callSiteLine = $rc->getMethod($syntheticFn)->getStartLine() ?: 0;
                    } catch (ReflectionException) {}
                }

                $base        = basename($resolvedFile);
                $parsedGhost = [
                    'file'     => $resolvedFile,        // حالا مسیر واقعی مثل ...\JackPointNexus.php
                    'line'     => $callSiteLine,        // خط دقیق JackPoint::on(...)
                    'function' => is_string($syntheticFn) ? self::decryptFunctionSignature($syntheticFn, $resolvedFile) : $syntheticFn,
                    'class'    => $ghostClass,
                    'short'    => $base . ':' . $callSiteLine,
                ];

                if ($ghostSector === self::SECTOR_PHANTOM) {
                    // 🥷 Shadow Matrix: Record secretly, bypass the main targeting chain
                    // (eg. KrubiK internals configured as stealth drop here!)
                    $shadowMatrix[] = $parsedGhost;
                    continue;
                }

                // 🎯 Target acquired
                $chain[] = $parsedGhost;

                if (count($chain) >= $limit) {
                    break; // Report Chain limit reached, aborting deeper trace
                }

                // مهم: دیگر فوری break نمی‌کنیم → loop ادامه پیدا می‌کند و بقیه فریم‌های خارجی را هم جمع می‌کند
                continue;

            }

            // ── Normal frames with a physical file ──
            //
            // ⚠️  PHP backtrace semantics — the N-th frame means:
            //       frame[N].file / frame[N].line  = WHERE the call was made (the CALLER's coordinates)
            //       frame[N].function / frame[N].class = WHAT was called (the CALLEE, i.e. frame[N-1])
            //
            // Consequence: the `class` stored in frame[N] is the namespace of the function that was
            // *invoked from* this file, NOT the class that owns this file.
            //
            // Example — WebAppDriver calls JackPoint::injectParamType():
            //   frame[N].file     = WebAppDriver.php          ✅ correct
            //   frame[N].class    = KrubiK\JackPoint          ❌ this is the CALLEE
            //   frame[N].function = injectParamType            ❌ this is the CALLEE
            //
            // If we hand that `class` to investigateEnigma() it wrongly classifies a perfectly
            // innocent user-land file (WebAppDriver.php) as PHANTOM, because KrubiK\* is stealth.
            //
            // Fix: pass NULL for $class so the Detective detects the frame purely by its
            // physical file path (which is always correct). Then, for the displayed metadata,
            // pull function/class from frame[N+1] — that is the function whose body this file
            // actually contains and whose call-site line is recorded in frame[N].
            $sector = $this->investigateEnigma($file, null, $originAtlas);

            if ($sector === self::SECTOR_VOID) {
                continue; // 🕳️ Obliterate completely into the Void
            }

            // The function/class that *this* frame's code belongs to lives one level deeper:
            // frame[i+1] is the frame whose body is inside $file.
            $ownerFrame = $trace[$i + 1] ?? [];
            $ownerClass = $ownerFrame['class']    ?? null;
            $ownerFn    = $ownerFrame['function'] ?? null;

            // Suppress engine-internal metadata from the HUD.
            // ownerClass comes from frame[i+1] which is the function this $file's code
            // belongs to — if that function is engine-internal, null it out so the HUD
            // shows clean coordinates without leaking engine implementation details.
            if ($ownerClass !== null) {
                $suppressOwner = $ownerClass === $originAtlas['self']
                    || str_starts_with($ownerClass, $originAtlas['ns']);

                if (!$suppressOwner) {
                    foreach ($originAtlas['stealth'] as $stealthNs) {
                        if (str_starts_with($ownerClass, $stealthNs)) {
                            $suppressOwner = true;
                            break;
                        }
                    }
                }

                if ($suppressOwner) {
                    $ownerClass = null;
                    $ownerFn    = null;
                }
            }

            // Parse the entity signature
            $line = $frame['line'] ?? 0;
            $base = basename($file);
            $parsedFrame = [
                'file'     => $file,
                'line'     => $line,
                'function' => is_string($ownerFn) ? self::decryptFunctionSignature($ownerFn, $file) : $ownerFn,
                'class'    => $ownerClass,
                'short'    => $base . ':' . $line,
                // 'captured' => true,
            ];

            if ($sector === self::SECTOR_PHANTOM) {
                // 🥷 Shadow Matrix: Record secretly, bypass the main targeting chain
                $shadowMatrix[] = $parsedFrame;
                continue; 
            }

            // 🎯 External target acquired! (Pure User App logic)
            $chain[] = $parsedFrame;

            if (count($chain) >= $limit) {
                break; // Report Chain limit reached, aborting deeper trace
            }
        }

        // Edge Case: The entire trace was internal engine calls (should rarely happen)
        if (empty($chain)) {

            if (!empty($shadowMatrix)) {

                // 👻 GHOST PROTOCOL: Declassify the Shadow Matrix!
                // Reveal the deepest phantom frame as the primary origin.
                $chain = $shadowMatrix;

            } else {
                // Absolute Void Exception: No tangible coordinates found.

                $fallback = [
                    'file'     => 'unknown',
                    'line'     => 0,
                    'function' => null,
                    'class'    => null,
                    'short'    => 'unknown',
                    // 'captured' => false,
                ];

                return [
                    'primary' => $fallback,
                    'chain'   => [$fallback],
                ];
            }
        }

        return [
            'primary' => $chain[0], // Nearest non-engine frame = The Exact Origin
            'chain'   => $chain,    // Outward → Inward user/app frames
        ];
    }

    /**
     * KarAgah enters the armory ⚡ — telemetry charged, trace depth tuned, and the hunt matrix brought to life.
     * He takes his orders once 🕵️, loads the chain limits, and wraps the declared namespaces beneath the Phantom cloak.
     * No clearance, no hunt 🥷: the chamber stays dark; granted clearance turns every descent into an origin interrogation.
     * Then the Inspector moves 🔥 — armed with evidence, blind to noise, and hunting the caller behind the call.
     * 
     * ⚙️ Lazyly hydrates config. Hits `config()` AT MOST once per process.
    */
    protected static function ammunitize(?bool $forced = null): bool
    {
        static $booted = false;

        // @Todo: Sixth Sense WarnSignal !
        if($forced !== null) {
            $booted = false;
        }

        // Early Exit to Cached entry.
        if ($booted) {
            return self::$recordOrigins;
        }

        if ($forced === null && !function_exists('config')) {
            return self::$recordOrigins; // Config provider offline, relying on core defaults
        }

        // listen to orders from config
        $booted = true;
        
        $config_prefix = 'krubot.extensions.inspect-hook-origins.';

        // Main Breaker ⚡: Arm or disarm the telemetry matrix for the entire process.
        self::$recordOrigins   = (bool) config($config_prefix.'enabled', self::$recordOrigins);

        if($forced !== null && self::$recordOrigins != $forced) {

            if(function_exists('config'))
                \config([$config_prefix.'enabled' => $forced]); // Update Entry in RAM (Not Config File)

            self::$recordOrigins = $forced;
        }

        // Fast-path Exit
        if(!self::$recordOrigins)
            return false;
        
        // Rabbit Hole 🕳️: Maximum depth the Detective will dive into the raw stack trace.
        self::$originTraceDepth = (int) config($config_prefix.'backtrace-depth', self::$originTraceDepth);

        // Memory Shield 🛑: Hard limit on external origin frames captured per synapse.
        self::$originChainLimit = (int) config($config_prefix.'chain-limit', self::$originChainLimit);

        if(self::$originTraceDepth < 1 || self::$originChainLimit < 1) {
            self::$recordOrigins   = false;
            return false;
        }

        // Phantom Protocols 🥷: Merge user-defined stealth zones (e.g., 'KrubiK') with the base engine cloak.
        $customStealth = config($config_prefix.'stealth-namespaces');
        if (is_array($customStealth)) {
            self::$stealthNamespaces = array_merge(self::$stealthNamespaces, $customStealth);
        }

        return true;

    }

    /**
     * 🧠 Build the Intel Skip Index ONCE.
     * Uses hybrid strategy: Engine Root Heuristic (Config-Driven) + Phantom Namespaces.
    */
    protected function classifyBaseEvidences(): array
    {
        if ($this->matrixTopology !== null) {
            return $this->matrixTopology;
        }

        $basenames = [];
        $dirs      = [];
        $phantom_dirs = []; // Start empty!

        // 🥷 Prepare The Phantoms (Stealth Namespaces from Config)
        $stealth = array_map(fn($ns) => rtrim($ns, '\\') . '\\', self::$stealthNamespaces);

        // 🚀 THE ENGINE ROOT HEURISTIC (Conditioned by Config)
        $nsParts = explode('\\', __NAMESPACE__);
        $baseNs  = $nsParts[0] . '\\'; // E.g., 'KrubiK\'

        // ⚡ ONLY arm the root directory if the config explicitly demands it!
        if (in_array($baseNs, $stealth, true)) {
            $engineRoot = str_replace('\\', '/', __DIR__);
            while (
                basename($engineRoot) !== 'src' && 
                basename($engineRoot) !== rtrim($baseNs, '\\') && 
                strlen($engineRoot) > 3
            ) {
                $parent = dirname($engineRoot);
                if ($parent === $engineRoot) break;
                $engineRoot = $parent;
            }
            // Add the engine root to phantoms dynamically
            $phantom_dirs[] = $engineRoot . '/';
        }

        // 1. Map the Local Extensions Directory seamlessly
        $extensionsDir = __DIR__;
        if (is_dir($extensionsDir)) {
            $dirs[] = str_replace('\\', '/', $extensionsDir) . '/';

            // SCANDIR_SORT_NONE for sheer speed.
            $entries = @scandir($extensionsDir, SCANDIR_SORT_NONE) ?: [];
            foreach ($entries as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                if (str_ends_with($entry, '.php')) {
                    $basenames[$entry] = true; 
                }
            }
        }

        // DYNAMIC HOST RESOLUTION ⚡ (FullStack De Ver)
        // Dynamically binds the target Host Class by evaluating the injected parameter (`$this->resolvedHostClass`).
        // Accepts either an instantiated object or a raw FQCN string passed to the constructor.
        // This cleanly decouples the discovery logic from Late Static Binding (`self::class`),
        // providing flawless O(1) component selection without the performance tax of Reflection.
        $hostClass = $this->resolvedHostClass;
        $short = strrchr($hostClass, '\\');
        $jack  = ($short === false ? $hostClass : substr($short, 1)) . '.php';
        $basenames[$jack] = true;

        // 3. Cement this specific Trait file
        $basenames[basename(__FILE__)] = true;
        $dirs[] = str_replace('\\', '/', dirname(__FILE__)) . '/';

        // ⚠️ THE INFORMANT DIRECTIVE: Ensure BlackWire is NEVER hidden, 
        // even if it was caught in the scandir net above.
        unset($basenames['BlackWire.php']);

        // 🕳️ Prepare The Void
        $void = array_map(fn($ns) => rtrim($ns, '\\') . '\\', self::$voidNamespaces);

        $this->matrixTopology = [
            'booted'       => true,
            'basenames'    => $basenames,
            'dirs'         => array_values(array_unique($dirs)),
            'self'         => $hostClass,
            'ns'           => __NAMESPACE__ . '\\',
            
            // Static Target Signatures
            'void'         => $void,
            'stealth'      => $stealth, 
            
            // 🧠 RAM Allocation for Dynamic Grid Coordinates (O(1) memory buckets)
            'void_dirs'    => [],
            'phantom_dirs' => $phantom_dirs, // Will contain 'src/' ONLY if config allowed it
        ];

        return $this->matrixTopology;
    }

    /**
     * Skill 🧠: Engram Burn (Remember Grid Coordinates)
     * Pins 📌 a newly discovered engine territory into the process-local neural index.
     * Assigns the isolated directory to its designated Matrix sector (Void or Phantom).
     * Utilizes zero-copy reference passing (&$originAtlas) to prevent RAM hemorrhaging 
     * during high-frequency telemetry mutations.
     * 
     * @param string $normalizedFile The raw physical trajectory of the frame.
     * @param array &$originAtlas The mutable memory grid containing known sector coordinates.
     * @param string $matrixSector The target dimension (e.g., 'dirs', 'void_dirs', 'phantom_dirs').
    */
    protected function rememberGridCoordinates(string $normalizedFile, array &$originAtlas, string $matrixSector): void
    {
        $slash = strrpos($normalizedFile, '/');
        if ($slash === false) {
            return;
        }

        $dir = substr($normalizedFile, 0, $slash + 1);

        // Scan the neural index: Ensure we don't double-register a known sector coordinate
        foreach ($originAtlas[$matrixSector] as $known) {
            if ($known === $dir) {
                return;
            }
        }
        
        $originAtlas[$matrixSector][] = $dir;
        $this->matrixTopology = $originAtlas; // Commit the new grid coordinates to the matrix topology (instance memory)
    }

    /**
     * Skill 🕵️‍♂️: Interrogate Enigma (The Matrix Decryption)
     * The Detective pierces through the telemetry noise to classify a stack frame anomaly.
     * Deploys O(1) photographic engram lookups to bypass redundant deep-matrix scans.
     * Accurately determines if the entity belongs to the Target HUD, the Phantom Layer, or the Absolute Void.
     *
     * ──────────────────────────────────────────────────────────────────────────
     * ⚠️  Caller contract (PHP backtrace semantics):
     *
     *   For NORMAL frames (file != ''):
     *     • Pass $class = NULL.
     *     • Reason: frame[N].class is the CALLEE's namespace (the function that was
     *       *called from* this file), NOT the namespace that owns this file.
     *       Trusting it would mis-classify a user-land file that happens to call a
     *       KrubiK method as PHANTOM — the exact bug this fix addresses.
     *     • Classification is therefore purely file-path-based, which is always correct.
     *     • $phantomClass (optional) lets a caller hint "I know the file resolved from
     *       this class — use the class only for sector-learning, not for the primary
     *       decision".  Used by ghost-frame resolution paths.
     *
     *   For GHOST frames (file == ''):
     *     • The ghost-frame branch resolves the file via ReflectionClass and then calls
     *       investigateEnigma($resolvedFile, null, $originAtlas) as a normal frame would.
     *       Pass the class as $phantomClass so the engine can learn the directory sector
     *       even before the dir cache is warm.
     * ──────────────────────────────────────────────────────────────────────────
     *
     * @param string      $file         The physical file path of the frame being examined.
     * @param string|null $class        MUST BE NULL for normal frames (see above).
     *                                  Pass the actual class only for ghost-frame paths
     *                                  where the file was *resolved from* that class.
     * @param array       &$originAtlas The mutable memory grid for coordinate caching.
     * @param string|null $phantomClass Optional class hint used solely for sector-learning
     *                                  when $class is null (normal frame context).
     * @return int self::SECTOR_* (TARGET, PHANTOM, or VOID)
    */
    protected function investigateEnigma(string $file, ?string $class, array &$originAtlas, ?string $phantomClass = null): int
    {
        $base = basename($file);
        
        // 🚨 CRITICAL OVERRIDE: The Informant Directive: BlackWire always bypasses the Void.
        if ($base === 'BlackWire.php')
            return self::SECTOR_TARGET; 

        $norm = str_replace('\\', '/', $file);

        // 1. Core Engine Check -> Absolute Void
        foreach ($originAtlas['dirs'] as $dir) {
            if ($dir !== '' && str_starts_with($norm, $dir))
                return self::SECTOR_VOID; // Matched a known internal-engine zone
        }
        
        // 2. Cached Void Coordinates -> Absolute Void
        foreach ($originAtlas['void_dirs'] as $dir) {
            if ($dir !== '' && str_starts_with($norm, $dir))
                return self::SECTOR_VOID;
        }

        // 3. Cached Phantom Coordinates -> Stealth Layer
        foreach ($originAtlas['phantom_dirs'] as $dir) {
            if ($dir !== '' && str_starts_with($norm, $dir))
                return self::SECTOR_PHANTOM;
        }

        // 🕳️ Match Absolute Void Entities
        // 🛡️ ANTI-PARADOX SHIELD: Prevent Closure Context Poisoning!
        // Check the physical path structure BEFORE trusting any class hint.
        // (e.g., Laravel's Container executing a KrubiK Closure).
        foreach ($originAtlas['void'] as $voidNs) {
            $bareVoid = rtrim($voidNs, '\\');
            // Path-structure hint (e.g., '/Illuminate/') — always reliable
            if (str_contains($norm, '/' . $bareVoid . '/')) {
                $this->rememberGridCoordinates($norm, $originAtlas, 'void_dirs');
                return self::SECTOR_VOID;
            }
            // Class hint — only trust when it was explicitly passed as $class
            // (ghost-frame context where the file truly belongs to that class).
            // Never use the CALLEE class from a normal frame for this decision.
            if ($class !== null && str_starts_with($class, $voidNs)) {
                $this->rememberGridCoordinates($norm, $originAtlas, 'void_dirs');
                return self::SECTOR_VOID;
            }
        }

        // Deep Matrix Scan: Resolving unknown signatures
        // Use $class when provided (ghost-frame), else fall back to $phantomClass hint.
        $effectiveClass = $class ?? $phantomClass;

        if ($effectiveClass !== null) {

            // Internal Identity -> Burn coordinate to Void 
            // (فقط خودِ کلاس کارآگاه و فضای نام مستقیمش را نادیده می‌گیرد تا لوپ بی‌نهایت نشود)
            if ($effectiveClass === $originAtlas['self'] || str_starts_with($effectiveClass, $originAtlas['ns'])) {
                $this->rememberGridCoordinates($norm, $originAtlas, 'dirs'); // Learn this new base for future speed
                return self::SECTOR_VOID;
            }

            // 🥷 Match Phantom Protocol Signatures (کاملاً مطیع کانفیگ)
            foreach ($originAtlas['stealth'] as $stealthNs) {
                if (str_starts_with($effectiveClass, $stealthNs)) {
                    $this->rememberGridCoordinates($norm, $originAtlas, 'phantom_dirs'); // Learn vendor directory to lock it down fast next time
                    return self::SECTOR_PHANTOM;
                }
            }

        }

        // Fast-path Fallback: Known baseline signatures -> Void
        // Basename Fallback (O(1) Bump-Key)
        if (isset($originAtlas['basenames'][$base])) {
            $this->rememberGridCoordinates($norm, $originAtlas, 'dirs'); // Pin exact path upon first sighting
            return self::SECTOR_VOID;
        }

        // Entity is cleared: Return as an external Target
        return self::SECTOR_TARGET;
    }

    /**
     * 🪦 Shared Sentinel Payload.
     * Zero-allocation return matrix for when the system is running hot (disabled tracking).
     *
     * @return array{
     *     primary: array{file: string, line: int, short: string, function: ?string, class: ?string, captured: bool},
     *     chain: array{}
     * }
    */
    protected function originDisabledSentinel(): array
    {
        return $this->originDisabledSentinel ??= [
            'primary' => [
                'file'     => 'disabled',
                'line'     => 0,
                'function' => null,
                'class'    => null,
                'short'    => 'disabled',
                'captured' => false,
            ],
            'chain' => [],
        ];
    }

    private static array $cyber_signatures = [
        '#MechaDrive',     // 🦾 Heavy robotic integration
        '#ArmorPatch',     // 🛡️ Fortified defense code
        '#DarkSynapse',    // 🧠 Organic-machine connection
        '#CoreReactor',    // ☢️ Deep engine power source
        '#SteelDirective', // 📜 Immutable machine law
        '#Injection'       // 💉 Clinical code insertion
    ];

    /**
     * 🎲 The Forge of Chaos
     * Randomly assigns a dark-industrial cyber signature to user-land plugins.
     * Makes the engine feel alive, unpredictable, and raw.
     *
     * @return string
    */
    protected static function DigOutCyberSignature(?string $file_name = null): string
    {

        if(!empty($file_name)) {
            if(str_ends_with($file_name, 'index.php'))
                return '@MainGateway::';
        }

        // Let the *Universe* ghost in the machine pick one at random
        return self::$cyber_signatures[array_rand(self::$cyber_signatures)];
    }

    /**
     * ② isKnownSignature — O(1) با flip cache
     *
     * @param string $payload
     * @return bool
    */
    public static function isKnownSignature(string $payload): bool
    {
        // $normalizedEntry = str_starts_with($payload, '#') ? $payload : '#' . $payload;
        // return in_array($normalizedEntry, self::$cyber_signatures, true);

        static $map = null;
        if ($map === null) {
            $map = array_flip(self::$cyber_signatures);
        }

        return isset($map[$payload]);
    }

    /**
     * 👁️‍🗨️ The Function Signature Decryptor
     *
     * A specialized helper to translate cryptic, low-level PHP runtime signatures
     * into meaningful, context-aware labels for the Detective's HUD.
     * This ensures the final report is not just data, but intelligence.
     *
     * @param string|null $signature The raw 'function' field from a backtrace frame.
     * @return string|null The decrypted, DX-friendly signature.
    */
    protected static function decryptFunctionSignature(?string $signature, ?string $file_name = null): ?string
    {
        if ($signature === null) {
            return null;
        }

        // Rule 1: Mirror Known Values
        if (self::isKnownSignature($signature)) {
            return $signature;
        }

        // Rule 2: A wild Closure appears! Tag it as an inline, untraceable code block.
        if (str_ends_with($signature, '{closure}')) {
            return '{[Inline_Closure]}';
        }

        // Rule 3: 'require_*'/'include_*' is some of high-level indicators of loading a module/plugin.
        if ($signature === 'require_once' || $signature === 'include_once'
            || $signature === 'require' || $signature === 'include') {
            return self::DigOutCyberSignature($file_name);
        }

        return $signature;
    }

    /**
     * 📊 Full synapse map for one event, with registration coordinates.
     * Dispatch path never calls this — HUD / dd() / diagnostics only.
     *
     * public function traceListeners(string $event): array;
    */

    /**
     * 🧠 Exegesis: The Detective's Data Decoder
     * 
     * Formats a raw list of synapses (fragments) into a clean, DX-friendly origin map.
     * Pure static function: Stateless, O(N) linear projection.
     * Perfect for Snapshot matrices and HUD integrations.
     *
     * @param list<array> $fragments 
     * @return list<array{
     *     id: mixed,
     *     priority: mixed,
     *     file: string,
     *     line: int,
     *     short: string,
     *     function: ?string,
     *     class: ?string,
     *     chain: list<array{file: string, line: int, short: string, function: ?string, class: ?string}>
     * }>
    */
    public static function exegesis(array $fragments): array
    {
        $out = [];

        foreach ($fragments as $key => $fragment) {

            if (!is_array($fragment)) {
                continue;
            }

            /// [$priority, $id, $listener, $origin] = $fragment + [null, null, null, null];
            /// unset($listener); // closures are not DX, they're a memory dump waiting to happen

            // Upgrade: Polymorphic Anatomy Recognition ⚡ (Zero-Manual-Alloc Strategy)
            $priority = null;
            $id       = null;
            $origin   = null;

            if (isset($fragment['origin']) || array_key_exists('origin', $fragment)) {
                // Shape 1: Once-Maps [ $id => ['wrapper' => ..., 'origin' => ...] ]
                // Here, the array key itself is the true ID.
                $id       = $key; 
                $origin   = $fragment['origin'];
            } elseif (array_key_exists(3, $fragment)) {
                // Shape 2: Standard Event/Pipe [priority, id, callable, origin]
                // Closures are intentionally ignored here; no memory dumps waiting to happen.
                $priority = $fragment[0] ?? null;
                $id       = $fragment[1] ?? null;
                $origin   = $fragment[3] ?? null;
            } else {
                // Shape 3: Parameter Type/Name Injectors [priority, resolver, origin]
                $priority = $fragment[0] ?? null;
                $origin   = $fragment[2] ?? null;
            }
            $primary = null;
            $chain   = [];

            if (is_array($origin) && isset($origin['primary']) && is_array($origin['primary'])) {
                $primary = $origin['primary'];
                $chain   = $origin['chain'] ?? [];
            } elseif (is_array($origin) && isset($origin['file'])) {
                // v1 payload backward compatibility — single frame, no chain wrapper
                $primary = $origin;
                $chain   = [$origin];
            }

            $file = is_array($primary) ? ($primary['file'] ?? 'unknown') : 'unknown';
            $line = is_array($primary) ? (int) ($primary['line'] ?? 0) : 0;

            
            // استخراج فانکشن برای بازجویی
            $function = is_array($primary) ? ($primary['function'] ?? null) : null;
            // ⚡ بررسی فوق سریع O(1) با استفاده از کش داخلی isKnownSignature
            $isCyberSignature = $function !== null && self::isKnownSignature($function);

            $out[] = [
                'id'       => $id,
                'priority' => $priority,
                'file'     => $file,
                'line'     => $line,
                'short'    => is_array($primary)
                    ? ($primary['short'] ?? (basename((string) $file) . ':' . $line))
                    : 'unknown',
                'function' => $function,
                'class'    => is_array($primary) ? ($primary['class'] ?? null) : null,
                'chain'    => is_array($chain) ? $chain : [],
                // 'captured' => is_array($primary) ? (bool) ($primary['captured'] ?? true) : false,
            ];
        }

        return $out;
    }

    /**
     * 🧩 The Matrix Decoder (Alias for exegesis)
     * 
     * Syntactic sugar for `exegesis()`. Derived from the native query "What is this?".
     * Feeds raw, entangled synapse fragments into the Detective's decoder ring.
     * It strips away the framework noise and projects a clean, human-readable O(N) map 
     * of the exact execution coordinates for the cockpit HUD.
     * Usage: KarAgah::chist($inan) // Means "What is this, Detective?"
     * 
     * @param list<array> $inan Raw event/hook synapses harvested from the core.
     * @return list<array{
     *     id: mixed,
     *     priority: mixed,
     *     file: string,
     *     line: int,
     *     short: string,
     *     function: ?string,
     *     class: ?string,
     *     chain: list<array{file: string, line: int, short: string, function: ?string, class: ?string}>
     * }> Decoded and purified origin map.
    */
    public static function chist(array $inan): array
    {
        return self::exegesis($inan);
    }
}
