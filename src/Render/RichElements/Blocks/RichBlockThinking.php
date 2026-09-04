<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;

/**
 * A "Thinking..." placeholder block.
*/
class RichBlockThinking extends RichBlockEntity
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
        $content = $this->renderHtml($this->text);

        if($this->targetsTelegram())
            // Renders the thinking placeholder.
            return '<tg-thinking>' . $content . '</tg-thinking>';

        // Animated dots + collapsible body for AI "thinking" states
        return '<div class="richy-thinking">'
            . '<div class="richy-thinking__header">'
            .   '<span class="richy-thinking__dots">'
            .     '<span class="richy-thinking__dot"></span>'
            .     '<span class="richy-thinking__dot"></span>'
            .     '<span class="richy-thinking__dot"></span>'
            .   '</span>'
            .   '<span>Thinking…</span>'
            . '</div>'
            . '<div class="richy-thinking__content">' . $content . '</div>'
            . '</div>';
    }

    // No Markdown native equivalent; renders as italic blockquote.
    public function toMd(): string
    {
        $inner = $this->renderText($this->text);
        $lines = explode("\n", rtrim($inner));
        $quoted = implode("\n", array_map(fn($l) => '>_' . $l . '_', $lines));
        return $quoted . "\n\n";
    }
}
