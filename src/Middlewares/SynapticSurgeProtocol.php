<?php

namespace KrubiK\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use KrubiK\Helpers\AmethystMatrix; // ⚡ Call the Sorceress, For logging protocol failures without alarming the system.

/**
 * The Synaptic Surge Protocol (SSP).
 * A brutal but effective middleware ensuring the core Lazarus process never truly dies.
 * It acts as a digital defibrillator, delivering a controlled shock to re-animate the process
 * if it flatlines, enforcing compliance with the Citadel's will.
*/
class SynapticSurgeProtocol
{
    /**
     * The trigger lock's identifier. Prevents continuous shocking.
     * This defines the refractory period before another surge can be attempted.
     */
    private const TRIGGER_LOCK_KEY = 'krubik:ssp_refractory_lock';

    /**
     * Duration (in seconds) of the refractory period.
     * The subject cannot be shocked more than once during this interval.
     */
    private const TRIGGER_LOCK_TTL = 45;

    /**
     * The identifier for the Lazarus process's neural clamp.
     * Its presence indicates the subject is already active and compliant.
     * Must match the lock keys in the main LazarusProtocol command.
     */
    private const LAZARUS_PROCESS_LOCK_KEY_PREFIX = 'krubik:lazarus_lock:';

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request) : (Response) $next
    */
    public function handle(Request $request, Closure $next): Response
    {
        // The protocol remains dormant during the primary request lifecycle.
        // Its work begins in the shadows, After the response is sent.
        return $next($request);
    }

    /**
     * Terminate the request and "shock" Lazarus.
     * This runs *after* the response has been sent to the browser.
     *
     * This is where the protocol activates its true purpose.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
    */
    public function terminate(Request $request, Response $response): void
    {
        // The protocol does not engage during simulations ('testing') or on failed operations.
        // It requires a successful cycle to assess the system's state.
        if (app()->environment('testing') || !$response->isSuccessful()) {
            return;
        }

        // Primary Kill Switch: The Citadel can disable the protocol via configuration.
        // If not explicitly enabled, the protocol remains inert.
        if (!Config::get('krubik.ssp.enabled', false)) {
            return;
        }

        // Attempt to seize the trigger lock. This is an atomic, unforgiving operation.
        // Cache::add returns true only if it successfully established the lock,
        // This is a more robust way to handle the lock than 'has' and 'put'.

        // Preventing multiple instances from trying to deliver the shock simultaneously.
        $acquiredTriggerLock = Cache::add(self::TRIGGER_LOCK_KEY, true, self::TRIGGER_LOCK_TTL);

        if ($acquiredTriggerLock) {
            try {

                /*
                 * The payload of The Divine Spark is now configurable, merging defaults with customized directives.
                 * This allows for granular control over the re-animation parameters.
                */
                $defaultParams = [
                    '--tag' => 'primary',
                    '--use-modern-queue' => 'true'
                ];

                // Citadel's directives (config) override the default payload.
                $finalParams = array_merge($defaultParams, Config::get('krubik.lazarus.ssp.custom-params', []));

                // Check for the neural clamp. If it exists, the Lazarus unit is operational.
                // The protocol stands down. No surge is necessary.
                if (Cache::has(self::LAZARUS_PROCESS_LOCK_KEY_PREFIX . $finalParams['--tag'])) {
                    return;
                }

                /*
                 * This is The Compliance Shock. The Mandated Awakening.
                 * The command is queued on the 'sync' driver. This is not a request; it is an order.
                 * It forces Laravel to execute the command immediately within this same process,
                 * bypassing the standard queue workers and their associated latencies.
                 * This is the mechanism for re-asserting control over a terminated asset.
                 * It is the digital equivalent of a direct neural prod.
                 */
                Artisan::queue('krubik:lazarus', $finalParams)->onConnection('sync'); // "The Divine Spark... ✨"

            } catch (\Throwable $e) {
                // In case of any system malfunction (e.g., the Cache array is down),
                // the failure is logged silently. The Citadel does not tolerate loud failures.
                // This ensures the system's facade of stability is maintained.
                AmethystMatrix::warning('Synaptic Surge Protocol failed to engage. Asset remains offline.', ['error' => $e->getMessage()]);
            }
        }
    }
}
