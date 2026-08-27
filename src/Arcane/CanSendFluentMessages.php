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
use KrubiK\Arcane\MetaTextTransformer;

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

    use MetaTextTransformer; // parse rich-blocks to rubika-compatible format

    // --- Text & Options State ---
    protected ?string $fText = null;
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
    public function message(string $text): static
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
            return $result;
        }

        // 3. Prepare Common Parameters
        // این پارامترها بین ارسال متن، مدیا و حتی برخی ادیت‌ها مشترک هستند
        $commonParams = [];
        if ($this->fReplyTo) $commonParams['reply_to_message_id'] = $this->fReplyTo;
        if ($this->fSilent) $commonParams['disable_notification'] = true;
        
        // مدیریت کیبورد شیشه‌ای (Inline)
        if ($this->fInlineKeyboard) {
            $commonParams['inline_keypad'] = $this->fInlineKeyboard;
        }
        
        // مدیریت کیبورد منو (Chat Keypad)
        // روبیکا نیاز دارد type آن مشخص شود
        if ($this->fReplyKeyboard) {
            $commonParams['chat_keypad'] = $this->fReplyKeyboard;
            $commonParams['chat_keypad_type'] = 'New'; 
        }

        // 4. Handle Attachments (Media) - بررسی وضعیت Trait HasAttachments
        if ($this->hasPendingAttachment()) {
            // --- SENARIO: SEND MEDIA ---
            
            // ترکیب کپشن با اطلاعات اضافی (مثل آدرس Venue)
            $caption = $this->attachmentCaption;
            if (isset($this->extraPayload['venue_info'])) {
                $caption .= $this->extraPayload['venue_info'];
            }

            // پردازش متن کپشن برای استخراج متادیتا (بولد، لینک و ...)
            $metadata = null;
            if ($caption) {
                $parsed = $this->parseToRubika($caption, $this->fParseMode); // Engage MetaTextTransformer Engine
                $caption = $parsed['text'];
                if (!empty($parsed['metadata'])) {
                    $metadata = $parsed['metadata'];
                }
            }

            // پارامترهای پایه مدیا
            $mediaParams = array_merge([
                'chat_id' => $targetChatId,
                'caption' => $caption,
                'metadata' => $metadata, // روبیکا معمولا متادیتا را برای کپشن هم پردازش می‌کند
            ], $commonParams);

            // تعیین متد API بر اساس نوع مدیا
            $method = 'sendMessage'; // Fallback
            $fileParamKey = 'file_inline'; // نام پارامتر پیش‌فرض برای فایل در اکثر متدها

            switch ($this->attachmentType) {
                case 'Contact':
                    $method = 'sendContact';
                    $mediaParams['phone_number'] = $this->extraPayload['phone_number'];
                    $mediaParams['first_name'] = $this->extraPayload['first_name'];
                    $mediaParams['last_name'] = $this->extraPayload['last_name'] ?? '';
                    // Contact کپشن و متادیتا ندارد
                    unset($mediaParams['caption'], $mediaParams['metadata']); 
                    break;
                    
                case 'Location':
                    $method = 'sendLocation';
                    $coords = json_decode($this->attachmentContent, true);
                    $mediaParams['latitude'] = $coords['lat'];
                    $mediaParams['longitude'] = $coords['long'];
                    // Location کپشن و متادیتا ندارد
                    unset($mediaParams['caption'], $mediaParams['metadata']);
                    break;

                // سایر مدیاها که فایل محور هستند
                case 'Image': $method = 'sendImage'; break;
                case 'Video': $method = 'sendVideo'; break;
                case 'Voice': $method = 'sendVoice'; break;
                case 'Music': $method = 'sendMusic'; break;
                case 'File':  $method = 'sendFile';  break;
            }

            // افزودن فایل به پارامترها (برای متدهای غیر Contact/Location)
            if (!in_array($this->attachmentType, ['Contact', 'Location'])) {
                // حل کردن مسیر فایل (Local, URL, Storage)
                $filePath = $this->resolveFilePath($this->attachmentContent);
                
                // اختصاص به پارامتر مربوطه
                $mediaParams[$fileParamKey] = $filePath;
                
                // اگر نام فایل تنظیم شده باشد
                if ($this->attachmentFileName) {
                    $mediaParams['file_name'] = $this->attachmentFileName;
                }
                
                // اگر تامنیل داشته باشیم (برای ویدیو)
                if ($this->thumbnailPath && $this->attachmentType === 'Video') {
                    $mediaParams['thumb_inline'] = $this->thumbnailPath;
                }
            }

            // اجرای درخواست مدیا
            // فرض بر این است که کلاس والد متد makeRequest را دارد
            $result = $this->makeRequest($method, $mediaParams);
            
            // پاکسازی و بازگشت
            $this->resetFluent();
            $this->resetAttachments();
            return $result;
        }

        // 5. Handle Text / Edit (No Attachment)
        // --- SENARIO: SEND TEXT or EDIT MESSAGE ---
        
        // پردازش متن و متادیتا با Parsentinel
        $finalText = $this->fText;
        $metadata = null;
        
        if ($finalText !== null) {
            $parsed = $this->parseToRubika($finalText, $this->fParseMode); // Engage MetaTextTransformer Engine
            $finalText = $parsed['text'];
            if (!empty($parsed['metadata'])) {
                $metadata = $parsed['metadata'];
            }
        }

        // پارامترهای پایه متن
        $textParams = array_merge([
            'chat_id' => $targetChatId,
        ], $commonParams);

        if ($this->isEditMode) {
            // --- حالت ویرایش (EDIT) ---
            $textParams['message_id'] = $this->editMessageId;
            
            if ($finalText !== null) {
                // ویرایش متن (همراه با کیبورد احتمالی)
                $textParams['text'] = $finalText;
                if ($metadata) $textParams['metadata'] = $metadata;
                $method = 'editMessageText';
            } elseif ($this->fInlineKeyboard !== null) {
                // فقط ویرایش کیبورد (بدون تغییر متن)
                // در روبیکا متد editMessageReplyMarkup کمتر رایج است، اما اگر متن ندهیم
                // و از editMessageText استفاده کنیم ممکن است خطا دهد.
                // راهکار ایمن: معمولا کاربر متن را هم می‌فرستد.
                // اگر کاربر متن نفرستاده بود، سعی می‌کنیم از editMessageText استفاده نکنیم مگر مجبور شویم.
                // fallback: تلاش برای استفاده از editMessageText (ممکن است نیاز به متن داشته باشد)
                // یا اگر API روبیکا متد editMessageReplyMarkup دارد:
                // $method = 'editMessageReplyMarkup';
                // فعلاً فرض بر editMessageText است چون رایج‌تر است.
                 throw new InvalidArgumentException("For editing, please provide the text again (even if unchanged) to ensure stability in Rubika API.");
            } else {
                // نه متن داده شده نه کیبورد
                throw new InvalidArgumentException("For editing, provide text or keyboard.");
            }
        } else {
            // --- حالت ارسال جدید (NEW MESSAGE) ---
            if ($finalText === null) {
                 throw new InvalidArgumentException("Message text is required when not sending attachments.");
            }
            $textParams['text'] = $finalText;
            if ($metadata) $textParams['metadata'] = $metadata;
            $method = 'sendMessage';
        }

        // اجرای درخواست متن/ادیت
        $result = $this->makeRequest($method, $textParams);
        
        // پاکسازی وضعیت
        $this->resetFluent();
        $this->resetAttachments(); // محض احتیاط
        
        return $result;
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
