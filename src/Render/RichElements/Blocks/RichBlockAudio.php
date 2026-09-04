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
     * @param AudioDTO|callable|array $audio The Audio DTO or array.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return self Returns a new instance of the class.
     */
    public static function make(AudioDTO|callable|array $audio, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self(self::resolveContent($audio, true), $resolvedCaption);
    }

    public function toArray(): array { return $this->filterEmpty(['type' => 'audio', 'audio' => $this->normalize($this->audio), 'caption' => $this->normalizeCaption($this->caption)]); }

    public function toHtml(): string
    {
        $a      = $this->audio instanceof AudioDTO
            ? $this->audio->toArray()
            : (array)$this->audio;

        $src       = $this->esc($a['url'] ?? '');
        $fileId    = $this->esc($a['file_id'] ?? '');
        $title     = $this->esc($a['title'] ?? 'Audio');
        $performer = $this->esc($a['performer'] ?? '');
        $duration  = (int)($a['duration'] ?? 0);

        $durationStr = $duration > 0
            ? sprintf('%d:%02d', intdiv($duration, 60), $duration % 60)
            : '';

        $playerHtml = $src
            ? '<audio controls preload="none" src="' . $src . '" data-richy-file-id="' . $fileId . '">'
              . 'Your browser does not support audio.'
              . '</audio>'
            : '<div class="richy-media-placeholder" data-richy-file-id="' . $fileId . '">🎵 Audio</div>';

        $captionHtml = $this->caption !== null
            ? $this->renderHtml($this->caption)
            : '';

        $metaHtml = '';
        if ($title) {
            $metaHtml = '<div class="richy-audio__meta">'
                . '<strong class="richy-audio__title">' . $title . '</strong>'
                . ($performer ? ' <span class="richy-audio__performer">— ' . $performer . '</span>' : '')
                . ($durationStr ? ' <span class="richy-audio__duration">' . $durationStr . '</span>' : '')
                . '</div>';
        }

        return '<div class="richy-audio">'
            . '<div class="richy-audio__icon">♪</div>'
            . '<div class="richy-audio__player">'
            .   $metaHtml
            .   $playerHtml
            . '</div>'
            . '</div>'
            . ($captionHtml ? '<div class="richy-caption">' . $captionHtml . '</div>' : '');
    }

    public function toMd(): string
    {
        $a       = $this->audio instanceof AudioDTO
            ? $this->audio->toArray()
            : (array)$this->audio;

        $url     = $a['url'] ?? $a['file_id'] ?? '';
        $caption = $this->caption !== null ? $this->renderText($this->caption) : '';
        $safeUrl = str_replace(['\\', ')'], ['\\\\', '\\)'], $url);
        return '![ ](' . $safeUrl . ($caption ? ' "' . addslashes($caption) . '"' : '') . ')' . "\n";
    }
}
