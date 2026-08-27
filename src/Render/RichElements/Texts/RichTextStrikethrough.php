<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

readonly class RichTextStrikethrough extends RichTextEntity
{
    /** @param RichEntity|string|array $text */
    public function __construct(public RichEntity|string|array $text) {}

    /**
     * Static factory to create a new RichTextStrikethrough instance.
     *
     * @param RichEntity|callable|string|array $text The content to strike through.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text): self
    {
        // Resolve the text content, as it might be a closure.
        return new self(self::resolveContent($text));
    }

    public function toArray(): array { return ['type' => 'strikethrough', 'text' => $this->normalize($this->text)]; }
    public function toHtml(): string
    {
        // Renders content within <s> tags.
        return '<s>' . $this->renderHtml($this->text) . '</s>';
    }
    public function toMd()
    {
        return '~' . $this->renderText($this->text) . '~';
    }
}
