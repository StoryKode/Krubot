<?php

namespace KrubiK\Render\RichElements\Components;

use KrubiK\Render\RichElements\RichEntity;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

/**
 * Represents a specialized list item with a mandatory checkbox (a task list item).
 *
 * This class enhances Developer Experience by providing a clear and intentional API
 * for creating checklist items, abstracting away the underlying boolean flags of its parent.
 * It inherits all rendering logic from RichBlockListItem, as the parent already knows
 * how to render checkboxes. This class's primary role is to simplify object construction.
 */
readonly class RichBlockCheckListItem extends RichBlockListItem
{
    /**
     * Creates a new checklist item.
     *
     * @param string $label The text content of the checklist item.
     * @param bool $is_checked The status of the checkbox (true for checked, false for unchecked).
     * @param (RichBlockEntity&Htmlable)[]|Arrayable $blocks Any nested blocks within this item. Defaults to an empty array.
     */
    public function __construct(
        string $label,
        public bool $is_checked,
        array|Arrayable $blocks = []
    ) {
        // We call the parent constructor, forcing 'has_checkbox' to true
        // and passing through the other relevant values.
        // 'value' and 'type' are set to null as checklists are inherently unordered.
        parent::__construct(
            label: $label,
            blocks: $blocks,
            has_checkbox: true,
            is_checked: $this->is_checked,
            value: null,
            type: null
        );
    }

    /**
     * HyperDX factory for creating a checklist item using PHP-friendly camelCase.
     * This method provides a cleaner public API and handles the translation
     * from `isChecked` (camelCase) to `is_checked` (snake_case) for the constructor.
     *
     * @param string $label The text content of the checklist item.
     * @param bool $isChecked The status of the checkbox (true for checked).
     * @param (RichEntity)[]|Arrayable $blocks Any nested blocks.
     * @return self
     */
    public static function make(string $label, bool $isChecked, array|Arrayable $blocks = []): self
    {
        // The translation from the public-facing camelCase API to the
        // internal/API-aligned snake_case constructor happens here.
        return new self($label, is_checked: $isChecked, blocks: $blocks);
    }

    /**
     * A highly expressive factory method to create a completed/checked list item.
     *
     * @param string $label The text content of the completed task.
     * @param (RichBlockEntity&Htmlable)[]|Arrayable $blocks Any nested blocks.
     * @return self A new instance representing a checked item.
     */
    public static function done(string $label, array|Arrayable $blocks = []): self
    {
        return new self($label, is_checked: true, blocks: $blocks);
    }

    /**
     * A highly expressive factory method to create a pending/unchecked list item.
     *
     * @param string $label The text content of the pending task.
     * @param (RichBlockEntity&Htmlable)[]|Arrayable $blocks Any nested blocks.
     * @return self A new instance representing an unchecked item.
     */
    public static function pending(string $label, array|Arrayable $blocks = []): self
    {
        return new self($label, is_checked: false, blocks: $blocks);
    }

    /**
     * Returns the array representation of the object.
     * This method simply delegates to the parent, as the array structure is identical.
     *
     * @return array
     */
    public function toArray(): array
    {
        // The parent's toArray method already correctly handles the properties
        // we've set in our constructor, so we can just call it directly.
        return parent::toArray();
    }

    /**
     * Converts the RichBlockCheckListItem object to its HTML representation (an <li> element with a checkbox).
     *
     * This method explicitly renders an <li> tag containing a checkbox. Following the
     * GitHub Flavored Markdown (GFM) specification and web best practices for static
     * content representation, the checkbox is ALWAYS rendered with the 'disabled' attribute.
     * This prevents user confusion by making it clear that the checkbox is a read-only
     * indicator of state, not an interactive form element.
     *
     * @return string The rendered HTML string for the checklist item.
     */
    public function toHtml(): string
    {
        // Step 1: Define all attributes for the checkbox in a structured, declarative array.
        // This approach is much cleaner and safer than manual string concatenation.
        $checkboxAttributes = [
            'type' => 'checkbox',
            // The 'disabled' attribute is always true, ensuring a static, non-interactive representation
            // as explained in the method's docblock. Our attributesToString helper handles `true` correctly.
            'disabled' => true,
            // The 'checked' attribute's presence is directly tied to the is_checked property.
            // The attributesToString helper will correctly render the 'checked' attribute if this
            // is true, and remove it from rendering entirely, if false or null or ===''.
            'checked' => $this->is_checked,
        ];

        // Step 2: Generate the secure and standard-compliant <input> tag using our central helper.
        // This guarantees all values are escaped and boolean attributes are handled correctly.
        $checkboxTag = '<input ' . $this->attributesToString($checkboxAttributes) . '>';

        // Step 3: Render the primary text content (label) of the list item.
        // The label itself can contain other RichText entities (like bold/italic),
        // so we must use the recursive renderHtml helper.
        $renderedLabel = $this->renderHtml($this->label);

        // Step 4: Render any nested blocks that are part of this list item.
        // This allows for complex structures like paragraphs or notes inside a checklist item.
        $renderedBlocks = $this->renderBlocks($this->blocks); // Utilizing renderBlocks helper

        // Step 5: Assemble the final <li> tag. A space is added after the checkbox for readability.
        // Since checklists are conceptually unordered, we don't need 'value' or 'type' on the <li>.
        return "<li>{$checkboxTag} {$renderedLabel}{$renderedBlocks}</li>";
    }

    public function toMd()
    {
        return '- [' . ($this->is_checked ? 'x' : ' ') . '] ' . $this->renderText($this->label);
    }
}
