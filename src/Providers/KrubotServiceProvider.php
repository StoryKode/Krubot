<?php

namespace KrubiK\Providers;
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

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use KrubiK\Drivers\Contracts\MultiverseEnforcer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use KrubiK\Krubot;
use KrubiK\Drivers\Nemesis as KrubotManager;
use KrubiK\Console\KrubiKPulse;
use KrubiK\Console\LazarusProtocol;
use KrubiK\Console\MakeMigrationsCommand;
use KrubiK\Console\CacheNexusesCommand;
use KrubiK\Console\KrubotMindSimulator;
use KrubiK\Console\MakeNexusCommand;
use KrubiK\Helpers\AmethystMatrix;
use KrubiK\Helpers\OpcacheRuler;
use KrubiK\WebApps\AxiomCore;
use KrubiK\Render\RenderAura;
use Krubot\Render\RichMan;
use KrubiK\Render\Kernel\BladeCipher;
use KrubiK\Render\Parsers\Parsentinel;
use KrubiK\Arcane\PlatformConstantsRobustGen; // Ensure the PlatformConstants Trait is imported
use KrubiK\Middlewares\SynapticSurgeProtocol; // Import the Lazarus SSP

/**
 * =========================================================================
 *  KRUBIK GALACTIC COMMAND CENTER
 * =========================================================================
 *     v5.8.6 "The Galactic Titan"
 * 
 * This Service Provider is the heart of the KrubiK package. It bootstraps
 * the bot, discovers and integrates Nexuses, provides Artisan commands for
 * a superior Developer Experience (DX), and handles asset publishing.
 * It is designed for maximum performance, flexibility, and clarity.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class KrubotServiceProvider extends ServiceProvider implements DeferrableProvider
{
    use PlatformConstantsRobustGen; // Quenchs `Platform::XXX` Constants from Config

    /**
     * Register any application services.
     *
     * This method is for binding things into the container. It should be FAST.
     * We will bind the Krubot instance here, but defer the heavy-lifting
     * of Nexus integration to the `boot` method.
     */
    public function register(): void
    {
        // Merge the default package config with the user's published version.
        $this->mergeConfigFrom(__DIR__ . '/../../config/krubot.php', 'krubot');

        $this->registerPlatformConstants(); // internally checks if config('krubot.cache.platform-constants-generation') enabled

        // Register the core bindings for the Krubot engine.
        $this->registerBindings();

        if($this->app['config']->get('krubot.blade-cipher.enabled', false)) {
            BladeCipher::registerOnSP($this->app);
        }

        Parsentinel::registerOnSP($this->app);
    }

    /**
     * Bootstrap any application services.
     *
     * This is where the magic happens. After the app is booted, we
     * can safely resolve the Krubot instance and perform the heavy
     * logic of discovering and integrating all Nexuses.
     */
    public function boot(): void
    {

        // These actions are only relevant when running in a console environment.
        if ($this->app->runningInConsole()) {
            $this->offerPublishing();
            $this->registerCommands();
        }

        if($this->app['config']->get('krubot.blade-cipher.enabled', false)) {
            BladeCipher::bootOnSP($this->app);
        }

        // Set the application locale based on our package's config (un-DX-ly)
        /// if (config('krubot.locale'))
        ///  app()->setLocale(config('krubot.locale'));

        // Load the translation files for our package
        // The second argument is the "namespace" for our translations
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'krubot');

        // Boot the Nexus integration engine.
        // This is done in the `boot` method to ensure all engine services are available.
        // $this->app->make(Krubot::class); // Eager Loading // دستور ساخت فوری
        $this->bootNexuses(); // Call the Nexus Integration Core to set up the 'resolving' listener.

        /// Load web routes of the package
        /// $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        /// Moved To: KrubotRouteProvider

        /**
         * Register The Identity Gateway :: Attach or Retrieve the Universal Identity Card via Symfony ParameterBag.
         * Archetype: The Conduit (State Mutator & Accessor)
         *
         * A Hyper-DX Request macro providing a unified, context-aware API to bind and extract 
         * the `UniversalIdentity` (The Soul Stone) within the current request lifecycle.
         * 
         * It operates via a polymorphic, jQuery-like dual-mode mechanism:
         * 1. Setter Mode (Write): Injects the resolved identity into the request's internal attribute bag.
         * 2. Getter Mode (Read): Retrieves the bound identity, seamlessly falling back to a 
         *    contextual guest state if the WebApp ecosystem is active.
         *
         * @param UniversalIdentity|null $identity Optional. Pass to set the identity (Setter Mode). Omit to retrieve (Getter Mode).
         * @return UniversalIdentity|bool Returns the Identity/Null in Getter Mode, or a boolean representing success state in Setter Mode.
        */
        Request::macro('identityCard', function (?UniversalIdentity $identity = null): bool|UniversalIdentity|null {

                /**
                 * @var \Illuminate\Http\Request $this
                 * Binds to the current Illuminate Request instance operating within the Axiom Orchestrator.
                 */
                $identityKey = 'krubik.axiom.identity';
                $webappsEnabled = config('krubot.webapps.enabled', true);

                // [Setter Mode] Triggered strictly when an argument is explicitly provided.
                // We use func_num_args() to differentiate between passing `null` explicitly vs. not passing anything.
                if(func_num_args() > 0) {

                    // Gatekeeper Check: Abort the injection if the WebApp ecosystem is 
                    // turned off at the global configuration level.
                    if(!$webappsEnabled)
                        return false;

                    /// if ($this->attributes->has($identityKey) && $this->attributes->get($identityKey) === $identity)
                    ///            return false;
                    
                    // Persist the resolved UniversalIdentity into the request-scoped attribute bag,
                    // a Symfony ParameterBag-backed storage layer, so every downstream layer reads
                    // From One canonical, request-local authoritative identity snapshot.
                    $this->attributes->set($identityKey, $identity);
                    return true;
                }

                // [Getter Mode] Extract the bound identity from the internal attributes.
                // If no identity was resolved (e.g., first access), we forge a transient Guest entity.
                // Notice: If webapps are disabled, it strictly returns `null` instead of a Guest, 
                // preventing unauthorized contextual leaks.
                return $this->attributes->get($identityKey,
                    $webappsEnabled ? UniversalIdentity::guest() : null
                );

            });

            /// $this->transportKrubot();
    }

    /**
     * Binds the core Krubot singleton and its alias to the service container.
     * This method focuses *only* on the instantiation logic, making it extremely fast.
     */
    protected function registerBindings(): void
    {
        // 2. KrubotManager را به عنوان یک Singleton ثبت می‌کنیم.
        // این مغز متفکر سیستم است.
        $this->app->singleton('krubot.manager', function ($app) {
            // منیجر خودش شعور دارد، نیازی به تغذیه دستی نیست.
            return new KrubotManager($app);

            // $manager = new KrubotManager($app);
            // تزریق کانفیگ به تریت داخل منیجر (طبق بحث قبلی)
            // $manager->setConfig($app['config']['krubot']); 
            // return $manager;
        });

        $this->app->singleton('nemesis', function ($app) {
            return app('krubot.manager');
        });

        // 3. اینترفیس را به درایور *پیش‌فرض* متصل می‌کنیم.
        // این برای تزریق وابستگی در کنترلرها و جاب‌ها حیاتی است.
        // وقتی در متدی تایپ-هینت MultiverseEnforcer بدهید، لاراول می‌داند چه چیزی بسازد.
        $this->app->bind(MultiverseEnforcer::class, function ($app) {
            return $app['krubot.manager']->driver();
        });

        // Bind Krubot as a Singleton. This ensures the same bot instance is used
        // throughout the entire application request lifecycle.
        // The closure is kept lean and mean for maximum performance.
        $this->app->singleton(Krubot::class, function ($app) {

            // Extract the universal knowledge
            $config = $app['config']->get('krubot');

            // 4. KRUBOT BINDING (The Independent Commander)
            // ###. ⚡ نقطه ادغام حیاتی (The Fusion Point) ⚡ .###

            // جنگ‌سالار مستقیماً به Nemesis دستور می‌دهد تا کالبدشکافی را انجام داده
            // و سلاح جهش‌یافته (BOW) را شخصاً تحویل دهد.
            $bow = app('nemesis')->driver();
            
            // 3. ⚡ Genesis: Summon the Warlord with absolute precision.
            // We pass the Application, the deployed Soldier, and the Strategy (Config).
            return new \KrubiK\Krubot($app, $bow, $config);
        });

        // The Oracle is born once, and lives forever (Singleton).
        $this->app->singleton('amethyst.empress', function ($app) {
            return new AmethystMatrix();
        });

        // Register the service as a singleton, as we only need one instance.
        $this->app->singleton(OpcacheRuler::class, function ($app) {
            return new OpcacheRuler();
        });

        // Bind the AxiomCore as a singleton.
        // The container will create ONE instance of this stateless service per worker,
        // which is highly efficient and perfectly safe for Octane.
        $this->app->singleton(AxiomCore::class, function ($app) {
            return new AxiomCore();
        });

        // The call is clean, expressive, and self-documenting.
        // --- ⚜️ The Inscription of the Ether Anchor ⚜️ ---
        // A law is now etched into the Application's core (Laravel IoC): RenderAura's lifecycle
        // is quantum-entangled with the singular scope of a Request. Her `awaken` genesis ritual is deferred,
        // invoked once upon first resolution to forge a *Scoped Singleton* Or Singleton-per-Request
        //
        // [Laravel Scoped, Optimal for Octane/Swoole/RoadRunner]
        //
        // An incorruptible, hyper-stable anchor of contextual truth for that journey's entire duration.
        $this->app->scoped(RenderAura::class, RenderAura::awaken(...));
        // PHP 8.1+ Supported :: First-Class Callable Syntax

        // We use 'bind' instead of 'singleton' because RichMan is a stateful builder.
        // Every time the facade is called, we need a fresh, empty instance
        // to start building a new document from scratch.
        $this->app->bind('richman', function ($app) {
            return RichMan::summon();
        });

        if (!class_exists('Lazarus', false)) {
            \class_alias(
                LazarusProtocol::class,
                'Lazarus'
            );
        }

        // Create a convenient alias for easier resolution binding or for the Facade.
        $this->app->alias(Krubot::class, 'krubot');
        $this->app->alias(MultiverseEnforcer::class, 'krubot.driver');
        $this->app->alias(OpcacheRuler::class, 'opcache.ruler');

        // Register the Middleware Alias (Codename SSP)
        // This makes the 'ssp.protocol' codename available throughout the host application.
        $this->app['router']->aliasMiddleware('ssp.protocol', SynapticSurgeProtocol::class);
    }

    /**
     *                  The Ultimate bootNexuses method
     *                    The Nexus Integration Core.
     * 
     *    This is the brain that loads all Nexus modules into the bot.
     *
     * Lazily configures the Krubot instance right after it's been resolved.
     * This method represents the architectural core of Nexus integration, combining
     * a performance-first caching strategy with flexible discovery mechanisms.
     * All expensive operations (filesystem I/O) are deferred until the bot is
     * actually requested, and are completely bypassed in production when a cache is present.
    */
    protected function bootNexuses(): void
    {
        $this->app->resolving(Krubot::class, function (Krubot $krubot, $app) {
            // Fetch the entire package configuration once to minimize overhead.
            $config = $app['config']->get('krubot');

            // PHASE 0: PRODUCTION-FIRST CACHE RETRIEVAL
            // This is the fastest execution path. If caching is enabled and the
            // cache file exists, we load it and terminate the configuration process immediately.
            $cachePath = $config['cache']['path'] ?? null;
            if (($config['cache']['enabled'] ?? false) && $cachePath && file_exists($cachePath)) {
                $cachedNexuses = require $cachePath;
                // Use setNexuses for a bulk, high-performance assignment.
                if (is_array($cachedNexuses) && !empty($cachedNexuses)) {
                    $krubot->setNexuses($cachedNexuses, true); // true => clear before fill
                }
                return; // Mission accomplished. The bot is ready from cache.
            }

            // --- If cache is not hit, proceed with manual loading ---

            // PHASE 1: THE VIP LANE - EXPLICIT NEXUSES
            // Load statically defined Nexuses from the config file. These are considered
            // critical and are always loaded first (when not using cache).
            $staticNexuses = Arr::wrap($config['nexuses'] ?? []);
            if (!empty($staticNexuses)) {
                // We start by setting this list as the definitive base.
                $krubot->setNexuses($staticNexuses, true); // true => clear before fill
            }

            // PHASE 2: THE DISCOVERY ENGINE - AUTOMATIC SCANNING
            // If discovery is enabled, scan the defined paths for Nexus classes.
            // This is an I/O-heavy operation, perfectly placed inside this lazy-loaded callback.
            if ($config['discovery']['enabled'] ?? false) {
                $discoveryPaths = Arr::wrap($config['discovery']['paths'] ?? [app_path('Nexus')]);

                foreach ($discoveryPaths as $path) {
                    // Failsafe check: ensure the path is a valid, readable directory before scanning.
                    // This prevents errors if the config contains invalid or inaccessible paths.
                    if (is_string($path) && is_dir($path) && is_readable($path)) {
                        // This method should APPEND discovered nexuses to the existing list.
                        $krubot->discoverAndIntegrateNexuses($path);
                    }
                }
            }
        });
    }

    /**
     * Sets up the assets that can be published by the user.
     */
    protected function offerPublishing(): void
    {
        // 3. Publish Command Logic

        // This allows users to publish the configuration file to their own
        // config directory for customization using: `php artisan vendor:publish`
        $this->publishes([
            // مسیر فایل مبدأ (Source) => مسیر فایل مقصد (Destination)
            __DIR__ . '/../../config/krubot.php' => config_path('krubot.php'),
            __DIR__ . '/../lang'       => $this->app->langPath('engine/krubot'),
        ], 'krubot-config'); // تگ اختصاصی برای پابلیش

        $this->publishes([
            __DIR__ . '/Client/Res/__main__/Krubot.js' => public_path('engine/krubot/Krubot.js'),
        ], 'public');

        // php artisan vendor:publish --tag=krubot-config
        // => Copied File [/app/KrubiK/config/krubot.php] To [/config/krubot.php]
        // => Publishing complete.

        // [FEATURE PRESERVED] Commented-out suggestion from Provider #1 for future expansion.
        // This offers to publish a default Nexus directory for a quick start.
        /*
        $this->publishes([
            __DIR__ . '/../../stubs/Nexus' => app_path('Nexus'),
        ], 'krubot-nexuses');
        */

        // Publish the migrations
        $this->publishes([
            __DIR__ . '/../DivineMessageSender/Migrations/DivineMessageMigration.php' => database_path('migrations/' . date('Y_m_d_His') . '_divine_messages.php'),
            __DIR__ . '/../DivineMessageSender/Migrations/DivineDispatchQueueMigration.php' => database_path('migrations/' . date('Y_m_d_His', time() + 1) . '_divine_dispatch_queue.php'),
            __DIR__ . '/../Migrations/LazarusTodosMigration.php' => database_path('migrations/' . date('Y_m_d_His', time() + 2) . '_lazarus_todos.php'),
        ], 'krubot-migrations');
    }

    public function transportKrubot(): bool
    {
        // مسیر فایل منبع داخل پکیج
        $source = __DIR__ . '/Client/Res/__main__/Krubot.js';

        // مسیر مقصد در فولدر public
        $destination = public_path('engine/krubot/Krubot.js');

        if (File::exists($destination)) {
            return true; // <<--- cheap and idiot! dont be like him in your life!
        }

        // ایجاد فولدر مقصد در صورت عدم وجود
        if (!File::exists(dirname($destination))) {
            File::makeDirectory(dirname($destination), 0755, true);
        }

        // کپی کردن فایل
        File::copy($source, $destination);

        return true;
    }

    /**
     * Registers the package's "Ammunition" - the Artisan commands.
     */
    protected function registerCommands(): void
    {
        // [FEATURE MERGE] Full command list from Provider #3.
        // This provides a complete toolkit for managing Nexuses.
        $this->commands([
            KrubiKPulse::class,
            LazarusProtocol::class,
            CacheNexusesCommand::class, // The performance booster
            KrubotMindSimulator::class,  // The debugging tool
            MakeNexusCommand::class,    // The workflow accelerator
            MakeMigrationsCommand::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * Being a DeferrableProvider improves application performance by only loading
     * this provider when one of its services is explicitly requested.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        $provided_dxkit = [
            Krubot::class,
            SynapticSurgeProtocol::class,
            OpcacheRuler::class,
            MultiverseEnforcer::class,
            AxiomCore::class,
            RenderAura::class,
            'krubot',
            'amethyst.empress',
            'nemesis',
            'krubot.manager',
            'krubot.driver',
            'opcache.ruler',
            'richman',


            KrubiKPulse::class, 'command.krubik:pulse',
            LazarusProtocol::class, 'command.krubik:lazarus',
            CacheNexusesCommand::class, 'command.krubik:nexus-cache', // The performance booster
            MakeNexusCommand::class, 'command.krubik:nexus-make',    // The workflow accelerator
            MakeMigrationsCommand::class, 'command.krubik:make-migrations',
            // The debugging tool
            KrubotMindSimulator::class, 'command.krubot:mind-simulator', 'command.krubik:nexus:inspect', 'command.krubik:nexus-list',
        ];

        if($this->app['config']->get('krubot.blade-cipher.enabled', false)) {
            $provided_dxkit []= 'blade.compiler';
            $provided_dxkit []= BladeCipher::class;
        }

        Parsentinel::introduceTo($provided_dxkit);

        return $provided_dxkit;
    }
}
