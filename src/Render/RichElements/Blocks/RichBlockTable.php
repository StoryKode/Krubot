<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use Illuminate\Contracts\Support\Arrayable;

use KrubiK\Render\RichElements\Components\RichBlockTableRow;
use KrubiK\Render\RichElements\Components\RichBlockTableCell;

class RichBlockTable extends RichBlockEntity
{
    /** @param RichBlockTableCell[][] $cells */
    public function __construct(public array|Arrayable $cells, public ?bool $is_bordered = null, public ?bool $is_striped = null, public ?bool $is_compact = null, public RichEntity|string|array|null $caption = null) {}

    /**
     * Static factory to create a new RichBlockTable instance.
     *
     * @param array|Arrayable|callable $cells A 2D array of RichBlockTableCell instances.
     * @param bool|null $isBordered Whether the table should have borders.
     * @param bool|null $isStriped Whether the table rows should be striped.
     * @param RichEntity|callable|string|array|null $caption An optional table caption.
     * @return self Returns a new instance of the class.
    */
    public static function make(array|Arrayable|callable $cells, ?bool $isBordered = null, ?bool $isStriped = null, ?bool $isCompact = null, RichEntity|callable|string|array|null $caption = null): self
    {
        $resolvedCaption = self::resolveContent($caption); // Resolve the caption if it's a callable closure.
        return new self(self::resolveContent($cells, true), $isBordered, $isStriped, $isCompact, $resolvedCaption);
    }

    public function toArray(): array { return $this->filterEmpty(['type' => 'table', 'cells' => $this->normalize($this->cells), 'is_bordered' => $this->is_bordered, 'is_striped' => $this->is_striped, 'is_compact' => $this->is_compact, 'caption' => $this->normalizeCaption($this->caption)]); }

    /**
     * Renders the entire table structure.
     * It uses the attributesToString helper to handle boolean attributes like 'bordered'
     * and recursively renders the caption and all rows.
     *
     * @return string The rendered HTML string for the complete table.
    */
    public function toHtml(): string
    {
        /*
        * RichBlockTable has two HTML consumers:
        *
        * 1. Telegram Rich HTML:
        *    <table bordered striped compact>
        *        <caption>...</caption>
        *        <tr>...</tr>
        *    </table>
        *
        * 2. Web / WebApps / MiniApps:
        *    Real semantic HTML table markup enhanced with local CSS classes.
        *
        * The table cells themselves remain responsible for rendering their
        * own <td>/<th> markup and attributes, while this method owns only
        * the table/caption structure and platform-specific decoration.
        *
        * RichBlockTableRow owns <tr>...</tr>
        * RichBlockTableCell owns <td>/<th>...</td>/<th>
        *
        * Therefore this renderer must never manually manufacture rows or cells
        * when actual Row/Cell components are available.
        */

        /*
        * Telegram Rich HTML
        * ------------------
        * Telegram understands table-level boolean attributes:
        *
        *   bordered
        *   striped
        *   compact
        *
        * attributesToString() already provides the exact required semantics:
        * true => bare attribute
        * false/null => omitted
        */
        if ($this->targetsTelegram()) {
            // Define attributes declaratively. Our helper correctly handles boolean `true`
            // by rendering just the attribute name, and omits attributes for `false` or `null`.
            $tableAttributes = $this->attributesToString([
                'bordered' => $this->is_bordered,
                'striped'  => $this->is_striped,
                'compact'  => $this->is_compact,
            ]);

            // Renders a complete RichHTML <table>.
            $html = '<table';

            if ($tableAttributes !== '') {
                $html .= ' ' . $tableAttributes;
            }

            $html .= '>';

            /*
            * Caption is intentionally checked against null rather than truthiness:
            *
            *   "0" / 0 / ""
            *
            * are all distinct values, and only null means "caption omitted".
            */
            if ($this->caption !== null) {
                $html .= '<caption>'
                    . $this->renderHtml($this->caption)
                    . '</caption>';
            }
        } else {
            /*
            * Web / WebApps / MiniApps
            * ------------
            * Telegram-specific boolean attributes are intentionally not emitted.
            * Their visual equivalents are represented by stable CSS classes.
            *
            * caption remains a genuine <caption> inside <table>, which
            * is the correct HTML structure.
            */
            $classes = ['richy-table'];

            if ($this->is_bordered === true) {
                $classes[] = 'richy-table--bordered';
            }

            if ($this->is_striped === true) {
                $classes[] = 'richy-table--striped';
            }

            if ($this->is_compact === true) {
                $classes[] = 'richy-table--compact';
            }

            $html = '<div class="richy-table-wrap">';
            $html .= '<table class="' . $this->esc(implode(' ', $classes)) . '">';

            if ($this->caption !== null) {
                $html .= '<caption>'
                    . $this->renderHtml($this->caption)
                    . '</caption>';
            }
        }

        /*
        * Preserve Row component identity whenever possible.
        *
        * This distinction is critical:
        *
        *   foreach ($this->cells as $row)
        *
        * preserves RichBlockTableRow instances.
        *
        * In contrast:
        *
        *   $this->cells->toArray()
        *
        * may recursively normalize Arrayable rows and destroy their identity.
        *
        * RichEntity::renderHtml() itself performs exactly such Arrayable
        * normalization, so using renderHtml($this->cells) directly here would
        * be semantically weaker.
        */
        $rows = $this->cells;

        /*
        * A Traversable Arrayable (for example a collection-like object) can be
        * iterated directly, preserving its actual row objects.
        *
        * A non-Traversable Arrayable has no iteration contract, so its normalized
        * representation is the only available fallback.
        */
        if ($rows instanceof Arrayable && !$rows instanceof \Traversable) {
            $rows = $rows->toArray();
        }

        foreach ($rows as $row) {

            /*
            * The canonical path
            *     => <tr> ... cells ... </tr>
            *
            * This lets the Row component remain the sole authority over row
            * rendering.
            */
            if ($row instanceof Htmlable) {
                $html .= $row->toHtml(); // Delegates Cell rendering to RichBlockTableRow::toHtml()
                continue;
            }

            /*
            * Compatibility path for normalized Arrayable data.
            *
            * RichBlockTableRow::toArray() produces:
            *
            *     ['cells' => ...]
            *
            * while a legacy/raw 2D row may simply be:
            *
            *     [RichBlockTableCell, RichBlockTableCell, ...]
            */
            if (is_array($row) && array_key_exists('cells', $row)) {
                $rowCells = $row['cells'];
            } else {
                $rowCells = $row;
            }

            /*
            * Only the compatibility path manufactures <tr>.
            * Real Row components never come through here.
            */
            $html .= '<tr>'
                . $this->renderHtml($rowCells)
                . '</tr>';
        }

        $html .= '</table>';

        /*
        * The Web renderer owns the optional wrapper; Telegram must receive
        * nothing except its Rich HTML table itself.
        */
        if (!$this->targetsTelegram()) {
            $html .= '</div>';
        }

        // Return the final table structure for TG/WebAppz.
        return $html;
    }


    public function toMd(): string
    {
        $rows = $this->cells instanceof Arrayable
            ? $this->cells->toArray()
            : $this->cells;

        $rows = is_array($rows) ? $rows : (array) $rows;

        if ($rows === []) {
            return '';
        }

        $normalizedRows = [];
        $hasHeader = false;

        foreach ($rows as $row) {
            /*
            * Preserve real RichBlockTableRow instances whenever possible.
            *
            * If the table has already been normalized into:
            *
            *     ['cells' => [...]]
            *
            * unwrap that representation transparently.
            */
            if ($row instanceof RichBlockTableRow) {
                $cells = $row->cells;
            } elseif (is_array($row) && array_key_exists('cells', $row)) {
                $cells = $row['cells'];
            } else {
                $cells = $row;
            }

            $cells = $cells instanceof Arrayable
                ? $cells->toArray()
                : $cells;

            $cells = is_array($cells) ? $cells : (array) $cells;

            $normalizedRows[] = $cells;

            foreach ($cells as $cell) {
                if ($cell instanceof RichBlockTableCell) {
                    $hasHeader = $hasHeader || $cell->is_header === true;
                } elseif (is_array($cell) && ($cell['is_header'] ?? false) === true) {
                    $hasHeader = true;
                }
            }
        }

        /*
        * Markdown tables require a delimiter row.
        *
        * In the presence of an explicit header cell, the delimiter belongs
        * immediately after the first row — which is the canonical form used
        * by Telegram-oriented Markdown output.
        *
        * If no cell is explicitly marked as a header, we still emit a valid
        * Markdown table by treating the first row as the header row.
        */
        $headerIndex = 0;

        /*
        * Render a single cell into Markdown-safe table content.
        *
        * The pipe character is structural in Markdown tables, therefore it
        * must never escape the cell boundary.
        */
        $renderCell = function (mixed $cell): string {
            if ($cell instanceof RichBlockTableCell) {
                $value = $cell->toMd();
            } elseif (is_array($cell)) {
                $value = $this->renderText($cell['text'] ?? null);
            } else {
                $value = $this->renderText($cell);
            }

            /*
            * A newline would terminate the Markdown table row.
            * Preserve the visual break without corrupting table structure.
            */
            $value = str_replace(
                ["\r\n", "\r", "\n"],
                '<br>',
                $value
            );

            /*
            * Escape literal pipes while preserving table delimiters.
            *
            * Existing escaped pipes are not double-escaped.
            */
            $value = preg_replace(
                '/(?<!\\\\)\|/',
                '\\\\|',
                $value
            ) ?? $value;

            return trim($value);
        };

        /*
        * Render the alignment marker of a cell.
        *
        * left   => :---------
        * center => :--------:
        * right  => ---------:
        */
        $renderSeparator = function (mixed $cell): string {
            $align = $cell instanceof RichBlockTableCell
                ? $cell->align
                : (is_array($cell) ? ($cell['align'] ?? 'left') : 'left');
        
            return match ($align) {
                'center' => ':--------:',
                'right'  => '---------:',
                default  => ':---------',
            };
        };

        $output = [];

        foreach ($normalizedRows as $rowIndex => $cells) {
            $values = [];

            foreach ($cells as $cell) {
                $values[] = $renderCell($cell);
            }

            /*
            * Empty rows are still represented as a valid Markdown row.
            */
            $output[] = '| ' . implode(' | ', $values) . ' |';

            /*
            * Insert exactly one delimiter row after the header row.
            *
            * Even without an explicit is_header, the first row is treated
            * as the Markdown header because Markdown tables require one.
            */
            if ($rowIndex === $headerIndex) {
                $separator = [];

                foreach ($cells as $cell) {
                    $separator[] = $renderSeparator($cell);
                }

                $output[] = '| ' . implode('|', $separator) . ' |';
            }
        }

        return implode("\n", $output);
    }

}
