<?php

namespace KrubiK\Routing\Arcane;
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

use KrubiK\Routing\Route; // Import the Route Class ⚡
use KrubiK\DTOs\Message;
use KrubiK\Keyboard\Keyboard;
use KrubiK\Helpers\AmethystMatrix; // ⚡ Import the Sorceress

use KrubiK\Attributes\{
    ForceJoin, When
};

use Throwable;
use ReflectionMethod;

/**
* ══════════════════════════════════════════════════════════════════════════
*  ⚡ STORM JUSTICE TURBINE ⚡ — The Cyber-Cortex of Absolute Arbitration
* ══════════════════════════════════════════════════════════════════════════
*
 * Deep within the KrubiK Cyber-Citadel, where photons of raw requests collide with
 * the force-fields of The Route, this Turbine spins. It is neither bolted
 * on for glory nor whispered of in changelogs — it is the UNSEEN SENTINEL
 * that stands between chaos and order.
*
 * Forged in the digital ether, this Trait is the very soul of Krubot's hyper-logic decision
 * engine {_EpicEngine_}, when collide with #[When] & #[ForceJoin] sentinels.
 * It is the Sovereign Governor where access protocols, conditional guards, and judicial overrides converge.
 *
 * It doesn't just process routes; it passes sentence. It's the gatekeeper
 * standing between a user's request and the controller's sanctum.
 * Within this code, the fate of every incoming signal is decided.
 * 
 *  ─────────────────────────────────────────────────────────────────────────
 *  DESIGN COVENANTS (UNBREAKABLE):
 *    • Judge verdicts are BINDING. Only an explicit PARDON opens the way.
 *    • Unguarded routes pay ZERO tribute — no reflection, no cost, no attention needed.
 *  ─────────────────────────────────────────────────────────────────────────
 *
 *  >> ONE TURBINE. THREE TRIALS. <<
 *
 *   [1] :: THE SUPREME JUDGE TRIBUNAL   — `tryInvokeJudge()`
 *         Summons a custom "Judge" method from the Controller's blueprint,
 *         arms it with DI-powered evidence, and binds its verdict as LAW.
 *
 *   [2] :: THE GUARDIAN WALL            — `evaluateRouteGuards()`
 *         A zero-cost phalanx of #[When] sentinels. Micro-cached, type-safe,
 *         ruthless. If the state does not align with prophecy, the gate SLAMS.
 *
 *   [3] :: THE FORCEJOIN GATEKEEPER     — `handleForceJoinGuard()`
 *         An Alchemist's check of loyalty. Amethyst-cached, early-exit armed,
 *         and when denied... it does not merely reject — it forges an
 *         interactive, button-lit CALL TO ARMS.
 *
*/
trait StormJusticeTurbine // HyperionGates | NexusGovernor
{

    /**
     * ═════════════════════════════════════════════════════════════════════
     *  ⚖️ THE THREE VERDICTS :: Sacred Constants of the Tribunal ⚖️
     *  ─────────────────────────────────────────────────────────────────────
     *  Forged in the first compile of any Nexus, these three sigils are
     *  the ONLY truths the Turbine accepts from the Judge's bench. No grey
     *  zones. No coin-tosses. Every judgment terminates in exactly one of
     *  these states — and the routing loop OBEYS.
     *
     *      ◈ PARDON   → The gates FLY open. Next guardian, step aside.
     *      ◈ HALT     → The storm STOPS here. Verdict is final. Shield up.
     *      ◈ NOT_FOUND→ No Judge was summoned — fall back to STANDARD sentencing.
     *
     *  Handle with reverence. These are not integers; they are JURISDICTION. 🜂
     * ═════════════════════════════════════════════════════════════════════
    */

     /**
     * ◈ VERDICT CODE[1]:: PARDON — The Judge has spoken: the way is CLEAR. Speed forth. ⚡ 
     * The Judge grants clemency. The gate is bypassed. The request is sanctified.
     * The original sin of a failed guard is forgiven. Proceed.
    */
    protected const JUDGE_RESULT_PARDON = 1;

    /**
     * ◈ VERDICT CODE[2]:: HALT — The Judge has spoken: the storm FREEZES here. No passage. 🛑
     * The sentence is passed. The barrier holds. Access is terminated.
     * The Judge's gavel falls, and the request is cast into the void
     * Note! blocks other responses, eg when you've already provided an
     * error message to user return JUDGE_RESULT_HALT, so Krubot won't send other error messages to them.
    */
    protected const JUDGE_RESULT_HALT = 2;

    /**
     * ◈ VERDICT CODE[3]:: NOT_FOUND — No Judge on the bench? Default sentencing protocol engages. 🕳️
     * The call echoes into an empty chamber. The summoned Judge does not exist.
     * The signal is a ghost, a whisper in the machine. Default protocols are engaged.
    */
    protected const JUDGE_NOT_FOUND = 3;

    /**
     * The Supreme Judge Tribunal.
     * Centralizes the logic for finding, summoning, and interpreting a custom "Judge" method.
     *
     * @param Route $route The context route.
     * @param null|string $judgeDirective The message string, potentially starting with '.' to signify a Judge.
     * @param array $customPayload The specific evidence payload for this case (e.g., channels or guard info).
     * @return int Returns JUDGE_RESULT_PARDON on pardon, JUDGE_RESULT_HALT on halt, JUDGE_NOT_FOUND if no Judge was invoked.
    */
    protected function tryInvokeJudge(Route $route, ?string $judgeDirective, array $customPayload = []): int
    {
        // If there's no directive or it doesn't start with the Judge sigil, court is not in session.
        if ($judgeDirective === null || !str_starts_with($judgeDirective, '.')) {  // Changed to DOT Notation ;)
            return self::JUDGE_NOT_FOUND; // Case dismissed, proceed to default sentencing.
        }

        // --- THE JUDGE'S CHAMBERS (NEW PARADIGM) ---
        $methodName = substr($judgeDirective, 1);
        $action = $route->getAction();

        // X-1-X :: EXTRACT THE CONTROLLER'S BLUEPRINT from the Route's action
        $controllerClass = null;
        if (is_array($action) && is_string($action[0])) {
            $controllerClass = $action[0];
        } elseif (is_string($action) && str_contains($action, '@')) {
            $controllerClass = explode('@', $action, 2)[0];
        }

        // We can only summon a Judge if it resides within a class-based controller.
        // A Closure route has no class context for the Judge to exist in.
        if (!$controllerClass) {
            return self::JUDGE_NOT_FOUND;
        }

        // X-2-X :: SUMMON THE CONTROLLER INSTANCE for this judgment.
        // We follow the sacred rule: use Laravel's container if available, otherwise new.
        $controllerInstance = function_exists('app') ? app($controllerClass) : new $controllerClass();

        // X-3-X :: VERIFY THE JUDGE'S EXISTENCE on the summoned controller.
        // Does the designated Judge (custom method) exist in the current Nexus?
        if (method_exists($controllerInstance, $methodName)) {

            // --- THE JUDGEMENT ---
            // We summon the Judge, pass it the evidence (required channels),
            // and capture its final, binding verdict.
            /// $verdict = $controllerInstance->{$methodName}($route->forceJoinChannels);

            // X-4-X :: Forge the Reflection of the Judge's method.
            // This is the key to unlocking the auto-wiring engine.
            $reflection = new ReflectionMethod($controllerInstance, $methodName);

            // The base payload, always available to any Judge.
            // We pass the standard context, making the handler a first-class citizen.
            $basePayload = [
                'bot'     => $this,
                'message' => $this->thisMessage(),
                'msg'     => $this->thisMessage(),
            ];

            // X-5-X :: Prepare the payload for the KRUBOT-DI engine.
            // We provide not just the custom data, but also the context of the current request.
            // THE CRITICAL EVIDENCE injected via $customPayload:
            // Any developer's custom handler can now type-hint forExample:: `array $requiredChannels`.
            // Merge the specific evidence with the standard context.
            $finalPayload = array_merge($customPayload, $basePayload);
            
            // X-6-X :: Invoke the Judge using the framework's own sacred engine.
            // This is no longer a simple call; it's a DI-powered invocation.
            $verdict = $this->invokeWithAutoWiring(
                method: $reflection,
                targetInstance: $controllerInstance, // Correctly using the summoned instance.
                payloadData: $finalPayload,
                extraInjects: [$this, $this->thisMessage()]
            );

            // Return the final verdict: true for pardon, false for halt.
            return ($verdict === true) ? self::JUDGE_RESULT_PARDON : self::JUDGE_RESULT_HALT;
        }

        // The specified Judge was not found on the controller.
        return self::JUDGE_NOT_FOUND;
    }

    /**
     * Evaluates the #[When] guards for a given route candidate.
     * This is the new gatekeeper, called INSIDE the main routing loop.
     * It enables "continue-on-fail" logic.
     *
     * @param Route $route The route object to check.
     * @param Message $message The current message context.
     * @return bool Returns true if all guards pass, false otherwise.
    */
    protected function evaluateRouteGuards(Route $route, Message $message): bool
    {
        // === HYPER-PERFORMANCE PATH (NO REFLECTION) ===
        $whenGuards = $route->getGuards();

        // If there are no guards, the way is clear.
        // If there are no #[When] attributes, this entire logic block is skipped instantly.
        // Zero performance cost for unguarded methods.
        if (empty($whenGuards)) {
            return true;
        }
        
        // --- The Guardian Logic ---
        // Optimization: Fetch the UserStorage driver instance only once.
        $userStorage = $this->userStorage();

        // Micro-cache: If a method has multiple attributes for the same state key,
        // (e.g., #[When('>level', 10)] and #[When('<level', 50)]),
        // we hit the storage only ONCE for that key per request.
        $stateKeyCache = []; // Cache is now localized to this check

        foreach ($whenGuards as $when) {
            /** @var \KrubiK\Attributes\When $when */
            // newInstance() is fast now, thanks to our optimized, non-reflection constructor.
            $conditionMet = false;
            $key = $when->stateKey;
            
            // Use the per-request cache to avoid redundant storage hits (Cache-First).
            if (!array_key_exists($key, $stateKeyCache)) {
                $exists = $userStorage->has($key);
                $stateKeyCache[$key] = [
                    'exists' => $exists,
                    'value'  => $exists ? $userStorage->get($key) : null,
                ];
            }
            
            $stateExists = $stateKeyCache[$key]['exists'];
            $actualValue = $stateKeyCache[$key]['value'];

            // This logic is designed based on our strict, predictable `When` attribute rules.
            if (!$when->hasExpectedValue) {
                // Case: #[When('state')] -> Pure existence check.
                // The state must exist AND must not have been flushed to null.
                $conditionMet = ($stateExists && $actualValue !== null);
            } else {

                // Case: Attribute has an expectedValue, e.g., #[When('state', 123)] or #[When('>level', 10)]
                $expectedValue = $when->expectedValue;

                switch ($when->operator) {
                    case '=':
                        // Passes if the actual value is strictly equal to the expected one.
                        // This correctly handles #[When('state', null, 'msg')] because
                        // a non-existent state's actualValue is null, so null === null passes.
                        $conditionMet = ($actualValue === $expectedValue);
                        break;
    
                    case '!':
                        // Passes if the actual value is NOT equal.
                        // If the state doesn't exist, its value is null, which is not equal
                        // to any non-null expected value, so the condition correctly passes.
                        $conditionMet = ($actualValue !== $expectedValue);
                        break;
    
                    case '>':
                        // Type-safe numeric comparison. Prevents errors and weird PHP type juggling.
                        $conditionMet = $stateExists && is_numeric($actualValue) && is_numeric($expectedValue) && ($actualValue > $expectedValue);
                        break;
    
                    case '<':
                        // Type-safe numeric comparison.
                        $conditionMet = $stateExists && is_numeric($actualValue) && is_numeric($expectedValue) && ($actualValue < $expectedValue);
                        break;
    
                    case '~': // IN array
                        // Type-safe "in_array" check. Fails safely if developer provides a non-array.
                        $conditionMet = $stateExists && is_array($expectedValue) && in_array($actualValue, $expectedValue, true);
                        break;
    
                    case '×': // NOT IN array
                        // Type-safe "not in_array" check.
                        $conditionMet = $stateExists && is_array($expectedValue) && !in_array($actualValue, $expectedValue, true);
                        break;
                }

            }

            if (!$conditionMet) {

                // A condition was not met. We must stop and potentially send a message.
                $failMessage = $when->failMessage;

                // --- THE JUDGE'S SUMMONS (NOW CENTRALIZED) ---

                if ($failMessage !== null) {

                    $payload = [
                        'guard'       => $when,
                        'stateKey'    => $when->stateKey,
                        'actualValue' => $actualValue,
                    ];
                    $verdict = $this->tryInvokeJudge($route, $failMessage, $payload);

                    if ($verdict === self::JUDGE_RESULT_PARDON)
                        // CLEMENCY! The Judge overrode the failure. Check the next guard.
                        continue;

                    // Guard failed, Request NOT Pardoned by the Judge.
                    // Check if we need to send a message.
                    if ($verdict === self::JUDGE_NOT_FOUND) {

                        // --- THE STANDARD RESPONSE ---
                        // This block executes if no Judge was summoned, or the Judge method didn't exist.
                        // Use the centralized helper to resolve the message. No more F** DRY!
                        $messageText = trim($this->resolveAndTranslateMessage(
                            $failMessage, 
                            "Access denied." // A fallback default, though it will rarely be used.
                        ));
                        
                        if (!empty($messageText)) {
                            $this->reply($messageText)->send();
                        }
                    }
                }

                // Signal failure to the routing loop.
                return false;
            }
        }

        // All guardians have reported success. The way is clear.
        return true;
    }

    /**
     * The ForceJoin Gatekeeper - ALCHEMIST EDITION.
     * This final form transforms the denial message into a fully interactive, user-friendly guide.
     * It uses the PowerButton architecture to create clickable, full-width inline buttons for each
     * required channel, turning a restriction into an elegant call-to-action.
     *
     * @param Route $route The modern Route object being checked.
     * @return bool Returns true if access is granted, false otherwise.
    */
    protected function handleForceJoinGuard(Route $route): bool
    {
        // O(1) Performance Check.
        if (empty($route->forceJoinChannels)) {
            return true;
        }

        $userId = $this->senderId();
        if (!$userId) return false;

        $allowedStatuses = ['creator', 'administrator', 'member'];
        $accessGranted = true; // Assume loyalty until proven otherwise.

        // --- PHASE 1: THE FAST GUARD (Performance) ---
        // We check loyalty with ruthless efficiency. The moment one failure is found, we stop.
        foreach ($route->forceJoinChannels as $channelId) {
            $cacheKey = "forcejoin:{$userId}:in:{$channelId}";

            // Check Amethyst memory first (The Just Caching).
            if (AmethystMatrix::recall($cacheKey) === true) {
                continue; // Loyalty confirmed from cache. Check next channel.
            }

            // Not in cache, we must verify with the source.
            try {
                $status = $this->core()->getChatMember($channelId, $userId)['result']['status'] ?? 'left';
                if (in_array($status, $allowedStatuses, true)) {
                    // Loyalty confirmed. Remember this success for 5 Minutes.
                    AmethystMatrix::vault($cacheKey, true, 300);
                } else {
                    // FAILURE DETECTED!
                    $accessGranted = false;
                    break; // <-- THE DIVINE COMMAND! Halt all further checks.
                }
            } catch (Throwable $e) {
                AmethystMatrix::error('ForceJoin Guard API error.', ['user_id' => $userId, 'channel_id' => $channelId, 'error' => $e->getMessage()]);
                $accessGranted = false;
                break; // <-- On error, makes failure and HALT.
            }
        }

        // --- PHASE 2: THE ALCHEMIST'S RESPONSE (Interactive Honesty) ---
        // If access is still granted after all channels loop, it means the user is a member of all.
        if ($accessGranted) {
            return true;
        }

        // --- THE JUDGE'S CHAMBERS (NOW CENTRALIZED) ---
        $payload = [
            'requiredChannels' => $route->forceJoinChannels,
            'channels'         => $route->forceJoinChannels,
        ];
        $verdict = $this->tryInvokeJudge($route, $route->forceJoinMessage, $payload);

        // VERDICT ANALYSIS: Does the Judge grant clemency?
        // A strict check for JUDGE_RESULT_PARDON is paramount. Only an explicit JUDGE_RESULT_PARDON
        // constitutes an override to proceed.
        if ($verdict === self::JUDGE_RESULT_PARDON) {
            // Clemency granted. The guard stands down. The request shall pass.
            return true;
        }
        
        // If the verdict was JUDGE_RESULT_HALT,
        // the judgement is to HALT. The guard's original duty is upheld.
        if($verdict === self::JUDGE_RESULT_HALT)
            return false;

        // if $verdict === JUDGE_NOT_FOUND, so It should Render It's Deafult Messages Now ;)

        // If we are here, it means access was denied and Judge Not accept his Defense/Submissions.
        // Now, we provide the FULL and HONEST guide.
        // We will use the ORIGINAL `$route->forceJoinChannels` list to build the message,
        // ensuring the user gets the complete picture in one go.

        // --- STANDARD RESPONSE (If no "Judge" was summoned or the request lacks a special recommendation) ---

        // 1. Forge the PowerButtons using our new Platform-Aware Factory.
        // This single line replaces the entire complex array_map block.
        $buttons = $this->createPlatformAwareJoinButtons($route->forceJoinChannels);

        // If no valid buttons could be created for this platform, don't send an empty keyboard.
        if (empty($buttons)) {

            // Fallback message to a simple text for platforms with no joinable buttons
            $fallbackMessage = $this->resolveAndTranslateMessage(
                $route->forceJoinMessage, // Still respect the custom message
                '::krubot.errors.force_join_text_only|برای ادامه، عضویت در کانال‌های مورد نیاز الزامی است.'
            );

            $this->reply($fallbackMessage)->send();
            return false;
        }

        // Resolve the master denial text using our new, powerful resolver.
        $denialMessageText = $this->resolveAndTranslateMessage(
            $route->forceJoinMessage,
            // The default value is now a translation key itself, following the same pattern.
            '::krubot.messages.force_join_denial|برای ادامه، عضویت در تمام کانال‌های زیر الزامی است. پس از عضویت، دوباره تلاش کنید:'
            // Try to get the master text from config/lang files.
        );

        // 3. Send the final, powerful message with the interactive keyboard.
        $this->reply($denialMessageText)
            ->keyboard(
                Keyboard::make()
                    ->buttons($buttons)
                    ->inline() // Command: Make it "شیشه‌ای" (Inline)
                    ->chunk(1)  // Command: Ensure each button width is 100%
            )
            ->send();

        // Signal the final failure.
        return false;
    }
}
