<?php

namespace KrubiK\Enums\Arsenal;
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

use KrubiK\Attributes\Lexicon;
use ReflectionEnum;
use UnitEnum;
use BackedEnum;
use ReflectionEnumBackedCase;
use ReflectionEnumUnitCase;
use ValueError;

use function KrubiK\Render\Helpers\{
    attributesToString,
    global_esc
};

/**
 * ╔══════════════════════════════════════════════════════════════════════╗
 * ║                            RUNIX TRAIT v3.1                          ║
 * ╠══════════════════════════════════════════════════════════════════════╣
 * ║ A reusable enum utility arsenal for the KrubiK ecosystem.            ║
 * ║                                                                      ║
 * ║ Runix converts native PHP Enums into a powerful command interface:   ║
 * ║                                                                      ║
 * ║   • Introspection                                                    ║
 * ║   • Random case generation                                           ║
 * ║   • Name/value lookup                                                ║
 * ║   • HTML form generation                                             ║
 * ║   • Attribute-based descriptions                                     ║
 * ║   • Fluent case comparisons                                          ║
 * ║   • OmniRadar Integration   (`radar()`, `xray()`, `signals()`)       ║
 * ║                                                                      ║
 * ║ Think of it as an arcane runtime module plugged directly into        ║
 * ║ your enum's core. Every method is a portal into its case matrix.     ║
 * ╚══════════════════════════════════════════════════════════════════════╝
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.9×
 * @license MIT
*/
trait RunixCore
{
    /**
     * 🦾 JIT (Just-In-Time) Reflection Engine with Macro-Powered Caching.
     *
     * This is the performance core. It avoids the catastrophic cost of repeatedly
     * instantiating `ReflectionEnum` on hot paths.
     *
     * THE STRATEGY:
     * It hijacks the OverMind's Macroable trait, using it as a high-speed,
     * out-of-band cache. On first call ("cold boot"), it reflects the enum and
     * *implants* the result as a zero-argument ghost method (`__runixReflection`)
     * into the class's virtual table.
     *
     * All subsequent calls hit this pre-computed cache, returning the reflection
     * instance at near-zero cost. If the `Macroable` trait is not detected, it
     * gracefully degrades to non-cached, ephemeral behavior.
     *
     * @return ReflectionEnum The cached, singleton-like reflection instance for this enum.
    */
    protected static function reflect(): ReflectionEnum
    {
        // Fallback Protocol: If the Macroable infrastructure isn't detected, we have
        // no persistent universe to store the reflection. Operate in ephemeral mode.
        if (!method_exists(static::class, 'macro')) {
            // Reflection works alongside `Macroable` (thus armed in `ExoFrame`) cause PHP Enums Natively can't have any props.
            return new ReflectionEnum(static::class);
        }

        // Hot-Path Execution: Check if the ghost method has already been implanted.
        if (static::hasMacro('__runixReflection')) {
            // A direct hit. Invoke the pre-compiled ghost method to retrieve the
            // payload from the macroverse instantly. This is our zero-cost read.
            return static::__runixReflection();
        }

        // --- Cold Boot Sequence: This block executes exactly ONCE per class lifecycle. ---

        // 1. The expensive operation we want to avoid repeating.
        $reflection = new ReflectionEnum(static::class);

        // 2. THE IMPLANT: Forge the ghost method.
        // We register a new macro that acts as a perpetual, zero-argument factory.
        // The closure eternally captures the `$reflection` instance, effectively locking
        // it into the class's runtime definition from outside the enum's static boundary.
        static::macro('__runixReflection', static fn (): ReflectionEnum => $reflection);

        // Return the fresh payload for this initial call.
        return $reflection;
    }

    /**
     * [CASE VALUE MATRIX]
     * Unleashes an array of all case values from the enum's core.
     *
     * Extracts the backing values of every enum case and returns them
     * as a zero-indexed array.
     *
     * Backed enum example:
     *
     *     ['active', 'inactive', 'pending']
     *
     * For unit enums, no scalar backing value exists.
     *
     * @return array<int, string|int>
    */
    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }

    /**
     * [CASE NAME MATRIX]
     * Summons an array of all case names, the very identity of each state.
     *
     * Example:
     *     ['ACTIVE', 'INACTIVE', 'PENDING']
     * These names represent the immutable identifiers encoded
     * inside the enum's declaration.
     *
     * @return array<int, string>
    */
    public static function names(): array
    {
        return array_column(static::cases(), 'name');
    }

    /**
     * [NAME-TO-VALUE SYNCHRONIZER]
     * Forges an associative array, mapping the sacred names to their potent values.
     *
     * Builds an associative map where every enum case name points
     * to its corresponding backing value.
     *
     * Example:
     *
     *     [
     *         'ACTIVE'   => 'active',
     *         'INACTIVE' => 'inactive',
     *     ]
     *
     * This structure is useful for serialization, configuration maps,
     * API payloads, and validation layers.
     *
     * @return array<string, string|int>
    */
    public static function toArray(): array
    {
        return array_combine(static::names(), static::values());
    }

    /**
     * [SELECTABLE VALUE GATEWAY]
     * Constructs a value-to-name array, primed for the front-end matrix (e.g., HTML select boxes).
     *
     * Generates an associative array optimized for select-boxes,
     * dropdown menus, filters, and UI component bindings.
     *
     * Backed enum:
     *
     *     [
     *         'active' => 'ACTIVE',
     *     ]
     *
     * Unit enum:
     *
     *     [
     *         'ACTIVE' => 'ACTIVE',
     *     ]
     *
     * The backing value becomes the key whenever the enum supports it.
     *
     * @return array<string|int, string>
    */
    public static function asSelectable(): array
    {
        if (!static::isBacked()) {
            return array_combine(static::names(), static::names());
        }

        return array_combine(static::values(), static::names());
    }

    /**
     * [BACKING SIGNATURE SCANNER]
     *
     * Determines whether the current enum is a BackedEnum.
     * 
     * Peers into the enum's soul to determine if it's a 'Backed' entity, possessing a scalar value.
     *
     * A backed enum contains scalar values such as:
     *
     *     enum Status: string
     *     {
     *         case ACTIVE = 'active';
     *     }
     *
     * Unit enums contain names only and do not carry scalar values.
     *
     * @return bool
    */
    public static function isBacked(): bool
    {
        return static::reflect()->isBacked();
    }

    /**
     * [BACKING TYPE ORACLE]
     *
     * Reveals the scalar type used by a backed enum.
     * Reveals the fundamental 'backing type' of a Backed Enum
     *
     * Possible return values:
     *
     *     'string'
     *     'int'
     *     null    // unit enum
     *
     * Reflection is used here to inspect the enum's native declaration
     * without relying on assumptions about its implementation.
     *
     * @return string|null
    */
    public static function getBackingType(): ?string
    {
        if (!static::isBacked()) {
            return null;
        }

        return (string) static::reflect()->getBackingType();
    }

    /**
     * [RANDOM CASE SUMMONER]
     * Pulls a random case from the vortex of possibilities.
     *
     * Selects one enum case at random.
     *
     * Useful for:
     *
     *   • Fixtures
     *   • Mock data
     *   • Test scenarios
     *   • Procedural simulations
     *   • Randomized demo environments
     *
     * @return self
    */
    public static function random(): self
    {
        $cases = static::cases();

        return $cases[array_rand($cases)];
    }

    /**
     * [RANDOM NAME TRANSMISSION]
     * Conjures the name of a randomly selected case.
     *
     * Returns the symbolic name of a randomly selected enum case.
     *
     * Example:
     *
     *     Status::randomName(); // 'ACTIVE'
     *
     * @return string
    */
    public static function randomName(): string
    {
        return static::random()->name;
    }

    /**
     * [QUANTUM VALUE HARVESTER]
     * Retrieves the value of a randomly chosen case. A power reserved for the Backed ones.     *
     * Returns the backing value of a randomly selected enum case.
     * 
     * Example:
     *
     *     Status::randomValue(); // 'active'
     *
     * Extracts the backing scalar payload from a randomized case vector.
     * Guarded by quantum-type reflection: If invoked within a pure UnitEnum realm
     * (where scalar backing is void), it seamlessly falls back to the symbolic case name
     * rather than collapsing the runtime matrix with an Undefined Property fatal error.
     *
     * @return string|int
    */
    public static function randomValue(): string|int
    {
        $case = self::random();

        return self::isBacked() ? $case->value : $case->name;
    }

    /**
     * [NAME EXISTENCE SCANNER]
     * Scans the enum's registry to verify the existence of a case by its given name.
     *
     * Checks whether the supplied symbolic name exists among
     * the declared enum cases.
     *
     * Strict comparison is intentionally enabled so that values
     * are not accidentally matched through type coercion.
     *
     * @param string $name Enum case name to inspect.
     *
     * @return bool
    */
    public static function hasName(string $name): bool
    {
        return in_array($name, static::names(), true);
    }

    /**
     * [VALUE EXISTENCE SCANNER]
     * Queries the enum's essence to confirm a case with the given value exists.
     *
     * Checks whether the supplied scalar exists among the backing
     * values of the enum.
     *
     * Unit enums have no backing values and therefore always return
         * false from this method.
     *
     * @param string|intvalue Backing value to inspect.
     *
     * @return bool
    */
    public static function hasValue(string|int $value): bool
    {
        if (!static::isBacked()) {
            return false;
        }

        return in_array($value, static::values(), true);
    }

    /**
     * [NAME-BASED CASE RESOLVER]
     *
     * Resolves an enum case using its symbolic name.
     *
     * Materializes an enum case from its given name.
     * A failed conjuration will trigger a `ValueError`.
     *
     * Unlike the native from() method, which resolves backed values,
     * this method resolves declarations such as:
     *
     *     Status::fromName('ACTIVE');
     *
     * An exception is emitted when the requested case does not exist.
     *
     * @param string $name Enum case name.
     *
     * @throws ValueError When the name does not match any case.
     *
     * @return self
    */
    public static function fromName(string $name): self
    {
        foreach (static::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        throw new ValueError(
            "$name is not a valid case name for enum " . static::class
        );
    }

    /**
     * [SILENT NAME RESOLVER]
     *
     * A more cautious version of `fromName`. Attempts to materialize a case,
     * returning null if the name is not found in the codex.
     *
     * Performs the same lookup as fromName(), but suppresses the
     * ValueError and returns null when no matching case is found.
     *
     * This method is ideal for nullable input, request data,
     * optional filters, and defensive parsing.
     *
     * @param string $name Enum case name.
     *
     * @return self|null
    */
    public static function tryFromName(string $name): ?self
    {
        try {
            return static::fromName($name);
        } catch (ValueError) {
            return null;
        }
    }

    /**
     * [FIRST CASE ANCHOR]
     * Retrieves the Alpha case, the very first defined in the enum's scripture.
     *
     * Returns the first case declared inside the enum.
     *
     * Declaration order defines the result. This can be useful when
     * an enum has a natural default or initial state.
     *
     * @return self
    */
    public static function first(): self
    {
        return static::cases()[0];
    }

    /**
     * [LAST CASE ANCHOR]
     * Summons the Omega case, the final entry in the enum's chronicle.
     *
     * Returns the final case declared inside the enum.
     *
     * The result is determined by declaration order, not alphabetic
     * sorting or backing value order.
     *
     * @return self
    */
    public static function last(): self
    {
        $cases = static::cases();

        return end($cases);
    }

    /**
     * [NEXT CASE STEP]
     * Returns the next enum case by *declaration order*.
     *
     * - Fast path: single static::cases() fetch + strict index lookup.
     * - Safe default: does NOT wrap; returns null at the end.
     * - Wrap mode: loops from last -> first (perfect for cyclic state machines).
     *
     * @param bool $wrap If true, last->first (cyclic traversal)
     *
     * @return self|null
    */
    public function next(bool $wrap = false): ?self
    {
        $cases = static::cases();
        $max   = count($cases) - 1;

        // Single-case enum: next is either itself (wrap) or null (non-wrap)
        if ($max <= 0) {
            return $wrap ? $cases[0] : null;
        }

        $i = self::__runixIndexOf($this);

        if ($i < $max) {
            return $cases[$i + 1];
        }

        return $wrap ? $cases[0] : null;
    }

    /**
     * [PREVIOUS CASE STEP]
     * Returns the previous enum case by *declaration order*.
     *
     * - Safe default: does NOT wrap; returns null at the beginning.
     * - Wrap mode: loops from first -> last (cyclic traversal).
     *
     * @param bool $wrap If true, first->last (cyclic traversal)
     *
     * @return self|null
    */
    public function prev(bool $wrap = false): ?self
    {
        $cases = static::cases();
        $max   = count($cases) - 1;

        // Single-case enum: prev is either itself (wrap) or null (non-wrap)
        if ($max <= 0) {
            return $wrap ? $cases[0] : null;
        }

        $i = self::__runixIndexOf($this);

        if ($i > 0) {
            return $cases[$i - 1];
        }

        return $wrap ? $cases[$max] : null;
    }

    /**
     * [NEXT CASE (HARD)]
     * Same as next(), but throws a ValueError instead of returning null
     * when the boundary is reached (non-wrap mode).
     *
     * This pairs nicely with your existing fromName()/make() style that throws ValueError.
     *
     * @param bool $wrap If true, last->first (cyclic traversal)
     *
     * @return self
     *
     * @throws ValueError
    */
    public function nextOrFail(bool $wrap = false): self
    {
        $next = $this->next($wrap);

        if ($next === null) {
            throw new ValueError('RunixCore::nextOrFail() boundary reached (no wrap).');
        }

        return $next;
    }

    /**
     * [PREVIOUS CASE (HARD)]
     * Same as prev(), but throws a ValueError instead of returning null
     * when the boundary is reached (non-wrap mode).
     *
     * @param bool $wrap If true, first->last (cyclic traversal)
     *
     * @return self
     *
     * @throws ValueError
    */
    public function prevOrFail(bool $wrap = false): self
    {
        $prev = $this->prev($wrap);

        if ($prev === null) {
            throw new ValueError('RunixCore::prevOrFail() boundary reached (no wrap).');
        }

        return $prev;
    }

    /**
     * [NAME STREAM FORMATTER]
     * Weaves all case names into a single string, bound by a chosen separator.
     *
     * Joins all enum case names into a single string using the
     * provided separator.
     *
     * Example:
     *
     *     ACTIVE | INACTIVE | PENDING
     *
     * @param string $separator Text inserted between names.
     *
     * @return string
    */
    public static function namesAsString(string $separator = ', '): string
    {
        return implode($separator, static::names());
    }

    /**
     * [VALUE STREAM FORMATTER]
     * Melds all case values into a cohesive string, linked by a custom separator.
     *
     * Joins all backing values into a single string using the
     * provided separator.
     *
     * This is useful for logs, query fragments, CLI output,
     * and compact diagnostic messages.
     *
     * @param string $separator Text inserted between values.
     *
     * @return string
    */
    public static function valuesAsString(string $separator = ', '): string
    {
        return implode($separator, static::values());
    }

    /**
     * [JSON SERIALIZATION PORTAL]
     * Transmutes the enum's name-to-value map into a JSON-encoded string, ready for data transmission.
     *
     * JSON payload.
     *
     * This can payload.
     *
     * This can be consumed by:
     *
     *   • Frontend applications
     *   • REST endpoints
     *   • Configuration clients
     *   • Debugging tools
     *
     * @return string
    */
    public static function asJson(): string
    {
        return json_encode(static::toArray());
    }

    /**
     * [VALUE-TO-NAME SEARCH ENGINE]
     * Initiates a reverse lookup, searching for a case name that corresponds to a given value.
     *
     * Reverse-scans the enum subspace to trace the symbolic Case Name mapped
     * to a designated scalar backing value.
     *
     * Example:
     *     Status::search('active'); // 'ACTIVE'
     *     Level::search(0);         // 'ZERO_LEVEL' (Survived falsy purge)
     *
     * Unit enums cannot be searched by value and return null.
     *
     * @param string|int $value Backing scalar target to triangulate and search.
     *
     * @return string|null Resolved symbolic name, or null if lost in the void.
    */
    public static function search(string|int $value): ?string
    {
        if (!static::isBacked()) {
            return null;
        }

        $res = array_search($value, static::toArray(), true);

        // Strict boolean validation (`!== false`) prevents falsy zero-key collapse,
        // ensuring cases mapped to integer 0 or string '0' do not vanish into the null void.
        return $res !== false ? $res : null;
    }

    /**
     * [STRING TRANSMUTATION]
     * Grants the case the power of speech, allowing it to present itself as its value (if backed) or name.
     *
     * Converts the current enum case into a string.
     *
     * Backed enums emit their scalar value.
     * Unit enums emit their symbolic case name.
     *
     * This allows enum cases to participate naturally in string
     * concatenation, logging, templates, and output pipelines.
     *
     * @return string
    */
    public function toString(): string
    {
        return (string) ($this->value ?? $this->name);
    }

    /**
     * [IDENTITY COMPARATOR]
     * Performs a strict identity check. Is this case the very same as another?
     *
     * Determines whether the current enum case is exactly the same
     * case instance as the supplied case.
     *
     * Enum cases are singleton-like objects, so strict identity
     * comparison is the correct comparison strategy.
     *
     * @param self $case Case to compare against.
     *
     * @return bool
    */
    public function mirrored(self $case): bool
    {
        return $this === $case;
    }

    /**
     * [NEGATIVE IDENTITY COMPARATOR]
     * A check of non-identity. Is this case distinct from another?
     *
     * Determines whether the current enum case differs from the
     * supplied enum case.
     *
     * Strict identity supplied enum case.
     *
     * Strict identity comparison prevents accidental equivalence
     * caused self $case Case to compare against.
     *
     * @return bool
    */
    public function isNot(self $case): bool
    {
        return $this !== $case;
    }

    /**
     * [CASE MEMBERSHIP CHECK]
     * Determines if the current case is part of a given collection of cases.
     *
     * Checks whether the current case exists inside the supplied
     * collection of enum cases.
     *
     * Strict comparison ensures that only the exact enum case
     * object is accepted.
     *
     * @param array<int, self> $cases Collection of enum cases.
     *
     * @return bool
    */
    public function in(array $cases): bool
    {
        return in_array($this, $cases, true);
    }

    /**
     * [FIRST-CASE DETECTOR]
     * Checks if this case is the Alpha, the first of its kind.
     *
     * Determines whether the current case is the first declared
     * case in the enum.
     *
     * @return bool
    */
    public function isFirst(): bool
    {
        return $this === static::first();
    }

    /**
     * [LAST-CASE DETECTOR]
     * Checks if this case is the Omega, the last of its lineage.
     *
     * Determines whether the current case is the final declared
     * case in the enum.
     *
     * @return bool
    */
    public function isLast(): bool
    {
        return $this === static::last();
    }

    /**
     * [BACKING VALUE EQUALITY CHECK]
     * Compares the case's inner value against a given scalar. A Backed-enum exclusive ability.
     *
     * Compares the current case's backing value against an arbitrary
     * scalar value using strict comparison.
     *
     * Unit enums have no backing values and therefore return false.
     *
     * @param mixed $value Value to compare.
     *
     * @return bool
    */
    public function equals(mixed $value): bool
    {
        if (!static::isBacked()) {
            return false;
        }

        return $this->value === $value;
    }

    /**
     * [LEXICON ATTRIBUTE READER]
     *
     * Deciphers the `[Lexicon]` attribute, revealing the lore or narrative text of a case.
     *
     * Extracts the textual lexicon attached to the current case
     * through the Lexicon attribute.
     *
     * Example:
     *
     *     #[Lexicon('Currently Activated! ; ) ')]
     *     case ACTIVE = 'active';
     *
     * OmniRadar dynamically resolves the correct enum case reflector
     * regardless of whether this enum is backed or unit-based.
     *
     * @return string|null
    */
    public function lexicon(): ?string
    {
        return OmniRadar::xray($this, Lexicon::class)?->entry;
    }

    /**
     * [LEXICON PRESENCE DETECTOR]
     * Verifies if a case has been inscribed with a `[Lexicon]` attribute.
     *
     * Determines whether the current case has a Lexicon attribute
     * attached to its declaration.
     *
     * @return bool
    */
    public function hasLexicon(): bool
    {
        return OmniRadar::pings($this, Lexicon::class);
    }

    /**
     * [LEXICON ARCHIVE]
     * Gathers the lore from all cases, returning an array of their lexicons.
     *
     * Collects the lexicons of every enum case into an indexed array.
     *
     * Cases without a Lexicon attribute contribute null.
     *
     * @return array<int, string|null>
    */
    public static function allLexicons(): array
    {
        return array_map(
            static fn(UnitEnum $case): ?string => $case->lexicon(),
            static::cases()
        );
    }

    /**
     * [VALUE-TO-LEXICON MAP]
     * Forges an associative array mapping case values (or names for unit enums) to their lexical lore.
     *
     * Builds an associative map where each enum value or name points
     * to its human-readable lexicon.
     *
     * Backed enum:
     *
     *     [
     *         'active' => 'Currently active',
     *     ]
     *
     * Unit enum:
     *
     *     [
     *         'ACTIVE' => 'Currently active',
     *     ]
     *
     * When no lexicon exists, the case name becomes the fallback text.
     *
     * @return array<string|int, string>
    */
    public static function asLexiconArray(): array
    {
        $array = [];

        foreach (static::cases() as $case) {
            $key = $case instanceof BackedEnum
                ? $case->value
                : $case->name;

            $array[$key] = $case->lexicon() ?? $case->name;
        }

        return $array;
    }

    /**
     * [SANITIZED HTML OPTION FORGE]
     *
     * Generates a collection of HTML <option> elements from the enum.

     * A string of HTML `<option>` tags, perfect for constructing a selection interface.
     * Marks the chosen one as selected.
     *
     * Transmutes enum cases into secure HTML <option> tags.
     * Hardened against malicious injection vectors: Both the inner case value and its 
     * human-readable presentation layer are neutralized via double-encoded htmlspecialchars(),
     * rendering XSS payloads utterly dormant before they hit the client-side DOM viewport.
     *
     * @param mixed $selected The active scalar or case reference to mark as selected.
     *
     * @return string Validated, escaped HTML option string stream.
    */
    public static function toHtmlOptions(mixed $selected = null): string
    {
        $options = '';

        foreach (static::asLexiconArray() as $value => $text) {
            $escapedValue = static::escape($value);
            $escapedText  = static::escape($text);
            $selectedAttr = ($value == $selected) ? ' selected' : '';

            $options .= "<option value=\"$escapedValue\"$selectedAttr>$escapedText</option>\n";
        }

        return $options;
    }

    /**
     * [FORTIFIED HTML SELECT BARRICADE]
     *
     * Constructs a complete HTML `<select>` element, imbued with all enum cases as options.
     * A powerful UI-forging tool.
     *
     * Plus Additional HTML attributes may be supplied through the
     * $attributes associative array.
     * 
     * DX Example:
     *     Status::toHtmlSelect(
     *         'status',
     *         Status::Active,
     *         ['class' => 'cyber-select', 'required' => 'required']
     *     );
     *
     * Forges a fully armored HTML <select> control containing sanitized enum options.
     * Every dynamic attribute key and payload is routed through an escaping gauntlet
     * to neutralize attribute-breakout vulnerabilities while preserving HTML5 data attributes,
     * classes, and micro-interactions.
     *
     * @param string $name Form input name identifier.
     * @param mixed $selected Currently targeted case value or identifier.
     * @param array<string, scalar> $attributes Key-value registry of DOM attributes.
     *
     * @return string The finalized, impenetrable <select> markup node.
    */
    public static function toHtmlSelect(string $name, mixed $selected = null, array $attributes = []): string
    {
        $escapedName = static::escape($name);

        $attrs = attributesToString($attributes);

        $options = static::toHtmlOptions($selected);

        return "<select name=\"$escapedName\"$attrs>\n$options</select>";
    }

    /**
     * [CASE COUNT ORACLE]
     *
     * Returns the total number of cases declared in the enum.
     *
     * This is useful for validation, diagnostics, progress indicators,
     * and dynamic UI generation.
     *
     * @return int
    */
    public static function count(): int
    {
        return count(static::cases());
    }

    /**
     * [UNIVERSAL CASE RESOLVER]
     *
     * Resolves a case by backing value or symbolic name.
     *
     * Resolution priority:
     *
     *   1. Backing value
     *   2. Case name
     *
     * Example:
     *
     *     Status::make('active'); // resolves by value
     *     Status::make('ACTIVE'); // resolves by name
     *
     * A ValueError is thrown when neither strategy succeeds.
     *
     * @param string|int $valueOrName Backing value or case name.
     *
     * @return self
    */
    public static function make(string|int $valueOrName): self
    {
        if (static::isBacked()) {
            $case = static::tryFrom($valueOrName);

            if ($case) {
                return $case;
            }
        }

        return static::fromName($valueOrName);
    }

    /**
     * Performs the same dual-resolution strategy as make(), but
     * returns null instead of throwing when no case can be resolved.
     *
     * Resolution priority:
     *
     *   1. Backing value
     *   2. Case name
     *   3. null when both fail
     *
     * This method is suitable for request parsing, optional filters,
     * nullable DTO fields, and defensive application boundaries.
     *
     * @param string|int $valueOrName Backing value or case name.
     *
     * @return self|null
    */
    public static function tryMake(string|int $valueOrName): ?self
    {
        if (static::isBacked()) {
            $case = static::tryFrom($valueOrName);

            if ($case) {
                return $case;
            }
        }

        return static::tryFromName($valueOrName);
    }

    /**
     * [EXACT CASE STEP]
     * Retrieves the enum case at the given index with O(1) array lookup.
     *
     * Performance notes:
     * - Uses `static::cases()` directly (already an indexed array) instead of
     *   re-fetching or re-indexing, avoiding any extra allocation.
     * - Bounds are checked with a cheap `isset()` on the index rather than
     *   `count()` + comparison, since `isset()` short-circuits on missing offsets.
     * - No exceptions are thrown on mi exceptions are thrown on miss; returns null for a branch-predictable,
     *   allocation-free fast path (ideal for hot loops / high-throughput code).
     *
     * @|null The matching enum instance, or null if out of range.
    */
    public static function at(int $index): ?self
    {
        $cases = static::cases();

        return $cases[$index] ?? null;
    }

    /**
     * @template T of object
     *
     * @param class-string<T>|null $attrClass
     * @return ($attrClass is null ? list<object> : list<T>)
    */
    public function radar(?string $attrClass = null): array
    {
        return OmniRadar::scan($this, $attrClass);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $attrClass
     * @return T|null
    */
    public function xray(string $attrClass): ?object
    {
        return OmniRadar::xray($this, $attrClass);
    }

    /** @param class-string $attrClass */
    public function pings(string $attrClass): bool
    {
        return OmniRadar::pings($this, $attrClass);
    }

    /**
     * [AUXILIARY — CYBERNETIC ESCAPE HATCH]
     *
     * Shields the output stream from hostile XSS injection vectors.
     * Ingests any scalar/stringable payload, casts it into the string matrix,
     * and runs it through the strict HTML escaping gauntlet (UTF-8, Quotes, Substitutes).
     *
     * @param mixed $value Raw scalar, stringable, or null payload.
     *
     * @return string Sanitized and harmless character stream.
    */
    protected static function escape(mixed $value): string
    {
        return global_esc($value);
    }

    /**
     * [CASE INDEX LOCATOR]
     * Ultra-fast identity lookup (strict) for the current enum case index
     * inside the native declaration-order list returned by static::cases().
     *
     * Why this exists:
     * - We want next()/prev() to be O(n) with *minimal* overhead (no Reflection).
     * - We want strict identity matching (===) to avoid any backed-value ambiguity.
     * - We keep the implementation centralized to avoid copy/paste bugs.
     *
     * @param self $case
     *
     * @return int
    */
    protected static function __runixIndexOf(self $case): int
    {
        // array_search(..., true) => strict identity match (===)
        // It should *always* succeed for legitimate enum cases.
        $i = array_search($case, static::cases(), true);

        // Defensive fallback: if something impossible happens, normalize to 0
        // instead of exploding at runtime in a traversal call.
        return ($i === false) ? 0 : $i;
    }
}
