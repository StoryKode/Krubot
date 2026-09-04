<?php

namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

class RichTextEmailAddress extends RichTextEntity
{
    public function __construct(public RichEntity|string|array $text, public string $email_address) {}

    /**
     * Static factory to create a new RichTextEmailAddress instance.
     *
     * @param RichEntity|callable|string|array $text The visible text of the email link.
     * @param string $emailAddress The actual email address.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text, string $emailAddress): self
    {
        // Resolve the display text, as it might be a closure.
        return new self(self::resolveContent($text), $emailAddress);
    }
    public function toArray(): array { return ['type' => 'email_address', 'text' => $this->normalize($this->text), 'email_address' => $this->email_address]; }

    // Creates a mailto: link.
    public function toHtml(): string
    {
        $escapedEmail = $this->esc($this->email_address);
        return '<a href="mailto:' . $escapedEmail . '" class="richy-email">' . $this->renderHtml($this->text) . '</a>';
    }

    public function toMd(): string
    {
        $label  = $this->renderText($this->text);
        // URL inside () — only ")" and "\" need escaping inside the parens
        $safeUrl = 'mailto:' . str_replace(['\\', ')'], ['\\\\', '\\)'], $this->email_address);
        return '[' . $label . '](' . $safeUrl . ')';
    }
}
