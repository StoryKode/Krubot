<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

/**
 * Represents a generic text container. It can be a simple string or a complex nested structure.
 * The type property for this element will be determined by the context it is used in (e.g., 'plain').
 * This is a utility class for internal representation.
*/
class RichText extends RichTextEntity
{
    /** @param RichEntity|string|array $text */
    public function __construct(public RichEntity|string|array $text) {}

    /**
     * Static factory to create a new RichText instance.
     *
     * @param RichEntity|callable|string|array $text The content to be wrapped.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text): self
    {
        // Resolve the text content, as it might be a closure.
        return new self(self::resolveContent($text));
    }

    public function toArray(): array { return ['text' => $this->normalize($this->text)]; }

    public function toHtml():string {
        return $this->renderHtml($this->text);
    }
}
