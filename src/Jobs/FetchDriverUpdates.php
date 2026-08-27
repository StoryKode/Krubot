<?php

namespace KrubiK\Jobs;
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

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use KrubiK\Helpers\AmethystMatrix as Log;
// اینجا از KrubiK\KrubotManager استفاده می‌کنیم تا درایور خاص را بسازیم.
use KrubiK\Drivers\Nemesis as KrubotManager; 
use Throwable;

/**
 * FetchDriverUpdates v4.0 (Infinity Protocol)
 *
 * This Job replicates the classic "Long Polling" loop mechanism but adapted for
 * Laravel's Queue Architecture. Instead of blocking a process indefinitely with `while(true)`,
 * this job performs a single "Fetch Cycle", dispatches processing jobs for found updates,
 * and then (optionally) re-dispatches itself to maintain the loop via the Queue Worker.
 *
 * It handles:
 * 1. Secure Offset Management (via Laravel Cache instead of flat files).
 * 2. Bulk Update Fetching (getUpdates).
 * 3. Dispatching `HandleDriverUpdate` for each individual message (The Aegis Protocol).
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class FetchDriverUpdates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The target driver name (Instance Identity).
     * e.g., 'rubika', 'tel2'
     */
    protected string $driverName;

    /**
     * The cache key used to store the last processed update ID (offset).
     * This replaces the file_put_contents($hashedToken . '.txt') method.
    */
    // protected const OFFSET_CACHE_KEY = 'krubot:polling:offset_id';

    /**
     * The Gatekeeper Lock Key.
     * Prevents race conditions between Pulse and Lazarus run simultaneously.
    */
    // protected const FETCH_GATEKEEPER_LOCK_KEY = 'krubot:polling:gatekeeper_lock';

    /**
     * Create a new job instance.
     * We pass the Target Name to the Hunter; during generate payloads
     */
    public function __construct(string $driverName)
    {
        $this->driverName = $driverName;
    }

    /**
     * Execute the job.
     *
     * @param KrubotManager $manager The singleton KrubotManager instance (injected).
     * @return void
     */
    public function handle(KrubotManager $manager): void
    {
        // -----------------------------------------------------------------
        // 🛑 PHASE 0-A: CONFIG CHECK (The Kill Switch)
        // 🛑 KILL SWITCH: Check if polling is globally enabled
        // -----------------------------------------------------------------
        // If polling is disabled in config (e.g. relying on Webhooks), abort immediately.
        if (! config('krubot.polling.enabled', true)) {
            return;
        }

        // -----------------------------------------------------------------
        // 🛡️ PHASE 0-B: THE GATEKEEPER PROTOCOL (ATOMIC LOCK)
        // -----------------------------------------------------------------
        // We try to acquire a lock for 10 seconds.
        // If Lazarus is fetching, Pulse will see this locked and return silently.
        // If Lazarus dies, the lock expires automatically, and Pulse takes over.

        // Attempt to acquire lock for 10s. If occupied (by Lazarus or Pulse), abort gracefully.
        // $lock = Cache::lock(self::FETCH_GATEKEEPER_LOCK_KEY, 10);

        // -----------------------------------------------------------------
        // 🔐 PHASE 0-B: The DYNAMIC GATEKEEPER (Per-Driver Locking)
        // -----------------------------------------------------------------
        // Critical: The lock key MUST include the driver name.
        // Otherwise, polling 'rubika' would block polling 'tel2'.
        $lockKey = "krubik:polling:gatekeeper:{$this->driverName}";

        // Attempt to acquire lock for 10s. If occupied (by Lazarus or Pulse), abort gracefully.
        $lock = Cache::lock($lockKey, 10);

        if (!$lock->get()) {
            // Optional: Log collision only for debugging
            // Log::debug("FetchRubikaUpdates: Skipped execution. Another instance is already fetching.");

            return;  // Busy fetching for THIS driver; Silently exit, let the other process finish.

        }

        try {
            // =================================================================
            // STEP 1: PREPARE POLLING PARAMETERS
            // =================================================================
            $params = ['limit' => 100];

            // 1. Resolve the Bot Instance
            // Manager creates the specific bot for 'tel2' or 'rubika'
            $botDriver = $manager->driver($this->driverName);

            // 2. Prepare Dynamic Offset Key
            // e.g. krubot:polling:rubika:offset_id
            $offsetKey = "krubik:polling:{$this->driverName}:offset_id";

            // Retrieve the last known offset from Cache (Laravel 11.47.0 style)
            // If not found, it returns null, starting from the beginning/latest.
            // $offset_id = Cache::get(self::OFFSET_CACHE_KEY);
            $offset_id = Cache::get($offsetKey);

            if ($offset_id) {
                $params['offset_id'] = $offset_id;
            }

            // =================================================================
            // STEP 2: EXECUTE API CALL (getUpdates)
            // =================================================================
            // We use the injected $botDriver instance to call the API.
            // Note: Ensure getUpdates() is public/accessible in your Krubot/Bot class.
            $updatesResponse = $botDriver->getUpdates($params);
            // $botDriver is now the correct instance (TelegramDriver or RubikaDriver)
            // Polymorphism for getUpdates() handles the rest.

            // =================================================================
            // STEP 3: ANALYZE RESPONSE
            // =================================================================
            // If no updates found, we just finish this job cycle.
            // The loop continuance is handled by the Scheduler or Recursive Dispatch.
            if (empty($updatesResponse['data']['updates'])) {
                // Optional: Log heartbeat for debugging polling health
                // Log::debug('Polling heartbeat: No new updates.');
                return;
            }

            // =================================================================
            // STEP 4: UPDATE OFFSET (STATE MANAGEMENT)
            // =================================================================
            if (isset($updatesResponse['data']['next_offset_id'])) {
                $nextOffset = $updatesResponse['data']['next_offset_id'];
                
                // Store indefinitely (or for a very long time) until next update overwrites it.
                // Cache::forever(self::OFFSET_CACHE_KEY, $nextOffset);
                Cache::forever($offsetKey, $nextOffset);
                
                Log::info("Offset updated to: {$nextOffset}");
            }

            // =================================================================
            // STEP 5: DISPATCH PROCESSING JOBS (FAN-OUT)
            // =================================================================
            // Instead of processing inside the loop, we delegate each message
            // to the Aegis Protocol (HandleDriverUpdate). This allows parallel processing!
            
            foreach ($updatesResponse['data']['updates'] as $updateRaw) {
                // We wrap the update in a standard structure expected by HandleDriverUpdate
                // The older code did: $this->update = ['update' => $update];
                // $payload = ['update' => $updateRaw];

                // Dispatch the Aegis Protocol Job for this specific message.
                // This puts the processing into the queue, keeping the Poller light and fast.
                // HandleDriverUpdate::dispatch($payload);

                HandleDriverUpdate::dispatch([
                    'update' => $updateRaw,
                    'driver' => $this->driverName // Optional: Pass context
                ]);
            }

            Log::info("Dispatched " . count($updatesResponse['data']['updates']) . " updates to Aegis Protocol.");

        } catch (Throwable $e) {
            // =================================================================
            // STEP 6: ERROR CONTAINMENT
            // =================================================================
            /*Log::error("Polling Error (Infinity Protocol): " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);*/

            Log::error("Polling Error (Infinity Protocol) For Driver `[{$this->driverName}]`: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // We do not re-throw, to prevent the worker from marking this job as failed repeatedly 
            // in a way that stops the queue. We log and exit, ready for the next scheduled run.
        } finally {
            // 🔓 Always release the lock so the next cycle (Lazarus or Pulse) can run immediately.
            // If we don't release, the next run has to wait 10s for expiration.
            $lock->release();
        }
    }
}
