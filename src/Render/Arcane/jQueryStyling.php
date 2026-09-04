<?php

namespace KrubiK\Render\Arcane;
/*
| Krubot BotEngine: The Architect's Lexicon [×RC.8×] 🚀📜
|--------------------------------------------------------------------------
| This is **a Playground For Mastery**, a laboratory of ***Software Dev Artistry***;
| not a weapon for production's final battles.
|
| Our Bond: ***"Rebuilding The Rebellion"*** Within S.N.P. (The Foundation of Pure Power & Revel).
| Your Mandate [MIT]: Deconstruct Krubot. Command it. Master it. You are The Architect Now!
|
| *Go build something revolutionary!* 💜⚡️
*/

use Illuminate\Contracts\Support\Arrayable;

/**
 * jQuery-inspired, entity-scoped styling DSL for fluent HTML composition.
 *
 * Provides Arrayable-aware CSS/class mutation, multi-getters, normalization,
 * deduplication, removal semantics, and deterministic presentation state.
 *
 * Transparently decorates the first semantic HTML element while preserving
 * existing attributes and allowing entity-level declarations to take precedence.
 *
 * Designed as a zero-dependency rendering concern: composable, chainable,
 * serialization-safe, and equally ergonomic for scalar, associative, or iterable-style inputs.
 * 
 * @template T of static
 * @mixin T
*/
trait jQueryStyling
{

    /**
     * Entity-level CSS classes.
     *
     * @var array<string, true>
    */
    protected array $richClasses = [];

    /**
     * Entity-level inline CSS declarations.
     *
     * @var array<string, string>
    */
    protected array $richStyles = [];

    /**
     * jQuery-inspired CSS Hyper-Getter|Setter ;)
     *
     * Getterz:
     *      css()
     *          → all styles
     *
     *      css('color')
     *          → one style or null
     *
     *      css(['color', 'display'])
     *          → associative multi-getter; missing values are null
     *
     * Setterz:
     *      css(['color' => 'red', 'font-size' => '14px'])
     *          → multi-setter
     *
     *      css('color', 'red')
     *          → setter
     *
     *      css('color', null)
     *          → remover
     *
     *      css('color:red')
     *          → colon-syntax setter
     *
     *      css('color:red; font-family:RW_FXT; font-size:3em')
     *          → colon-syntax multi-setter
     *
     *      css('color:')
     *          → colon-syntax remover
     *
     *      css('color:red; font-weight:; font-size:3em')
     *          → mixed set/remove in one string
     *            → color: 'red'
     *            → font-size: '3em'
     *            → font-weight: removed
     *
     * @return static|array<string, string|null>|string|null
    */
    public function css(
        string|array|Arrayable|null $property = null,
        string|int|float|null $value = null
    ): static|array|string|null {
        $arguments = func_num_args();
    
        /*
         * css()
         * → Get all styles
        */
        if ($arguments === 0) {
            return $this->richStyles;
        }

        // Arrayable → array, exactly once.
        if ($property instanceof Arrayable) {
            $property = $property->toArray();
        }
    
        /*
         * css([...])
         *
         * Non-associative array:
         * → Multi-getter
         *
         * ['color', 'font-size']
         * →
         * [
         *     'color'     => 'red',
         *     'font-size' => null,
         * ]
         *
         * Associative array:
         * → Multi-setter
         *
         * [
         *     'color' => 'red',
         *     'margin' => 0,
         * ]
        */
        if (is_array($property)) {
            if (self::isAssoc($property)) { // or use native array_is_list()
                foreach ($property as $name => $value) {
                    $name = $this->normalizeCssProperty((string) $name);
    
                    // Multi-Setter / remover
                    if ($value === null) {
                        unset($this->richStyles[$name]);
                        continue;
                    }
    
                    $this->richStyles[$name] = trim((string) $value);
                }
    
                return $this;
            }
    
            $styles = [];
    
            foreach ($property as $name) {
                if (!is_string($name)) {
                    continue;
                }
    
                $name = $this->normalizeCssProperty($name);
    
                $styles[$name] = $this->richStyles[$name] ?? null;
            }
    
            return $styles;
        }

        // NEW: string with colon → colon-separated style string
        if (is_string($property) && str_contains($property, ':')) {
            if ($value !== null) {
                throw new \InvalidArgumentException(
                    'Second argument is not allowed when using colon syntax.'
                );
            }

            // Split by semicolons, trim each part, skip empty parts
            $declarations = preg_split('/\s*;\s*/', trim($property), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($declarations as $declaration) {
                // Split into property and value at the first colon
                $parts = explode(':', $declaration, 2);
                if (count($parts) !== 2) {
                    continue; // ignore malformed declarations
                }

                $prop = trim($parts[0]);
                $val  = trim($parts[1]);

                if ($prop === '') {
                    continue;
                }

                $prop = $this->normalizeCssProperty($prop);
                if ($val === '') {
                    // Colon-Setter / remover
                    // Empty value → remove the style (like css('prop', null))
                    unset($this->richStyles[$prop]);
                } else {
                    $this->richStyles[$prop] = $val;
                }
            }

            return $this;
        }
    
        if ($property === null || trim($property) === '') {
            throw new \InvalidArgumentException(
                'CSS property name cannot be empty.'
            );
        }
    
        $property = $this->normalizeCssProperty($property);
    
        /*
         * css('color')
         * → Getter
        */
        if ($arguments === 1) {
            return $this->richStyles[$property] ?? null;
        }
    
        /*
         * css('color', null)
         * → Remove
        */
        if ($value === null) {
            unset($this->richStyles[$property]);
    
            return $this;
        }
    
        /*
         * css('color', 'red')
         * → Setter
        */
        $this->richStyles[$property] = trim((string) $value);
    
        return $this;
    }

    /**
     * Adds one or many CSS classes without duplicates.
     *
     * @param string|array<int|string, mixed> ...$classes
     * @return static
    */
    public function addClass(string|array|Arrayable ...$classes): static
    {
        foreach ($classes as $group) {
            foreach ($this->normalizeClassTokens($group) as $class) {
                $this->richClasses[$class] = true;
            }
        }

        return $this;
    }

    /**
     * Removes one or many CSS classes.
     *
     * Calling removeClass() without arguments clears all classes.
     *
     * @param string|array<int|string, mixed> ...$classes
     * @return static
    */
    public function removeClass(string|array|Arrayable ...$classes): static
    {
        if ($classes === []) {
            $this->richClasses = [];

            return $this;
        }

        foreach ($classes as $group) {
            foreach ($this->normalizeClassTokens($group) as $class) {
                unset($this->richClasses[$class]);
            }
        }

        return $this;
    }

    /**
     * Checks whether the entity contains the requested CSS class(es).
     *
     * Multiple classes use "all-of" semantics:
     *
     * hasClass('foo')        → true|false
     * hasClass('foo bar')    → true only when both exist
     *
     * @param string $class
     * @return bool
    */
    public function hasClass(string $class): bool
    {
        $tokens = $this->normalizeClassTokens($class);

        if ($tokens === []) {
            return false;
        }

        foreach ($tokens as $token) {
            if (!isset($this->richClasses[$token])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Renders the entity's HTML and applies entity-level presentation state.
    */
    protected final function renderedHtml(): string
    {
        return $this->decorateHtml($this->toHtml());
    }

    /**
     * Injects entity-level class/style state into the first HTML element.
     *
     * Existing class/style attributes are preserved and extended.
    */
    protected function decorateHtml(string $html): string
    {
        if ($html === '' || ($this->richClasses === [] && $this->richStyles === [])) {
            return $html;
        }

        $newClass = $this->richClasses === []
            ? null
            : implode(' ', array_keys($this->richClasses));

        $newStyle = $this->richStyles === []
            ? null
            : implode(
                '; ',
                array_map(
                    static fn (string $property, string $value): string
                        => $property . ': ' . $value,
                    array_keys($this->richStyles),
                    array_values($this->richStyles)
                )
            );

        /*
         * Find the first real HTML opening tag while naturally skipping
         * comments / doctype declarations.
         */
        if (!preg_match(
            '~<([a-z][\w:-]*)(?:\s+(?:"[^"]*"|\'[^\']*\'|[^\'">])*)?\s*/?>~i',
            $html,
            $match,
            PREG_OFFSET_CAPTURE
        )) {
            return $html;
        }

        $openingTag = $match[0][0];
        $offset = $match[0][1];

        /*
         * Merge class.
         */
        if ($newClass !== null) {
            $openingTag = preg_replace_callback(
                '~\sclass\s*=\s*(["\'])(.*?)\1~is',
                function (array $match) use ($newClass): string {
                    $existing = trim($match[2]);

                    return ' class="' . $this->esc(
                        trim($existing . ' ' . $newClass)
                    ) . '"';
                },
                $openingTag,
                1,
                $count
            ) ?? $openingTag;

            if ($count === 0) {
                $attributes = 'class="' . $this->esc($newClass) . '"';

                $openingTag = preg_replace(
                    '~(\s*/?>)$~',
                    ' ' . $attributes . '$1',
                    $openingTag,
                    1
                ) ?? $openingTag;
            }
        }

        /*
         * Merge style.
         *
         * New declarations are appended intentionally, so entity-level
         * styles win over earlier inline declarations under normal CSS
         * cascade rules.
         */
        if ($newStyle !== null) {
            $openingTag = preg_replace_callback(
                '~\sstyle\s*=\s*(["\'])(.*?)\1~is',
                function (array $match) use ($newStyle): string {
                    $existing = rtrim(trim($match[2]), " \t\n\r;");

                    $style = $existing !== ''
                        ? $existing . '; ' . $newStyle
                        : $newStyle;

                    return ' style="' . $this->esc($style) . '"';
                },
                $openingTag,
                1,
                $count
            ) ?? $openingTag;

            if ($count === 0) {
                $attributes = 'style="' . $this->esc($newStyle) . '"';

                $openingTag = preg_replace(
                    '~(\s*/?>)$~',
                    ' ' . $attributes . '$1',
                    $openingTag,
                    1
                ) ?? $openingTag;
            }
        }

        return substr($html, 0, $offset)
            . $openingTag
            . substr($html, $offset + strlen($match[0][0]));
    }

    /**
     * Normalizes CSS property names.
     *
     * Supports:
     *   fontSize
     *   font-size
     *   font_size
     *   --my-variable
    */
    protected function normalizeCssProperty(string $property): string
    {
        $property = trim($property);

        if ($property === '') {
            throw new \InvalidArgumentException('CSS property name cannot be empty.');
        }

        // CSS custom properties are case-sensitive.
        if (str_starts_with($property, '--')) {
            return $property;
        }

        $property = str_replace('_', '-', $property);

        $property = preg_replace(
            '/([a-z0-9])([A-Z])/',
            '$1-$2',
            $property
        );

        return strtolower((string) $property);
    }

    /**
     * Converts arbitrary class input into unique whitespace-separated tokens.
     *
     * Supports:
     *   'foo bar baz'
     *   'foo bar, baz'
     *   'foo, bar, baz'
     *   ['foo', 'bar baz']
     *   ['foo', ['bar', 'baz']]
     *
     * @return list<string>
    */
    protected function normalizeClassTokens(string|array|Arrayable $classes): array
    {
        $tokens = [];

        $walk = function (mixed $value) use (&$walk, &$tokens): void {

            // Arrayable → array
            if ($value instanceof Arrayable) {
                $walk($value->toArray());
                return;
            }

            if (is_array($value)) {
                foreach ($value as $item) {
                    $walk($item);
                }

                return;
            }

            if (!is_string($value)) {
                return;
            }

            foreach (preg_split('/\s*[,]\s*|\s+/', trim($value), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $token) {
                $tokens[$token] = true;
            }
        };

        $walk($classes);

        return array_keys($tokens);
    }

    protected static function isAssoc(array $array): bool
    {
        return $array !== []
            && array_keys($array) !== range(0, count($array) - 1);
    }
}
