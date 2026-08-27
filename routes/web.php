<?php

use Illuminate\Support\Facades\Route;
use KrubiK\Controllers\QuantumGatewayController;
use KrubiK\WebApps\Middlewares\AuthenticateWebApp;
use KrubiK\WebApps\Middlewares\AdaptiveCsrfVerify;

use Illuminate\Http\Request;
use KrubiK\Helpers\OpcacheRuler;

// -----------------------------------------------------------------------------
// 🧹 UTILITY ROUTES
// -----------------------------------------------------------------------------
/*
|--------------------------------------------------------------------------
| KrubiK Web Routes (Dashboard & Utils)
|--------------------------------------------------------------------------
|
| These routes are loaded by KrubotRouteServiceProvider with the "web"
| middleware group. Designed for browser-based interaction.
|
*/

Route::get('/clear-cache', function () {
    $commands = [
        'optimize:clear',
        'config:clear',
        // 'cache:clear', // Fu**s History of Conversations,Forms,Chains,InlineMenus,...! a !_Dangerous_CMD_!
        'route:clear',
        'view:clear',
        'event:clear',
        'schedule:clear-cache',

        // Re-cache for performance
        'krubik:nexus-cache',
        'config:cache',
    ];

    $outputBuffer = collect();

    // \Illuminate\Support\Facades\Cache::flush();
    foreach ($commands as $command) {
        try {
            Artisan::call($command);
            $outputBuffer->push(trim(Artisan::output()));
        } catch (\Throwable $e) {
            $outputBuffer->push("❌ Error running {$command}: " . $e->getMessage());
        }
    }

    // return nl2br("<b>Cache Cleared Successfully!</b> ::: " . $output);
    return Response::make(
        str($outputBuffer->implode(PHP_EOL . PHP_EOL))
            ->prepend("✅ **SYSTEM PURGE COMPLETE** ✅" . PHP_EOL . "================================" . PHP_EOL)
            ->append(PHP_EOL . "================================" . PHP_EOL . "🚀 Ready for deployment.")
            ->replace(PHP_EOL, '<br>')
    );
})->name('krubik.clear-cache');



/*
|--------------------------------------------------------------------------
| Web Routes :: The User-Facing Gateway
|--------------------------------------------------------------------------
|
| This route handles all interactions from user-facing WebApps and MiniApps.
| It lives within the 'web' middleware group, giving it access to sessions,
| CSRF protection, and cookies. We add our custom 'webapp.auth' middleware
| to this stack to handle the specific logic of authenticating users
| via mechanisms like Telegram's InitData.
|
*/

// The 'web' group is applied by default in RouteServiceProvider
Route::any('/webapps/{path?}', [QuantumGatewayController::class, 'handleWebApp'])
    ->middleware([
        // STEP 1: Resolve and attach identity via Our specific WebApp Identity Investigator ; This is the Single Source of Truth.
        AuthenticateWebApp::class,
        // STEP 2: Read the attached identity and apply intelligent host-conditional CSRF protection.
        AdaptiveCsrfVerify::class,
    ])
     ->withoutMiddleware([
        // Still necessary to prevent Laravel's default global middleware from running on this route.
        \Illuminate\Foundation\Http\Middleware\ValidateCsrfTokens::class,       // Laravel 11+ Core
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,          // Legacy Core & App Default
        \App\Http\Middleware\ValidateCsrfTokens::class,                         // Possible App Customization
        \App\Http\Middleware\VerifyCsrfToken::class,                            // The Classic App Customization
    ])
    ->where('path', '.*')
    ->name('nexus.quantum.portal'); // The First Portal to Infinite Dev_Experience ✔️♾️

// OpCache Headquarters for CLI->Web
// @see \KrubiK\Helpers\OpcacheRuler
Route::post(config('krubot.cache.opcache.bridge_uri', '_internal/opcache-manager'), function (Request $request, OpcacheRuler $opcache) {
    
    // we even check for IP here
    if ($request->header('X-Opcache-Secret') !== config('krubot.cache.opcache.bridge_secret')) {
        abort(403);
    }
    
    // We delegate EVERYTHING to the Ruler, which will execute locally (in the FPM/Web context).
    $action = $request->input('action');
    $payload = $request->except('action');
    
    $result = match($action) {
        'status' => $opcache->status(),
        'config' => $opcache->config(),
        'reset' => ['success' => $opcache->reset()],
        'invalidate' => ['success' => $opcache->invalidate($payload['file'])],
        'check' => ['is_cached' => $opcache->status($payload['file'])],
        default => null,
    };
    
    if (is_null($result)) {
        return response()->json(['message' => "Unknown action: {$action}"], 400);
    }

    return response()->json(['data' => $result]);
});
