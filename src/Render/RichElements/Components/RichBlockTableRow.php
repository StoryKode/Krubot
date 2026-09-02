<?php

namespace KrubiK\Render\RichElements\Components;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Represents a row of cells within a RichBlockTable.
*/
class RichBlockTableRow extends RichComponentEntity
{
    /**
     * @param RichBlockTableCell[] $cells An array of table cell components.
     */
    public function __construct(public array|Arrayable $cells) {}

    /**
     * A straightforward factory for creating a table row.
     * Accepts an array or Arrayable of RichBlockTableCell instances.
     *
     * @param RichBlockTableCell[]|Arrayable $cells The cells for this row.
     * @return self
    */
    public static function make(array|Arrayable $cells): self { return new self($cells); }

    public function toArray(): array { return ['cells' => $this->normalize($this->cells)]; }

    /**
     * Renders a table row (<tr>) and recursively renders all of its cells.
     *
     * @return string The rendered HTML string for the table row.
    */
    public function toHtml(): string
    {
        $renderedCells = $this->renderHtml($this->cells);
        return "<tr>{$renderedCells}</tr>";
    }

    public function toMd()
    {
        return '| ' . implode(' | ', array_map([$this, 'renderText'], $this->cells)) . ' |';
    }
}
