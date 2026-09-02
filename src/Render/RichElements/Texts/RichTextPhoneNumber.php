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
    public function toHtml(): string
    {
        // Creates a tel: link.
        $escapedPhone = $this->esc($this->phone_number);
        return '<a href="tel:' . $escapedPhone . '">' . $this->renderHtml($this->text) . '</a>';
    }
}
