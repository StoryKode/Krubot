<?php

namespace KrubiK\Arcane;
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

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\ClassLoader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;
use KrubiK\Facades\Opcache;

trait PlatformConstantsRobustGen
{
    /**
     * The absolute path to the directory where generated files are stored.
     * @var string
    */
    private string $generatedPlatformsDir;

    /**
     * The absolute path to the generated Platform constants class.
     * @var string
    */
    private string $generatedPlatformsFilePath;

    /**
     * Registers the generated platforms by locking, validating OPcache,
     * checking for config updates, and generating if necessary.
    */
    public function registerPlatformConstants(): void
    {
        if(!config('krubot.cache.platform-constants-generation', true))
            return;

        $this->setupGPlatformPaths();

        // --- THE ELEGANT GUARDIAN'S GATE: USING LARAVEL'S ATOMIC LOCKS ---
        // This is the superior, framework-native, driver-agnostic way to prevent race conditions.
        $lock = Cache::lock('krubot:platform-generation-lock', 60); // Lock for a max of 60 seconds

        // The get() method will attempt to acquire the lock. If successful, it
        // executes the closure and then automatically releases the lock.
        // If it can't acquire the lock, it does nothing.
        // This is an atomic, safe, and elegant operation.
        $lock->get(function () {
            // --- WE ARE THE CHOSEN SCRIBE (and we didn't have to get our hands dirty) ---
            // This closure only executes if we successfully acquired the lock.

            // We must clear the OPcache compiled version of the file *before* we run any checks
            Opcache::invalidate($this->generatedPlatformsFilePath);

            // The check is now much faster and more integrated.
            if ($this->needsUpdateGeneratedPlatforms()) {
                $this->generatePlatformConstants();
            }

            // Immediately after generating, we compile the fresh file into OPcache.
            // This ensures subsequent requests hit the memory cache immediately,
            // maintaining O(1) class loading speed.
            Opcache::compile($this->generatedPlatformsFilePath);
        });
        
        // This part runs for EVERY request, after the lock attempt.
        // It ensures the class is available for the application to use.
        $this->registerGeneratedClassAutoloader();
    }

    /**
     * Sets up the necessary paths for generated files.
     * --- UPDATED: Meta file path is removed. ---
     */
    private function setupGPlatformPaths(): void
    {
        $this->generatedPlatformsDir = app_path('Generated');
        $this->generatedPlatformsFilePath = $this->generatedPlatformsDir . '/Platform.php';
    }

    /**
     * Checks if the Platform class needs regeneration by comparing live config
     * with the config snapshot embedded within the generated file itself.
     * --- COMPLETELY REWRITTEN ---
     * --- UPDATED to include aliases in the comparison. ---
     */
    private function needsUpdateGeneratedPlatforms(): bool
    {
        // Condition 1: If the generated file doesn't exist, we must generate it.
        if (! file_exists($this->generatedPlatformsFilePath)) {
            return true;
        }

        // We can safely include the file. It's either up-to-date or will be overwritten.
        // Using require_once is safe and leverages opcache for maximum speed on subsequent requests.
        require_once $this->generatedPlatformsFilePath;
        $generatedClass = 'App\\Generated\\Platform';

        // Condition 2: Defensive check in case the file is corrupt or empty.
        if (! property_exists($generatedClass, 'sourceConfig')) {
            return true;
        }

        // Load the live source-of-truth configuration.
        // --- NEW: Fetching aliases as part of the source of truth ---
        $driversConfig = $this->app['config']->get('krubot.drivers', []);
        $legionsConfig = $this->app['config']->get('krubot.legions', []);
        
        $currentConfig = [
            'platforms' => is_array($driversConfig) ? array_keys(Arr::except($driversConfig, ['aliases'])) : [],
            'legions'   => is_array($legionsConfig) ? $legionsConfig : [],
            'aliases'   => $driversConfig['aliases'] ?? [], // Aliases are now part of the check!
        ];

        // Sort keys recursively to ensure comparison is not affected by order.
        $this->sortConfigRecursively($currentConfig);

        // Retrieve the cached config from the generated file itself.
        $cachedConfig = $generatedClass::$sourceConfig;
        $this->sortConfigRecursively($cachedConfig);

        // Condition 3: The core logic. A fast, in-memory comparison of the arrays.
        // Now accounts for platforms, legions, AND aliases.
        // If the live config doesn't match the cached config, so regeneration is needed.
        return $currentConfig !== $cachedConfig;
    }

    /**
     * Generates the `Platform.php` constants class.
     * --- UPDATED to fetch and pass aliases to the builder. ---
     */
    private function generatePlatformConstants(): void
    {
        $files = new Filesystem();
        $files->ensureDirectoryExists($this->generatedPlatformsDir);

        $driversConfig = $this->app['config']->get('krubot.drivers', []);
        $legionsConfig = $this->app['config']->get('krubot.legions', []);

        $platforms = is_array($driversConfig) ? array_keys(Arr::except($driversConfig, ['aliases'])) : [];
        $legions = is_array($legionsConfig) ? $legionsConfig : [];
        $aliases = $driversConfig['aliases'] ?? [];

        // --- NEW: Pass aliases to the content builder ---
        $content = $this->buildGPlatformsContent($platforms, $legions, $aliases);
        
        $files->put($this->generatedPlatformsFilePath, $content);
    }

    /**
     * Builds the final string content for the `Platform.php` class.
     * --- HEAVILY UPDATED to generate alias constants and the new sourceConfig. ---
    */
    private function buildGPlatformsContent(array $platforms, array $legions, array $aliases): string
    {
        // --- Section 1: Canonical Platform Constants ---
        $platformConsts = collect($platforms)
            ->map(fn(string $name) => "    public const {$this->formatIdentifierAsConstantName($name)} = '{$name}';")
            ->implode("\n");

        // --- Section 2: Alias Constants ---
        $aliasConsts = collect($aliases)
            ->map(fn(string $canonical, string $alias) => "    public const {$this->formatIdentifierAsConstantName($alias)} = '{$canonical}';")
            ->implode("\n");

        // --- Section 3: Legion Constants ---
        $legionConsts = collect(array_keys($legions))
            ->map(fn(string $name) => "    public const Legion{$this->formatIdentifierAsConstantName($name)} = '{$name}';")
            ->implode("\n\n");
            
        // --- Section 4: The Self-Validation Snapshot ---
        $sourceConfigForExport = "[\n"
            . "        'platforms' => " . $this->formatListForPhpExport($platforms) . ",\n"
            . "        'legions'   => " . $this->formatListForPhpExport($legions) . ",\n"
            . "        'aliases'   => " . $this->formatAssocArrayForPhpExport($aliases) . ",\n"
            . "    ]";
        
        $date = now()->toIso8601String();

        return <<<PHP
<?php

// THIS FILE IS AUTO-GENERATED BY KrubotServiceProvider. DO NOT EDIT.
// Generated on: {$date}
// Source: config/krubot.php

namespace App\\Generated;
use KrubiK\Enums\Platform as SmartPlatform;


/**
 * Provides compile-time safe constants for all platforms, legions, and aliases.
 * This class is automatically regenerated when config/krubot.php changes.
 * It contains its own source configuration for hyper-fast, in-memory validation.
 */
final class Platform
{
    /**
     * A snapshot of the configuration used to generate this file.
     * @var array
     */
    public static array \$sourceConfig = {$sourceConfigForExport};

    // -- Universal Wildcard --
    public const All = '*';

    // -- Canonical Platforms --
{$platformConsts}

    // -- Platform Aliases --
{$aliasConsts}

    // -- Legion Identifiers --
{$legionConsts}

    /**
     * A flexible, Brainless proxy to the core SmartPlatform::matches() logic.
     * It intelligently handles various input types to maximize developer experience (DX).
     *
     * @param string|Stringable|SmartPlatform \$value The platform identifier to check.
     * @param mixed ...\$platforms The platform constants, legion constants, or arrays to match against.
     * @return bool True if a match is found, false otherwise.
     */
    public static function matches(string|Stringable|SmartPlatform \$value, ...\$platforms): bool
    {
        /** @var SmartPlatform|null \$resolvedInstance */
        \$resolvedInstance = null;

        // Fast-path optimization: If a SmartPlatform instance is already provided,
        // we can use it directly without any lookup overhead.
        if (\$value instanceof SmartPlatform) {
            \$resolvedInstance = \$value;
        } 
        // Fallback path: If the input is a string or a Stringable object,
        // we attempt to resolve it into a SmartPlatform instance.
        else {
            \$resolvedInstance = SmartPlatform::tryFrom((string) \$value);
        }

        // Guard clause: If the provided value could not be resolved to a valid
        // platform (e.g., an invalid string), we cannot perform a match.
        if (\$resolvedInstance === null) {
            return false;
        }

        // Delegation: Forward the call to the actual logic within the resolved
        // SmartPlatform instance, passing all check-arguments transparently.
        return \$resolvedInstance->matches(...\$platforms);
    }

    public static function tryFrom(string \$value): ?SmartPlatform
    {
        return SmartPlatform::tryFrom(\$value);
    }

    /**
     * Checks if an identifier points to a legion.
     *
     * @param string \$identifier The name of the potential legion.
     * @return bool
     */
    public static function isLegion(string \$identifier): bool
    {
        return isset(self::\$sourceConfig['legions'][\$identifier]);
    }

    /**
     * Retrieves the members of a given legion, if it exists.
     * The members are returned as-is from the config (can be aliases or canonical names).
     *
     * @param string \$legionName The name of the legion to look up.
     * @return array<string>|null The array of member identifiers, or null if the legion doesn't exist.
     */
    public static function getLegionMembers(string \$legionName): ?array
    {
        return self::\$sourceConfig['legions'][\$legionName] ?? null;
    }
}
PHP;
    }

    // --- HELPER METHODS ---
    
    /**
     * --- NEW HELPER ---
     * Formats an associative array into a modern, short-syntax PHP array string.
     * @param string[] \$map AssocArray
     * @return string A string representing the array in PHP code.
    */
    private function formatAssocArrayForPhpExport(array $assocArray): string
    {
        if (empty($assocArray)) return '[]';
        $items = collect($assocArray)->map(fn($value, $key) => "'{$key}' => '{$value}'");
        return '[' . $items->implode(', ') . ']';
    }
    

    /**
     * Formats a simple list of strings (numerically indexed array) into a modern, short-syntax PHP array string.
     * e.g., ['a', 'b'] becomes "['a', 'b']"
     * @param string[] \$list A flat array of strings.
     * @return string A string representing the array in PHP code.
     */
    private function formatListForPhpExport(array $list): string
    {
        if (empty($list)) return '[]';
        sort($list); // Ensure consistent order

        // 1. Wrap each string item in single quotes.
        $quotedItems = collect($list)->map(fn(string $item) => "'{$item}'");

        // 2. Join them with a comma and a space AND Wrap the whole thing in square brackets..
        return '[' . $quotedItems->implode(', ') . ']';
    }

    /**
     * Sorts the configuration array recursively to ensure consistent ordering for comparison.
    */
    private function sortConfigRecursively(array &$array): void
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->sortConfigRecursively($value);
            }
        }
        ksort($array);
    }

    /**
     * Dynamically registers the PSR-4 autoloader for our generated class.
     * --- CORRECTED ---
    */
    private function registerGeneratedClassAutoloader(): void
    {
        if (file_exists($this->generatedPlatformsFilePath)) {
            // CHANGE: The namespace points to the generated classes directory.
            // This allows us to have KrubiK\Generated\Platform class.

            // This is the clean, modern way to add a dynamic autoloader path in Laravel.
            $this->app->make(ClassLoader::class)
                ->addPsr4('App\\Generated\\', $this->generatedPlatformsDir);
        }
    }

    /**
     * --- THE ART OF NAMING CONVENTION (THE DEFINITIVE BULLETPROOF VERSION) ---
     * Converts a configuration identifier into a perfectly formatted constant name.
     * This version includes a critical exception for 'app' to prevent it from
     * being incorrectly converted to 'APP' by the short-name rule.
     *
     * Rules Hierarchy:
     * 1. [Unified Initialism]: Handles patterns like 'tg_pro' -> 'TGPro' OR 'r-main' -> 'RMain'.
     * "Initialism" Style: If it starts with exactly 2 letters followed by a non-letter
     *    (e.g., 'TG2', 'bl_11', 'rb33333'), the entire string becomes UPPERCASE.
     *
     * 2. [Intelligent Short-name]: Uppercases identifiers of 3 chars or less
     *    (like 'api' -> 'API'), but *explicitly excludes 'app'* to let it be
     *    handled by the PascalCase rule. THIS IS THE KEY FIX.
     *
     * 3. [Default PascalCase]: The fallback rule, which correctly handles 'app' -> 'App'
     *    and also manages suffixes like 'webapp' -> 'WebApp'.
     *
     * @param string $identifier The platform or alias name from the config.
     * @return string The formatted, compile-time safe constant name.
     */
    private function formatIdentifierAsConstantName(string $identifier): string
    {
        $v14Formula = false;
        // Rule 1: The unified initialism rule.
        // "Initialism" check using regex.
        // Pattern: ^[a-zA-Z]{2}[^a-zA-Z]
        //          ^         - Start of the string
        //          [a-zA-Z]{2} - Exactly two alphabetic characters
        //          [^a-zA-Z] - A single character that is NOT an alphabet letter
        if (preg_match('/^([a-zA-Z]{1,2})([^a-zA-Z].*)/', $identifier, $matches)) {
            // $matches[1] is the initialism (e.g., 'Tg','tg' -> 'TG')
            $initialism = strtoupper($matches[1]);

            // $matches[2] Take the rest of the string, including the separator (e.g., '_pro' or '114' or '_office')
            $rest = $matches[2];

            if($v14Formula) {
                // Convert the rest of the string to standard PascalCase.
                // This intelligently handles both '_office' -> '_Office' and '114' -> '114'.
                $pascalCasedRest = preg_replace_callback('/[_-]([a-zA-Z])/', fn($m) => strtoupper($m[1]), $rest);
                $pascalCasedRest = str_replace(['_', '-'], '', $pascalCasedRest);
            }


            else {
                // Convert the ENTIRE rest of the string to PascalCase.
                // This correctly handles the separator by using it as a word boundary
                // and then discarding it. e.g., ucwords('_pro', '_-') becomes '_Pro'.
                // The str_replace then removes the leading separator.
                $pascalCasedRest = str_replace(['_', '-'], '', ucwords($rest, '_-'));
            }

            // Concatenate them directly, without any separator.
            return $initialism . $pascalCasedRest;
        }

        // Rule 2: Handle general short names, WITH a critical exception for 'app'.
        // This catches anything 3 chars or less that didn't match Rule 1.
        if (mb_strlen($identifier) <= 3 && strtolower($identifier) !== 'app') {
            return strtoupper($identifier);
        }

        // Rule 3: Default to PascalCase for everything else, also handling kebab-case.
        $pascalCased = str_replace(['_', '-'], '', ucwords($identifier, '_-'));

        // The special 'App' suffix sub-rule.
        if (str_ends_with(strtolower($pascalCased), 'app')) {
            // This handles 'webapp' -> 'WebApp' and now also 'app' -> 'App'
            return substr($pascalCased, 0, -3) . 'App';
        }

        return $pascalCased;
    }
}
