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

use KrubiK\Helpers\AmethystMatrix;
use KrubiK\DTOs\Message;

trait HasAmethystMatrix
{
    /**
     * Boot the Matrix when the Warlord wakes up.
     * This ensures Amethyst knows WHO is currently executing commands.
    */
    public function awakenAmethystMatrix(): void
    {
        // 🔗 Link this instance to the Static Facade
        AmethystMatrix::awaken($this);
    }

    /**
     * Sets/Clears The-Context-Message for the current AmethystMatrix Logging cycle.
     * @internal
    */
    public function tunnelAmethyst(?Message $message = null): void
    {
        AmethystMatrix::setWorkingMessage($message);
    }

    /**
     * Retrieves The-Context-Message for the current AmethystMatrix Logging cycle.
     * @internal
    */
    public function whereAmethyst(): ?Message
    {
        return AmethystMatrix::getWorkingMessage();
    }
    
    /**
     * Destructor to ensure we break the link in long-running processes.
    */
    public function sleepAmethystMatrix(): void
    {
        // 🌑 Disconnect on death
        AmethystMatrix::sleep();
    }
    
    /**
     * Direct access to the Matrix class name for static calls via variable.
     * $matrix = $bot->matrix(); $matrix::observe(...);
     */
    public function matrix(): string
    {
        return AmethystMatrix::class;
    }
}