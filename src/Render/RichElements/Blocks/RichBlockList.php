<?php
namespace KrubiK\Render\RichElements\Blocks;

use Illuminate\Contracts\Support\Arrayable;
use KrubiK\Render\RichElements\Components\RichBlockListItem;

/**
 * A list of blocks, corresponding to the HTML tag <ul> or <ol>.
*/
class RichBlockList extends RichBlockEntity
{
    /**
     * Create a new RichTextList instance.
     *
     * @param RichBlockListItem[] $items An array of list items.
     * @param string $style The list style: 'ordered' or 'bullet'.
     */
    public function __construct(public array|Arrayable $items, public string $style = 'bullet') 
    {
        // Runtime validation to ensure type safety and integrity.
        if (!in_array($style, ['bullet', 'ordered'])) {
            throw new InvalidArgumentException("Invalid list style provided. Must be 'bullet' or 'ordered'.");
        }
    }

    /**
     * Static factory method for creating a new RichBlockList instance.
     *
     * @param RichBlockListItem[] $items The list items.
     * @param string $style The list style. can be either:: 'bullet' | 'ordered' | 'checkbox'
     * @return self Returns a new instance of the class.
    */
    public static function make(array|Arrayable|callable $items, string $style = 'bullet'): self 
    {
        return new self(self::resolveContent($items, true), $style);
    }

    /**
     * Get the instance as an array for serialization.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array 
    {
        return [
            'type' => 'list', 
            'items' => $this->normalize($this->items),
            'style' => $this->style
        ];
    }
    
    public function toHtml(): string
    {
        /**
         * The list renderer owns only the outer <ul>/<ol> structure.
         *
         * RichBlockListItem remains responsible for its own <li> element,
         * checkbox semantics, label, ordered-item attributes and nested blocks.
         */

        $style = $this->style ?? 'bullet';

        /*
        * Determine if this should be an ordered list (<ol>).
        * The presence of 'type' or 'value' on any item signifies an ordered list.
        *
        * An explicitly ordered style takes precedence, while checkbox lists
        * remain unordered because their semantics are represented by checkbox
        * controls rather than ordered markers.
        */
        $isOrdered = $style === 'ordered';

        if (!$isOrdered && $style !== 'checkbox') {
            foreach ($this->items as $item) {
                if ($item instanceof RichBlockListItem) {
                    if ($item->type !== null || $item->value !== null) {
                        $isOrdered = true;
                        break; // Optimization: we only need to find one instance.
                    }
                } elseif (is_array($item)) {
                    if (($item['type'] ?? null) !== null || ($item['value'] ?? null) !== null) {
                        $isOrdered = true;
                        break; // Optimization: we only need to find one instance.
                    }
                }
            }
        }

        /*
        * Select the appropriate HTML tag based on the list type.
        *
        * Telegram receives native Rich HTML without Web-specific classes,
        * while Web/WebApp receives the same semantic list enhanced with
        * stable richy-* CSS classes.
        */
        $tag = $isOrdered ? 'ol' : 'ul';

        if ($this->targetsTelegram()) {
            // Build the final HTML string.
            $html = "<{$tag}>";

            // The core of the recursive rendering.
            // Each item is an Htmlable object (RichBlockListItem), so rendering
            // the item delegates the <li> structure to its own toHtml() method.
            $items = $this->items;

            /*
            * Preserve actual item instances whenever the supplied Arrayable
            * object is also Traversable. Only non-iterable Arrayable values
            * need to be normalized into an array.
            */
            if ($items instanceof Arrayable && !$items instanceof \Traversable) {
                $items = $items->toArray();
            }

            foreach ($items as $item) {
                $html .= $this->renderHtml($item);
            }

            $html .= "</{$tag}>";

            return $html;
        }

        $modClass = match ($style) {
            'ordered'  => ' richy-list--ordered',
            'checkbox' => ' richy-list--checkbox',
            default    => '',
        };

        $html = "<{$tag} class=\"richy-list{$modClass}\">";

        /*
        * The actual rendering of each <li> element is delegated to the toHtml()
        * method of the RichBlockListItem objects themselves, following the principle of single responsibility.
        *
        * renderHtml() will iterate through the supplied collection/array while
        * preserving every Htmlable RichBlockListItem encountered.
        */
        $items = $this->items;

        /*
        * Preserve Row/Item component identity whenever possible.
        * A Traversable Arrayable can be iterated directly; a non-Traversable
        * Arrayable must first expose its normalized representation.
        */
        if ($items instanceof Arrayable && !$items instanceof \Traversable) {
            $items = $items->toArray();
        }

        foreach ($items as $item) {
            $html .= $this->renderHtml($item);
        }

        $html .= "</{$tag}>";

        return $html;
    }

    public function toMd(): string
    {
        // TG 10.3 Says:
        //   bullet:   - item\n  or  * item\n  or  + item\n
        //   ordered:  1. item\n  2. item\n
        //   checkbox: - [ ] item\n  - [x] item\n
        $style = $this->style ?? 'bullet';

        /*
        * Preserve the actual RichBlockListItem objects whenever possible.
        *
        * Calling toArray() on a collection-like Arrayable would normalize the
        * children and unnecessarily discard their component identity.
        */
        $items = $this->items;

        if ($items instanceof Arrayable && !$items instanceof \Traversable) {
            $items = $items->toArray();
        }

        $items = is_array($items) ? $items : (array) $items; // ensure we have numeric keys

        $lines = [];

        foreach ($items as $i => $item) {
            /*
            * RichBlockListItem already composes its label, nested blocks,
            * checkbox state and standalone marker.
            *
            * The parent list owns the final top-level marker, therefore we
            * normalize the child's generated marker before applying our own.
            */
            if ($item instanceof RichBlockListItem) {
                $rendered = trim($item->toMd());

                $hasCheckbox = $item->has_checkbox;
                $explicitValue = $item->value;

                /*
                * Strip only the marker generated by the child.
                *
                * The remainder is the actual semantic content of the item and
                * includes all nested blocks already composed by RichBlockListItem.
                */
                $content = preg_replace(
                    '/^(?:- \[(?: |x)\] |\d+\. |[-*+] )/u',
                    '',
                    $rendered,
                    1
                ) ?? $rendered;

                $prefix = match (true) {
                    $hasCheckbox => $item->is_checked ? '- [x] ' : '- [ ] ',
                    $style === 'ordered' => ($explicitValue ?? ($i + 1)) . '. ',
                    default => '- ',
                };

                /*
                * A checkbox is an item-level semantic property, so it remains
                * authoritative even if the surrounding list was created with a
                * generic bullet style.
                */
                $lines[] = $prefix . $content;

                continue;
            }

            /*
            * Compatibility path for already-normalized/raw list item data.
            *
            * This preserves support for arrays while keeping actual component
            * instances on the canonical rendering path above.
            */
            if (is_array($item)) {
                $hasCheckbox = $item['has_checkbox'] ?? false;
                $isChecked = $item['is_checked'] ?? false;
                $value = $item['value'] ?? null;

                $label = trim($this->renderText($item['label'] ?? null));

                $nestedBlocks = $item['blocks'] ?? [];
                $nestedBlocks = trim($this->mergeTexts(
                    $nestedBlocks instanceof Arrayable
                        ? $nestedBlocks
                        : (array) $nestedBlocks,
                    "\n"
                ));

                if ($nestedBlocks !== '') {
                    $nestedBlocks = preg_replace('/^/m', '  ', $nestedBlocks) ?? $nestedBlocks;
                    $label .= ($label !== '' ? "\n" : '') . $nestedBlocks;
                }

                $prefix = match (true) {
                    $hasCheckbox => $isChecked ? '- [x] ' : '- [ ] ',
                    $style === 'ordered' => ($value ?? ($i + 1)) . '. ',
                    default => '- ',
                };

                $lines[] = $prefix . $label;

                continue;
            }

            /*
            * Last-resort compatibility path for scalar/stringable content.
            */
            $rendered = trim($this->renderText($item));

            $prefix = match ($style) {
                'ordered'  => ($i + 1) . '. ',
                'checkbox' => '- [ ] ',
                default    => '- ',
            };

            $lines[] = $prefix . $rendered;
        }

        return implode("\n", $lines) . "\n\n";
    }
}
