<?php

namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;

use KrubiK\Render\RichElements\Components\RichBlockCaption

/**
 * **RichBlockEntity**
 *
 * This is an abstract base class for all rich block elements.  It inherits
 * core functionality (like `normalize`) and acts as a foundation for concrete
 * block types (e.g., RichTextBlock, RichImageBlock).  This structure embodies
 * the core logic for all block-level components that build up the full entity.
 *
 * @abstract
 */
abstract class RichBlockEntity extends RichEntity
{

    /**
     * Intelligently normalizes a mixed input into a RichBlockCaption object.
     *
     * This powerful helper centralizes the logic for handling captions. It ensures
     * that any input (be it null, a string, a closure, or another RichEntity)
     * is consistently converted into a proper RichBlockCaption object, or null if the
     * input is empty. This drastically simplifies the `toArray` methods in child
     * block classes that support captions.
     *
     * - If the input is already a `RichBlockCaption`, it's returned as array.
     * - If the input is a `Closure`, it's resolved and the result is wrapped.
     * - If the input is a `string` or another `RichEntity`, it's wrapped.
     * - If the input is falsy (null, empty string, etc.), it returns `null`.
     *
     * @param mixed $captionInput The raw caption data.
     * @return array|null The normalized caption array or null.
    */
    protected function normalizeCaption(mixed $captionInput): ?array
    {
        // Guard clause: If the input is falsy (null, '', false, []), return null immediately.
        if (!$captionInput) {
            return null;
        }

        // Idempotency: If it's already a RichBlockCaption instance, do nothing and return it.
        if ($captionInput instanceof RichBlockCaption) {
            return $this->normalize($captionInput);
        }

        // For everything else (string, callable, RichEntity, array):
        // 1. Resolve the content. This executes a closure or passes through other types.
        $resolvedContent = self::resolveContent($captionInput);

        // 2. After resolving, if the content is now empty, return null.
        if (!$resolvedContent) {
            return null;
        }
        
        // 3. Wrap the resolved content in a new RichBlockCaption instance using its own factory.
        return $this->normalize(RichBlockCaption::make($resolvedContent), true);
    }

}
