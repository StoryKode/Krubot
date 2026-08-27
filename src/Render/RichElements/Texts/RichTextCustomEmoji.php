<?php

namespace KrubiK\Render\RichElements\Texts;

readonly class RichTextCustomEmoji extends RichTextEntity
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
    public function toHtml(): string
    {
        // Renders a custom <tg-emoji> tag.
        $escapedId = $this->esc($this->custom_emoji_id);
        $escapedAlt = $this->esc($this->alternative_text);
        return '<tg-emoji emoji-id="' . $escapedId . '">' . $escapedAlt . '</tg-emoji>';
    }
    public function toMd()
    {
        return '![' . $this->renderText($this->alternative_text) . '](tg://emoji?id=' . $this->custom_emoji_id . ')';
    }
}