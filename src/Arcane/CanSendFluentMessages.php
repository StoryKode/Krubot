<?php

namespace KrubiK\Arcane;
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

use Closure;
use InvalidArgumentException;
use KrubiK\Keyboard\Keyboard;       // کلاس کیبورد شیشه‌ای
use KrubiK\Keyboard\ReplyKeyboard;  // کلاس کیبورد منو
use KrubiK\Render\RichMan;
use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Drivers\RubikaDriver;

/**
 * Trait CanSendFluentMessages
 * 
 * موتور اصلی ارسال پیام به صورت Fluent Interface.
 * این تریت قابلیت‌های ارسال متن، مدیا (از طریق CanSendMedia)، ویرایش، فوروارد
 * و مدیریت کیبوردها را تجمیع می‌کند.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait CanSendFluentMessages
{
    // ادغام تریت مدیریت پیوست‌ها برای دسترسی به متدها و پراپرتی‌های مدیا
    use CanSendMedia;

    // --- Text & Options State ---
    protected string|RichMan|null $fText = null;
    protected string $fParseMode = 'MarkdownMode';
    protected ?int $fReplyTo = null;
    protected bool $fSilent = false;
    protected bool $fProtected = false;
    protected bool $fWithoutPreview = false;
    
    // --- Keyboards State (Separated for Rubika API Structure) ---
    // در روبیکا کیبورد شیشه‌ای (inline_keypad) و کیبورد منو (chat_keypad) پارامترهای جداگانه دارند
    protected array|null $fInlineKeyboard = null; 
    protected array|null $fReplyKeyboard = null;
    
    // --- Action State ---
    protected bool $isEditMode = false;
    protected ?int $editMessageId = null;
    protected ?string $forcedMethod = null; // برای متدهایی مثل forwardMessages که ساختار متفاوت دارند
    protected array $forcedParams = [];

    // ========================================================================
    // 1. Text & Content Builders
    // ========================================================================

    /**
     * تنظیم متن پیام.
     * به صورت پیش‌فرض از پارسر MarkdownMode استفاده می‌کند.
    */
    public function message(string|RichMan $text): static
    {
        $this->fText = $text;
        $this->fParseMode = 'MarkdownMode';
        return $this;
    }

    /**
     * تنظیم متن پیام با فرمت HTML.
    */
    public function html(string $html): static
    {
        $this->fText = $html;
        $this->fParseMode = 'HTML';
        return $this;
    }

    /**
     * تنظیم متن پیام با فرمت Markdown.
    */
    public function markdown(string $markdown): static
    {
        $this->fText = $markdown;
        $this->fParseMode = 'MarkdownMode';
        return $this;
    }

    /**
     * تنظیم متن پیام با فرمت MarkdownV2.
     * در روبیکا معمولا همان MarkdownMode استاندارد پاسخگو است.
    */
    public function markdownV2(string $markdown): static
    {
        $this->fText = $markdown;
        $this->fParseMode = 'MarkdownMode'; 
        return $this;
    }

    // ========================================================================
    // 2. Modifiers (Reply, Silent, etc.)
    // ========================================================================

    /**
     * ریپلای زدن روی یک پیام خاص.
    */
    public function replyToMessage(int $messageId): static
    {
        $this->fReplyTo = $messageId;
        return $this;
    }

    /**
     * ارسال پیام بدون صدا (Silent).
    */
    public function silent(): static
    {
        $this->fSilent = true;
        return $this;
    }

    /**
     * محافظت از محتوا (جلوگیری از فوروارد/ذخیره).
     * (پشتیبانی این ویژگی در کلاینت‌های مختلف روبیکا ممکن است متفاوت باشد)
    */
    public function beProtected(): static
    {
        $this->fProtected = true;
        return $this;
    }

    /**
     * غیرفعال کردن پیش‌نمایش لینک‌ها.
    */
    public function withoutPreview(): static
    {
        $this->fWithoutPreview = true;
        return $this;
    }

    // ========================================================================
    // 3. Keyboard Logic (Separated & Enhanced)
    // ========================================================================

    /**
     * افزودن کیبورد شیشه‌ای (Inline Keyboard).
     * این متد مقدار را در inline_keypad قرار می‌دهد.
     * 
     * @param Keyboard|Closure|array $keyboard
     * @return static
    */
    public function keyboard(Keyboard|Closure|array $keyboard): static
    {
        if ($keyboard instanceof Closure) {
            $builder = Keyboard::make();
            $result = $keyboard($builder);
            // اگر کلوژر خود آبجکت را برگرداند یا فقط روی آن کار کرد
            $finalObj = ($result instanceof Keyboard) ? $result : $builder;
            $this->fInlineKeyboard = $finalObj->toArray();
        } elseif ($keyboard instanceof Keyboard) {
            $this->fInlineKeyboard = $keyboard->toArray();
        } else {
            // اگر آرایه خام داده شد
            $this->fInlineKeyboard = $keyboard;
        }
        return $this;
    }

    /**
     * افزودن کیبورد منو/چت (Reply/Chat Keyboard).
     * این متد مقدار را در chat_keypad قرار می‌دهد.
     * 
     * @param ReplyKeyboard|Closure|array $keyboard
     * @return static
    */
    public function replyKeyboard(ReplyKeyboard|Closure|array $keyboard): static
    {
        if ($keyboard instanceof Closure) {
            $builder = ReplyKeyboard::make();
            $result = $keyboard($builder);
            $finalObj = ($result instanceof ReplyKeyboard) ? $result : $builder;
            $this->fReplyKeyboard = $finalObj->toArray();
        } elseif ($keyboard instanceof ReplyKeyboard) {
            $this->fReplyKeyboard = $keyboard->toArray();
        } else {
            $this->fReplyKeyboard = $keyboard;
        }
        return $this;
    }

    /**
     * حذف کیبورد منو (Reply Keyboard).
     * در استاندارد تلگرام remove_keyboard است.
    */
    public function removeReplyKeyboard(bool $selective = false): static
    {
        $this->fReplyKeyboard = [
            'remove_keyboard' => true,
            'selective' => $selective
        ];
        return $this;
    }

    // ========================================================================
    // 4. Advanced Actions (Edit, Forward, Delete)
    // ========================================================================

    /**
     * فعال‌سازی حالت ویرایش پیام.
     * متد send رفتار خود را به editMessage تغییر می‌دهد.
    */
    public function edit(int $messageId): static
    {
        $this->isEditMode = true;
        $this->editMessageId = $messageId;
        return $this;
    }

    /**
     * جایگزینی سریع کیبورد یک پیام (میانبر برای Edit).
     */
    public function replaceKeyboard(int $messageId, Keyboard|Closure|array $newKeyboard): static
    {
        $this->edit($messageId);
        return $this->keyboard($newKeyboard);
    }

    /**
     * حذف کیبورد یک پیام (ویرایش و خالی کردن کیبورد).
    */
    public function deleteKeyboard(int $messageId): static
    {
        $this->edit($messageId);
        $this->fInlineKeyboard = null; // یا یک آرایه خالی بسته به رفتار API
        return $this; 
    }

    /**
     * فوروارد کردن پیام.
     * این متد متد نهایی send را مجبور به استفاده از forwardMessages می‌کند.
    */
    public function forwardMessage(string $fromChatId, int $messageId): static
    {
        $this->forcedMethod = 'forwardMessages';
        $this->forcedParams = [
            'from_chat_id' => $fromChatId,
            'message_ids' => [$messageId] // روبیکا آرایه می‌پذیرد
        ];
        return $this;
    }

    /**
     * کپی کردن پیام.
     * از آنجا که روبیکا متد اختصاصی copyMessage مشابه تلگرام ندارد،
     * ما این را به forwardMessage نگاشت می‌کنیم تا پایداری حفظ شود.
    */
    public function copyMessage(string $fromChatId, int $messageId): static
    {
        return $this->forwardMessage($fromChatId, $messageId);
    }

    // --- Immediate Actions (متدهای آنی که نیاز به صدا زدن send ندارند) ---

    /**
     * حذف آنی یک پیام.
    */
    public function deleteMessage(int $messageId): array
    {
        // استفاده از متد makeRequest والد یا apiRequest
        return $this->makeRequest('deleteMessages', [
            'chat_id' => $this->resolveChatId(null),
            'message_ids' => [$messageId]
        ]);
    }

    /**
     * حذف آنی چندین پیام.
    */
    public function deleteMessages(array|int ...$ids): array
    {
        $flatIds = [];
        foreach ($ids as $id) {
            if (is_array($id)) {
                $flatIds = array_merge($flatIds, $id);
            } else {
                $flatIds[] = $id;
            }
        }
        
        return $this->makeRequest('deleteMessages', [
            'chat_id' => $this->resolveChatId(null),
            'message_ids' => $flatIds
        ]);
    }

    // ========================================================================
    // 5. THE HYPER-METHOD: SEND
    // ========================================================================

     // =========================================================================
    // 5. ⚡ send() — pure dispatcher, zero platform logic
    //
    // Builds the param bag with raw text + `_parse_mode` hint.
    // makeRequest() (overridden in the driver) owns ALL transformation.
    // =========================================================================
    /**
     * متد نهایی و قدرتمند ارسال.
     * این متد قلب تپنده سیستم است و با بررسی تمام وضعیت‌ها (متن، مدیا، ادیت، فوروارد)
     * بهترین تصمیم را برای فراخوانی API می‌گیرد.
     * 
     * @param string|null $chatId شناسه چت هدف (اگر null باشد، تلاش می‌کند هوشمندانه پیدا کند)
     * @return array خروجی خام API روبیکا (json decoded)
     * @throws InvalidArgumentException اگر chat_id یا متن ضروری یافت نشود.
    */
    public function send(?string $chatId = null): array
    {
        // 1. Resolve Target Chat ID
        // اولویت: آرگومان متد -> پراپرتی کلاس (کانتکست ربات) -> پراپرتی بیلدر قدیمی
        $targetChatId = $chatId ?? ($this->chat_id ?? null); 
        if (!$targetChatId) {
            $targetChatId = $this->builder_chat_id ?? throw new InvalidArgumentException("Chat ID is required for send().");
        }

        // 2. Handle Forced Actions (Forward/Copy) - بالاترین اولویت
        if ($this->forcedMethod) {
            $params = array_merge(['chat_id' => $targetChatId], $this->forcedParams);
            // فوروارد نیازی به پردازش متن و کیبورد معمول ندارد
            $result = $this->makeRequest($this->forcedMethod, $params);
            $this->resetFluent();
            $this->resetAttachments();
            return $result;
        }

        // 3. Prepare Common {Shared} Parameters
        // All keys are driver-agnostic internal names.
        // The driver's normalizeParams() renames, strips, or transforms as needed.
        $commonParams = $this->buildCommonParams();

        // ================================================================
        // 4. 🔥 RICH CONTENT — MUST BE RESOLVED BEFORE ANY TEXT PARSER
        // ================================================================

        $richContent = $this->resolveRichContent($this->fText);

        if ($richContent instanceof RichMan) {

            // A RichMan is already a complete document tree.

            if ($this->hasPendingAttachment()) {
                // @Todo: auto-handle this problem conditionally
                throw new InvalidArgumentException(
                    'Rich content cannot be combined with a pending attachment. '
                    . 'Compose the media directly inside RichMan instead.'
                );
            }

            $result = $this->makeRequest(
                'sendMessage',
                array_merge(
                    [
                        'chat_id' => $targetChatId,

                        /*
                        * Intentionally keep the RichMan OBJECT alive.
                        *
                        * TelegramDriver::makeRequest() is responsible for
                        * recognizing it and converting it to sendRichMessage.
                        */
                        'text' => $richContent,
                    ],
                    $commonParams
                )
            );

            $this->resetFluent();
            $this->resetAttachments();

            return $result;
        }

        // 5. Handle Attachments (Media) - بررسی وضعیت CanSendMedia Trait
        if ($this->hasPendingAttachment()) {
            // --- SCENARIO: SEND MEDIA ---
            
            // ── The Unique Media path
            $result = $this->dispatchMediaMessage($targetChatId, $commonParams);
            
            // پاکسازی و بازگشت
            $this->resetFluent();
            $this->resetAttachments();
            return $result;
        }

        // 6. Handle Text / Edit (No Attachment)
        // ── send/edit(Text) path
        // اجرای درخواست متن/ادیت
        $result = $this->dispatchTextMessage($targetChatId, $commonParams);
        
        // پاکسازی وضعیت
        $this->resetFluent();
        $this->resetAttachments(); // محض احتیاط
        
        return $result;
    }

    private function responsingRubika(): bool
    {
        $activeDriver = $this->enforcer();
        return ($activeDriver && $activeDriver instanceof RubikaDriver);
    }

    /**
     * Dispatch a sendMessage or editMessageText request.
     * Raw text passed through — driver owns transformation.
    */
    private function dispatchTextMessage(string $targetChatId, array $commonParams): array
    {
        // 6. Handle Text / Edit (No Attachment)
        // --- SENARIO: SEND TEXT or EDIT MESSAGE ---
        
        // پردازش متن و متادیتا با Parsentinel
        $finalText = $this->fText;

        // پارامترهای پایه متن
        $textParams = array_merge([
            'chat_id' => $targetChatId,
        ], $commonParams);

        if ($this->isEditMode) {
        // --- حالت ویرایش (EDIT) ---

            // @Todo: Automate this
            if ($this->fText === null) {
                throw new InvalidArgumentException(
                    '[Krubot] edit() requires text. Pass the current text to update keyboard-only.'
                );
            }
            // ویرایش متن (همراه با کیبورد احتمالی)
            return $this->makeRequest('editMessageText', array_merge($textParams, [
                'message_id' => $this->editMessageId,
                'text'       => (string) $this->fText,
            ]));
        }

        // --- حالت ارسال جدید (NEW MESSAGE) ---

        if ($this->fText === null) {
            throw new InvalidArgumentException('[Krubot] send() requires text when no attachment is pending.');
        }

        // اجرای درخواست ارسال
        // Hand off to InteractsWithApi::makeRequest which calls driver()->apiRequest() &+ AmethystMatrix debug
        return $this->makeRequest('sendMessage', array_merge($textParams, [
            'text' => (string) $this->fText,
        ]));
    }

    /**
     * Dispatch a media (file-based or contact/location) request.
     *
     * Method names are Telegram-style; RubikaDriver::RENAMED_METHODS translates them.
     * Now Raw text is passed as-is; only RubikaDriver parses it via MetaTextTransformer.
    */
    private function dispatchMediaMessage(string $chatId, array $common): array
    {
        $typeToMethod = [
            'Image'    => 'sendPhoto',
            'Video'    => 'sendVideo',
            'Voice'    => 'sendVoice',
            'Music'    => 'sendAudio',
            'File'     => 'sendDocument',
            'Contact'  => 'sendContact',
            'Location' => 'sendLocation',
        ];

        // تعیین متد API بر اساس نوع مدیا
        $method = $typeToMethod[$this->attachmentType] ?? 'sendDocument';
        
        // پارامترهای پایه
        $params = array_merge([
            'chat_id' => $chatId
        ], $common);

        if ($this->attachmentType === 'Contact') {
            return $this->makeRequest($method, array_merge($params, [
                'phone_number' => $this->extraPayload['phone_number'],
                'first_name'   => $this->extraPayload['first_name'],
                'last_name'    => $this->extraPayload['last_name'] ?? '',
            ]));
        }

        if ($this->attachmentType === 'Location') {
            $coords = json_decode($this->attachmentContent, true);
            return $this->makeRequest($method, array_merge($params, [
                'latitude'  => $coords['lat'],
                'longitude' => $coords['long'],
            ]));
        }

        // File-based media — raw path/id; driver resolves to FileObject
        $params['file_inline'] = $this->attachmentContent;

        // ترکیب کپشن با اطلاعات اضافی (مثل آدرس Venue)
        // $caption = null;
        $caption = $this->attachmentCaption; // Presence Signal of a Caption-able Content
        if ($caption && isset($this->extraPayload['venue_info'])) {
            $caption .= $this->extraPayload['venue_info'];
        }

        if ($caption)
            $params['caption']      = $caption;

        // اگر نام فایل تنظیم شده باشد
        if ($this->attachmentFileName) {
            $params['file_name'] = $this->attachmentFileName;
        }

        // اگر تامنیل داشته باشیم (برای ویدیو)
        if ($this->thumbnailPath && $this->attachmentType === 'Video') {
            $params['thumb_inline'] = $this->thumbnailPath;
        }

        // اجرای درخواست مدیا
        // InteractsWithApi متد makeRequest را دارد
        return $this->makeRequest($method, $params);
    }

    /**
     * Converts any structured Rich content into a canonical RichMan document.
     *
     * @param mixed $content
     * @return RichMan|null
    */
    protected function resolveRichContent(mixed $content): ?RichMan
    {
        // Already the canonical container.
        if ($content instanceof RichMan) {
            return $content;
        }

        // A single RichEntity.
        if ($content instanceof RichEntity) {
            return RichMan::summon()->add($content);
        }

        // An array containing Rich entities.
        if (is_array($content)) {

            foreach ($content as $item) {

                if (
                    $item instanceof RichEntity
                    || $this->containsRichEntity($item)
                ) {
                    return RichMan::summon()->add($content);
                }
            }
        }

        return null;
    }


    /**
     * Recursively determines whether an arbitrary value contains
     * at least one RichEntity.
     */
    protected function containsRichEntity(mixed $value): bool
    {
        if ($value instanceof RichEntity) {
            return true;
        }

        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $item) {
            if ($this->containsRichEntity($item)) {
                return true;
            }
        }

        return false;
    }

    // =========================================================================
    // PRIVATE DISPATCH HELPERS
    // =========================================================================

    /**
     * Build the param fields shared across text and media calls.
     *
     * Uses internal field names only. The `_parse_mode` hint (underscore prefix)
     * is an opaque token for the driver — this trait doesn't interpret it.
     */
    private function buildCommonParams(): array
    {

        // Common Parameters
        // این پارامترها بین ارسال متن، مدیا و حتی برخی ادیت‌ها مشترک هستند

        // '_parse_mode' → hint مخصوص driver، با underscore prefix تا با هیچ API field ای collision نداشته باشد.
        // InteractsWithApi یا driver آن را مصرف و از params حذف می‌کند.
        $commonParams = [
            '_parse_mode' => $this->fParseMode, // opaque hint — driver consumes & removes
        ];

        if ($this->fReplyTo) $commonParams['reply_to_message_id'] = $this->fReplyTo;
        if ($this->fSilent) $commonParams['disable_notification'] = true;
        if ($this->fProtected)  $commonParams['protect_content'] = true;
        if ($this->fWithoutPreview) $commonParams['disable_web_page_preview'] = true;
        
        // مدیریت کیبورد شیشه‌ای (Inline)
        // کیبوردها با همان کلیدهای RC.8 — InteractsWithApi::makeRequest cleanup را دارد
        if ($this->fInlineKeyboard) {
            $commonParams['inline_keypad'] = $this->fInlineKeyboard;
        }
        
        // مدیریت کیبورد منو (Chat Keypad)
        // روبیکا نیاز دارد type آن مشخص شود
        if ($this->fReplyKeyboard) {
            $commonParams['chat_keypad'] = $this->fReplyKeyboard;
            // $commonParams['chat_keypad_type'] = 'New'; // responsibility /Moved to RubikaDriver
        }
        return $commonParams;
    }

    /**
     * بازنشانی تمام متغیرهای وضعیت Fluent برای جلوگیری از تداخل در درخواست‌های بعدی.
     */
    protected function resetFluent(): void
    {
        $this->fText = null;
        $this->fParseMode = 'MarkdownMode';
        $this->fReplyTo = null;
        $this->fSilent = false;
        $this->fProtected = false;
        $this->fWithoutPreview = false;
        
        $this->fInlineKeyboard = null;
        $this->fReplyKeyboard = null;
        
        $this->isEditMode = false;
        $this->editMessageId = null;
        $this->forcedMethod = null;
        $this->forcedParams = [];
        
        // اگر متد resetBuilder قدیمی وجود دارد (برای سازگاری) صدا زده شود
        if (method_exists($this, 'resetBuilder')) {
            $this->resetBuilder();
        }
    }

    /**
     * هلپر متد برای حل کردن chat_id در صورتی که null باشد.
     * (برای استفاده داخلی در متدهای deleteMessage و ...)
    */
    protected function resolveChatId(?string $chatId): string
    {
        $id = $chatId ?? ($this->chat_id ?? null);
        if (!$id) {
             $id = $this->builder_chat_id ?? throw new InvalidArgumentException("Chat ID is required.");
        }
        return $id;
    }
}
