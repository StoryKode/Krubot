<?php
namespace KrubiK\Render\RichElements\Blocks;

readonly class RichBlockDivider extends RichBlockEntity
{
    public function __construct() {}
    /**
     * Static factory to create a new RichBlockDivider instance.
     * Represents a thematic break (<hr>).
     *
     * @return self Returns a new instance of the class.
    */
    public static function make(): self { return new self(); }
    public function toArray(): array { return ['type' => 'divider']; }
    public function toHtml(): string
    {
        // Renders a horizontal rule.
        return '<hr/>';
    }
    public function toMd()
    {
        return '---';
    }
}
