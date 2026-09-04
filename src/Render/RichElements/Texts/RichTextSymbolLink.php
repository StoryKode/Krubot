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

    /**
     * برچسب قابل خواندن برای aria-label و title
     * مثال: 'Cashtag', 'Hashtag', 'Bot command'
    */
    abstract protected function getDisplayType(): string;

    /**
     * ویژگی‌های اضافی برای تگ <a> (در صورت نیاز)
    */
    protected function getAdditionalAttributes(): array
    {
        return [];
    }

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
     * خروجی HTML با تمام جزئیات پیشرفته
     *
     * This method now includes logic to wrap the leading symbol of the display text
     * within a <span class="richy-symbol">. This allows developers to easily hide or style
     * the symbol using simple CSS, providing maximum visual flexibility.
     *
     * Example CSS to hide the symbol:
     * .richy-symbolic-link-symbol { display: none; }
     *
     * @return string The rendered HTML string.
    */
    public function toHtml(): string
    {
        // Render the potentially rich text content first.
        $renderedText = $this->renderHtml($this->text);
        $href = $this->buildHref($this->value);

        // شرط تلگرام: لینک ساده با حداقل ویژگی
        if ($this->targetsTelegram()) {
            return '<a href="' . $this->esc($href) . '">' . $renderedText . '</a>';
        }

        // نسخه کامل برای وب
        $symbol = $this->getSymbol();
        $dataType = $this->getDataType();
        $displayType = $this->getDisplayType();

        $baseAttributes = [
            'class' => 'richy-symbolic-link richy-symbolic-link-' . $dataType . ' richy-' . $dataType,
            'href' => $href,
            'data-type' => $dataType,
            'data-value' => $this->value,
            'data-symbol' => $symbol,
            'data-raw' => $symbol . $this->value,
            'data-entity' => $dataType,
            'dir' => 'auto',
            'rel' => 'noopener noreferrer',
            'role' => 'link',
            'aria-label' => $displayType . ': ' . $symbol . $this->value,
            'title' => $displayType . ': ' . $symbol . $this->value,
        ];

        $attributes = array_merge($baseAttributes, $this->getAdditionalAttributes());
        $attrString = $this->attributesToString($attributes);

        $escapedSymbol = $this->esc($symbol);

        // Smartly wrap the symbol in a span for CSS control.
        $finalDisplayText = $renderedText;
        if (str_starts_with($renderedText, $escapedSymbol)) {

            // We use preg_replace for a safe, non-greedy, start-of-string replacement.
            // This robustly handles the case where the symbol might appear elsewhere in the text.
            $wrappedSymbol = '<span class="richy-symbolic-link-symbol richy-' . $dataType . '-symbol" aria-hidden="true">' . $escapedSymbol . '</span>';
            $finalDisplayText = preg_replace(
                '/^' . preg_quote($escapedSymbol, '/') . '/',
                $wrappedSymbol,
                $renderedText,
                1
            );
        }

        return "<a {$attrString}>{$finalDisplayText}</a>";
    }

    /**
     * خروجی Markdown استاندارد
    */
    public function toMd(): string
    {
        $displayText = $this->renderMarkdownText($this->text);
        $href = $this->buildHref($this->value);

        if(self::$autoPrependSymbol)
            $displayText = $this->escForMd($this->getSymbol()) . ltrim($displayText, $this->getSymbol());

        return '[' . $displayText . '](' . $href . ')';
    }

    /**
     * تبدیل محتوای متنی به رشته‌ی امن برای Markdown
    */
    protected function renderMarkdownText(mixed $content): string
    {
        if ($content === null || $content === false) {
            return '';
        }

        if ($content instanceof RichEntity) {
            if (method_exists($content, 'toMd')) {
                return $content->toMd();
            }
            return $this->escForMd($content->toText());
        }

        if ($content instanceof \Stringable) {
            $content = (string)$content;
        }

        if (is_string($content)) {
            return $this->escForMd($content);
        }

        if (is_array($content)) {
            return implode('', array_map(fn($item) => $this->renderMarkdownText($item), $content));
        }

        return '';
    }
}
