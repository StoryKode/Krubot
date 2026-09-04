<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

class RichTextPre extends RichTextEntity
{
    /** @param RichEntity|string|array $text */
    public function __construct(public RichEntity|string|array $text, public ?string $language = null) {}

    /**
     * Static factory for a RichTextPre instance (pre-formatted code block).
     *
     * @param RichEntity|callable|string|array $text The content of the pre-formatted block.
     * @param string|null $language The programming language for syntax highlighting.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text, ?string $language = null): self
    {
        // Resolve the text content, as it might be a closure.
        return new self(self::resolveContent($text), $language);
    }

    public function toArray(): array {
        return $this->filterEmpty(['type' => 'pre', 'text' => $this->normalize($this->text), 'language' => $this->language]);
    }

    public function toHtml(): string
    {
        // Renders a pre-formatted code block.
        $lang      = $this->language ?? null;
        
        if($this->targetsTelegram()) {
            // If a language is specified, it's added as a class for syntax highlighting.
            $langAttr = $lang ? ' class="language-' . $this->esc($lang) . '"' : '';
            // The content within a pre/code block should be escaped to be displayed literally.
            $renderedText = $this->renderHtml($this->text); // Render first to handle nested entities, then escape the result.
        
            return '<pre><code' . $langAttr . '>' . $this->esc($renderedText) . '</code></pre>';
        }

        $langBadge = $lang
            ? '<span class="richy-pre__lang-badge">' . $this->esc($lang) . '</span>'
            : '';

        // For pre, innerContent should be treated as literal code text, not HTML
        $codeText = $this->renderPlainText($this->text);

        // Copy-to-clipboard button injected by krubot-web-render.js
        return '<div class="richy-pre">'
            . $langBadge
            . '<code>' . $this->esc($codeText) . '</code>'
            . '</div>';

    }

    public function toMd(): string
    {
        $lang = $this->language ?? '';
        $code = $this->renderPlainText($this->text);
        // Inside code block, escape only backtick and backslash.
        $safeCode = str_replace(['\\', '`'], ['\\\\', '\\`'], $code);
        return "```{$lang}\n{$safeCode}\n```\n"; // ```lang\ncode\n```
    }
}