<?php

namespace KrubiK\Render\RichElements\Components;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Represents a single item within a RichBlockList.
 * This is a component, not a top-level block.
*/
class RichBlockListItem extends RichComponentEntity
{
    /**
     * @param string $label Label of the item.
     * @param RichBlockEntity[] $blocks The content of the item.
     * @param bool|null $has_checkbox True, if the item has a checkbox.
     * @param bool|null $is_checked True, if the item has a checked checkbox.
     * @param int|null $value For ordered lists, the numeric value of the item label.
     * @param string|null $type For ordered lists, the type of the item label; must be one of “a” for lowercase letters, “A” for uppercase letters, “i” for lowercase Roman numerals, “I” for uppercase Roman numerals, or “1” for decimal numbers
     */
    public function __construct(
        public string $label,
        public array|Arrayable $blocks,
        public ?bool $has_checkbox = null,
        public ?bool $is_checked = null,
        public ?int $value = null,
        public ?string $type = null
    ) {
        if($this->type) {
            if (!in_array($this->type, ['a', 'A', 'i', 'I', '1'])) {
                throw new InvalidArgumentException("Invalid type for ordered lists labels.");
            }
        }
    }

    /**
     * HyperDX factory for a list item using a clean, PHP-standard camelCase API.
     * This method acts as a translation layer, mapping the developer-friendly
     * parameter names (`$hasCheckbox`, `$isChecked`) to the `snake_case`
     * properties required by the constructor and the Telegram API.
     *
     * @param string $label Label of the item.
     * @param array|Arrayable $blocks Nested content blocks.
     * @param bool|null $hasCheckbox True to display a checkbox.
     * @param bool|null $isChecked True to display the checkbox as checked.
     * @param int|null $value Numeric value for ordered list items.
     * @param string|null $type Label type for ordered list items. must be one of “a” for lowercase letters, “A” for uppercase letters, “i” for lowercase Roman numerals, “I” for uppercase Roman numerals, or “1” for decimal numbers
     * @return self
     */
    public static function make(
        string $label,
        array|Arrayable|callable $blocks = [],
        ?bool $hasCheckbox = null,
        ?bool $isChecked = null,
        ?int $value = null,
        ?string $type = null
    ): self {
        // Pass arguments to the constructor, translating camelCase to snake_case.
        return new self(
            label: $label,
            blocks: self::resolveContent($blocks, true),
            has_checkbox: $hasCheckbox,
            is_checked: $isChecked,
            value: $value,
            type: $type
        );
    }

    public function toArray(): array
    {
        return $this->filterEmpty([
            'label' => $this->label,
            'blocks' => $this->normalize($this->blocks),
            'has_checkbox' => $this->has_checkbox,
            'is_checked' => $this->is_checked,
            'value' => $this->value,
            'type' => $this->type,
        ]);
    }

    public function toHtml(): string
    {
        // Step 1: Define attributes for the <li> tag in a declarative array.
        // Our 'attributesToString' helper will automatically ignore any null values,
        // making the code much cleaner than manual 'if' checks.
        $liAttributes = [
            // The 'value' attribute is used in <ol> to specify a starting number for an item.
            'value' => $this->value,
            // The 'type' attribute specifies the kind of marker (e.g., 'a', 'i').
            'type'  => $this->type,
        ];

        // Generate the attribute string for the <li> tag. The helper ensures proper escaping and formatting.
        $liAttrString = $this->attributesToString($liAttributes);

        // Step 2: Conditionally generate the checkbox tag if required.
        $checkboxTag = '';
        if ($this->has_checkbox) {
            // Define checkbox attributes using the same declarative, safe approach.
            $checkboxAttributes = [
                'type'     => 'checkbox',
                // We add 'disabled' as this is a read-only representation.
                'disabled' => true,
                // The 'checked' attribute's presence is based on the 'is_checked' flag.
                'checked'  => $this->is_checked,
            ];

            // Generate the complete, secure <input> tag. A trailing space is added for better visual separation.
            $checkboxTag = '<input ' . $this->attributesToString($checkboxAttributes) . '> ';
        }

        // Step 3: Render the primary text content (label) of the list item.
        // The label itself can contain other RichText entities, so we must use the central renderHtml method.
        $renderedLabel = $this->renderHtml($this->label);

        // Step 4: Render any nested blocks that are part of this list item.
        // This allows for complex structures like paragraphs or even other lists inside an <li>.
        $renderedBlocks = $this->renderBlocks($this->blocks);

        /*
        * Telegram and Web deliberately receive different representations.
        *
        * Telegram consumes the native Rich HTML structure, including its
        * supported checkbox input semantics, while Web receives a semantic
        * list enhanced by stable richy-* classes.
        */
        if ($this->targetsTelegram()) {
            // Step 5: Assemble the final HTML output. This structure is now clean and easy to read.
            return "<li {$liAttrString}>{$checkboxTag}{$renderedLabel}{$renderedBlocks}</li>";
        }

        /*
        * Web / WebApp representation.
        *
        * The actual checkbox control is replaced by an accessible visual
        * representation because the list item is a rendered/read-only
        * component rather than an interactive form control.
        */
        $classes = ['richy-list__item'];

        // An ordered marker is rendered by the parent <ol> / CSS counters,
        // while bullet lists receive an explicit visible bullet span.
        $isOrderedItem = $this->value !== null || $this->type !== null;

        if ($this->has_checkbox) {
            // Checkbox variant
            $checkedClass = $this->is_checked
                ? ' richy-list__checkbox--checked'
                : '';

            $checkbox = '<span class="richy-list__checkbox' . $checkedClass . '"'
                . ' role="checkbox" aria-checked="' . ($this->is_checked ? 'true' : 'false') . '"'
                . '></span>';

            return '<li class="' . $this->esc(implode(' ', $classes)) . '">'
                . $checkbox
                . '<span class="richy-list__content">'
                . $renderedLabel
                . $renderedBlocks
                . '</span>'
                . '</li>';
        }

        // Bullet / ordered — the bullet/number is rendered via CSS counters,
        // but we emit a visible bullet span for bullet lists.
        $bullet = $isOrderedItem
            ? ''
            : '<span class="richy-list__bullet" aria-hidden="true">•</span>';

        return '<li class="' . $this->esc(implode(' ', $classes)) . '">'
            . $bullet
            . '<span class="richy-list__content">'
            . $renderedLabel
            . $renderedBlocks
            . '</span>'
            . '</li>';
    }

    public function toMd(): string
    {
        // MDV2:  - label  |  1. label  |  - [ ] label  |  - [x] label
        //
        // The list item remains independently renderable, while RichBlockList
        // can replace its top-level marker when it owns the numbering context.
        $label = trim($this->renderText($this->label));

        // Compose nested block content instead of discarding it.
        // This keeps the complete semantic content of the item intact.
        $blocks = trim($this->mergeTexts($this->blocks, "\n"));

        if ($blocks !== '') {
            /*
            * Nested blocks belong to the current list item.
            *
            * Indent them so nested lists/paragraphs remain structurally
            * attached to the item instead of becoming sibling top-level blocks.
            */
            $blocks = preg_replace('/^/m', '  ', $blocks) ?? $blocks;
        }

        $content = $label;

        if ($blocks !== '') {
            $content .= ($content !== '' ? "\n" : '') . $blocks;
        }

        if ($this->has_checkbox) {
            $box = $this->is_checked ? '[x]' : '[ ]';
            return "- {$box} {$content}\n";
        }

        // Ordered numbering comes from the parent RichBlockList
        // For standalone listItem, default to bullet
        $num = $this->value ?? null;
        $prefix = $num !== null ? $num . '. ' : '- ';

        return $prefix . $content . "\n";
    }
}
