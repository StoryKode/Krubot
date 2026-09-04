<?php
namespace KrubiK\Render\RichElements\Texts;

class RichTextMathematicalExpression extends RichTextEntity
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

    // KaTeX auto-render picks up .richy-math if loaded on the page.
    // Falls back gracefully to <code>.
    public function toHtml(): string
    {
        // Content is escaped to be displayed literally.
        $safeExpr = $this->esc($this->expression);

        if($this->targetsTelegram()) // Renders a custom <tg-math> tag. 
            return '<tg-math>' . $this->esc($this->expression) . '</tg-math>';

        return '<span class="richy-math" data-richy-math="' . $safeExpr . '">'
            . '<code>' . $safeExpr . '</code>'
            . '</span>';
    }

    public function toMd(): string
    {
        // TG 10.2: inline math uses single $...$
        return '$' . $this->expression . '$';
    }
}
