<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;

class RichBlockFooter extends RichBlockEntity
{
    public function __construct(public RichEntity|string|array $text) {}
    /**
     * Static factory to create a new RichBlockFooter instance.
     *
     * @param RichEntity|callable|string|array $text The content of the footer.
     * @return self Returns a new instance of the class.
    */
    public static function make(RichEntity|callable|string|array $text): self
    {
        $resolvedText = self::resolveContent($text); // Resolve the footer text as it can be built via a closure.
        return new self($resolvedText);
    }
    public function toArray(): array { return ['type' => 'footer', 'text' => $this->normalize($this->text)]; }
    public function toHtml(): string
    {
        // Wraps the text content in a <footer> tag.
        return '<footer class="richy-footer">' . $this->renderHtml($this->text) . '</footer>';
    }

    // separator + italic text (cause no native footer in MD)
    public function toMd(): string
    {
        return "\n\n---\n_" . $this->renderText($this->text) . "_\n";
    }
}
