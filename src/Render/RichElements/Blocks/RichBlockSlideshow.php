<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use Illuminate\Contracts\Support\Arrayable;
use KrubiK\Render\RichElements\Components\RichBlockCaption;

/**
 * A slideshow of other blocks.
*/
readonly class RichBlockSlideshow extends RichBlockEntity
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
    public static function make(array|Arrayable $blocks, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self($blocks, $resolvedCaption);
    }

    public function toArray(): array { return $this->filterEmpty(['type' => 'slideshow', 'blocks' => $this->normalize($this->blocks), 'caption' => $this->normalizeCaption($this->caption)]); }
    public function toHtml(): string
    {
        // Renders a custom <tg-slideshow> containing other blocks.
        $html = '<tg-slideshow>';
        $html .= $this->renderBlocks($this->blocks);
        
        if ($this->caption) {
            $html .= '<figcaption>' . $this->caption->toHtml() . '</figcaption>';
        }
        
        $html .= '</tg-slideshow>';
        return $html;
    }
}
