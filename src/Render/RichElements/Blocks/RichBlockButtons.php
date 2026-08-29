<?php

namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\RichElements\Components\RichButton;

/**
 * @see https://core.telegram.org/bots/api#richblockbuttons
*/
readonly class RichBlockButtons implements RichBlockEntity
{
    /**
     * @param list<RichButton> $buttons
    */
    public function __construct(
        public array|RichEntity $buttons,
        public ?string $align = null,
    ) {}

    public static function make(array|callable $buttons, ?string $align = null): self
    {
        // Resolve buttons, as they might be a sent as closure.
        $result = collect(self::resolveContent($buttons))->map(function ($button) {
            return ($button instanceof RichButton) ? $button : RichButton::make($button);
        })->toArray();
        return new self(
            ((count($result) === 1) ? $result[0] : $result),
            $align
        );
    }

    public function toArray(): array
    {
        return $this->filterEmpty([
            'type'    => 'buttons',
            'buttons' => $this->normalize($this->buttons),
            'align'   => $this->align
        ]);
    }

    public function toHtml(): string
    {
        $content = $this->renderHtml($this->buttons);

        if ($content === '') {
            return '';
        }

        $attributes = $this->attributesToString([
            'align' => $this->align,
        ]);

        return '<tg-button-row'
            . ($attributes !== '' ? ' ' . $attributes : '')
            . '>'
            . $content
            . '</tg-button-row>';
    }
}
