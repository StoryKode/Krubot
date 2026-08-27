<?php
namespace KrubiK\Render\RichElements\Blocks;

use Illuminate\Contracts\Support\Arrayable;
use KrubiK\Render\RichElements\Components\RichBlockListItem;

/**
 * A list of blocks, corresponding to the HTML tag <ul> or <ol>.
*/
readonly class RichBlockList extends RichBlockEntity
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
     * @param string $style The list style.
     * @return self Returns a new instance of the class.
    */
    public static function make(array|Arrayable $items, string $style = 'bullet'): self 
    {
        return new self($items, $style);
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

    /**
     * Converts the RichBlockList object to its HTML representation (<ol> or <ul>).
     *
     * This method intelligently determines whether to render an ordered list (<ol>)
     * or an unordered list (<ul>) by inspecting its child RichBlockListItem objects.
     * If any list item contains properties specific to ordered lists (like 'type' or 'value'),
     * the entire list is rendered as <ol>. Otherwise, it defaults to <ul>.
     *
     * The actual rendering of each <li> element is delegated to the toHtml() method
     * of the RichBlockListItem objects themselves, following the principle of single responsibility.
     *
     * @return string The rendered HTML string for the list.
    */
    public function toHtml(): string
    {
        // Determine if this should be an ordered list (<ol>).
        // The presence of 'type' or 'value' on any item signifies an ordered list.
        $isOrdered = false;
        foreach ($this->items as $item) {
            if ($item->type !== null || $item->value !== null) {
                $isOrdered = true;
                break; // Optimization: we only need to find one instance.
            }
        }

        // Select the appropriate HTML tag based on the list type.
        $tag = $isOrdered ? 'ol' : 'ul';

        // Build the final HTML string.
        $html = "<{$tag}>";

        // The core of the recursive rendering.
        // renderHtml() will iterate through the $items array. Since each item
        // is an Htmlable object (RichBlockListItem), it will call the toHtml()
        // method on each one automatically.
        $html .= $this->renderHtml($this->items);

        $html .= "</{$tag}>";

        return $html;
    }
}
