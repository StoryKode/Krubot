<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use Illuminate\Contracts\Support\Arrayable;

readonly class RichBlockBlockQuotation extends RichBlockEntity
{
    /** @param RichBlockEntity[] $blocks */
    public function __construct(public array|Arrayable $blocks, public RichEntity|string|array|null $credit = null) {}
    /**
     * Static factory to create a new RichBlockBlockQuotation instance.
     *
     * @param array|Arrayable $blocks An array of blocks inside the quotation.
     * @param RichEntity|callable|string|array|null $credit Optional attribution/credit text.
     * @return self Returns a new instance of the class.
    */
    public static function make(array|Arrayable $blocks, RichEntity|callable|string|array|null $credit = null): self
    {
        $resolvedCredit = self::resolveContent($credit); // Resolve the credit if it's a callable closure.
        return new self($blocks, $resolvedCredit);
    }
    public function toArray(): array { return $this->filterEmpty(['type' => 'blockquote', 'blocks' => $this->normalize($this->blocks), 'credit' => $this->normalize($this->credit, true)]); }
    public function toHtml(): string
    {
        // Renders a blockquote, including all nested blocks.
        // An optional <cite> tag is added for the credit.
        $html = '<blockquote>';
        $html .= $this->renderBlocks($this->blocks);
        if ($this->credit) {
            $html .= '<cite>' . $this->renderHtml($this->credit) . '</cite>';
        }
        $html .= '</blockquote>';
        return $html;
    }

    public function toMd()
    {
        $lines = explode("\n", $this->renderText($this->blocks));
        $quotedLines = array_map(fn($line) => '> ' . $line, $lines);
        return implode("\n", $quotedLines);
    }
}
