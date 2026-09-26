<?php

declare(strict_types=1);

namespace KrubiK\Extensions;
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

use KrubiK\Routing\Route;
use KrubiK\DTOs\Message;

use ReflectionMethod;
use ReflectionClass;

/**
 * ╔══════════════════════════════════════════════════════════════════════╗
 * ║  KRUBOT ⚡ ATTRIBUTE PLUGIN CONTRACT (RC.9)                           ║
 * ║  The Atomic Interface Every JackPoint-Registered Plugin Must Sign    ║
 * ╚══════════════════════════════════════════════════════════════════════╝
 *
 * [ HYPER-DX BRIEFING ]
 * The immutable law of the KrubiK attribute scene. Every plugin that wants
 * to jack into the Nexus routing pipeline implements this contract and
 * declares its own policy — the system OBEYS the declaration instead of
 * enforcing opinionated defaults. Maximum freedom, zero ceremony.
 *
 * [ THE FIVE MOMENTS OF POWER ]
 *   ① cryptonBeacon() → O(1) FQCN registry key (identity only — the
 *      attribute class never even needs to be autoloaded).
 *   ② scanHorizon()   → target bitmask + repeatability policy. The dev
 *      decides: Class / Method / Property / combos, Singular or Repeatable.
 *   ③ analyse()       → cold path. Once per attribute occurrence at
 *      integrateNexus time. Heavy lifting is legal here — results are
 *      cached in nexusManifestCache, so request time pays ZERO interest.
 *   ④ crossmatch()    → hot path. Per-request matching gate inside the innermost
 *      routing of EpicEngine.
 *   ⑤ dossier()       → introspection feed for artisan / debugger / docs.
 *
 * [ PERFORMANCE CONTRACT — HARD, NON-NEGOTIABLE ]
 *   - analyse()    → getArguments() ONLY. No newInstance(). Ever.
 *                    Attribute classes may be pure scarecrows — metadata
 *                    without instantiation.
 *   - crossmatch() → < 1µs. No DB. No HTTP. No heavy logic. Read only
 *                    pre-computed data via $route->harvestExtension().
 *   - dossier()    → introspection-only; never on the request path.
 *
 * [ ECOSYSTEM PROMISE ]
 * Anyone can implement this interface, ship a plugin on GitHub, and let
 * JackPoint orchestrate the entire lifecycle — assemble, dispatch, document.
 *
 * [ LIST — INTROSPECTION CONSUMERS ]
 *   - `php artisan krubot:routes` table output
 *   - Route debugger / inspector
 *   - Auto-generated documentation
 *   - JackPoint::fire('route.listed', ...) consumers
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.9×
 * @license MIT
*/
interface Syringe
{
    // =========================================================================
    // ① THE ATTRIBUTE FQCN — O(1) Registry Key
    // =========================================================================

    /**
     * Beacon the FQCN of the PHP Attribute this plugin patrols.
     *
     * Krubot keys its plugin registry on this string — O(1) lookup,
     * no reflection roulette. It MUST match the #[Attribute]-decorated
     * class exactly, letter for letter.
     *
     * Identity-only protocol: the attribute class does not even need to
     * be loaded for this key to resolve.
     *
     * Example:
     *   return \MyPackage\Attributes\Mock::class;
     *
     * @return class-string
    */
    public static function cryptonBeacon(): string;

    /**
     * Declare the allowed targets as a PHP-Attribute bitmask — the plugin
     * author owns the policy, the system merely enforces it.
     *
     * Legal loadouts:
     *   Attribute::TARGET_METHOD                                // method-only
     *   Attribute::TARGET_CLASS                                 // class-only
     *   Attribute::TARGET_CLASS | Attribute::TARGET_METHOD      // hybrid
     *   Attribute::TARGET_PROPERTY                              // property (future)
     *
     * Return 0 → the system falls back to reading the policy from the
     * attribute's own Reflection (policy delegation mode).
     *
     * @return int Bitmask of Attribute::TARGET_* | Attribute::IS_REPEATABLE
    */
    public static function scanHorizon(): int;

    /**
     * Singular or Repeatable? — the tri-state repeatability switch.
     *
     *   true  → the system harvests EVERY occurrence and hands analyse()
     *           an array; the plugin appends into its own vault.
     *   false → classic Singular behavior (first occurrence wins, e.g. Throttle).
     *   null  → defer to the IS_REPEATABLE flag baked into the attribute
     *           itself (system reads it via Reflection).
     *
     * @return bool|null
    */
    // public static function isRepeatable(): ?bool;

    // =========================================================================
    // ② SCAN PHASE — Cold Path, Called Once Per Attribute Occurrence
    // =========================================================================

    /**
     * [ ZERO-INSTANCE SCAN ] — extract attribute data WITHOUT instantiation.
     *
     * Called once per attribute occurrence when a Nexus method carries your
     * Attribute (class-level too, whenever scanHorizon() includes
     * TARGET_CLASS). For Repeatable attributes this fires once per instance
     * — appending into the vault is the plugin's own responsibility.
     *
     * ⚡ PERFORMANCE CLAUSE: instead of newInstance(), this receives the raw
     *   ReflectionAttribute::getArguments() payload. Attribute classes can
     *   remain pure scarecrows. Heavy computation is acceptable here — it
     *   runs ONCE per nexus per worker lifecycle, cached in
     *   nexusManifestCache. Zero cost at request time.
     *
     * $args contract — positional or named, verbatim from Reflection:
     *   #[Throttle(maxAttempts: 5, decaySeconds: 60)]
     *   → named:      ['maxAttempts' => 5, 'decaySeconds' => 60]
     *   → positional: [5, 60]
     *
     * Your mission at scan time:
     *   - Pre-compute anything dispatch will need.
     *   - Write it onto the $route via
     *     $route->implantExtension(static::cryptonBeacon(), $data)
     *     for clean namespacing.
     *   - Optionally read class-level attributes from $class for inheritance.
     *
     * @param  Route            $route  The route being assembled — write here.
     * @param  array            $args   Raw attribute arguments — never an instance.
     * @param  ReflectionMethod $method The method carrying the attribute.
     * @param  ReflectionClass  $class  The nexus class (for class-level reading).
     * @return void
    */
    public function analyse(
        Route            $route,
        array            $args,
        ReflectionMethod $method,
        ReflectionClass  $class,
    ): void;

    // =========================================================================
    // ③ 🧪 CROSSMATCH 🔬 — Hot Path, Per Request, Inside the Innermost Loop
    // =========================================================================

    /**
     * [ VETO GATE ] — evaluated for every candidate route per message.
     *
     * ⚠️  PERFORMANCE CONTRACT:
     *     This lives in the innermost routing loop of EpicEngine.
     *     Typical case MUST return in < 1µs.
     *     NO database queries. NO HTTP calls. NO heavy computation.
     *     Read ONLY pre-computed data via
     *     $route->harvestExtension(static::cryptonBeacon()).
     *
     * Return semantics:
     *   true/null/return-nothing   → no objection; the matching pipeline continues.
     *   false                      → VETO. The pipeline stopped & This route is skipped entirely.
     *
     * Purely decorative / metadata-only plugins: return true unconditionally.
     *
     * @param  Route $route   The candidate route (carries your scanned data).
     * @param  mixed $message The incoming message/context (platform-agnostic).
     * @return ?bool           true = pass, null = pass, false = block this route.
    */
    public function crossmatch(Route $route, mixed $message): ?bool;

    // =========================================================================
    // ④ LIST PHASE — Introspection Only, Never on the Request Path
    // =========================================================================

    /**
     * [ DOSSIER ] — a human-readable summary of what this plugin stamped
     * onto $route. Consumed by artisan tables, debuggers, and doc-gen.
     *
     * Return [] if the plugin added nothing to $route (attribute absent,
     * or no visible effect).
     *
     * Example:
     *   ['throttle' => '5/min (user)', 'throttle.decay' => '60s']
     *
     * @param  Route $route The route to describe.
     * @return array<string, string> Key–value pairs for display.
    */
    public function dossier(Route $route): array;
}
