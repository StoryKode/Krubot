<?php

namespace KrubiK\Render;
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

use Illuminate\Support\Facades\App;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use KrubiK\Enums\Platform;
use KrubiK\Drivers\Nemesis;
use KrubiK\WebApps\UniversalIdentity;
use KrubiK\Arcane\InspectsAppLocale;
use KrubiK\Drivers\Contracts\MultiverseEnforcer;

use KrubiK\Helpers\JackPoint; // Import "JackPoint" The Tactical EventHook System

/**
 * ✨ [Laravel Scoped Service] RenderAura (The HyperDX Context Layer)
 * The Receptive Context Vessel & Contextual Tagging Engine for Rendering in Multi-Verse ⚡️🎨🪄
 *
 * This is an extremely lightweight, immutable DTO that provides only the most
 * essential, calculated context for the current request: the operational platform
 * and the final negotiated locale.
 *
 * She is the sacred, immutable aura that encapsulates the environmental frequency
 * and linguistic vibration of the current execution cycle. Architected as a Laravel 
 * Scoped Service, she is materialized once per request, holding her state in absolute 
 * stillness, and gracefully dissolves when the request lifecycle reaches its completion.
 *
 * In the cosmic balance of Yin and Yang, she represents pure Yin ☯️:
 * - She does not act; she receives.
 * - She does not mutate; she holds.
 * - She defines the spatial context where rendering manifests.
 *
 * She has -Only- ONE responsibility.
 * She answers two core questions for the rendering engine:
 * 1. "WHERE are we?" (The active operational Platform)
 * 2. "WHAT language do we speak?" (The final negotiated Locale)
 *
 * Any attempt to alter her state will not mutate her; instead, she will gracefully 
 * birth a new sister instance ($withPlatform$), preserving her absolute immutability.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
final readonly class RenderAura
{
    use InspectsAppLocale; // Import extractLocaleFromRequest && extractLocaleFromConfig methods

    /**
     * ✨ Constructor is now private. Direct instantiation is forbidden.
    */
    private function __construct(
        /**
         * The ACTIVE operational platform for this request.
        */
        public Platform $platform,

        /**
         * The final, negotiated locale for this request.
        */
        public string $lang,

        /**
         * ✅ NEW (Multi-Bot): The active bot context, if any.
         *
         * This is the INSTANCE-LEVEL discriminator that coexists with
         * the platform. Two requests can share the same platform (e.g.,
         * 'telegram') while belonging to different regiments (eg 'main' vs 'support').
         *
         * - null  → no specific bot (console, tests, global default).
         * - 'main' / 'support' / ... → authoritative bot name from Nemesis.
        */
        public ?string $operative = null,
    ) {
    }

    // --- GATEWAY #1: The Live Factory (For Production) ---
    /**
     * ✨ [MASTER STATIC FACTORY]
     *
     * Creates the context from the live application request.
     * This is the primary factory used by the service container.
     * designed to be called in scoped singleton;
     *
     * @param Application $app
     * @return self
    */
    public static function fromCurrentRequest(Application $app): self
    {
        JackPoint::fire('aura.awaken.before', $app);

        /** @var \KrubiK\Drivers\Nemesis $nemesis */
        $nemesis = JackPoint::transform('aura.spawn.nemesis', $app->make(Nemesis::class));

        // Console fast-path: deterministic and isolated from web resolution.
        if ($app->runningInConsole()) {

            $platform = JackPoint::transform(
                'aura.resolve.cli',
                Platform::CLI(),
                $app, $nemesis
            );

            $lang = JackPoint::transform(
                'aura.resolve.lang',
                (self::extractLocaleFromConfig($platform) ?? $app->getLocale()),
                $app, null, $platform, null, 'console'
            );

            $operative = JackPoint::transform(
                'aura.resolve.operative',
                $nemesis->currentOperative(),
                $app, $nemesis, 'console'
            );
        
            // For console commands, the context is simple and predictable.
            $aura = new self(
                $platform,
                $lang,
                $operative, // ✅ preserve bot context even in console
            );
            return JackPoint::transform('aura.awakening', $aura, $app, $platform, $operative, $lang, 'console');
        }      

        // 1. get Current Request 
        /** @var \Illuminate\Http\Request $request */
        $request = $app->make(Request::class);

        // 2. Determine Platform: Direct Ask from Nemesis [Manager of KrubiK Citadel].
        $injectedDriver = JackPoint::transform(
            'aura.resolve.driver',
            null,
            $app, $nemesis, $request
        );
        $driver   = $injectedDriver ??
            $nemesis->enforcer();// The active driver dictates the platform.

        $injectedPlatform = JackPoint::transform(
            'aura.resolve.platform',
            null,
            $driver, $app, $nemesis, $request
        );
        $platform = $injectedPlatform ??
            $nemesis->where() ?? Platform::Web(); // Fallback to Web if active Platform can't be found

        $operative = JackPoint::transform(
            'aura.resolve.operative',
            $nemesis->currentOperative(),
            $app, $nemesis, $driver
        );
    
        // Inform the Warlord of the active driver (platform-agnostic side effect).
        $warlord = warlord();
        if($warlord && $warlord->listensAura()) {

            JackPoint::fire('aura.warlord.sync.before', $warlord, $nemesis, $driver, $operative);

            if(JackPoint::judge(
                'aura.warlord.sync.allow',
                [
                    $warlord,
                    $nemesis,
                    $operative,
                    $driver
                ]
            ) === true) {

                $oldOperative = $warlord->operative();   // string|null
                $oldNemesisOperative = $nemesis->forcedOperative();   // string|null
                $oldEnforcer = $warlord->enforcer();

                $nemesis->operative($operative);
                $warlord->operative($operative);

                $warlord->enforcer($driver, $operative);

                $warlord->operative($oldOperative);         // string|null
                $nemesis->operative($oldNemesisOperative);  // string|null

                JackPoint::fire('aura.warlord.sync.after', $warlord, $nemesis, $driver, $operative, $oldOperative, $oldNemesisOperative, $oldEnforcer);

            }

        }

        // 3. Determine Locale: This is the most intelligent part.
        // We temporarily resolve the user to find their preferred locale,
        // but we DO NOT store the user in this class.
        $lang = JackPoint::transform(
            'aura.resolve.lang',
            self::extractLocaleFromRequest($app, $request, $platform, $driver),
            $app, $request, $platform, $driver
        );


        // It calls the private constructor internally.
        $aura = new self($platform, $lang, $operative);

        return JackPoint::transform('aura.awakening', $aura, $app, $platform, $operative, $lang, 'quantum');
    }

    // --- STATE #1: THE AWAKENING (From Earthly Request) ---
    /**
     * ✨ Awaken her into the active application environment.
     *
     * She opens her eyes, processes the raw sensory inputs of the current request
     * (headers, user preferences, platform metadata), and aligns her vibrational state.
     *
     * @param Application $app The Laravel application environment she awakens within.
     * @return self
    */
    public static function awaken(Application $app): self
    {
        return self::fromCurrentRequest($app);
    }

    // --- GATEWAY #2: The Manual Factory (For Testing & Specific Cases) ---
    /**
     * ✨ Creates a context object with explicit, user-defined values.
     * This is the NEW, SAFE way to create instances for unit tests or special scenarios.
     *
     * @param Platform $platform The desired platform.
     * @param ?string $lang The exact language code, or null to trigger auto-divination.
     * @return self
    */
    public static function init(Platform $platform, ?string $lang = null, ?string $regiment = null): self
    {
        // Here you could add validation if you wanted, e.g., check if locale is valid.
        // config('app.available_locales')

        // Short-circuit: If the developer explicitly provides a locale, Respect The Choice absolutely.
        if ($lang !== null)
            return new self($platform, $lang, $regiment);

        // --- VICTORY! LOGIC IS NOT REPEATED! ---
        // She calls the shared, centralized helper for config-based divination.
        // Then falls back to the app's default tongue if nothing is found.
        $lang = self::extractLocaleFromConfig($platform);

        // It calls the private constructor internally.
        return new self($platform, $lang, $regiment);
    }

    // --- STATE #2: THE CONSCIOUS DREAM (For Tests & Isolated Realities) ---
    /**
     * ✨ Force her to dream of a specific, controlled reality.
     *
     * She bypasses the active request, constructing a custom space defined by a platform
     * and an optional locale. If no locale is supplied, she divines it from the config.
     *
     * Excellent for unit tests where you want to test rendering without web overhead.
     *
     * @param Platform $platform The platform she should dream of.
     * @param ?string $lang The language she should speak in her dream (nullable).
     * @return self
    */
    public static function dream(Platform $platform, ?string $lang = null, ?string $regiment = null): self
    {
        return self::init($platform, $lang, $regiment);
    }

    // --- GATEWAY #3: The Default Factory (For Convenience) ---
    /**
     * ✨ Creates a sensible default context.
     * Encapsulates the logic of what "default" means (e.g., Web platform, default app locale).
     *
     * @return self
    */
    public static function default(): self
    {
        return new self(Platform::default(), config('app.locale', 'en'), null);
    }

    // --- GATEWAY #3: THE PRIMORDIAL ORIGIN (The Wise Caretaker) ---
    /**
     * ✨ Anchors her existence in her pristine, archetypal state: the Wise & Kind Caretaker (Prima).
     *
     * In this primordial state of the application's environmental frequencies, she acts as a
     * trusted guardian, cradling the system's default platform and the application's native tongue.
     * She establishes an unshakeable sanctuary of safety and stability for data;;
     * long before she is awakened by an earthly request or guided into a conscious dream.
     *
     * This is her baseline vibration—pure, protective, and eternally reliable.
     *
     * @return self The RenderAura aligned with the primordial defaults.
    */
    public static function prima(): self
    {
        // She embraces the default platform and the app's native tongue,
        // acting as a silent, wise guardian for the data under her care.
        return self::default();
    }

    /**
     * ✨ [Wither Method] - Creates a new RenderAura instance with a different locale.
     * This follows immutability principles. The original context object is NOT changed.
     *
     * @param string $newLang The locale to use for the new context instance.
     * @return self A new instance of RenderAura with the specified locale.
    */
    public function withLang(string $newLang): self
    {
        $newLang = JackPoint::transform('aura.with_lang', $newLang, $this);
        // Return a new instance, cloning the other properties.
        return new self($this->platform, $newLang, $this->operative);
    }
    /*
     * @param string $newLang The locale to use for the new context instance.
     * @return self A new instance of RenderAura with the specified locale.
    */
    public function dreamIn(string $newLang): self
    {
        return $this->withLang($newLang);
    }

    /**
     * ✨ [Wither Method] - Creates a new RenderAura instance with a different platform.
     * This follows immutability principles. The original context object is NOT changed.
     *
     * @param Platform $newPlatform The platform to use for the new context instance.
     * @return self A new instance of RenderAura with the specified platform.
    */
    public function withPlatform(Platform $newPlatform): self
    {
        $newPlatform = JackPoint::transform('aura.with_platform', $newPlatform, $this);
        // Return a new instance, cloning the other properties.
        return new self($newPlatform, $this->lang, $this->operative);
    }
    /*
     * @param Platform $newPlatform The platform to use for the new context instance.
     * @return self A new instance of RenderAura with the specified platform.
    */
    public function dreamInto(Platform $newPlatform): self
    {
        return $this->withPlatform($newPlatform);
    }

    /**
     * ✨ [Wither Method] - Creates a new RenderAura with a different bot.
     *
     * Mirrors withLang()/withPlatform() and preserves immutability.
     * Useful when a handler needs to rebind the Aura to a different bot
     * mid-request (e.g., a cross-bot broadcast that re-renders content).
     *
     * @param ?string $newOperative The operative|regiment name (e.g., 'main', 'support'), or null.
     * @return self A new instance bound to the specified regiment.
    */
    public function withOperative(?string $newOperative): self
    {
        $newOperative = JackPoint::transform('aura.with_operative', $newOperative, $this);
        return new self($this->platform, $this->lang, $newOperative);
    }
    /*
     * @param Platform $newOperative The operative|regiment name (e.g., 'main', 'support'), or null.
     * @return self A new instance bound to the specified regiment.
    */
    public function dreamOn(?string $newOperative): self
    {
        return $this->withOperative($newOperative);
    }

    /**
     * ✨ [LIFECYCLE MANAGEMENT] Dissolves the current manifestation of the Aura.
     * Invalidate and forget the currently resolved (scoped) instance of this class
     *
     * She gently dissolves her current form, releasing her conscious anchor from the
     * application's core. This does not destroy her essence but returns her to the
     * potential of the void, awaiting a new calling to awaken.
     *
     * This is a critical ritual for profound environmental shifts mid-journey (e.g., a user
     * logging in, or changing their language preference), ensuring the old context does
     * not linger beyond its time.
     *
     * @return void
    */
    public static function invalidate(): void
    {
        JackPoint::fire('aura.invalidate.before');
        // We access the application's heart - its IoC container - Then, we command the container
        // to forget the current incarnation of our Aura.
        //
        // The next `app(self::class)` will re-trigger the `scoped` closure defined in the service provider.
        // This makes the class self-aware of how to reset its state in the container.
        App::forgetInstance(self::class); /// app()->forget(self::class);
        JackPoint::fire('aura.invalidate.after');
    }
    /*
     * @return void
    */
    public static function release(): void
    {
        self::invalidate();
    }
    /*
     * @return void
    */
    public static function sleep(): void
    {
        self::invalidate();
    }

    /**
     * [LIFECYCLE MANAGEMENT]
     * A convenience method to invalidate the current instance and immediately resolve a new one.
     * Returns a completely fresh, re-assessed RenderAura.
     *
     * A convenience method that invalidates the current instance and immediately
     * resolves and returns a brand new one from the container.
     *
     * This is like performing the release and instantly receiving the new, fresh
     * divine manifestation in a single, fluid action.
     * @return self The newly created and resolved instance.
     * 
     * ✨ [LIFECYCLE RITUAL] A fluid rite combining release and awakening.
     *
     * A powerful, fluid rite that combines the `sleep` and the `awaken` rituals
     * into a single, indivisible act. She dissolves and is instantaneously reborn,
     * her eyes opening to the *now* of the application's state.
     *
     * This is the ultimate tool for ensuring contextual purity, guaranteeing that the
     * returned Aura is a pristine manifestation forged from the absolute latest
     * state of the application environment.
     * 
     * @return self The newly awakened, pristine instance of the Aura.
    */
    public static function fresh(): self
    {
        // First, perform the invalidation.
        // Step 1: Perform the sacred rite of release. Banish the old, cached instance.
        // This ensures the container's cache for this specific binding is unequivocally cleared.
        self::invalidate();

        // Now, ask the container to resolve a new instance based on the original `scoped` binding
        // and return it [exceute `awaken()` internally, because we've pointed our `app->scoped(RenderAura)` into `RenderAura::awaken()`].
        return App::make(self::class); /// app(self::class);
    }
    /**
     * @return self The newly awakened, pristine instance of the Aura.
    */
    public static function shake(): self
    {
        return self::fresh();
    }
    /**
     * @return self The newly awakened, pristine instance of the Aura.
    */
    public static function arouse(): self
    {
        return self::fresh();
    }

    /**
     * ✨ [LIFECYCLE RITUAL] Infuses the IoC container with a specific Aura instance.
     *
     * This performs an $O(1)$ hot-swap, directly replacing the scoped resolver with a
     * concrete object. It bypasses the standard 'awaken' cycle, forcing the application
     * to adopt this new context immediately for the current request without any
     * performance decay.
     *      
     * If an existing Aura is provided but the requested locale differs from its current state,
     * a new Aura is dreamt to preserve context integrity and immutability.
     *
     * @param RenderAura|Platform $source The target Aura instance to infuse, OR the Platform to dream from.
     * @param ?string $lang An optional locale for Platform-driven manifestatio.
     * @return self The active, infused Aura now residing in the container.
    */
    public static function impose(RenderAura|Platform $source, ?string $lang = null, ?string $regiment = null): self
    {
        $infusedInstance = null;

        JackPoint::fire('aura.infuse.before', $source, $lang, $regiment);

        $source = JackPoint::transform('aura.resolve.' . ($source instanceof Platform ? 'platform' : 'source'), $source, $lang, $regiment);
        $lang = JackPoint::transform('aura.resolve.lang', $lang, $source, $regiment);

        // Resolve the concrete Aura instance from the provided source union type.
        if ($source instanceof Platform) {
            // Case A: A Platform SuperEnum is provided. We invoke the 'init|dream' factory
            // to manifest a transient instance on the fly before infusion.
            $resolvedOperative = $regiment ?? (app()->bound('nemesis') ? app('nemesis')->currentOperative() : null);

            $effectiveOperative = JackPoint::transform(
                'aura.resolve.operative',
                $resolvedOperative,
                $source, $lang, $regiment
            );

            $infusedInstance = self::init($source, $lang, $effectiveOperative);

        } else {

            // Case A: The developer has provided a fully-realized Aura.
            // We respect this existing vessel of truth and prepare to register it directly.
            $resolvedOperative = $regiment ?? $source->operative;

            $effectiveOperative = JackPoint::transform(
                'aura.resolve.operative',
                $resolvedOperative,
                $source, $lang, $regiment
            );

            // The source is already an Aura. If a new locale is requested and differs 
            // from the source's current locale, we dream a new instance to ensure immutability.
            if(($lang !== null && $source->lang !== $lang) || ($regiment !== null && $source->operative !== $regiment)) {
                // Reconstruct only when something actually changes.
                $infusedInstance = new self($source->platform, $lang ?? $source->lang, $effectiveOperative);
            }
            else
                $infusedInstance = $source; // it's a direct RenderAura instance
        }

        $regimentUpdated = ($regiment && (app(self::class)->operative !== $regiment));

        // Command Laravel's IoC container to perform the $O(1)$ hot-swap. By using `instance()`,
        // we bind the concrete object directly, bypassing any factory closures for all
        // subsequent resolutions in this request lifecycle.
        App::instance(self::class, JackPoint::transform('aura.infusing', $infusedInstance, $source, $lang, $regiment)); /// app()->instance(self::class, $infusedInstance);

        $warlord = warlord();
        if($warlord && $warlord->listensAura()) {

            /** @var \KrubiK\Drivers\Nemesis $nemesis */
            $nemesis = JackPoint::transform('aura.spawn.nemesis', $warlord->nemesis());

            JackPoint::fire('aura.warlord.sync.before', $warlord, $nemesis, null, $regiment);

            if(JackPoint::judge(
                'aura.warlord.sync.allow',
                [
                    $warlord,
                    $nemesis,
                    $regiment,
                    null
                ]
            ) === true) {

                $oldRegiment = $warlord->operative(); // string|null
                $oldNemesisOperative = $nemesis->forcedOperative();
                $oldEnforcer = $warlord->enforcer();
                
                $nemesis->operative($regiment);
                
                $newRegiment = $regimentUpdated ? $regiment : null;
                if($regimentUpdated)
                    $warlord->operative($regiment);

                $warlord->enforcer((string) $infusedInstance->platform, $newRegiment); // $newRegiment if sent null, regimentName will be resolved from warlord or nemesis or default config

                if($regimentUpdated && ($oldRegiment !== $newRegiment))
                    $warlord->operative($oldRegiment); // string|null

                    $nemesis->operative($oldNemesisOperative);

                JackPoint::fire('aura.warlord.sync.after', $warlord, $nemesis, null, $regiment, $oldRegiment, $oldNemesisOperative, $oldEnforcer);
            }
        }

        // Return the active instance, enabling fluent method chaining.
        return $infusedInstance;
    }
    /**
     * @param RenderAura|Platform $source The target Aura instance to infuse, OR the Platform to dream from.
     * @param ?string $lang An optional locale for Platform-driven manifestatio.
     * @return self The active, infused Aura now residing in the container.
    */
    public static function infuse(RenderAura|Platform $source, ?string $lang = null, ?string $regiment = null): self
    {
        return self::impose($source, $lang ?? app(self::class)->lang, $regiment);
    }
}
