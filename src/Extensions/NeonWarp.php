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

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder; // Import The Symfony Matrix Scanner
use KrubiK\Helpers\JackPoint;        // Import "JackPoint" The Tactical EventHook System
use KrubiK\Helpers\AmethystMatrix;

/**
 * 🌌 THE NEON-WARP PROTOCOL 🌌
 * 
 * Bending the digital light-grid. This trait is the high-speed routing nexus, 
 * bending event-space and weaving quantum threads across the Krubot matrix.
 * Buckle up. You are riding the NeonWarp now. 🏎️💨✨
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait NeonWarp
{
    // =====================================================================
    // 🔗 SYNAPTIC JUNCTION / SYMBOLIC / HARD LINK MATRIX [NEON-WARP CORE]
    // =====================================================================

    /** 
     * @var array<string, array{target: string, bidirectional: bool, condition: callable|null}> 
     * 🌐 The Light-Trails: Routing tables for event teleportation across the Neon Grid.
    */
    protected static array $softLinks = [];

    /** 
     * @var array<string, string> Map of [eventKey => poolKey] 
     * 💎 The Titanium Tethers: Unbreakable quantum binds locking events together.
    */
    protected static array $hardLinks = [];

    /** 
     * @var array<string, array<string, true>> Map of [poolKey => [eventKeys...]] 
     * 🔋 The Plasma Pools: Shared memory sectors for fused event synapses.
    */
    protected static array $hardLinkPools = [];

    /** 
     * @var int Zero-allocation counter for hyper-fast pool ID generation 
     * 🚀 Engine RPM: Keeping the warp-drive overhead at absolute zero!
    */
    protected static int $poolCounter = 0;

    /** 
     * @var bool Flag to trigger regeneration of cached maps if needed 
     * ⚠️ Grid Status: Signals when the neon matrix geometry has shifted.
    */
    protected static bool $linkDirty = true;

    /** @var array<string, true> Virtual (Ghost) events — no direct listeners allowed */
    protected static array $virtualEvents = [];

    /** @var array<string, array{expires: int|string}> */
    protected static array $temporaryJunctions = [];

    /**
     * 🌀 Create a Soft Link (Symbolic / Junction)
     * 
     * Bending the event horizon! Dynamically route one event to another 
     * across the neon highways. If bidirectional, they mirror each other!
     * 
     * @param string        $from           Source event signature (The Entry Portal)
     * @param string        $to             Target event signature (The Exit Portal)
     * @param bool          $bidirectional  True? Two-way quantum mirror established! 🪞✨
     * @param callable|null $when           Condition closure (returns bool). Pure DX control. The gatekeeper logic!
    */
    public static function junction(
        string $from,
        string $to,
        bool $bidirectional = false,
        ?callable $when = null
    ): void {
        $fromKey = static::resolveEventKey($from);
        $toKey   = static::resolveEventKey($to);

        if ($fromKey === $toKey) {
            return; // 🛑 Block paradoxical self-loops at the gate! No infinite black holes allowed!
        }

        static::$softLinks[$fromKey] = [
            'target'        => $toKey,
            'bidirectional' => $bidirectional,
            'condition'     => $when,
        ];

        if ($bidirectional) {
            static::$softLinks[$toKey] = [
                'target'        => $fromKey,
                'bidirectional' => true, // Enforce true on the mirror - Reflection active!
                'condition'     => $when,
            ];
        }

        static::$linkDirty = true;
    }

    /**
     * 🐧 Alias for junction (bidirectional true; Unix-vibe developer experience)
     * ⚡ Terminal-style quick-bind for the backend rebels.
    */
    public static function symlink(
        string $from,
        string $to,
        bool $bidirectional = true,
        ?callable $when = null
    ): void {
        static::junction($from, $to, $bidirectional, $when);
    }

    /**
     * 🐧 Alias for junction (fast-manifest)
     * 🛹 The shortcut. Kickflip into the event loop.
    */
    public static function junc(
        string $from,
        string $to,
        bool $bidirectional = false,
        ?callable $when = null
    ): void {
        static::junction($from, $to, $bidirectional, $when);
    }

    /**
     * 🐧 Alias for junction (fast-manifest)
     * 🛹 The shortcut. Kickflip into the event loop.
    */
    public static function junk(
        string $from,
        string $to,
        bool $bidirectional = false,
        ?callable $when = null
    ): void {
        static::junction($from, $to, $bidirectional, $when);
    }

    /**
     * ⛓️ Create a Hard Link (Shared Synapse Pool)
     * 
     * Deep architectural fusion! Both events will literally share the EXACT same 
     * memory reference for their listeners. Firing one sends a shockwave through the shared plasma pool.
    */
    public static function hardLink(string $eventA, string $eventB): void
    {
        $keyA = static::resolveEventKey($eventA);
        $keyB = static::resolveEventKey($eventB);

        if ($keyA === $keyB) {
            return; // Already the exact same entity. No need to fold space twice.
        }

        $poolA = static::$hardLinks[$keyA] ?? null;
        $poolB = static::$hardLinks[$keyB] ?? null;

        if ($poolA && $poolB && $poolA === $poolB) {
            return; // ⚡ Already fused in the same quantum pool!
        }

        // 🚀 PERF-BOOST: Replaced heavy spl_object_id with zero-alloc static integer! (Warp-speed ID generation)
        $poolKey = $poolA ?? $poolB ?? 'pool_hyper_' . (++static::$poolCounter);

        // 🧠 Fix Array Initialization for Reference Binding
        if (!isset(static::$eventSynapses[$keyA])) static::$eventSynapses[$keyA] = [];
        if (!isset(static::$eventSynapses[$keyB])) static::$eventSynapses[$keyB] = [];

        // pool قدیمی دیگر که merge می‌شه باید پاک بشه
        if ($poolA && $poolB && $poolA !== $poolB) {

            // تمام اعضای pool قدیمی را به pool جدید منتقل کن
            foreach (static::$hardLinkPools[$poolB] as $memberKey => $true) {
                static::$hardLinks[$memberKey] = $poolKey;
                static::$hardLinkPools[$poolKey][$memberKey] = true;
                
                // CRITICAL FIX: Relink PHP Memory Reference for all migrated members!
                if ($memberKey !== $keyB) {
                    static::$eventSynapses[$memberKey] = &static::$eventSynapses[$keyA];
                }
            }

            unset(static::$hardLinkPools[$poolB]); // ← پاک کردن pool قدیمی
        }

        static::$hardLinks[$keyA] = $poolKey;
        static::$hardLinks[$keyB] = $poolKey;

        static::$hardLinkPools[$poolKey][$keyA] = true;
        static::$hardLinkPools[$poolKey][$keyB] = true;

        // 🧠 Merge Listeners (Synapses) into the shared matrix
        if (isset(static::$eventSynapses[$keyB]) && !isset(static::$eventSynapses[$keyA])) {
            static::$eventSynapses[$keyA] = static::$eventSynapses[$keyB];
        } elseif (isset(static::$eventSynapses[$keyA]) && isset(static::$eventSynapses[$keyB])) {

            static::$eventSynapses[$keyA] = array_merge(
                static::$eventSynapses[$keyA],
                static::$eventSynapses[$keyB]
            );

            static::$synapsesDirty[$keyA] = true;   // ← per-key flag
            static::$synapsesDirty[$keyB] = true;   // ← both buckets touched
        }

        // 🔗 Reference Binding: B now literally points to A's memory block! O(1) sharing at lightspeed! 💫
        static::$eventSynapses[$keyB] = &static::$eventSynapses[$keyA];

        static::$linkDirty = true;
    }

    

    /**
     * Junction that auto-expires.
     * $until: Unix timestamp (int) OR 'request.end' (string) OR any time format parsed by CarbonPHP
    */
    public static function temporaryJunction(
        string $from,
        string $to,
        \DateTimeInterface|\DateInterval|int|string $until = 'request.end',
        bool $bidirectional = false,
        ?callable $when = null
    ): void {

        static::junction($from, $to, $bidirectional, $when);

        $fromKey = static::resolveEventKey($from);

        if ($until === 'request.end') {

            $cleanupClosure = static function () use ($fromKey) {

                // FIX : Use native `unlink()` instead of manual foreach to avoid missing it's benefits!
                static::unlink($fromKey);

                /*
                unset(static::$softLinks[$fromKey]);
                // bidirectional mirror cleanup هم انجام بده
                foreach (static::$softLinks as $k => $link) {
                    if ($link['target'] === $fromKey) {
                        unset(static::$softLinks[$k]);
                    }
                }
                */
            };

            // 🤝 Integration Boost: Try Laravel's termination first, fallback to PHP shutdown
            if (function_exists('app') && app()->has('events')) {
                app()->terminating($cleanupClosure);
            } else {
                // Register shutdown cleanup
                register_shutdown_function($cleanupClosure);
            }

            return;
        }

        // THE NEON-CLOCK UPGRADE ⏱️:
        // We use advanceDateTime to parse ANY format (Carbon, strings, intervals, small ints).
        // Then we immediately extract the raw Unix Timestamp. 
        // Why? So the high-speed resolver (resolveFinalTarget) does zero math during runtime! O(1) Velocity.
        
        $expiresTimestamp = advanceDateTime($until)->getTimestamp();
        
        // Timestamp-based: چک در resolveFinalTarget
        static::$temporaryJunctions[$fromKey] = ['expires' => $expiresTimestamp];
    }

    /**
     * Declare a Ghost/Virtual event.
     * It holds no direct listeners; it only routes via junctions.
    */
    public static function virtual(string $event): void
    {
        $key = static::resolveEventKey($event);
        static::$virtualEvents[$key] = true;
    }

    public static function isVirtual(string $event): bool
    {
        return isset(static::$virtualEvents[static::resolveEventKey($event)]);
    }

    /**
     * 🔬 Check if an event acts as a bridge (Junction or HardLink)
     * 🌉 Is this node a glowing gateway or a dead end?
    */
    public static function isJunction(string $event): bool
    {
        $key = static::resolveEventKey($event);
        return isset(static::$softLinks[$key]) || isset(static::$hardLinks[$key]);
    }

    /**
     * 🎯 Resolve Final Target (Hyper-Performance Iterative Resolver)
     * 
     * Surfing the routing matrix!
     * 🚀 PERF-BOOST: Converted from recursive to iterative! 
     * Eliminates call-stack overhead and makes loop-detection memory efficient. Pure velocity.
    */
    protected static function resolveFinalTarget(string $eventKey): string
    {
        $currentKey = $eventKey;
        $visited = [];

        // Traverse the Soft Links graph infinitely... until condition fails or target found. (Riding the warp tunnel)
        while (isset(static::$softLinks[$currentKey])) {

            // Hard link اولویت دارد — traverse را متوقف کن
            if (isset(static::$hardLinks[$currentKey])) {
                break;
            }

            if (isset($visited[$currentKey])) {
                break; // [loop detection] 🚨 Infinite Paradox detected! Fast bail-out before the core melts down.
            }
            $visited[$currentKey] = true;

            $link = static::$softLinks[$currentKey];

            // Evaluate quantum condition (Bypass execution if it fails)
            if ($link['condition'] !== null && !($link['condition'])()) {
                break; // 🛡️ Condition unmet! Halt the jump sequence here. Firewall activated.
            }



            // ⏳ FIX : Expiry Clean Check for Temporary Junctions (Using Unlink to drop mirrors)
            if (isset(static::$temporaryJunctions[$currentKey])) {
                $exp = static::$temporaryJunctions[$currentKey]['expires'];
                if (is_int($exp) && time() > $exp) {
                    /// unset(static::$softLinks[$currentKey], static::$temporaryJunctions[$currentKey]);
                    unset(static::$temporaryJunctions[$currentKey]);
                    static::unlink($currentKey); // Fix: Safely destroys both softLink and its mirror!
                    break;
                }
            }

            $currentKey = $link['target'];
        }

        // Note: Hard links don't shift the key, they share the storage natively. The destination is already here.
        return $currentKey;
    }

    /**
     * 📡 HUD / Diagnostics: Exposes the full linking matrix for debugging
     * 🖲️ Projecting the entire neon routing topography onto the Architect's terminal.
    */
    public static function allLinks(): array
    {
        $soft = [];
        foreach (static::$softLinks as $from => $info) {
            $soft[static::prettyKey($from)] = [
                'target'        => static::prettyKey($info['target']),
                'bidirectional' => $info['bidirectional'],
                'conditional'   => $info['condition'] !== null,
            ];
        }

        $hard = [];
        foreach (static::$hardLinkPools as $pool => $events) {
            $hard[$pool] = array_map([static::class, 'prettyKey'], array_keys($events));
        }

        return [
            'soft' => $soft,
            'hard' => $hard,
        ];
    }

    /**
     * 🧨 Shatter the links! (Unlink / Unbind)
     * 
     * Sever the glowing threads. Removes all soft and hard links 
     * gracefully without destroying the base events. A clean disconnect.
    */
    public static function unlink(string $event): void
    {
        $key = static::resolveEventKey($event);

        // 🪓 Cleave Soft Links (Dimming the neon trails)
        if (isset(static::$softLinks[$key])) {
            $target = static::$softLinks[$key]['target'];
            unset(static::$softLinks[$key]);

            // Mirror breaker (Bidirectional cleanup - shattering the reflection)
            if (isset(static::$softLinks[$target]) && static::$softLinks[$target]['target'] === $key) {
                unset(static::$softLinks[$target]);
            }
        }

        // 🪓 Cleave Hard Links (Decoupling the titanium fusion)
        if (isset(static::$hardLinks[$key])) {
            $poolKey = static::$hardLinks[$key];
            unset(static::$hardLinks[$key]);
            unset(static::$hardLinkPools[$poolKey][$key]);

            // Break the PHP memory reference! Let it become independent again. The synapse breathes free!
            if (isset(static::$eventSynapses[$key])) {
                $unbound = static::$eventSynapses[$key]; // clone the array by value
                unset(static::$eventSynapses[$key]);
                static::$eventSynapses[$key] = $unbound; // Re-assign without reference
            }

            // Cleanup dead pools (Sweeping the ashes)
            if (empty(static::$hardLinkPools[$poolKey])) {
                unset(static::$hardLinkPools[$poolKey]);
            }
        }

        static::$linkDirty = true;
    }

    public static function clearLinks(): void
    {
        static::$softLinks          = [];
        static::$hardLinks          = [];
        static::$hardLinkPools      = [];
        static::$linkDirty          = true;
        static::$poolCounter        = 0;
        static::$virtualEvents      = [];
        static::$temporaryJunctions = [];
    }
}
