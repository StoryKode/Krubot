<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

class RichTextPhoneNumber extends RichTextEntity
{
    public function __construct(public RichEntity|string|array $text, public string $phone_number) {}

    /**
     * Static factory to create a new RichTextPhoneNumber instance.
     *
     * @param RichEntity|callable|string|array $text The visible text for the phone number.
     * @param string $phoneNumber The phone number in a callable format.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text, string $phoneNumber): self
    {
        // Resolve the display text, as it might be a closure.
        return new self(self::resolveContent($text), $phoneNumber);
    }

    public function toArray(): array { return ['type' => 'phone_number', 'text' => $this->normalize($this->text), 'phone_number' => $this->phone_number]; }

    // Creates a tel: link.
    public function toHtml(): string
    {
        // Strip everything except digits, +, -, spaces, parens for the href
        $dialable  = preg_replace('/[^\d+\-\s()]/', '', $this->phoneNumber);
        $safePhone = $this->esc($dialable);

       return '<a href="tel:' . $safePhone . '" class="richy-phone">' . $this->renderHtml($this->text) . '</a>';
    }

    public function toMd(): string
    {
        $dialable = preg_replace('/[^\d+\-\s()]/', '', $this->phoneNumber);
        $label    = $this->renderText($this->text);
        return '[' . $label . '](tel:' . $dialable . ')';
    }
}
