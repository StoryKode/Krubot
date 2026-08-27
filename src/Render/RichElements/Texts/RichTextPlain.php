<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

readonly class RichTextPlain extends RichTextEntity
{
    public function __construct(public string $text) {}

    /**
     * Static factory for a RichTextPlain instance.
     *
     * @param string $text The plain text content.
     * @return self
    */
    public static function make(string $text): self { return new self($text); }
    public function toArray(): array { return ['type' => 'plain', 'text' => $this->text]; }

    /**
     * Renders plain text by simply escaping it.
     *
     * This is the base case for the recursive renderer. It does not produce any HTML tags,
     * only the character data of the text. The central `e()` helper is used to prevent
     * Cross-Site Scripting (XSS) vulnerabilities by converting special HTML characters.
     *
     * @return string The HTML-escaped text content.
    */
    public function toHtml(): string
    {
        // Use the centralized escaping function from the trait.
        return $this->esc($this->text);
    }
}
