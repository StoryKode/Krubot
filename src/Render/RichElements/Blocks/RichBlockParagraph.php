<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;

class RichBlockParagraph extends RichBlockEntity
{
    public function __construct(public RichEntity|string|array $text) {}
    /**
     * Static factory to create a new RichBlockParagraph instance.
     *
     * @param RichEntity|callable|string|array $text The paragraph content.
     * @return self Returns a new instance of the class.
    */
    public static function make(RichEntity|callable|string|array $text): self
    {
        $resolvedText = self::resolveContent($text); // Resolve the paragraph text as it can be built via a closure.
        return new self($resolvedText);
    }

    public function toArray(): array { return ['type' => 'paragraph', 'text' => $this->normalize($this->text)]; }
    public function toHtml(): string
    {
        // Wraps the text content in a <p> tag.
        return '<p>' . $this->renderHtml($this->text) . '</p>';
    }
    public function toMd()
    {
        return $this->renderText($this->text) . "\n\n";
    }
}

