<?php

namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\RichElements\Components\RichButton;
use KrubiK\Keyboard\PowerButton;

/**
 * @see https://core.telegram.org/bots/api#richblockbuttons
*/
class RichBlockButtons extends RichBlockEntity
{
    /**
     * @param list<RichButton> $buttons
    */
    public function __construct(
        public array|Arrayable $buttons,
        public ?string $align = null, // can be either:: 'left' | 'center' | 'right' | null (: auto)
    ) {}

    public static function make(array|Arrayable|callable $buttons, ?string $align = null): self
    {
        // resolveContent می‌تواند null، یک شیء یا آرایه برگرداند
        $resolved = self::resolveContent($buttons, true);

        // تبدیل به آرایه‌ای از دکمه‌ها
        $buttonsArray = match (true) {
            is_null($resolved) => [],
            is_array($resolved) => $resolved,
            default => [$resolved], // تک‌عنصر را در آرایه می‌پیچیم
        };

        // اگر آرایه شامل آرایه‌های تو در تو باشد، یک سطح flatten می‌کنیم
        // اما فقط در صورتی که تک‌عنصر باشد و آن عنصر آرایه باشد
        if (count($buttonsArray) === 1 && is_array($buttonsArray[0])) {
            $buttonsArray = $buttonsArray[0]; // خارج کردن از سطح اول
        }

        // اطمینان از اینکه همه‌ی آیتم‌ها از نوع RichButton هستند
        $result = collect($buttonsArray)
            ->map(fn($button) => 
                ($button instanceof RichButton || $button instanceof PowerButton) 
                    ? $button 
                    : RichButton::make($button)
            )
            ->all(); // تبدیل به آرایه‌ای از اشیاء RichButton برای تطابق با type hint سازنده, Collection بدون تغییر اشیاء

        return new self($result, $align);
    }

    public function toArray(): array
    {
        return $this->filterEmpty([
            'type'    => 'buttons',
            'buttons' => $this->normalize($this->buttons),
            'align'   => $this->align
        ]);
    }

    public function toHtml(): string
    {
        $content = $this->renderHtml($this->buttons);
        /*$content = '';
        foreach ($this->buttons as $btn) {
            $content .= $this->renderHtml($btn);
        }*/

        if ($content === '') {
            return '';
        }

        $align = $this->align;

        $alignClass = match ($align) {
            'center' => ' richy-buttons--center',
            'right'  => ' richy-buttons--right',
            'left'   => ' richy-buttons--left',
            default  => '',
        };
        $class = 'richy-buttons' . $alignClass;
        $attributes = $this->attributesToString(compact('align', 'class'));

        $tagName = $this->targetsTelegram() ? 'tg-button-row' : 'div';

        return '<' . $tagName
            . ($attributes !== '' ? ' ' . $attributes : '')
            . '>'
            . $content
            . '</'.$tagName.'>';
    }

    public function toMd(): string
    {
        $parts = [];
        foreach ($this->buttons as $btn) {
            $parts[] = $this->renderText($btn);
        }
        return implode('  ', $parts) . "\n";
    }
}
