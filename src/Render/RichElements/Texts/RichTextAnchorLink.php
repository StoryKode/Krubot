<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

readonly class RichTextAnchorLink extends RichTextEntity
{
    public function __construct(public RichEntity|string|array $text, public string $anchor_name) {}
    /**
     * Static factory to create a new RichTextAnchorLink instance.
     *
     * @param RichEntity|callable|string|array $text The visible, clickable text.
     * @param string $anchorName The name of the anchor to link to.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text, string $anchorName): self
    {
        // Resolve the display text, as it might be a closure.
        return new self(self::resolveContent($text), $anchorName);
    }
    public function toArray(): array { return ['type' => 'anchor_link', 'text' => $this->normalize($this->text), 'anchor_name' => $this->anchor_name]; }
    public function toHtml(): string
    {
        // Creates a link to an anchor within the same document.
        $escapedAnchorName = $this->esc($this->anchor_name);
        return '<a href="#' . $escapedAnchorName . '">' . $this->renderHtml($this->text) . '</a>';
    }
}
