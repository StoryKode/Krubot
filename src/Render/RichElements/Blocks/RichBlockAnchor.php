<?php

namespace KrubiK\Render\RichElements\Blocks;

class RichBlockAnchor extends RichBlockEntity
{
    public function __construct(public string $name) {}
    public static function make(string $name): self { return new self($name); }
    public function toArray(): array { return ['type' => 'anchor', 'name' => $this->name]; }
    public function toHtml(): string
    {
        // Creates an empty anchor tag with a name attribute. Used as a target for links.
        $escapedName = $this->esc($this->name);
        return '<a name="' . $escapedName . '"></a>';
    }
}
