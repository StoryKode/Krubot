<?php

namespace KrubiK\Render\RichElements\Blocks;

readonly class RichBlockNewLine extends RichBlockEntity
{
    public function __construct() {}
    public static function make(): self { return new self(); }
    public function toArray(): array { return ['type' => 'new_line']; }
    public function toHtml(): string
    {
        // Renders a new line html tag.
        return '<br/>';
    }
}
