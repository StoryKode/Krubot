<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

class RichTextSpoiler extends RichTextEntity
{
    /** @param RichEntity|string|array $text */
    public function __construct(public RichEntity|string|array $text) {}

    /**
     * Static factory to create a new RichTextSpoiler instance.
     *
     * @param RichEntity|callable|string|array $text The content to be concealed.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text): self
    {
        // Resolve the text content, as it might be a closure.
        return new self(self::resolveContent($text));
    }

    public function toArray(): array { return ['type' => 'spoiler', 'text' => $this->normalize($this->text)]; }
    
    // Revealed by JS click → adds .richy-spoiler--revealed
    public function toHtml(): string
    {
        $content = $this->renderHtml($this->text);

        if($this->targetsWeb())
            return '<span class="richy-spoiler" title="Click to reveal" role="button" tabindex="0" aria-label="Spoiler — click to reveal">'
            . $content
            . '</span>';

        // Renders content within a custom <tg-spoiler> tag.
        return '<tg-spoiler>' . $content . '</tg-spoiler>';
    }

    public function toMd()
    {
        return '||' . $this->renderText($this->text) . '||';
    }
}