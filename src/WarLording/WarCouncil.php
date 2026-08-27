<?php

namespace KrubiK\WarLording;
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
use Throwable;

/**
 * The War Council
 *
 * An ephemeral entity for orchestrating a single command across multiple platforms
 * and analyzing the results as a collective report.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class WarCouncil
{
    /** @var array<string, mixed> Stores the results or exceptions from each driver. */
    protected array $report = [];

    /**
     * @param Krubot $bot The Warlord instance issuing the command.
     * @param array $aliases The list of aliases participating in the council.
     */
    public function __construct(
        protected Krubot $bot,
        protected array $aliases
    ) {}

    /**
     * Broadcasts a single command to all members of the council.
     *
     * @param string $method The name of the method to call on each driver.
     * @param array $params The parameters to pass to the method.
     * @param object|null $context (Future-proof) An optional context object.
     * @return $this The council itself, now containing the battle report.
     */
    public function broadcast(string $method, array $params = [], ?object $context = null): self
    {
        foreach ($this->aliases as $alias) {
            try {
                $driver = $this->bot->core($alias);
                $this->report[$alias] = $driver->{$method}(...$params);
            } catch (Throwable $e) {
                $this->report[$alias] = $e;
            }
        }
        return $this;
    }

    /**
     * Checks if at least one operation failed.
     * @return bool
     */
    public function hasFailures(): bool
    {
        foreach ($this->report as $result) {
            if ($result instanceof Throwable) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets the aliases of all drivers that failed.
     * @return string[]
     */
    public function getFailedAliases(): array
    {
        return array_keys(array_filter($this->report, fn($r) => $r instanceof Throwable));
    }
    
    /**
     * Returns the full battle report.
     * @return array<string, mixed>
     */
    public function getReport(): array
    {
        return $this->report;
    }
}
