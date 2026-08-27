<?php
namespace KrubiK\Render\RichElements\Blocks;
use KrubiK\Render\RichElements\RichEntity;

readonly class RichBlockPreformatted extends RichBlockEntity
{
    public function __construct(public RichEntity|string|array $text, public ?string $language = null) {}

    /**
     * Static factory for a RichBlockPreformatted instance (pre-formatted code block).
     *
     * @param RichEntity|callable|string|array $text The content of the pre-formatted block.
     * @param string|null $language The programming language for syntax highlighting.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text, ?string $language = null): self
    {
        // Resolve the text content, as it might be a closure.
        return new self(self::resolveContent($text), $language);
    }

    public function toArray(): array { return $this->filterEmpty(['type' => 'pre', 'text' => $this->normalize($this->text), 'language' => $this->language]); }
    public function toHtml(): string
    {
        // Renders a pre-formatted code block.
        // If a language is specified, it's added as a class for syntax highlighting.
        $langAttr = $this->language ? ' class="language-' . $this->esc($this->language) . '"' : '';
        // The content within a pre/code block should be escaped to be displayed literally.
        $renderedText = $this->renderHtml($this->text); // Render first to handle nested entities, then escape the result.
        return '<pre><code' . $langAttr . '>' . $this->esc($renderedText) . '</code></pre>';
    }
    public function toMd()
    {
        return "
    ```" . $this->language . "\n" . $this->text . "\n" . "
    ```";
    }
}
