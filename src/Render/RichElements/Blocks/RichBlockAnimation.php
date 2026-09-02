<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\DTOs\Animation as AnimationDTO;
use KrubiK\Render\RichElements\Components\RichBlockCaption;

class RichBlockAnimation extends RichBlockEntity
{
    public function __construct(public AnimationDTO|array $animation, public ?bool $has_spoiler = null, public RichBlockCaption|RichEntity|string|null $caption = null) {}
    /**
     * Static factory to create a new RichBlockAnimation instance.
     *
     * @param AnimationDTO|array $animation The Animation DTO or array.
     * @param bool|null $hasSpoiler Whether the animation is hidden by a spoiler.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return self Returns a new instance of the class.
    */
    public static function make(AnimationDTO|array $animation, ?bool $hasSpoiler = null, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self($animation, $hasSpoiler, $resolvedCaption);
    }

    public function toArray(): array { return $this->filterEmpty(['type' => 'animation', 'animation' => $this->normalize($this->animation), 'has_spoiler' => $this->has_spoiler, 'caption' => $this->normalizeCaption($this->caption)]); }
}
