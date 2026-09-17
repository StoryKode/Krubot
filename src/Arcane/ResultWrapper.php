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

use KrubiK\Krubot;
use KrubiK\WarLording\CommandOutcomeShifter;
use Throwable;

/**
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait ResultWrapper
{
    /**
     * --------------------------------------------------------------------------
     * ⚙️ Global Outcome Wrapping Control
     * --------------------------------------------------------------------------
     * This property acts as a global switch to control whether method
     * call results are wrapped in a CommandOutcomeShifter object.
     *
     * @var bool Defaults to `false` to disable ``->then()_chaining`` by default.
    */
    public bool $wrapsInOutcomeShifter = false;

    /**
     * Globally disables the CommandOutcomeShifter wrapping mechanism.
     *
     * After calling this, all subsequent bot method calls will return the raw
     * result from the driver (e.g., an array, an int, or an exception).
     * This will disable the ->then() chaining capability.
     *
     * @return void
    */
    public function disableOutcomeWrapping(): void
    {
        $this->wrapsInOutcomeShifter = false;
    }
    public function DisableESPromiseMode(): void // switch to Normal Method Chaining
    {
        $this->wrapsInOutcomeShifter = false;
    }
    /**
     * Globally enables the CommandOutcomeShifter wrapping mechanism (default behavior).
     *
     * After calling this, all subsequent bot method calls will wrap their
     * results in a CommandOutcomeShifter object, enabling ->then() chaining.
     *
     * @return void
    */
    public function enableOutcomeWrapping(): void
    {
        $this->wrapsInOutcomeShifter = true;
    }
    public function EnableESPromiseMode(): void // switch to ES-Promises Like Chaining
    {
        $this->wrapsInOutcomeShifter = true;
    }
    /**
     * Globally toggles the CommandOutcomeShifter wrapping mechanism (default behavior).
     *
     * After calling this, all subsequent bot method calls will wrap their
     * results in a CommandOutcomeShifter object, enabling ->then() chaining.
     *
     * @return void
    */
    public function toggleOutcomeWrapping(): void
    {
        $this->wrapsInOutcomeShifter = !$this->wrapsInOutcomeShifter;
    }
    public function toggleESPromises(): void // toggle ECMASciprt_Like-Promises Chaining state
    {
        $this->wrapsInOutcomeShifter = !$this->wrapsInOutcomeShifter;
    }

    public function wrapIfNeeded(mixed $result, ?Krubot $warlord = null, ?Throwable $exception = null): mixed
    {

        $warlord ??= $this;
        
        return $warlord->wrapsInOutcomeShifter
            ? (new CommandOutcomeShifter($warlord, $result, $exception))
            : $result;
    }
}
