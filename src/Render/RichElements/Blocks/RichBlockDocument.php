<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\DTOs\Document as DocumentDTO;
use KrubiK\Render\RichElements\Components\RichBlockCaption;

/**
 * A block containing a Document inside the Article.
*/
readonly class RichBlockDocument extends RichBlockEntity
{
    /**
     * @param array $document The document object, expected as an array.
     * @param RichBlockCaption|null $caption Optional caption.
     */
    public function __construct(public DocumentDTO|array $document, public RichBlockCaption|RichEntity|string|null $caption = null) {}

    /**
     * Static factory to create a new RichBlockDocument instance.
     *
     * @param DocumentDTO|array $document The Document DTO or array.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return self Returns a new instance of the class.
    */
    public static function make(DocumentDTO|array $document, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self($document, $resolvedCaption);
    }
    public function toArray(): array { return $this->filterEmpty(['type' => 'document', 'document' => $this->normalize($this->document), 'caption' => $this->normalizeCaption($this->caption)]); }
}
