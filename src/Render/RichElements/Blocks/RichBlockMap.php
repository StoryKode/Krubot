<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\Models\Location as LocationDTO;
use KrubiK\Render\RichElements\Components\RichBlockCaption;

/**
 * A block with a map.
*/
class RichBlockMap extends RichBlockEntity
{
    /**
     * @param LocationDTO|array $location Central LocationDTO object.
     * @param int $zoom Map zoom level; 13-20.
     * @param int $width Expected width of the map.
     * @param int $height Expected height of the map.
     * @param RichBlockCaption|null $caption Optional caption.
     */
    public function __construct(public LocationDTO|array $location, public int $zoom, public int $width, public int $height, public RichBlockCaption|RichEntity|string|null $caption = null) {}

    /**
     * Static factory to create a new RichBlockMap instance.
     *
     * @param LocationDTO|array $location The Location DTO or array.
     * @param int $zoom The map zoom level.
     * @param int $width The width of the map in pixels.
     * @param int $height The height of the map in pixels.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return self Returns a new instance of the class.
    */
    public static function make(LocationDTO|array $location, int $zoom, int $width, int $height, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the map-caption if it's a callable closure.
        return new self($location, $zoom, $width, $height, $resolvedCaption);
    }

    public function toArray(): array { return $this->filterEmpty(['type' => 'map', 'location' => $this->normalize($this->location), 'zoom' => $this->zoom, 'width' => $this->width, 'height' => $this->height, 'caption' => $this->normalizeCaption($this->caption)]); }

    /**
     * Renders the map block to a custom <tg-map> HTML element.
     * It uses semantic <figure> and <figcaption> when a caption is present.
     * All attributes are securely escaped.
     *
     * @return string The rendered HTML.
    */
    public function toHtml(): string
    {
        // Step 1: Prepare the attributes array for the map tag.
        // This approach is clean, readable, and easy to maintain.
        $attributes = [
            'lat' => $this->location->latitude,
            'long' => $this->location->longitude,
            'zoom' => $this->zoom,
            'width' => $this->width,
            'height' => $this->height,
            'class' => 'rich-block-map', // Add a default class for styling
        ];

        // Step 2: Generate the map tag using the new helper from our trait.
        // This ensures all attributes are correctly formatted and escaped.
        $mapTag = '<tg-map ' . $this->attributesToString($attributes) . '></tg-map>';

        // Step 3: Render the caption using the robust `renderHtml` helper.
        // This will return an empty string if the caption is null, simplifying the logic.
        // The result will already be wrapped in a <figcaption> tag by RichBlockCaption itself.
        $captionHtml = $this->renderHtml($this->caption);

        // Step 4: If a caption exists, wrap everything in a semantic <figure> tag.
        if (!empty($captionHtml)) {
            return "<figure class=\"rich-map-figure\">{$mapTag}{$captionHtml}</figure>";
        }

        // Step 5: If there's no caption, just return the map tag itself.
        return $mapTag;
    }
}
