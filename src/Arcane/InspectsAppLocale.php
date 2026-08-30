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

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use KrubiK\Enums\Platform;
use KrubiK\WebApps\UniversalIdentity;

/**
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait InspectsAppLocale
{

    /**
     * Default fallback locale.
    */
    protected const FALLBACK_LOCALE = 'en';

    /**
     * Extract and normalize the system/server environment locale for telemetry and diagnostics.
     * Aligns with context injection rules to capture host fallback telemetry.
     * 
     * @return string|null
    */
    protected static function extractLocaleFromEnvironment(): ?string
    {
        foreach ([
            getenv('APP_LOCALE'),
            getenv('LANG'),
            getenv('LC_ALL'),
            getenv('LANGUAGE'),
        ] as $environmentLocale) {
            // Validate environment variable string availability
            if (
                is_string($environmentLocale)
                && $environmentLocale !== ''
            ) {
                return $environmentLocale;
            }
        }

        // now extractLocaleFromEnvironment itself can return null
        return null;
    }

    /**
     * Retrieve the strict preferred language matching the given or default available locales.
     * Integrates with HyperDX tracing context for observability pipelines.
     * 
     * @param Request $request
     * @param array|null $availableLocales
     * @return string|null
    */
    protected static function getStrictPreferredLanguage(Request $request, ?array $availableLocales = null): ?string
    {
        // Fallback to application default available locales config if none provided
        $locales = $availableLocales ?? config('app.available_locales', []);

        // Short-circuit and return null if no locales are configured for the HyperDX telemetry context
        if (empty($locales)) {
            return null;
        }

        $preferred = $request->getPreferredLanguage($locales);

        // Validate strict membership against allowed locales before proceeding in the chain
        return in_array($preferred, $locales, true) ? $preferred : null;
    }

    /**
     * ✨ [THE NEW CENTRALIZED HELPER - The Scroll of Configuration]
     *          Resolve Locale from the dynamic Krubot Config
     *
     * This is the SINGLE SOURCE OF TRUTH for reading locale from the `krubot.locale` config.
     * It is completely independent of the request and can be safely called from any context.
     *
     * @param Platform $platform The platform to look up.
     * @return ?string The locale code if found, otherwise null.
    */
    protected static function extractLocaleFromConfig(Platform $platform): ?string
    {
        $krubotConfigSetting = config('krubot.locale');

        if (is_array($krubotConfigSetting)) {

            // HyperDX in action: We leverage the `Stringable` interface of the Platform class.
            // PHP automatically calls the `__toString()` method on the `$platform` object
            // when it's used as an array key, returning its canonical value (e.g., 'web', 'cli').
            // This is cleaner and more idiomatic than accessing `$platform->value` directly.
            $platformKey = (string) $platform;

            // Priority #1: Exact key match (e.g., 'telegram', 'bale', 'web')
            // Step 1: Perform the primary, most likely lookup first.
            $locale = $krubotConfigSetting[$platformKey] ?? null;
            if ($locale) return self::normalizeLocale($locale);

            // Lookup priorities - #2: Group fallback ('web', 'cli', 'bot')
            // Step 2: ONLY if the first lookup fails, proceed to compute the fallback and perform the second lookup.
            // If the platform is 'web' or 'cli', keep it. Otherwise, classify it under 'bot'.
            //
            // Type-safe matching: We query the Platform instances directly through __callStatic.
            // This eliminates raw strings, allows IDE navigation, and leverages O(1) instance lookup.
            $fallbackGroup = $platform->matches(Platform::Web(), Platform::CLI()) ? $platformKey : 'bot';                    
            return self::normalizeLocale($krubotConfigSetting[$fallbackGroup] ?? null);
        }

        if (is_string($krubotConfigSetting) && !empty($krubotConfigSetting)) {
            // If config is a non-empty string, it acts as a powerful global override.
            return self::normalizeLocale($krubotConfigSetting);
        }

        // If no config is found, we return nothing (null).
        return null;
    }

    /**
     * ✨ [Private Helper] - Resolves the negotiated locale using the HyperDX cascade.
     * This is the highest-level private helper for the `awaken` state.
     * Calculates the best locale without polluting the class.
     * This logic is isolated to demonstrate its transient nature.
     * 
     * This method is fully aware of the sophisticated, dynamic nature of the KrubiK\Enums\Platform
     * class and the structure of the 'krubot.locale' configuration, which can be:
     * - a global string override (e.g., 'fa')
     * - a platform-specific associative array mapping (e.g., ['web' => 'fa', 'bot' => 'en', 'cli' => 'en_US'])
     *
     * The negotiation cascade priority:
     * 1. Authenticated User's explicit preference ($user?->preferred_locale)
     * 2. Dedicated Krubot Config override (config('krubot.locale') for the resolved platform/bot group)
     * 3. Browser/Request preferred language detection (Accept-Language header)
     * 4. Application default fallback locale ($app->getLocale())
     *
     * @param Application $app
     * @param Request $request
     * @param mixed $driver The active driver instance (from Nemesis)
     * @param Platform $platform The currently resolved operational platform instance.
     * @return string The resolved locale code.
    */
    protected static function extractLocaleFromRequest(Application $app, Request $request, Platform $platform, $driver): string
    {
        $user = null;

        if (method_exists($request, 'identityCard')) {
        
            /** @var \KrubiK\WebApps\UniversalIdentity|null $identity */
            $identity = $request->identityCard(); // Assumes a helper/macro on Request
            
            if ($identity?->isAuthenticated()) {
                // Efficiently resolve user only when authenticated
                // Check if user is a full model (from web session) or just an ID (from API/WebApp).
                if ($identity->user instanceof \Illuminate\Contracts\Auth\Authenticatable) {
                    $user = $identity->user;
                } elseif ($userId = $identity->id()) {
                    // Query the database ONLY when necessary. The resulting $user object
                    // is used here and then discarded. It does not become a property of RenderAura.
                    if ($driver && method_exists($driver, 'findUserById'))
                        $user = $driver->findUserById($userId);
                }
            }
        }

        // Laravel Auth fallback
        $user ??= auth()->user();
        
        // The Final Sage: The complete HyperDX negotiation cascade::
        // User's preference > Krubot Config > Browser's preference > App default.
        // +++ حالا متد normalize برای پاکسازی فرمت زبان (مثل en_US.UTF-8 به en) وجود دارد
        return self::normalizeLocale(
            $user?->preferred_locale
            ?? self::extractLocaleFromConfig($platform) // It's now cleaner and more declarative.
            ?? self::getStrictPreferredLanguage($request)
            ?? self::extractLocaleFromEnvironment()
            ?? $app->getLocale()
        );
    }

    /**
     * Normalize locale.
    */
    protected static function normalizeLocale(?string $locale = null): ?string
    {
        if($locale === null)
            return null;

        $locale = trim($locale);

        if ($locale === '') {
            return self::FALLBACK_LOCALE;
        }

        /*
        | en_US.UTF-8 -> en_US
        */
        if (($position = strpos($locale, '.')) !== false) {
            $locale = substr($locale, 0, $position);
        }

        /*
        | en_US / en-US -> en
        */
        $locale = str_replace('-', '_', $locale);

        if (($position = strpos($locale, '_')) !== false) {
            $locale = substr($locale, 0, $position);
        }

        return strtolower($locale);
    }

}
