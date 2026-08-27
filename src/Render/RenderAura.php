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
        public string $locale
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
        /** @var \KrubiK\Drivers\Nemesis $nemesis */
        $nemesis = $app->make(Nemesis::class);
        
        // .#. Determine Platform: Direct Ask from Nemesis [Manager of KrubiK Citadel].
        $platform = $nemesis->where(); // === // $nemesis->platform()

        // For console commands, the context is simple and predictable.
        if ($app->runningInConsole()) {
            return new self(
                Platform::tryFrom('cli') ?? Platform::Web(), // Fallback to Web if 'cli' enum does not exist
                self::extractLocaleFromConfig($platform) ?? $app->getLocale()
            );
        }

        // 1. Determine Platform: The active driver dictates the platform.

        /** @var \KrubiK\Drivers\Contracts\MultiverseEnforcer $driver */
        $driver = $nemesis->driver();       
        $platform = Platform::from($driver->getDriverAlias());

        // 2. get Current Request 
        /** @var \Illuminate\Http\Request $request */
        $request = $app->make(Request::class);

        // 3. Determine Locale: This is the most intelligent part.
        // We temporarily resolve the user to find their preferred locale,
        // but we DO NOT store the user in this class.
        $locale = self::extractLocaleFromRequest($app, $request, $platform, $driver);

        // It calls the private constructor internally.
        return new self($platform, $locale);
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
     * @param ?string $locale The exact language code, or null to trigger auto-divination.
     * @return self
     */
    public static function init(Platform $platform, ?string $locale = null): self
    {
        // Here you could add validation if you wanted, e.g., check if locale is valid.
        // config('app.available_locales')

        // Short-circuit: If the developer explicitly provides a locale, Respect The Choice absolutely.
        if ($locale !== null)
            return new self($platform, $locale);

        // --- VICTORY! LOGIC IS NOT REPEATED! ---
        // She calls the shared, centralized helper for config-based divination.
        // Then falls back to the app's default tongue if nothing is found.
        $locale = self::extractLocaleFromConfig($platform);

        // It calls the private constructor internally.
        return new self($platform, $locale);
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
     * @param ?string $locale The language she should speak in her dream (nullable).
     * @return self
     */
    public static function dream(Platform $platform, ?string $locale = null): self
    {
        return self::init($platform, $locale);
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
        return new self(Platform::default(), config('app.locale', 'en'));
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
     * @param string $newLocale The locale to use for the new context instance.
     * @return self A new instance of RenderAura with the specified locale.
     */
    public function withLocale(string $newLocale): self
    {
        // Return a new instance, cloning the other properties.
        return new self($this->platform, $newLocale);
    }
    /*
     * @param string $newLocale The locale to use for the new context instance.
     * @return self A new instance of RenderAura with the specified locale.
    */
    public function dreamIn(string $newLocale): self
    {
        return $this->withLocale($newLocale);
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
        // Return a new instance, cloning the other properties.
        return new self($newPlatform, $this->locale);
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
        // We access the application's heart - its IoC container - Then, we command the container
        // to forget the current incarnation of our Aura.
        //
        // The next `app(self::class)` will re-trigger the `scoped` closure defined in the service provider.
        // This makes the class self-aware of how to reset its state in the container.
        App::forgetInstance(self::class); /// app()->forget(self::class);
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
     * @param ?string $locale An optional locale for Platform-driven manifestatio.
     * @return self The active, infused Aura now residing in the container.
    */
    public static function impose(RenderAura|Platform $source, ?string $locale = null): self
    {
        // Resolve the concrete Aura instance from the provided source union type.
        if ($source instanceof Platform) {
            // Case A: A Platform SuperEnum is provided. We invoke the 'init|dream' factory
            // to manifest a transient instance on the fly before infusion.
            $infusedInstance = self::init($source, $locale);
        } else {
            // Case A: The developer has provided a fully-realized Aura.
            // We respect this existing vessel of truth and prepare to register it directly.

            // The source is already an Aura. If a new locale is requested and differs 
            // from the source's current locale, we dream a new instance to ensure immutability.
            $infusedInstance = ($locale !== null && $source->locale !== $locale)
                ? $source->dreamIn($locale)
                : $source; // it's a direct RenderAura instance
        }

        // Command Laravel's IoC container to perform the $O(1)$ hot-swap. By using `instance()`,
        // we bind the concrete object directly, bypassing any factory closures for all
        // subsequent resolutions in this request lifecycle.
        App::instance(self::class, $infusedInstance); /// app()->instance(self::class, $infusedInstance);

        // Return the active instance, enabling fluent method chaining.
        return $infusedInstance;
    }
    /**
     * @param RenderAura|Platform $source The target Aura instance to infuse, OR the Platform to dream from.
     * @param ?string $locale An optional locale for Platform-driven manifestatio.
     * @return self The active, infused Aura now residing in the container.
    */
    public static function infuse(RenderAura|Platform $source, ?string $locale = null): self
    {
        return self::impose($source, $locale);
    }
}
