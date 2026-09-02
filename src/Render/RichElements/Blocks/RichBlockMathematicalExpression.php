<?php

namespace KrubiK\Render\RichElements\Blocks;

class RichBlockMathematicalExpression extends RichBlockEntity
{
    public function __construct(public string $expression) {}

    /**
     * Static factory for a RichBlockMathematicalExpression instance.
     *
     * @param string $expression The mathematical expression as a string.
     * @return self
    */
    public static function make(string $expression): self
    {
        // Expression is expected to be a string, no resolving needed.
        return new self($expression);
    }

    public function toArray(): array { return ['type' => 'mathematical_expression', 'expression' => $this->expression]; }

    /**
     * Projects the mathematical expression into the current Realm's DOM/Markup.
     */
    public function toHtml(): string
    {
        $escapedExpression = $this->esc($this->expression);

        // Realm: Telegram - Render via native <tg-math-block>
        if ($this->targetsTelegram()) {
        // Renders a block-level LaTeX expression. Content should be escaped.
            return "<tg-math-block>{$escapedExpression}</tg-math-block>";
        }

        // Realm: Web - Render semantic container optimized for KaTeX/MathJax interception
        if ($this->targetsWeb()) {
            return "<div class=\"kr-math-block kr-latex\" data-expr=\"{$escapedExpression}\">"
                 . "\\[ {$escapedExpression} \\]"
                 . "</div>";
        }

        // Realm: CLI or Unknown Fallback
        return $escapedExpression;
    }

    public function toText(): string
    {
        // The most logical plain text representation is the raw expression itself.
        return $this->expression;
    }
}
