<?php

namespace KrubiK\Arcane;
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

use Closure;
use KrubiK\Conversations\Conversation;
use KrubiK\Conversations\Form;
use KrubiK\Conversations\ShadowConversation;
use KrubiK\Conversations\Fields\InteractiveField;
use Illuminate\Support\Facades\Cache;
use KrubiK\Helpers\AmethystMatrix as Log;
use Laravel\SerializableClosure\SerializableClosure;

/**
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait ResilienceKit
{
    /**
     * 🛡️ The Divine Shield of Resilience (resilientCall/rescueResult method) 🛡️
     * 🛡️ The Archangel's Aegis Protocol v4.0 (resilientRun Remastered) 🛡️
     *
     * This method imbues Krubot with a divine shield, allowing it to gracefully
     * Executes a given callback, gracefully catching any exceptions thrown within it.
     * 
     * Acts as a metaphysical force-field, allowing Krubot to continue his mission 
     * even when unforeseen turbulences arise, ensuring a seamless
     * user experience and robust system operation.
     *
     * It integrates deeply with AmethystMatrix for intelligent logging and
     * offers a flexible custom exception handler, embodying the ultimate HyperDX.
     *
     * @param callable $op          The risky operation to execute.
     * @param mixed   $def      TThe value to return if an exception is caught. Defaults to null.
     * @param Closure|null $exceptionHandler Optional. A custom callback to handle the caught exception.
     *                                   It receives: `function(Throwable $e, Krubot $bot): mixed` as arguments.
     *                                   If this handler returns a non-null value, that value (from exceptionHandler) will be used
     *                                   instead of the `defaultValue`. This is where `$handleException($e, $this);`
     *                                   concept finds its ultimate expression.
     * @param null|bool    $useLaravelContainer If true, the callback will be executed via `App::call()`,
     *                                   enabling automatic dependency injection for its parameters. Defaults to false for maximum performance.
     * @param null|bool    $logExceptions      Optional. Whether to log the exception via AmethystMatrix. Defaults to null.
     * @return mixed The result of the callback, the default value, or the result of the customExceptionHandler.
    */
    public function resilientRun(
        callable $op,
        mixed $def = null,
        ?Closure $exceptionHandler = null,
        ?bool $useLaravelContainer = null, // ⚡️ NEW: Control Laravel IoC container usage
        ?bool $logExceptions = null         // Changed to nullable for dynamic fallback
    ): mixed {

        // ⚡ HyperDX Logic: Harmonizing explicit call parameters with Krubot's internal configuration.
        // If the method parameter is explicitly provided (not null), it takes precedence.
        // Otherwise, Krubot consults its internal 'bRCUseLaravelContainer' and 'bRCLogException' states.
        $bUseLaravelContainer = $useLaravelContainer ?? $this->bRCUseLaravelContainer;
        $bLogException = $logExceptions ?? $this->bRCLogException;

        // rename variables
        $callback = &$op;
        $defaultValue = &$def;

        try {
            // Attempt to execute the sacred operation.
            if ($bUseLaravelContainer && function_exists('app')) {
                // ⚡️ Laravel Container Power: Invoke the callback using App::call()
                // This enables automatic dependency injection for the callback's parameters.
                // The Krubot instance ($this) is always available as a bound instance in the container.
                // For optimal flexibility, we pass an array of parameters, ensuring Krubot is available
                // for injection if the callback requests it.

                // Engage the full metaphysical auto-wiring engine.
                $reflection = new ReflectionFunction(Closure::fromCallable($callback));

                // The payload remains simple here as the engine will resolve the rest.
                $payloadData = ['bot' => $this, 'message' => $this->thisMessage(), 'msg' => $this->thisMessage()];
                $extraInjects = [$this, $this->thisMessage()];
                
                return $this->invokeWithAutoWiring(
                    method: $reflection,
                    payloadData: $payloadData,
                    extraInjects: $extraInjects
                    // No forceNative flag needed, as we are in the correct 'if' block.
                );
            } else {
                // --- PATH OF THE SWIFT BLADE (NATIVE PHP) ---
                // No reflection, no overhead. A direct, lightning-fast invocation.
                // Default execution: Directly invoke the callback
                return $callback($this);
                // Pass Krubot instance to the callback for context
            }
        } catch (Throwable $e) {
            // A disturbance in the force detected!
            // Engage the AmethystMatrix for cosmic record-keeping and activate The Divine Shield.

            // 1. 🔮 AmethystMatrix Logging (The Oracle's Chronicle)
            if ($bLogException && class_exists(AmethystMatrix::class)) {
                AmethystMatrix::yell(
                    "Krubot Divine Shield: An unexpected anomaly occurred during a protected operation.",
                    [
                        'error_message' => $e->getMessage(),
                        'error_code'    => $e->getCode(),
                        'file'          => $e->getFile(),
                        'line'          => $e->getLine(),
                        'trace'         => $e->getTraceAsString(),
                        'default_value' => $defaultValue,
                        'operation_context' => 'rescue_attempt',
                        // ⚡ HyperDX: Auto-inject relevant Krubot context for deeper insights
                        'bot_context'   => [
                            'chat_id'       => $this->chatId(),
                            'sender_id'     => $this->senderId(),
                            'message_id'    => $this->findMessageId(),
                            'message_text'  => $this->text(),
                            'driver_codename'  => $this->getDriverCodeName()
                        ]
                    ]
                );
            }

            // 2. ⚡ Custom Exception Handler (The Warlord's Decree)
            // If a custom handler is provided, invoke it. This is where the
            // `$customHandlerResult = $handleException($e, $this);` concept comes to life.
            if ($exceptionHandler instanceof Closure) {

                $customHandlerResult = null;               
                // ⚜️ The handler's execution path mirrors the main operation's path. ⚜️
                if ($bUseLaravelContainer && function_exists('app')) {
                    // ⚡️ NEW: Hyper-Laravel Container Power for exception handler
                    // The handler also gets full auto-wiring power.
                    $handlerReflection = new ReflectionFunction($exceptionHandler);

                    // The payload for the handler includes the exception itself.
                    $handlerPayload = [
                        'bot' => $this, 
                        'message' => $this->thisMessage(),
                        'msg' => $this->thisMessage(),
                        'e' => $e,
                        'exception' => $e,
                        Throwable::class => $e
                    ];
                    // Pass the exception and Krubot instance explicitly, allowing D-I via Laravel|invokeWithAutoWiring() for other params.
                    $handlerExtraInjects = [$this, $this->thisMessage(), $e];

                    $customHandlerResult = $this->invokeWithAutoWiring(
                        method: $handlerReflection,
                        payloadData: $handlerPayload,
                        extraInjects: $handlerExtraInjects
                    );

                } else {

                    // --- PATH OF THE SWIFT BLADE (NATIVE PHP) ---
                    // Direct, fast, and simple invocation for the handler.
                    // Pass the exception and the current Krubot instance to the custom handler
                    $customHandlerResult = $exceptionHandler($e, $this);

                }

                // If the handler returned a non-null value, it is the new decree so takes precedence.
                if ($customHandlerResult !== null) {
                    return $customHandlerResult;
                }
            }

            // 3. ✨ Return Default Value (The Graceful Retreat)
            // If no custom handler or if it returned null, fall back to the default value.
            return $defaultValue;
        }
    }
    /**
     * 🔮 Configures the IoC Container (Laravel App::call) usage for resilientCall.
     *
     * This method allows you to dynamically control whether subsequent calls to `resilientCall`
     * will leverage Laravel's service container for dependency injection within callbacks
     * and exception handlers by default. It's a powerful lever for performance optimization and
     * architectural flexibility, embodying the HyperDX principle of granular control over
     * Krubot's operational parameters.
     *
     * @param bool $useLaravelContainer If true, `resilientCall` will attempt to use `App::call()`
     *                                  by default. If false, it will directly invoke callbacks.
     *                                  Defaults to `true` to enable IoC by default when calling this setter.
     * @return Krubot Returns the current Krubot instance for method chaining,
     *                allowing for fluent configuration of Krubot's metaphysical state.
    */
    public function resilientIoC(bool $useLaravelContainer = true): self
    {
        // ⚡️ Setting the default behavior for IoC container usage across all resilient operations.
        // This affects resilientCall when its `$useLaravelContainer` parameter is null,
        // providing a central control point for Krubot's dependency resolution strategy.
        $this->bRCUseLaravelContainer = $useLaravelContainer;
        return $this; // 🚀 Chainable for fluent configuration, aligning with ECMA2026 paradigms.
    }

    /**
     * 📡 Configures exception logging via AmethystMatrix for resilientCall.
     *
     * @param bool $logExceptions If true, exceptions will be logged by default. If false, they will be
     *                           silently handled without AmethystMatrix intervention.
     *                           Defaults to `true` to enable logging by default when calling this setter.
     * @return Krubot Returns the current Krubot instance for method chaining,
     *                facilitating a fluid configuration experience.
    */
    public function resilientLog(bool $logExceptions = true): self
    {
        // 📡 Setting the default behavior for exception logging across all resilient operations.
        // This affects resilientCall when its `$logExceptions` parameter is null,
        // granting Krubot the power to decide its level of self-reporting.
        $this->bRCLogException = $logExceptions;
        return $this; // 🚀 Chainable for fluent configuration.
    }
}
