<?php

namespace KrubiK\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use KrubiK\Krubot;
use KrubiK\Drivers\Nemesis as KrubotManager;
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
     * The Immutable Payload DTO.
     * PHP 8.2 will serialize this object perfectly for the queue.
     * 
     * @param UniversalInboundUpdate $dto          The immutable, pre-validated update DTO to process.
     * @param string                 $operativeName      The bot universe ('main', 'support', ...).
     * @param string                 $driverName   The RESOLVED driver instance ('telegram_main', ...).
     * @param string                 $platformName The canonical platform ('telegram', ...).
     */
    public function __construct(
        public UniversalInboundUpdate $payload,

        // These are Crucial for routing the response back to the correct platform.
        public readonly string $operativeName,
        public readonly string $driverName,
        public readonly string $platformName,
    ) {}

    /**
     * Prevents two workers from racing on the same logical update.
     *
     * The lock key is scoped by bot + platform + DTO signature, so
     * Telegram-Main's update_id=500 and Telegram-Support's update_id=500
     * are treated as DISTINCT (as they should be).
     *
     * The lock is automatically released when the job finishes or fails
     * past its retry budget.
    */
    public function uniqueId(): string
    {
        return "{$this->operativeName}:{$this->platformName}:{$this->dto->signature()}";
    }

    /**
     * Execute the job.
     * The worker-side execution.
     * This is where the magic happens, orchestrated by the Laravel Queue Worker.
     *
     * We deliberately avoid constructor/method injection for Krubot so we
     * can control the exact ORDER of operations:
     *
     *     1. Bind Nemesis to the correct bot context.
     *     2. Forget any stale Krubot from a previous job.
     *     3. Resolve Krubot fresh — its closure resolves the driver
     *        in the correct bot context.
     *     4. Forge the Message DTO and hand off to processUpdate().
     *     5. Always clear the bot context in `finally`.
     */
    public function handle(): void
    {
        /** @var KrubotManager $nemesis */
        $nemesis = app('krubot.manager');

        try {

            // ── STEP 0: Clear nemesis Cached Regiments & Enforcers
            $nemesis->clearOperative();

            // ── STEP 1: Bind bot context BEFORE any driver resolution. ──
            $nemesis->operative($this->operativeName);

            // ── STEP 2: Resolve Krubot fresh. ──
            // The singleton Krubot instance, automatically resolved and injected
            // by Laravel's Service Container. This instance is already
            // "live" and fully configured by KrubotServiceProvider,
            // with all Nexuses discovered and handlers registered.
            // WE DO NOT `new Krubot()` HERE. EVER.
            /** @var Krubot $engine */
            $engine = krubot();

            // 🛑 IDENTITY CHECK
            // Resolve the specific driver instance for this job.
            // Because Nemesis caches by "operative::driver", this is O(1)
            // after the first resolution.
            $driver = $nemesis->driver($this->driverName, $this->botName);

            $engine->setCurrentDriver($driver);
            // Inform the Engine of the active driver (platform-agnostic side effect).

            // The service provider closure now runs in the correct bot
            // context, so `$nemesis->driver()` inside it resolves the
            // proper driver instance (e.g. telegram_support).
            $nemesis->enforcer()->serve($engine);

            // ── STEP 3: Forge the normalized Message DTO. ──
            // Payload is DTO (UniversalInboundUpdate), dispatched from Gatekeeper.
            // Builds normalized Message compatible with Krubot core pipeline.
            $messageObject = Message::fromInboundPayload($this->dto);

            // Optional: Log the creation for high-level monitoring.
            /*            
            $messageId = $messageObject->message_id ?? 'N/A';
            Log::info(
                "[{$this->operativeName}/{$this->platformName}/{$this->driverName}] "
                . "Message [{$messageId}] forged for Krubot processing.",
                [
                    'job_id'  => $this->job?->getJobId(),
                    'chat_id' => $messageObject->chat_id ?? 'N/A',
                ]
            );
            */

            // =================================================================
            // ── STEP 4: DELEGATION TO THE CORE PROCESSING ENGINE ──
            // =================================================================
            // This is the most critical step. We hand off the standardized Message
            // object to the bot's central nervous system: `processUpdate`.
            // The `$engine` instance already knows about all routes, Nexus handlers, middlewares, and conversations.
            // This single method call triggers the entire routing pipeline.
            $engine->processUpdate($messageObject);

        } catch (Throwable $e) {
            // =================================================================
            // STEP 3: CATASTROPHIC FAILURE CONTAINMENT
            // =================================================================
            // If anything goes wrong during the process, from Message creation to the
            // depths of `processUpdate`, we catch it here to prevent the entire
            // queue worker from crashing. A failed job should never take down the system.
            //
            // Instead We log with full context (bot + platform + driver) so that
            // cross-bot failures can be traced without ambiguity.
            Log::critical(
                "CRITICAL: Failed to process [{$this->operativeName}/{$this->platformName}/{$this->driverName}] update, due to an unhandled exception.",
                [
                    'job_id'            => $this->job?->getJobId(),
                    'bot'               => $this->operativeName,
                    'platform'          => $this->platformName,
                    'driver'            => $this->driverName,
                    'exception_class'   => $e::class,
                    'exception_message' => $e->getMessage(),
                    'file'              => $e->getFile(),
                    'line'              => $e->getLine(),
                    // Serialize the DTO (not the raw payload — we don't store it).
                    'dto'               => $this->dto,
                    'trace_as_string'   => $e->getTraceAsString(),
                ]
            );

            // Re-throw so Laravel's retry/backoff machinery can decide
            // whether to retry based on $tries and backoff().
            throw $e;

            // Depending on your queue strategy, you might want to explicitly fail the job
            // so Laravel can attempt to retry it based on your configuration.
            // $this->fail($e);

            // Optional: Release back to queue if it's a timeout issue?
            // $this->release(10);

        } finally {
            // ⚠️ ALWAYS clear the bot context.
            // Nemesis is a singleton; a stale context would corrupt the
            // NEXT job processed by this same worker.
            $nemesis->clearOperative();
        }
    }
}
