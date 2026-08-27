<?php

namespace KrubiK\GamifyDices\Types;
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

use Stringable;

/**
 * A Readonly Object representing a specific Dice.
 * 
 * - Acts as a String ('⚽') when used in string context (API compatible).
 * - Holds metadata (name, max value) when used as an object (Logic compatible).
 * 
 * @immutable
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
readonly class DiceVariant implements Stringable
{
    public function __construct(
        public string $emoji,
        public string $name,
        public int $max,
    ) {}

    /**
     * Returns the emoji string when the object is echoed or used in string concatenation.
     */
    public function __toString(): string
    {
        return $this->emoji;
    }
}
