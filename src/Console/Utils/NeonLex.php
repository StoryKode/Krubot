<?php

declare(strict_types=1);

namespace KrubiK\Console\Utils;

use KrubiK\Arcane\InspectsAppLocale;
use Illuminate\Http\Request;
use KrubiK\Enums\Platform;

// Resolves language, even if laravel translation is not ready to provide service (useful for CLI ScriptZ)
final class NeonLex
{
    use InspectsAppLocale; // of course it Inspects!

    // prevent generate NeonLex instance
    private function __construct()
    {
    }

    /**
     * Loaded translation arrays.
     *
     * @var array<string, array>
     */
    private static array $loaded = [];

    /**
     * Current locale override.
     */
    private static ?string $locale = null;

    /**
     * Translation directory.
     */
    private static ?string $path = null;

    /**
     * Translate a key.
     *
     * Example:
     *
     * NeonLex::fetch('rituals.prompt_agreement');
     * NeonLex::fetch('rituals.prompt_agreement', 'Fallback text');
    */
    public static function fetch(
        string $key,
        ?string $fallback = null,
    ): string {
        if ($key === '') {
            return $fallback ?? '';
        }

        $locale = self::locale();
        $translations = self::load($locale);

        $value = self::resolve($translations, $key);

        if ($value !== null) {
            return $value;
        }

        if ($locale !== self::FALLBACK_LOCALE) {
            $value = self::resolve(
                self::load(self::FALLBACK_LOCALE),
                $key
            );

            if ($value !== null) {
                return $value;
            }
        }

        return $fallback ?? $key;
    }

    /**
     * Short alias.
    */
    public static function get(
        string $key,
        ?string $fallback = null,
    ): string {
        return self::fetch($key, $fallback);
    }

    /**
     * AND EVEN A Shorter, more familiar syntax ;)
    */
    public static function __(
        string $key,
        ?string $fallback = null,
    ): string {
        return self::fetch($key, $fallback);
    }

    /**
     * Set a locale explicitly.
     */
    public static function setLocale(string $locale): void
    {
        self::$locale = self::normalizeLocale($locale);
    }

    /**
     * Forget explicit locale override.
     */
    public static function resetLocale(): void
    {
        self::$locale = null;
    }
    
    /**
     * Get current effective locale.
    */
    public static function locale(
        ?Platform $platform = null,
        ?Request $request = null,
        mixed $driver = null,
    ): string {
        if (self::$locale !== null) {
            return self::$locale;
        }

        // try Environment fallback
        $fallback = self::normalizeLocale(self::extractLocaleFromEnvironment()) ?? self::FALLBACK_LOCALE;

        try {
            if (!function_exists('app')) {
                // Laravel may not be bootstrapped yet.
                return $fallback;
            }

            $app = app();

            if (!$app instanceof Application) {
                // Laravel may not be bootstrapped yet.
                return $fallback;
            }

            /*
            |--------------------------------------------------------------------------
            | Resolve Platform
            |--------------------------------------------------------------------------
            */

            $platform ??= (
                (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg')
                    ? Platform::CLI()
                    : Platform::Web()
            );

            /*
            |--------------------------------------------------------------------------
            | Try to Resolve Request if Laravel has one
            |--------------------------------------------------------------------------
            */

            if (
                $request === null
                && $app->bound('request')
            ) {
                try {
                    $request = $app->make('request');

                    if (!$request instanceof Request) {
                        $request = null;
                    }
                } catch (\Throwable) {
                    $request = null;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | USE InspectsAppLocale
            |--------------------------------------------------------------------------
            |
            | This is the important part.
            |
            | The locale resolution is delegated directly to the Arcane.
            |
            */

            if ($request !== null) {
                $locale = self::extractLocaleFromRequest(
                    $app,
                    $request,
                    $platform,
                    $driver
                );

                if (
                    is_string($locale)
                    && trim($locale) !== ''
                ) {
                    return self::normalizeLocale($locale);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CLI / no Request
            |--------------------------------------------------------------------------
            |
            | extractLocaleFromRequest() requires Request,
            | so for CLI we use the other method from the SAME Trait.
            |
            */

            $locale = self::extractLocaleFromConfig(
                $platform
            );

            if (
                is_string($locale)
                && trim($locale) !== ''
            ) {
                return self::normalizeLocale($locale);
            }

            /*
            |--------------------------------------------------------------------------
            | Laravel fallback
            |--------------------------------------------------------------------------
            */

            $locale = $app->getLocale();

            if (
                is_string($locale)
                && trim($locale) !== ''
            ) {
                return self::normalizeLocale($locale);
            }

        } catch (\Throwable) {
            // Locale resolution must never break translation.
        }

        return $fallback;
    }

    /**
     * Determine whether a translation exists.
     */
    public static function has(string $key): bool
    {
        if ($key === '') {
            return false;
        }

        $locale = self::locale();

        if (
            self::resolve(
                self::load($locale),
                $key
            ) !== null
        ) {
            return true;
        }

        return $locale !== self::FALLBACK_LOCALE
            && self::resolve(
                self::load(self::FALLBACK_LOCALE),
                $key
            ) !== null;
    }

    /**
     * Get the entire locale file.
     */
    public static function all(?string $locale = null): array
    {
        return self::load(
            self::normalizeLocale(
                $locale ?? self::locale()
            )
        );
    }

    /**
     * Define a custom language directory.
     */
    public static function path(string $path): void
    {
        self::$path = rtrim($path, '/\\');

        /*
        | A new path means old loaded arrays may no longer be valid.
        */
        self::$loaded = [];
    }

    /**
     * Clear in-memory language cache.
     */
    public static function flush(): void
    {
        self::$loaded = [];
    }
    
    /**
     * Load a language file exactly once per process.
     */
    private static function load(string $locale): array
    {
        if (isset(self::$loaded[$locale])) {
            return self::$loaded[$locale];
        }

        $path = self::languageFile($locale);

        if (!is_file($path)) {
            return self::$loaded[$locale] = [];
        }

        try {
            $data = require $path;
        } catch (\Throwable) {
            return self::$loaded[$locale] = [];
        }

        return self::$loaded[$locale] =
            is_array($data) ? $data : [];
    }

    /**
     * Resolve dot notation.
     */
    private static function resolve(
        array $translations,
        string $key,
    ): ?string {
        $value = $translations;

        foreach (explode('.', $key) as $segment) {
            if (
                !is_array($value)
                || !array_key_exists($segment, $value)
            ) {
                return null;
            }

            $value = $value[$segment];
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return null;
    }

    private static function getLanguagePath(): string
    {
        return __DIR__ . '/../../../lang';
    }

    /**
     * Resolve language file.
     */
    private static function languageFile(string $locale): string
    {
        $directory = self::$path ?? self::getLanguagePath();

        return $directory
            . DIRECTORY_SEPARATOR
            . $locale
            . '.php';
    }
}
