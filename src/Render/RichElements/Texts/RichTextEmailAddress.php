<?php

namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

readonly class RichTextEmailAddress extends RichTextEntity
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
    public function toHtml(): string
    {
        // Creates a mailto: link.
        $escapedEmail = $this->esc($this->email_address);
        return '<a href="mailto:' . $escapedEmail . '">' . $this->renderHtml($this->text) . '</a>';
    }
}
