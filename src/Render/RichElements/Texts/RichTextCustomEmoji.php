<?php

namespace KrubiK\Render\RichElements\Texts;

class RichTextCustomEmoji extends RichTextEntity
{
    public function __construct(public string $custom_emoji_id, public string $alternative_text) {}

    /**
     * Static factory to create a new RichTextCustomEmoji instance.
     *
     * @param string $customEmojiId The unique identifier for the custom emoji.
     * @param string $alternativeText Fallback text description.
     * @return self
    */
    public static function make(string $customEmojiId, string $alternativeText): self { return new self($customEmojiId, $alternativeText); }

    public function toArray(): array { return ['type' => 'custom_emoji', 'custom_emoji_id' => $this->custom_emoji_id, 'alternative_text' => $this->alternative_text]; }

    // Host app can swap img[src] via JS using data-richy-emoji-id.
    public function toHtml(): string
    {
        $safeId  = $this->esc($this->customEmojiId);
        $safeAlt = $this->esc($this->alternativeText);

        if($this->targetsTelegram()) // Renders a custom <tg-emoji> tag.
            return '<tg-emoji emoji-id="' . $escapedId . '">' . $escapedAlt . '</tg-emoji>';

        return '<span class="richy-custom-emoji" data-richy-emoji-id="' . $escapedId . '" aria-label="' . $escapedAlt . '">'
            . '<img src="" alt="' . $escapedAlt . '" data-richy-emoji-pending="1" loading="lazy">'
            . '</span>';
    }

    public function toMd()
    {
        return '![' . $this->renderText($this->alternative_text) . '](tg://emoji?id=' . $this->custom_emoji_id . ')';
    }
}