<?php

namespace KrubiK\Providers;
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

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

/**
 * =========================================================================
 *  🚦 KRUBIK ROUTE SENTINEL (TITANIUM CORE)
 * =========================================================================
 *          v3.2.0 "Titanium Core"
 * The absolute authority on KrubiK routing logic.
 * This provider is the "First Responder" of the package.
 * It is NOT deferrable. It wakes up on every request to ensure
 * KrubiK's endpoints (Webhooks, Dashboard, Utils) are registered in the Laravel Router.
 *
 * Implements the "Hybrid Configuration" pattern:
 *  - Hardcoded Paths: For structural integrity & zero breakage.
 *  - Dynamic Behavior: For total developer control via config.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class KrubotRouteProvider extends ServiceProvider
{
    public function register(): void
    {
        // Ensure config is merged so we have access to 'krubot.routes.*' keys
        // This is CRITICAL because the Main Provider might be deferred (sleeping),
        // but we need the config NOW to register routes correctly.
        $this->mergeConfigFrom(__DIR__ . '/../../config/krubot.php', 'krubot');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ConfigRepository $config): void
    {
        // ⚡ Quick Exit: If webhook routing is globally disabled, burn zero cycles.
        if (! $config->get('krubot.routing.enabled', true)) {
            return;
        }

        $this->mapApiRoutes($config);
        $this->mapWebRoutes($config);
    }

    /**
     * Maps the "API" routes (Webhooks, Stateless interactions).
     */
    protected function mapApiRoutes(ConfigRepository $config): void
    {
        if (! $this->shouldMapGroup('api', $config)) {
            return;
        }

        $path = __DIR__ . '/../../routes/api.php';

        if (file_exists($path)) {
            Route::group($this->routeConfiguration('api', $config), $path);
        }
    }

    /**
     * Maps the "Web" routes (Dashboard, Utilities, Stateful interactions).
     */
    protected function mapWebRoutes(ConfigRepository $config): void
    {
        if (! $this->shouldMapGroup('web', $config)) {
            return;
        }

        $path = __DIR__ . '/../../routes/web.php';

        if (file_exists($path)) {
            Route::group($this->routeConfiguration('web', $config), $path);
        }
    }

    /**
     * Constructs the behavioral configuration for a route group.
     * Merges Laravel defaults with user overrides intelligently.
     */
    protected function routeConfiguration(string $group, ConfigRepository $config): array
    {
        $prefix = $config->get("krubot.routing.groups.{$group}.prefix");
        $domain = $config->get("krubot.routing.groups.{$group}.domain");
        $userMiddleware = (array) $config->get("krubot.routing.groups.{$group}.middleware", []);
        
        // Resolve the default middleware group (e.g., 'web' or 'api')
        $defaultGroup = $config->get("krubot.routing.groups.{$group}.apply_laravel_defaults", $group);

        // Merge and unique: [Default Group] + [User Middlewares]
        $middleware = array_unique(array_merge(
            (array) $defaultGroup,
            $userMiddleware
        ));

        // Build the precise array required by Route::group()
        return array_filter([
            'domain'     => $domain,
            'prefix'     => $prefix,
            'middleware' => $middleware,
        ], fn($value) => !is_null($value)); // Clean nulls strictly
    }

    /**
     * Determines if a specific route group is enabled.
     */
    private function shouldMapGroup(string $group, ConfigRepository $config): bool
    {
        return (bool) $config->get("krubot.routing.groups.{$group}.enabled", false);
    }
}
