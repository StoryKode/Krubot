<?php

declare(strict_types=1);

namespace KrubiK\Attributes;
/*
| Krubot BotEngine: The Architect's Lexicon [×RC.8 ALPHA×] 🚀📜
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
use Illuminate\Contracts\Validation\Rule as LegacyRule;
use Illuminate\Contracts\Validation\ValidationRule;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Validate
{
    /**
     * Parameters this validation applies to.
     *
     * Examples:
     *  - ['email']
     *  - ['email', 'username']
     *  - ['*']
     *
     * @var array<int, string>
    */
    public readonly array $parameters;

    /**
     * Final normalized Laravel validation rules.
     *
     * @var array<int, mixed>
    */
    public readonly array $rules;

    /**
     * @param string|array<int, string> $parameters
     * @param string|array<int, mixed>|LegacyRule|ValidationRule ...$rules
    */
    public function __construct(
        string|array $parameters,
        string|array|LegacyRule|ValidationRule ...$rules
    ) {
        $parameters = is_array($parameters)
            ? $parameters
            : [$parameters];

        $normalizedParameters = [];

        foreach ($parameters as $parameter) {
            if (!is_string($parameter)) {
                throw new InvalidArgumentException(
                    'Validate parameters must contain only strings.'
                );
            }

            $parameter = trim($parameter);

            if ($parameter === '') {
                continue;
            }

            $normalizedParameters[$parameter] = true;
        }

        $this->parameters = array_keys($normalizedParameters);

        if ($this->parameters === []) {
            throw new InvalidArgumentException(
                'Validate requires at least one parameter name.'
            );
        }

        $this->rules = self::normalizeRules($rules);

        if ($this->rules === []) {
            throw new InvalidArgumentException(
                'Validate requires at least one validation rule.'
            );
        }
    }

    /**
     * Convert:
     *
     * #[Validate(['email', 'username'], 'required|min:3')]
     *
     * into:
     *
     * [
     *     'email'    => ['required', 'min:3'],
     *     'username' => ['required', 'min:3'],
     * ]
    */
    public function toArray(): array
    {
        $compiled = [];

        foreach ($this->parameters as $parameter) {
            $compiled[$parameter] = $this->rules;
        }

        return $compiled;
    }

    /**
     * Recursively flattens Laravel-style rule definitions.
     *
     * Supported:
     * - 'required|min:3|rs:ruleSetName'
     * - ['required', 'min:3', 'rs:ruleSetName']
     * - new CustomRule
     * - ['required', new CustomRule(), ['max:255']]
     *
     * @param array<int, mixed> $rules
     * @return array<int, mixed>
    */
    private static function normalizeRules(array $rules): array
    {
        $normalized = [];

        foreach ($rules as $rule) {

            if (is_string($rule)) {
                foreach (explode('|', $rule) as $part) {
                    $part = trim($part);

                    if ($part !== '') {
                        $normalized[] = $part;
                    }
                }

                continue;
            }

            if (is_array($rule)) {
                foreach (self::normalizeRules($rule) as $nestedRule) {
                    $normalized[] = $nestedRule;
                }

                continue;
            }

            if (
                $rule instanceof LegacyRule
                || $rule instanceof ValidationRule
            ) {
                $normalized[] = $rule;

                continue;
            }

            throw new InvalidArgumentException(
                sprintf(
                    'Unsupported validation rule type [%s].',
                    get_debug_type($rule)
                )
            );
        }

        return $normalized;
    }
}
