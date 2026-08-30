<?php
declare(strict_types=1);
/*
|--------------------------------------------------------------------------
| Krubot CyberCitadel — Ritual Bootstrapper
| Hardened Composition: v6 (structural) + v12.x (field logic)
|--------------------------------------------------------------------------
|
| Loaded by Composer's generated autoloader via the "files" autoload key.
|
| SINGLE GOAL: execute  \KrubiK\Console\RitualEngine::TakeCovenant()
|              reliably, EXACTLY ONCE, after Laravel is fully booted
|              inside the  `php artisan package:discover`  invocation.
|
| Composition notes
| ──────────────────
|  From v6    : structural layering, 5-signal argv detection, project-root
|               walker, 4-path class fallback loader, pcntl SIGTERM hook,
|               fatal-error guard, atomic $alreadyFired static flag.
|
|  From v12   : named function in global scope (survive opcache/JIT flush
|               better than closures in some envs), Throwable catch with
|               dual-channel error reporting (Laravel logger + error_log),
|               $isComposerAction extended detection, define() guard placed
|               AFTER SAPI check (fixed ordering bug from v12.x).
|
| Defense layers (all preserved, none weakened)
| ──────────────────────────────────────────────
|  0. PHP_SAPI guard              — non-CLI → bail before ANY side-effect.
|  1. KRUBIK_RITUAL_ARMED define  — process-wide re-entry guard (v12.x).
|  2. Six independent argv signals — package:discover detection (v6 ×5
|                                    + v12.x composer-action broadening).
|  3. register_shutdown_function  — primary execution point (v6).
|  4. Fatal-error guard           — E_ERROR / E_PARSE / E_CORE_ERROR (v6).
|  5. Four-path class fallback    — spl → vendor/ → packages/ → __DIR__
|                                    (v6), guarded by class_exists (v12.x).
|  6. Atomic "fired" flag         — named function + static local (v12.x).
|  7. Throwable catch             — dual-channel logging; never silently
|                                    drops; never lets Composer crash (v12.x).
|  8. pcntl SIGTERM hook          — secondary execution point (v6).
|
*/

/* ═══════════════════════════════════════════════════════════════════════
   Layer 0 — non-CLI bail-out  (MUST be first; no side-effects before this)
   ═══════════════════════════════════════════════════════════════════════ */
if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg') {
    return;
}

/* ═══════════════════════════════════════════════════════════════════════
   Layer 1 — process-wide re-entry guard
   ═══════════════════════════════════════════════════════════════════════
   Placed AFTER the SAPI check so the constant is never defined in a
   web/FPM context (fixed ordering bug present in v12.x).
   Using a constant (not a static variable) makes it immune to:
     • duplicate file includes via require/include
     • OPcache sharing the file across processes on some configurations
*/
if (defined('KRUBIK_RITUAL_ARMED')) {
    return;
}
define('KRUBIK_RITUAL_ARMED', true);

/* ═══════════════════════════════════════════════════════════════════════
   Layer 2 — six independent package:discover detection signals
   ═══════════════════════════════════════════════════════════════════════
   Why not trust a single argv index?
     • Shells and IDE launchers inject wrapper args before 'artisan'.
     • Composer calls artisan as a sub-process with extra flags.
     • Some CI/CD wrappers set ARTISAN_COMMAND instead of using argv.
     • composer install/update/dump-autoload trigger package:discover
       internally — we need to arm ourselves in those parent processes
       so the child artisan process (where argv[1] IS package:discover)
       is already covered by layers 1-8.
*/
(static function (): void {

    /* Merge $_SERVER['argv'] and $GLOBALS['argv'] defensively. */
    $argv = array_values(array_filter(
        array_merge(
            array_map('strval', $_SERVER['argv'] ?? []),
            array_map('strval', $GLOBALS['argv'] ?? [])
        ),
        'is_string'
    ));

    $command          = $argv[1] ?? '';
    $fullCommandLine  = implode(' ', $argv);

    /*
     * Signal A — canonical artisan invocation:
     *   php artisan package:discover
     *   argv[1] === 'package:discover'
     */
    $isPackageDiscover = ($command === 'package:discover');

    /*
     * Signal B — linear scan (catches any argv position):
     *   php /full/path/to/artisan --no-ansi package:discover
     */
    if (! $isPackageDiscover) {
        foreach ($argv as $segment) {
            if ($segment === 'package:discover') {
                $isPackageDiscover = true;
                break;
            }
        }
    }

    /*
     * Signal C — substring match on the full reconstructed line:
     *   Catches flags injected between the command and its name.
     */
    if (! $isPackageDiscover && str_contains($fullCommandLine, 'package:discover')) {
        $isPackageDiscover = true;
    }

    /*
     * Signal D — ARTISAN_COMMAND env var (some CI/CD wrappers):
     *   export ARTISAN_COMMAND=package:discover
     */
    if (! $isPackageDiscover) {
        $envCmd = (string) ($_SERVER['ARTISAN_COMMAND'] ?? getenv('ARTISAN_COMMAND') ?: '');
        if ($envCmd === 'package:discover' || str_contains($envCmd, 'package:discover')) {
            $isPackageDiscover = true;
        }
    }

    /*
     * Signal E — Composer parent-process broadening (from v12.x):
     *   When the user runs `composer install`, `composer update`, or
     *   `composer dump-autoload`, Composer calls artisan package:discover
     *   in a child process.  In that CHILD process, argv[1] will be
     *   'package:discover', which Signals A-D already catch.
     *
     *   In the PARENT Composer process this file is included too (via
     *   the freshly-generated autoloader), but argv[1] there is 'install'
     *   etc., so we arm ourselves defensively.  The define() guard (Layer 1)
     *   prevents double-execution in the child.
     */
    if (! $isPackageDiscover) {
        $isComposerTrigger = in_array($command, ['install', 'update', 'dump-autoload', 'require', 'remove'], true);
        if ($isComposerTrigger) {
            $isPackageDiscover = true;
        }
    }

    /*
     * Signal F — COMPOSER env var set by Composer itself:
     *   Composer always exports COMPOSER pointing to composer.json.
     *   If it's set AND we are in a composer-related process, arm up.
     */
    if (! $isPackageDiscover) {
        $composerEnv = (string) ($_SERVER['COMPOSER'] ?? getenv('COMPOSER') ?: '');
        if ($composerEnv !== '' && str_ends_with($composerEnv, 'composer.json')) {
            $isPackageDiscover = true;
        }
    }

    if (! $isPackageDiscover) {
        return;
    }

    /* ─── Locate project root ─────────────────────────────────────────
       Walk up from this file looking for artisan + bootstrap/ together.
       Fallback to getcwd() which Composer and Artisan both set to the
       project root before invoking sub-processes.
    ──────────────────────────────────────────────────────────────────── */
    $projectRoot = (static function (): string {
        $dir = __DIR__;
        for ($depth = 0; $depth < 10; $depth++) {
            if (is_file($dir . '/artisan') && is_dir($dir . '/bootstrap')) {
                return $dir;
            }
            $parent = dirname($dir);
            if ($parent === $dir) {
                break;
            }
            $dir = $parent;
        }
        return rtrim((string) getcwd(), '/\\');
    })();

    /* ─── Named fire function (Layer 6 + 7) ──────────────────────────
       Using a NAMED FUNCTION (not a closure) gives better stack-trace
       readability, survives certain OPcache edge-cases on shared hosts,
       and makes the function_exists guard meaningful for deduplication
       across multiple autoloader includes.

       NOTE: No namespace declaration anywhere in this file — this is a
       file-level bootstrap script.  The function lives in the global
       namespace intentionally, prefixed to avoid collisions.
    ──────────────────────────────────────────────────────────────────── */
    if (! function_exists('_krubik_omega_gate_fire')) {

        /**
         * Load \KrubiK\Console\RitualEngine via four independent paths,
         * then call ::TakeCovenant() exactly once, with Throwable protection.
         *
         * @param string $projectRoot
         */
        function _krubik_omega_gate_fire(string $projectRoot): void
        {
            /* Atomic once-only guard (Layer 6). */
            static $alreadyFired = false;
            if ($alreadyFired) {
                return;
            }
            $alreadyFired = true;

            /* ── Layer 5: four-path class fallback loader ── */
            if (! class_exists(\KrubiK\Console\RitualEngine::class, false)) {
                /* Path 1 — trigger all registered SPL autoloaders. */
                spl_autoload_call(\KrubiK\Console\RitualEngine::class);
            }

            if (! class_exists(\KrubiK\Console\RitualEngine::class, false)) {
                /* Paths 2-4 — direct require as last resort. */
                $candidates = [
                    $projectRoot . '/vendor/krubik/krubot/src/Console/RitualEngine.php',
                    $projectRoot . '/packages/KrubiK/src/Console/RitualEngine.php',
                    dirname(__DIR__)    . '/Console/RitualEngine.php',
                    dirname(__DIR__, 2) . '/src/Console/RitualEngine.php',
                ];
                foreach ($candidates as $file) {
                    if (is_file($file)) {
                        require_once $file;
                        if (class_exists(\KrubiK\Console\RitualEngine::class, false)) {
                            break;
                        }
                    }
                }
            }

            if (! class_exists(\KrubiK\Console\RitualEngine::class, false)) {
                /* Class truly not found — log and bail without crashing. */
                error_log('[KrubiK] CRITICAL: RitualEngine class not found; ritual aborted.');
                return;
            }

            /* ── Layer 7: Throwable catch with dual-channel logging ── */
            try {
                \KrubiK\Console\RitualEngine::TakeCovenant();
            } catch (\Throwable $e) {
                /*
                 * Channel 1 — Laravel logger (available only after boot).
                 * We check function_exists to avoid a fatal if called too
                 * early (e.g., from the SIGTERM handler on a half-booted app).
                 */
                if (function_exists('logger')) {
                    try {
                        logger()->error('[KrubiK CyberCitadel] OmegaGate Ritual Failure: ' . $e->getMessage(), [
                            'exception' => $e,
                        ]);
                    } catch (\Throwable) {
                        /* logger itself is broken — fall through to channel 2. */
                    }
                }

                /*
                 * Channel 2 — PHP's error_log (always available).
                 * Never lets Composer crash; never silently drops the error.
                 */
                error_log(sprintf(
                    '[KrubiK Fatal] RitualEngine::TakeCovenant() threw %s: %s in %s:%d',
                    get_class($e),
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                ));
            }
        }
    }

    /* Capture $projectRoot into a variable the closures below can use. */
    $root = $projectRoot;

    /* ═══════════════════════════════════════════════════════════════════
       Layer 3 — PRIMARY execution point: register_shutdown_function
       ═══════════════════════════════════════════════════════════════════
       Execution timeline inside  `php artisan package:discover`:

         vendor/autoload.php           ← this file is included here
             ↓
         bootstrap/app.php             ← Laravel boots
             ↓
         Artisan::handle('package:discover')
             ↓
         command exits normally
             ↓
         PHP shutdown sequence begins
             ↓
         [OUR shutdown handler fires]  ← Laravel fully booted ✓
             ↓
         _krubik_omega_gate_fire()
             ↓
         RitualEngine::TakeCovenant()

       register_shutdown_function is the gold-standard hook because it
       fires AFTER Application::run() returns, meaning all service
       providers, configs, and the translation system (__()) are available.
    */
    register_shutdown_function(static function () use ($root): void {
        /* ── Layer 4: fatal-error guard ──────────────────────────────
           A fatal during bootstrap means Laravel never finished booting.
           Running the ritual against a broken environment would produce
           misleading errors and potentially corrupt state.
        ─────────────────────────────────────────────────────────────── */
        $last = error_get_last();
        if (
            $last !== null &&
            in_array($last['type'], [
                E_ERROR,
                E_PARSE,
                E_CORE_ERROR,
                E_CORE_WARNING,
                E_COMPILE_ERROR,
                E_COMPILE_WARNING,
            ], true)
        ) {
            return;
        }

        _krubik_omega_gate_fire($root);
    });

    /* ═══════════════════════════════════════════════════════════════════
       Layer 8 — SECONDARY execution point: pcntl SIGTERM hook
       ═══════════════════════════════════════════════════════════════════
       Some process managers (systemd, supervisord, Docker) send SIGTERM
       for graceful shutdown BEFORE PHP's normal shutdown sequence.
       The atomic $alreadyFired flag in _krubik_omega_gate_fire() prevents
       double-execution when both this handler and the shutdown function fire.
    */
    if (function_exists('pcntl_async_signals') && function_exists('pcntl_signal')) {
        pcntl_async_signals(true);
        pcntl_signal(SIGTERM, static function () use ($root): void {
            _krubik_omega_gate_fire($root);
            exit(0);
        });
    }

})();
