<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

readonly class RichTextReference extends RichTextEntity
{
    public function __construct(public RichEntity|string|array $text, public string $name) {}

    /**
     * Static factory to create a new RichTextReference instance.
     *
     * @param RichEntity|callable|string|array $text The visible text of the reference.
     * @param string $name The unique name of the item being referenced.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text, string $name): self
    {
        // Resolve the display text, as it might be a closure.
        return new self(self::resolveContent($text), $name);
    }

    public function toArray(): array { return ['type' => 'reference', 'text' => $this->normalize($this->text), 'name' => $this->name]; }
    public function toHtml(): string
    {
        // Renders a custom <tg-reference> tag.
        $escapedName = $this->esc($this->name);
        return '<tg-reference name="' . $escapedName . '">' . $this->renderHtml($this->text) . '</tg-reference>';
    }
}
