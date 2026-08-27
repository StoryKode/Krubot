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

use Illuminate\Support\Facades\Storage;

/**
 * Trait CanSendMedia
 * 
 * این تریت وظیفه مدیریت و نگهداری وضعیت فایل‌ها و مدیاهای پیوست شده به پیام را بر عهده دارد.
 * این تریت به تنهایی ارسال را انجام نمی‌دهد، بلکه داده‌ها را برای متد send در CanSendFluentMessages آماده می‌کند.
 * 
 * کاملا سازگار با:
 * - سیستم فایل لاراول (Storage Facade)
 * - آدرس‌های اینترنتی (URL)
 * - مسیرهای لوکال (Local Paths)
 * - شناسه فایل‌های روبیکا (File IDs)
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait CanSendMedia
{
    // --- Media State Properties ---
    // نوع پیوست (Image, File, Voice, Music, Video, Contact, Location)
    protected ?string $attachmentType = null;
    
    // محتوای اصلی (مسیر فایل، شناسه فایل، یا جیسون مختصات)
    protected string|null $attachmentContent = null;
    
    // نام فایل (برای اسناد و فایل‌ها)
    protected ?string $attachmentFileName = null;
    
    // کپشن (توضیحات زیر مدیا)
    protected ?string $attachmentCaption = null;
    
    // مسیر تصویر بندانگشتی (کاور ویدیو)
    protected ?string $thumbnailPath = null;

    // --- Special Payloads ---
    // داده‌های اضافه برای متدهای خاص مثل Contact یا Venue که ساختار فایل ندارند
    protected array $extraPayload = [];

    // ========================================================================
    // 1. Fluent Setters (Media Types)
    // ========================================================================

    /**
     * تنظیم تصویر برای ارسال.
     * 
     * @param string $path مسیر فایل، URL یا FileID
     * @param string|null $filename نام فایل (اختیاری)
     * @return static
     */
    public function photo(string $path, ?string $filename = null): static
    {
        $this->attachmentType = 'Image';
        $this->attachmentContent = $path;
        $this->attachmentFileName = $filename;
        return $this;
    }

    /**
     * تنظیم سند/فایل برای ارسال.
     * 
     * @param string $path مسیر فایل، URL یا FileID
     * @param string|null $filename نام فایل (اختیاری)
     * @return static
     */
    public function document(string $path, ?string $filename = null): static
    {
        $this->attachmentType = 'File';
        $this->attachmentContent = $path;
        $this->attachmentFileName = $filename;
        return $this;
    }

    /**
     * تنظیم ویدیو برای ارسال.
     * 
     * @param string $path مسیر فایل، URL یا FileID
     * @param string|null $filename نام فایل (اختیاری)
     * @return static
     */
    public function video(string $path, ?string $filename = null): static
    {
        $this->attachmentType = 'Video';
        $this->attachmentContent = $path;
        $this->attachmentFileName = $filename;
        return $this;
    }

    /**
     * تنظیم ویس (Voice Note) برای ارسال.
     * 
     * @param string $path مسیر فایل، URL یا FileID
     * @param string|null $filename نام فایل (اختیاری)
     * @return static
     */
    public function voice(string $path, ?string $filename = null): static
    {
        $this->attachmentType = 'Voice';
        $this->attachmentContent = $path;
        $this->attachmentFileName = $filename;
        return $this;
    }

    /**
     * تنظیم فایل صوتی (موزیک) برای ارسال.
     * 
     * @param string $path مسیر فایل، URL یا FileID
     * @param string|null $filename نام فایل (اختیاری)
     * @return static
     */
    public function audio(string $path, ?string $filename = null): static
    {
        $this->attachmentType = 'Music';
        $this->attachmentContent = $path;
        $this->attachmentFileName = $filename;
        return $this;
    }

    /**
     * تنظیم موقعیت مکانی (Location) برای ارسال.
     * 
     * @param float $latitude عرض جغرافیایی
     * @param float $longitude طول جغرافیایی
     * @return static
     */
    public function location(float $latitude, float $longitude): static
    {
        $this->attachmentType = 'Location';
        // ذخیره مختصات به صورت JSON برای بازخوانی راحت‌تر در متد send
        $this->attachmentContent = json_encode(['lat' => $latitude, 'long' => $longitude]);
        return $this;
    }

    /**
     * تنظیم مخاطب (Contact) برای ارسال.
     * 
     * @param string $phoneNumber شماره تلفن (مثال: 0912...)
     * @param string $firstName نام مخاطب
     * @param string|null $lastName نام خانوادگی مخاطب (اختیاری)
     * @return static
     */
    public function contact(string $phoneNumber, string $firstName, ?string $lastName = null): static
    {
        $this->attachmentType = 'Contact';
        $this->extraPayload = [
            'phone_number' => $phoneNumber,
            'first_name' => $firstName,
            'last_name' => $lastName
        ];
        return $this;
    }

    // ========================================================================
    // 2. Polyfills & Enhancements (Missing Features Simulation)
    // ========================================================================

    /**
     * ارسال انیمیشن (GIF).
     * نکته: در API روبیکا نوع خاص Animation وجود ندارد، بنابراین به عنوان File ارسال می‌شود.
     * 
     * @param string $path مسیر فایل گیف
     * @param string|null $filename نام فایل
     * @return static
     */
    public function animation(string $path, ?string $filename = null): static
    {
        $this->attachmentType = 'File'; // Fallback به فایل
        $this->attachmentContent = $path;
        $this->attachmentFileName = $filename;
        return $this;
    }

    /**
     * ارسال مکان با جزئیات (Venue).
     * نکته: در API روبیکا نوع Venue وجود ندارد.
     * استراتژی: ارسال Location و افزودن عنوان و آدرس به کپشن یا پیام جداگانه.
     * در اینجا ما آن را برای الحاق به کپشن آماده می‌کنیم.
     * 
     * @param float $latitude عرض جغرافیایی
     * @param float $longitude طول جغرافیایی
     * @param string $title عنوان مکان
     * @param string $address آدرس دقیق
     * @return static
     */
    public function venue(float $latitude, float $longitude, string $title, string $address): static
    {
        // تنظیم مختصات پایه
        $this->location($latitude, $longitude);
        
        // ذخیره اطلاعات اضافی برای الحاق به کپشن در لحظه ارسال
        $this->extraPayload['venue_info'] = "\n\n📍 " . $title . "\n" . $address;
        
        return $this;
    }

    /**
     * افزودن تصویر بندانگشتی (Thumbnail/Cover).
     * معمولاً برای ویدیوها استفاده می‌شود.
     * 
     * @param string $path مسیر تصویر کاور
     * @return static
     */
    public function thumbnail(string $path): static
    {
        $this->thumbnailPath = $this->resolveFilePath($path);
        return $this;
    }

    /**
     * تنظیم متن توضیحات (Caption) برای مدیا.
     * 
     * @param string $text متن توضیحات
     * @return static
     */
    public function setCaption(string $text): static
    {
        $this->attachmentCaption = $text;
        return $this;
    }

    // ========================================================================
    // 3. Internal Logic & Helpers
    // ========================================================================

    /**
     * بررسی می‌کند که آیا پیوستی در صف ارسال وجود دارد یا خیر.
     * توسط متد send استفاده می‌شود.
     * 
     * @return bool
     */
    protected function hasPendingAttachment(): bool
    {
        return $this->attachmentType !== null;
    }

    /**
     * حل‌کننده هوشمند مسیر فایل.
     * 
     * اولویت‌ها:
     * 1. اگر URL باشد -> همان را برمی‌گرداند.
     * 2. اگر فایل لوکال وجود داشته باشد -> مسیر کامل را برمی‌گرداند.
     * 3. اگر در Storage لاراول باشد -> مسیر فیزیکی را از Storage درمی‌آورد.
     * 4. در غیر این صورت -> همان رشته ورودی (که ممکن است FileID باشد) را برمی‌گرداند.
     * 
     * @param string $path
     * @return string
     */
    protected function resolveFilePath(string $path): string
    {
        // اگر URL معتبر است
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        
        // اگر مسیر فیزیکی وجود دارد
        if (file_exists($path)) {
            return $path;
        }
        
        // اگر در دیسک Storage لاراول است
        if (Storage::exists($path)) {
            return Storage::path($path);
        }
        
        // فرض بر FileID یا رشته خام
        return $path;
    }

    /**
     * بازنشانی تمام متغیرهای وضعیت مربوط به پیوست‌ها.
     * باید پس از هر بار ارسال موفق صدا زده شود.
     */
    protected function resetAttachments(): void
    {
        $this->attachmentType = null;
        $this->attachmentContent = null;
        $this->attachmentFileName = null;
        $this->attachmentCaption = null;
        $this->thumbnailPath = null;
        $this->extraPayload = [];
    }

    // ========================================================================
    // 3. Old Style Logic & Helpers
    // ========================================================================
    
    /**
     * Send a Voice message.
     *
     * @param string $voice File ID or Path
     * @param string|null $caption Optional caption
     * @param int|null $replyTo Optional reply to message ID
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null)
     * @return array
     */
    public function sendVoice(string $voice, ?string $caption = null, ?int $replyTo = null, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        // Note: If $voice is a path, handling upload logic is complex here.
        // Assuming $voice is usually a file_id or handled by a pre-upload logic if path.
        
        $params = [
            'chat_id' => $realChatId,
            'voice'   => $voice,
        ];

        if ($caption) $params['caption'] = $caption;
        if ($replyTo) $params['reply_to_message_id'] = $replyTo;

        return $this->makeRequest('sendVoice', $params);
    }

    /**
     * Send a photo (Uploads local file OR sends by file_id).
     *
     * @param string $photo Path to local file OR file_id string
     * @param string|null $caption Optional caption
     * @param int|null $replyTo Optional reply to message ID
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null)
     * @return array
     */
    public function sendPhoto(string $photo, ?string $caption = null, ?int $replyTo = null, ?string $chatId = null): array
    {
        // Resolve Chat ID first
        $realChatId = $this->resolveChatId($chatId);
        
        // Set chat ID for the internal builder
        $this->chat($realChatId);

        // Determine if input is file path or file_id
        if (file_exists($photo)) {
            $this->file($photo); // Trigger upload process
        } else {
            $this->file_id($photo); // Use existing file_id
            $this->file_type('Image'); // Default type
        }

        // Optional settings
        if ($caption) {
            $this->caption($caption);
        }
        
        if ($replyTo) {
            $this->replyTo($replyTo);
        }

        // Execute parent method (assumed from KrubiK structure)
        return $this->sendFile();
    }

    /**
     * Send Media Group (Album).
     *
     * @param array $media Array of InputMedia objects (or arrays)
     * @param int|null $replyTo Optional reply to message ID
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null)
     * @return array
     */
    public function sendMediaGroup(array $media, ?int $replyTo = null, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        $params = [
            'chat_id' => $realChatId,
            'media'   => $media, // API expects Array, library handles JSON encoding if needed
        ];

        if ($replyTo) $params['reply_to_message_id'] = $replyTo;

        return $this->makeRequest('sendMediaGroup', $params);
    }

    /**
     * Send a sticker.
     *
     * @param string $sticker Sticker to send. Pass a file_id as String.
     * @param int|null $replyToMessageId If the message is a reply, ID of the original message
     * @param array $keypad Optional inline keyboard (reply_markup)
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null)
     * @return array
     */
    public function sendSticker(string $sticker, ?int $replyToMessageId = null, array $keypad = [], ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        $params = [
            'chat_id' => $realChatId,
            'sticker' => $sticker,
        ];

        if ($replyToMessageId) {
            $params['reply_to_message_id'] = $replyToMessageId;
        }

        if (!empty($keypad)) {
            $params['reply_markup'] = ['inline_keyboard' => $keypad];
        }

        return $this->makeRequest('sendSticker', $params);
    }
}
