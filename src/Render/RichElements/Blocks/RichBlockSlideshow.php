<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use Illuminate\Contracts\Support\Arrayable;
use KrubiK\Render\RichElements\Components\RichBlockCaption;

/**
 * A slideshow of other blocks.
*/
class RichBlockSlideshow extends RichBlockEntity
{
    /**
     * @param RichBlockEntity[] $blocks Elements of the slideshow.
     * @param RichBlockCaption|null $caption Optional caption.
    */
    public function __construct(public array|Arrayable $blocks, public RichBlockCaption|RichEntity|string|null $caption = null) {}

    /**
     * Static factory to create a new RichBlockSlideshow instance.
     *
     * @param array|Arrayable $blocks An array of photo or video blocks.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption for the entire slideshow.
     * @return self Returns a new instance of the class.
    */
    public static function make(array|Arrayable|callable $blocks, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self(self::resolveContent($blocks, true), $resolvedCaption);
    }

    public function toArray(): array { return $this->filterEmpty(['type' => 'slideshow', 'blocks' => $this->normalize($this->blocks), 'caption' => $this->normalizeCaption($this->caption)]); }

    public function toHtml(): string
    {
        if($this->targetsTelegram()) {
            // Renders a custom <tg-slideshow> containing other blocks.
            $html = '<tg-slideshow>';
            $html .= $this->renderBlocks($this->blocks);
            
            if ($this->caption) {
                $html .= '<figcaption>' . $this->caption->toHtml() . '</figcaption>';
            }
            
            $html .= '</tg-slideshow>';
            return $html;
        }

        // Full touch/keyboard/dot navigation via krubot-web-render.js
        $blocks = $this->blocks instanceof Arrayable
            ? $this->blocks->toArray()
            : (array)$this->blocks;

        $count = count($blocks);

        $slidesHtml = '';
        foreach ($blocks as $block) {
            $slidesHtml .= '<div class="richy-slideshow__slide">'
                . $this->renderHtml($block)
                . '</div>';
        }

        $dotsHtml = '';
        for ($i = 0; $i < $count; $i++) {
            $active     = $i === 0 ? ' richy-slideshow__dot--active' : '';
            $dotsHtml  .= '<button class="richy-slideshow__dot' . $active . '"'
                . ' aria-label="Slide ' . ($i + 1) . '"></button>';
        }

        $svgLeft  = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        $svgRight = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        $captionHtml = $this->caption !== null
            ? '<div class="richy-slideshow__caption">' . $this->renderHtml($this->caption) . '</div>'
            : '';

        return '<div class="richy-slideshow">'
            . '<div class="richy-slideshow__counter">1 / ' . $count . '</div>'
            . '<div class="richy-slideshow__track">' . $slidesHtml . '</div>'
            . '<button class="richy-slideshow__arrow richy-slideshow__arrow--prev" aria-label="Previous slide">' . $svgLeft . '</button>'
            . '<button class="richy-slideshow__arrow richy-slideshow__arrow--next" aria-label="Next slide">' . $svgRight . '</button>'
            . '<div class="richy-slideshow__dots">' . $dotsHtml . '</div>'
            . $captionHtml
            . '</div>';
    }

    // <tg-slideshow>\n![](url)\n![](url)\n</tg-slideshow>
    public function toMd(): string
    {
        $inner   = $this->mergeTexts($this->blocks);
        $caption = $this->caption !== null ? "\n" . $this->renderText($this->caption) : '';

        return "<tg-slideshow>\n\n" . $inner . "\n</tg-slideshow>" . $caption . "\n\n";
    }
}
