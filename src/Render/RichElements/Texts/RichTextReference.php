<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

class RichTextReference extends RichTextEntity
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
        $escapedName = $this->esc($this->name);

        if($this->targetsTelegram()) // Renders a custom <tg-reference> tag.
            return '<tg-reference name="' . $escapedName . '">' . $this->renderHtml($this->text) . '</tg-reference>';

        // Superscript that points to a RichBlockFootnoteDefinition.
        return '<sup class="richy-reference" data-richy-ref="' . $escapedName
            . '" title="Reference: ' . $escapedName . '" tabindex="0" role="button">'
            . $this->renderHtml($this->text)
            . '</sup>';
    }

    public function toMd(): string
    {
        /// [^name]  TG 10.1 inline reference
        return '[^' . $this->escForMd($this->name) . ']';
    }
}
