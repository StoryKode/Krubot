<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use Illuminate\Contracts\Support\Arrayable;

class RichBlockExpandableBlockQuotation extends RichBlockEntity
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

    // Renders a expandable blockquote, including all nested blocks.
    public function toHtml(): string
    {
        if($this->targetsTelegram()) {
            $html = '<blockquote expandable>';
            $html .= $this->renderHtml($this->text);
            if ($this->credit) {
                // An optional <cite> tag is added for the credit.
                $html .= '<cite>' . $this->renderHtml($this->credit) . '</cite>';
            }
            $html .= '</blockquote>';
            return $html;
        }

        $svgChevron = '<svg class="richy-expandable-quote__chevron" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
            . '<path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>'
            . '</svg>';

        $creditText = $this->credit !== null
            ? $this->renderHtml($this->credit)
            : 'Spoiler';

        $bodyHtml = $this->text !== null
            ? $this->renderHtml($this->text)
            : '';

        // Collapsed by default; JS toggles .richy-open
        return '<div class="richy-expandable-quote">'
            . '<div class="richy-expandable-quote__trigger" role="button" tabindex="0" aria-expanded="false">'
            .   $svgChevron
            .   '<span>' . $creditText . '</span>'
            . '</div>'
            . '<div class="richy-expandable-quote__body">' . $bodyHtml . '</div>'
            . '</div>';
    }

    /*
    public function toMdOld(): string
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
    */

    // Matches TG spec exactly: **>first line\n>...\n>last line||
    // start with a bold entity (**>)
    // end with the expandability mark ||
    public function toMd(): string
    {
        $bodyText = $this->text !== null ? $this->renderText($this->text) : '';
        $lines    = explode("\n", rtrim($bodyText));

        $result = '';
        foreach ($lines as $i => $line) {
            if ($i === 0) {
                // First line gets the **> prefix (bold empty entity trick from TG spec)
                $result .= '**>' . $line . "\n";
            } else {
                $result .= '>' . $line . "\n";
            }
        }

        // Last line ends with expandability mark ||
        $result  = rtrim($result, "\n") . "||\n\n";
        return $result;
    }
}
