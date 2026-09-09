<?php

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
use LogicException;
use Illuminate\Contracts\Validation\Rule as LegacyRule;
use Illuminate\Contracts\Validation\ValidationRule;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class RuleSet
{
    /**
     * Globally meaningful name inside the current Krubot instance.
    */
    public readonly string $name;

    /**
     * Normalized rules belonging to this named set.
     *
     * @var array<int, mixed>
    */
    public readonly array $rules;

    /**
     * @param string $name
     * @param string|array<int, mixed>|LegacyRule|ValidationRule ...$rules
    */
    public function __construct(
        string $name,
        string|array|LegacyRule|ValidationRule ...$rules
    ) {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException(
                'RuleSet name cannot be empty.'
            );
        }

        $this->name = $name;
        $this->rules = self::normalize($rules);

        if ($this->rules === []) {
            throw new InvalidArgumentException(
                "RuleSet [{$name}] must contain at least one rule."
            );
        }
    }

    /**
     * Return this RuleSet as a plain rules array.
     *
     * @return array<int, mixed>
    */
    public function toArray(): array
    {
        return $this->rules;
    }

    /**
     * Expands rs:* references against the current Krubot RuleSet registry.
     *
     * Supports nested RuleSets too:
     *
     * RuleSet A -> rs:B -> rs:C
     *
     * Circular references are rejected.
     *
     * @param array<int, mixed> $rules
     * @param array<string, array<int, mixed>> $registry
     *
     * @return array<int, mixed>
    */
    public static function expand(
        array $rules,
        array $registry
    ): array {
        $expanded = [];

        $walk = static function (
            mixed $rule,
            array $stack = []
        ) use (
            &$walk,
            &$expanded,
            $registry
        ): void {
            if (is_array($rule)) {
                foreach ($rule as $nested) {
                    $walk($nested, $stack);
                }

                return;
            }

            if (
                is_string($rule)
                && str_starts_with($rule = trim($rule), 'rs:')
            ) {
                $name = trim(substr($rule, 3));

                if ($name === '') {
                    throw new LogicException(
                        'Empty RuleSet reference [rs:].'
                    );
                }

                if (!array_key_exists($name, $registry)) {
                    throw new LogicException(
                        "Unknown RuleSet [{$name}]. " .
                        'Make sure the Nexus containing #[RuleSet(...)] ' .
                        'has been integrated.'
                    );
                }

                if (isset($stack[$name])) {
                    $chain = implode(
                        ' -> ',
                        [...array_keys($stack), $name]
                    );

                    throw new LogicException(
                        "Circular RuleSet reference detected: {$chain}"
                    );
                }

                $nextStack = $stack;
                $nextStack[$name] = true;

                foreach ($registry[$name] as $setRule) {
                    $walk($setRule, $nextStack);
                }

                return;
            }

            $expanded[] = $rule;
        };

        foreach ($rules as $rule) {
            $walk($rule);
        }

        return $expanded;
    }

    /**
     * @param array<int, mixed> $rules
     *
     * @return array<int, mixed>
    */
    private static function normalize(array $rules): array
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
                foreach (self::normalize($rule) as $nested) {
                    $normalized[] = $nested;
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
                    'Unsupported RuleSet item type [%s].',
                    get_debug_type($rule)
                )
            );
        }

        return $normalized;
    }
}

