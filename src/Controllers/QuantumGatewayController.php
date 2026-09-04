<?php

namespace KrubiK\Controllers;
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

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

// Krubot Core Engine & Helpers
use KrubiK\Krubot;
use KrubiK\Enums\Platform;
use KrubiK\DTOs\Message;
use KrubiK\Helpers\AmethystMatrix as Log;
use KrubiK\Drivers\Nemesis as KrubotManager;    // 🧠 The Brain
use KrubiK\DTOs\UniversalInboundUpdate;         // ⚗️ The Alchemist
use KrubiK\Jobs\HandleDriverUpdate;             // 🚀 The Executor
use KrubiK\WebApps\UniversalIdentity;

use KrubiK\WebApps\DTOs\WebRequest; // ⚡ Our Sacred WebRequest HyperDTO, for WebApp calls.

/**
 * =========================================================================
 *  THE QUANTUM GATEWAY CONTROLLER v4.2.4 (Unified Core)
 * =========================================================================
 *
 * 👑 The Singular Point of Entry *
 * "The brain of the Legacy, the soul of the Modern, and the mindset of the Universal."
 *
 * This is the ultimate, unified entry point for ALL Krubot traffic, pure focused on efficiency.
 * This is the KrubiK Ultimate Gatekeeper. It stands as the single, unified entry point
 * for webhook updates from ANY supported platform. It is designed for maximum
 * performance, scalability, and resilience under extreme load.
 *
 * 🧬 Its DNA is composed of four core principles:
 * 1.  IDENTIFY: Delegate driver identification to the hyper-intelligent KrubotManager.
 * 2.  INTERCEPT: Flash-check raw IDs to reject duplicate Requests InstanTly.
 * 3.  STANDARDIZE: Forge raw, toxic payloads into safe, immutable DTOs.
 * 4.  DISPATCH: Hand off the standardized DTO to the Laravel Queue.
 * 
 * It intelligently routes requests to one of two fates:
 * 1. ASYNC WEBHOOK: Validated, standardized, and dispatched to the queue.
 * 2. SYNC WEBAPP: Authenticated, processed in real-time, and returns a direct response.
 *
 * This consolidation enhances developer experience (HyperDX) and centralizes control.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class QuantumGatewayController extends Controller
{

    /**
     * Cache the sync decision to avoid re-evaluating on every call.
     * @var bool|null
     */
    private ?bool $shouldDispatchSync = null;

    /**
     * Base filename for the Web Render assets (JS/CSS).
    */
    public static $webRenderFileName = 'Krubot-Web-Render';

    /**
     * The constructor now depends on the KrubotManager, our Single Source of Truth for the request's state.
    */
    public function __construct(
        protected KrubotManager $manager,
        protected Krubot $engine
    ) {}


    // A new method in as a dedicated utility
    public function extractQuickId(string $driver, array $payload): ?string
    {
        // This logic can be more complex, residing within each driver's domain
        return match($driver) {
            'rubika'   => $payload['message_update']['message_id'] ?? null,
            'telegram' => (string)($payload['update_id'] ?? null),
            'bale'     => (string)($payload['update_id'] ?? null),
            default    => null,
        };
    }

    // =====================================================================
    //  FATE 1: ASYNCHRONOUS¦OR¦SYNCHRONOUS WEBHOOK HANDLING
    // =====================================================================
    /**
     * The one and only entry point for all incoming webhook traffic.
     * The Hyper-Optimized Gatekeeper.
     *
     * @param Request       $request The incoming HTTP request.
     * @param string|mull   $driver The resolved driver.
     * @return JsonResponse A swift, immediate response to the calling platform.
    */
    public function handleWebhook(Request $request, ?string $driver): JsonResponse
    {

        // -----------------------------------------------------------------
        // PHASE 1: 🧠 IDENTITY RESOLUTION PHASE (The Brain)
        // We ask the master strategist, "Who is at the gate?"
        // The Manager uses its 4-layered logic (SAPI -> Route -> Payload -> Config).
        // -----------------------------------------------------------------

        // Now It just asks from Nemesis.
        $driver = (string) ($this->manager->platform() ?? Platform::default());
        // Log::info("SuperWebhook identified signal.", ['platform_name' => $driver]); // Optional Debug

        // Get raw payload once
        $payload = $request->all();

        // Guard Clause: Acknowledge and ignore empty requests immediately.
        if (empty($payload)) {
            // Log with context
            Log::warning("QuantumGateway received empty payload.", ['driver' => $driver]);
            return response()->json(['status' => 'ignored_empty'], 200);
        }

        // -----------------------------------------------------------------
        // PHASE 2: ⚡ FLASH IDEMPOTENCY CHECK (The Vanguard Optimization)
        //
        // "Stop right there, criminal scum!"
        // Why build the DTO again if we've seen this ID 1ms ago?
        // If identified here, we exit BEFORE the heavy Forge process.
        //
        // Note: Cast to string ensures consistency across drivers.
        // -----------------------------------------------------------------
        $rawMsgId = $this->extractQuickId($driver, $payload);
        if ($rawMsgId && $this->isDuplicate((string)$rawMsgId, $driver)) {
            return response()->json(['ok' => true, 'status' => 'duplicate_fast_fail']);
        }

        // -----------------------------------------------------------------
        // PHASE 3: ⚗️ PAYLOAD STANDARDIZATION (The Alchemist)
        // We take the raw input and pass it to our Alchemist (the DTO)
        // to be transmuted into a standard, safe, and immutable data structure.
        // -----------------------------------------------------------------
        try {
            $dto = UniversalInboundUpdate::forge($payload);
        } catch (\Throwable $e) {
            Log::error("QuantumGateway Forge Error [{$driver}]: " . $e->getMessage());
            // Return 200 to prevent platform retries on malformed data
            return response()->json(['status' => 'error_structure'], 200);
        }

        // -----------------------------------------------------------------
        // PHASE 4: 🛡️ DEEP STRUCTURAL VALIDATION & FALLBACK IDEMPOTENCY
        // We use the DTO's own intelligence to validate itself.
        // -----------------------------------------------------------------
        if (!$dto->isValid()) {
            Log::warning("QuantumGateway ignored invalid structure.", ['driver' => $driver, 'payload' => $payload]);
            return response()->json(['status' => 'ignored_invalid'], 200);
        }

        // Fallback Idempotency: If Phase 2 missed the ID (e.g. obscured structure),
        // check again using the DTO's signature, BUT only if we didn't check already.
        if (!$rawMsgId && $this->isDuplicate($dto->signature(), $driver)) {
             return response()->json(['ok' => true, 'status' => 'duplicate_dto_check']);
        }

        Log::debug("QuantumGatewayWebhook found structure:: " . $driverIdentity, [$payload, $dto]);

        // -----------------------------------------------------------------
        // PHASE 5: 🚀 DISPATCH TO THE ABYSS (Queue) OR THE PRESENT (Sync)
        // We now use our configuration-aware dispatcher.
        // -----------------------------------------------------------------
        $this->dispatchConditionally(
            new HandleDriverUpdate($dto, $driver),
            $request
        );

        return response()->json([
            'status' => $this->shouldDispatchSync($request) ? 'processed_sync' : 'queued',
        ], 200);
    }

    // =====================================================================
    //  FATE 2: SYNCHRONOUS WEBAPP HANDLING
    // =====================================================================
    /**
     * Processes synchronous WebApp requests by transforming the incoming HTTP request
     * into a structured Krubot Message and executing the stateless engine.
     *
     * Archetype: The Conduit (Interface Adapter)
     *
     * @param Request $request The incoming HTTP request carrying the payload.
     * @param string $path The resolved routing path.
     * @return JsonResponse|Response
    */
    public function handleWebApp(Request $request, string $path): JsonResponse|Response
    {
        try {
            // STEP 1: RESOLVE THE AUTHENTICATED IDENTITY (The Soul Stone Extraction)
            // We extract the authenticated UniversalIdentity directly from the request,.
            //  using our macro. This is a pure $O(1)$ lookup from Symfony's ParameterBag.
            /** @var UniversalIdentity $identity */
            $identity = $request->identityCard();
    
            // Safety Gate: Ensure the middleware did its job and we have a valid Identity Card.
            // and we are not operating on a null identity.
            if (!$identity instanceof UniversalIdentity) {
                throw new \RuntimeException('AxiomCore failed to bind or resolve a valid UniversalIdentity for this request lifecycle.');
            }
    
            // STEP 2: EXTRACT CLEAN SUB-PATH & TRANSFORMS SLASHES TO DOTS
            // Resolves the sub-path from the route wildcard, e.g., "game/dashboard/vip-store"
            $targetPath = $path ?? $request->route('path') ?? '';
            $normalizedPath = str_replace('/', '.', trim($targetPath, '/'));
    
            // STEP 3: TRANSLATE HTTP REQUEST TO RICHER CONTEXT (The WebRequest DTO)
            // We instantiate our newly refactored WebRequest DTO. It automatically
            // parses and contains the validated WebAppInitData inside itself.
            /// $webRequest = WebRequest::from($request);

            // STEP 3: TRANSLATE HTTP REQUEST (Pass Pre-Validated Genesis to avoid Redundant HMAC checks)
            // By passing $identity->getData(), we instruct WebRequest to BYPASS parsing
            // and re-validating the initData string. This saves computational power (CPU cycles).
            // This is the physical manifestation of "Zero-Redundant-Validation" philosophy.
            $baseWebRequest = WebRequest::from($request, $identity->getData());

            // Since WebRequest is immutable (final readonly), we use the fluid .with() copy strategy
            // to perfectly inject our normalized, clean target path.
            $webRequest = $baseWebRequest->with([
                'path' => $normalizedPath
            ]);
    
            // STEP 4: CONSTRUCT THE KRUBOT MESSAGE
            // We wrap the WebRequest inside the engine's message model.
            $message = new Message([]);
            $message->web_request = $webRequest;
    
            // We strictly map properties from the Immutable, Certified Identity Card `$identity`.
            // Any attempt of Session Hijacking or Payload Impersonation is mathematically impossible here.

            // CRITICAL BINDING: Inject Sender ID and Platform from the Identity Card.
            $message->sender_id = $identity->id();
            
            // We dynamically fetch the platform. If the identity doesn't expose a clean platform string,
            // we fall back to the DTO's detected platform or 'web'.
            $message->platform = $identity->platform ?? $webRequest->initData?->platform?->value ?? 'web';
    
            // STEP 5: ENGINE INVOCATION (The Execution Stage)
            // We pass the fully hydrated message into the core Krubot Engine.
            $this->engine->processUpdate($message);
            // The engine processes the update. It remains logic-pure and unaware of the underlying HTTP delivery layer. (...maybe ;)-)
    
            // If the engine generated a direct response payload (e.g., webhook replies, custom views),
            // we immediately transmit it back to the client.
            if ($this->engine->hasResponse()) {
                return $this->engine->response();
            }

            // Standard API response if no specific output was rendered by the engine or matching-route returns void.
            return response()->json([
                'status' => 'success',
                'message' => 'Action executed successfully.',
                'context' => [
                    'platform' => $message->platform,
                    'identity_source' => $identity->source
                ]
            ], 200, [
                'X-Krubik-Gateway' => 'Axiom-Synchronous',
                'X-Identity-Resolved' => 'True'
            ]);
    
        } catch (\Throwable $e) {
            // We report the error to the global handler (Sentry, Log, etc.)
            // while maintaining the user experience with a clean, localized error response.
            report($e);
            /// dd($e);
            
            return response()->json([
                'status' => 'error',
                'message' => 'An internal gateway error occurred while processing your WebApp request.'
            ], 500);
        }
    }

    /**
     * Serves the embeddable widget snippet.
     * Drop <script src="/krubot.js"></script> anywhere on your site.
    */
    public function embedWebRendererz(): Response
    {
        $base = request()->root(); // rtrim((string) config('app.url'), '/');

        $jsPath  = public_path('engine/krubot/'.self::$webRenderFileName.'.js');
        $cssPath = public_path('engine/krubot/'.self::$webRenderFileName.'.css');

        $jsUrl = $base
            . '/engine/krubot/'.self::$webRenderFileName.'.js?v='
            . filemtime($jsPath);

        $cssUrl = $base
            . '/engine/krubot/'.self::$webRenderFileName.'.css?v='
            . filemtime($cssPath);

        $jsUrl  = json_encode($jsUrl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $cssUrl = json_encode($cssUrl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return response(
            <<<JS
(function () {
    var css = document.createElement('link');
    css.rel = 'stylesheet';
    css.type = 'text/css';
    css.media = 'all';
    css.href = {$cssUrl};
    document.head.appendChild(css);

    var script = document.createElement('script');
    script.src = {$jsUrl};
    script.defer = true;
    document.head.appendChild(script);
})();
JS,
            200,
            [
                'Content-Type' => 'application/javascript; charset=UTF-8',

                // Absolute cache prevention for Manifestor.
                'Cache-Control' => 'no-store, no-cache, max-age=0, s-maxage=0, must-revalidate, proxy-revalidate',
                'Surrogate-Control'   => 'no-store',
                'Pragma'        => 'no-cache',
                'Expires'       => '0',
            ]
        );
    }

    /**
     * =========================================================================
     *  THE REBEL MAGICIAN'S UTILITY BELT
     * =========================================================================
    */

    /**
     * 🧙‍♂️ The Time-Turner: Conditionally dispatches a job.
     * It decides whether to dispatch to the queue (async) or execute
     * immediately (sync) based on configuration and request headers.
     * This provides immense flexibility for debugging and special cases.
     *
     * @param object $job The job instance to be dispatched.
     * @param Request $request The current HTTP request.
    */
    private function dispatchConditionally(object $job, Request $request): void
    {
        // The Gatekeeper's job is done. The pure DTO and its identity are handed off to ::

        if ($this->shouldDispatchSync($request)) {
            // A ## sync_process()
            // It's The Magician's spell for immediacy. Blocks execution until done.
            dispatch_sync($job);
        } else {
            // B ## Or Asynchronous world of Laravel Queues.
            // The standard Architect's blueprint: send it to the queue network.
            dispatch($job)->onConnection(config('krubot.queue.connection'))
                          ->onQueue(config('krubot.queue.name'));
        }
    }

    /**
     * 🧠 The Decision Core: Determines if jobs for this request should be sync.
     * The logic is memoized (cached) for the duration of the request.
     * The hierarchy of decision is:
     * 1. A specific HTTP Header (`X-Krubot-Sync-Dispatch: true`) - for on-the-fly debugging.
     * 2. The global config setting (`krubot.queue.force_sync_dispatch`).
     *
     * @param Request $request
     * @return bool
    */
    private function shouldDispatchSync(Request $request): bool
    {
        // Memoization: calculate the decision only once per request.
        if ($this->shouldDispatchSync !== null) {
            return $this->shouldDispatchSync;
        }

        // Priority 1: Check for the magic debug header.
        // A Rebel's backdoor for forcing sync behaviour.
        if (filter_var($request->header(config('krubot.queue.sync_response_header', 'X-Krubot-Sync-Dispatch')), FILTER_VALIDATE_BOOLEAN)) {
            return $this->shouldDispatchSync = true;
        }

        // Priority 2: Fallback to the environment configuration.
        // The Architect's setting for the entire environment.
        return $this->shouldDispatchSync = (bool) config('krubot.queue.sync_response_enable', false);
    }

    /**
     * High-performance duplicate check using Laravel Cache.
     * Checks if a given unique signature for a specific driver has been processed recently.
     * The cache key is namespaced by the driver to prevent cross-platform collisions.
     * 
     * @param string $signature Unique identifier (from Raw or DTO).
     * @param string $driver    The resolved driver identity.
     * @return bool True if it's a duplicate.
    */
    private function isDuplicate(string $signature, string $driver): bool
    {
        // Namespaced by driver to prevent cross-platform collisions.
        $cacheKey = "krubot:msg_sig:{$driver}:{$signature}";
        
        // Use cache()->add() for an atomic, race-condition-free check and set.
        // It returns false if the key already exists.
        // The lock duration is now configurable for better DX.
        return !cache()->add($cacheKey, true, now()->addMinutes(config('krubot.cache.deduplication_ttl', 2)));
    }

    /*
    private function unrecognizedRequest(): JsonResponse
    {
        // Nemesis couldn't identify the request platform.
        return response()->json([
            'status' => 'error',
            'message' => 'Unrecognized request type. The Quantum Gateway is sealed.'
        ], 400);
    }
    */
}
