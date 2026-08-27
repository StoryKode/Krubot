<?php

namespace KrubiK\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use KrubiK\Krubot;
use KrubiK\DTOs\UniversalInboundUpdate; // <--- Engage The Omega Toxic DTO
use KrubiK\DTOs\Message;
use KrubiK\Helpers\AmethystMatrix as Log;
use Throwable;

/**
 * HandleDriverUpdate v5.0 (Omni-Channel + Aegis Protocol + Omega Toxic DTO)
 *
 * This Job acts as a hardened, bulletproof entry point for every incoming MESSAGE update.
 * This Job acts as a hardened entry point for ANY driver update (Rubika, Bale, Telegram).
 * It carries the "Identity" of the driver to prevent Cross-Wiring.
 * Its sole responsibility is to safely transport the raw payload from the queue,
 * adapt it into a standard `Message` object, and hand it over to the pre-booted,
 * fully-configured Krubot singleton for processing.
 *
 * It embodies the Single Responsibility Principle: it doesn't route, it doesn't handle logic,
 * it simply prepares and delegates.
 *
 * It is the unbreakable shield (Aegis) of the entire system.
 *
 *                  Toxic DTOs Update:
 * The unbreakable shield, powered by PHP 8.2 Strict Typing.
 * It receives a pre-validated, immutable DTO and orchestrates the bot logic.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class HandleDriverUpdate implements ShouldQueue
{
    // These traits are standard for a robust, queueable Laravel job.
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The raw update payload received from the Rubika webhook.
     * We use modern PHP 8.0+ constructor property promotion for clean, concise code.
     * The payload is stored as a simple array, making it highly serializable for queues.
     
     * @param public array $payload
     * /
    public function __old_construct(
        public array $payload
    ) {} */

    /**
     * The Identity of the Driver (e.g., 'rubika', 'bale', 'tel2').
     * Crucial for routing the response back to the correct platform.     
       public string $driverName;
    */

    /**
     * The Immutable Payload DTO.
     * PHP 8.2 will serialize this object perfectly for the queue.
     */
    public function __construct(
        public UniversalInboundUpdate $payload,
        public string $driverName = 'rubika' // ✅ Clean, Defaulted, Promoted / Default to rubika for backward compatibility
    ) {}

    /**
     * Execute the job.
     * This is where the magic happens, orchestrated by the Laravel Queue Worker.
     *
     * @param Krubot $bot The singleton Krubot instance, automatically resolved and injected
     *                    by Laravel's Service Container. This instance is already
     *                    "live" and fully configured by KrubotServiceProvider,
     *                    with all Nexuses discovered and handlers registered.
     *                    WE DO NOT `new Krubot()` HERE. EVER.
     * @return void
     */
    public function handle(Krubot $bot): void
    {
        try {
            
            /*
            // =================================================================
            // STEP 1: PAYLOAD ADAPTATION & MESSAGE OBJECT FORGING
            // =================================================================
            // Find the actual message data within the potentially nested payload.
            // This provides resilience against slight variations in webhook formats.
            $updateRoot = $this->payload['update'] ?? $this->payload['new_message'] ?? $this->payload;

            // Defensive Check: A payload might be empty or malformed.
            // If there's no recognizable data, we log it and terminate gracefully.
            if (empty($updateRoot)) {
                Log::warning('HandleRubikaUpdate: Job terminated. Received a payload without a recognizable update structure.', [
                    'job_id' => $this->job?->getJobId(),
                    'payload' => $this->payload
                ]);
                return;
            }

            // Forge the raw array into a structured `Message` object that Krubot's Engine understands.
                $updateRoot = $this->payload->effectiveData;
                $messageObject = new Message($updateRoot);
            */

            // Payload is DTO (UniversalInboundUpdate), dispatched from Gatekeeper.
            // Builds normalized Message compatible with Krubot core pipeline.
            $messageObject = Message::fromInboundPayload($this->payload);

            // Optional: Log the creation for high-level monitoring.
            $messageId = $messageObject->message_id ?? 'N/A';
            // Log::info("Message Object [{$messageId}] forged for Krubot processing.", [
            Log::info("[{$this->driverName}] Message [{$messageId}] forged for Krubot processing.", [
                'job_id' => $this->job?->getJobId(),
                'chat_id' => $messageObject->chat_id ?? 'N/A'
            ]);

            // =================================================================
            // STEP 2: DELEGATION TO THE CORE PROCESSING ENGINE
            // =================================================================
            // This is the most critical step. We hand off the standardized Message
            // object to the bot's central nervous system: `processUpdate`.
            // The `$bot` instance already knows about all routes, middlewares, and conversations.
            // This single method call triggers the entire routing pipeline.

            // 🛑 IDENTITY CHECK
            // Resolve the specific driver instance for this job.
            // If $this->driverName is 'bale', we get the Bale driver.
            // $bot = $manager->driver($this->driverName);
            // @ToDo: Force-Set Driver Identity

            $bot->processUpdate($messageObject);

        } catch (Throwable $e) {
            // =================================================================
            // STEP 3: CATASTROPHIC FAILURE CONTAINMENT
            // =================================================================
            // If anything goes wrong during the process, from Message creation to the
            // depths of `processUpdate`, we catch it here to prevent the entire
            // queue worker from crashing. A failed job should never take down the system.
            // Log::critical('CRITICAL: Failed to process Rubika update due to an unhandled exception.', [
            Log::critical("CRITICAL: Failed to process [{$this->driverName}] update due to an unhandled exception.", [
                'job_id'          => $this->job?->getJobId(),
                'exception_class'   => get_class($e),
                'exception_message' => $e->getMessage(),
                'file'              => $e->getFile(),
                'line'              => $e->getLine(),
                'payload'           => $this->payload, // Log the exact payload that caused the failure for debugging.
                'trace_as_string'   => $e->getTraceAsString(), // Full stack trace for deep analysis.
            ]);

            // Depending on your queue strategy, you might want to explicitly fail the job
            // so Laravel can attempt to retry it based on your configuration.
            // $this->fail($e);

            // Optional: Release back to queue if it's a timeout issue?
            // $this->release(10);
        }
    }
}
