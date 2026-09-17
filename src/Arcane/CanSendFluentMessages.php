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
use KrubiK\Helpers\JackPoint; // Import "JackPoint" - The Tactical EventHook System

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
    public function message(string|RichMan $content): static
    {
        if($content)
            $this->fText = JackPoint::transform('spell.hex.put', $content, 'message', $this);
        $this->fParseMode = 'MarkdownMode';
        return $this;
    }

    /**
     * تنظیم متن پیام با فرمت HTML.
    */
    public function html(?string $html = null): static
    {
        if($html)
            $this->fText = JackPoint::transform('spell.hex.put', $html, 'html', $this);
        $this->fParseMode = 'HTML';
        return $this;
    }

    /**
     * تنظیم متن پیام با فرمت Markdown.
    */
    public function markdown(string $markdown): static
    {
        if($markdown)
            $this->fText = JackPoint::transform('spell.hex.put', $markdown, 'markdown', $this);
        $this->fParseMode = 'MarkdownMode';
        return $this;
    }

    /**
     * تنظیم متن پیام با فرمت MarkdownV2.
     * در روبیکا معمولا همان MarkdownMode استاندارد پاسخگو است.
    */
    public function markdownV2(string $markdown): static
    {
        if($markdown)
            $this->fText = JackPoint::transform('spell.hex.put', $markdown, 'markdownV2', $this);
        $this->fParseMode = 'MarkdownMode'; 
        return $this;
    }

    // ========================================================================
    // 2. Modifiers (Reply, Silent, etc.)
    // ========================================================================

    /**
     * Helper to send message without reply (Say), and without auto-send.
    */
    public function say(string|RichMan $text): static
    {
        if (!$this->chatId())
            return $this;
        $this->chat($this->chatId())->message($text);
        return $this;
    }

    /**
     * Helper to reply to the current message.
     * Automatically sets replyTo ID if available.
    */
    public function reply(string|RichMan $text): static
    {
        if (!$this->chatId()) {
            $this->message($text);
            return $this;
        }
        
        $builder = $this->chat($this->chatId());
        
        if ($msgId = $this->findMessageId()) {
            $builder->replyTo($msgId);
        }
        
        $builder->message($text);
        return $this;
    }

    /**
     * Helper to edit a specific message.
    */
    public function modify(string $messageId, string $newText): static
    {
        if (!$this->chatId()) return $this;

        $this->chat($this->chatId())
             ->messageId($messageId)
             ->message($newText);
             
        return $this;
    }

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

        // ⚡ JackPoint: allow plugins to rewrite the keyboard layout
        $this->fInlineKeyboard = JackPoint::transform(
            'spell.keys.flow',
            $this->fInlineKeyboard,
            $this
        );

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

        // ⚡ JackPoint: allow plugins to rewrite the reply keyboard
        $this->fReplyKeyboard = JackPoint::transform(
            'spell.keys.dock',
            $this->fReplyKeyboard,
            $this
        );

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
        $messageId = JackPoint::transform('builder.edit.target', $messageId, $this);

        $this->isEditMode = true;
        $this->editMessageId = $messageId;

        JackPoint::fire('builder.edit.updated', $messageId, $this);

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
        // ⚡ JackPoint: allow plugins to rewrite the source
        $fromChatId = JackPoint::transform(
            'dispatcher.forward.target',
            $fromChatId,
            $messageId,
            $this
        );

        $this->forcedMethod = 'forwardMessages';
        $this->forcedParams = [
            'from_chat_id' => $fromChatId,
            'message_ids' => [$messageId] // روبیکا آرایه می‌پذیرد
        ];

        JackPoint::fire('dispatcher.forward.updated', $fromChatId, $messageId, $this);

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
        JackPoint::fire('outgoing.delete.before', [$messageId], $chatId, $this);

        // استفاده از متد makeRequest والد یا apiRequest
        $result = $this->makeRequest('deleteMessages', [
            'chat_id' => $this->resolveChatId(null),
            'message_ids' => [$messageId]
        ]);

        JackPoint::fire('outgoing.delete.after', [$messageId], $result, $chatId, $this);

        return $result;
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

        JackPoint::fire('outgoing.delete.before', $flatIds, $chatId, $this);
        
        $result = $this->makeRequest('deleteMessages', [
            'chat_id' => $this->resolveChatId(null),
            'message_ids' => $flatIds
        ]);

        JackPoint::fire('outgoing.delete.after', $flatIds, $result, $chatId, $this);

        return $result;
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
            $targetChatId = $this->builder_chat_id ?? null;
        }

        // 2. ⚡ transform یک شانس نجات هم دارد — چون null ورودی قابل پر شدن است
        $targetChatId = JackPoint::transform(
            'builder.send.target',
            $targetChatId,
            $chatId,
            $this
        );

        if (!$targetChatId)
            throw new InvalidArgumentException("Chat ID is required for send().");

        JackPoint::fire('builder.send.started', $targetChatId, $this);

        try {

            // 2. Handle Forced Actions (Forward/Copy) - بالاترین اولویت
            if ($this->forcedMethod) {

                $params = array_merge(['chat_id' => $targetChatId], $this->forcedParams);

                // ⚡ JackPoint: allow plugins to rewrite the forced method + params
                [$method, $params] = JackPoint::transform(
                    'builder.resolve.forced',
                    [$this->forcedMethod, $params],
                    $targetChatId,
                    $this
                );

                // فوروارد نیازی به پردازش متن و کیبورد معمول ندارد
                $result = $this->makeRequest($this->forcedMethod, $params);
                $this->resetFluent();
                $this->resetAttachments();

                JackPoint::fire('builder.send.completed', $result, $targetChatId, null, 'forced', $this);

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

                JackPoint::fire('builder.send.completed', $result, $targetChatId, $richContent, 'rich', $this);

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

                JackPoint::fire('builder.send.completed', $result, $targetChatId, $commonParams, 'media', $this); // @Todo: expand_data

                return $result;
            }

            // 6. Handle Text / Edit (No Attachment)
            // ── send/edit(Text) path
            // اجرای درخواست متن/ادیت
            $result = $this->dispatchTextMessage($targetChatId, $commonParams);
            
            // پاکسازی وضعیت
            $this->resetFluent();
            $this->resetAttachments(); // محض احتیاط

            JackPoint::fire('builder.send.completed', $result, $targetChatId, $commonParams, 'text', $this);
            
            return $result;
        } catch (\Throwable $e) {

            // ⚡ JackPoint: allow plugins to observe / report / recover
            $allowReport = JackPoint::fire(
                'builder.send.failed',
                $e,
                $targetChatId,
                $this
            );

            // cleanup state even on failure
            $this->resetFluent();
            $this->resetAttachments();

            if ($allowReport === false) {
                return ['status' => 'ERROR', 'message' => $e->getMessage()];
            }

            throw $e;
        }
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

            // ⚡ JackPoint: transform text payload before edit
            $payload = JackPoint::transform(
                'outgoing.text.payload',
                [
                    'message_id' => $this->editMessageId,
                    'text'       => (string) $this->fText,
                ],
                'edit',
                $targetChatId,
                $this
            );

            // ویرایش متن (همراه با کیبورد احتمالی)
            $result = $this->makeRequest('editMessageText', array_merge($textParams, $payload));

            JackPoint::fire('outgoing.text.dispatched', $result, 'edit', $targetChatId, $this);

            return $result;
        }

        // --- حالت ارسال جدید (NEW MESSAGE) ---

        if ($this->fText === null) {
            throw new InvalidArgumentException('[Krubot] send() requires text when no attachment is pending.');
        }

        // ⚡ JackPoint: transform text payload before send
        $payload = JackPoint::transform(
            'outgoing.text.payload',
            [
                'text' => (string) $this->fText,
            ],
            'new',
            $targetChatId,
            $this
        );

        // اجرای درخواست ارسال
        // Hand off to InteractsWithApi::makeRequest which calls driver()->apiRequest() &+ AmethystMatrix debug
        $result = $this->makeRequest('sendMessage', array_merge($textParams, $payload));

        JackPoint::fire('outgoing.text.dispatched', $result, 'new', $targetChatId, $this);
        return $result;
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

            $payload = JackPoint::transform(
                'outgoing.media.payload.contact',
                [
                    'phone_number' => $this->extraPayload['phone_number'],
                    'first_name'   => $this->extraPayload['first_name'],
                    'last_name'    => $this->extraPayload['last_name'] ?? '',
                ],
                'Contact',
                $chatId,
                $this
            );

            $result = $this->makeRequest($method, array_merge($params, $payload));

            JackPoint::fire('outgoing.media.dispatched', $result, 'Contact', $chatId, $this);

            return $result;
        }

        if ($this->attachmentType === 'Location') {

            $coords = json_decode($this->attachmentContent, true);

            $payload = JackPoint::transform(
                'outgoing.media.payload.location',
                [
                    'latitude'  => $coords['lat'],
                    'longitude' => $coords['long'],
                ],
                'Location',
                $chatId,
                $this
            );

            $result = $this->makeRequest($method, array_merge($params, $payload));

            JackPoint::fire('outgoing.media.dispatched', $result, 'Location', $chatId, $this);

            return $result;
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

        // ⚡ JackPoint: full payload transform for file media
        $params = JackPoint::transform(
            'outgoing.media.payload.media',
            $params,
            $this->attachmentType,
            $chatId,
            $this
        );

        // اجرای درخواست مدیا
        // InteractsWithApi متد makeRequest را دارد
        $result = $this->makeRequest($method, $params);

        JackPoint::fire('outgoing.media.dispatched', $result, $this->attachmentType, $chatId, $this);

        return $result;
    }

    

    /**
     * Send a message to a SPECIFIC target (User/Group GUID) directly.
    */
    public function to(string $targetChatId, string|RichMan $text): array
    {

        $targetChatId = JackPoint::transform('outgoing.to.target', $targetChatId, $text, $this);
        $text         = JackPoint::transform('outgoing.to.text', $text, $targetChatId, $this);

        return $this->chat($targetChatId)
            ->message($text)
            ->send();
    }

    /**
     * Delete the current message immediately.
    */
    public function deleteCurrent(): array
    {

        $verdict = JackPoint::fire('outgoing.delete.current.before', $this);
        if ($verdict === false) {
            return ['status' => 'ERROR', 'message' => 'vetoed_by_plugin'];
        }

        if (!$this->chatId() || !$this->findMessageId()) {
            return ['status' => 'ERROR', 'message' => 'No context available'];
        }
        
        $result = $this->chat($this->chatId())
            ->messageId($this->findMessageId())
            ->sendDelete();

        JackPoint::fire('outgoing.delete.current.after', $result, $this);

        return $result;
    }

    /**
     * Edit the current message immediately (Useful for updating Bot's own menus).
    */
    public function editCurrent(string $newText): array
    {
        if (!$this->chatId() || !$this->findMessageId()) {
            return ['status' => 'ERROR', 'message' => 'No context available'];
        }

        $newText = JackPoint::transform('outgoing.edit.current.text', $newText, $this);

        return $this->chat($this->chatId())
            ->messageId($this->findMessageId())
            ->message($newText)
            ->editMessage();
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
            return JackPoint::transform('rich.resolve.richman', $content, $this);
        }

        // A single RichEntity.
        if ($content instanceof RichEntity) {
            return JackPoint::transform('rich.resolve.entity', RichMan::summon()->add($content), $content, $this);
        }

        // An array containing Rich entities.
        if (is_array($content)) {

            foreach ($content as $item) {

                if (
                    $item instanceof RichEntity
                    || $this->containsRichEntity($item)
                ) {
                    return JackPoint::transform('rich.resolve.entity', RichMan::summon()->add($content), $content, $this);
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

        // ⚡ JackPoint: final param bag transform (escape hatch)
        return JackPoint::transform(
            'outgoing.params.build',
            $commonParams,
            $this
        );
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

        JackPoint::fire('outgoing.fluent.reset', $this);
    }

    /**
     * هلپر متد برای حل کردن chat_id در صورتی که null باشد.
     * (برای استفاده داخلی در متدهای deleteMessage و ...)
    */
    protected function resolveChatId(?string $chatId): string
    {
        $id = $chatId ?? ($this->chat_id ?? null);
        if (!$id) {
             $id = $this->builder_chat_id ?? null;
        }

        // ⚡ transform فرصت rescue هم دارد — روی null هم اجرا می‌شود
        $id = JackPoint::transform('resolve.chat.id', $id, $chatId, $this);

        if (!$id)
            throw new InvalidArgumentException("Chat ID is required.");

        return $id;
    }
}
