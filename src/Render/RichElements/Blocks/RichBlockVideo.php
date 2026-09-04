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
     * @param VideoDTO|callable|array $video The Video DTO or array.
     * @param bool|null $hasSpoiler Whether the video is hidden by a spoiler.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return self Returns a new instance of the class.
    */
    public static function make(VideoDTO|callable|array $video, ?bool $hasSpoiler = null, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self(self::resolveContent($video, true), $hasSpoiler, $resolvedCaption);
    }

    public function toArray(): array { return $this->filterEmpty(['type' => 'video', 'video' => $this->normalize($this->video), 'has_spoiler' => $this->has_spoiler, 'caption' => $this->normalizeCaption($this->caption)]); }

    public function toHtml(): string
    {
        $v      = $this->video instanceof VideoDTO
            ? $this->video->toArray()
            : (array)$this->video;

        $fileId  = $this->esc($v['file_id'] ?? '');
        $src     = $this->esc($v['url'] ?? '');
        $thumb   = $this->esc($v['thumbnail']['url'] ?? $v['thumb']['url'] ?? '');
        $mime    = $v['mime_type'] ?? 'video/mp4';
        $width   = (int)($v['width']  ?? 0);
        $height  = (int)($v['height'] ?? 0);

        $hasSpoiler   = $this->has_spoiler === true;
        $spoilerClass = $hasSpoiler ? ' richy-video--spoiler' : '';

        if ($src) {
            $posterAttr  = $thumb ? ' poster="' . $thumb . '"' : '';
            $mediaHtml   = '<video controls preload="metadata"'
                . $posterAttr
                . ' width="' . $width . '" height="' . $height . '"'
                . ($hasSpoiler ? ' style="cursor:pointer" title="Click to reveal"' : '') . '>'
                . '<source src="' . $src . '" type="' . $this->esc($mime) . '">'
                . 'Your browser does not support video.'
                . '</video>';
        } else {
            $mediaHtml = '<div class="richy-media-placeholder" data-richy-file-id="' . $fileId . '">'
                . ($thumb ? '<img src="' . $thumb . '" alt="Video thumbnail" style="width:100%">' : '<span>📹 Video</span>')
                . '</div>';
        }

        $captionHtml = $this->caption !== null
            ? $this->renderHtml($this->caption)
            : '';

        return '<figure class="richy-video' . $spoilerClass . '">'
            . $mediaHtml
            . ($captionHtml ? '<figcaption>' . $captionHtml . '</figcaption>' : '')
            . '</figure>';
    }

    public function toMd(): string
    {
        $v       = $this->video instanceof VideoDTO
            ? $this->video->toArray()
            : (array)$this->video;

        $url     = $v['url'] ?? '';
        $caption = $this->caption !== null ? $this->renderText($this->caption) : '';
        $safeUrl = str_replace(['\\', ')'], ['\\\\', '\\)'], $url);
        $md = '![ ](' . $safeUrl . ($caption ? ' "' . addslashes($caption) . '"' : '') . ')';

        return $this->has_spoiler ? '||' . $md . '||' . "\n" : $md . "\n";
    }
}
