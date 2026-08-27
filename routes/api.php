<?php

use Illuminate\Support\Facades\Route;
use KrubiK\Controllers\SuperWebhookController;
use KrubiK\Controllers\QuantumGatewayController;
// use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

/*
|--------------------------------------------------------------------------
| KrubiK API Routes (Webhooks)
|--------------------------------------------------------------------------
|
| Here is where the "SuperWebhookController" lives. These routes are
| loaded by the KrubotRouteProvider within a group which
| is assigned the "api" middleware group by default.
|
*/

// The Main Entry Point for all Webhook Updates to Nemesis/Krubot
/*
|--------------------------------------------------------------------------
| API Routes :: The Server-to-Server Gateway
|--------------------------------------------------------------------------
|
| This route is the dedicated, high-performance entry point for webhooks
| from platforms like Telegram. It's stateless, protected by a specific
| 'api.webhook' middleware group, and designed for one thing:
| to catch, validate, and queue updates as fast as possible.
|

This single route now governs all incoming webhook traffic.
The '{driver?}' parameter makes it configurable & optional,
allowing Nemesis[KrubotManager]'s payload-sniffing to work its magic for legacy webhooks.
*/

Route::any('/run-krubik/{driver?}', [QuantumGatewayController::class, 'handleWebhook'])
    // ->withoutMiddleware([VerifyCsrfToken::class); // 🛡️ Bypass CSRF for Webhooks, Not Needed For API Routes
    ->middleware('ssp.protocol') // 🛡️ Attach the Synaptic Surge Protocol as a silent guardian.
    ->name('nexus.quantum.resonance'); // The Point of Resonance
