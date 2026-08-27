<?php

namespace KrubiK\Enums;
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

use Stringable; // Import the interface
use BadMethodCallException;
use ValueError;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Sacred 'Platform' 2.0 / MultiCase-Class (First Origin of FusionPrimes)
 *
 * The Dynamic, Config-driven "Enum-like" Platform class.
 *
 * IT DYNAMICALLY LEARNS FROM YOUR `config/krubot.php` FILE!
 *
 * This is not a native PHP Enum. It is a more powerful, dynamic class
 * that simulates an Enum's behavior with 100% identical DX.
 *
 * - Boots lazily from config('krubot.drivers.aliases')
 * - Supports case-insensitive static calls for aliases and canonical names:
 *      Platform::r(), Platform::R(), Platform::Rubika(), Platform::tg(), Platform::TG(), ...
 * - Supports shorthand for default: Platform::def(), Platform::Default()
 *
 * Usage:
 *   Platform::r() === Platform::Rubika(); // true
 *   (string) Platform::tg() === 'telegram'; 
 *
 * The single source of truth for all driver identities in the multiverse.
 * Eliminates magic strings, provides type-safety, and boosts IDE autocompletion.
 *
 * Now implements Stringable for the ultimate Developer Experience.
 * Allows direct usage in string contexts without calling ->value.
 *
 * How to use:
 * $platform = Platform::Rubika();
 * echo $platform; // 'rubika'
 * $platform === Platform::Rubika(); // true
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
final class Platform implements Stringable
{
    /** 
     * @var array<string,string> aliasKey(lowercase) => canonicalName(lowercase) 
     *
     * This holds the final, normalized map of all aliases pointing to their canonical names.
     * It's populated either from the generated class or the config file.
    */
    private static array $aliasMap = [];

    /**
     * 🗺️ The map of legion names to their member identifiers.
     * @var array<string,array<string>> legionName(lowercase) => members
    */
    private static array $legionsMap = [];

    /**
     * ✨ A flag indicating if we successfully booted from the hyper-fast generated class.
     * @var bool
    */
    private static bool $loadedFromGenerated = false;

	/**
     * 🧠 The Singleton Instance Registry.
     * Stores the single instance of each platform.
     * Key: 'rubika', Value: Platform object for Rubika.
	 * @var array<string,self> canonicalName => instance
	*/
    private static array $instances = [];

    /**
     * The flag to ensure we only boot once.
    */
	private static bool $booted = false;

    /**
     * The actual canonical string value of the platform (e.g., 'rubika').
    */
	private readonly string $value;

    /**
     * The constructor is private to enforce the Singleton pattern.
     * No one can create a new instance from outside.
    */
	private function __construct(string $canonical)
    {
        $this->value = $canonical;
    }

	/**
     * 🚀 The Bootstrapper.
     * This is the magic that reads from your config file OR the hyper-fast generated class.
     * It runs only ONCE, the very first time a Platform is requested.
	 * Boot once, Load aliases from faster-source, and Normalize them.
     * 
     * Priority Order:
     * 1. Attempts to load from `App\Generated\Platform` for O(1) speed via OPcache.
     * 2. If the generated class is not found or empty, it falls back to reading from `config/krubot.php`.
    */
    private static function boot(): void
    {
        if (self::$booted) return;

        // --- Stage 1: The Quantum Leap - Attempt to load from the Generated Class ---
        $aliases = [];
        $legions = [];
        $generatedClass = 'App\\Generated\\Platform';

        // Check if the generated class exists and has the data property, this is extremely fast.
        if (class_exists($generatedClass) && property_exists($generatedClass, 'sourceConfig')) {
            // Access the static property which contains the snapshot of the config.
            $source = $generatedClass::$sourceConfig;
            
            // Extract aliases and legions safely.
            $potentialAliases = $source['aliases'] ?? [];
            $potentialLegions = $source['legions'] ?? [];

            // Only proceed if the generated source is valid (has aliases).
            if (is_array($potentialAliases) && !empty($potentialAliases)) {
                $aliases = $potentialAliases;
                $legions = is_array($potentialLegions) ? $potentialLegions : [];
                self::$loadedFromGenerated = true; // Set the success flag!
            }
        }

        // --- Stage 2: The Fallback - Load from Laravel Config ---
        // This block only runs if the quantum leap failed.
        if (!self::$loadedFromGenerated) {
            // Get and Read the list of all canonical platform names & aliases from the config.
            // result expected: ['r'=>'rubika', 'tg'=>'telegram', ...]
            $aliases = config('krubot.drivers.aliases', []);
            $legions = config('krubot.legions', []);
        }

        // normalize: lower-case keys and values
        $map = [];
        foreach ($aliases as $key => $val) {
            if (!is_string($key) || !is_string($val)) continue;
            $map[strtolower($key)] = strtolower($val);
        }

        // store map
        self::$aliasMap = $map;

        // Normalize legions: lower-case keys.
        foreach ($legions as $key => $val) {
            if (!is_string($key) || !is_array($val)) continue;
            self::$legionsMap[strtolower($key)] = $val;
        }

        // Ensure canonical set includes the canonical keys (values of aliases)
        $canonicals = array_unique(array_values(self::$aliasMap));

        // Create a singleton instance for each canonical platform.
        foreach ($canonicals as $canonical) {
            self::$instances[$canonical] = new self($canonical);
        }

        // Also ensure that canonical names map to themselves (allow Platform::Rubika())
        foreach (array_keys(self::$instances) as $canonical) {
            self::$aliasMap[$canonical] = $canonical;
        }

        self::$booted = true;
    }

    /**
     * Ensures the class is booted before any operation.
    */
    private static function ensureBooted(): void
    {
        if (!self::$booted) self::boot();
    }

    /**
     * 🎩 The Magic Static Caller.
     * This method is triggered when you call a static method that doesn't exist,
     * like `Platform::Rubika()` or `Platform::Telegram()`, Platform::r(), Platform::Rubika(), Platform::TG(), Platform::Def(), ...
     *
     * - Case-insensitive
     * - If name is 'default'|'def' -> resolve from config('krubot.default_driver')
	 *
     * @param string $name The name of the case (e.g., "Rubika").
     * @return self
    */
    public static function __callStatic(string $name, array $args): self
    {
        self::ensureBooted();

        $lower = strtolower($name); // 'Rubika' -> 'rubika'

        // Handle default aliases
        if (in_array($lower, ['default', 'def', 'd'], true)) {
            $default = strtolower((string) config('krubot.default_driver', 'rubika'));
            return self::fromOrCreate($default);
        }

        // Direct lookup in alias map (case-insensitive keys)
        if (isset(self::$aliasMap[$lower])) {
            $canonical = self::$aliasMap[$lower];
            return self::fromOrCreate($canonical);
        }

        // Try treating the provided name as canonical (lowercased)
        if (isset(self::$instances[$lower])) {
            return self::$instances[$lower];
        }

        throw new BadMethodCallException("Platform [{$name}] is not defined in your `krubot.php` config's aliases and is not a known platform.");
    }

    /** Helper that returns existing instance or creates a new one (defensive) */
    private static function fromOrCreate(string $canonical): self
    {
        $canonical = strtolower($canonical);
        if (!isset(self::$instances[$canonical])) {
            // lazily create (covers case where config aliases didn't include explicit canonical list)
            self::$instances[$canonical] = new self($canonical);
            // also map canonical to itself for future calls
            self::$aliasMap[$canonical] = $canonical;
        }
        return self::$instances[$canonical];
    }

    /**
     * The modern `from` method, like a real enum.
	 * Behaves like enum::from — accepts canonical or any alias (case-insensitive)
     * @throws ValueError
    */
    public static function from(string $value): self
    {
        $instance = self::tryFrom($value);
        if ($instance === null) {
            throw new ValueError("\"{$value}\" is not a valid backing value for Platform Enum");
        }
        return $instance;
    }

	/**
     * The modern `cases` method, like a real enum.
	 *
	 * Return all cases (instances)
     * @return array<int, self>
    */
    public static function cases(): array
    {
        self::ensureBooted();
        return array_values(self::$instances);
    }

    /**
     * Returns the default platform for the entire application.
     * Centralizes the fallback logic.
     *
     * @return self
     */
    public static function default(): self
    {
        self::ensureBooted();
        $default = strtolower((string) config('krubot.default_driver', 'rubika')); // Return default platform instance from config
        return self::fromOrCreate($default);
    }
    
    /**
     * Tactical method to produce the wildcard token.
     *
     * @return string
     */
    public static function all(): string
    {
        return '*';
    }

    /** Get canonical string */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Checks if this platform instance matches any of the provided platforms.
     * This method is the epitome of Developer Experience (DX):
     * - Accepts multiple arguments (variadic OR).
     * - Accepts a single array of platforms.
     * - Accepts Platform instances (`Platform::Rubika()`), canonical string names (`'rubika'`), or aliases (`'r'`, `'tg'`).
     * - Extremely performant with early exit on first match.
     *
     * @param mixed ...$platforms A list of platforms to check against. Can be Platform instances, strings, or an array|Arrayable of these.
     * @return bool True if this instance matches any of the given platforms, false otherwise.
     *
     * @example
     * $currentPlatform = Platform::Rubika();
     * $currentPlatform->matches(Platform::Rubika()); // true
     * $currentPlatform->matches(Platform::Telegram()); // false
     * $currentPlatform->matches('r'); // true
     * $currentPlatform->matches('TG', Platform::WebApp()); // false
     * $currentPlatform->matches(Platform::Telegram(), 'rubika'); // true
     * $currentPlatform->matches(['webapp', 'bale', 'r']); // true
     * $currentPlatform->matches(collect(['webapp', 'r'])); // true
     */
    public function matches(...$platforms): bool
    {
        // DX Enhancement: Allow passing a single array or an Arrayable object (like a Laravel Collection), instead of sending multiple arguments.
        // This normalizes the input into a simple, flat array for processing.
        // e.g., matches(['r', 'tg']) instead of matches('r', 'tg').
        if (count($platforms) === 1) {
            $firstItem = $platforms[0];
            if ($firstItem instanceof Arrayable) {
                // If it's an Laravel Arrayable, convert it to an array.
                $platforms = $firstItem->toArray();
            } elseif (is_array($firstItem)) {
                // If it's already an array, just unpack it.
                $platforms = $firstItem;
            }
        }

        // Performance: Store the canonical value of the current instance once.
        $thisValue = $this->value;

        // Iterate through each provided platform to check for a match.
        foreach ($platforms as $platformToCheck) {

            // Case 1: The provided item is another Platform instance.
            // This allows for type-safe comparisons.
            if ($platformToCheck instanceof self) {
                // Direct comparison of canonical values is the most reliable and fastest check.
                // (Using `===` on the object itself would also work due to the singleton pattern,
                // but comparing the primitive string value is arguably more explicit and robust).
                if ($thisValue === $platformToCheck->value) {
                    return true; // Early exit on match.
                }
                continue; // Move to the next item if it's not a match.
            }
            
            // Case 2: The provided item is a string (alias or canonical name).
            // This is the most common use case.
            if (is_string($platformToCheck)) {
                // Use the highly-optimized `tryFrom` to resolve the string to a Platform instance.
                // It's fast because it uses the pre-booted static alias map.
                $resolvedPlatform = self::tryFrom($platformToCheck);

                // If resolution is successful and the canonical values match, we found it.
                // Return true immediately for maximum performance (early exit).
                if ($resolvedPlatform !== null && $thisValue === $resolvedPlatform->value) {
                    return true;
                }
            }

            // Other data types (int, null, etc.) are ignored.
        }

        // If the loop completes without finding any matches, return false.
        return false;
    }


    /**
     * The Hyper-DX Modern `tryFrom` method, like a real enum, but on the space steroids.
     *
     * `tryFrom` Intelligently attempts to resolve a Platform instance from various common data types,
     * making it extremely versatile and boosting Developer Experience within a Laravel application.
     * This method is fully backward-compatible.
     *
     * It follows a specific, performance-oriented order of operations:
     * 1. `self` (Platform instance)   :: **Idempotency Check:** If a `Platform` instance is passed, it's returned immediately.
     * 2. `null`                       :: `null` input results in `null` output.     
     * 3. `BackedEnum` (Native PHP)    :: **Native Enum Support:** Extracts backing scalar value (string|int) from native PHP enums.
     * 4. `Stringable`                 :: Handles `\Stringable` && `Illuminate\Support\Stringable` objects.
     * 5. `string`                     :: **String:** MAIN_METHOD. Processes raw strings (Aliases, Canonical names, or resolve first item of Legions).
     * 6. `array` | `LaravelArrayable` :: (e.g., Laravels Collections, ...) Searches recursively and Resolves first matching element.
     * 7. `Model` (Laravel Eloquent)   :: Checks for `Model` instances and extracts the platform from a `platform` or `platform_name` attribute.
     * 8. `Request`(LaravelHTTPRequest):: Checks `Http\Request` instances for a `platform` value in the route parameters or input data.
     * 9. Any other type will result in `null`.
     *
     * @param mixed $value The value to resolve. Can be a `Platform` instance, `null`, a string ('r', 'rubika'),
     *                     an Eloquent Model, a Laravel Request, or a Stringable object.
     * @return self|null Returns a `Platform` instance on success, or `null` on failure.
     */
    public static function tryFrom(mixed $value): ?self
    {
        // Step 0: Ensure the class is booted. This is a cheap check.
        // It populates the static maps (`$aliasMap`, `$instances`) which are critical for what follows.
        self::ensureBooted();

        // Step 1: Idempotency & Null check. The fastest paths.
        // If the input is already the correct type or null, we can exit immediately.
        // This is a massive performance win for common cases.
        if ($value instanceof self || $value === null) {
            return $value;
        }

        // Step 2: Native PHP BackedEnum support (Just in Case!).
        // If a native PHP BackedEnum is passed, extract its backing scalar value (string or int).
        // This allows seamless integration with native domains.
        if ($value instanceof \BackedEnum) {
            $value = $value->value;
        }

        // Step 3: Stringable & Laravel Stringable support.
        // Handles `Str::of('...')` objects seamlessly.
        if ($value instanceof \Stringable || $value instanceof \Illuminate\Support\Stringable) {
            // The object's contract guarantees it can be cast to a string.
            // We then process that string in the next step.
            $value = (string) $value;
        }

        // Step 4: Core String-based resolution (The #_Main_# logic).
        // This block only executes if the value is a string or has been coerced into one.
        if (is_string($value)) {
            // Normalize the string: lowercase and trim whitespace for robustness.
            $key = strtolower(trim($value));

            // Instantly return if key is empty string after trim.
            if ($key === '') {
                return null;
            }

            // Primary Lookup: Check the alias map. This is the most common path for aliases like 'r' or 'tg'.
            // The map is pre-computed during boot for O(1) complexity.
            if (isset(self::$aliasMap[$key])) {
                // `fromOrCreate` is a safe helper that returns the singleton instance.
                return self::fromOrCreate(self::$aliasMap[$key]);
            }

            // Secondary Lookup: If not in aliases, check if it's a direct canonical name.
            // This covers cases where `Platform::tryFrom('rubika')` is called.
            if (isset(self::$instances[$key])) {
                return self::$instances[$key];
            }

             // Legion Lookup: Check if the string points to a group of platforms (Legion)
            if (self::isLegion($key)) {
                $members = self::getLegionMembers($key);
                if (!empty($members)) {
                    return self::tryFrom($members); // delegates to the array processor below
                }
                return null;
            }
        }

        // Step 5: Array / Collection / Arrayable support (Recursive resolution)
        // Loops through nested structures and returns the very first one that successfully resolves.
        if (is_array($value) || $value instanceof \Illuminate\Contracts\Support\Arrayable) {
            $items = $value instanceof \Illuminate\Contracts\Support\Arrayable ? $value->toArray() : $value;
            
            foreach ($items as $item) {
                if($resolved = self::tryFrom($item))
                    return $resolved; // Early exit on the first successful resolution.
            }
            return null;
        }

        // Step 6: Laravel Eloquent Model integration.
        // This allows developers to pass a model instance directly.
        // e.g., Platform::tryFrom($user) where $user->platform = 'rubika';
        if ($value instanceof \Illuminate\Database\Eloquent\Model) {
            // Check for a 'platform' property/attribute first, as it's the most likely candidate.
            // If it exists, recursively call tryFrom with its value. This supports nested resolutions
            // (e.g., the property could be another Platform instance or a string alias).
            // The `??` operator provides a clean fallback to check 'platform_name'.
            $platformIdentifier = $value->platform ?? $value->platform_name ?? null;

            if ($platformIdentifier !== null) {
                return self::tryFrom($platformIdentifier); // Recursive call with the extracted value.
            }
        }

        // Step 7: Laravel HTTP Request integration.
        // Extremely useful in controllers or middleware. Allows resolving the platform
        // directly from the incoming request context.
        if ($value instanceof \Illuminate\Http\Request) {
            // We prioritize the route parameter ('/api/{platform}/...'), as it's more explicit.
            // Then we fall back to a general input field (form data or JSON body).
            $platformIdentifier = $value->route('platform') ?? $value->input('platform') ?? null;
            
            if ($platformIdentifier !== null) {
                return self::tryFrom($platformIdentifier); // Recursive call with the extracted value.
            }
        }

        // Step 8: If all checks fail, the value is not a resolvable platform.
        return null;
    }

    /**
     * Checks if an identifier points to a legion.
     * This now uses the in-memory map populated during boot.
     *
     * @param string $identifier The name of the potential legion.
     * @return bool
    */
    public static function isLegion(string $identifier): bool
    {
        self::ensureBooted();
        return isset(self::$legionsMap[strtolower($identifier)]);
    }

    /**
     * Retrieves the members of a given legion, if it exists.
     * This method is case-insensitive and reads from in-memory data or fallbakcs to the config data.
     * loaded during the class's boot process.
     *
     * @param string $legionName The name of the legion to look up (e.g., 'all_fronts').
     * @return array<string>|null The array of member identifiers, or null if the legion doesn't exist.
     */
    public static function getLegionMembers(string $legionName): ?array
    {
        // Ensures that the configuration, including legions, is loaded before proceeding.
        // This is crucial for the lazy-loading architecture of this SmartEnum.
        self::ensureBooted();

        // Performs a case-insensitive lookup in the pre-populated and normalized legions map.
        // Returns null if the key is not found, matching the required behavior.
        return self::$legionsMap[strtolower($legionName)] ?? null; // ensureBooted() Fills-In:: `$legionsMap = config('krubot.legions', [])`
    }

    /**
     * Magic method that makes this Enum behave like a string.
     * This is the heart of the DX improvement.
     *
     * The magic __toString method for Stringable interface.
     * Allows `echo Platform::Bale;` to print 'bale'.
     *
     * Stringable: when cast to string, return canonical
     * @return string
    */
    public function __toString(): string // 👈 3. Define the conversion logic
    {
        return $this->value;
    }
}
