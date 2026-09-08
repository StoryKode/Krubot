<?php

declare(strict_types=1);

namespace KrubiK\Conversations;
/*
| Krubot BotEngine: The Architect's Lexicon [×vRC.8×] 🚀📜
|--------------------------------------------------------------------------
| This is **a Playground For Mastery**, a laboratory of ***Software Dev Artistry***;
| not a weapon for production's final battles.
|
| Our Bond: ***"Rebuilding The Rebellion"*** Within S.N.P. (The Foundation of Pure Power & Revel).
| Your Mandate [MIT]: Deconstruct Krubot. Command it. Master it. You are The Architect Now!
|
| *Go build something revolutionary!* 💜⚡️
*/

use Stringable;
use KrubiK\Keyboard\Keyboard;
use KrubiK\Keyboard\PowerButton;

/**
 * Question — A lightweight conversational presentation object.
 *
 * Responsibilities:
 * - Hold the human-facing question text.
 * - Compose native KrubiK PowerButtons.
 * - Delegate keyboard layout to the native Keyboard engine.
 * - Remain completely independent from HyperField / Conversation state.
 *
 * Question is presentation.
 * HyperField is interaction state.
 * Conversation is lifecycle.
 *
 * Usage Examples ::
 *  * Question::create('🚀 آماده‌ای وارد مرحله بعد شوی؟')
 *     ->button(
 *         '✅ بزن بریم',
 *         fn (PowerButton $button) =>
 *             $button->action('continue')
 *     )
 *     ->button(
 *         '🛑 فعلاً نه',
 *         fn (PowerButton $button) =>
 *             $button->action('cancel')
 *     )
 *     ->columns(2);
 *
 * Question::create('🎯 نوع حساب را انتخاب کن:')
 *     ->buttons(
 *         PowerButton::simple('personal', '👤 شخصی'),
 *         PowerButton::simple('business', '🏢 تجاری'),
 *         PowerButton::simple('developer', '💻 Developer'),
 *         PowerButton::simple('company', '🚀 Company'),
 *     )
 *     ->columns(2)
 *     ->rtl();
 *
 * // برای بهره‌گیری کامل از Smart Width خود Keyboard
 * Question::create('⚡ انتخاب سریع:')
 *     ->button(
 *         'PHP',
 *         fn (PowerButton $b) => $b->action('php')->width(0.33)
 *     )
 *     ->button(
 *         'Laravel',
 *         fn (PowerButton $b) => $b->action('laravel')->width(0.34)
 *     )
 *     ->button(
 *         'Krubot',
 *         fn (PowerButton $b) => $b->action('krubot')->width(0.33)
 *     )
 *     ->smart();

 * using inside a Conversation
 * $this->ask(
 *     Question::create('چه کاری انجام دهیم؟')
 *         ->buttons(
 *             PowerButton::simple('deploy', '🚀 Deploy'),
 *             PowerButton::simple('logs', '📜 Logs'),
 *         )
 *         ->columns(2),
 *     'handleAction'
 * );
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×vRC.8×
 * @license MIT
*/
class Question implements Stringable
{
    protected string $text;

    /** @var array<int, PowerButton> */
    protected array $buttons = [];

    /**
     * Default legacy-compatible layout.
     *
     * null = let Keyboard use its Smart Width engine.
    */
    protected ?int $columns = 2;

    protected bool $rtl = false;

    public function __construct(string $text = '')
    {
        $this->text = $text;
    }

    /**
     * Factory method برای ساخت سریع
    */
    public static function create(string $text): static
    {
        return new static($text);
    }

    /**
     * Change the question text.
    */
    public function text(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Return the question text.
    */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Add an already-built PowerButton.
     * افزودن یک دکمه PowerButton به سوال
    */
    public function addButton(PowerButton $button): static
    {
        $this->buttons[] = $button;

        return $this;
    }

    /**
     * Add multiple PowerButtons.
     *
     * Invalid values are intentionally ignored so this remains compatible
     * with loosely-built button arrays from older codes.
    */
    public function addButtons(iterable $buttons): static
    {
        foreach ($buttons as $button) {
            if ($button instanceof PowerButton) {
                $this->buttons[] = $button;
            }
        }

        return $this;
    }

    /**
     * Variadic button composition.
     *
     * HyperDX:
     * ->buttons($yes, $no, $later)
    */
    public function buttons(PowerButton ...$buttons): static
    {
        foreach ($buttons as $button) {
            $this->buttons[] = $button;
        }

        return $this;
    }

    /**
     * Create and append a PowerButton inline.
     *
     * HyperDX:
     * ->button('✅ Continue', fn ($button) => $button->action(...))
    */
    public function button(
        string $text,
        ?\Closure $configure = null
    ): static {
        $button = PowerButton::make($text);

        if ($configure) {
            $configure($button);
        }

        $this->buttons[] = $button;

        return $this;
    }

    /**
     * Remove all buttons.
    */
    public function clearButtons(): static
    {
        $this->buttons = [];

        return $this;
    }

    /**
     * Whether this question currently has buttons.
    */
    public function hasButtons(): bool
    {
        return $this->buttons !== [];
    }

    /**
     * Number of attached buttons.
    */
    public function countButtons(): int
    {
        return count($this->buttons);
    }

    /**
     * Return the raw PowerButton collection.
     *
     * @return array<int, PowerButton>
    */
    public function getButtons(): array
    {
        return $this->buttons;
    }

    /**
     * Control how many buttons appear in each row.
     *
     * Passing null delegates layout to Keyboard's Smart Width engine.
    */
    public function columns(?int $columns): static
    {
        $this->columns = $columns === null
            ? null
            : max(1, $columns);

        return $this;
    }

    /**
     * Enable Smart Width layout explicitly.
    */
    public function smart(): static
    {
        $this->columns = null;

        return $this;
    }

    /**
     * Set RTL/LTR presentation.
    */
    public function rtl(bool $enabled = true): static
    {
        $this->rtl = $enabled;

        return $this;
    }

    public function ltr(bool $enabled = true): static
    {
        return $this->rtl(!$enabled);
    }

    /**
     * Current column configuration.
    */
    public function getColumns(): ?int
    {
        return $this->columns;
    }

    /**
     * Current RTL state.
    */
    public function isRtl(): bool
    {
        return $this->rtl;
    }    

    /**
     * Direct final payload access when a raw keyboard array is required.
    */
    public function toArray(): array
    {
        return $this->toKeyboard()->toArray();
    }

    /**
     * تبدیل شیء به رشته (برای استفاده در متن پیام)
    */
    public function __toString(): string
    {
        return $this->text;
    }

    /**
     * Materialize the question's native KrubiK Keyboard.
     *
     * No manual button serialization is performed here.
     * The native Keyboard engine remains the single source of truth
     * for chunking, width calculation and final serialization.
    */
    public function toKeyboard(): Keyboard
    {
        $keyboard = Keyboard::make();

        if ($this->buttons !== []) {
            $keyboard->buttons($this->buttons);

            if ($this->columns !== null) {
                $keyboard->chunk($this->columns);
            }
        }

        if ($this->rtl) {
            $keyboard->rtl();
        }

        return $keyboard;
    }

    /***
     * تبدیل به فرمت کیبورد Krubot
     * (این متد پل ارتباطی بین UniChatKit-Style و Krubot است)
    * /
    public function toKeyboard(): array
    {
        $rows = [];
        // یک منطق ساده: هر دو دکمه در یک ردیف
        $chunks = array_chunk($this->buttons, 2);
        
        foreach ($chunks as $chunk) {
            $row = [];
            /** @var PowerButton $btn * /
            foreach ($chunk as $btn) {
                $row[] = $btn->toArray();
            }
            $rows[] = $row;
        }
        return $rows;
    } * /

    /*****
     * تبدیل دکمه‌های ذخیره شده به آبجکت استاندارد و بومی KrubiK Keyboard
     * با رعایت منطق "دو دکمه در هر سطر".
     * /
    public function toKeyboard(): Keyboard
    {
        // 1. ایجاد نمونه جدید از کیبورد بومی
        $keyboard = Keyboard::make();

        // 2. منطق اختصاصی: تقسیم دکمه‌ها به دسته‌های 2 تایی
        $chunks = array_chunk($this->buttons, 2);

        // 3. ساخت ردیف‌ها در آبجکت کیبورد
        foreach ($chunks as $chunk) {
            $keyboard->row(function($rowBuilder) use ($chunk) {
                foreach ($chunk as $btn) {
                    // فرض بر این است که $btn متد toArray دارد یا آرایه است
                    $data = is_object($btn) && method_exists($btn, 'toArray') 
                            ? $btn->toArray() 
                            : (array)$btn;

                    $text = $data['text'] ?? 'Button';
                    
                    // تشخیص لینک یا دکمه ساده
                    if (isset($data['url'])) {
                         $rowBuilder->link($text, $data['url']);
                    } else {
                        // تلاش برای یافتن ولیو
                        $value = $data['value'] ?? $data['name'] ?? $data['callback_data'] ?? null;
                        $rowBuilder->add($text, $value);
                    }
                }
            });
        }

        return $keyboard;
    } */
}
