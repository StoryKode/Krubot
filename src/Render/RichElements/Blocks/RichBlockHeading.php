<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;

class RichBlockHeading extends RichBlockEntity
{
    public function __construct(public RichEntity|string|array $text, public int $size = 3) {}
    /**
     * Static factory to create a new RichBlockHeading instance.
     *
     * @param RichEntity|callable|string|array $text The heading text.
     * @param int $size The heading level (e.g., 1 for <h1>, 2 for <h2>).
     * @return self Returns a new instance of the class.
    */
    public static function make(RichEntity|callable|string|array $text, int $size = 3): self
    {
        $resolvedText = self::resolveContent($text); // Resolve the heading text as it can be built via a closure.
        return new self($resolvedText, $size);
    }
    public function toArray(): array { return ['type' => 'heading', 'text' => $this->normalize($this->text), 'size' => $this->size]; }
    public function toHtml(): string
    {
        // Generates a heading tag from <h1> to <h6> based on the size property.
        // We ensure size is within the valid 1-6 range.
        $level = max(1, min(6, $this->size));
        return "<h{$level} class=\"richy-heading richy-heading-{$level}\">" . $this->renderHtml($this->text) . "</h{$level}>";
    }

    public function toMd()
    {
        // TG-MD 10.x:  # Heading 1  ## Heading 2  … ###### Heading 6
        $level  = max(1, min(6, (int)$this->size));
        return str_repeat('#', $level) . ' ' . $this->renderText($this->text) . "\n\n";
    }
}
