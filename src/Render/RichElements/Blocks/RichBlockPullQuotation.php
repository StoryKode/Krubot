<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;

readonly class RichBlockPullQuotation extends RichBlockEntity
{
    public function __construct(public RichEntity|string|array $text, public RichEntity|string|array|null $credit = null) {}
    /**
     * Static factory to create a new RichBlockPullQuotation instance.
     *
     * @param RichEntity|callable|string|array $text The quotation text.
     * @param RichEntity|callable|string|array|null $credit Optional attribution/credit text.
     * @return self Returns a new instance of the class.
    */
    public static function make(RichEntity|callable|string|array $text, RichEntity|callable|string|array|null $credit = null): self
    {
        // Resolve both text and credit as any of them can be a closure.
        $resolvedText = self::resolveContent($text);
        $resolvedCredit = self::resolveContent($credit);
        return new self($resolvedText, $resolvedCredit);
    }
    public function toArray(): array { return $this->filterEmpty(['type' => 'pullquote', 'text' => $this->normalize($this->text), 'credit' => $this->normalize($this->credit, true)]); }
    public function toHtml(): string
    {
        // Renders a pull quote using the <aside> tag.
        // An optional <cite> tag is added for the credit.
        $html = '<aside>';
        $html .= $this->renderHtml($this->text);
        if ($this->credit) {
            $html .= '<cite>' . $this->renderHtml($this->credit) . '</cite>';
        }
        $html .= '</aside>';
        return $html;
    }
}
