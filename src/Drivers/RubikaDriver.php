<?php

namespace KrubiK\Drivers;
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

use KrubiK\Drivers\Contracts\BotDriverInterface;
use KrubiK\Drivers\Contracts\VanguardInterface;
use RubikaBot\Bot as VanguardCore; // کتابخانه اصلی
use KrubiK\Drivers\Arcane\NeonVitality;   // سوپر تریت
use KrubiK\Drivers\Arcane\MetaTextTransformer; // Rubika text parsing is RubikaDriver's responsibility
use KrubiK\Drivers\Strategies\DeferredResponse;

class RubikaDriver extends VanguardCore implements BotDriverInterface, VanguardInterface
{
    // 💉 Inject the Soul
    use NeonVitality;

    use MetaTextTransformer; // parse rich-blocks to rubika-compatible format

    // -------------------------------------------------------------------------
    // Method name map: internal/Telegram name → Rubika API name
    //
    // Split into two buckets so identical names don't pollute the rename list.
    // -------------------------------------------------------------------------

    public const DEF_BASE_URL = 'https://botapi.rubika.ir/v3/';
    private string $baseApiUrl;

    /**
     * Methods whose names are identical on both Telegram and Rubika.
     * Listed explicitly so the intent is clear — not implicit by absence.
     *
     * @var string[]
    */
    private const PASSTHROUGH_METHODS = [
        'sendMessage',
        'sendLocation',
        'sendContact',
        'sendVoice',
        'sendVideo',
        'editMessageText',
        'deleteMessages',
        'forwardMessages',
        'answerCallbackQuery',
        'getChatMember',
        'banChatMember',
        'unbanChatMember',
        'setWebhook',
        'getUpdates',
    ];

    /**
     * Methods that need renaming: internal/Telegram name → Rubika API wire.
     * Only non-identical pairs live here — no noise.
     *
     * @var array<string, string>
    */
    private const RENAMED_METHODS = [
        'sendPhoto'              => 'sendImage',
        'sendAudio'              => 'sendMusic',
        'sendDocument'           => 'sendFile',
        'editMessageReplyMarkup' => 'editMessageText',  // Rubika edits markup via same endpoint
        'copyMessage'            => 'forwardMessages',  // no native copy; forward is closest
        'leaveChat'              => 'leaveGroup',
        'getChat'                => 'getGroupInfo',
        'getFile'                => 'getFileUrl',
        'kickChatMember'         => 'banChatMember',    // TG legacy alias
    ];

    // -------------------------------------------------------------------------
    // Fields that are Telegram-only and must be stripped before shipping to the wire
    // -------------------------------------------------------------------------
    private const STRIP_FIELDS = [
        'parse_mode',
        'entities',
        'caption_entities',
        'disable_web_page_preview',
        'allow_sending_without_reply',
        'protect_content',
        'message_thread_id',
        'has_spoiler',
        'supports_streaming',
        'disable_content_type_detection',
    ];

    /**
    * Dynamically change private $baseApiUrl value for the current driver.
    *
    * @param string $newUrl The new base URL to inject at runtime.
    * @return static Returns self for method chaining.
    * @throws \ReflectionException (Handled silently in trait)
    */
    public function setBaseUrl(string $newUrl): static
    {
        $this->baseApiUrl = $newUrl;
        return $this;
    }

    /**
    * Retrieve the current private $baseApiUrl value from the driver.
    *
    * @return string
    * @throws \ReflectionException
    */
    public function getBaseUrl(): string
    {
        return $this->baseApiUrl;
    }

    /**
     * RubikaDriver constructor.
     *
     * @param array $config The specific configuration for this driver.
    */
    public function __construct(array $config)
    {
        // 1. Call the Old God (VanguardCore) constructor
        // This sets up the Token and BaseUrl naturally.
        parent::__construct($config['token'], $config['config'] ?? []);
        $this->setBaseUrl(rtrim(($config['base_url'] ?? self::DEF_BASE_URL), '/') . '/' . $config['token'] . '/');

        // 2. Ignite the NeonSoul Engine (Arcane)
        $this->igniteNeon($config);
    }

    // =========================================================================
    // 🚜 IMPLEMENTATION OF BotDriverInterface
    // =========================================================================
    // اینجا ما متدهای اینترفیس (مثل send) را به متدهای Vanguard (مثل sendText) وصل می‌کنیم.
    // از متغیرهای Context که توسط NeonVitality مدیریت می‌شوند استفاده می‌کنیم.

    public function legacySend(): array
    {
        // Example mapping:
        // If we have text, use sendText. If we have file_id, use sendFile, etc.
        
        // Using properties from InteractsWithContext: $this->chat_id, $this->text_content
        return $this->sendText($this->chat_id, $this->text_content);
    }

    public function editMessage(): array
    {
        return $this->editMessageText($this->message_id, $this->text_content);
    }
    
    // و بقیه متدها...
    // نکته مهم: چون VanguardCore را اکستند کردیم، اگر متدی در اینترفیس نباشد
    // ولی در Vanguard باشد (مثلا getBannedUsers)، مستقیماً قابل صدا زدن است!

    /**
     * ⚡️ THE THANOS SNAP ⚡️
     * قلب تپنده درایور. این متد تمام درخواست‌های سطح بالا را می‌گیرد،
     * تمیزکاری می‌کند، ترجمه می‌کند و به سمت سرور Rubika شلیک می‌کند.
     *
     * ⚡ Engages the new pulseApi() from InteractsWithApi. ⚡
     * We intercept BEFORE that: translate method name + normalize params,
     * then hand off to Krubot::pulseApi() which does the actual HTTP call to Driver's Server.
     *
     * Execution chain:
     *   RubikaDriver::makeRequest()
     *     → resolveMethod()        translate method name between Rubika/Telegram
     *     → normalizeParams()      strip/rename/transform fields for Rubika API
     *     → Krubot::pulseApi()  Instead of :: core()::makeRequest()
     *
     * @param  string  $method  نام متد API , Internal or Telegram-style method name
     * @param array $params پارامترهای درخواست
     * @return array|DeferredResponse پاسخ خام آرایه‌ای (برای استفاده داخلی)
    */
    public function makeRequest(string $method, array $params = []): array|DeferredResponse
    {
        $rubikaMethod = $this->resolveMethod($method);
        $rubikaParams = $this->normalizeParams($rubikaMethod, $params);

        /// return $this->forceCallMethod('apiRequest', [$rubikaMethod, $rubikaParams], $this); // ---Obsolete---
        // Hand off to InteractsWithApi modern ApiRequest
        return warlord()->pulseApi($rubikaMethod, $rubikaParams, $this);
    }

    // =========================================================================
    // PRIVATE: Method resolution
    // =========================================================================

    /**
     * Resolve an internal/Telegram method name to its Rubika API equivalent.
     *
     * Checks RENAMED_METHODS first; falls back to PASSTHROUGH_METHODS verification;
     * unknown methods pass through as-is (VanguardCore will handle or reject them).
    */
    protected function resolveMethod(string $method): string
    {
        // Fast path: explicit rename exists
        if (isset(self::RENAMED_METHODS[$method])) {
            return self::RENAMED_METHODS[$method];
        }

        // Passthrough: name is already correct for Rubika (or unknown — let core decide)
        return $method;
    }

    // =========================================================================
    // PRIVATE: Param normalization pipeline
    // =========================================================================

    /**
     * Transform a raw internal param bag into a Rubika-wire-ready payload.
     *
     * Pipeline order matters:
     *   1. Parse text/caption → extract metadata (SoC: replaces MetaTextTransformer in trait)
     *   2. Keyboard normalization (inline_keyboard → inline_keypad shape)
     *   3. chat_keypad type annotation
     *   4. file_inline resolution
     *   5. Strip Telegram-only fields
    */
    protected function normalizeParams(string $rubikaMethod, array $params): array
    {
        // ── 1. Text/caption → Rubika metadata (Rubika enforcer owns this, not the Warlord) ──
        //
        // CanSendFluentMessages::send() passes raw text/caption with a `_parse_mode`
        // hint key (underscore-prefixed so it won't conflict with TG parse_mode).
        // We consume it here and transform to Rubika's metadata object.
        $parseMode = $params['_parse_mode'] ?? 'MarkdownMode';
        unset($params['_parse_mode']); // always consume the hint

        // روبیکا متادیتا را برای کپشن هم پردازش می‌کند
        foreach (['text', 'caption'] as $textField) {
            if (!empty($params[$textField])) {
                // MetaTextTransformer Engine lives/rules here //
                $parsed = $this->parseToRubika($params[$textField], $parseMode);
                $params[$textField] = $parsed['text'];

                if (!empty($parsed['metadata']['meta_data_parts'])) {
                    $params['metadata'] = $parsed['metadata'];
                }
            }
        }

        // ── 2. Inline keyboard
        // ────────────────────────────────────────────────────
        // ── Inline keyboard: rename key + normalize button shape ───────────
        if (isset($params['inline_keyboard'])) {
            $params['inline_keypad'] = $this->normalizeInlineKeypad($params['inline_keyboard']);
            unset($params['inline_keyboard']);
        }

        // ── 3. Chat keypad 
        // ────────────────────────────────────────────────────
        // ── Chat keypad: detect remove vs new, annotate type
        if (isset($params['chat_keypad'])) {
            if (!empty($params['chat_keypad']['remove_keyboard'])) {
                $params['chat_keypad_type'] = 'Remove';
                unset($params['chat_keypad']);
            } else {
                // Strip TG-only keys that leaked in from ReplyKeyboard::toArray()
                unset($params['chat_keypad']['selective'], $params['chat_keypad']['keyboard']);
                $params['chat_keypad_type'] = $params['chat_keypad_type'] ?? 'New';
            }
        }

        // ── 4. File resolution 
        // ────────────────────────────────────────────────────
        // ── file_inline: resolve path/URL to Rubika FileObject
        if (isset($params['file_inline'])) {
            $params['file_inline'] = $this->resolveFileInline($params['file_inline']);
        }
        if (isset($params['thumb_inline'])) {
            $params['thumb_inline'] = $this->resolveFileInline($params['thumb_inline']);
        }

        // ─────── 5. Strip Telegram-only fields that Rubika API does not speak ───────
        $params = array_diff_key($params, array_flip(self::STRIP_FIELDS));

        return $params;
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Normalize an inline keyboard to Rubika's `inline_keypad` object shape.
     *
     * Already-native (has `buttons` key): pass through.
     * TG-style (array of rows of buttons): convert to Rubika button objects.
    */
    protected function normalizeInlineKeypad(array $keypad): array
    {

        if (isset($keypad['buttons'])) {
            return $keypad; // Keyboard::toArray() already produced Rubika-native format
        }

        $rubikaRows = [];
        foreach ($keypad as $row) {
            $rubikaRow = [];
            foreach ((array) $row as $btn) {
                $rubikaBtn = [
                    'ButtonID'   => $btn['ButtonID'] ?? uniqid('btn_', true),
                    'ButtonType' => $btn['ButtonType'] ?? 'Simple',
                    'ButtonText' => $btn['text'] ?? $btn['ButtonText'] ?? '',
                ];

                if (!empty($btn['callback_data'])) {
                    $rubikaBtn['action'] = ['Type' => 'CallbackData', 'Data' => $btn['callback_data']];
                } elseif (!empty($btn['url'])) {
                    $rubikaBtn['ButtonType'] = 'OpenURL';
                    $rubikaBtn['action']     = ['Type' => 'OpenURL', 'Data' => $btn['url']];
                } elseif (!empty($btn['action'])) {
                    $rubikaBtn['action']     = $btn['action'];
                    $rubikaBtn['ButtonType'] = $btn['ButtonType'] ?? 'Simple';
                }

                if (isset($btn['width'])) {
                    $rubikaBtn['width'] = $btn['width']; // PowerButton::col() grid value
                }

                $rubikaRow[] = $rubikaBtn;
            }
            if ($rubikaRow) {
                $rubikaRows[] = $rubikaRow;
            }
        }

        return ['buttons' => $rubikaRows];
    }

    /**
     * Resolve a file reference to a Rubika FileObject array.
     *
     * A) Array with `file_id`   → already a FileObject, pass through
     * B) Rubika file_id string  → wrap minimally
     * C) Local path             → upload via VanguardCore
     * D) Remote URL             → download → upload
    */
    protected function resolveFileInline(mixed $file): array
    {
        if (is_array($file) && isset($file['file_id'])) {
            return $file;
        }

        if (is_string($file) && file_exists($file)) {

            // حل کردن مسیر فایل (Local, URL, Storage)
            $filePath = null;
            if(method_exists($this, 'resolveFilePath'))
                $filePath = $this->resolveFilePath($file); // attachmentContent

            if((!$filePath) && method_exists($this, 'uploadFile'))
                $filePath = $this->uploadFile($file);

            if($filePath !== null)
                return ['file_path' => $filePath];
        }

        if (is_string($file) && str_starts_with($file, 'http')) {
            $tmp = sys_get_temp_dir() . '/krubot_' . md5($file);
            file_put_contents($tmp, file_get_contents($file));
            $result = $this->resolveFileInline($tmp);
            @unlink($tmp);
            return $result;
        }

        // Treat as pre-existing file_id
        return ['file_id' => (string) $file];
    }

}
