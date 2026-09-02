<?php

namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\Components\RichButton;
use KrubiK\Keyboard\PowerButton;

/**
 * @see https://core.telegram.org/bots/api#richtextbutton
*/
class RichTextButton implements RichTextEntity
{
    public function __construct(
        public PowerButton|RichButton $button,
    ) {}

    public static function make(PowerButton|RichButton $button): self
    {
        return new self($button);
    }

    public function toArray(): array
    {
        return [
            'type' => 'button',
            'button' => $this->normalize($this->button),
        ];
    }

    public function toHtml(): string
    {
        // delegates content renders to PowerButton'z Professional Button Renderer
        return $this->renderHtml($this->button);
    }
}
