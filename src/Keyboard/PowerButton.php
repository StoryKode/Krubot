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

use RubikaBot\Keyboard\Button as VanguardButton;
use RubikaBot\Keyboard\ButtonLink;
use KrubiK\Enums\ButtonType; // obey(New World Order)
use KrubiK\DTOs\SelectionItem;
use KrubiK\Arcane\InteractsWithLockedProperties;
use Illuminate\Contracts\Support\Arrayable;
// use ValueError;

/**
 * THE ULTIMATE `PowerButton` CLASS. v7.64
 *
 * This PowerButton Class is the result of the "Grand Unification Operation".
 * It merges all legacy and modern buttonitation approaches into a single, intelligent, and strictly typed entity.
 * It respects PHP 8.2 standards while maintaining 100% backward compatibility logic and improved performance.
 * 
 * 🌋 🎼⛈️.🌐†🧪🍄† 🔄|♻️+➡️“Vegetarians” [2K3] 🌋
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class PowerButton implements Arrayable //  extends VanguardButton
{
    use InteractsWithLockedProperties; // Uses PhantomShell Capabilities to Inject into VanGuard's

    /**
     * مخزن داده‌های اضافی (Payload) که ممکن است کلاینت‌های خاص یا آپدیت‌های آینده نیاز داشته باشند.
     * (ترکیبی از $payload اسنیپت اول و $extraPayload اسنیپت دوم)
     * 
     * @var array
    */
    protected array $extraPayload = [];

    /**
     * مخزن داده‌های اکشن
     *
     * مخزن داده‌های اکشن (Callback Data)
    */
    protected array $callbackData = [];

    /**
     * عرض دکمه
    */
    protected float $width = 1.0;

    /**
     * @const string JalaliCalendar Represents the code for the Jalali Calendar type identification.
    */
    const JalaliCalendar = 'DatePersian';
    const PersianCalendar = 'DatePersian';

    /**
     * @const string GregorianCalendar Represents the code for the Gregorian Calendar type identification.
    */
    const GregorianCalendar = 'DateGregorian';

    /**
     * نوع دکمه به صورت Enum (برای استفاده داخلی و مدرن)
    */
    protected ?ButtonType $buttonTypeEnum = null;

    /**
     * سازنده هوشمند و یکپارچه (Grand Unified Constructor).
     *
     * این متد جایگزین __construct, __construct2 و __construct3 شده است.
     * تمامی ورودی‌های ممکن را می‌پذیرد و هوشمندانه پردازش می‌کند.
     *
     * سازنده هوشمند با حفظ ارث‌بری
     * توجه: ترتیب استاندارد ($text, $actionId, $type) است.
     *
     * سازنده مدرن با استفاده از PHP 8 Property Promotion.
     * ما نوع ورودی $type را به Enum ارتقا دادیم تا امنیت تضمین شود.
     *
     * @param string $text متن دکمه
     * @param string|null $actionId شناسه اکشن
     * @param string|ButtonType $type نوع دکمه (هم رشته و هم Enum پذیرفته می‌شود)
     * @param array $payload داده‌های اضافی (جایگزین $extraPayload در نسخه‌های قبلی)
     * @param float $width عرض دکمه
    */
    public function __construct(
        string $text = '',
        ?string $actionId = null,
        string|ButtonType $type = 'Simple', // Unified Type
        array $payload = [], // Unified Payload container
        float $width = 1.0
    ) {
        // 1. Resolve Type Logic
        $typeString = $type instanceof ButtonType ? $type->value : $type;
        $this->buttonTypeEnum = $type instanceof ButtonType ? $type : ButtonType::tryFrom($typeString) ?? ButtonType::Simple;

        // 2. Parent Construction (Legacy Compatibility)
        // فراخوانی والد برای حفظ سازگاری با کتابخانه پایه
        // نکته: مقدار stringِ اینام را به والد پاس می‌دهیم
        /// parent::__construct($text, $actionId, $typeString);
        $this->unlock('*'); // Raw Access To Vanuard's Button Heart

        // 3. Logic Preservation from __construct1
        if (!empty($text)) {
            $this->text($text);
        }

        // 4. Logic Preservation from __construct2 & 3 (Payload & Width)
        // مدیریت متمرکز Payload
        if (!empty($payload)) {
            $this->extraPayload = $payload;
        }
        
        $this->width = $width;
    }

    /**
     * متد پایه برای شروع زنجیره
     *
     * نقطه شروع ساخت دکمه (Factory Method)
     *
     * متد استاتیک با بازگشت دقیق (Return Type)
    */
    public static function make(string $text): static
    {
        return new static($text);
    }

    // ------------------------------------------------------------------
    // متدهای فکتوری پیشرفته (UNIFIED FACTORY METHODS)
    // ------------------------------------------------------------------

    /**
     * ساخت دکمه ساده (Simple Button).
     *
     * متدهای فکتوری با DX انفجاری (Typed Arguments).
     * تجمیع شده از: simple, simple2.
     *
     * @param string $id شناسه دکمه
     * @param string $text متن دکمه
     * @return static
     */
    public static function simple(string $id, string $text): static
    {
        // استفاده از Enum در داخل برای مدرن‌سازی، اما خروجی نهایی استاندارد است
        return new static($text, $id, ButtonType::Simple);
    }

    /**
     * ساخت دکمه انتخابی (Selection Button).
     *
     * این متد "ابَرمتد" (Super Method) است که جایگزین selection, selection2, selection3, selection4, selection5 شده است.
     * هم آرایه خام می‌پذیرد (Legacy) و هم DTO های مدرن (SelectionItem).
     *
     * متد Selection هیبریدی.
     *
     * متد Selection جدید با ورودی کاملاً کنترل شده (Strict Typed).
     *
     * ساخت دکمه انتخابی (Selection)
     * پارامترها کاملاً شفاف و Type-Hinted هستند.
     *
     * متد Selection با DX فضایی
     * اینجا ما آرایه کثیف نمی‌سازیم، مستقیم و تمیز کار می‌کنیم.
     *
     * @param string $id شناسه انتخاب
     * @param string $title عنوان لیست
     * @param array $items لیست آیتم‌ها (آرایه ساده یا آرایه SelectionItem)
     * @param bool $multi انتخاب چندگانه؟
     * @param int $columns تعداد ستون‌ها
     * @return static
    */
    public static function selection(
        string $id,
        string $title,
        array $items,
        bool $multi = false,
        int $columns = 1
    ): static {
        // 1. پردازش هوشمند آیتم‌ها با حفظ سازگاری (Logic from selection2x & sl3t)
        // گارانتی می‌کنیم که داده‌ها ولید هستند
        $formattedItems = array_map(function ($item) {
            return match(true) {
                // حالت مدرن: استفاده از متد داخلی DTO
                $item instanceof SelectionItem => $item->toArray(),

                // حالت سنتی (Legacy): عبور دادن آرایه خام (با اعتماد به دولوپر)
                is_array($item) => $item,

                // حالت غیرمجاز: جلوگیری از ورود داده‌های پرت (Logic from selection3 error handling)
                // اگر دولوپر اشتباه کرد، همون لحظه مچش رو می‌گیریم (DX بالا)
                default => throw new \InvalidArgumentException(
                    'Item must be an array or instance of SelectionItem.'
                ),
            };
        }, $items);

        // 2. ساخت آبجکت با استفاده از متد withPayload (Logic from selection4 & 5)
        return (new static($title, $id, ButtonType::Selection))
            ->withPayload('button_selection', [
                'selection_id' => $id,
                'items' => $formattedItems,
                'is_multi_selection' => $multi,
                'columns_count' => $columns,
                'title' => $title,
            ]);
    }

    /**
     * ساخت دکمه تقویم (Calendar Button).
     *
     * یکپارچه‌سازی هیبریدی (The Hybrid Integration).
     * تجمیع شده از: calendar, calendar2, calendarx2, calendar3.
     * پشتیبانی از رشته ساده، Enum یا آبجکت کانفیگ.
     *
     * متد تقویم با پشتیبانی از Config Object.
     *
     * @param string $id شناسه
     * @param string $title عنوان
     * @param string|mixed $configOrType نوع تقویم (Jalali/Gregorian) یا آبجکت CalendarConfig
     * @param string|null $min حداقل سال (فقط در حالت Legacy)
     * @param string|null $max حداکثر سال (فقط در حالت Legacy)
     * @return static
    */
    public static function calendar(
        string $id,
        string $title,
        mixed $configOrType, // Hybrid Input (String or Config Object)
        ?string $min = '1360',
        ?string $max = '1405'
    ): static {
        
        // Logic from calendar2: Match & Normalize Payload
        $payload = match(true) {
            // 1. Modern Way: همه چیز داخل یک آبجکت تمیز است (instanceof check assumed if class exists)
            is_object($configOrType) && method_exists($configOrType, 'toArray') => $configOrType->toArray(),

            // 2. Legacy Way: ساختن دستی آرایه از روی پارامترهای جداگانه
            is_string($configOrType) => [
                'type' => $configOrType, // e.g., 'Jalali'
                'min_year' => $min ?? '1360',
                'max_year' => $max ?? '1405', // Logic preserved from calendarx2() default
                'title' => $title
            ],
            
            default => [
                'type' => 'Jalali',
                'min_year' => $min,
                'max_year' => $max,
                'title' => $title
            ]
        };

        return (new static($title, $id, ButtonType::Calendar))
            ->withPayload('button_calendar', $payload);
    }

    /**
     * ساخت دکمه انتخاب عدد (Number Picker).
     *
     * تجمیع شده از: numberPicker, numberPicker2.
    */
    public static function numberPicker(
        string $id,
        string $title,
        int $min,
        int $max,
        ?int $default = null
    ): static {
        return (new static($title, $id, ButtonType::NumberPicker))
            ->withPayload('button_number_picker', [
                'min_value' => $min,
                'max_value' => $max,
                'default_value' => $default,
                'title' => $title,
            ]);
    }

    /**
     * ساخت دکمه انتخاب متن (String Picker).
     *
     * تجمیع شده از: stringPicker, stringPicker2.
    */
    public static function stringPicker(
        string $id,
        string $title,
        array $items,
        ?string $default = null
    ): static {
        return (new static($title, $id, ButtonType::StringPicker))
            ->withPayload('button_string_picker', [
                'items' => $items,
                'default_value' => $default,
                'title' => $title,
            ]);
    }

    /**
     * ساخت دکمه موقعیت مکانی (Location).
     * 
     * تجمیع شده از متد location.
    */
    public static function location(string $id, string $title, string $type = 'Picker'): static
    {
        return (new static($title, $id, ButtonType::Location ?? 'Location')) // Handle enum if exists or string
            ->withPayload('button_location', [
                'type' => $type,
                'title' => $title,
            ]);
    }

    /**
     * ساخت دکمه پرداخت (Payment).
     *
     * پیاده‌سازی سایر متدهای فکتوری با همین الگوی تمیز...
     * تجمیع شده از: payment, payment2.
    */
    public static function payment(string $id, string $title): static
    {
        return new static($title, $id, ButtonType::Payment);
    }

    /**
     * ساخت دکمه لینک (Link).
     *
     * متد لینک با پشتیبانی از چندریختی (Polymorphism).
     * این متد جایگزین link و link2 شده است.
     * هم رشته URL ساده، هم آبجکت ButtonLink قدیمی و هم LinkTarget مدرن را می‌پذیرد.
     *
     * @param string $id شناسه دکمه
     * @param string $title عنوان دکمه
     * @param mixed $target هدف لینک (URL String, ButtonLink Object, LinkTarget Object)
     * @param string|null $type نوع لینک (فقط برای حالت Legacy استفاده می‌شود)
     * @return static
    */
    public static function link(
        string $id,
        string $title,
        mixed $target, // ورودی هیبریدی (Url String یا LinkTarget Object یا ButtonLink)
        ?string $type = null // ورودی اختیاری برای سازگاری با متد link قدیمی
    ): static {
        
        // استفاده از ثابت‌های کلاس والد یا رشته‌های خام در صورت عدم دسترسی (Logic from original link method)
        $urlTypeConst = defined('\RubikaBot\Types\ButtonLinkType::URL') ? \RubikaBot\Types\ButtonLinkType::URL : 'Url'; // Why ??? Cause it Prevents Execution-Interrupt if 'RubikaBot' isn't well-configured/installed on this env.
        $joinTypeConst = defined('\RubikaBot\Types\ButtonLinkType::JoinChannel') ? \RubikaBot\Types\ButtonLinkType::JoinChannel : 'JoinChannel';

        $payload = match(true) {
            // 1. Modern: هر کلاسی که LinkTarget را ایمپلمنت کرده باشد (Logic from link2)
            is_object($target) && method_exists($target, 'toPayload') => $target->toPayload(),

            // 2. Legacy Object: ButtonLink (Logic from original link method)
            $target instanceof ButtonLink => (function() use ($target, $type, $urlTypeConst, $joinTypeConst) {
                // هندل کردن داده‌های لینک بر اساس اسنیپت ارائه شده
                if ($type === $urlTypeConst) {
                    return [
                        'type' => $type,
                        'link_url' => $target->link_url
                    ];
                } elseif ($type === $joinTypeConst) {
                    return [
                        'type' => $type,
                        'joinchannel_data' => $target->joinchannel_data ? [
                            'username' => $target->joinchannel_data->username,
                            'ask_join' => $target->joinchannel_data->ask_join
                        ] : null
                    ];
                }
                return [];
            })(),

            // 3. Simple String: فرض می‌کنیم رشته ورودی، یک URL ساده است (Logic from link2 fallback)
            is_string($target) => ['type' => 'Url', 'link_url' => $target],
            
            default => []
        };

        return (new static($title, $id, ButtonType::Link))
             ->withPayload('button_link', $payload);
    }

    // ------------------------------------------------------------------
    // سایر متدهای ساده (One-Liners)
    // ------------------------------------------------------------------

    public static function cameraImage(string $id, string $title): static
    {
        return new static($title, $id, ButtonType::CameraImage ?? 'CameraImage');
    }

    public static function cameraVideo(string $id, string $title): static
    {
        return new static($title, $id, ButtonType::CameraVideo ?? 'CameraVideo');
    }

    public static function galleryImage(string $id, string $title): static
    {
        return new static($title, $id, ButtonType::GalleryImage ?? 'GalleryImage');
    }

    public static function galleryVideo(string $id, string $title): static
    {
        return new static($title, $id, ButtonType::GalleryVideo ?? 'GalleryVideo');
    }

    public static function file(string $id, string $title): static
    {
        return new static($title, $id, ButtonType::File ?? 'File');
    }

    public static function audio(string $id, string $title): static
    {
        return new static($title, $id, ButtonType::Audio ?? 'Audio');
    }

    public static function recordAudio(string $id, string $title): static
    {
        return new static($title, $id, ButtonType::RecordAudio ?? 'RecordAudio');
    }

    public static function myPhoneNumber(string $id, string $title): static
    {
        return new static($title, $id, ButtonType::MyPhoneNumber ?? 'MyPhoneNumber');
    }

    public static function myLocation(string $id, string $title): static
    {
        return new static($title, $id, ButtonType::MyLocation ?? 'MyLocation');
    }

    public static function textBox(string $id, string $title, string $lineType = 'SingleLine', string $keypadType = 'String'): static
    {
        return (new static($title, $id, ButtonType::TextBox ?? 'TextBox'))
            ->withPayload('button_textbox', [
                'type_line' => $lineType,
                'type_keypad' => $keypadType,
                'title' => $title,
            ]);
    }

    public static function activityPhoneNumber(string $id, string $title): static
    {
        return new static($title, $id, ButtonType::ActivityPhoneNumber ?? 'ActivityPhoneNumber');
    }

    public static function asMLocation(string $id, string $title): static
    {
        return new static($title, $id, ButtonType::AsMLocation ?? 'AsMLocation');
    }

    public static function barcode(string $id, string $title): static
    {
        return new static($title, $id, ButtonType::Barcode ?? 'Barcode');
    }

    // ------------------------------------------------------------------
    // متدهای Fluent (Logic & State) - UNIFIED
    // ------------------------------------------------------------------

    public function text(string $text): static
    {
        $this->text = $text;
        // همگام‌سازی با والد اگر لازم باشد (بسته به نحوه کارکرد RubikaBot)
        $this->forceSetProperty('text', $text);
        return $this;
    }

    public function url(string $url): static
    {
        $this->type = 'Link';
        $this->forceSetProperty('type', 'Link');
        $this->extraPayload['url'] = $url;
        $this->extraPayload['link_data'] = ['url' => $url, 'type' => 'Url'];
        $this->forceSetProperty('url', $url);
        return $this;
    }

    public function webApp(string $url): static
    {
        return $this->url($url)->param('is_web_app', true);
    }

    // ------------------------------------------------------------------
    // متدهای Action/Payload Fluent-Injection
    // ------------------------------------------------------------------

    /**
     * تنظیم اکشن و داده‌های کال‌بک.
     * تجمیع شده از: action, action2, action3.
     *
     * افزودن اکشن دیتا با قابلیت Merge هوشمند.
     *
     * @param string|array|int $data داده اکشن
     * @param string|ButtonType $type نوع دکمه (پیش‌فرض Button)
     * @param bool $append آیا داده‌ها به قبلی اضافه شوند؟
     * @return static
    */
    public function actionOldVer(
        string|array|int $data,
        string|ButtonType $type = 'Simple',
        bool $append = false): static
    {
        // Resolve Type
        $typeString = $type instanceof ButtonType ? $type->value : $type;
        
        $this->type = $typeString;
        $this->forceSetProperty('type', $typeString);

        if (is_array($data)) {
            $this->callbackData = $append ? array_merge($this->callbackData, $data) : $data;
            $finalData = json_encode($this->callbackData, JSON_UNESCAPED_UNICODE);
        } else {
            $finalData = (string) $data;
        }

        $this->extraPayload['action_data'] = $finalData;
        
        // اطمینان از ست شدن ID اگر دکمه ساده است (Logic from original action)
        // اطمینان از ست شدن در والد برای سازگاری (Logic from action2)
        $this->forceSetProperty('action_id', $finalData);
        $this->forceSetProperty('action_data', $finalData);

        return $this;
    }

        /**
     * Set the button action, payload parameters, and layout type dynamically.
     * Simulated method overloading to handle multiple signatures seamlessly.
     *
     * Supported Calling Signatures:
     * 1. action('order', ['id' => 1], 'Simple') -> Dynamic Action with structured parameters
     * 2. action(['action' => 'order', 'id' => 1], 'Simple') -> Raw structured payload
     * 3. action('simple_callback', 'Simple') -> Single raw action key without JSON wrapping
     *
     * @param string|array<string, mixed>|int $data Core action identifier or complete payload array.
     * @param array<string, mixed>|string|ButtonType|null $paramsOrType Parameters array or button type.
     * @param string|ButtonType|bool|null $typeOrAppend Custom button type or append flag.
     * @param bool $append Defines whether to merge with existing callback parameters.
     * @return static
     */
    public function action(
        string|array|int $data,
        mixed $paramsOrType = null,
        mixed $typeOrAppend = null,
        bool $append = false
    ): static {
        // 1. Initialize default values
        $type = 'Simple';
        
        // Ensure callbackData is initialized to avoid type-errors
        if (!isset($this->callbackData) || !is_array($this->callbackData)) {
            $this->callbackData = [];
        }

        // 2. Resolve dynamic $append flag based on argument position
        if (is_bool($typeOrAppend)) {
            $append = $typeOrAppend;
        } elseif (is_bool($paramsOrType)) {
            $append = $paramsOrType;
        }

        // 3. Resolve the ButtonType (supports String backing or Enum structure)
        if ($typeOrAppend instanceof ButtonType || is_string($typeOrAppend)) {
            $type = $typeOrAppend;
        } elseif ($paramsOrType instanceof ButtonType || is_string($paramsOrType)) {
            // Only capture as type if it's not a payload parameter array
            if (!is_array($paramsOrType)) {
                $type = $paramsOrType;
            }
        }

        $finalData = '';

        // 4. Architect and compile the callback payload structure
        if (is_array($data)) {
            // Direct array mapping: action(['route' => 'order', 'id' => 5])
            $this->callbackData = $append ? array_merge($this->callbackData, $data) : $data;
            $finalData = json_encode($this->callbackData, JSON_UNESCAPED_UNICODE);
        } else {
            if (is_array($paramsOrType)) {
                // Structured actions: action('order', ['id' => $product->id])
                $structuredBase = ['action' => $data];
                $this->callbackData = $append
                    ? array_merge($this->callbackData, $structuredBase, $paramsOrType)
                    : array_merge($structuredBase, $paramsOrType);
                $finalData = json_encode($this->callbackData, JSON_UNESCAPED_UNICODE);
            } else {
                // Simple scalar actions: action('show_menu')
                $finalData = (string) $data;
            }
        }

        // 5. Convert enum values to raw string representations if applicable
        $typeString = $type instanceof ButtonType ? $type->value : $type;
        
        $this->type = $typeString;
        $this->forceSetProperty('type', $typeString);

        // 6. Synchronize raw and structured payloads inside the button properties
        $this->extraPayload['action_data'] = $finalData;
        $this->forceSetProperty('action_id', $finalData);
        $this->forceSetProperty('action_data', $finalData);

        return $this;
    }

    public function addActionParam(string $key, mixed $value): static
    {
        if (!isset($this->callbackData) || !is_array($this->callbackData)) {
            $this->callbackData = [];
        }
        $this->callbackData[$key] = $value;
        return $this->action($this->callbackData, $this->type); // Use current type
    }

    /**
     * افزودن پارامتر یا متای اضافی به دکمه.
     * این متد همان کارکرد `param` را دارد و داده‌ها را به `extraPayload` اضافه می‌کند.
     * (کاربرد: افزودن رنگ، سایز، یا متادیتای خاص ارسالی به کلاینت)
     * 
     * متد قدیمی برای افزودن پارامتر (جهت حفظ سازگاری)
     * 
     * @param string $key کلید پارامتر
     * @param mixed $value مقدار پارامتر
     * @return static
    */
    public function param(string $key, mixed $value): static
    {
        $this->extraPayload[$key] = $value;
        return $this;
    }

    /**
     * متد مدرن برای افزودن پارامتر
     * متد کمکی برای افزودن تمیز Payload (جایگزین param قدیمی)
    */
    public function withPayload(string $key, mixed $value): static
    {
        $this->extraPayload[$key] = $value;
        return $this;
    }

    /**
     * متد اختصاصی برای تنظیم مستقیم کل آرایه پی‌لود (در صورت نیاز به تنظیم گروهی).
     * 
     * @param array $payload
     * @return static
    */
    public function setPayload(array $payload): static
    {
        $this->extraPayload = array_merge($this->extraPayload, $payload);
        return $this;
    }

    // ------------------------------------------------------------------
    // متدهای Fluent-WidthSpan
    // ------------------------------------------------------------------

    public function getWidth(): float
    {
        return $this->width;
    }

    public function width(float $width): static
    {
        // Logic 1: Original Clamp
        if ($width < 0.1 || $width > 1.0) {
             $width = $width > 1.0 ? 1.0 : 0.1;
        }
        
        // Logic 2: v2 Compact Logic check (Redundant but preserved as requested)
        // $this->width = ($width < 0.1 || $width > 1.0) ? ($width > 1.0 ? 1.0 : 0.1) : $width;
        
        $this->width = $width;
        return $this;
    }

    /**
     * 📐 سیستم گرید 6 ستونه (Bootstrap-like Grid System for Bots!)
     *
     * سیستم گرید (Grid System) که شما دوست داشتید
     *
     * این متد عرض دکمه را بر اساس سیستم 6 ستونه محاسبه می‌کند.
     * فضای کل ردیف = 6 واحد.
     *
     * @param int $span تعداد ستون‌هایی که دکمه اشغال می‌کند (1 تا 6)
     * @return static
    */
    public function col(int $span): static
    {
        // 1. Validation & Clamping (امنیت در برابر ورودی پرت)
        // اگر کمتر از 1 بود، 1 شود. اگر بیشتر از 6 بود، 6 شود.
        // استفاده از clamp در PHP 8 (تمیزتر و سریعتر)
        $span = max(1, min(6, $span));

        // 2. Calculation (تبدیل به منطق هسته)
        // 6 Columns Total:
        // col(1) = 1/6 = 16.6% = 0.166...
        // col(2) = 2/6 = 33.3% = 0.333...
        // col(3) = 3/6 = 50%   = 0.500 (نیم‌صفحه)
        // col(6) = 6/6 = 100%  = 1.000 (تمام‌صفحه)
        $calculatedWidth = $span / 6.0; // ensure float-precision divide.

        // 3. Delegation (سپردن به متد اصلی)
        return $this->width($calculatedWidth);
    }

    /**
     * خروجی نهایی تجمیع شده (THE ULTIMATE TO_ARRAY).
     *
     * این متد تجمیع شده از toArray و toArray2 و toArray4 است.
     * از سرعت Match در PHP 8 و منطق ادغام هوشمند استفاده می‌کند.
     * تمام منطق‌های قدیمی (تولید ID، فیلد button_text) حفظ شده‌اند.
     *
     * خروجی نهایی با پرفورمنس بالا و منطق Match.
    */
    public function toArray(): array
    {
        // 1. دریافت دیتای پایه والد (فقط در صورت نیاز قطعی - Logic from toArray2)
        $base = []; // method_exists(parent::class, 'toArray') ? parent::toArray() : [];

        // 2. ساخت بیس اگر والد خالی بود (Logic from toArray)
        if (empty($base)) {
            $base = [
                'text' => $this->text ?? '',
                'type' => $this->type ?? 'Simple',
            ];
        }
        
        // 3. Ensure Type Consistency (From Enum or String)
        $currentTypeString = $this->buttonTypeEnum ? $this->buttonTypeEnum->value : $this->type;

        // 4. ساخت دیتای اصلی با سینتکس آرایه جدید (Logic from toArray2)
        $coreData = [
            'text' => $this->text,
            'type' => $currentTypeString,
        ];

        // 5. مدیریت هوشمند ID و Action Data (Merged Logic)
        $actionData = null;
        
        // Priority 1: callbackData array
        if (!empty($this->callbackData)) {
             $actionData = json_encode($this->callbackData, JSON_UNESCAPED_UNICODE); // Take-Care of UTF-8 Orders.
             // Ensure legacy array structure has it (Logic from toArray)
             if (!isset($base['action_data'])) {
                 $base['action_data'] = $actionData;
             }
        } 
        // Priority 2: extraPayload 'action_data'
        elseif (isset($this->extraPayload['action_data'])) {
            $actionData = $this->extraPayload['action_data'];
            $base['action_data'] = $actionData;
        }
        
        if ($actionData) {
            $coreData['action_data'] = $actionData;
        }

        // 6. هندل کردن ID (اولویت: action_id > id > تولید خودکار)
        // Merging logic from toArray (uniqid) and toArray2 (crc32)
        if (!isset($base['id'])) {
            $parentId = $this->forceGetProperty('action_id');
            // Priority: Parent ID > Action Data > URL > Hash (Optimized)
            $coreData['id'] = $parentId
                ?? $actionData
                ?? $this->extraPayload['url']
                ?? 'btn_' . crc32($this->text . microtime()); // CRC32 سریعتر از uniqid است (Logic from toArray2)
                
            $base['id'] = $coreData['id']; // Ensure base has it too
        }

        // 7. اضافه کردن فیلد button_text برای سازگاری با کدهای قدیمی‌تر (Logic from toArray)
        if (!isset($base['button_text']) && isset($base['text'])) {
            $base['button_text'] = $base['text'];
        }

        // 8. بازنویسی کلیدهای خاص با استفاده از match (Logic from toArray2)
        $typeSpecificData = [];
        if ($this->buttonTypeEnum === ButtonType::Link || $currentTypeString === 'Link') {
             $typeSpecificData = ['url' => $this->extraPayload['url'] ?? ''];
        }

        // 9. ادغام نهایی:
        // اولویت با داده‌های جدید (ExtraPayload) است.
        return array_merge(
            $base,              // 1. Legacy
            $coreData,          // 2. Modern
            $typeSpecificData,  // 3. TypeSpecific
            $this->extraPayload // 4. ExtraPayload // most-important for array_merge
        );
    }
}
