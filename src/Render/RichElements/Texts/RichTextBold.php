<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

class RichTextBold extends RichTextEntity
{
    /** @param RichEntity|string|array $text */
    public function __construct(public RichEntity|string|array $text) {}

    /**
     * Static factory to create a new RichTextBold instance.
     *
     * @param RichEntity|callable|string|array $text The content to render as bold.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text): self
    {
        // Resolve the text content, as it might be a closure.
        return new self(self::resolveContent($text));
    }

    public function toArray(): array { return ['type' => 'bold', 'text' => $this->normalize($this->text)]; }
    public function toHtml(): string
    {
        // Renders content within <b> tags.
        return '<b>' . $this->renderHtml($this->text) . '</b>';
    }
    public function toMd()
    {
        return '*' . $this->renderText($this->text) . '*';
    }
}
