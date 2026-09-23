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

use ReflectionEnum;
use UnitEnum;

/**
 * [OMNIDIRECTIONAL ATTRIBUTE RADAR]
 *
 * Inspects enum cases and materializes the attributes encoded on them.
 *
 * OmniRadar supports both backed and unit enums because every PHP enum
 * case implements UnitEnum, while only backed cases implement BackedEnum.
 *
 * Resolved attribute instances are cached by enum class, case name,
 * and attribute class to eliminate redundant reflection operations.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.9×
 * @license MIT
*/
final class OmniRadar
{
    /**
     * Attributes already identified and materialized by the radar.
     *
     * Cache key format:
     *
     *     EnumClass::CASE_NAME|AttributeClass
     *
     * A wildcard (`*`) identifies an unfiltered scan containing
     * every attribute attached to the target enum case.
     *
     * @var array<string, list<object>>
    */
    private static array $identified = [];

    /**
     * [OMNIDIRECTIONAL ATTRIBUTE SCAN]
     *
     * Scans the target enum case and returns every detected attribute.
     *
     * Utilizing PHP Modern & Hyper-Fast Way ::
     * No constructor execution. No side effects. Fast + deterministic.
     *
     * When an attribute class is supplied, only instances of that exact
     * attribute are returned. When omitted, the complete attribute field
     * surrounding the case is revealed.
     *
     * - If $attrClass is null => returns ALL attributes on the case.
     * - If $attrClass is provided => returns only matching attributes.
     *
     * Results are memoized, allowing repeated scans to bypass reflection.
     *
     * Example:
     *
     *     OmniRadar::scan(Status::ACTIVE);
     *     OmniRadar::scan(Status::ACTIVE, Lexicon::class);
     *
     * @template T of object
     *
     * @param UnitEnum             $case      The enum case being scanned.
     * @param class-string<T>|null $attrClass Optional attribute signal filter.
     *
     * @xxxreturn ($attrClass is null ? list<object> : list<T>)
     * @return list<array{name: class-string, args: array<int|string, mixed>}>
    */
    public static function scan(UnitEnum $case, ?string $attrClass = null): array {
        $key = $case::class
            . '::'
            . $case->name
            . '|'
            . ($attrClass ?? '*');

        return self::$identified[$key] ??= (function () use ($case, $attrClass): array {
            $enum = new ReflectionEnum($case::class);
            $caseRef = $enum->getCase($case->name);  // ReflectionEnumUnitCase
            $attributes = $caseRef->getAttributes($attrClass);

            return array_map(
                static fn($a) => [
                    'signal'  => $a->getName(),   // normally equals $attrClass
                    'payload' => $a->getArguments(),
                ],
                $attributes
            );
        })();
    }

    /**
     * [TARGETED ATTRIBUTE XRAY]
     *
     * Penetrates the target case and retrieves the first instance
     * of the requested attribute class.
     *
     * If the attribute exists, returns only its arguments array.
     * If not found, returns null.
     *
     * Example:
     *
     *     $lexicon = OmniRadar::xray(
     *         Status::ACTIVE,
     *         Lexicon::class
     *     );
     *
     * @template T of object
     *
     * @param UnitEnum        $case      The enum case being inspected.
     * @param class-string<T> $attrClass The attribute signal to isolate.
     *
     * @return array<int|string, mixed>|null
    */

    public static function xray(UnitEnum $case, string $attrClass): ?array
    {
        $signal = self::scan($case, $attrClass)[0] ?? null;

        return $signal['payload'] ?? null;
    }

    /**
     * [ATTRIBUTE SIGNAL DETECTOR]
     *
     * Reports whether the requested attribute signal can be detected
     * on the target enum case.
     *
     * Example:
     *
     *     OmniRadar::pings(
     *         Status::ACTIVE,
     *         Lexicon::class
     *     );
     *
     * @param UnitEnum             $case      The enum case being inspected.
     * @param class-string<object> $attrClass The attribute signal to detect.
    */
    public static function pings(UnitEnum $case, string $attrClass): bool {
        return self::xray($case, $attrClass) !== null;
    }

    /**
     * Returns the first decoded signal packet for the requested attribute class.
     *
     * Output shape:
     *   ['signal' => class-string, 'payload' => array]
     *
     * @template T of object
     *
     * @param UnitEnum $case
     * @param class-string<T> $attrClass
     *
     * @return array{signal: class-string, payload: array<int|string, mixed>}|null
    */
    public static function signal(UnitEnum $case, string $attrClass): ?array
    {
        return self::scan($case, $attrClass)[0] ?? null;
    }

    /**
     * [RADAR MEMORY PURGE]
     *
     * Erases every identified signal from the in-memory radar archive.
     *
     * This operation is primarily useful in tests and long-running
     * processes where attribute state must be explicitly refreshed.
    */
    public static function purge(): void
    {
        self::$identified = [];
    }
}
