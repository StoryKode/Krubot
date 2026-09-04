<?php
namespace KrubiK\Render\RichElements\Blocks;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Represents the definition block for a footnote at the end of a document.
*/
class RichBlockFootnoteDefinition extends RichBlockEntity // Not Telegram Original !
{
    /**
     * @param string $name The unique identifier for the footnote (e.g., "fn:id1").
     * @param RichBlockEntity[] $blocks The content blocks of the footnote.
    */
    public function __construct(public string $name, public array|Arrayable $blocks) {}

    /**
     * Static factory to create a new RichBlockFootnoteDefinition instance.
     *
     * @param string $name The unique name/identifier for the footnote, matching a reference.
     * @param array|Arrayable<RichBlockEntity>|callable $blocks The block content of the footnote.
     * @return self Returns a new instance of the class.
    */
    public static function make(string $name, array|Arrayable|callable $blocks): self
    {
        return new self($name, self::resolveContent($blocks, true));
    }

    public function toArray(): array
    {
        return [
            'type' => 'footnote_definition',
            'name' => $this->name,
            'blocks' => $this->normalize($this->blocks),
        ];
    }

    /**
     * Renders the footnote definition as a <div> block.
     *
     * This block acts as the target for footnote reference links.
     * - The `id` attribute is set to the footnote's name, allowing `href="#fn:id1"` to link here.
     * - `role="doc-endnote"` is added for enhanced accessibility, signaling its purpose to screen readers.
     * - The content (`blocks`) is rendered recursively inside the div.
     *
     * @return string The rendered HTML string.
    */
    public function toHtml(): string
    {
        $escName  = $this->esc($this->name);

        // JS highlights this when its reference-link is clicked.
        $attributes = [
            'id' => 'fn-' . $escName,
            'class' => 'richy-footnote-def',
            'role' => 'doc-endnote', // Good for accessibility
            'data-richy-footnote' => $escName,
        ];

        $attrString = $this->attributesToString($attributes);
        
        // The content of a footnote can itself be complex, so we render the blocks recursively.
        $renderedBlocks = $this->renderHtml($this->blocks);
        
        return "<div {$attrString}><span class=\"richy-footnote-def__name\">[{$escName}]</span>{$renderedBlocks}</div>";
    }

    // TG 10.x API:  [^id]: Definition text
    public function toMd(): string
    {
        return '[^' . $this->escForMd($this->name) . ']: ' . $this->mergeTexts($this->blocks) . "\n";
    }
}