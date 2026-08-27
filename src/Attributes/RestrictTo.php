<?php

namespace KrubiK\Attributes;
/*
| Krubot BotEngine: The Architect's Lexicon [×vRC.8×] 🚀📜
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
use InvalidArgumentException;
use Throwable;
use Stringable;
use BackedEnum;
use App\Generated\Platform;
use KrubiK\Enums\SmartPlatform as SmartPlatform;
use Illuminate\Support\Arr;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 *
 * Restricts execution contexts to specified platforms. This ultimate version combines
 * maximum input flexibility with powerful wildcard support.
 *
 * It flawlessly processes:
 * - Single strings: 'telegram'
 * - Comma-separated strings: 'tg, rubika'
 * - Platform enum instances: Platform::Bale
 * - Complex, deeply nested mixed arrays: [Platform::Bale, 'tg, rubika', ['another', ['deeper']]]
 * 
 * All inputs are normalized into a numerically indexed array of normalized platform names (strings).
 *
 * --- Wildcard Superpower ---
 * Using a wildcard ('*' or Platform::All) exempts a method from class-level restrictions,
 * opening it to ALL configured drivers defined in 'config/krubot.php'. This is ideal
 * for creating universally available commands or handlers.
 *
 * Examples of use:
 * #[RestrictTo('telegram')]
 * #[RestrictTo(Platform::Bale)]
 * #[RestrictTo('tg, r, b')] // Handles aliases and whitespace
 * #[RestrictTo(['telegram', Platform::Bale, 'rubika'])]
 * #[RestrictTo([Platform::Rubika, Platform::Bale])] // Using Platform instances
 * #[RestrictTo(['tg', ['bale, rubika']])] // Deeply nested resilience
 * #[RestrictTo(Platform::All)] // Available on ALL configured platforms
 * #[RestrictTo('*')] // Same as above
 * #[RestrictTo([
 *       Platform::Bale, 
 *       'tg, rubika', 
 *      [
 *          'sorush', 
 *          [
 *              'eitaa, bale', 
 *              Platform::Telegram
 *          ]
 *      ]
 *  ])] // even more complex deep merge will be a unique array, combined all of them together (Allow on Any of listed platforms)
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class RestrictTo
{
    /**
     * The unique, flat, normalized list of allowed platform names.
     * Declared as readonly to ensure immutability post-construction.
     *
     * @var array<string>
     */
    public readonly array $platforms;

    /**
     * The final, canonical list of allowed platform names after resolving aliases and legions.
     * This is a private cache (memoization) to avoid re-computation.
     * @var string[]|null
    */
    private ?array $resolvedPlatforms = null;

    /**
     * Flag indicating if this restriction is a wildcard, signifying universal
     * availability across all configured platforms.
     *
     * @var bool
     */
    public readonly bool $isWildcard;

    /**
     * Constructor accepts any supported input format, detects wildcards,
     * flattens the structure, and normalizes the final platform set.
     * 
     * Normalizes various input types into a flat array of normalized platform strings.
     * Handles single strings, comma-separated strings, arrays of strings,
     * arrays of Platform objects, and mixed arrays.
     *
     * @param mixed $platformsInput Can be a string, Enum, complex-nested array, or '*' wildcard.
     */
    public function __construct(mixed $platformsInput)
    {
        // Step 1: Recursively flatten the input into a simple, one-dimensional array of identifiers.
        $rawIdentifiers = $this->flattenAndSplit($platformsInput);

        // Scan for the presence of a wildcard token ('*' || Platform::All) before any validation.
        if (in_array(Platform::All, $rawIdentifiers, true)) {
            $this->isWildcard = true;
            // If wildcard exists, ignore all other inputs and resolve all active platforms from config.
            $this->platforms = $this->resolveAllConfiguredPlatforms();
        } else {
            $this->isWildcard = false;
            // If no wildcard, proceed with standard validation and normalization of the provided identifiers.
            $this->platforms = $this->normalizeAndValidate($rawIdentifiers);
        }
    }

    /**
     * Checks if a target platform is included in the current restriction set.
     *
     * @param string|Stringable|BackedEnum $platform The platform to check.
     * @return bool True if the platform is allowed, false otherwise.
     */
    public function includes(string|Stringable|SmartPlatform|BackedEnum $platform): bool
    {
        $value = $platform instanceof BackedEnum ? $platform->value : $platform;
        return in_array((string) $value, $this->getPlatforms(), true);
    }

    /**
     * Returns the finalized, immutable array of normalized unique platform names.
     *
     * @return array<string>
     */
    public function getPlatforms(): array
    {
        return $this->platforms;
    }

    /**
     * Validates and canonicalizes a flat list of raw identifiers against the Platform Super-Enum.
     * This is the core logic for the non-wildcard path.
     *
     * @param array<mixed> $identifiers A flat array of potential platform identifiers.
     * @return array<string> A unique, normalized array of platform names.
     * @throws InvalidArgumentException If any identifier is invalid or unrecognized.
     */
    private function normalizeAndValidate(array $identifiers): array
    {
        $normalizedNames = [];

        foreach ($identifiers as $identifier) {
            try {
                // Safely convert any identifier type to its string representation.
                $platformString = match (true) {

                    // Convert the identifier to a string. If it's already a Platform object, its __toString method will be called.
                    $identifier instanceof Stringable => ((string) $identifier), // It Was:: ($identifier instanceof SmartPlatform)

                    is_string($identifier) => $identifier,
                    is_object($identifier) && method_exists($identifier, '__toString') => (string) $identifier,
                    default => throw new InvalidArgumentException("Unsupported platform identifier type: " . gettype($identifier))
                };

                // Use the Super-Enum's resolver to normalize and validate the string identifier.
                // This internnaly leverages the dynamic nature of Platform Super-Enum.
                $platformInstance = (class_exists(Platform::class)) ? Platform::tryFrom($platformString) : SmartPlatform::tryFrom($platformString);

               // Path 1: It's a direct canonical name or an alias.
                if ($platformInstance !== null) {
                    // Add the canonical string name to our list.
                    $normalizedNames[] = (string) $platformInstance;
                    continue;
                }

                // Path 2: Maybe It's a Legion. If so, Expand it and recursively normalize its members.
                $legionMembers = (class_exists(Platform::class)) ? Platform::getLegionMembers($platformString) : SmartPlatform::getLegionMembers($platformString);

                if ($legionMembers !== null) {
                    // Recursive call to resolve the members of the legion and inject them to our list.
                    $resolvedLegionMembers = $this->normalizeAndValidate($legionMembers);
                    $normalizedNames = array_merge($normalizedNames, $resolvedLegionMembers);
                    continue;
                }

                // Path 3: It's neither a valid platform/alias nor a legion. Throw error.
                throw new InvalidArgumentException("Unrecognized platform or legion identifier '{$platformString}'.");

            } catch (InvalidArgumentException $e) {
                // Re-throw with more context for better debugging.
                throw new InvalidArgumentException("Platform resolution failed: " . $e->getMessage());
            } catch (Throwable $e) {
                // Catch any other unexpected errors during processing.
                throw new InvalidArgumentException("An unexpected error occurred while processing platform metadata: " . $e->getMessage());
            }
        }

        // Return platform names final set, while ensuring they are unique strings and correctly indexed.
        return array_values(array_unique($normalizedNames));
    }

    /**
     * Recursively flattens complex arrays and splits comma-separated strings at any depth.
     * This method ensures that any input structure is resolved into a simple, flat list.
     *
     * @param mixed $input The raw, potentially nested input.
     * @return array<mixed> A flat array of potential identifiers.
     */
    private function flattenAndSplit(mixed $input): array
    {
        if ($input instanceof BackedEnum) {
            return [(string) $input->value];
        }

        // Case A: A direct SmartPlatform Super-Enum instance.
        if ($input instanceof SmartPlatform) {
            return [$input];
        }

        // Case B: A string, which might be a single identifier or a comma-separated list.
        if (is_string($input)) {
            // Input is a platform-set string: Split it by comma, then trim whitespaces.
            if (str_contains($input, ',')) {
                $parts = array_map('trim', explode(',', $input));
                $result = [];
                // Recursively process each part in case of nested structures within strings (less common but supported).
                foreach ($parts as $part) {
                    $result = array_merge($result, $this->flattenAndSplit($part));
                }
                return array_filter($result); // Filter out and Remove empty strings from ",," scenarios.
            }

            // Input is a single string.
            $trimmed = trim($input);
            return $trimmed !== '' ? [$trimmed] : [];
        }

        // Case C: An array, which can contain any mix of supported types.
        if (is_array($input)) {
            $result = [];
            foreach ($input as $item) {
                // The core of the recursion: process each item and merge the flattened result.
                $result = array_merge($result, $this->flattenAndSplit($item));
            }
            return $result;
        }

        // Case D: A generic object that can be cast to a string.
        if (is_object($input) && method_exists($input, '__toString')) {
            return $this->flattenAndSplit((string) $input);
        }

        // Fallback: Return any other type as-is. The validation logic will catch and report it as an error.
        return [$input];
    }

    /**
     * Dynamically resolves all active platform drivers from the application's configuration.
     * This is triggered only when a wildcard ('*') is used.
     *
     * @return array<string> An array of canonical platform names.
     */
    private function resolveAllConfiguredPlatforms(): array
    {

        // This represents a hardcoded default set of common platforms in the ecosystem.
        $defList = ['rubika', 'bale', 'telegram', 'web'];

        // Use the high-speed generated Platform snapshot if available.
        if (class_exists(Platform::class)) {
            return Platform::$sourceConfig['platforms'] ?? $defList;
        }

        // Else Check if the global 'config' helper function is available (e.g., in a Laravel context).
        if (function_exists('config')) {
            $drivers = config('krubot.drivers', []);
            if (is_array($drivers)) {
                // Exclude the 'aliases' map to ensure we only get the canonical driver configurations.
                unset($drivers['aliases']);
                $platforms = array_keys($drivers);
                if (!empty($platforms)) {
                    return $platforms;
                }
            }
        }

        // A safety net in case the generated class is unavailable or the config is empty.
        return $defList;
    }

    /**
     * Resolves the raw platform/legion names into a final, canonical list of platforms.
     * This is the "brain" of the attribute, interpreting the user's intent
     * against the master configuration file.
     *
     * @return string[] A flat, unique array of canonical platform names (e.g., ['telegram', 'rubika']).
    */
    public function getResolvedPlatforms(): array
    {
        // Memoization: The Sage's wisdom. Resolve only once.
        if ($this->resolvedPlatforms !== null) {
            return $this->resolvedPlatforms;
        }

        if (class_exists(Platform::class)) {

            // Fetch the Architect's Lexicon from the high-speed generated Platform config snapshot.
            $aliases = Platform::$sourceConfig['aliases'] ?? [];
            $legions = Platform::$sourceConfig['legions'] ?? [];

        }
        else {

            // Fetch the Architect's Lexicon from the config.
            $aliases = config('krubot.drivers.aliases', []);
            $legions = config('krubot.legions', []);

        }
        
        $finalPlatforms = [];

        foreach ($this->platforms as $name) {
            // Priority 1: Is it a legion? (e.g., 'all_fronts')
            if (isset($legions[$name])) {
                // If it's a legion, expand it. We must also resolve any aliases *within* the legion.
                $members = Arr::wrap($legions[$name]);
                foreach ($members as $legionMember) {
                    $finalPlatforms[] = $aliases[$legionMember] ?? $legionMember;
                }
                continue;
            }

            // Priority 2: Is it an alias? (e.g., 'tg' -> 'telegram')
            if (isset($aliases[$name])) {
                $finalPlatforms[] = $aliases[$name];
                continue;
            }

            // Priority 3: It's a direct, canonical name or wildcard.
            $finalPlatforms[] = $name;
        }

        // Store the resolved, unique list for future calls.
        return $this->resolvedPlatforms = array_values(array_unique($finalPlatforms));
    }
}
