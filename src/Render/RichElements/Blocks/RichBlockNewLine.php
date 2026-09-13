<?php

namespace KrubiK\Render\RichElements\Blocks;

class RichBlockNewLine extends RichBlockEntity
{
    protected static bool $tgNative = false;

    public function __construct() {}
    public static function make(): self { return new self(); }
    public function toArray(): array { return ['type' => 'new_line']; }

    public function toHtml(): string
    {
        // Renders a new line html tag.
        return $this->targetsWeb() ? '<br class="richy-newline"/>' : "\n";
    }

    public function toMd(): string
    {
        return "\n";
    }

    // will be used if rich_render_fallback == 'text'
    public function toText(): string
    {
       return "\n";
    }
}
