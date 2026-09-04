<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

class RichTextMarked extends RichTextEntity
{
    public function __construct(public RichEntity|string|array $text) {}

    /**
     * Static factory to create a new RichTextMarked instance for highlighted text.
     *
     * @param RichEntity|callable|string|array $text The content to be marked/highlighted.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text): self
    {
        // Resolve the text content, as it might be a closure.
        return new self(self::resolveContent($text));
    }

    public function toArray(): array { return ['type' => 'marked', 'text' => $this->normalize($this->text)]; }

    public function toHtml(): string
    {
        // Renders content within <mark> tags.
        return '<mark class="richy-marked">' . $this->renderHtml($this->text) . '</mark>';
    }

    public function toMd(): string
    {
        return '==' . $this->renderText($this->text) . '==';
    }
}
