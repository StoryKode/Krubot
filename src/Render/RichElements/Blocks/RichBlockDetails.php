<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use Illuminate\Contracts\Support\Arrayable;

class RichBlockDetails extends RichBlockEntity
{
    /** @param RichBlockEntity[] $blocks */
    public function __construct(public RichEntity|string|array $summary, public array|Arrayable $blocks, public ?bool $is_open = null) {}
    /**
     * Static factory to create a new RichBlockDetails instance (collapsible block).
     *
     * @param RichEntity|callable|string|array $summary The visible summary text.
     * @param array|Arrayable $blocks The content that is initially hidden.
     * @param bool|null $isOpen Whether the details block is open by default.
     * @return self Returns a new instance of the class.
    */
    public static function make(RichEntity|callable|string|array $summary, array|Arrayable|callable $blocks, ?bool $isOpen = null): self
    {
        $resolvedSummary = self::resolveContent($summary); // Resolve the summary as it can be built via a closure.
        return new self($resolvedSummary, self::resolveContent($blocks, true), $isOpen);
    }
    public function toArray(): array { return array_filter(['type' => 'details', 'summary' => $this->normalize($this->summary), 'blocks' => $this->normalize($this->blocks), 'is_open' => $this->is_open]); }

    public function toHtml(): string
    {
        $isOpen = $this->isOpen === true;
        $summaryHtml = $this->renderHtml($this->summary);
        $bodyHtml    = $this->renderBlocks($this->blocks);

        // Renders an expandable <details> block on tg.
        if($this->targetsTelegram()) {
            $openAttr = $isOpen ? ' open' : '';
            $html = '<details' . $openAttr . '>';
            $html .= '<summary>' . $summaryHtml . '</summary>';
            $html .= $bodyHtml;
            $html .= '</details>';
            return $html;
        }

        $svgChevron = '<svg class="richy-details__chevron" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
            . '<path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>'
            . '</svg>';

        // JS toggles .richy-open; $isOpen sets initial state.
        $openClass   = $isOpen ? ' richy-open' : '';
        $openAttr    = $isOpen ? ' data-richy-open="true"' : '';

        return '<div class="richy-details' . $openClass . '"' . $openAttr . '>'
            . '<div class="richy-details__summary" role="button" tabindex="0" aria-expanded="' . ($isOpen ? 'true' : 'false') . '">'
            .   $svgChevron
            .   $summaryHtml
            . '</div>'
            . '<div class="richy-details__body">' . $bodyHtml . '</div>'
            . '</div>';
    }

    // No Exact Markdown equivalent — render summary as bold heading + body
    public function toMd(): string
    {
        $summaryMd = '*' . $this->renderText($this->summary) . '*';

        $bodyMd = $this->mergeTexts($this->blocks);

        return $summaryMd . "\n" . $bodyMd . "\n";
    }
}
