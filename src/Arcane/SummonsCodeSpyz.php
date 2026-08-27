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

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Exception;
use RuntimeException;

use KrubiK\Helpers\PhantomShell; // 🔥 Summon The Ghost 🔥

/**
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait SummonsCodeSpyz
{
    /**
     * 🔥 THE SUMMONING SPELL 🔥
     * Wraps any object in a PhantomShell, returning a spectral control avatar.
     *
     * @param object $target The object to take control of.
     * @return PhantomShell The control avatar.
     */
    public static function control(object $target): PhantomShell
    {
        return new PhantomShell($target);
    }
    
    /**
     * 🔥 NEW ALIAS: The Ghosting Spell 🔥
     * A more thematic alias for the `control` method.
     *
     * @param object $target The object to haunt.
     * @return PhantomShell The spectral control avatar.
     */
    public static function ghost(object $target): PhantomShell
    {
        return static::control($target);
    }
    
    /**
     *  🔥 NEW ALIAS: Krubot::PhantomShell() 🔥
     * 👻 SUMMONS A PHANTOMSHELL ON DEMAND 👻
     *
     * A fail-safe helper to wrap any object in a PhantomShell for violent introspection.
     * If the target is falsy (null, false, empty string/array), it wisely returns null,
     * preventing errors and ensuring smooth, chainable operations.
     *
     * @param object|null $target The object to be possessed.
     * @return PhantomShell|null The spectral avatar or null if the target is unworthy.
    */
    public static function phantomshell(object $target): PhantomShell
    {
        return static::control($target);
    }

    /**
     * 🕵️‍♂️ WARLORD'S GAMBIT SPY PROTOCOL v2.4
     * Provides a unified, flexible, and silent interface for interacting with properties
     * on the `_spyTarget` (if set) or on `$this` (the Krubot instance itself).
     *
     * This method offers polymorphic behavior:
     * - `spy()`: Returns the current spy target or `$this`.
     * - `spy(string $name)`: Gets a single property.
     * - `spy(array $names)`: Gets multiple properties, returning an associative array.
     * - `spy(string $name, mixed $value)`: Sets a single property.
     * - `spy(array $names, array $values)`: Sets multiple properties in a batch.
     *
     * **Key Features:**
     * - **Intelligent Prioritization:** Prefers `_spyTarget` properties; falls back to `$this`.
     * - **Auto-Unlock:** Automatically unlocks properties for the duration of the `get`/`set` operation.
     * - **Silent Operation:** Never throws exceptions. Returns `null` for unfound gets, `0` for unsuccessful sets.
     * - **DX Optimized:** Designed for maximum developer experience and fluid chaining.
     *
     * @param string|array|null $nameOrNames The property name(s) to get/set.
     * @param mixed|array|null $valueOrValues The value(s) to set.
     * @return mixed The property value, an array of values, the object itself, or `null`/`0` on failure.
     */
    public function spy(string|array|null $nameOrNames = null, mixed $valueOrValues = null): mixed
    {
        // -----------------------------------------------------------------
        // 🕵️‍♂️ Mode 1: No Arguments (Return Control Avatar of [Spy Target || self])
        // 
        // Usage: $bot->spy() -> returns the currently spied object'z Avatar.
        // 
        // 🕵️‍♂️ Mode 1: No Arguments (Summon the Control Avatar)
        // -----------------------------------------------------------------
        if (func_num_args() === 0) {
            $target = $this->_spyTarget ?? $this;
            return static::control($target);
        }

        // Determine the actual target object(s) for the operation
        // Priority: _spyTarget (if set) > $this
        // We use an anonymous class as a proxy to avoid exposing methods
        // from the original target that are not intended to be called directly.
        $targets = [];
        if ($this->_spyTarget) {
            $targets[] = $this->_spyTarget;
        }
        $targets[] = $this; // Always include $this as a fallback

        // -----------------------------------------------------------------
        // 🕵️‍♂️ Mode 2: One Argument (Getter)
        // Usage: $bot->spy('curlTimeout') or $bot->spy(['curlTimeout', 'xzcounter'])
        // -----------------------------------------------------------------
        if (func_num_args() === 1) {
            if (is_string($nameOrNames)) {
                // Single property get
                foreach ($targets as $target) {
                    if ($this->hasProperty($nameOrNames, $target)) {
                        return $this->forceGetProperty($nameOrNames, $target);
                    }
                }
                return null; // Not found in any target
            } elseif (is_array($nameOrNames)) {
                // Multiple properties get
                $results = [];
                foreach ($nameOrNames as $propName) {
                    $found = false;
                    foreach ($targets as $target) {
                        if ($this->hasProperty($propName, $target)) {
                            $results[$propName] = $this->forceGetProperty($propName, $target);
                            $found = true;
                            break; // Found in this target, move to next property
                        }
                    }
                    if (!$found) {
                        $results[$propName] = null; // Property not found in any target
                    }
                }
                return $results;
            }

            elseif($nameOrNames instanceof stdClass || is_object($nameOrNames)) {
                return static::control($nameOrNames); // so... +> $bot->spy($anyObjOrInstance) : \PhantomShell($obJInstanc)\ ;
            }
        }

        // -----------------------------------------------------------------
        // 🕵️‍♂️ Mode 3: Two Arguments (Setter)
        // Usage: $bot->spy('curlTimeout', 1.1) or $bot->spy(['cT', 'xZ'], [1.1, 999])
        // -----------------------------------------------------------------
        if (func_num_args() === 2) {
            $affectedCount = 0;

            if (is_string($nameOrNames) && $valueOrValues !== null) {
                // Single property set
                foreach ($targets as $target) {
                    if ($this->hasProperty($nameOrNames, $target)) {
                        if ($this->forceSetProperty($nameOrNames, $valueOrValues, $target)) {
                            $affectedCount++;
                        }
                        break; // Property found and attempted to set, move on.
                    }
                }
            } elseif (is_array($nameOrNames) && is_array($valueOrValues) && count($nameOrNames) === count($valueOrValues)) {
                // Multiple properties set
                $propertiesToSet = array_combine($nameOrNames, $valueOrValues);
                foreach ($propertiesToSet as $propName => $propValue) {
                    foreach ($targets as $target) {
                        if ($this->hasProperty($propName, $target)) {
                            if ($this->forceSetProperty($propName, $propValue, $target)) {
                                $affectedCount++;
                            }
                            break; // Property found and attempted to set, move to next property.
                        }
                    }
                }
            }
            return $affectedCount; // Return number of properties successfully set
        }

        // Fallback for unsupported calls (e.g., wrong argument types/counts)
        return null;
    }
}
