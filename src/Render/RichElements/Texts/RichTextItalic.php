<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

class RichTextItalic extends RichTextEntity
{
    /** @param RichEntity|string|array $text */
    public function __construct(public RichEntity|string|array $text) {}

    /**
     * Static factory to create a new RichTextItalic instance.
     *
     * @param RichEntity|callable|string|array $text The content to render as italic.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text): self
    {
        // Resolve the text content, as it might be a closure.
        return new self(self::resolveContent($text));
    }

    public function toArray(): array { return ['type' => 'italic', 'text' => $this->normalize($this->text)]; }
    public function toHtml(): string
    {
        // Renders content within <i> tags.
        return '<i>' . $this->renderHtml($this->text) . '</i>';
    }
    public function toMd()
    {
        return '_' . $this->renderText($this->text) . '_';
    }
}