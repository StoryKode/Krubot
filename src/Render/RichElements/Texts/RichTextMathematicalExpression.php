<?php
namespace KrubiK\Render\RichElements\Texts;

readonly class RichTextMathematicalExpression extends RichTextEntity
{
    public function __construct(public string $expression) {}

    /**
     * Static factory for a RichTextMathematicalExpression instance.
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
    public function toHtml(): string
    {
        // Renders a custom <tg-math> tag. Content is escaped to be displayed literally.
        return '<tg-math>' . $this->esc($this->expression) . '</tg-math>';
    }
}
