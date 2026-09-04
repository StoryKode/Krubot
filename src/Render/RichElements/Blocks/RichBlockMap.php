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
     * @param LocationDTO|callable|array $location The Location DTO or array.
     * @param int $zoom The map zoom level.
     * @param int $width The width of the map in pixels.
     * @param int $height The height of the map in pixels.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return self Returns a new instance of the class.
    */
    public static function make(LocationDTO|callable|array $location, int $zoom, int $width, int $height, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the map-caption if it's a callable closure.
        return new self(self::resolveContent($location, true), $zoom, $width, $height, $resolvedCaption);
    }

    public function toArray(): array { return $this->filterEmpty(['type' => 'map', 'location' => $this->normalize($this->location), 'zoom' => $this->zoom, 'width' => $this->width, 'height' => $this->height, 'caption' => $this->normalizeCaption($this->caption)]); }

    /**
     * @return string The rendered HTML.
    */
    public function toHtml(): string
    {

        $loc    = $this->location instanceof LocationDTO
            ? $this->location->toArray()
            : (array)$this->location;

        $lat    = (float)($loc['latitude']  ?? 0);
        $lng    = (float)($loc['longitude'] ?? 0);
        $zoom   = max(1, min(19, (int)$this->zoom));
        $width  = (int)$this->width  ?: 600;
        $height = (int)$this->height ?: 300;

        if($this->targetsTelegram()) {

            /*
            * Renders the map block to a custom <tg-map> HTML element.
            * It uses semantic <figure> and <figcaption> when a caption is present.
            * All attributes are securely escaped.
            */

            // Step 1: Prepare the attributes array for the map tag.
            // This approach is clean, readable, and easy to maintain.
            $attributes = [
                'lat' => $lat,
                'long' => $lng,
                'zoom' => $zoom,
                'width' => $width,
                'height' => $height,
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

        // Render Static map via OpenStreetMap / Staticmap — no API key needed. // @Todo: Implement Leaflet Dynamic Mapz
        // Swap to Google Static Maps or Mapbox if the host prefers.
        $tileUrl = sprintf(
            'https://staticmap.openstreetmap.de/staticmap.php?center=%s,%s&zoom=%d&size=%dx%d&markers=%s,%s,red',
            $lat, $lng, $zoom, $width, $height, $lat, $lng
        );

        $directionsUrl = sprintf(
            'https://www.openstreetmap.org/?mlat=%s&mlon=%s#map=%d/%s/%s',
            $lat, $lng, $zoom, $lat, $lng
        );

        $captionHtml = $this->caption !== null
            ? $this->renderHtml($this->caption)
            : '';

        return '<div class="richy-map" style="max-width:' . $width . 'px">'
            . '<a href="' . $this->esc($directionsUrl) . '" target="_blank" rel="noopener noreferrer">'
            . '<img src="' . $this->esc($tileUrl) . '" width="' . $width . '" height="' . $height . '"'
            . ' alt="Map location" loading="lazy">'
            . '</a>'
            . ($captionHtml ? '<div class="richy-caption" style="padding:.5rem .75rem">' . $captionHtml . '</div>' : '')
            . '</div>';
    }

    // Renders via OpenStreetMap static tile (no API key needed).
    public function toMd(): string
    {
        $loc = $this->location instanceof LocationDTO
            ? $this->location->toArray()
            : (array)$this->location;

        $lat  = (float)($loc['latitude']  ?? 0);
        $lng  = (float)($loc['longitude'] ?? 0);
        $zoom = max(1, min(19, (int)$this->zoom));
        $url  = sprintf('https://www.openstreetmap.org/?mlat=%s&mlon=%s#map=%d/%s/%s', $lat, $lng, $zoom, $lat, $lng);

        $caption = $this->caption !== null
            ? $this->renderText($this->caption)
            : $this->escForMd(sprintf('📍 %s, %s', $lat, $lng));

        $safeUrl = str_replace(['\\', ')'], ['\\\\', '\\)'], $url);

        return '[' . $caption . '](' . $safeUrl . ')' . "\n";
    }
}
