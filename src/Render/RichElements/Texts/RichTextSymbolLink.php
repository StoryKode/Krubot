<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

/**
 * An abstract base class for symbol-based links with "HyperDX" features.
 * It centralizes rendering logic, provides a global toggle for auto-prepending symbols,
 * and wraps symbols in a <span> for CSS control.
 */
abstract class RichTextSymbolLink extends RichTextEntity
{

    // --- Configuration ---
    /**
     * Global switch to enable or disable automatic prepending of symbols.
     * Defaults to true for a more forgiving and intuitive API.
     * This is a static property, so it's not affected by the 'readonly' instance constraint.
     * @var bool
     */
    public static bool $autoPrependSymbol = true;

    /**
     * A static method to fluently control the auto-prepending behavior application-wide.
     * @param bool $enabled Set to true to enable, false to disable.
     */
    public static function toggleAutoPrepending(?bool $enabled = null): void
    {
        if($enabled === null)
            $enabled = !self::$autoPrependSymbol;

        self::$autoPrependSymbol = $enabled;
    }

    // --- Instance Properties ---
    /**
     * The full text content to be displayed (e.g., "#Laravel" or a RichElement).
     * @var RichEntity|string|array
     */
    public RichEntity|string|array $text;

    /**
     * The raw value without the leading symbol (e.g., "laravel").
     * @var string
     */
    protected string $value;

    // --- Abstract Methods ---
    abstract protected function getSymbol(): string;
    abstract protected function getDataType(): string;
    abstract protected function buildHref(string $value): string;

    public function toArray(): array
    {

        $dataType = $this->getDataType(); // 'hashtag'||'cashtag'||'bot_command'

        $outCome = [
            'type' => $dataType,
            'text' => $this->normalize($this->text)
        ];
        $outCome[$dataType] = $this->{$dataType};

        return $outCome;

        /*
        Or Just::
            return [
                'type' => $this->getDataType(),
                'text' => $this->normalize($this->text),
                $this->getDataType() => $this->{($this->getDataType())},
            ];
        */
    }

    /**
     * Renders the symbol link to its final HTML form.
     *
     * This method now includes logic to wrap the leading symbol of the display text
     * within a <span class="tg-symbol">. This allows developers to easily hide or style
     * the symbol using simple CSS, providing maximum visual flexibility.
     *
     * Example CSS to hide the symbol:
     * .tg-symbolic-link-symbol { display: none; }
     *
     * @return string The rendered HTML string.
     */
    public function toHtml(): string
    {
        $attributes = [
            'class' => ['tg-rich-symbolic-link', 'tg-rich-symbolic-link-' . $this->getDataType()],
            'href' => $this->buildHref($this->value),
            'data-type' => $this->getDataType(),
            'data-value' => $this->value,
            'dir' => 'auto',
            'rel' => 'noopener noreferrer',
        ];

        $attrString = $this->attributesToString($attributes);

        // Render the potentially rich text content first.
        $renderedText = $this->renderHtml($this->text);

        // Smartly wrap the symbol in a span for CSS control.
        $symbol = $this->getSymbol();
        $escapedSymbol = $this->esc($symbol);

        $finalDisplayText = $renderedText;
        // We use preg_replace for a safe, non-greedy, start-of-string replacement.
        // This robustly handles the case where the symbol might appear elsewhere in the text.
        if (str_starts_with($renderedText, $escapedSymbol)) {
            $wrappedSymbol = '<span class="tg-symbolic-link-symbol">' . $escapedSymbol . '</span>';
            $finalDisplayText = preg_replace('/^' . preg_quote($escapedSymbol, '/') . '/', $wrappedSymbol, $renderedText, 1);
        }

        return "<a{$attrString}>{$finalDisplayText}</a>";
    }
}
