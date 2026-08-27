<?php

namespace KrubiK\Helpers;
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

use RuntimeException;
use KrubiK\Arcane\InteractsWithLockedProperties;

/**
 * The Phantom Shell v2.4 - The Aligned Operative
 *
 * A spectral proxy now perfectly harmonized with the "Warlord's Command" architecture.
 * It no longer explicitly passes its target. Instead, it "arms" the InteractsWithLockedProperties
 * trait by setting the `_spyTarget` property once, then delegates all calls with complete trust.
 *
 * ☠️ PhantomShell (Obsidian Edition) ☠️
 * The ultimate spectral gateway to an object's soul. This final, readonly proxy class
 * wraps any target and grants the Warlord unfiltered, violent access to its most
 * hidden properties and methods. It moves through encapsulation walls like a ghost.
 *
 * It arms itself with the InteractsWithLockedProperties trait, turning the trait's
 * reflection powers against the outside world.
 * Forged for PHP 8.2 and beyond. Use its power wisely.
 *
 * ☠️ The Hacked Instance (HackdInst) ☠️
 * This proxy class wraps any target object and provides unrestricted, "violent" access
 * to its private/protected properties and methods via PHP's magic methods.
 *
 * It is a Warlord's ultimate tool for direct, unfiltered manipulation of an object's soul.
 * It uses the InteractsWithLockedProperties trait to arm itself with the necessary
 * reflection weaponry.
 *
 * USE WITH EXTREME CAUTION. THIS BYPASSES ALL ENCAPSULATION.
 *
 * @method mixed __call(string $name, array $arguments)
 * @method mixed __get(string $name)
 * @method void  __set(string $name, mixed $value)
 * @method object __unwrap()
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/

class PhantomShell
{
    // By using the trait, PhantomShell inherits the Warlord's Command (`forceCallMethod`)
    // and all contracts to use `_spyTarget`.
    use InteractsWithLockedProperties; // PhantomShell has Generously disseminated his Esoteric Wisdom in the InteractsWithLockedProperties Arcane, across the entire KrubiK Cyber-Citadel, enabled pervasive penetrating access everywhere.

    /**
     * Creates a new PhantomShell, capturing the soul of the target object.
     * The target is readonly, its fate sealed upon instantiation.
     * 
     * Possesses the target object, creating a controllable shell around it.
     * This constructor performs the one-time mission briefing.
     *
     * The soul of the instance. This is the original object being controlled.
     * 
     * @param object $target The object to be possessed by this shell.
    */
    public function __construct(protected object $target)
    {
        // Inform the integrated trait about its designated spy target.
        // All subsequent calls to `force...` methods will now automatically
        // operate on this target object.
        $this->_spyTarget = $target;
    }

    /**
     * Violently retrieves a property from the target, bypassing all visibility rules.
     *
     * Violently rips a property's value from the possessed target,
     * ignoring all visibility modifiers.
     *
     * Violently retrieves a property from the possessed target.
     * It now delegates the call cleanly, knowing the trait will use `_spyTarget`.
     *
     * @param string $name The property to read.
     * @return mixed
    */
    public function __get(string $name): mixed
    {
        if (!$this->isLocked($name)) {
            // No target parameter needed. The trait knows the mission.
            return $this->forceGetProperty($name);
        }
        throw new RuntimeException("Read access to private/protected property [{$name}] is locked. Use unlock('{$name}').");
    }
    
    /**
     * Violently sets a property on the target, bypassing all visibility rules.
     * 
     * Forcefully injects a value into a property of the possessed target,
     * bypassing all encapsulation.
     *
     * Violently sets a property on the possessed target.
     * Clean delegation to the trait's power.
     *
     * @param string $name The property name to write to.
     * @param mixed $value The value to inject.
    */
    public function __set(string $name, mixed $value): void
    {
        if ($this->isLocked($name)) {
            throw new RuntimeException("Modification of private/protected property [{$name}] is locked by default. Use unlock('{$name}') to allow it.");
        }
        // Then No target parameter needed.
        $this->forceSetProperty($name, $value);
    }

    /**
     * Violently calls a method on the target, bypassing all visibility rules.
     *
     * Executes a hidden method on the possessed target as if it were public,
     * passing through any arguments.
     *
     * Violently calls a method on the possessed target.
     * This is the cleanest form: simply pass the command and arguments.
     *
     * @param string $name The method name to invoke.
     * @param array $arguments The arguments for the method.
     * @return mixed
    */
    public function __call(string $name, array $arguments): mixed
    {
        // The Warlord's Command (`forceCallMethod`) automatically detects `_spyTarget`.
        return $this->forceCallMethod($name, $arguments);
    }

    /**
     * 🔥 The Exorcism 🔥
     * Unwraps the shell to return the original, raw target object.
     * Use this to free the soul from your control.
     *
     * Unwraps the instance to return the original, raw target object.
     * A backdoor to get the soul back-sleep.
     * 
     * @return object
     *
     * Retrieves the original, unwrapped object.
     * Provides an escape hatch to get the raw instance back.
    */
    public function __unwrap(): object
    {
        return $this->target;
    }
}
