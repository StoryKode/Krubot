<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use Illuminate\Contracts\Support\Arrayable;

class RichBlockCollage extends RichBlockEntity
{
    /** @param RichBlockEntity[] $blocks */
    public function __construct(public array|Arrayable $blocks, public RichBlockCaption|RichEntity|string|null $caption = null) {}
    /**
     * Static factory to create a new RichBlockCollage instance.
     *
     * @param array|Arrayable|callable $blocks An array of photo or video blocks.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption for the entire collage.
     * @return self Returns a new instance of the class.
    */
    public static function make(array|Arrayable|callable $blocks, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self(self::resolveContent($blocks, true), $resolvedCaption);
    }
    public function toArray(): array { return $this->filterEmpty(['type' => 'collage', 'blocks' => $this->normalize($this->blocks), 'caption' => $this->normalizeCaption($this->caption)]); }

    public function toHtml(): string
    {
        if($this->targetsTelegram()) {
            // Renders a custom <tg-collage> containing other blocks.
            $html = '<tg-collage>';
            $html .= $this->renderBlocks($this->blocks);
            
            if ($this->caption) {
                $html .= '<figcaption>' . $this->renderHtml($this->caption) . '</figcaption>'; // $this->caption->toHtml()
            }
            
            $html .= '</tg-collage>';
            return $html;
        }

        // JS hover zoom is in CSS. Grid layout adapts to item count.
        $blocks     = $this->blocks;
        $count      = (is_array($blocks) || $blocks instanceof \Countable) ? count($blocks) : collect($blocks)->count();
        $countClass = ' richy-collage--' . min($count, 5);

        $itemsHtml = '';
        foreach ($blocks as $block) {
            $itemsHtml .= '<div class="richy-collage__item">'
                . $this->renderHtml($block)
                . '</div>';
        }

        $captionHtml = $this->caption !== null
            ? $this->renderHtml($this->caption)
            : '';

        return '<div class="richy-collage' . $countClass . '">'
            . $itemsHtml
            . '</div>'
            . ($captionHtml ? '<div class="richy-caption">' . $captionHtml . '</div>' : '');
    }

    // TG 10.x API:  <tg-collage>\n![](url)\n![](url)\n</tg-collage>
    public function toMd(): string
    {
        $inner   = $this->mergeTexts($this->blocks);

        $caption = $this->caption !== null ? "\n" . $this->renderText($this->caption) : '';

        return "<tg-collage>\n\n" . $inner . "\n</tg-collage>" . $caption . "\n\n";
    }
}
