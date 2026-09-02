<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\DTOs\PhotoSize as PhotoSizeDTO;
use KrubiK\Render\RichElements\Components\RichBlockCaption;
use Illuminate\Support\Arr;

class RichBlockPhoto extends RichBlockEntity
{
    public function __construct(public PhotoSizeDTO|array $photo, public ?bool $has_spoiler = null, public RichBlockCaption|RichEntity|string|null $caption = null) {}
    /**
     * Static factory to create a new RichBlockPhoto instance.
     *
     * @param PhotoSizeDTO|array $photo The PhotoSize DTO or array.
     * @param bool|null $hasSpoiler Whether the photo is hidden by a spoiler.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return self Returns a new instance of the class.
    */
    public static function make(PhotoSizeDTO|array $photo, ?bool $hasSpoiler = null, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self($photo, $hasSpoiler, $resolvedCaption);
    }
    public function toArray(): array { return $this->filterEmpty(['type' => 'photo', 'photo' => $this->normalize($this->photo), 'has_spoiler' => $this->has_spoiler, 'caption' => $this->normalizeCaption($this->caption)]); }

    /**
     * Converts the RichBlockPhoto object into a semantic HTML5 <figure> element.
     *
     * This method intelligently handles the rendering of a photo block by:
     * 1. Selecting the highest resolution photo from the provided array of PhotoSize objects.
     * 2. Using semantic <figure> and <figcaption> tags for better accessibility and SEO.
     * 3. Placing the Telegram `file_id` into a `data-file-id` attribute. This decouples the
     *    HTML rendering from the logic of fetching the actual public image URL, which
     *    should be handled by a separate service or a front-end script.
     * 4. Wrapping the entire element in a container with a 'telegram-spoiler' class
     *    if the `has_spoiler` flag is set.
     *
     * @return string The rendered HTML string.
    */
    public function toHtml(): string
    {
        // -- Step 1: Normalize the input and select the best photo to render --
        
        $photoSizes = Arr::wrap($this->photo);

        // If for some reason the photo array is empty, return an empty string to avoid errors.
        if (empty($photoSizes)) {
            return '<!-- RichBlockPhoto: No photo data provided -->';
        }

        // Find the photo with the largest width (as a proxy for the best resolution).
        // We use a simple reduce operation to find the PhotoSize object with the maximum width.
        /** @var PhotoSize $photoToRender */
        $photoToRender = array_reduce($photoSizes, function ($carry, $item) {
            // Ensure we are comparing PhotoSize objects, not arrays.
            if (is_array($item)) $item = PhotoSize::fromArray($item);
            if ($carry === null || $item->width() > $carry->width()) {
                return $item;
            }
            return $carry;
        });

        // -- Step 2: Build the HTML attributes for the <img> tag --

        // The 'alt' text is crucial for accessibility. Use the caption text if available,
        // otherwise, a generic fallback. We need to render the caption to plain text for this.
        $altText = $this->caption ? $this->caption->toPlainText() : 'Photo';
        
        $imgAttributes = [
            // The `src` is intentionally a placeholder. The actual URL should be populated
            // client-side or during a post-processing step using the `data-file-id`.
            'src' => '#',
            'data-file-id' => $this->e($photoToRender->fileId()),
            'width' => $photoToRender->width(),
            'height' => $photoToRender->height(),
            'alt' => $this->e($altText),
            'class' => 'rich-element-photo', // For styling purposes
            'loading' => 'lazy', // Modern browser optimization
        ];

        // Convert the attributes array to an HTML string.
        $imgTag = '<img ' . $this->attributesToString($imgAttributes) . '>';

        // -- Step 3: Construct the <figure> and <figcaption> structure --

        // Render the caption if it exists. The RichBlockCaption object handles its own complex rendering.
        $captionHtml = $this->caption ? $this->caption->toHtml() : '';

        // Wrap the image and caption in a <figure> element for semantic correctness.
        $figureTag = "<figure class=\"rich-block-photo\">{$imgTag}{$captionHtml}</figure>";

        // -- Step 4: Handle the spoiler effect --
        
        // If `has_spoiler` is true, wrap the entire figure in a div with a specific class
        // that can be targeted by CSS to apply the spoiler effect (e.g., blur).
        if ($this->has_spoiler) {
            return "<div class=\"telegram-spoiler\" onclick=\"this.classList.remove('telegram-spoiler')\">{$figureTag}</div>";
        }
        
        return $figureTag;
    }
}
