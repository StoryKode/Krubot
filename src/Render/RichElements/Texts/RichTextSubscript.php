<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

class RichTextSubscript extends RichTextEntity
{
    /** @param RichEntity|string|array $text */
    public function __construct(public RichEntity|string|array $text) {}

    /**
     * Static factory to create a new RichTextSubscript instance.
     *
     * @param RichEntity|callable|string|array $text The content to be subscripted.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text): self
    {
        // Resolve the text content, as it might be a closure.
        return new self(self::resolveContent($text));
    }

    public function toArray(): array { return ['type' => 'subscript', 'text' => $this->normalize($this->text)]; }
    public function toHtml(): string
    {
        // Renders content within <sub> tags.
        return '<sub>' . $this->renderHtml($this->text) . '</sub>';
    }
}
