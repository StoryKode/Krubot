<?php

namespace KrubiK\Attributes;
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

use Attribute;
use InvalidArgumentException;

/**
 * FINAL, UNAMBIGUOUS, ROCK-SOLID IMPLEMENTATION
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class When
{
    public readonly string $operator;
    public readonly string $stateKey;
    public readonly mixed $expectedValue;
    public readonly bool $hasExpectedValue;
    public readonly ?string $failMessage;

    private const VALID_OPERATORS = ['=', '!', '>', '<', '~', '×'];

    /**
     * The definitive, predictable, and non-magical constructor for the When attribute.
     * Follows a strict (state, value, message) signature.
     *
     * @param string $stateAndOperator The combined state key, potentially prefixed with an operator.
     * @param mixed ...$args The value to compare against (arg 0) and the failure message (arg 1).
     */
    public function __construct(
        string $stateAndOperator,
        ...$args
    ) {
        // --- Step 1: Parse operator and state key (unchanged, still fast) ---
        $this->parseStateAndOperator($stateAndOperator);

        // --- Step 2: STRICT, POSITIONAL ASSIGNMENT. No magic, no inference. ---
        $argCount = count($args);

        $this->hasExpectedValue = isset($args[0]);
        $this->expectedValue = $this->hasExpectedValue ? $args[0] : null;
        $this->failMessage = isset($args[1]) ? (string) $args[1] : null;

        // Argument validation
        if ($argCount > 2) {
            throw new InvalidArgumentException("The #[When] attribute accepts a maximum of 3 arguments (state, value, message).");
        }
        
        $operatorRequiresValue = in_array($this->operator, ['!', '>', '<', '~', '×'], true);
        if ($operatorRequiresValue && !$this->hasExpectedValue) {
            throw new InvalidArgumentException("Operator '{$this->operator}' for '{$this->stateKey}' requires an expectedValue as the second argument.");
        }
    }
    
    private function parseStateAndOperator(string $input): void
    {
        $firstChar = $input[0] ?? '';
        if ($firstChar !== '' && !ctype_alnum($firstChar) && $firstChar !== '_') {
            $operator = $firstChar;
            $key = substr($input, 1);
            if (in_array($operator, self::VALID_OPERATORS, true)) {
                $this->operator = $operator;
                $this->stateKey = $key;
                return;
            }
        }
        $this->operator = '=';
        $this->stateKey = $input;
    }
}
