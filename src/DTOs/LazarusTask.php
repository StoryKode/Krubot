<?php

namespace KrubiK\DTOs;
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

/**
 * A fluent, immutable handle for a scheduled task created via Lazarus::todo().
 * It provides a clean way to access the task's ID, due date, and to cancel it.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
final class LazarusTask
{
    public function __construct(
        private readonly string $id,
        private readonly ?\DateTimeImmutable $due
    ) {}

    /**
     * Factory method to create a representation of a rejected task.
     * This avoids returning null and maintains a consistent return type.
     */
    public static function rejected(): self
    {
        return new self('', null);
    }

    /**
     * Get the unique identifier of the scheduled task.
     */
    public function id(): string
    {
        return $this->id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the scheduled execution time of the task.
     */
    public function due(): ?\DateTimeImmutable
    {
        return $this->due;
    }

    /**
     * Attempt to cancel the scheduled task.
     * Returns true on success, false if cancellation is not possible (e.g., for a rejected handle).
     */
    public function cancel(): bool
    {
        // Delegate the cancellation call to the Lazarus instance that created this task.
        // The nullsafe operator (?->) gracefully handles rejected tasks where protocol is null.
        return LazarusProtocol::cancelTodo($this->id) ?? false;
    }
}
