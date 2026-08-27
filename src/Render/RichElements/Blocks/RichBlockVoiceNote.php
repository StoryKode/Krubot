<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\DTOs\Voice as VoiceDTO;
use KrubiK\Render\RichElements\Components\RichBlockCaption;

/**
 * A block containing a voice note.
*/
readonly class RichBlockVoiceNote extends RichBlockEntity
{
    /**
     * @param array $voice_note The voice note object, expected as an array.
     * @param RichBlockCaption|null $caption Optional caption.
     */
    public function __construct(public VoiceDTO|array $voice_note, public RichBlockCaption|RichEntity|string|null $caption = null) {}

    /**
     * Static factory to create a new RichBlockVoiceNote instance.
     *
     * @param VoiceDTO|array $voiceNote The Voice DTO or array.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return self Returns a new instance of the class.
    */
    public static function make(VoiceDTO|array $voiceNote, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self($voiceNote, $resolvedCaption);
    }
    public function toArray(): array { return $this->filterEmpty(['type' => 'voice_note', 'voice_note' => $this->normalize($this->voice_note), 'caption' => $this->normalizeCaption($this->caption)]); }
}
