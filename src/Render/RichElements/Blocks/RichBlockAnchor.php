<?php

namespace KrubiK\Render\RichElements\Blocks;

class RichBlockAnchor extends RichBlockEntity
{
    public function __construct(public string $name) {}
    public static function make(string $name): self { return new self($name); }
    public function toArray(): array { return ['type' => 'anchor', 'name' => $this->name]; }
    public function toHtml(): string
    {
        // Creates an Invisible/Empty anchor tag with a name attribute. Used as a target for in-page anchor links.
        $escapedName = $this->esc($this->name);
        return $this->targetsWeb() ?
        (
            '<span class="richy-anchor" id="' . $escapedName .
            '" data-richy-anchor-name="' . $escapedName . '" aria-hidden="true"></span>'
        ) : (
            '<a name="' . $escapedName . '"></a>'
        );
    }

    // MD has no in-page anchors; renders as empty string.
    public function toMd(): string
    {
        return '';
    }
}
