<?php

namespace KrubiK\Keyboard;
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

// use RubikaBot\Keyboard\Keypad as BaseKeypad;
use KrubiK\Keyboard\Keyboard;
use KrubiK\Keyboard\PowerButton;
use KrubiK\Arcane\InteractsWithLockedProperties;
use Closure;
use InvalidArgumentException;

/**
 * کلاس پیشرفته ReplyKeyboard برای مدیریت دکمه‌های زیر کیبورد کاربر.
 *
 * این کلاس تجمیع‌کننده تمام قابلیت‌های:
 * 1. تغییر اندازه خودکار (Resize)
 * 2. یک‌بار مصرف بودن (OneTime)
 * 3. متن نگهدارنده (Placeholder)
 * 4. و قابلیت‌های پاسخ‌دهی خاص (Contact/Location)
 * است.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class ReplyKeyboard extends Keyboard
{
    use InteractsWithLockedProperties;

    /**
     * مخزن ردیف‌های دکمه‌ها.
     * ما این را جداگانه نگه می‌داریم تا کنترل کامل روی فرمت Reply داشته باشیم،
     * اما در toArray با والد همگام‌سازی می‌کنیم.
     * 
     * @var array
    */
    protected array $fluentRows = [];

    /** @var bool درخواست تغییر اندازه کیبورد سمت کلاینت */
    protected bool $resizeKeyboard = true; // پیش‌فرض true معمولاً UX بهتری دارد

    /** @var bool درخواست مخفی شدن کیبورد پس از یک بار استفاده */
    protected bool $oneTimeKeyboard = false;

    /** @var bool نمایش کیبورد فقط برای کاربران خاص (مثلاً در ریپلای یا منشن) */
    protected bool $selective = false;

    /** @var bool آیا کیبورد دائمی باشد؟ (پشتیبانی خاص در برخی کلاینت‌ها) */
    protected bool $persistent = false;

    /** @var string|null متن راهنما داخل اینپوت باکس کاربر */
    protected ?string $placeholder = null;

    /**
     * سازنده استاتیک برای شروع زنجیره (Factory Method).
     *
     * @return static
    */
    public static function make(): static
    {
        return new static();
    }

    /**
     * افزودن یک ردیف دکمه به کیبورد.
     * این متد فوق‌العاده هوشمند است و انواع ورودی‌ها را می‌پذیرد.
     *
     * @param array|Closure $buttons آرایه‌ای از دکمه‌ها، یا یک کلوژر بیلدر.
     *                               اگر آرایه باشد، می‌تواند شامل رشته (متن ساده) یا آبجکت PowerButton باشد.
     * @return mixed
    */
    public function hybridRow(array|Closure|null $buttons = null): mixed
    {
        // اگر کلوژر باشد، یک آرایه خالی به آن پاس می‌دهیم تا پر شود (الگوی بیلدر)
        if ($buttons instanceof Closure) {
            $collection = [];
            $buttons($collection);
            $buttons = $collection;
        }

        $formattedRow = [];

        foreach ($buttons as $btn) {
            if ($btn instanceof PowerButton) {
                // اگر دکمه از نوع کلاس Button کتابخانه یا KrubiK باشد
                $formattedRow[] = $btn->toArray();
            } elseif ($btn instanceof ReplyButton) {
                // پشتیبانی از کلاس اختصاصی ReplyButton (اگر وجود داشته باشد)
                $formattedRow[] = $btn->toArray();
            } elseif (is_string($btn)) {
                // تبدیل رشته متنی ساده به فرمت دکمه استاندارد
                // { "text": "Button Name" }
                $formattedRow[] = ['text' => $btn];
            } elseif (is_array($btn)) {
                // آرایه خام
                $formattedRow[] = $btn;
            } else {
                // اگر نوع ناشناخته بود، تلاش برای کست به آرایه
                $formattedRow[] = (array) $btn;
            }
        }

        if (!empty($formattedRow)) {
            $this->fluentRows[] = $formattedRow;
        }

        return $this;
    }

    /**
     * تنظیم قابلیت تغییر اندازه کیبورد (Resize).
     * معمولاً برای جلوگیری از اشغال کل صفحه موبایل استفاده می‌شود.
     *
     * @param bool $active
     * @return static
     */
    public function resize(bool $active = true): static
    {
        $this->resizeKeyboard = $active;
        return $this;
    }

    /**
     * تنظیم قابلیت یک‌بار مصرف بودن (One Time).
     * پس از کلیک، کیبورد مخفی می‌شود.
     *
     * @param bool $active
     * @return static
     */
    public function oneTime(bool $active = true): static
    {
        $this->oneTimeKeyboard = $active;
        return $this;
    }

    /**
     * تنظیم قابلیت انتخابی (Selective).
     * کیبورد فقط به کاربری که پیامش ریپلای شده یا منشن شده نمایش داده می‌شود.
     *
     * @param bool $active
     * @return static
     */
    public function selective(bool $active = true): static
    {
        $this->selective = $active;
        return $this;
    }

    /**
     * تنظیم قابلیت دائمی بودن (Persistent).
     * (از اسنیپت ۱ اضافه شد - ممکن است در برخی نسخه‌های API روبیکا/تلگرام کاربرد داشته باشد).
     *
     * @param bool $active
     * @return static
     */
    public function persistent(bool $active = true): static
    {
        $this->persistent = $active;
        return $this;
    }

    /**
     * تنظیم متن راهنما (Placeholder) در اینپوت فیلد.
     *
     * @param string $text
     * @return static
     */
    public function placeholder(string $text): static
    {
        // اطمینان از اینکه متن طولانی نباشد (محدودیت تلگرام ۶۴ کاراکتر است، اما اینجا سخت‌گیری نمی‌کنیم)
        $this->placeholder = $text;
        return $this;
    }

    /**
     * آلیاس (نام مستعار) برای متد placeholder جهت سازگاری با اسنیپت ۱.
     *
     * @param string $text
     * @return static
     */
    public function inputPlaceholder(string $text): static
    {
        return $this->placeholder($text);
    }

    // -------------------------------------------------------------------
    // هلپرهای دکمه‌های خاص (Contact & Location)
    // این متدها طبق درخواست، در سطح کلاس کیبورد تجمیع شده‌اند.
    // منطق: افزودن یک ردیف جدید تک‌دکمه‌ای با قابلیت درخواست.
    // -------------------------------------------------------------------

    /**
     * افزودن سریع دکمه درخواست شماره تماس.
     *
     * @param string $text متن دکمه
     * @return static
     */
    public function requestContact(string $text = 'ارسال شماره تماس'): static
    {
        // ساختار استاندارد دکمه درخواست تماس
        $button = [
            'text' => $text,
            'request_contact' => true
        ];
        
        // افزودن به عنوان یک ردیف جدید
        $this->fluentRows[] = [$button];
        
        return $this;
    }

    /**
     * افزودن سریع دکمه درخواست موقعیت مکانی.
     *
     * @param string $text متن دکمه
     * @return static
     */
    public function requestLocation(string $text = 'ارسال موقعیت مکانی'): static
    {
        // ساختار استاندارد دکمه درخواست لوکیشن
        $button = [
            'text' => $text,
            'request_location' => true
        ];

        // افزودن به عنوان یک ردیف جدید
        $this->fluentRows[] = [$button];

        return $this;
    }

    /**
     * خروجی نهایی آرایه جهت ارسال به API.
     * این متد تمام پراپرتی‌های اختصاصی و داده‌های والد را ترکیب می‌کند.
     *
     * @return array
     */
    public function toArray(): array
    {
        // 1. دریافت داده‌های پایه از کلاس والد (اگر چیزی ست شده باشد)
        // معمولاً کلاس Keypad والد ممکن است خروجی متفاوتی داشته باشد،
        // اما ما اینجا "برتری" ساختار خودمان را اعمال می‌کنیم.
        $baseData = method_exists(parent::class, 'toArray') ? parent::toArray() : [];

        // 2. ساختار اصلی Reply Keyboard
        // توجه: کلید استاندارد برای دکمه‌های ریپلای 'keyboard' است، نه 'inline_keyboard'.
        $data = [
            'keyboard' => $this->fluentRows,
            'resize_keyboard' => $this->resizeKeyboard,
            'one_time_keyboard' => $this->oneTimeKeyboard,
            'selective' => $this->selective,
        ];

        // 3. افزودن ویژگی‌های شرطی
        if ($this->persistent) {
            $data['is_persistent'] = true;
        }

        if (!empty($this->placeholder)) {
            $data['input_field_placeholder'] = $this->placeholder;
        }

        // 4. ادغام هوشمند: اگر والد دیتای اضافی تولید کرده (که ما اورراید نکرده‌ایم)، نگهش دار.
        // اما دیتای ما (مثل 'keyboard') اولویت دارد.
        return array_merge($baseData, $data);
    }
}
