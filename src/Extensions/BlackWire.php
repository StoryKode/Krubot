<?php

namespace KrubiK\Extensions;
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

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder; // Import The Symfony Matrix Scanner
use KrubiK\Helpers\JackPoint;        // Import "JackPoint" The Tactical EventHook System
use KrubiK\Helpers\AmethystMatrix as Log;
use Composer\InstalledVersions as Composer;
use Throwable;

/**
 * ⚡️ [ BlackWire: 🕸⚡️🕷 The Neural Ignition Core ] ⚡️
 * 
 * A highly-privileged, zero-gravity ServiceProvider executing BEFORE the Krubot lifecycle. 
 * It systematically scans the `app/Synapses` matrix, wiring JackPoint event-hooks and 
 * injecting scoped, idempotent architectures directly into the system's veins.
 *
 * > "We wire the listeners in darkness; before the system even boots up." 🖤⚡️🕷
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.9×
 * @license MIT
*/
final class BlackWire extends ServiceProvider
{
    // intentionally NOT DeferrableProvider — must always run

    /**
     * @var array<string, true> Tracks loaded files to ensure idempotency.
    */
    private array $wiredSynapseFiles = []; // ← افزوده شد (State Tracker)

    public function register(): void
    {
        // error_log('BlackWire Started');
        // \Illuminate\Support\Facades\Log::info('🚀 [BlackWire] REGISTER method is running!');

        $this->aliasJackPoint();          // makes JackPoint Accessible in Nexuses/Synapses/Syringes via `use JackPoint;`
        $this->bindTheObserver();         // Binds the Eternal Witness 👁️ [KarAgah → JackPoint]
        $this->loadWordPressApiHelpers(); // Conditionally loads the WordPress API helpers
        $this->wireSynapses();            // Integrates Synapses from app/Synapses/*.php (or your configured paths)
        $this->blazeComposer();           // Conditionally discovers Krubot-Plugins from vendor directory
    }

    public function xboot(): void
    {
        error_log('✅ [BlackWire] BOOT method is running!');
        \Illuminate\Support\Facades\Log::info('✅ [BlackWire] BOOT method is running!');
    }

    public function boot(): void
    {
        $ignoring = 0; /// ... void(null);
    }

    /**
     * Define Fake-Facades for global namespace access.
    */
    private function aliasJackPoint(): void
    {
        // define a Fake-Facade, so we can access \JackPoint without importing FQCN.
        if (!class_exists('JackPoint', false)) {
            \class_alias(
                JackPoint::class,           // مستقیم از FQCN استفاده می‌کنیم
                'JackPoint'
            );
        }
        if (!class_exists('JackSpot', false)) {
            \class_alias(
                JackPoint::class,
                'JackSpot'
            );
        }
    }

    /**
     * 👁️ Binding The Observer (Pre-heating the Telemetry)
     * 
     * Summons the Recording Angel and permanently binds its gaze to `JackPoint`.
     * By pre-morphing the Observer BEFORE the `wireSynapses` matrix scans the files, 
     * we guarantee that when thousands of `on()` hooks fire, the Origin Locator 
     * operates at pure $O(1)$ velocity without ever triggering a redundant filesystem scan.
    */
    private function bindTheObserver(): void
    {
        // Morph the Detective to strictly watch the JackPoint DNA.
        // It locks the Singleton instance in RAM, ready to testify at a moment's notice.
        KarAgah::morph(JackPoint::class);
    }

    /**
     * Synapses Ignition Core — Pre-Krubot Intelligent Directory Scanning
     *
     * Scans app/Synapses/*.php and require_once's each file.
     * Files ending in .disabled.php are silently skipped.
     *
     * These files are plain PHP scripts (not classes) that call JackPoint::on/once/...
     * directly. They defined BEFORE Krubot is born, so listeners are already wired
     * by the time KrubotAwaken fires.
     *
     * Discovery is done ONCE at require-time (no re-scan on each request
     * when OPcache is hot).
    */
    private function wireSynapses(): void
    {
        // 1. Get raw paths (supports both String and Array)
        $rawPaths = (array) $this->app['config']->get(
            'krubot.extensions.path',
            app_path('Synapses')
        );
        
        // 2. Get exclude suffixes
        $excludeSuffixes = (array) $this->app['config']->get(
            'krubot.extensions.exclude_suffixes',
            ['disabled']         // ← ".disabled.php" را رد می‌کند
        );
        
        // 3. Experimental Feature: Scoped Require (Prevents global variable pollution)
        $isScoped = $this->app['config']->get('krubot.extensions.scoped-register', false);

        $loadedCount = 0;

        // Stage 0: Absorption Started
        JackPoint::fire('synapses.absorption.started', $rawPaths, $excludeSuffixes);

        foreach ($rawPaths as $pathDef) {
            $pathDef = trim($pathDef);
            
            // Check if it's a recursive wildcard request (ends with '/*' OR '*')
            $isRecursive = str_ends_with($pathDef, '/*') || str_ends_with($pathDef, '*');

            // فقط در صورتی که Recursive باشد، الگوهای انتهایی را با Regex بردار
            $cleanPath = $isRecursive ?
                preg_replace('/[\/\*]+$/', '', $pathDef) // این Regex دقیقاً به دنبال اسلش یا ستاره در انتهای رشته است
            :
                rtrim($pathDef, '/*\\'); // Clean the path for validation

            // با استفاده از realpath، تمام ناخالصی‌های سیستم‌عامل (مثل /./ یا /../) هم پاک می‌شود
            $realPath = realpath($cleanPath);

            if ((!$realPath) || (!is_dir($realPath))) {

                // لاگ کن که مسیر نامعتبر است و برو مرحله بعد
                $retriedPath = JackPoint::transform('synapses.absorption.bad.path', $realPath, $cleanPath, $pathDef);
                if($retriedPath === $realPath) // no filters applied here
                    continue;

                if ((!$retriedPath) || (!is_string($retriedPath)) || (!is_dir($retriedPath)))
                    continue;
                
                $realPath = $retriedPath;

            }

            // Enter The Matrix: Using Symfony Finder for robust scanning
            $finder = Finder::create()->files()->name('*.php')->in($realPath);

            // If no /* was provided, lock the scan to the top directory only (depth = 0)
            if (!$isRecursive) {
                $finder->depth('== 0');
            }

            // Apply suffix exclusions dynamically (e.g., excludes *.disabled.php)
            foreach ($excludeSuffixes as $suffix) {
                $finder->notName("*.$suffix.php");
            }

            // Ignite!
            foreach ($finder as $file) {
                $realPath = $file->getRealPath();
        
                // تمام هندلینگ‌ها، لاگ‌ها و وتوها حالا داخل این متد انجام می‌شود
                // Execute the file safely
                if($this->integrate($realPath, $isScoped))
                    $loadedCount++;
                else
                    JackPoint::fire('synapses.file.not.loaded', $realPath);
            }
        }

        // Stage 6: Absorption Completed
        JackPoint::fire('synapses.absorption.completed', $loadedCount, $this->wiredSynapseFiles);
    }

    /**
     * Safely require/register the file, respecting JackPoint's scoped execution and idempotency.
    */
    private function integrate(string $file, bool $isScoped): bool
    {
        // 1. Idempotency Check (Never run the same file twice in one process)
        if (isset($this->wiredSynapseFiles[$file])) {
            $allowSkip = JackPoint::fire('synapses.file.skipped', $file, 'already_loaded'); // Useful for debug about file-updates
            if($allowSkip !== false)
                return false;
        }

        // Veto Check via JackPoint
        // اجازه می‌دهیم سیستم قبل از لود شدن، فایل را بررسی کند. اگر false برگرداند، لغو می‌شود.
        if (JackPoint::fire('synapses.file.loading', $file) === false) {
            $finalVerdict = JackPoint::fire('synapses.file.vetoed', $file); // اعلام وتو شدن
            if($finalVerdict !== false) // `.vetoed` can reject the 'synapses.file.loading's verdict
                return false;
        }

        // 2. Mark as loaded BEFORE execution (prevents Re-entrancy loops)
        $this->wiredSynapseFiles[$file] = true;

        try {

            $registerSynapse = function () use ($file) {
                // Register JackPoint Event-HookZ
                return require_once $file;

            };

            // Execution Context Stage
            if ($isScoped && method_exists(JackPoint::class, 'scopedExecute')) {
                
                $allowedScoping = JackPoint::fire('synapses.file.scoping', $file); // اعلام ورود به محیط ایزوله

                if($allowedScoping === false)
                    $registerSynapse(); // Fallback to standard isolated execution
                else {
                    // Delegate to JackPoint's native context manager
                    // ForNow we use the filename as the scope identifier.
                    JackPoint::scopedExecute($file, $registerSynapse);
                }
            } else {
                // Fallback to standard isolated execution
                $registerSynapse();
            }

            // Stage 4: Mission Accomplished
            JackPoint::fire('synapses.file.loaded', $file);
            return true;

        } catch (Throwable $e) {

            // Catastrophic Failure
            JackPoint::fire('synapses.file.failed', $file, $e);

            // Graceful Error Handling via Log if available
            if (class_exists(Log::class)) {
                Log::error("Synapses Injection: Failed executing file [{$file}]", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            } else {
                // Native Laravel fallback
                report($e);
            }

            return false;
        }
    }
    
    /**
     * Composer-driven plugin auto-discovery
     * Discover and register plugins declared in vendor packages' composer.json
     *
     * Ignites the dependency grid to auto-discover and assimilate external vendor payloads.
     * Scrapes the `composer.json` matrix for architectural blueprints (`extra.krubot`), seamlessly
     * injecting third-party Synapses and plugins directly into the BlackWire neural net.
     * Routed through JackPoint, every injection can be mutated, vetoed, or supercharged before breaching the mainframe. ⚡️🔌
     * 
     * Packages declare plugins via:
     * {
     *   "extra": {
     *     "krubot": {
     *       "plugins": ["MyVendor\\MyPlugin"],
     *       "synapses": ["src/Synapses"]
     *     }
     *   }
     * }
    */
    protected function blazeComposer(): void
    {
        // Skip if Composer runtime API is unavailable (unlikely in production)
        if (!class_exists(Composer::class)) {
            return;
        }
       
        // Checking the Architect's permission to blaze the syringes...
        if(!$this->app['config']->get('krubot.extensions.blaze-composer-syringes', false)) {
            JackPoint::fire('blaze.discovery.disabled'); // Is This Useful ???
            return;
        }

        $discoveredPlugins = []; // will be like => ['PluginClass' => 'vendor/package']
        $discoveredSynapses = []; // will be like => [['file' => '/path/to/synapse.php', 'package' => 'vendor/package'], ...]        

        // Hook: allow listeners to veto or prepare discovery
        JackPoint::fire('blaze.discovery.started');

        $rootPackageName = Composer::getRootPackage()['name'];

        // Iterate all installed packages
        foreach (Composer::getInstalledPackages() as $packageName) {
            try {

                $extra = ($rootPackageName === $packageName) ?
                    $this->getRootComposerExtra()
                :
                    $this->getPackageExtra($packageName);

                if (!isset($extra['krubot'])) {
                    continue;
                }

                $krubotConfig = $extra['krubot'];

                // Transform hook: allow filtering/modifying package config
                $krubotConfig = JackPoint::transform(
                    'blaze.package.discovered',
                    $krubotConfig,
                    [$packageName, $extra]
                );

                if ($krubotConfig === false) {
                    continue; // Vetoed
                }

                // Register plugin classes
                if (!empty($krubotConfig['plugins'])) {
                    $this->registerPluginClasses(
                        (array) $krubotConfig['plugins'],
                        $packageName,
                        $discoveredPlugins
                    );
                }

                // Integrate Synapse directories
                if (!empty($krubotConfig['synapses'])) {
                    $this->wirePackageSynapses(
                        (array) $krubotConfig['synapses'],
                        $packageName,
                        $discoveredSynapses
                    );
                }
            } catch (Throwable $e) {
                JackPoint::fire('blaze.package.failed', [$packageName, $e]);
                $this->reportError($e);
            }
        }

        // Completion hook with stats
        JackPoint::fire('blaze.discovery.completed', [
            'plugins' => $discoveredPlugins,
            'synapses' => $discoveredSynapses,
        ]);
    }

    /**
     * Helper to read 'extra' field from a given composer.json path
    */
    protected function getExtraData(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }
        
        try {
            $json = json_decode(file_get_contents($path), true);
            return $json['extra'] ?? [];
        } catch (Throwable $th) {
            return [];
        }
    }

    /**
     * Get 'extra' field from root composer.json
    */
    protected function getRootComposerExtra(): array
    {
        return $this->getExtraData(base_path('composer.json'));
    }

    /**
     * Get 'extra' field from a vendor package's composer.json
    */
    protected function getPackageExtra(string $packageName): array
    {
        $installPath = Composer::getInstallPath($packageName);
        if (!$installPath) {
            return [];
        }

        $composerPath = $installPath . '/composer.json';
        return $this->getExtraData($composerPath);
    }

    /**
     * Register plugin classes via JackPoint
    */
    protected function registerPluginClasses(array $pluginClasses, string $packageName, array &$registry): void
    {
        foreach ($pluginClasses as $pluginClass) {

            // Veto hook per class
            if (JackPoint::transform('syringe.loading', true, [$pluginClass, $packageName]) === false) {
                JackPoint::fire('syringe.vetoed', [$pluginClass, $packageName]);
                continue;
            }

            if (!class_exists($pluginClass)) {
                JackPoint::fire('syringe.class.not.found', [$pluginClass, $packageName]);
                continue;
            }

            try {

                // Fire the standard plugin registration event through ToxicOverlord.
                JackPoint::install($pluginClass);
                
                $registry[$pluginClass] = $packageName;
                JackPoint::fire('syringe.injected', [$pluginClass, $packageName]);
            } catch (Throwable $e) {
                JackPoint::fire('syringe.failed', [$pluginClass, $packageName, $e]);
                $this->reportError($e);
            }
        }
    }

    /**
     * Integrate Synapse directories from a vendor package
     * Reuses existing integrate() idempotency + hooks
    */
    protected function wirePackageSynapses(array $synapseDirs, string $packageName, array &$registry): void
    {
        $installPath = Composer::getInstallPath($packageName);
        if (!$installPath) {
            return;
        }

        foreach ($synapseDirs as $relativeDir) {
            $absoluteDir = $installPath . '/' . ltrim($relativeDir, '/');

            if (!is_dir($absoluteDir)) {
                JackPoint::fire('synapses.dir.not.found', [$absoluteDir, $packageName]);
                continue;
            }

            // Use Symfony Finder to scan (same as wireSynapses)
            $finder = new Finder();
            $finder->files()
                ->in($absoluteDir)
                ->name('*.php')
                ->notName('*.disabled.php'); // Respect same exclusion pattern

            foreach ($finder as $file) {
                $filePath = $file->getRealPath();
                
                // Reuse existing integrate() method — gets idempotency, hooks, scoping
                if ($this->integrate($filePath)) {
                    $registry[] = ['file' => $filePath, 'package' => $packageName];
                }
            }
        }
    }

    /**
     * Error reporting (same pattern as integrate())
    */
    protected function reportError(Throwable $e): void
    {
        if (class_exists(Log::class)) {
            Log::error($e);
        } elseif (function_exists('report')) {
            report($e);
        }
    }

    /**
     * Load WordPress-style helper functions if enabled in config.
    */
    protected function loadWordPressApiHelpers(): void
    {
        if (config('krubot.extensions.wp_plugin_api', false)) {
            $helperPath = __DIR__ . '/../Extensions/WP_NeuralRail.php';

            if (file_exists($helperPath)) {
                require_once $helperPath;
            }
        }
    }
}
