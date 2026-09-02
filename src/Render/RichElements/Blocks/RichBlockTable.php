<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use Illuminate\Contracts\Support\Arrayable;

class RichBlockTable extends RichBlockEntity
{
    /** @param RichBlockTableCell[][] $cells */
    public function __construct(public array|Arrayable $cells, public ?bool $is_bordered = null, public ?bool $is_striped = null, public RichEntity|string|array|null $caption = null) {}

    /**
     * Static factory to create a new RichBlockTable instance.
     *
     * @param array|Arrayable $cells A 2D array of RichBlockTableCell instances.
     * @param bool|null $isBordered Whether the table should have borders.
     * @param bool|null $isStriped Whether the table rows should be striped.
     * @param RichEntity|callable|string|array|null $caption An optional table caption.
     * @return self Returns a new instance of the class.
    */
    public static function make(array|Arrayable $cells, ?bool $isBordered = null, ?bool $isStriped = null, RichEntity|callable|string|array|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self($cells, $isBordered, $isStriped, $resolvedCaption);
    }

    public function toArray(): array { return $this->filterEmpty(['type' => 'table', 'cells' => $this->normalize($this->cells), 'is_bordered' => $this->is_bordered, 'is_striped' => $this->is_striped, 'caption' => $this->normalizeCaption($this->caption)]); }

    /**
     * Renders the entire table structure.
     * It uses the attributesToString helper to handle boolean attributes like 'bordered'
     * and recursively renders the caption and all rows.
     *
     * @return string The rendered HTML string for the complete table.
    */
    public function toHtml(): string
    {
        // Define attributes declaratively. Our helper correctly handles boolean `true`
        // by rendering just the attribute name, and omits attributes for `false` or `null`.
        $attributes = [
            'bordered' => $this->bordered,
            'striped' => $this->striped,
        ];
        
        $tableAttrString = $this->attributesToString($attributes);

        // Render inner components recursively. The renderHtml helper handles nulls gracefully.
        $captionHtml = $this->renderHtml($this->caption);
        $rowsHtml = $this->renderHtml($this->rows);
        
        // Assemble the final table structure.
        return "<table{$tableAttrString}>{$captionHtml}{$rowsHtml}</table>";
    }
    
    public function toHtml_x2(): string
    {
        // Renders a complete <table>.
        $attributes = [];
        if ($this->is_bordered) {
            $attributes[] = 'bordered';
        }
        if ($this->is_striped) {
            $attributes[] = 'striped';
        }
        
        $html = '<table' . (empty($attributes) ? '' : ' ' . implode(' ', $attributes)) . '>';

        if ($this->caption) {
            $html .= '<caption>' . $this->renderHtml($this->caption) . '</caption>';
        }

        foreach ($this->cells as $row) {
            $html .= '<tr>';
            $html .= $this->renderHtml($row); // Delegates Cell rendering to RichBlockTableCell::toHtml()
            $html .= '</tr>';
        }

        $html .= '</table>';
        return $html;
    }
}
