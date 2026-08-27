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

use RubikaBot\Keyboard\Keypad as BaseKeypad;
use RubikaBot\Keyboard\Button as BaseButton;     // کلاس والد دکمه (جهت تایپ‌هینتینگ و سازگاری با RubikaBot)
use KrubiK\Keyboard\PowerButton;       // کلاس تقویت‌شده دکمه ما
use Closure;
use Illuminate\Contracts\Support\Arrayable;

/**
 * کلاس نهایی و قدرتمند مدیریت کیبورد شیشه‌ای (Inline Keyboard).
 * 
 * این کلاس با ارث‌بری از `RubikaBot\Keyboard\Keypad`، تمام قابلیت‌های کلاس پایه را حفظ کرده
 * و آن‌ها را با الگوی طراحی Fluent Interface، محاسبات هوشمند عرض (Smart Width Calculation)
 * و مدیریت صف (Queue Management) ترکیب می‌کند.
 * 
 * ویژگی‌های کلیدی تجمیع‌شده:
 * 1. **Hybrid Row Method**: پشتیبانی همزمان از متد `row()` کلاس والد (Legacy) و متد مدرن (Fluent).
 * 2. **Deferred Execution**: تمام تبدیل‌ها (به آرایه) و اعمال RTL تا لحظه فراخوانی `toArray()` به تعویق می‌افتند.
 * 3. **Smart Width Calculation**: چینش خودکار دکمه‌ها بر اساس عرض (Width) آن‌ها (حتی اعشاری).
 * 4. **Chunking**: قابلیت تقسیم دکمه‌ها به گروه‌های N تایی.
 * 5. **RTL Support**: پشتیبانی سراسری از راست-چین کردن دکمه‌ها (شامل ردیف‌های قدیمی و جدید).
 * 6. **Conditional Logic**: متد `when` برای ساخت شرطی کیبورد.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class Keyboard extends BaseKeypad implements Arrayable
{
    /**
     * مخزن دکمه‌هایی که هنوز پردازش نشده‌اند.
     * این دکمه‌ها در "صف انتظار" هستند تا یا توسط متد `chunk` گروه‌بندی شوند
     * یا توسط متد `processPendingButtons` بر اساس عرضشان چیده شوند.
     * 
     * @var array<int, BaseButton|PowerButton>
    */
    protected array $pendingButtons = [];

    /**
     * مخزن ردیف‌های اختصاصی بخش Fluent.
     * نکته حیاتی: ردیف‌های بخش Legacy (که با `$keypad->row()->add(...)` ساخته می‌شوند)
     * در پراپرتی‌های `private/protected` کلاس والد (`BaseKeypad`) ذخیره می‌شوند.
     * ما ردیف‌های جدید را اینجا نگه می‌داریم و در خروجی نهایی با والد ادغام می‌کنیم.
     * 
     * @var array<int, array>
    */
    protected array $fluentRows = [];

    /** 
     * آیا چینش دکمه‌ها باید معکوس (راست به چپ) شود؟
     * این تنظیم به صورت سراسری روی تمام ردیف‌ها اعمال می‌شود.
     * @var bool 
    */
    protected bool $isRightToLeft = false;

    /**
     * سازنده استاتیک برای شروع سریع زنجیره (Factory Method).
     * 
     * @return static
    */
    public static function make(): static
    {
        return new static();
    }

    // =================================================================================
    // 1. HYBRID ROW METHOD (قلب تپنده سازگاری - The Compatibility Core)
    // =================================================================================

    /**
     * متد هوشمند و هیبریدی افزودن ردیف.
     * این متد تشخیص می‌دهد که کاربر قصد استفاده از روش قدیمی را دارد یا روش جدید.
     * 
     * حالت ۱ (Legacy): اگر ورودی خالی باشد (`null`)، رفتار والد را تقلید می‌کند.
     * در این حالت، متد `parent::row()` صدا زده می‌شود که معمولاً یک شیء `KeypadRow`
     * برمی‌گرداند تا کاربر بتواند متد `add()` را روی آن زنجیره کند.
     * 
     * حالت ۲ (Fluent): اگر آرایه یا کلوژر پاس داده شود، به سبک مدرن رفتار می‌کند.
     * دکمه‌ها را به لیست `fluentRows` اضافه کرده و آبجکت کیبورد (`$this`) را برمی‌گرداند
     * تا زنجیره متدهای کیبورد ادامه یابد.
     * 
     * @param array|Closure|null $buttons آرایه‌ای از دکمه‌ها، کلوژر، یا null برای حالت Legacy.
     * @return mixed (static در حالت Fluent، و خروجی والد در حالت Legacy)
    */
    public function hybridRow(array|Closure|null $buttons = null): mixed
    {
        // --- MODE 1: Legacy Support ($keypad->hybridRow()->add(...)) ---
        if ($buttons === null) {
            // قبل از تحویل کنترل به والد، اگر دکمه‌ای در صف انتظار (Pending) داریم،
            // باید آن را پردازش کنیم تا ترتیب بصری دکمه‌ها در کیبورد حفظ شود.
            $this->processPendingButtons();

            // فراخوانی متد والد. فرض بر این است که والد یک شیء مدیریت ردیف برمی‌گرداند
            // و خودش آن ردیف را در لیست داخلی‌اش ذخیره می‌کند.
            return parent::row();
        }

        // --- MODE 2: Fluent Support ($keypad->hybridRow([...])) ---
        
        // همیشه قبل از افزودن ردیف دستی جدید، صف انتظار دکمه‌های تکی قبلی را پردازش می‌کنیم.
        $this->processPendingButtons();

        // پردازش کلوژر (اگر کاربر تابعноمن پاس داده باشد)
        if ($buttons instanceof Closure) {
            $buttons = $buttons($this);
        }

        // اگر آرایه دکمه‌ها معتبر باشد، آن را ذخیره می‌کنیم.
        if (is_array($buttons) && !empty($buttons)) {
            // نکته: ما اینجا هنوز دکمه‌ها را به فرمت نهایی (آرایه) تبدیل نمی‌کنیم.
            // آبجکت‌های دکمه را خام نگه می‌داریم تا در toArray بتوانیم تغییرات نهایی را اعمال کنیم.
            $this->fluentRows[] = $buttons;
        }

        return $this;
    }

    // =================================================================================
    // 2. BUTTON MANAGEMENT & QUEUEING (مدیریت دکمه‌ها و صف)
    // =================================================================================

    /**
     * افزودن مجموعه‌ای از دکمه‌ها به صف انتظار.
     * این دکمه‌ها بعداً توسط الگوریتم `processWidths` یا `chunk` چیده می‌شوند.
     * 
     * @param array $buttons آرایه‌ای از آبجکت‌های Button
     * @return static
    */
    public function buttons(array $buttons): static
    {
        foreach ($buttons as $button) {
            // پشتیبانی از هر دو نوع دکمه (قدیمی RubikaBot و جدید KrubiK)
            // چون KrubiK\Button از RubikaBot\Button ارث می‌برد، این شرط برای هر دو کار می‌کند.
            if ($button instanceof PowerButton || $button instanceof BaseButton) {
                $this->pendingButtons[] = $button;
            }
        }
        return $this;
    }

    /**
     * افزودن یک دکمه تکی به صف انتظار و بازگرداندن خودِ دکمه (Fluent Button).
     * این متد زنجیره `Keyboard` را قطع می‌کند و آبجکت `Button` را برمی‌گرداند
     * تا بتوانید روی دکمه تنظیمات (مثل رنگ، اکشن و ...) را اعمال کنید.
     * 
     * @param string $text متن دکمه
     * @return PowerButton
    */
    public function button(string $text): PowerButton
    {
        // اینجا حتماً از دکمه پیشرفته (KrubiK) استفاده می‌کنیم تا تمام متدهای جدید در دسترس باشند.
        $button = PowerButton::make($text);
        $this->pendingButtons[] = $button;
        return $button;
    }

    /**
     * افزودن یک دکمه و قرار دادن فوری آن در یک ردیف جدید (Single Row Button).
     * تفاوت این متد با `button()` این است که زنجیره `Keyboard` را حفظ می‌کند (`return $this`)
     * و دکمه را فوراً در یک ردیف اختصاصی قرار می‌دهد.
     * 
     * @param string $text متن دکمه
     * @param Closure|null $callback کلوژر اختیاری برای تنظیم ویژگی‌های دکمه
     * @return static
    */
    public function addRowButton(string $text, ?Closure $callback = null): static
    {
        // ۱. پردازش صف قبلی
        $this->processPendingButtons();

        // ۲. ساخت دکمه
        $btn = PowerButton::make($text);
        if ($callback) {
            $callback($btn);
        }
        
        // ۳. افزودن مستقیم به لیست ردیف‌های فلوئنت
        $this->fluentRows[] = [$btn];
        
        return $this;
    }

    // =================================================================================
    // 3. LAYOUT MODIFIERS (تنظیم‌کننده‌های چیدمان)
    // =================================================================================

    /**
     * تقسیم دکمه‌های موجود در صف انتظار به دسته‌های N تایی (Chunking).
     * اگر این متد صدا زده شود، منطق Smart Width نادیده گرفته شده و دکمه‌ها 
     * صرفاً بر اساس تعداد در هر ردیف چیده می‌شوند.
     * 
     * @param int $size تعداد دکمه در هر ردیف
     * @return static
    */
    public function chunk(int $size): static
    {
        if (empty($this->pendingButtons)) {
            return $this;
        }

        $chunks = array_chunk($this->pendingButtons, $size);
        foreach ($chunks as $chunk) {
            // هر تکه (Chunk) تبدیل به یک ردیف فلوئنت می‌شود.
            $this->fluentRows[] = $chunk;
        }

        // صف را خالی می‌کنیم چون همه دکمه‌ها تبدیل به ردیف شدند.
        $this->pendingButtons = [];
        return $this;
    }

    /**
     * فعال/غیرفعال کردن حالت راست-چین (RTL - Right To Left).
     * وقتی این متد صدا زده شود، تمام ردیف‌ها (چه لگاسی و چه فلوئنت) 
     * در خروجی نهایی معکوس خواهند شد.
     * 
     * @param bool $active وضعیت فعال بودن
     * @return static
    */
    public function rightToLeft(bool $active = true): static
    {
        $this->isRightToLeft = $active;
        return $this;
    }
    /**
     * @param bool $active وضعیت فعال بودن
     * @return static
    */
    public function rtl(bool $active = true): static
    {
        return $this->rightToLeft($active);
    }
    public function ltr(bool $active = true): static
    {
        return $this->rightToLeft(!$active);
    }
    public function leftToRight(bool $active = true): static
    {
        return $this->rightToLeft(!$active);
    }


    /**
     * اجرای شرطی کد (Conditional Builder).
     * این متد برای زمانی مفید است که می‌خواهید بخشی از کیبورد فقط در شرایط خاصی
     * (مثلاً فقط برای ادمین‌ها) ساخته شود.
     * 
     * @param bool|Closure $condition شرط (می‌تواند مقدار بولی یا تابع باشد)
     * @param Closure $callback تابعی که در صورت صحیح بودن شرط اجرا می‌شود (آبجکت کیبورد پاس داده می‌شود)
     * @return static
    */
    public function when(bool|Closure $condition, Closure $callback): static
    {
        $value = $condition instanceof Closure ? $condition($this) : $condition;
        
        if ($value) {
            $callback($this);
        }
        
        return $this;
    }

    // =================================================================================
    // 4. CORE LOGIC (هسته محاسباتی)
    // =================================================================================

    /**
     * این متد "مغز" محاسباتی کلاس برای چیدمان هوشمند است.
     * دکمه‌های موجود در صف انتظار (`pendingButtons`) را پیمایش کرده و بر اساس
     * خاصیت `width` (عرض) آن‌ها، سعی می‌کند ردیف‌ها را تا سقف ۱ (۱۰۰٪) پر کند.
    */
    protected function processPendingButtons(): void
    {
        if (empty($this->pendingButtons)) {
            return;
        }

        $currentRow = [];
        $currentWidth = 0.0;
        // تلورانس برای رفع خطای محاسبات اعشاری (Floating Point Precision Issues)
        $tolerance = 0.001;

        foreach ($this->pendingButtons as $button) {
            // تلاش برای دریافت عرض دکمه.
            // اگر دکمه از نوع جدید باشد متد getWidth دارد، وگرنه پیش‌فرض ۱.۰ (تمام عرض) در نظر گرفته می‌شود.
            $w = 1.0;
            if (method_exists($button, 'getWidth')) {
                $w = $button->getWidth();
            }

            // اگر (عرض فعلی ردیف + عرض دکمه جدید) از ۱۰۰٪ بیشتر شد، ردیف فعلی پر شده است.
            if (($currentWidth + $w) > (1.0 + $tolerance)) {
                // ردیف تکمیل شده را به لیست ردیف‌ها اضافه می‌کنیم.
                $this->fluentRows[] = $currentRow;
                
                // ریست کردن متغیرها برای ردیف بعدی
                $currentRow = [];
                $currentWidth = 0.0;
            }
            
            $currentRow[] = $button;
            $currentWidth += $w;
        }

        // اگر دکمه‌هایی در آخرین ردیف ناقص باقی مانده‌اند، آن‌ها را هم اضافه می‌کنیم.
        if (!empty($currentRow)) {
            $this->fluentRows[] = $currentRow;
        }
        
        // پاکسازی صف انتظار
        $this->pendingButtons = [];
    }

    // =================================================================================
    // 5. FINAL OUTPUT & MERGING (خروجی نهایی و تجمیع)
    // =================================================================================

    /**
     * تبدیل نهایی به ساختار استاندارد آرایه برای API روبیکا.
     * اینجا نقطه تلاقی تمام منطق‌هاست:
     * 1. صف‌ها پردازش می‌شوند.
     * 2. ردیف‌های والد (Legacy) و ردیف‌های جدید (Fluent) ادغام می‌شوند.
     * 3. آبجکت‌ها به آرایه تبدیل می‌شوند.
     * 4. تنظیم RTL اعمال می‌شود.
     * 
     * @return array
    */
    public function toArray(): array
    {
        // ۱. گام اول: اطمینان از اینکه هیچ دکمه‌ای در صف انتظار جا نمانده است.
        $this->processPendingButtons();

        // ۲. گام دوم: دریافت خروجی والد.
        // والد ردیف‌های خودش (که با روش قدیمی اضافه شده‌اند) را پردازش کرده و برمی‌گرداند.
        // ساختار احتمالی: ['rows' => [ ... ]]
        $parentData = parent::toArray();
        $legacyRows = $parentData['rows'] ?? [];

        // ۳. گام سوم: فرمت‌دهی ردیف‌های Fluent خودمان.
        // باید هر آبجکت دکمه را به آرایه تبدیل کنیم.
        $fluentRowsFormatted = [];
        foreach ($this->fluentRows as $rowButtons) {
            // نگاشت (Map) روی اعضای ردیف
            $mappedRow = array_map(function($btn) {
                // اولویت ۱: اگر دکمه متد toArray دارد (چه دکمه والد، چه دکمه ما)
                if (is_object($btn) && method_exists($btn, 'toArray')) {
                    return $btn->toArray();
                }
                // اولویت ۲: اگر دکمه شیء است اما toArray ندارد (صرفاً کست به آرایه)
                if ($btn instanceof PowerButton || $btn instanceof BaseButton) {
                    return (array)$btn;
                }
                // اولویت ۳: اگر از قبل آرایه یا چیز دیگری است
                return $btn;
            }, $rowButtons);
            
            // ساختار استاندارد هر ردیف در روبیکا: یک آبجکت که کلید buttons دارد.
            $fluentRowsFormatted[] = ['buttons' => $mappedRow];
        }

        // ۴. گام چهارم: ادغام ردیف‌ها.
        // تصمیم: ردیف‌های Legacy اول می‌آیند، سپس ردیف‌های Fluent.
        $allRows = array_merge($legacyRows, $fluentRowsFormatted);

        // ۵. گام پنجم: اعمال سراسری راست-چین (RTL).
        // این حلقه روی *تمام* ردیف‌ها (حتی ردیف‌هایی که از والد آمدند) اجرا می‌شود.
        if ($this->isRightToLeft) {
            foreach ($allRows as &$rowStructure) {
                // بررسی می‌کنیم که آیا ساختار ردیف معتبر است (کلید buttons دارد؟)
                if (isset($rowStructure['buttons']) && is_array($rowStructure['buttons'])) {
                    $rowStructure['buttons'] = array_reverse($rowStructure['buttons']);
                }
            }
        }

        // ۶. بازگشت آرایه نهایی.
        // این خروجی جایگزین خروجی والد می‌شود و شامل همه چیز است.
        return ['rows' => $allRows];
    }
}
