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
    public static function make(RichEntity|callable|string|array $summary, array|Arrayable $blocks, ?bool $isOpen = null): self
    {
        $resolvedSummary = self::resolveContent($summary); // Resolve the summary as it can be built via a closure.
        return new self($resolvedSummary, $blocks, $isOpen);
    }
    public function toArray(): array { return array_filter(['type' => 'details', 'summary' => $this->normalize($this->summary), 'blocks' => $this->normalize($this->blocks), 'is_open' => $this->is_open]); }
    public function toHtml(): string
    {
        // Renders an expandable <details> block.
        $openAttr = $this->is_open ? ' open' : '';
        $html = '<details' . $openAttr . '>';
        $html .= '<summary>' . $this->renderHtml($this->summary) . '</summary>';
        $html .= $this->renderBlocks($this->blocks);
        $html .= '</details>';
        return $html;
    }
}
