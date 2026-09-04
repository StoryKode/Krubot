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
     * @param AnimationDTO|callable|array $animation The Animation DTO or array.
     * @param bool|null $hasSpoiler Whether the animation is hidden by a spoiler.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return self Returns a new instance of the class.
    */
    public static function make(AnimationDTO|callable|array $animation, ?bool $hasSpoiler = null, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self(self::resolveContent($animation, true), $hasSpoiler, $resolvedCaption);
    }

    public function toArray(): array { return $this->filterEmpty(['type' => 'animation', 'animation' => $this->normalize($this->animation), 'has_spoiler' => $this->has_spoiler, 'caption' => $this->normalizeCaption($this->caption)]); }

    // Render as <video autoplay loop muted> for GIF-like behaviour.
    public function toHtml(): string
    {
        $anim   = $this->animation instanceof AnimationDTO
            ? $this->animation->toArray()
            : (array)$this->animation;

        // AnimationDTO contents: file_id, file_unique_id, width, height, duration, ?thumbnail, ?mime_type, ?file_name, ?file_size
        $fileId   = $this->esc($anim['file_id'] ?? '');
        $src      = $this->esc($anim['url'] ?? '');
        $width    = (int)($anim['width']  ?? 0);
        $height   = (int)($anim['height'] ?? 0);
        $mime     = $anim['mime_type'] ?? 'video/mp4';

        $hasSpoiler  = $this->has_spoiler === true;
        $spoilerClass = $hasSpoiler ? ' richy-animation--spoiler' : '';

        $sizingStyle = ($width && $height)
            ? ' style="max-width:' . $width . 'px"'
            : '';

        if ($src) {
            $mediaHtml = '<video autoplay loop muted playsinline'
                . ' width="' . $width . '" height="' . $height . '"'
                . ($hasSpoiler ? ' tabindex="0" title="Click to reveal"' : '') . '>'
                . '<source src="' . $src . '" type="' . $this->esc($mime) . '">'
                . '</video>';
        } else {
            $mediaHtml = '<div class="richy-media-placeholder" data-richy-file-id="' . $fileId . '">'
                . '<span>🎞 Animation</span>'
                . '</div>';
        }

        $captionHtml = $this->caption !== null
            ? $this->renderHtml($this->caption)
            : '';

        return '<figure class="richy-animation' . $spoilerClass . '"' . $sizingStyle . '>'
            . $mediaHtml
            . ($captionHtml ? '<figcaption>' . $captionHtml . '</figcaption>' : '')
            . '</figure>';
    }

    // MDV2 ![caption](url)  — TG detects GIF/MP4 by extension/mime
    public function toMd(): string
    {
        $anim    = $this->animation instanceof AnimationDTO
            ? $this->animation->toArray()
            : (array)$this->animation;

        $url     = $anim['url'] ?? '';
        $caption = $this->caption !== null ? $this->renderText($this->caption) : '';
        $safeUrl = str_replace(['\\', ')'], ['\\\\', '\\)'], $url);
        $md = '![ ](' . $safeUrl . ($caption ? ' "' . addslashes($caption) . '"' : '') . ')';

        return $this->hasSpoiler ? '||' . $md . '||' . "\n" : $md . "\n";
    }
}
