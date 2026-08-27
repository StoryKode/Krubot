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

use Closure;
use KrubiK\Krubot;
use Illuminate\Support\Facades\App;
use Throwable;

/**
 * ⚔️ THE FATEWEAVER'S EDICT (CommandOutcomeShifter 3.0) ⚔️
 *
 * This is not merely a result; it is the encapsulated destiny of a command.
 * It provides a bulletproof, fluent, and highly expressive API for handling
 * operations that may succeed or fail, inspired by modern Promise-based patterns
 * and supercharged with Laravel's Dependency Injection container.
 *
 * Every chained method receives the full power of Laravel's service container,
 * allowing for clean, testable, and powerful callbacks.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class CommandOutcomeShifter
{
    /**
     * The successful result of the operation. Null on failure.
     */
    protected readonly mixed $result;

    /**
     * The exception caught during a failed operation. Null on success.
     */
    protected readonly ?Throwable $exception;

    /**
     * Private constructor to force creation via the `execute` factory.
     * This ensures every instance is in a valid state (either result or exception).
     *
     * @param Krubot $bot The Warlord instance, providing context for all callbacks.
     * @param mixed $result The successful result.
     * @param Throwable|null $exception The captured failure.
     */
    private function __construct(
        protected readonly Krubot $bot,
        mixed $result,
        ?Throwable $exception
    ) {
        $this->result = $result;
        $this->exception = $exception;
    }

    /**
     * ⚡️ THE FORGE OF DESTINY ⚡️
     *
     * Primary factory method. Executes the initial operation within a safe try-catch
     * block and forges the first link in the fate chain.
     *
     * @param Krubot $bot The main Krubot instance, passed once for the entire chain.
     * @param Closure $operation The command to execute, e.g., fn() => $bot->core()->sendMessage(...).
     * @return self The initial CommandOutcome, representing success or failure.
     */
    public static function execute(Krubot $bot, Closure $operation): self
    {
        try {
            // Execute the initial, raw operation.
            $result = $operation();
            return new self($bot, $result, null);
        } catch (Throwable $e) {
            // If it fails, capture the exception and forge a failure outcome.
            return new self($bot, null, $e);
        }
    }

    /**
     * 🏆 THE VICTORY PROTOCOL 🏆
     *
     * Executes a callback only if the preceding operation was successful.
     * The result of this callback determines the fate of the next link in the chain.
     *
     * The callback benefits from Laravel's full Dependency Injection.
     * You can type-hint any service, plus access the previous result by name 'result'.
     *
     * @param Closure $callback fn(mixed $result, Krubot $bot, YourService $service, ...): mixed
     * @return self A new CommandOutcome wrapping the callback's result, or the propagated failure.
     */
    public function then(Closure $callback): self
    {
        if ($this->isFailure()) {
            return $this; // Propagate failure silently.
        }

        try {
            // Execute the callback using Laravel's container for DI.
            $nextResult = App::call($callback, [
                'result' => $this->result // Inject previous result by name.
            ]);
            return new self($this->bot, $nextResult, null);
        } catch (Throwable $e) {
            // If the `then` block itself fails, the chain's fate turns to failure.
            return new self($this->bot, null, $e);
        }
    }

    /**
     * 🛡️ THE CONTINGENCY PROTOCOL 🛡️
     *
     * Executes a callback only if the preceding operation failed.
     * Ideal for logging, sending alerts, or attempting recovery actions.
     * Does not change the outcome's "failed" state.
     *
     * The callback receives the captured exception via DI ('exception', 'e', or Throwable::class).
     *
     * @param Closure $callback fn(Throwable $e, Krubot $bot, LoggerInterface $logger, ...): void
     * @return self Returns the same failed instance for further chaining (e.g., to a `finally`).
     */
    public function catch(Closure $callback): self
    {
        if ($this->isSuccessful()) {
            return $this; // Do nothing on success.
        }

        try {
            // Execute the error handler with the exception injected.
            App::call($callback, [
                'exception' => $this->exception,
                'e'         => $this->exception, // Common alias
            ]);
        } catch (Throwable $newException) {
            // If the error handler itself throws an exception, create a new failure state
            // with this more critical, secondary exception.
            return new self($this->bot, null, $newException);
        }

        return $this;
    }

    /**
     * ⚖️ THE FINAL JUDGEMENT ⚖️
     *
     * Executes a callback regardless of success or failure.
     * Perfect for cleanup tasks like releasing locks or closing resources.
     * The original outcome (success or failure) is passed through after this block.
     *
     * @param Closure $callback fn(CommandOutcome $outcome, Krubot $bot, ...): void
     * @return self Returns the original outcome instance, allowing the chain to continue.
     */
    public function finally(Closure $callback): self
    {
        try {
            // Execute the final callback, injecting the current outcome for inspection.
            App::call($callback, ['outcome' => $this]);
        } catch (Throwable $e) {
            // If `finally` fails, its exception is critical and supersedes the original outcome.
            return new self($this->bot, null, $e);
        }

        // Pass through the original state.
        return $this;
    }

    /**
     * Checks if the chain is currently in a successful state.
     */
    public function isSuccessful(): bool
    {
        return $this->exception === null;
    }

    /**
     * Checks if the chain is currently in a failed state.
     */
    public function isFailure(): bool
    {
        return $this->exception !== null;
    }

    /**
     * Retrieves the successful result. Returns null if the operation failed.
     */
    public function getResult(): mixed
    {
        return $this->result;
    }

    /**
     * Retrieves the captured exception. Returns null if the operation was successful.
     */
    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    /**
     * 💥 UNLEASH THE FATE 💥
     *
     * A terminal operation. If the outcome was a failure, it re-throws the
     * captured exception. If successful, it returns the final result.
     * This is how you break the chain and handle the final state imperatively.
     *
     * @return mixed The final successful result.
     * @throws Throwable If the outcome was a failure.
     */
    public function throw(): mixed
    {
        if ($this->exception) {
            throw $this->exception;
        }
        return $this->result;
    }
}
