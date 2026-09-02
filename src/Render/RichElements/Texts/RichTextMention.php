<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

class RichTextMention extends RichTextEntity
{
    public function __construct(public RichEntity|string|array $text, public string $username) {}

    /**
     * Static factory to create a new RichTextMention instance (e.g., @username).
     *
     * @param RichEntity|callable|string|array $text The visible text of the mention.
     * @param string $username The username being mentioned, without '@'.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text, string $username): self
    {
        // Resolve the display text, as it might be a closure.
        return new self(self::resolveContent($text), $username);
    }

    public function toArray(): array { return ['type' => 'mention', 'text' => $this->normalize($this->text), 'username' => $this->username]; }

    public function toHtml(): string
    {
        // Although not explicitly defined in the HTML list, this is the standard way to link a @username.
        $escapedUsername = $this->e(ltrim($this->username, '@'));
        return '<a href="https://t.me/' . $escapedUsername . '">' . $this->renderHtml($this->text) . '</a>';
    }
}
