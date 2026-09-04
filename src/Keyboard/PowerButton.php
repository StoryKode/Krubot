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

/// use RubikaBot\Keyboard\Button as VanguardButton;
use RubikaBot\Keyboard\ButtonLink;
use KrubiK\Enums\ButtonType; // obey(New World Order)
use KrubiK\Render\RichElements\RichEntity;
use KrubiK\DTOs\SelectionItem;
use KrubiK\Arcane\InteractsWithLockedProperties;
use Illuminate\Contracts\Support\Arrayable;
use KrubiK\Render\Arcane\jQueryStyling;
// use ValueError;

use function KrubiK\Render\Helpers\{
    renderAsText
    /// filterNulls
};


/**
 * THE ULTIMATE `PowerButton` CLASS. v8.5
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
class PowerButton extends RichEntity
{
    use InteractsWithLockedProperties; // Uses PhantomShell Capabilities to Inject into VanGuard's
    
    use jQueryStyling; // Empowers Entity to css(......), addClass(), hasClass(), removeClass()

    protected string|RichEntity $text;

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

    protected string $defaultBTNType = 'Simple'; // 'Simple' is Strict Rubika's Default

    /*
    |--------------------------------------------------------------------------
    | RichButton State
    |--------------------------------------------------------------------------
    */

    protected ?string $buttonStyle = null; // can be either :: “danger”, “success”, “primary”, or “link”

    protected ?string $buttonUrl = null;

    /**
     * WebAppInfo equivalent:
     *
     * [
     *     'url' => 'https://example.com'
     * ]
    */
    protected ?array $webAppData = null;

    /**
     * LoginUrl equivalent.
     *
     * Kept as Arrayable|array|object-friendly payload so PowerButton
     * does not need another DTO class.
    */
    protected mixed $loginUrlData = null;

    protected ?string $switchInlineQueryValue = null;

    protected ?string $switchInlineQueryCurrentChatValue = null;

    /**
     * SwitchInlineQueryChosenChat equivalent.
    */
    protected ?SwitchInlineQueryChosenChat $switchInlineQueryChosenChat = null;

    /**
     * CopyTextButton equivalent.
     *
     * Stored directly as:
     *
     * [
     *     'text' => '...'
     * ]
    */
    protected ?array $copyTextData = null;

    /**
     * DisabledButton equivalent.
     *
     * DisabledButton itself serializes to [].
     *
     * null = not specified
     * true = specified as disabled
    */
    protected ?bool $disabledButton = null;

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
        string|RichEntity $text = '',
        ?string $actionId = null,
        string|ButtonType|null $type = null, // Unified Type
        array $payload = [], // Unified Payload container
        float $width = 1.0
    ) {
        // 1. ResolveNForce Button Type
        $this->setButtonType($type); // also handles null type to default

        // 2. Parent Construction (Legacy Compatibility)
        // فراخوانی والد برای حفظ سازگاری با کتابخانه پایه
        // نکته: مقدار stringِ اینام را به والد پاس می‌دهیم
        /// parent::__construct($text, $actionId, $typeString);
        $this->unlock('*'); // Raw Access To Vanuard's Button Heart

        // 3. Logic Preservation from __construct1
        ///if (!empty($text)) {
        if ($text !== '') {
            $this->text($text);
        }

        if ($actionId !== null) {
            $this->forceSetProperty('action_id', $actionId);
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
    public static function make(
        string|RichEntity|callable $text,
        ?string $actionId = null,
        string|ButtonType|null $type = null, // Unified Type
        ?array $payload = [], // Unified Payload container
        ?float $width = 1.0
    ): self
    {
        // Resolve the text if it's a callable closure.
        return new self(self::resolveContent($text), $actionId, $type, $payload, $width);
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
     * @return self
     */
    public static function simple(string $id, string|RichEntity $text): self
    {
        // استفاده از Enum در داخل برای مدرن‌سازی، اما خروجی نهایی استاندارد است
        return new self($text, $id, ButtonType::Simple);
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
     * @param string|RichEntity $title عنوان لیست
     * @param array $items لیست آیتم‌ها (آرایه ساده یا آرایه SelectionItem)
     * @param bool $multi انتخاب چندگانه؟
     * @param int $columns تعداد ستون‌ها
     * @return static
    */
    public static function selection(
        string $id,
        string|RichEntity $title,
        array $items,
        bool $multi = false,
        int $columns = 1
    ): self {
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
        return (new self($title, $id, ButtonType::Selection))
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
     * @param string|RichEntity $title عنوان
     * @param string|mixed $configOrType نوع تقویم (Jalali/Gregorian) یا آبجکت CalendarConfig
     * @param string|null $min حداقل سال (فقط در حالت Legacy)
     * @param string|null $max حداکثر سال (فقط در حالت Legacy)
     * @return static
    */
    public static function calendar(
        string $id,
        string|RichEntity $title,
        mixed $configOrType, // Hybrid Input (String or Config Object)
        ?string $min = '1360',
        ?string $max = '1405'
    ): self {
        
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

        return (new self($title, $id, ButtonType::Calendar))
            ->withPayload('button_calendar', $payload);
    }

    /**
     * ساخت دکمه انتخاب عدد (Number Picker).
     *
     * تجمیع شده از: numberPicker, numberPicker2.
    */
    public static function numberPicker(
        string $id,
        string|RichEntity $title,
        int $min,
        int $max,
        ?int $default = null
    ): self {
        return (new self($title, $id, ButtonType::NumberPicker))
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
        string|RichEntity $title,
        array $items,
        ?string $default = null
    ): self {
        return (new self($title, $id, ButtonType::StringPicker))
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
    public static function location(string $id, string|RichEntity $title, string $type = 'Picker'): self
    {
        return (new self($title, $id, ButtonType::Location ?? 'Location')) // Handle enum if exists or string
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
    public static function payment(string $id, string|RichEntity $title): self
    {
        return new self($title, $id, ButtonType::Payment);
    }

    /**
     * ساخت دکمه لینک (Link).
     *
     * متد لینک با پشتیبانی از چندریختی (Polymorphism).
     * این متد جایگزین link و link2 شده است.
     * هم رشته URL ساده، هم آبجکت ButtonLink قدیمی و هم LinkTarget مدرن را می‌پذیرد.
     *
     * @param string $id شناسه دکمه
     * @param string|RichEntity $title عنوان دکمه
     * @param mixed $target هدف لینک (URL String, ButtonLink Object, LinkTarget Object)
     * @param string|null $type نوع لینک (فقط برای حالت Legacy استفاده می‌شود)
     * @return self
    */
    public static function link(
        string $id,
        string|RichEntity $title,
        mixed $target, // ورودی هیبریدی (Url String یا LinkTarget Object یا ButtonLink)
        ?string $type = null // ورودی اختیاری برای سازگاری با متد link قدیمی
    ): self {
        
        // استفاده از ثابت‌های کلاس والد یا رشته‌های خام در صورت عدم دسترسی (Logic from original link method)
        $urlTypeConst = defined('\RubikaBot\Types\ButtonLinkType::URL') ? \RubikaBot\Types\ButtonLinkType::URL : 'Url'; // Why ??? Cause it Prevents Execution-Interrupt if 'RubikaBot' isn't well-configured/installed on this env.
        $joinTypeConst = defined('\RubikaBot\Types\ButtonLinkType::JoinChannel') ? \RubikaBot\Types\ButtonLinkType::JoinChannel : 'JoinChannel';

        /// if(!$type)
        ///    $type = $this->defaultBTNType;

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

        return (new self($title, $id, ButtonType::Link))
             ->withPayload('button_link', $payload);
    }

    // ------------------------------------------------------------------
    // سایر متدهای ساده (One-Liners)
    // ------------------------------------------------------------------

    public static function cameraImage(string $id, string|RichEntity $title): self
    {
        return new self($title, $id, ButtonType::CameraImage ?? 'CameraImage');
    }

    public static function cameraVideo(string $id, string|RichEntity $title): self
    {
        return new self($title, $id, ButtonType::CameraVideo ?? 'CameraVideo');
    }

    public static function galleryImage(string $id, string|RichEntity $title): self
    {
        return new self($title, $id, ButtonType::GalleryImage ?? 'GalleryImage');
    }

    public static function galleryVideo(string $id, string|RichEntity $title): self
    {
        return new self($title, $id, ButtonType::GalleryVideo ?? 'GalleryVideo');
    }

    public static function file(string $id, string|RichEntity $title): self
    {
        return new self($title, $id, ButtonType::File ?? 'File');
    }

    public static function audio(string $id, string|RichEntity $title): self
    {
        return new self($title, $id, ButtonType::Audio ?? 'Audio');
    }

    public static function recordAudio(string $id, string|RichEntity $title): self
    {
        return new self($title, $id, ButtonType::RecordAudio ?? 'RecordAudio');
    }

    public static function myPhoneNumber(string $id, string|RichEntity $title): self
    {
        return new self($title, $id, ButtonType::MyPhoneNumber ?? 'MyPhoneNumber');
    }

    public static function myLocation(string $id, string|RichEntity $title): self
    {
        return new self($title, $id, ButtonType::MyLocation ?? 'MyLocation');
    }

    public static function textBox(string $id, string $title, string|RichEntity $lineType = 'SingleLine', string $keypadType = 'String'): self
    {
        return (new self($title, $id, ButtonType::TextBox ?? 'TextBox'))
            ->withPayload('button_textbox', [
                'type_line' => $lineType,
                'type_keypad' => $keypadType,
                'title' => $title,
            ]);
    }

    public static function activityPhoneNumber(string $id, string|RichEntity $title): self
    {
        return new self($title, $id, ButtonType::ActivityPhoneNumber ?? 'ActivityPhoneNumber');
    }

    public static function asMLocation(string $id, string|RichEntity $title): self
    {
        return new self($title, $id, ButtonType::AsMLocation ?? 'AsMLocation');
    }

    public static function barcode(string $id, string|RichEntity $title): self
    {
        return new self($title, $id, ButtonType::Barcode ?? 'Barcode');
    }

    // ------------------------------------------------------------------
    // متدهای Fluent (Logic & State) - UNIFIED
    // ------------------------------------------------------------------

    public function text(string|RichEntity $text): self
    {
        $this->text = $text;
        // همگام‌سازی با والد اگر لازم باشد (بسته به نحوه کارکرد RubikaBot)
        $this->forceSetProperty('text', $text);
        return $this;
    }

    public function url(string $url): self
    {
        $this->setButtonType('Link');
        $this->buttonUrl = $url; // RichButton representation.
        $this->extraPayload['url'] = $url;
        $this->extraPayload['link_data'] = ['url' => $url, 'type' => 'Url'];
        $this->forceSetProperty('url', $url);
        return $this;
    }    

    /*
    |--------------------------------------------------------------------------
    | RichButton - Style
    |--------------------------------------------------------------------------
    */
    public function style(
        ?string $style
    ): self {
        $this->buttonStyle = $style;

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | RichButton - Web App
    |--------------------------------------------------------------------------
    |
    | Ultra-DX:
    |
    | ->webApp('https://example.com')
    |
    | No WebAppInfo object required.
    |
    */
    public function webApp(
        string|array $webApp
    ): self {

        if($this->targetsRubika())
            return $this->url($webApp)->param('is_web_app', true);

        $this->webAppData = is_string($webApp)
            ? ['url' => $webApp]
            : $webApp;

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | RichButton - Login URL
    |--------------------------------------------------------------------------
    |
    | Accepts an array or any object implementing toArray() (including RichEntity)
    |
    */
    public function loginUrl(
        mixed $loginUrl
    ): self {
        $this->loginUrlData = $loginUrl;

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | RichButton - Switch Inline Query
    |--------------------------------------------------------------------------
    */

    public function switchInlineQuery(
        ?string $query
    ): self {
        $this->switchInlineQueryValue = $query;

        return $this;
    }

    public function switchInlineQueryCurrentChat(
        ?string $query
    ): self {
        $this->switchInlineQueryCurrentChatValue = $query;

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | RichButton - Switch Inline Query Chosen Chat
    |--------------------------------------------------------------------------
    |
    | Ultra-DX:
    |
    | ->switchInlineQueryChosenChat(new SwitchInlineQueryChosenChat(
    |     'query' => '...',
    |     'allow_user_chats' => true,
    |     ...
    | ))
    |
    | OR:
    |
    | ->switchInlineQueryChosenChat(
    |     query: '...',
    |     allowUserChats: true,
    | )
    |
    */

    public function switchInlineQueryChosenChat(
        ?SwitchInlineQueryChosenChat $chosenChat = null,
        ?string $query = null,
        ?bool $allowUserChats = null,
        ?bool $allowBotChats = null,
        ?bool $allowGroupChats = null,
        ?bool $allowChannelChats = null,
    ): self {
        /*
        * Explicit Value Object.
        */
        if ($chosenChat !== null) {
            $this->switchInlineQueryChosenChat = $chosenChat;

            return $this;
        }

        /*
        * Ultra-DX named arguments.
        */
        $this->switchInlineQueryChosenChat =
            new SwitchInlineQueryChosenChat(
                query: $query,
                allowUserChats: $allowUserChats,
                allowBotChats: $allowBotChats,
                allowGroupChats: $allowGroupChats,
                allowChannelChats: $allowChannelChats,
            );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | RichButton - Copy Text
    |--------------------------------------------------------------------------
    |
    | Replaces:
    |
    | new CopyTextButton('...')
    |
    | with:
    |
    | ->copyText('...')
    |
    */

    public function copyText(
        string|RichEntity|array|null $text
    ): self {
        if ($text === null) {
            $this->copyTextData = null;

            return $this;
        }

        $this->copyTextData = is_array($text)
            ? $text
            : ['text' => $text];

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | RichButton - Disabled with Convenience Rich API
    |--------------------------------------------------------------------------
    |
    | Replaces:
    |
    | new DisabledButton()
    |
    | with:
    |
    | ->disabled()
    |
    */

    public function disabled(
        bool $disabled = true
    ): self {
        $this->disabledButton = $disabled;

        return $this;
    }

    public function enable(): self
    {
        return $this->disabled(false);
    }

    public function disable(): self
    {
        return $this->disabled(true);
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
        string|ButtonType|null $type = null,
        bool $append = false): self
    {
        $this->setButtonType($type);

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
    ): self {
        // 1. Initialize default values
        $type = $this->defaultBTNType;
        
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

        // 5. ResolveNForce ButtonType Logic
        $this->setButtonType($type);

        // 6. Synchronize raw and structured payloads inside the button properties
        $this->extraPayload['action_data'] = $finalData;
        $this->forceSetProperty('action_id', $finalData);
        $this->forceSetProperty('action_data', $finalData);

        return $this;
    }

    public function addActionParam(string $key, mixed $value): self
    {
        if (!isset($this->callbackData) || !is_array($this->callbackData)) {
            $this->callbackData = [];
        }
        $this->callbackData[$key] = $value;
        return $this->action($this->callbackData, $this->type); // Use current type
    }

    /*
    |--------------------------------------------------------------------------
    | RichButton - Callback Data
    | Hyper-DX Get/Set/Flush
    |--------------------------------------------------------------------------
    */
    public function callbackData(
        string|array|null $callbackData = null
    ): self|array {
        
        // Getter Mode
        if (func_num_args() === 0) {
            $callbackEntry = null;
            $callbackData = $this->callbackData;
            if(count($callbackData) === 1) {
                $temp1 = reset($callbackData); // get first element, even in assoc arrays
                if(is_string($temp1)) {
                    $temp2 = trim($temp1);                          // CLean it
                    if ($temp2 && str_starts_with($temp2, '{')) {   // Check it!
                        $temp3 = json_decode($temp2, true);         // FU*K it!!
                        if (json_last_error() === JSON_ERROR_NONE)
                            $callbackEntry = $temp3;                // and bring it back to everyone wants to see that.
                    }
                }
            }
            return $callbackEntry ?? $callbackData;
        }
    
        // Setter Mode
        $this->callbackData = match (true) {
            $callbackData === null => [], // null ==> clears $this->callbackData
    
            is_array($callbackData) => $callbackData,
    
            default => ['data' => $callbackData],
        };
    
        return $this;
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
    public function param(string $key, mixed $value): self
    {
        $this->extraPayload[$key] = $value;
        return $this;
    }

    /**
     * متد مدرن برای افزودن پارامتر
     * متد کمکی برای افزودن تمیز Payload (جایگزین param قدیمی)
    */
    public function withPayload(string $key, mixed $value): self
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
    public function injectPayload(array $payload): self
    {
        $this->extraPayload = array_merge($this->extraPayload, $payload);
        return $this;
    }

    protected function setButtonType(string|ButtonType|null $type = null): self
    {

        if(!$type)
            $type = $this->defaultBTNType;

        // Convert enum values to raw string representations if applicable
        $typeString = $type instanceof ButtonType
            ? $type->value
            : $type;

        // Resolve Type
        $this->buttonTypeEnum = $type instanceof ButtonType
            ? $type
            :(
                ButtonType::tryFrom($typeString) ?? ButtonType::Simple
            );

        // Force Type
        $this->type = $typeString;
        $this->forceSetProperty('type', $typeString);

        return $this;
    }

    // ------------------------------------------------------------------
    // متدهای Fluent-WidthSpan
    // ------------------------------------------------------------------

    public function getWidth(): float
    {
        return $this->width;
    }

    public function width(float $width): self
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
    public function col(int $span): self
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

    // ── Helper: convert float width → CSS flex style ──────────────
    private function colToFlexStyle(float $w): string
    {
        if ($w >= 1.0) {
            return 'flex:1 1 auto;min-width:0';
        }
        $pct = round($w * 100, 4);
        return 'flex:0 0 calc(' . $pct . '% - 0.3rem);min-width:0';
    }

    public function toHtml(): string
    {
        /*
        |--------------------------------------------------------------------------
        | 1) Resolve the effective Telegram <tg-button> type
        |--------------------------------------------------------------------------
        | The internal PowerButton vocabulary is not 1:1 with Telegram Rich HTML:
        |   Link        -> url
        |   CallbackData-> callback_data
        |   WebApp      -> web_app
        |   LoginUrl    -> login_url
        |   ...
        |
        | Also, several fluent methods (webApp/loginUrl/copyText/...) intentionally
        | store their state without changing the $buttonTypeEnum. Therefore,
        | when the current type is Simple/default, infer the semantic type from the
        | accumulated state.
        */

        $rawType = $this->buttonTypeEnum?->value
            ?? $this->type
            ?? $this->defaultBTNType;

        $normalizedInternalType = strtolower(
            str_replace(
                ['-', ' '],
                '_',
                preg_replace(
                    '/(?<!^)[A-Z]/',
                    '_$0',
                    (string) $rawType
                )
            )
        );

        $isDefaultType = in_array(
            $normalizedInternalType,
            ['', 'simple', 'button'],
            true
        );

        $hasActionData =
            !empty($this->callbackData)
            || array_key_exists('action_data', $this->extraPayload);

        /*
        * Infer semantic type only when the explicit type is effectively
        * Simple/default.
        */
        if ($isDefaultType) {
            $normalizedInternalType = match (true) {
                $this->disabledButton === true
                    => 'disabled',

                $this->webAppData !== null
                || array_key_exists('web_app', $this->extraPayload)
                    => 'web_app',

                $this->loginUrlData !== null
                || array_key_exists('login_url', $this->extraPayload)
                    => 'login_url',

                $this->switchInlineQueryChosenChat !== null
                || array_key_exists(
                    'switch_inline_query_chosen_chat',
                    $this->extraPayload
                )
                    => 'switch_inline_query_chosen_chat',

                $this->switchInlineQueryCurrentChatValue !== null
                || array_key_exists(
                    'switch_inline_query_current_chat',
                    $this->extraPayload
                )
                    => 'switch_inline_query_current_chat',

                $this->switchInlineQueryValue !== null
                || array_key_exists(
                    'switch_inline_query',
                    $this->extraPayload
                )
                    => 'switch_inline_query',

                $this->copyTextData !== null
                || array_key_exists('copy_text', $this->extraPayload)
                    => 'copy_text',

                $this->buttonUrl !== null
                || array_key_exists('url', $this->extraPayload)
                || array_key_exists('link_data', $this->extraPayload)
                    => 'url',

                $hasActionData
                    => 'callback_data',

                default
                    => 'simple',
            };
        }

        /*
        |--------------------------------------------------------------------------
        | 2) Map PowerButton vocabulary -> Telegram Rich HTML vocabulary
        |--------------------------------------------------------------------------
        */

        $type = match ($normalizedInternalType) {
            'link',
            'url',
            'button_link'
                => 'url',

            'callback',
            'callback_data',
            'callbackdata'
                => 'callback_data',

            'web_app',
            'webapp'
                => 'web_app',

            'login_url',
            'loginurl'
                => 'login_url',

            'switch_inline_query',
            'switchinlinequery'
                => 'switch_inline_query',

            'switch_inline_query_current_chat',
            'switchinlinequerycurrentchat'
                => 'switch_inline_query_current_chat',

            'switch_inline_query_chosen_chat',
            'switchinlinequerychosenchat'
                => 'switch_inline_query_chosen_chat',

            'copy_text',
            'copytext'
                => 'copy_text',

            'disabled',
            'disable'
                => 'disabled',

            default
                => $normalizedInternalType,
        };

        /*
        |--------------------------------------------------------------------------
        | 3) Canonical payload
        |--------------------------------------------------------------------------
        */

        $payload = $this->extraPayload;

        $style = $payload['style'] ?? $this->buttonStyle;

        $btnStyleClass = '';
        if($this->targetsWeb()) {
            $btnStyle = $this->buttonStyle;
            if($btnStyle !== null)
                $btnStyleClass = ' richy-btn-' . $btnStyle;
        }

        $url =
            $payload['url']
            ?? $this->buttonUrl
            ?? (
                is_array($payload['link_data'] ?? null)
                    ? ($payload['link_data']['url'] ?? null)
                    : null
            );

        /*
        |--------------------------------------------------------------------------
        | 4) Resolve action/callback data
        |--------------------------------------------------------------------------
        */

        $actionData = null;

        if (!empty($this->callbackData)) {
            $encoded = json_encode(
                $this->callbackData,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );

            $actionData = $encoded === false
                ? null
                : $encoded;
        } elseif (array_key_exists('action_data', $payload)) {
            $actionData = $payload['action_data'];
        }

        /*
        |--------------------------------------------------------------------------
        | 5) Build Telegram Rich HTML attributes
        |--------------------------------------------------------------------------
        |
        | attributesToString() from RichEntity performs the canonical escaping.
        | No local htmlspecialchars()/attribute serializer is necessary.
        |
        */

        $attributes = [
            'type'  => $type,
            'style' => $style,
        ];

        switch ($type) {

            /*
            |--------------------------------------------------------------------------
            | URL
            |--------------------------------------------------------------------------
            */

            case 'url':

                $attributes['url'] = $url;

                break;


            /*
            |--------------------------------------------------------------------------
            | CALLBACK DATA
            |--------------------------------------------------------------------------
            */

            case 'callback_data':

                $attributes['data'] =
                    $payload['data']
                    ?? $actionData;

                break;


            /*
            |--------------------------------------------------------------------------
            | WEB APP
            |--------------------------------------------------------------------------
            */

            case 'web_app':

                $webApp =
                    $this->webAppData
                    ?? ($payload['web_app'] ?? null);

                $webApp = $this->normalize($webApp);

                $attributes['url'] = is_array($webApp)
                    ? ($webApp['url'] ?? null)
                    : (
                        is_string($webApp)
                            ? $webApp
                            : null
                    );

                break;


            /*
            |--------------------------------------------------------------------------
            | LOGIN URL
            |--------------------------------------------------------------------------
            */

            case 'login_url':

                $login =
                    $this->loginUrlData
                    ?? ($payload['login_url'] ?? null);

                $login = $this->normalize($login);

                if (is_string($login)) {
                    $login = [
                        'url' => $login,
                    ];
                }

                if (!is_array($login)) {
                    $login = [];
                }

                $attributes['url'] =
                    $login['url']
                    ?? $login['login_url']
                    ?? null;

                $attributes['forward-text'] =
                    $login['forward_text']
                    ?? $login['forwardText']
                    ?? $login['forward-text']
                    ?? null;

                $attributes['request-write-access'] =
                    $login['request_write_access']
                    ?? $login['requestWriteAccess']
                    ?? $login['request-write-access']
                    ?? false;

                break;


            /*
            |--------------------------------------------------------------------------
            | SWITCH INLINE QUERY
            |--------------------------------------------------------------------------
            */

            case 'switch_inline_query':

                $attributes['query'] =
                    $this->switchInlineQueryValue
                    ?? ($payload['switch_inline_query'] ?? null);

                break;


            /*
            |--------------------------------------------------------------------------
            | SWITCH INLINE QUERY — CURRENT CHAT
            |--------------------------------------------------------------------------
            */

            case 'switch_inline_query_current_chat':

                $attributes['query'] =
                    $this->switchInlineQueryCurrentChatValue
                    ?? ($payload['switch_inline_query_current_chat'] ?? null);

                break;


            /*
            |--------------------------------------------------------------------------
            | SWITCH INLINE QUERY — CHOSEN CHAT
            |--------------------------------------------------------------------------
            */

            case 'switch_inline_query_chosen_chat':

                $chosen =
                    $this->switchInlineQueryChosenChat
                    ?? ($payload['switch_inline_query_chosen_chat'] ?? null);

                /*
                * normalize() is deliberately used here:
                *
                * SwitchInlineQueryChosenChat
                *      -> Arrayable
                *      -> toArray()
                *      -> recursive normalization
                */
                $chosen = $this->normalize($chosen);

                if (!is_array($chosen)) {
                    $chosen = [];
                }

                $attributes['query'] =
                    $chosen['query']
                    ?? null;

                $attributes['allow-user-chats'] =
                    $chosen['allow_user_chats']
                    ?? $chosen['allowUserChats']
                    ?? false;

                $attributes['allow-bot-chats'] =
                    $chosen['allow_bot_chats']
                    ?? $chosen['allowBotChats']
                    ?? false;

                $attributes['allow-group-chats'] =
                    $chosen['allow_group_chats']
                    ?? $chosen['allowGroupChats']
                    ?? false;

                $attributes['allow-channel-chats'] =
                    $chosen['allow_channel_chats']
                    ?? $chosen['allowChannelChats']
                    ?? false;

                break;


            /*
            |--------------------------------------------------------------------------
            | COPY TEXT
            |--------------------------------------------------------------------------
            */

            case 'copy_text':

                $copy =
                    $this->copyTextData
                    ?? ($payload['copy_text'] ?? null);

                $copy = $this->normalize($copy);

                $copyText = is_array($copy)
                    ? ($copy['text'] ?? null)
                    : $copy;

                if ($copyText !== null) {
                    $copyText = renderAsText(
                        $copyText,
                        $this
                    );
                }

                $attributes['text'] = $copyText;

                break;


            /*
            |--------------------------------------------------------------------------
            | DISABLED
            |--------------------------------------------------------------------------
            */

            case 'disabled':

                /*
                * <tg-button type="disabled">...</tg-button>
                *
                * No additional attributes required.
                */

                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Telegram Rich HTML renderer
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | renderHtml() is intentionally used instead of esc().
        |
        | This preserves RichEntity/Htmlable button labels while safely escaping
        | primitive string content through RichEntity's canonical HTML pipeline.
        | so a RichEntity remains capable of emitting Telegram Rich HTML markup.
        |
        */

        if ($this->targetsTelegram()) {

            /*
            |--------------------------------------------------------------------------
            | 6) Final body
            |--------------------------------------------------------------------------
            */

            $buttonText = $this->renderHtml($this->text);

            /*
            |--------------------------------------------------------------------------
            | 7) Canonical attribute serialization via RichEntity
            |--------------------------------------------------------------------------
            */

            $attributeString = $this->attributesToString(
                $attributes
                /// filterNulls($attributes)
            );

            return '<tg-button'
                . ($attributeString !== ''
                    ? ' ' . $attributeString
                    : '')
                . '>'
                . $buttonText
                . '</tg-button>';
        }

        // ── Resolve width → flex style ────────────────────────────
        $flexStyle = $this->colToFlexStyle($this->width);

        /*
        |--------------------------------------------------------------------------
        | Preserve both the canonical PowerButton style and the Web UI flex style.
        |--------------------------------------------------------------------------
        */

        $webStyle = trim(
            ($style !== null && $style !== ''
                ? rtrim((string) $style, ';') . ';'
                : '')
            . $flexStyle,
            ';'
        );

        if (!empty($this->richStyles)) {
            $traitStyles = [];
            foreach ($this->richStyles as $prop => $val) {
                $traitStyles[] = $prop . ': ' . $val;
            }
            $traitStyles = implode('; ', $traitStyles);
            $webStyle = trim((rtrim($webStyle, ';') . ';' . $traitStyles), ';');
        }

        // ── Render label ──────────────────────────────────────────
        $buttonText = $this->text instanceof RichEntity
            ? $this->text->toHtml()
            : $this->esc((string) ($this->text ?? ''));

        // ── Build per-type HTML ───────────────────────────────────
        $baseClass = 'richy-btn-button' . $btnStyleClass; // . ' tg-inline-button'
        if (!empty($this->richClasses)) {
            $baseClass = trim($baseClass . ' ' . implode(' ', array_keys($this->richClasses)));
        }

        switch ($type) {

            case 'url':

                return sprintf(
                    '<a class="%s" href="%s" target="_blank" rel="noopener noreferrer" style="%s">%s</a>',
                    $baseClass,
                    $this->esc((string) ($url ?? '#')),
                    $webStyle,
                    $buttonText
                );


            case 'web_app':

                $webApp = $this->normalize(
                    $this->webAppData
                    ?? ($payload['web_app'] ?? null)
                );

                $webUrl = is_array($webApp)
                    ? ($webApp['url'] ?? '#')
                    : (
                        is_string($webApp)
                            ? $webApp
                            : '#'
                    );

                return sprintf(
                    '<a class="%s richy-btn-inline-button--webapp" href="%s" target="_blank" rel="noopener noreferrer" style="%s">%s</a>',
                    $baseClass,
                    $this->esc((string) $webUrl),
                    $webStyle,
                    $buttonText
                );


            case 'login_url':

                $login = $this->normalize(
                    $this->loginUrlData
                    ?? ($payload['login_url'] ?? null)
                );

                if (is_string($login)) {
                    $login = [
                        'url' => $login,
                    ];
                }

                if (!is_array($login)) {
                    $login = [];
                }

                $loginUrl =
                    $login['url']
                    ?? $login['login_url']
                    ?? '#';

                return sprintf(
                    '<a class="%s richy-btn-inline-button--login" href="%s" target="_blank" rel="noopener noreferrer" style="%s">🔐 %s</a>',
                    $baseClass,
                    $this->esc((string) $loginUrl),
                    $webStyle,
                    $buttonText
                );


            case 'callback_data':

                return sprintf(
                    '<button class="%s" type="button" data-richy-btn-action="%s" data-richy-btn-type="callback_data" data-richy-btn-payload=\'%s\' style="%s">%s</button>',
                    $baseClass,
                    $this->esc(
                        (string) (
                            $this->forceGetProperty('action_id')
                            ?? ''
                        )
                    ),
                    $this->esc(
                        $actionData ?? '{}'
                    ),
                    $webStyle,
                    $buttonText
                );


            case 'switch_inline_query':

                return sprintf(
                    '<button class="%s" type="button" data-richy-btn-type="switch_inline_query" data-richy-btn-query="%s" style="%s">%s</button>',
                    $baseClass,
                    $this->esc(
                        (string) (
                            $this->switchInlineQueryValue
                            ?? $payload['switch_inline_query']
                            ?? ''
                        )
                    ),
                    $webStyle,
                    $buttonText
                );


            case 'switch_inline_query_current_chat':

                return sprintf(
                    '<button class="%s" type="button" data-richy-btn-type="switch_inline_query_current_chat" data-richy-btn-query="%s" style="%s">%s</button>',
                    $baseClass,
                    $this->esc(
                        (string) (
                            $this->switchInlineQueryCurrentChatValue
                            ?? $payload['switch_inline_query_current_chat']
                            ?? ''
                        )
                    ),
                    $webStyle,
                    $buttonText
                );


            case 'switch_inline_query_chosen_chat':

                $chosen = $this->normalize(
                    $this->switchInlineQueryChosenChat
                    ?? ($payload['switch_inline_query_chosen_chat'] ?? null)
                );

                if (!is_array($chosen)) {
                    $chosen = [];
                }

                return sprintf(
                    '<button class="%s" type="button" data-richy-btn-type="switch_inline_query_chosen_chat" data-richy-btn-query="%s" data-richy-btn-payload=\'%s\' style="%s">%s</button>',
                    $baseClass,
                    $this->esc(
                        $chosen['query'] ?? ''
                    ),
                    $this->esc(
                        json_encode(
                            $chosen,
                            JSON_UNESCAPED_UNICODE
                        ) ?: '{}'
                    ),
                    $webStyle,
                    $buttonText
                );


            case 'copy_text':

                $copy = $this->normalize(
                    $this->copyTextData
                    ?? ($payload['copy_text'] ?? null)
                );

                $copyText = is_array($copy)
                    ? ($copy['text'] ?? '')
                    : (string) ($copy ?? '');

                $copyText = renderAsText(
                    $copyText,
                    $this
                );

                return sprintf(
                    '<button class="%s richy-btn-inline-button--copy" type="button" data-richy-btn-type="copy_text" data-richy-btn-copy="%s" style="%s" onclick="navigator.clipboard.writeText(this.dataset.richyBtnCopy)">%s</button>',
                    $baseClass,
                    $this->esc(
                        (string) ($copyText ?? '')
                    ),
                    $webStyle,
                    $buttonText
                );


            case 'disabled':

                return sprintf(
                    '<button class="%s richy-btn-inline-button--disabled" type="button" disabled style="%s;opacity:.45;cursor:not-allowed">%s</button>',
                    $baseClass,
                    $webStyle,
                    $buttonText
                );


            // ── Rubika-specific / fallback ────────────────────────
            default:

                return sprintf(
                    '<button class="%s" type="button" data-richy-btn-type="%s" data-richy-btn-action="%s" style="%s">%s</button>',
                    $baseClass,
                    $this->esc($type),
                    $this->esc(
                        (string) (
                            $this->forceGetProperty('action_id')
                            ?? ''
                        )
                    ),
                    $webStyle,
                    $buttonText
                );
        }
    }

    /**
     * آرایهٔ نهایی و کامل برای نمایش دکمه را برمی‌گرداند.
     *
     * این متد جایگزین toArray1 و toArray2 شده و تمام حالات ممکن را پوشش می‌دهد.
     *
     * |--------------------------------------------------------------------------
     * | Modern RichButton Serialization
     * |--------------------------------------------------------------------------
     * 
     * The ONLY normalization/filtering authority is RichEntity.
     * 
     * @return array
    */
    public function toArray(): array
    {
        // ─── 1. تعیین نوع دکمه با fallback زنجیره‌ای ───
        // Ensure Type Consistency (From Enum or String)
        $type = $this->buttonTypeEnum?->value
            ?? $this->type
            ?? $this->defaultBTNType;

        // ─── 2. پردازش action_data ───
        // اولویت: callbackData (اگر خالی نباشد) > extraPayload['action_data']
        // Priority 1: callbackData array
        // Priority 2: extraPayload 'action_data'
        $actionData = !empty($this->callbackData)
            ? json_encode(
                $this->callbackData,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            )
            : ($this->extraPayload['action_data'] ?? null);

        // ─── 3. تولید شناسهٔ یکتا با fallback قوی ───
        // مدیریت هوشمند ID و Action Data (Merged Logic)
        // Priority: Parent Action ID > Action Data > URL > Hash (Optimized)
        $id = $this->forceGetProperty('action_id')
            ?? $actionData
            ?? ($this->extraPayload['url'] ?? null)
            ?? $this->buttonUrl
            ?? 'btn_' . crc32(
                ((string) (renderAsText($this->text, $this) ?? '')) . microtime() // CRC32 سریعتر از uniqid است (Logic from toArray2)
            );

        // ─── 4. ساخت آرایهٔ پایه ───
        // ساخت دیتای اصلی با سینتکس آرایه جدید
        $result = [
            'text'        => $this->text ?? '',
            'type'        => $type,
            'id'          => $id,                   // Ensure base has it too
            'button_text' => $this->text ?? '',     // اضافه کردن فیلد button_text برای سازگاری با کدهای قدیمی‌تر
        ];

        // ─── 5. افزودن action_data در صورت وجود ───
        if ($actionData !== null) {
            $result['action_data'] = $actionData;
        }

        // ─── 6. فیلدهای مخصوص لینک ───
        // Type-Specific Data

        $result['url'] = (strtolower($type) === 'link' || $this->buttonTypeEnum === ButtonType::Link) ?
        (
            $this->extraPayload['url']
            ?? $this->buttonUrl
            ?? ''
        ) : null;

        // ─── 7. فیلدهای غیر روبیکا (تنها در صورت هدف غیر روبیکا) ───
        if (!$this->targetsRubika()) {

            /*
            * RichButton fields
            *
            * null values are deliberately retained here and removed
            * only at the very end, after every source has been merged.
            */
            $result += [
                'style' =>
                    $this->buttonStyle,

                'url' =>
                    $result['url'] ?? $this->buttonUrl,

                'callback_data' =>
                    $this->callbackData ?: null,

                'web_app' =>
                    $this->webAppData,

                'login_url' =>
                    $this->normalize($this->loginUrlData),

                'switch_inline_query' =>
                    $this->switchInlineQueryValue,

                'switch_inline_query_current_chat' =>
                    $this->switchInlineQueryCurrentChatValue,

                /*
                 * IMPORTANT:
                 * SwitchInlineQueryChosenChat remains object.
                 *
                 * normalize() comes from RichEntity.
                */
                'switch_inline_query_chosen_chat' =>
                    $this->normalize(
                        $this->switchInlineQueryChosenChat,
                        true
                    ),

                'copy_text' =>
                    renderAsText(
                        $this->copyTextData,
                        $this,
                        true
                    ),

                /*
                 * DisabledButton::toArray() => []
                 *
                 * Therefore disabled=true becomes [].
                */
                'disabled' =>
                    $this->disabledButton === true
                        ? []
                        : null,
            ];
        }

        /*
        * ─────────────────────────────────────────────────────────────
        * Legacy + Modern + Rich → ONE canonical payload
        *
        * Later array_merge() values intentionally have precedence.
        * ─────────────────────────────────────────────────────────────
        */
        // ─── 8. ادغام extraPayload (با اولویت بالاتر برای کلیدهای اضافی) ───
        $result = array_merge(
            $result,
            $this->extraPayload // ExtraPayload is intentionally last: to always win. Most-important for array_merge.
        );

        // ─── 9. رندر نهایی فیلدهای متنی ───
        /*
        * ─────────────────────────────────────────────────────────────
        * Final text rendering
        *
        * Every text-bearing field passes through the SAME renderer.
        * ─────────────────────────────────────────────────────────────
        */
        foreach (['text', 'title', 'button_text'] as $key) {
            if (array_key_exists($key, $result)) {
                $result[$key] = renderAsText(
                    $result[$key],
                    $this,
                    true // help filterEmpty method with returning null
                );
            }
        }

        // ─── 10. حذف مقادیر null و خالی ───
        return $this->filterEmpty($result);
    }
}
