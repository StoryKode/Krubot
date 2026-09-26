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

use Attribute;
use KrubiK\Routing\Route;
use ReflectionMethod;
use ReflectionClass;

/**
 * ╔══════════════════════════════════════════════════════════════════════╗
 * ║  ⚡ EXTENSION BASE ⚡                                                  ║
 * ║  The Atomic Abstract Chassis — Parent of Every Krubot Plugin         ║
 * ╚══════════════════════════════════════════════════════════════════════╝
 *
 * [ FULL-STACK DEV BRIEFING ]
 *
 * This abstract class is the **single source of truth** for every Attribute
 * Plugin in the KrubiK ecosystem. It implements the immutable Syringe
 * contract once, then forces concrete plugins to supply only the four
 * methods that actually differ between plugins.
 *
 * Why this design?
 *   • Zero boilerplate in child plugins.
 *   • Provides sane, production-grade defaults for scanHorizon() and dossier()
 *   • Keeps the hot-path contract iron-clad while giving architects freedom
 *
 * [ THE CONTRACT YOU SIGN WHEN YOU EXTEND ]
 *   ① cryptonBeacon() → must return the exact Attribute FQCN
 *   ② analyse()       → cold-path data extraction (getArguments only)
 *   ③ crossmatch()    → hot-path veto gate (< 1 µs ideal)
 *   ④ scanHorizon()   → already implemented here (TARGET_METHOD)
 *   ⑤ dossier()       → already implemented here (hyper-complete)
 *
 * [ PERFORMANCE & SAFETY GUARANTEES ]
 *   - No container resolution, no reflection in hot path
 *   - Survives Octane / RoadRunner / Swoole worker restarts
 *   - Child classes may override scanHorizon() or dossier() if needed
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.9×
 * @license MIT
*/
abstract class Extension implements Syringe
{
    // =========================================================================
    // ① THE ATTRIBUTE FQCN — O(1) Registry Key (MUST be supplied by your Extension)
    // =========================================================================

    /**
     * Beacon the FQCN of the PHP Attribute this plugin patrols.
     *
     * Krubot keys its plugin registry on this string — O(1) lookup,
     * no reflection roulette. It MUST match the #[Attribute]-decorated
     * class exactly, letter for letter.
     *
     * @return class-string
    */
    abstract public static function cryptonBeacon(): string;

    // =========================================================================
    // ② SCAN HORIZON — Default Policy (Method-only, Singular)
    // =========================================================================

    /**
     * Declare the allowed targets as a PHP-Attribute bitmask.
     *
     * Default implementation: TARGET_METHOD only.
     * Child plugins that need Class-level or Repeatable behaviour
     * simply override this method.
     *
     * @return int Bitmask of Attribute::TARGET_* | Attribute::IS_REPEATABLE
    */
    public static function scanHorizon(): int
    {
        return Attribute::TARGET_METHOD;
    }

    // =========================================================================
    // ③ ANALYSE — Cold Path (MUST be supplied by child)
    // =========================================================================

    /**
     * [ ZERO-INSTANCE SCAN ] — extract attribute data WITHOUT instantiation.
     *
     * Called once per attribute occurrence when a Nexus method carries your
     * Attribute. Heavy computation is legal here — results are cached in
     * nexusManifestCache.
     *
     * @param  Route            $route  The route being assembled — write here.
     * @param  array            $args   Raw attribute arguments — never an instance.
     * @param  ReflectionMethod $method The method carrying the attribute.
     * @param  ReflectionClass  $class  The nexus class (for class-level reading).
     * @return void
    */
    abstract public function analyse(
        Route            $route,
        array            $args,
        ReflectionMethod $method,
        ReflectionClass  $class,
    ): void;

    // =========================================================================
    // ④ 🧪 CROSSMATCH 🔬 Bio-Kinetic Hot Zone ☣️ (MUST be supplied by your Extension)
    // =========================================================================

    /**
     * [ BIO-COMPATIBILITY ASSAY PROTOCOL ]
     * — performed on each candidate substrate against the incoming specimen.
     *
     * 🧬 STERILITY & PERFORMANCE PROTOCOL:
     *     This assay runs within the core diagnostic loop of the EpicEngine.
     *     Execution MUST resolve in < 1 µs under typical conditions.
     *
     *     EXTERNAL CONTAMINATION STRICTLY PROHIBITED:
     *     - NO database queries.
     *     - NO network I/O (HTTP, etc.).
     *     - NO heavy computation.
     *
     *     Analysis MUST be confined to pre-computed engrams retrieved via
     *     $route->harvestExtension(static::cryptonBeacon()).
     *
     * @param  Route $route   The candidate substrate 🧫; (carries your scanned data).
     * @param  mixed $message The specimen for analysis; a platform-agnostic biological or digital signal.
     * @return ?bool           `false` = Negative Match (Rejection, initiate Veto).
     *                         `true`  = Positive Match (Compatibility Confirmed, proceed).
     *                         `null` or HasNoReturn:   Also a Positive Match. (This is the implicit return value in php, if the method does not execute a `return` statement.)
    */
    abstract public function crossmatch(Route $route, mixed $message): ?bool;

    // =========================================================================
    // ⑤ DOSSIER — Hyper-Complete Introspection (Production-Grade Default)
    // =========================================================================

    /**
     * ══════════════════════════════════════════════════════════════════
     * ⚡ UNIVERSAL DOSSIER ENGINE — Full-Spectrum Introspection ⚡
     *
     * Generates a rich, human-readable, collision-free key→value map of
     * everything this plugin stamped onto the given Route.
     *
     * Design goals achieved:
     *   • Zero assumptions about payload shape (array / list / scalar)
     *   • Automatic namespacing by cryptonBeacon short-name
     *   • Pretty-printing of nested structures
     *   • Graceful handling of empty / missing / malformed data
     *   • Ready for artisan tables, debuggers, OpenAPI-style docs,
     *     and JackPoint::fire('route.listed') consumers
     *
     * This method is NEVER executed on the request hot-path.
     * Override only when you need domain-specific formatting.
     *
     * @param  Route $route The route to describe.
     * @return array<string, string> Flat, display-ready pairs.
    */
    public function dossier(Route $route): array
    {
        $beacon   = static::cryptonBeacon();
        $short    = $this->resolveShortName($beacon);
        $payload  = $route->harvestExtension($beacon);

        // Fast-exit: nothing was implanted
        if ($payload === [] || $payload === null) {
            return [];
        }

        // Normalize everything to a list of rule-sets
        // (supports both singular implant and repeatable pump)
        $rules = array_is_list($payload) ? $payload : [$payload];

        $result = [];
        $index  = 0;

        foreach ($rules as $rule) {
            $prefix = $index === 0 ? $short : "{$short}.{$index}";

            if (!is_array($rule)) {
                // Scalar fallback (rare but legal)
                $result[$prefix] = $this->stringify($rule);
                $index++;
                continue;
            }

            // Primary summary line (most important for tables)
            $summary = $this->buildSummaryLine($rule);
            if ($summary !== null) {
                $result[$prefix] = $summary;
            }

            // Flatten remaining keys with dotted notation
            foreach ($rule as $key => $value) {
                // Skip keys already consumed by the summary
                if (in_array($key, ['max', 'decay', 'by', 'key', 'message', 'routeId'], true)) {
                    continue;
                }

                $result["{$prefix}.{$key}"] = $this->stringify($value);
            }

            // Explicit secondary fields that are frequently useful
            if (isset($rule['key']) && $rule['key'] !== null && $rule['key'] !== '') {
                $result["{$prefix}.key"] = (string) $rule['key'];
            }

            if (isset($rule['message']) && is_string($rule['message']) && $rule['message'] !== '') {
                $result["{$prefix}.message"] = $rule['message'];
            }

            $index++;
        }

        return $result;
    }

    // ─────────────────────────────────────────────────────────────────
    // Internal helpers (protected so children can reuse / override)
    // ─────────────────────────────────────────────────────────────────

    /**
     * Extract a clean short name from a fully-qualified class name.
    */
    protected function resolveShortName(string $fqcn): string
    {
        $pos = strrpos($fqcn, '\\');
        return $pos === false ? strtolower($fqcn) : strtolower(substr($fqcn, $pos + 1));
    }

    /**
     * Build a single human-readable summary line for a rule set.
     * Designed for Throttle-style payloads but generic enough for others.
    */
    protected function buildSummaryLine(array $rule): ?string
    {
        $parts = [];

        if (isset($rule['max'], $rule['decay'])) {
            $parts[] = "{$rule['max']}/{$rule['decay']}s";
        } elseif (isset($rule['max'])) {
            $parts[] = (string) $rule['max'];
        }

        if (isset($rule['by']) && is_string($rule['by']) && $rule['by'] !== '') {
            $parts[] = "({$rule['by']})";
        }

        if (isset($rule['key']) && is_string($rule['key']) && $rule['key'] !== '') {
            $parts[] = "[{$rule['key']}]";
        }

        return $parts === [] ? null : implode(' ', $parts);
    }

    /**
     * Safely convert any value into a short display string.
    */
    protected function stringify(mixed $value): string
    {
        return match (true) {
            is_null($value)   => 'null',
            is_bool($value)   => $value ? 'true' : 'false',
            is_scalar($value) => (string) $value,
            is_array($value)  => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]',
            is_object($value) => $value::class,
            default           => get_debug_type($value),
        };
    }
}
