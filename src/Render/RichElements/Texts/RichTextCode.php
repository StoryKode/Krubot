<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

readonly class RichTextCode extends RichTextEntity
{
    /** @param RichElementEntity|string|array $text */
    public function __construct(public RichElementEntity|string|array $text) {}

    /**
     * Static factory to create a new RichTextCode instance.
     *
     * @param RichEntity|callable|string|array $text The inline code snippet.
     * @return self
     */
    public static function make(RichEntity|callable|string|array $text): self
    {
        // Resolve the text content, as it might be a closure.
        return new self(self::resolveContent($text));
    }

    public function toArray(): array {
        return ['type' => 'code', 'text' => $this->normalize($this->text)];
    }
    public function toHtml(): string
    {
        // Renders content within <code> tags. Note that the content is also escaped.
        return '<code>' . $this->renderHtml($this->text) . '</code>';
    }
    public function toMd()
    {
        return '`' . $this->renderText($this->text) . '`';
    }
}
