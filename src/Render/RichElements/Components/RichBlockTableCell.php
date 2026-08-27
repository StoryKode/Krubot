<?php
namespace KrubiK\Render\RichElements\Components;

use KrubiK\Render\RichElements\RichEntity;

/**
 * Represents a single cell within a RichBlockTable.
*/
readonly class RichBlockTableCell extends RichComponentEntity
{
    public function __construct(
        public RichEntity|string|array|null $text = null,
        public ?bool $is_header = null,
        public ?int $colspan = null,
        public ?int $rowspan = null,
        public string $align = 'left',
        public string $valign = 'top'
    ) {
        if (!in_array($this->align, ['left', 'center', 'right'])) {
            throw new InvalidArgumentException("Invalid horizontal alignment for table cell.");
        }
        if (!in_array($this->valign, ['top', 'middle', 'bottom'])) {
            throw new InvalidArgumentException("Invalid vertical alignment for table cell.");
        }
    }

    /**
     * HyperDX factory for a table cell, supporting closures and camelCase params.
     * This method provides a superior Developer Experience by:
     * 1. Accepting a `callable` for the `$text` parameter for fluent composition.
     * 2. Using the idiomatic PHP `camelCase` for `$isHeader`.
     * It then resolves the content and translates the parameter name before instantiation.
     *
     * @param RichEntity|callable|string|array|null $text Cell content.
     * @param bool|null $isHeader Set to true to render a <th> header cell.
     * @param int|null $colspan Number of columns to span.
     * @param int|null $rowspan Number of rows to span.
     * @param string $align Horizontal alignment ('left', 'center', 'right').
     * @param string $valign Vertical alignment ('top', 'middle', 'bottom').
     * @return self
     */
    public static function make(
        RichEntity|callable|string|array|null $text = null,
        ?bool $isHeader = null,
        ?int $colspan = null,
        ?int $rowspan = null,
        string $align = 'left',
        string $valign = 'top'
    ): self {
        // First, resolve any potential closure to its final RichEntity or string form.
        $resolvedText = self::resolveContent($text);

        // Then, instantiate the object, translating `$isHeader` to `is_header`.
        return new self(
            text: $resolvedText,
            is_header: $isHeader,
            colspan: $colspan,
            rowspan: $rowspan,
            align: $align,
            valign: $valign
        );
    }

    public function toArray(): array
    {
        return $this->filterEmpty([
            'text' => $this->normalize($this->text, true),
            'is_header' => $this->is_header,
            'colspan' => $this->colspan,
            'rowspan' => $this->rowspan,
            'align' => $this->align,
            'valign' => $this->valign,
        ]);
    }

    /**
     * Renders a table cell as either a <td> or <th> element.
     *
     * This method demonstrates the power of the declarative `attributesToString` helper.
     * It dynamically determines the tag name (th/td), generates CSS classes for alignment,
     * and includes `colspan` and `rowspan` attributes only if they are not null.
     * The cell's content is rendered recursively to support rich text.
     *
     * @return string The rendered HTML string for the table cell.
    */
    public function toHtml(): string
    {
        // Step 1: Determine the correct HTML tag based on the `is_header` property.
        $tagName = $this->is_header ? 'th' : 'td';
        
        // Step 2: Define all attributes in a single declarative array.
        // Our helper will automatically ignore any keys with `null` values.
        $attributes = [
            // We use CSS classes for alignment, which is a more modern approach than inline styles.
            'class' => "align-{$this->align} valign-{$this->valign}",
            'align' => $this->align,
            'valign' => $this->valign,
            'colspan' => $this->colspan,
            'rowspan' => $this->rowspan,
        ];
        
        // Step 3: Generate the attribute string using the central, secure helper.
        $attrString = $this->attributesToString($attributes);
        
        // Step 4: Render the inner content, which could be null or contain rich text.
        $renderedText = $this->renderHtml($this->text);
        
        // Step 5: Assemble the final tag.
        return "<{$tagName}{$attrString}>{$renderedText}</{$tagName}>";
    }
}
