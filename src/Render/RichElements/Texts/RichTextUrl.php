<?php
namespace KrubiK\Render\RichElements\Texts;
use KrubiK\Render\RichElements\RichEntity;

class RichTextUrl extends RichTextEntity
{
    /** @param RichEntity|string|array $text */
    public function __construct(public RichEntity|string|array $text, public string $url) {}

    /**
     * Static factory to create a new RichTextUrl instance (hyperlink).
     *
     * @param RichEntity|callable|string|array $text The visible, clickable text.
     * @param string $url The destination URL.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text, string $url): self
    {
        // Resolve the display text, as it might be a closure.
        return new self(self::resolveContent($text), $url);
    }

    public function toArray(): array { return ['type' => 'url', 'text' => $this->normalize($this->text), 'url' => $this->url]; }
    public function toHtml(): string
    {
        // Creates a standard hyperlink. The URL in href is properly escaped.
        $escapedUrl = $this->esc($this->url);
        return '<a href="' . $escapedUrl . '">' . $this->renderHtml($this->text) . '</a>';
    }
    public function toMd()
    {
        return '[' . $this->renderText($this->text) . '](' . $this->url . ')';
    }
}
