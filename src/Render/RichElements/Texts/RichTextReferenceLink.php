<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

class RichTextReferenceLink extends RichTextEntity
{
    public function __construct(public RichEntity|string|array $text, public string $reference_name) {}

    /**
     * Static factory to create a new RichTextReferenceLink instance.
     *
     * @param RichEntity|callable|string|array $text The visible, clickable text.
     * @param string $referenceName The name of the reference to link to.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text, string $referenceName): self
    {
        // Resolve the display text, as it might be a closure.
        return new self(self::resolveContent($text), $referenceName);
    }

    public function toArray(): array { return ['type' => 'reference_link', 'text' => $this->normalize($this->text), 'reference_name' => $this->reference_name]; }
    public function toHtml(): string
    {
        // Creates a link to a <tg-reference> element. This is functionally similar to an anchor link.
        $escapedRefName = $this->esc($this->reference_name);
        return '<a href="#' . $escapedRefName . '">' . $this->renderHtml($this->text) . '</a>';
    }
}
