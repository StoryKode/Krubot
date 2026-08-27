<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;

/**
 * A "Thinking..." placeholder block.
*/
readonly class RichBlockThinking extends RichBlockEntity
{
    /** @param RichEntity|string|array $text */
    public function __construct(public RichEntity|string|array $text) {}

    /**
     * Static factory to create a new RichBlockThinking instance.
     * Typically used to show a "bot is typing..." indicator.
     *
     * @param RichEntity|callable|string|array $text The text to display, often empty or a placeholder.
     * @return self Returns a new instance of the class.
    */
    public static function make(RichEntity|callable|string|array $text): self
    {
        $resolvedText = self::resolveContent($text); // Resolve the thinking text if it's a callable closure.
        return new self($resolvedText);
    }

    public function toArray(): array { return ['type' => 'thinking', 'text' => $this->normalize($this->text)]; }
    public function toHtml(): string
    {
        // Renders the thinking placeholder.
        return '<tg-thinking>' . $this->renderHtml($this->text) . '</tg-thinking>';
    }
}
