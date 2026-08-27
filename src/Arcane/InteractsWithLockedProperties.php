<?php
declare(strict_types=1);

namespace KrubiK\Arcane;
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
use ReflectionProperty;
use ReflectionMethod;
use ReflectionException;
use RuntimeException;
use Error;
use Throwable;
use Exception;
use WeakMap;
use FFI; // Say Hello to C++, [FFI Is a Fine ToolBox For Hypnosis PHP-Mind], brought to you by your RebelArchitect: Prometheus-K...

/**
 * InteractsWithLockedProperties - v17.0 (The Phantom Metaprogramming Tool)
 *
 *                 Welcome To:
 *          The Heart of PhantomShell 🫀 🦹
 * 
 *                  ::: Inspired On :::
 *      📻️ Infected Mushroom - Manipulator 🎧️
 * https://open.spotify.com/track/0FSxuVEj1lse3hvLtrItYN
 * https://soundcloud.com/InfectedMushroom/Manipulator
 *    (Listen When you Reveal System-Hypnotist Secrets)
 * 
 * COMBINED POWERS:
 * 1. Logic from v16 (EG Scanning) to bypass "Unsupported argument type".
 * 2. Offsets from PHP 8.2 Source-Code for precise flag manipulation.
 * 3. Logging from PhantomShell v13.1 for detailed forensics.
 * 
 *                  ::: Primary Inspiration Source :::
 * https://github.com/cleolibrary/CLEO5/blob/master/cleo_plugins/MemoryOperations/MemoryOperations.cpp
 * 
 * CAPABILITIES:
 * - Enforce Advanced Synaptic-Code Mutations via:
 * - `readonly` Bypass   (QuntumTunnel /=> FFI Memory Patching)
 * - `final`    Bypass   (BreachApex   /=> CE Flags)
 * - Visibility Bypass   (`private`, `protected` /=> Reflection)
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait InteractsWithLockedProperties
{
    // =========================================================================
    // ⚙️ CONSTANTS & CONFIG (The Rosetta Stone)
    // =========================================================================
    
    // PHP 8.2+ x64 Offsets (Carefully Crafted Based-On "https://github.com/php/php-src/tree/PHP-8.2.29/Zend")
    private const OFFSET_PROP_FLAGS = 40;  // Offset of 'flags' in zend_property_info
    private const OFFSET_CLASS_FLAGS = 28; // Offset of 'ce_flags' in zend_class_entry
    private const ZEND_ACC_READONLY = 0x80;
    private const ZEND_ACC_FINAL = 0x20;

    // =========================================================================
    // 🧠 MEMORY CORE (The Metaprogramming Engine)
    // =========================================================================

    private static mixed $ffi = null;
    private static bool $ffiInitialized = false;
    private static ?int $objectBucketsAddr = null; // The Holy Grail: Address of objects_store.object_buckets
    private static int $ptrSize = 8; // Default to x64

    /** @var array PhantomShell BlackBox */
    private array $traceLog = [];

    /**
     * The Agency's Central Database (Upgraded to WeakMap For Max Performance).
     * Stores locks, cache, and context for each object instance.
     * Automatically cleans up when the object is destroyed.
     * 
     * @var WeakMap|null
     */
    private static ?WeakMap $_agencyCache = null;

    /**
     * The Spy's current "Safehouse" or mission target.
     * اگر null باشد، عامل در حالت پیش‌فرض "نفوذ به والد" عمل می‌کند.
     * @var ?object
    */
    protected ?object $_spyTarget = null;

    /**
     * Internal Logging Mechanism
     * The BlackBox Recorder Method
    */
    private function _log(string $msg): void
    {
        $time = microtime(true);
        $micro = sprintf("%06d", ($time - floor($time)) * 1000000);
        $date = date("H:i:s");
        $log_entry = "[$date.$micro] $msg";
        $this->traceLog[] = $log_entry;
    }

    public function getQuantumLog(): array
    {
        return $this->traceLog;
    }

    // =========================================================================
    // 🏗️ INITIALIZATION & INTERNAL HELPERS (Zero-Config Ignition)
    // =========================================================================

    /**
     * [INTERNAL] Initializes the Agency Cache lazily for the exact target object.
     * Replaces the old array-based registry system with a memory-safe WeakMap.
    */
    private function _ensureAgencyReadyFor(object $target): void
    {
        self::$_agencyCache ??= new WeakMap();

        if (!isset(self::$_agencyCache[$target])) {
           self::$_agencyCache[$target] = [
                'unlocked' => [],
                'props'    => [], // ReflectionProperty Cache
                'methods'  => [], // ReflectionMethod Cache
           ];
        }
    }
    /**
     * Backward-compat helper where original code expected no argument.
     * It still prepares cache for $this or spyTarget. // oldName: initQuantumSpyAgency()
    */
    private function _ensureAgencyReady(): void
    {
        self::$_agencyCache ??= new WeakMap();

        $target = $this->_spyTarget ?? $this;        
        // Ensure the current object (or spy target) has a dossier in the cache
        $this->_ensureAgencyReadyFor($target);
    }

    /**
     * Initializes FFI and scans memory to find the Object Store.
     * This is the "Metaprogramming" search phase.
    */
    private function _initializeFFISuite(): void
    {
        if (self::$ffiInitialized) return;

        $this->_log("INIT: Starting FFI Suite initialization...");

        if (!extension_loaded('ffi') || !class_exists('FFI')) {
            $this->_log("FAIL: FFI extension missing.");
            self::$ffiInitialized = true;
            return;
        }

        try {
            // Define types and access to executor_globals (Weaponize The PHP Brain)
            self::$ffi = FFI::cdef('
                typedef uint32_t uint32;
                typedef unsigned long long size_t;
                typedef size_t uintptr_t;
                extern char executor_globals[]; 
            ');
            
            self::$ptrSize = PHP_INT_SIZE;
            $this->_log("SUCCESS: FFI CDef loaded. PtrSize: " . self::$ptrSize);

            // Trigger the memory scanner immediately to cache the bucket address of running env.
            $this->_locateObjectStore();

        } catch (Throwable $e) {
            $this->_log("CRITICAL INIT FAIL: " . $e->getMessage());
            self::$ffi = null;
        }

        self::$ffiInitialized = true;
    }

    // =========================================================================
    // 🛠️ HELPER METHODS (Memory IO)
    // =========================================================================

    // ... Here Comes `MemoryOperations.php` ...

    private function _readPtr(int $addr): int {
        if ($addr === 0) return 0;
        try {
            $ptr = self::$ffi->cast('uintptr_t*', self::$ffi->cast('void*', $addr));
            return $ptr[0];
        } catch (Throwable) { return 0; }
    }

    private function _readInt32(int $addr): int {
        if ($addr === 0) return 0;
        try {
            $ptr = self::$ffi->cast('uint32*', self::$ffi->cast('void*', $addr));
            return $ptr[0];
        } catch (Throwable) { return 0; }
    }

    private function _writeInt32(int $addr, int $val): void {
        if ($addr === 0) return;
        try {
            $ptr = self::$ffi->cast('uint32*', self::$ffi->cast('void*', $addr));
            $ptr[0] = $val;
        } catch (Throwable) { }
    }

    private function _scanMemoryForVal(int $startAddr, int $searchVal, int $limitBytes): bool {
        for ($i = 0; $i < $limitBytes; $i += self::$ptrSize) {
            $val = (self::$ptrSize === 8) ? $this->_readPtr($startAddr + $i) : $this->_readInt32($startAddr + $i);
            if ($val === $searchVal) return true;
        }
        return false;
    }

    /**
     * Resolves the memory address of ANY PHP object using its Handle ID.
    */
    private function _addr(object $obj): int
    {
        // Lazy Load FFI Suite
        if (!self::$ffiInitialized) $this->_initializeFFISuite();

        if (self::$objectBucketsAddr === null) {
            $this->_log("ADDR FAIL: Object Store not located.");
            return 0;
        }

        $id = spl_object_id($obj);
        $ptrLoc = self::$objectBucketsAddr + ($id * self::$ptrSize);
        $addr = $this->_readPtr($ptrLoc);
        
        // $this->_log("ADDR RES: ID $id -> " . dechex($addr));
        return $addr;
    }


    /**
     * Scans Executor Globals to find the `objects_store.object_buckets` pointer.
     * This bypasses the need for FFI::addr($obj).
    */
    private function _locateObjectStore(): void
    {
        if (self::$objectBucketsAddr !== null) return;

        $this->_log("SCANNER: Searching for Object Store in EG...");
        
        // 1. Create a Marker Object with a unique signature
        $markerSig = (self::$ptrSize === 8) ? 0x1122334455667788 : 0x11223344; // x64 vs x86
        $marker = new class($markerSig) {
            public int $sig;
            public function __construct(int $v) { $this->sig = $v; }
        };
        $markerId = spl_object_id($marker);
        
        $this->_log("SCANNER: Marker created. ID: $markerId. Sig: " . dechex($markerSig));

        try {
            // Inspect EG Address
            $egAddr = FFI::cast('uintptr_t', FFI::addr(self::$ffi->executor_globals))->cdata;
            
            // Scan the first 2KB of EG to find a pointer array
            // Logic: objects_store is near the start of EG. It contains a pointer (object_buckets).
            // object_buckets[markerId] MUST point to our Marker Object.
            // Marker Object MUST contain markerSig.
            
            for ($offset = 0; $offset < 2048; $offset += self::$ptrSize) {
                // Read potential bucket array pointer
                $candidateBuckets = $this->_readPtr($egAddr + $offset);
                
                if ($candidateBuckets === 0 || ($candidateBuckets % self::$ptrSize !== 0)) continue;

                // Calculate where our marker should be
                $markerPtrLoc = $candidateBuckets + ($markerId * self::$ptrSize);
                $markerObjAddr = $this->_readPtr($markerPtrLoc);

                if ($markerObjAddr === 0) continue;

                // Verification: Peek inside the object to find the signature
                // The sig should be within the first 128 bytes of the object
                if ($this->_scanMemoryForVal($markerObjAddr, $markerSig, 128)) {
                    $this->_log("EUREKA: Object Store found at EG offset +$offset!");
                    $this->_log("STORE ADDR: " . dechex($candidateBuckets));
                    self::$objectBucketsAddr = $candidateBuckets;
                    return;
                }
            }
            
            $this->_log("SCANNER FAIL: Could not locate object store.");

        } catch (Throwable $e) {
            $this->_log("SCANNER ERROR: " . $e->getMessage());
        }
    }

    // =========================================================================
    // 🧬 QUANTUM TUNNEL (The Readonly Bypass)
    // =========================================================================

    /**
     * Removes the 'readonly' flag from a property via memory manipulation.
    */
    protected function quantumTunnel(string $propertyName, object $target): bool
    {
        $this->_log("TUNNEL: Starting sequence for property [$propertyName]...");
        
        $objAddr = $this->_addr($target);
        if ($objAddr === 0) return false;

        try {
            $refProp = new ReflectionProperty($target, $propertyName);
            $refProp->getName(); // Force initialization

            // 1. Get Address of ReflectionProperty Object
            $refAddr = $this->_addr($refProp);
            if ($refAddr === 0) {
                 $this->_log("TUNNEL FAIL: Cannot get Reflection address.");
                 return false;
            }

            // 2. Extract internal zend_property_info pointer
            // In PHP 8.2 x64, it is typically at offset 56 (0x38) or 48 (0x30).
            // We use a smart probe.
            $propInfoAddr = 0;
            $candidates = [56, 48, 64, 32]; 
            
            foreach ($candidates as $offset) {
                $ptr = $this->_readPtr($refAddr + $offset);
                if ($ptr > 0x10000) { // Basic sanity check
                     // Check if flags at offset 40 match expected READONLY
                     $flags = $this->_readInt32($ptr + self::OFFSET_PROP_FLAGS);
                     if (($flags & self::ZEND_ACC_READONLY) !== 0) {
                         $propInfoAddr = $ptr;
                         $this->_log("TUNNEL: Found PropInfo at Reflection offset +$offset");
                         break;
                     }
                }
            }

            if ($propInfoAddr === 0) {
                $this->_log("TUNNEL FAIL: Could not locate zend_property_info.");
                return false;
            }

            // 3. Mutate Flags
            $flagsLoc = $propInfoAddr + self::OFFSET_PROP_FLAGS;
            $currentFlags = $this->_readInt32($flagsLoc);
            
            $this->_log("TUNNEL: Current Flags: " . dechex($currentFlags));

            $newFlags = $currentFlags & ~self::ZEND_ACC_READONLY;
            $this->_writeInt32($flagsLoc, $newFlags); // ⚡️

            $this->_log("TUNNEL: Flags patched to: " . dechex($newFlags));
            
            // Verification
            if (($this->_readInt32($flagsLoc) & self::ZEND_ACC_READONLY) === 0) {
                $this->_log("TUNNEL SUCCESS: ؛٪; QUANTUMTUNNEL ESTABLISHED ؛٪; Readonly shield disabled. 🌂");
                return true;
            }

        } catch (Throwable $e) {
            $this->_log("TUNNEL EXCEPTION: " . $e->getMessage());
        }

        return false;
    }

    // =========================================================================
    // 🧬 BREACH APEX (🔓Destiny Rewrite & The Synaptic Apex Mutagen)
    // =========================================================================

    /**
     * Executes a surgical Bio-Cybernetic intrusion into the Zend class entry. (Hunt The FinalBoss ; The `Final Bypass` 🔓)
    */
    public function breachApex(string|object $classOrObject): static
    {
        $this->_log("DEFINALIZE: Initiated.");
        
        try {
            $ref = ($classOrObject instanceof ReflectionClass) 
                ? $classOrObject 
                : new ReflectionClass($classOrObject);

            if (!$ref->isFinal()) return $this;

            $refAddr = $this->_addr($ref);
            if ($refAddr === 0) {
                // Try fallback: Get CE directly from object if it's an instance
                if (is_object($classOrObject) && !($classOrObject instanceof ReflectionClass)) {
                    $objAddr = $this->_addr($classOrObject);
                    // zend_object->ce is usually at offset 8 (x86) or 16 (x64)
                    $ceOffset = (self::$ptrSize === 8) ? 16 : 8;
                    $ceAddr = $this->_readPtr($objAddr + $ceOffset);
                } else {
                    $this->_log("DEFINALIZE FAIL: No memory handle.");
                    return $this;
                }
            } else {
                 // Get CE from ReflectionClass (internal pointer usually at 56 or 48)
                 $ceAddr = $this->_readPtr($refAddr + 56);
                 if ($ceAddr === 0) $ceAddr = $this->_readPtr($refAddr + 48);
            }

            if ($ceAddr === 0) {
                $this->_log("DEFINALIZE FAIL: Could not find ClassEntry address.");
                return $this;
            }

            // Patch ce_flags
            $flagsLoc = $ceAddr + self::OFFSET_CLASS_FLAGS; // 28
            $currentFlags = $this->_readInt32($flagsLoc);
            
            if (($currentFlags & self::ZEND_ACC_FINAL) !== 0) { // Is It `final` ?
                $this->_writeInt32($flagsLoc, $currentFlags & ~self::ZEND_ACC_FINAL); // ⛈️
                                                //  ...it] Was [it...
                $this->_log("DEFINALIZE SUCCESS: 'final' keyword removed.");
            }

        } catch (Throwable $e) {
            $this->_log("DEFINALIZE ERROR: " . $e->getMessage());
        }

        return $this;
    }

    // =========================================================================
    //                    🎮 PUBLIC API (PhantomShell Controls)
    // =========================================================================

    // =========================================================================
    // 🕵️ SPY MISSION CONTROL (کنترل مأموریت جاسوسی)
    // =========================================================================

    /**
     * Assigns a new mission and returns the target object itself, enabling direct chaining on it.
     * یک مأموریت جدید تعریف کرده و خود آبجکت هدف را برمی‌گرداند تا امکان زنجیره‌سازی مستقیم روی آن فراهم شود.
     *
     * @param object $target The object our spy should now interact with.
     * @return object The target object, ready for chaining.
    */
    public function setSpyTarget(object $target): object
    {
        $this->_spyTarget = $target;
        $this->_ensureAgencyReadyFor($target); // Register the new target immediately
        return $this->_spyTarget;
    }

    /**
     * Aborts the current special mission, recalling the spy to its default post [$this].
     * مأموریت ویژه فعلی را لغو کرده و جاسوس را به پست پیش‌فرض خود بازمی‌گرداند.
     *
     * @return static Returns the agent instance itself for chaining.
    */
    public function clearSpyTarget(): static
    {
        $this->_spyTarget = null;
        return $this;
    }

    // =========================================================================
    // 🛡️ GUARD SYSTEM
    // =========================================================================

    /**
     * Unlocks one or more private/protected props for the CURRENT CONTEXT.
     * @param string ...$props Use '*' to unlock all.
     * @return static
    */
    public function unlock(string ...$props): static
    {
        $target = $this->_spyTarget ?? $this;
        $this->_ensureAgencyReadyFor($target);
        $dossier = &self::$_agencyCache[$target]; // assign-by-ref, to easy-coding

        foreach ($props as $property) {
            if (!in_array($property, $dossier['unlocked'], true)) {
                $dossier['unlocked'][] = $property;
            }
        }
        return $this;
    }

    /**
     * Re-locks one or more props for the CURRENT CONTEXT.
     * @return static
    */
    public function lock(string ...$props): static
    {
        $target = $this->_spyTarget ?? $this;        
        $this->_ensureAgencyReadyFor($target);
        // if (!isset(self::$_agencyCache[$target])) return $this;
        $dossier = &self::$_agencyCache[$target]; // assign-by-ref, to easy-coding, while memory optimizations

        if (in_array('*', $props, true)) {
            $dossier['unlocked'] = []; // Global Lock
            return $this;
        }

        $dossier['unlocked'] = array_diff($dossier['unlocked'], $props);
        return $this;
    }

    /**
     * Checks if a property is currently locked. // iNITIATES Lawely ChaOS
    */
    public function isLocked(string $property, ?object $targetObject = null): bool
    {
        $finalTarget = $targetObject ?? $this->_spyTarget ?? $this;
        // Ensure cache exists (Silent check)
        $this->_ensureAgencyReadyFor($finalTarget); // Just to be safe, though usually handled by callers

        $unlockedList = self::$_agencyCache[$finalTarget]['unlocked'] ?? [];

        if (in_array('*', $unlockedList, true) || in_array($property, $unlockedList, true)) {
            return false;
        }

        // Default Deny Protocol: Is it private/protected?
        try {
            $reflection = $this->getReflectionProperty($property, $finalTarget);
            if ($reflection && ($reflection->isPrivate() || $reflection->isProtected())) {
                return true;
            }
        } catch (Throwable) {
            return true;
        }

        return false;
    }

    // =========================================================================
    // 🔮 MAGIC INTERCEPTORS
    // =========================================================================

    /****
     * Let's Bound Them to when PhantomShell SUMMONS(🌪️ ☄️).
    /**
     * Intercepts property setting to enforce locks and delegate to the mission engine.
    * /
    public function __set(string $name, mixed $value): void
    {
        if ($this->isLocked($name)) {
            throw new RuntimeException("Modification of private/protected property [{$name}] is locked by default. Use unlock('{$name}') to allow it.");
        }
        $this->forceSetProperty($name, $value);
    }

    public function __get(string $name): mixed
    {
        if (!$this->isLocked($name)) {
            return $this->forceGetProperty($name);
        }
        throw new RuntimeException("Read access to private/protected property [{$name}] is locked. Use unlock('{$name}').");
    }

    /**
     * Intercepts `unset` to enforce locks and operate on the correct context.
    * /
    public function __unset(string $name): void
    {
        if ($this->isLocked($name)) {
            throw new RuntimeException("Deletion of property [{$name}] is locked.");
        }

        /* $target = $this->_spyTarget ?? $this;
        if (property_exists($target, $name)) {
            // Note: Reflection property unset is tricky, usually we can only unset standard props
            unset($target->{$name});
        } * /
        
        if ($this->_spyTarget !== null) {
            if (property_exists($this->_spyTarget, $name)) unset($this->_spyTarget->{$name});
            return;
        }
        if (property_exists($this, $name)) unset($this->{$name});
    }

    public function __isset(string $name): bool
    {
        return $this->hasProperty($name);
    }
    ****/

    // =========================================================================
    // 🔧 MISSION EXECUTION ENGINE (Quantum Enhanced)
    // =========================================================================

    /**
     * [UNIFIED & ENHANCED] Force-sets a property on the CURRENT CONTEXT ($this or spy target).
     * Tries Reflection first (Standard). If it fails due to Readonly, triggers Quantum Tunneling.
     *
     * @param string $propertyName The name of the property to set.
     * @param mixed $value The value to assign.
    */
    public function forceSetProperty(string $propertyName, mixed $value, ?object $targetObject = null): void
    {
        $finalTarget = $targetObject ?? $this->_spyTarget ?? $this;
        $this->_ensureAgencyReadyFor($finalTarget);

        try {
            $reflection = $this->getReflectionProperty($propertyName, $finalTarget);
            
            if ($reflection) {
                // 1. Try Standard Reflection
                try {
                    $reflection->setValue($finalTarget, $value);
                } catch (\Error $e) {
                    // 2. Catch "Cannot modify readonly property" Error
                    if (str_contains($e->getMessage(), 'readonly')) {
                        // 3. Engage Quantum Tunneling (FFI)
                        if ($this->quantumTunnel($propertyName, $finalTarget)) {
                            // Retry set after unlocking memory
                            $reflection->setValue($finalTarget, $value);
                        } else {
                            throw $e; // Re-throw if tunnel failed
                        }
                    } else {
                        throw $e;
                    }
                }
            } else {
                // Dynamic property fallback
                $finalTarget->{$propertyName} = $value;
            }
        /*} catch (Exception $e) {
            // Silent fail for robustness (matches v5 behavior)
        }*/
        } catch (\Throwable $e) {
            $this->_log("forceSetProperty failed for {$propertyName} on " . get_debug_type($finalTarget) . ': ' . $e->getMessage());
        }
    }

    /**
     * [UNIFIED] Force-gets a property from the CURRENT CONTEXT (parent or spy target).
     *
     * @param string $propertyName The name of the property to retrieve.
     * @return mixed The value of the property or null if not found.
    */
    public function forceGetProperty(string $propertyName, ?object $targetObject = null): mixed
    {
        $finalTarget = $targetObject ?? $this->_spyTarget ?? $this;
        $this->_ensureAgencyReadyFor($finalTarget);

        try {
            if (property_exists($finalTarget, $propertyName)) {
                // Direct access check if public (unlikely if we are here via magic, but possible)
                    // return $finalTarget->{$propertyName}; 
            }
            $reflection = $this->getReflectionProperty($propertyName, $finalTarget);
            if ($reflection) {
                return $reflection->getValue($finalTarget);
            }
        // } catch (Exception) { /* Silent fail */ }
        } catch (\Throwable $e) {
            $this->_log("forceGetProperty failed for {$propertyName} on " . get_debug_type($finalTarget) . ': ' . $e->getMessage());
        }
        return null;
    }

    /**
     * [INTEGRATED] Checks if a property exists on the current/specified context object, regardless of its visibility,
     * leveraging the central reflection cache for maximum performance.
     *
     * @param string $name The property name.
     * @return bool
    */
    public function hasProperty(string $name, ?object $targetObject = null): bool
    {
        return $this->getReflectionProperty($name, $targetObject) !== null;
    }

    // =========================================================================
    // ⚔️ WARLORD COMMANDS
    // =========================================================================

    /**
     * [THE WARLORD'S COMMAND v3.0 - THE HYBRID INTELLECT]
     *
     * This definitive, hyper-flexible entry point for forced invocations embodies true strategic
     * intelligence. It prioritizes explicit commands while retaining context-aware fallbacks, making
     * it superior in every tactical scenario. It achieves this by accepting an optional, explicit
     * `$target` object.
     *
     * 📜 STRATEGIC ARCHITECTURE (UPGRADED):
     * 1.  **Hybrid Intellect (The Core Upgrade):** The target resolution is now a three-tiered system,
     *     providing maximum flexibility and backward compatibility.
     *     - PRIORITY 1: Explicit `$target` argument (Direct Command).
     *     - PRIORITY 2: Implicit `$this->_spyTarget` (Spy Context).
     *     - PRIORITY 3: Fallback to `$this` (Parent/Self Context).
     * 2.  **Pure Engine Delegation:** It now passes the resolved target to "The Chronos Engine"
     *     (`getReflectionMethod`), ensuring the reflection engine always operates on the correct
     *     context without needing to know about internal state like `_spyTarget`.
     * 3.  **Absolute Robustness & Simplicity:** The core logic remains lean, powerful, and encased
     *     in a `try/catch` shield for silent, graceful failure.
     *
     * This version fully embraces your command for superior design and flexibility.
     *
     * @param string $methodName The name of the method to call.
     * @param array  $args       Arguments to pass to the method.
     * @param ?object $target    [THE UPGRADE] Optional. An explicit target object to run the method on. If null, it uses context-awareness.
     * @return mixed The result of the method call, or null on any failure.
     */
    protected function forceCallMethod(string $methodName, array $args = [], ?object $target = null): mixed
    {
        try {
            // PHASE 1: THE HYBRID INTELLECT - Determine the ultimate target based on command priority.
            // This single line implements the three-tiered logic as per your strategic directive.
            $invocationTarget = $target ?? $this->_spyTarget ?? $this;
            $this->_ensureAgencyReadyFor($invocationTarget);

            // PHASE 2: THE ORACLE - Consult the Chronos Engine for the method.
            // We now must inform the engine which target to reflect upon.
            // This makes the engine a pure, stateless utility.
            $method = $this->getReflectionMethod($methodName, $invocationTarget);

            // If the Chronos Engine finds the method, the mission proceeds.
            if ($method) {
                // PHASE 3: THE ACTION - Execute the command on the correctly resolved invocationTarget.
                return $method->invokeArgs($invocationTarget, $args);
            }
            $this->_log("forceCallMethod: method {$methodName} not found on " . $invocationTarget::class);
        } catch (Throwable $e) {
            // Silent failure was our current strategy, if We do not want to halt execution.
            // For debugging: error_log("Krubot forceCallMethod failed for '{$methodName}': " . $e->getMessage());

            $this->_log("forceCallMethod: invocation of {$methodName} on " . $invocationTarget::class . " failed: " . $e->getMessage());
        }
        return null;
    }

    // =========================================================================
    // 🧠 REFLECTION ENGINE (WeakMap Cached)
    // =========================================================================

    /**
     * [THE CHRONOS PROPERTY ENGINE v3.2 - THE CONSOLIDATED TRUST EDITION]
     *
     * This is the definitive counterpart to the method retriever. It applies the same
     * deep-scan logic to props, ensuring that even private props buried deep
     * within parent classes are located, unlocked, and cached efficiently.
     *
     * 📜 STRATEGIC CAPABILITIES:
     * 1.  **Unified Cache Architecture:** Fixes the 'props' vs 'props' inconsistency.
     * 2.  **Deep Inheritance Mining:** Uses the `while` loop strategy to find the EXACT class where
     *       a private property is defined. This is crucial for modification.
     * 3.  **Explicit Context:** Accepts `$targetObject` for external introspection.
     * 4.  **Fail-Safe:** Wrapped in `try/catch` for silent operation on failure.
     *
     * @param string  $name         The property name to find.
     * @param ?object $targetObject The object to reflect upon. If null, defaults to `$this`.
     * @return ReflectionProperty|null The accessible reflection property or null if not found.
    */
    protected function getReflectionProperty(string $name, ?object $targetObject = null): ?ReflectionProperty
    {
        $finalTarget = $targetObject ?? $this->_spyTarget ?? $this;
        $this->_ensureAgencyReadyFor($finalTarget);
        $dossier = &self::$_agencyCache[$finalTarget];

        // 1. Cache Hit
        if (isset($dossier['props'][$name])) {
            return $dossier['props'][$name];
        }

        // 2. Deep Hunt
        try {
            $currentClass = new ReflectionClass($finalTarget);

            // Deep Inheritance Traversal:
            // Iterate up the hierarchy to find where the property is TRULY defined.
            while ($currentClass) {

                if ($currentClass->hasProperty($name)) {
                    $prop = $currentClass->getProperty($name);
                    $prop->setAccessible(true); // Break encapsulation.
                    
                    // DUAL CACHING & RETURN. The Hunt was successful.
                    $dossier['props'][$name] = $prop;
                    return $prop;
                }

                // Move up the inheritance chain to check the parent.
                $currentClass = $currentClass->getParentClass();
            }
        /* } catch (Exception) {
            // Fail silently.
            return null;
        } */
        } catch (Throwable $e) {
            $this->_log("getReflectionProperty: failed for {$name} on " . get_debug_type($finalTarget) . ': ' . $e->getMessage());
            return null;
        }

        // MISSION FAILURE
        return null;
    }

    /**
     * [THE CHRONOS ENGINE v3.2 - THE CONSOLIDATED TRUST EDITION]
     * 
     * This is the definitive, pure utility version of the reflection method retriever. It has been
     * refactored to be completely stateless regarding its target. It no longer discovers context
     * internally; instead, it receives the target object explicitly, making it a pure and highly
     * reusable engine commanded by `forceCallMethod`.
     *
     * 📜 STRATEGIC CAPABILITIES (VERIFIED & CONSOLIDATED):
     * 1.  **Global Static Cache (The Future):** Utilizes `$_agencyCache` WeakMap for hyper-fast, O(1) lookups shared across ALL bot instances, preventing redundant reflection work.
     * 2.  **Deep Inheritance Traversal (The Power):** Employs a `while` loop to scan the entire class hierarchy, reliably finding any `private` or `protected` method where simpler functions fail.
     * 4.  **Hybrid Context-Awareness (The Evolved Intellect):** The engine is now commanded externally. It accepts an explicit, nullable `$targetObject`. If null (legacy call), it intelligently falls back to `$this`. This eliminates internal guesswork (`_fetchReflectionContext`) while guaranteeing 100% Backward Compatibility. (Evolved & Fortified)
     * 5.  **Absolute Robustness (The Shield):** Every Part is Wrapped in `try/catch` blocks to handle reflection exceptions silently, ensuring maximum application stability.
     *
     * This version embodies complete trust through transparency and power.
     *
     * @param string  $name         The method name to find.
     * @param ?object $targetObject The object to reflect upon. If null, defaults to `$this` for Backward Compatibility.
     * @return ReflectionMethod|null The accessible reflection method or null if not found.
    */
    protected function getReflectionMethod(string $name, ?object $targetObject = null): ?ReflectionMethod
    {
        $finalTarget = $targetObject ?? $this->_spyTarget ?? $this;
        $this->_ensureAgencyReadyFor($finalTarget);
        $dossier = &self::$_agencyCache[$finalTarget];

        // 1. Cache Hit
        if (isset($dossier['methods'][$name])) {
            return $dossier['methods'][$name];
        }

        // 2. Deep Hunt
        // This block is executed only ONCE per method, per class context, for the entire application lifecycle.
        try {
            // Instantiate reflection on the *resolved* final target.
            $currentClass = new ReflectionClass($finalTarget);

            // Deep Inheritance Traversal. This is the critical Step.
            // It iterates up the entire class hierarchy, ensuring no ancestor is missed.
            while ($currentClass) {

                if ($currentClass->hasMethod($name)) {
                    $method = $currentClass->getMethod($name);
                    $method->setAccessible(true); // Break encapsulation.
                    
                    // DUAL CACHING & RETURN. The Hunt was successful.
                    $dossier['methods'][$name] = $method;
                    return $method;
                }

                // If not found, move up the inheritance chain.
                $currentClass = $currentClass->getParentClass();
            }
        /* } catch (Throwable) {
            // Fail silently to prevent application crashes from reflection errors.
            return null;
        } */
        } catch (\Throwable $e) {
            $this->_log("getReflectionMethod: failed for {$name} on " . get_debug_type($finalTarget) . ': ' . $e->getMessage());
            return null;
        }

        // MISSION FAILURE REPORT
        // If the hunt was unsuccessful after checking all ancestors, the method does not exist.
        return null;
    }

    /**
     * Clears the reflection cache for the CURRENT/Specified context. Useful for testing or long-running processes.
    */
    public function clearReflectionCache(?object $target = null): void
    {
        if (self::$_agencyCache === null) return;
        
        $finalTarget = $target ?? $this->_spyTarget ?? $this;
        if (isset(self::$_agencyCache[$finalTarget])) {
            self::$_agencyCache[$finalTarget]['props'] = [];
            self::$_agencyCache[$finalTarget]['methods'] = [];
            // We do NOT clear 'unlocked' to maintain security state
        }
    }
}
