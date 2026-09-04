<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\DTOs\Document as DocumentDTO;
use KrubiK\Render\RichElements\Components\RichBlockCaption;

/**
 * A block containing a Document inside the Article.
*/
class RichBlockDocument extends RichBlockEntity
{
    /**
     * @param array $document The document object, expected as an array.
     * @param RichBlockCaption|null $caption Optional caption.
     */
    public function __construct(public DocumentDTO|array $document, public RichBlockCaption|RichEntity|string|null $caption = null) {}

    /**
     * Static factory to create a new RichBlockDocument instance.
     *
     * @param DocumentDTO|callable|array $document The Document DTO or array.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return self Returns a new instance of the class.
    */
    public static function make(DocumentDTO|callable|array $document, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self(self::resolveContent($document, true), $resolvedCaption);
    }

    public function toArray(): array { return $this->filterEmpty(['type' => 'document', 'document' => $this->normalize($this->document), 'caption' => $this->normalizeCaption($this->caption)]); }

    public function toHtml(): string
    {
        $d        = $this->document instanceof DocumentDTO
            ? $this->document->toArray()
            : (array)$this->document;

        $src      = $this->esc($d['url'] ?? '');
        $fileId   = $this->esc($d['file_id'] ?? '');
        $fileName = $this->esc($d['file_name'] ?? 'Document');
        $mime     = $d['mime_type'] ?? '';
        $size     = (int)($d['file_size'] ?? 0);

        $icon = match (true) {
            str_contains($mime, 'pdf')   => '📄',
            str_contains($mime, 'zip')   => '🗜',
            str_contains($mime, 'image') => '🖼',
            str_contains($mime, 'audio') => '🎵',
            str_contains($mime, 'video') => '🎬',
            str_contains($mime, 'text')  => '📝',
            default                      => '📎',
        };

        $sizeStr = '';
        if ($size > 0) {
            $sizeStr = $size >= 1_048_576
                ? round($size / 1_048_576, 1) . ' MB'
                : round($size / 1_024, 1) . ' KB';
        }

        $tag     = $src ? 'a' : 'div';
        $hrefAttr = $src
            ? ' href="' . $src . '" download target="_blank" rel="noopener noreferrer"'
            : ' data-richy-file-id="' . $fileId . '"';

        $captionHtml = $this->caption !== null
            ? $this->renderHtml($this->caption)
            : '';

        return '<' . $tag . ' class="richy-document"' . $hrefAttr . '>'
            . '<div class="richy-document__icon">' . $icon . '</div>'
            . '<div class="richy-document__info">'
            .   '<span class="richy-document__name">' . $fileName . '</span>'
            .   ($sizeStr ? '<span class="richy-document__meta">' . $sizeStr . '</span>' : '')
            . '</div>'
            . '</' . $tag . '>'
            . ($captionHtml ? '<div class="richy-caption">' . $captionHtml . '</div>' : '');
    }

    public function toMd(): string
    {
        $d       = $this->document instanceof DocumentDTO
            ? $this->document->toArray()
            : (array)$this->document;

        $url      = $d['url'] ?? '';
        $fileName = $d['file_name'] ?? 'Document';
        $caption  = $this->caption !== null ? $this->renderText($this->caption) : $this->escForMd($fileName);
        $safeUrl  = str_replace(['\\', ')'], ['\\\\', '\\)'], $url);
        return '![ ](' . $safeUrl . ($caption ? ' "' . addslashes($caption) . '"' : '') . ')' . "\n";
    }
}
