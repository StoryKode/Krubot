<?php

namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

/**
 * **RichTextEntity**
 *
 * This is an abstract base class for all rich text elements.  It inherits
 * core functionality (like `normalize`) and acts as a foundation for concrete
 * block types (e.g., RichTextBlock, RichImageBlock).  This structure embodies
 * the core logic for all block-level components that build up the full entity.
 *
 * @package KrubiK\Render\RichElements\Texts
 * @author  [Your Name - Or Placeholder, for this example]
 * @since   1405/05/14
 * @abstract
 */
abstract class RichTextEntity extends RichEntity
{
    // This class is intentionally left blank.  Its primary purpose is to establish a clear
    // type hierarchy and to inherit the normalization and serialization capabilities from
    // its parent, RichEntity. All concrete block types (e.g., TextBlock, ImageBlock, CustomAnimationBlock)
    // should extend this class.
}
