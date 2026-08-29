<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use Illuminate\Contracts\Support\Arrayable;

readonly class RichBlockExpandableBlockQuotation extends RichBlockEntity
{
    public function __construct(public RichEntity|string|array|null $text, public RichEntity|string|array|null $credit = null) {}
    /**
     * Static factory to create a new RichBlockExpandableBlockQuotation instance.
     *
     * @param RichEntity|string|array|null $text Text blocks inside the quotation.
     * @param RichEntity|callable|string|array|null $credit Optional attribution/credit text.
     * @return self Returns a new instance of the class.
    */
    public static function make(RichEntity|callable|string|array|null $text, RichEntity|callable|string|array|null $credit = null): self
    {
        $resolvedText = self::resolveContent($text); // Resolve the text if it's a callable closure.
        $resolvedCredit = self::resolveContent($credit); // Resolve the credit if it's a callable closure.
        return new self($resolvedText, $resolvedCredit);
    }

    public function toArray(): array
    {
        return $this->filterEmpty([
            'type' => 'expandable_blockquote',
            'text' => $this->normalize($this->text, true),
            'credit' => $this->normalize($this->credit, true)
        ]);
    }

    public function toHtml(): string
    {
        // Renders a expandable blockquote, including all nested blocks.
        // An optional <cite> tag is added for the credit.
        $html = '<blockquote expandable>';
        $html .= $this->renderHtml($this->text);
        if ($this->credit) {
            $html .= '<cite>' . $this->renderHtml($this->credit) . '</cite>';
        }
        $html .= '</blockquote>';
        return $html;
    }

    public function toMd(): string
    {
        // تبدیل متن به خطوط جداگانه
        $lines = explode("\n", $this->renderText($this->blocks));
        $totalLines = count($lines);

        if ($totalLines === 0) {
            return '';
        }

        // ایجاد آرایه خطوط با پیشوند '>'
        $quotedLines = array_map(fn(string $line) => '> ' . $line, $lines);

        // جایگزینی اولین خط با '**>' به جای '> '
        $quotedLines[0] = '**>' . substr($quotedLines[0], 2);

        // اضافه کردن علامت پایان '||' به آخرین خط
        $quotedLines[$totalLines - 1] .= '||';

        return implode("\n", $quotedLines);
    }
}
