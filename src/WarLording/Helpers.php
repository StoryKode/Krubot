<?php

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

use KrubiK\Krubot;
use KrubiK\Helpers\PhantomShell;
use KrubiK\Helpers\AmethystMatrix;
use KrubiK\Helpers\OpcacheRuler;

/**
 * KrubiK WarLording Helpers v3.0
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/

if (! function_exists('warlord')) {
    /**
     * ⚡️ THE GLOBAL CONDUIT TO THE WARLORD – KRUBOT ⚡️
     *
     * The Warlord's global command helper function.
     *
     * Provides global, fluent access to the Krubot instance and its drivers.
     *
     * The ultimate helper function for issuing commands across the empire.
     * This is the fastest, most direct way to access any driver from anywhere
     * in your Laravel application (e.g., within Jobs, Listeners, or Controllers).
     *
     * It resolves the master Krubot instance from the container and immediately
     * sets the target driver for a single, surgical strike.
     * 
     * Examples:
     * warlord()->reply('Default driver message.');
     * warlord('tg')->reply('Telegram message.');
     * warlord(['r', 'b'])->via('Note! multi-cast is not supported like this'); // Use warlord()->assembleCouncil()
     *
     * @param string|array|null $driverAlias The target driver(s) ('tg', 'bale', ['r', 't']). If null, targets the default.
     * @return Krubot An instance of Krubot, primed and ready for command chaining.
     */
    function warlord(string|array|null $driverAlias = null): Krubot
    {
        // 1. Resolve the Singleton instance of the Warlord from Laravel's core.
        $bot = app('krubot'); // :OR: resolve(Krubot::class);
        if ($driverAlias === null)
            return $bot;

        // 2. Immediately invoke the `via()` method to set the temporary target.
        return $bot->via($driverAlias);
    }
}
if (! function_exists('krubot')) {
    function krubot(string|array|null $driverAlias = null): Krubot
    {
        return warlord($driverAlias);
    }
}

if (! function_exists('phantomshell')) {
    /**
     * 👻 SUMMONS A PHANTOMSHELL ON DEMAND 👻
     *
     * A fail-safe helper to wrap any object in a PhantomShell for violent introspection.
     * If the target is falsy (null, false, empty string/array), it wisely returns null,
     * preventing errors and ensuring smooth, chainable operations.
     *
     * @param object|null $target The object to be possessed.
     * @return PhantomShell|null The spectral avatar or null if the target is unworthy.
     */
    function phantomshell(?object $target): ?PhantomShell
    {
        // If the target is not a valid object, return null immediately.
        if (! $target) {
            return null;
        }

        // Summon the shell and grant unlimited access/control.
        return new PhantomShell($target);
    }
}

if (! function_exists('amethyst')) {
    /**
     * The Master Key to the Amethyst Matrix. A dynamic, intelligent global helper.
     * 
     * Provides a Hyper-DX interface for logging and accessing the Matrix's powers.
     * 
     * The Macro-Aware Quantum Conduit.
     * Supports AmethystMatrix Standard Static Methods AND Dynamic Laravel Macros.
     * 
     * NOW FEATURING: AUTO-AWAKENING PROTOCOL. ⚡
     *
     * USAGE 1: Access the static class itself.
     * Returns the fully-qualified class name to allow chaining any static method.
     * In Accessor Mode (no arguments), it returns the Matrix's class name,
     * allowing for fluent static method calls like `amethyst()::scream(...)`.
     * 
     * Example: amethyst()::gaze($variable);
     * Example: amethyst()::recall('some_key');
     *
     * USAGE 2: Log an informational event directly.
     * Acts as a shortcut for the primary 'observe' method for quick logging.
     *
     * In Executor Mode (with arguments), it dynamically calls any public static method on the AmethystMatrix,
     * providing a graceful fallback to `observe()` if an unknown method is invoked and it bounds to return null when an unknown/inaccessible method is invoked.
     * 
     * Example: amethyst('gaze', 'User created an account', $user->id);
     * Example: $storedMaxRetries = amethyst('recall', 'max-retries', 3);
     * Example: $callRes = amethyst('manipulate', 313); // 313 will be logged but `$callRes === null`
     *
     * @param string|null $method The name of the static method to call (e.g., 'gaze', 'whisper').
     * @param mixed       ...$args The arguments to pass to that method.
     *
     * @return mixed The result of the called method, the class name string in Accessor Mode, or null on failure on access method.
     */
    function amethyst(string $method = null, mixed ...$args): mixed
    {
        $targetClass = AmethystMatrix::class;

        // --- ⚡ AUTO-AWAKENING PROTOCOL ⚡ ---
        if (! $targetClass::$isAwake)
            $targetClass::refresh();

        if (! $targetClass::$isAwake) { // yet ?!
            // Return a predictable value for the failed operation.
            return null;
        }

        // ACCESSOR MODE: Return the master key to the Matrix itself.
        if (func_num_args() === 0) {
            return $targetClass;
        }

        // EXECUTOR MODE: Dynamic method invocation with intelligent fallback.
        // Dynamic, secure invocation with result tunneling.

        // --- INTELLIGENT VALIDATION PROTOCOL ---        
        //     --- THE ALGORITHMIC CORE ---

        // VALIDATION: Check if the requested spell exists in the AmethystMatrix's grimoire
        // && is the target method callable from this global scope?
        // This is the ultimate check for public, static existence. Fast and elegant.

        // We use this static property to CACHE the public interface of the class.
        // This runs only ONCE per request, making subsequent calls lightning fast.
        // Now We populate the class's consciousness onto $targetClass::$publicInterface
        if (empty($targetClass::$publicInterface)) {
            // MAGIC TRICK: Calling get_class_methods from OUTSIDE the class!
            // naturally returns ONLY public methods. No Reflection Checking needed!
            // We flip it to create a Hash Map for O(1) lookup speed.
            $targetClass::$publicInterface = array_flip(get_class_methods($targetClass));
        }
        // CHECK 1: Is it a Real, Public Method?
        // isset() on a hash map is significantly faster than in_array() or Reflection; Direct array lookup. The fastest operation in PHP.
        // If this was in this list, PHP itself guarantees that it's a PUBLIC method.
        if (isset($targetClass::$publicInterface[$method])) {
            // SUCCESS: The spell is known and allowed to use. Cast it directly and return the result.
            return $targetClass::$method(...$args);
        }

        // CHECK 2: Is it a Laravel Macro?
        // We only check this if the native check fails.
        // We check for the trait's existence strictly to avoid errors.
        if (isset($targetClass::$publicInterface['hasMacro']) && $targetClass::hasMacro($method)) {
            // SUCCESS: The spell is known and allowed to use. Cast it directly and return the result.

            // It is safe to call. Even if it's a macro, __callStatic will now handle it 
            // without throwing an exception because we verified existence via hasMacro.
            return $targetClass::$method(...$args);
        }

        // GRACEFUL FAILURE: The spell is unknown or inaccessible. Improvise without losing data.
        
        // Step 1: Log a prophecy about the failed attempt for the developer's awareness.
        $targetClass::prophesy(
            "Unknown method called via amethyst() helper.",
            [
                'unknown_method'  => $method,
                'provided_args'   => $args,
                // 'was_inaccessible'      => method_exists($targetClass, $method), // Add extra intel
                'fallback_action' => $targetClass . '::observe()'
            ]
        );

        // Step 2: Fulfill the user's primary intent. Log the data using the most generic method.
        // We intelligently assume the first argument was the intended primary message/signal.
        $fallbackSignal = array_shift($args) ?? 'Untranslatable signal from a failed helper call';
        
        // The rest of the arguments become the context for the generic 'observe' method.
        $targetClass::observe($fallbackSignal, ...$args);

        // Step 3: Return a predictable value for the failed operation.
        return null;
    }
}

if (!function_exists('opcache')) {
    /**
     * Get the OpcacheManager instance for method chaining, or invoke it.
     *
     * - If called with NO arguments `opcache()`:
     *   It returns the underlying OpcacheManager instance, allowing you to chain methods.
     *   e.g., `opcache()->flush();` or `opcache()->status();`
     *
     * - If called WITH ANY arguments `opcache(null)`, `opcache(true)`:
     *   It resolves the instance and immediately calls its `__invoke` method,
     *   passing along all arguments.
     *
     * @param  mixed  ...$parameters
     * @return \KrubiK\Helpers\OpcacheRuler|bool
     */
    function opcache(...$parameters)
    {
        // Always resolve the core service instance from the container.
        $instance = app(OpcacheRuler::class);

        // Check if any arguments were passed to this helper function.
        // The func_num_args() is the most explicit way to check this.
        // Using `if ($parameters)` also works well here.
        if (func_num_args() > 0) {
            // If arguments exist (e.g., opcache(null)), call the __invoke magic method on the instance
            // and pass the arguments through using the spread operator.
            return $instance(...$parameters);
        }

        // If NO arguments were passed (e.g., opcache()), return the raw instance
        // itself, making its methods chainable.
        return $instance;
    }
}

if (!function_exists('advanceDateTime')) {
    /**
     * Normalizes various time representations into a standard DateTimeImmutable object.
     *
     * @param \DateTimeInterface|\DateInterval|int|string $when
     * @return \DateTimeImmutable
    */
    function advanceDateTime(
        \DateTimeInterface|\DateInterval|int|string $when
    ): \DateTimeImmutable {

        // Get the current time once to ensure consistency within this function.
        $now = now();

        return match (true) {
            $when instanceof \DateTimeImmutable => $when,
            $when instanceof \DateTimeInterface => \DateTimeImmutable::createFromInterface($when),
            $when instanceof \DateInterval => $now->add($when)->toDateTimeImmutable(),

            // Treat large integers as Unix timestamps, smaller ones as second delays.
            is_int($when) && $when >= 1_000_000_000 => (new \DateTimeImmutable())->setTimestamp($when),
            is_int($when) => $now->addSeconds($when)->toDateTimeImmutable(),

            // Use Laravel's powerful Carbon::parse for string-based dates.
            is_string($when) => \Illuminate\Support\Carbon::parse($when)->toDateTimeImmutable(),
        };
    }
}
