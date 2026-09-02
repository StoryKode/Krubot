<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\DTOs\Video as VideoDTO;
use KrubiK\Render\RichElements\Components\RichBlockCaption;

class RichBlockVideo extends RichBlockEntity
{
    public function __construct(public VideoDTO|array $video, public ?bool $has_spoiler = null, public RichBlockCaption|RichEntity|string|null $caption = null) {}

    /**
     * Static factory to create a new RichBlockVideo instance.
     *
     * @param VideoDTO|array $video The Video DTO or array.
     * @param bool|null $hasSpoiler Whether the video is hidden by a spoiler.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return self Returns a new instance of the class.
    */
    public static function make(VideoDTO|array $video, ?bool $hasSpoiler = null, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self($video, $hasSpoiler, $resolvedCaption);
    }

    public function toArray(): array { return $this->filterEmpty(['type' => 'video', 'video' => $this->normalize($this->video), 'has_spoiler' => $this->has_spoiler, 'caption' => $this->normalizeCaption($this->caption)]); }
}
