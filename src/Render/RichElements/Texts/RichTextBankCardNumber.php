<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

readonly class RichTextBankCardNumber extends RichTextEntity
{
    public function __construct(public RichEntity|string|array $text, public string $bank_card_number) {}

    /**
     * Static factory to create a new RichTextBankCardNumber instance.
     *
     * @param RichEntity|callable|string|array $text The visible text representation.
     * @param string $bankCardNumber The full bank card number string.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text, string $bankCardNumber): self
    {
        // Resolve the display text, as it might be a closure.
        return new self(self::resolveContent($text), $bankCardNumber);
    }

    public function toArray(): array { return ['type' => 'bank_card_number', 'text' => $this->normalize($this->text), 'bank_card_number' => $this->bank_card_number]; }

    /**
     * Renders the bank card number as an inline <span> element.
     *
     * A <span> is used as a neutral inline container. A specific CSS class `tg-bank-card-number`
     * is added for styling, and a `data-card-number` attribute holds the raw number,
     * which can be useful for client-side scripts (e.g., a "copy to clipboard" feature)
     * without exposing it directly in a standard attribute like 'title'.
     *
     * @return string The rendered HTML string.
    */
    public function toHtml(): string
    {
        // Define attributes declaratively for our helper.
        $attributes = [
            'class' => 'richy-bank-card-number copyable-element',
            'data-card-number' => $this->bank_card_number,
        ];
        
        // Let the central helper handle escaping and string conversion.
        $attrString = $this->attributesToString($attributes);
        
        // Recursively render the inner text, which might contain other rich elements.
        $renderedText = $this->renderHtml($this->text);
        
        // Assemble the final tag.
        return "<span{$attrString}>{$renderedText}</span>";
    }
}
