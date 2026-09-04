<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\DTOs\Voice as VoiceDTO;
use KrubiK\Render\RichElements\Components\RichBlockCaption;

/**
 * A block containing a voice note.
*/
class RichBlockVoiceNote extends RichBlockEntity
{
    /**
     * @param array $voice_note The voice note object, expected as an array.
     * @param RichBlockCaption|null $caption Optional caption.
     */
    public function __construct(public VoiceDTO|array $voice_note, public RichBlockCaption|RichEntity|string|null $caption = null) {}

    /**
     * Static factory to create a new RichBlockVoiceNote instance.
     *
     * @param VoiceDTO|callable|array $voiceNote The Voice DTO or array.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return self Returns a new instance of the class.
    */
    public static function make(VoiceDTO|callable|array $voiceNote, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self(self::resolveContent($voiceNote, true), $resolvedCaption);
    }

    public function toArray(): array { return $this->filterEmpty(['type' => 'voice_note', 'voice_note' => $this->normalize($this->voice_note), 'caption' => $this->normalizeCaption($this->caption)]); }

    public function toHtml(): string
    {
        $v        = $this->voiceNote instanceof VoiceDTO
            ? $this->voiceNote->toArray()
            : (array)$this->voiceNote;

        $src      = $this->esc($v['url'] ?? '');
        $fileId   = $this->esc($v['file_id'] ?? '');
        $duration = (int)($v['duration'] ?? 0);
        $mime     = $this->esc($v['mime_type'] ?? 'audio/ogg');

        $durationStr = $duration > 0
            ? sprintf('%d:%02d', intdiv($duration, 60), $duration % 60)
            : '';

        $playerHtml = $src
            ? '<audio controls preload="none" data-richy-file-id="' . $fileId . '">'
              . '<source src="' . $src . '" type="' . $mime . '">'
              . '</audio>'
            : '<div class="richy-media-placeholder" data-richy-file-id="' . $fileId . '">🎤 Voice</div>';

        $captionHtml = $this->caption !== null
            ? $this->renderHtml($this->caption)
            : '';

        return '<div class="richy-voice-note">'
            . '<span class="richy-voice-note__icon" aria-hidden="true">🎤</span>'
            . '<div class="richy-voice-note__player">' . $playerHtml . '</div>'
            . ($durationStr
                ? '<span class="richy-voice-note__duration">' . $this->esc($durationStr) . '</span>'
                : '')
            . '</div>'
            . ($captionHtml ? '<div class="richy-caption">' . $captionHtml . '</div>' : '');
    }

    public function toMd(): string
    {
        $v       = $this->voiceNote instanceof VoiceDTO
            ? $this->voiceNote->toArray()
            : (array)$this->voiceNote;

        $url     = $v['url'] ?? '';
        $caption = $this->caption !== null ? $this->renderText($this->caption) : '';
        $safeUrl = str_replace(['\\', ')'], ['\\\\', '\\)'], $url);
        return '![ ](' . $safeUrl . ($caption ? ' "' . addslashes($caption) . '"' : '') . ')' . "\n";
    }
}
