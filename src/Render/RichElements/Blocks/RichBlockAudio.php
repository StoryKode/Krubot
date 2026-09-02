<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\DTOs\Audio as AudioDTO;
use KrubiK\Render\RichElements\Components\RichBlockCaption;

class RichBlockAudio extends RichBlockEntity
{
    public function __construct(public AudioDTO|array $audio, public RichBlockCaption|RichEntity|string|null $caption = null) {}
    /**
     * Static factory to create a new RichBlockAudio instance.
     *
     * @param AudioDTO|array $audio The Audio DTO or array.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return self Returns a new instance of the class.
     */
    public static function make(AudioDTO|array $audio, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self($audio, $resolvedCaption);
    }
    public function toArray(): array { return $this->filterEmpty(['type' => 'audio', 'audio' => $this->normalize($this->audio), 'caption' => $this->normalizeCaption($this->caption)]); }
}
