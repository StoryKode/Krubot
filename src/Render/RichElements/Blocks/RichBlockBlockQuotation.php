<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use Illuminate\Contracts\Support\Arrayable;

class RichBlockBlockQuotation extends RichBlockEntity
{
    /** @param RichBlockEntity[] $blocks */
    public function __construct(public array|Arrayable $blocks, public RichEntity|string|array|null $credit = null) {}
    /**
     * Static factory to create a new RichBlockBlockQuotation instance.
     *
     * @param array|Arrayable|callable $blocks An array of blocks inside the quotation.
     * @param RichEntity|callable|string|array|null $credit Optional attribution/credit text.
     * @return self Returns a new instance of the class.
    */
    public static function make(array|Arrayable|callable $blocks, RichEntity|callable|string|array|null $credit = null): self
    {
        $resolvedCredit = self::resolveContent($credit); // Resolve the credit if it's a callable closure.
        return new self(self::resolveContent($blocks, true), $resolvedCredit);
    }
    public function toArray(): array { return $this->filterEmpty(['type' => 'blockquote', 'blocks' => $this->normalize($this->blocks), 'credit' => $this->normalize($this->credit, true)]); }
    public function toHtml(): string
    {
        // Renders a blockquote, including all nested blocks.
        $blocksHtml = $this->renderBlocks($this->blocks);

        if($this->targetsTelegram()) {
            $html = '<blockquote>';
            $html .= $blocksHtml;
            if ($this->credit) {
                // An optional <cite> tag is added for the credit.
                $html .= '<cite>' . $this->renderHtml($this->credit) . '</cite>';
            }
            $html .= '</blockquote>';
            return $html;
        }

        $creditHtml = '';
        if ($this->credit !== null) {
            $creditHtml = '<footer class="richy-blockquote__credit">'
                . $this->renderHtml($this->credit)
                . '</footer>';
        }

        return '<blockquote class="richy-blockquote">'
            . '<div class="richy-blockquote__content">' . $blocksHtml . '</div>'
            . $creditHtml
            . '</blockquote>';
    }

    public function toMdOld()
    {
        $lines = explode("\n", $this->renderText($this->blocks));
        $quotedLines = array_map(fn($line) => '> ' . $line, $lines);
        return implode("\n", $quotedLines);
    }

    // >line one\n>line two\n>\n>credit
    public function toMd(): string
    {
        $inner = $this->mergeTexts($this->blocks);

        // Prefix every line with ">"
        $quoted = implode("\n",
            array_map(
                fn($line) => '>' . $line,
                explode("\n", rtrim($inner))
            )
        );

        if ($this->credit !== null) {
            $creditText = $this->renderText($this->credit);
            $quoted .= "\n>— " . $creditText;
        }

        return $quoted . "\n\n";
    }
}
