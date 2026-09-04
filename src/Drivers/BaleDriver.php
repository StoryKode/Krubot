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

use EFive\Bale\Api as BaleCore;
use KrubiK\Drivers\Contracts\BotDriverInterface;
use KrubiK\Drivers\Contracts\Layers\StandardDriverInterface;
use KrubiK\Drivers\Arcane\NeonVitality;

// ایمپورت آبجکت‌های استاندارد بله برای رعایت قرارداد اینترفیس
use EFive\Bale\Objects\Message;
use EFive\Bale\Objects\User;
use EFive\Bale\Objects\Chat;
use EFive\Bale\Objects\File;
use EFive\Bale\Objects\Update;
use EFive\Bale\Objects\WebhookInfo;
use EFive\Bale\Objects\StickerSet;
use EFive\Bale\Objects\BotCommand;

// ایمپورت ابزارهای KrubiK برای ترجمه کیبورد
use KrubiK\Keyboard\Keyboard as KrubiKInlineKeyboard;
use KrubiK\Keyboard\ReplyKeyboard as KrubiKReplyKeyboard;

class BaleDriver extends BaleCore implements BotDriverInterface, StandardDriverInterface, BaleVipInterface
{
    // 💉 تزریق روح نئونی (Fluent API + Context Management)
    use NeonVitality;

    protected array $config;

    /**
     * سازنده درایور بله.
     * توکن را از کانفیگ می‌گیرد و هسته اصلی SDK را راه می‌اندازد.
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $token = $config['token'] ?? $config['authtoken'] ?? null;

        if (empty($token)) {
            throw new \InvalidArgumentException("Bale Token is missing in configuration.");
        }

        // 1. راه‌اندازی هسته اصلی (ارث‌بری از EFive\Bale\Api)
        parent::__construct($token);

        // 2. روشن کردن موتور نئونی KrubiK
        $this->igniteNeon();
    }

    /**
     * ⚡️ THE THANOS SNAP ⚡️
     * قلب تپنده درایور. این متد تمام درخواست‌های سطح بالا را می‌گیرد،
     * تمیزکاری می‌کند، ترجمه می‌کند و به سمت سرور بله شلیک می‌کند.
     *
     * @param string $method نام متد API (مثلا sendMessage)
     * @param array $params پارامترهای درخواست
     * @return array پاسخ خام آرایه‌ای (برای استفاده داخلی)
     */
    public function makeRequest(string $method, array $params = []): array
    {
        $finalMethod = $method;
        $finalParams = $params;

        // Handle RichMan object for sending messages
        if (isset($finalParams['text']) && $finalParams['text'] instanceof RichMan) {
            $richMan = $finalParams['text'];

            // For Bale API, we must send simple text, not rich blocks
            $finalMethod = 'sendMessage';

            // Use toText() to render RichMan Elements as Markdown
            $finalParams['text'] = $richMan->toText();

            // Remove keys that are irrelevant or conflict with plain text message
            unset($finalParams['parse_mode'], $finalParams['entities'], $finalParams['isRich'], $finalParams['rich_blocks']);
        }
        // Handle isRich flag if set true but no RichMan object (optional)
        elseif (isset($finalParams['isRich']) && $finalParams['isRich'] === true) {

            // Since Bale API does not support rich messages natively, fallback to markdown text version
            if (isset($finalParams['rich_blocks']) && is_array($finalParams['rich_blocks'])) {
                // Convert rich_blocks to plain text string
                $finalParams['text'] = $this->convertRichBlocksToSimpleText($finalParams['rich_blocks']);
            }

            $finalParams['text'] = $finalParams['text'] ?? '';

            $finalMethod = 'sendMessage';

            unset($finalParams['parse_mode'], $finalParams['isRich'], $finalParams['isRtl'], $finalParams['rich_blocks']);
        }

        // 1. نرمال‌سازی: تبدیل کیبوردها و فایل‌ها به فرمت بله
        $normalizedParams = $this->normalizePayload($finalParams);

        // 2. تشخیص هوشمند نوع ارسال (Multipart یا JSON)
        $hasFile = $this->detectFileInParams($params);

        try {
            if ($hasFile) {
                // اگر فایل داریم، از متد multipart کلاس پدر استفاده کن
                $response = $this->multipart($finalMethod, $normalizedParams);
            } else {
                // اگر متن خالی است، از متد post کلاس پدر استفاده کن
                $response = $this->post($finalMethod, $normalizedParams);
            }
        } catch (\Exception $e) {
            // هندلینگ خطا یا لاگ کردن
            throw $e;
        }

        // 3. بازگرداندن آرایه (SDK بله معمولا آبجکت یا آرایه برمی‌گرداند، ما به آرایه کست می‌کنیم)
        // اگر پاسخ در کلید 'result' بود (استاندارد تلگرام/بله)، آن را استخراج می‌کنیم
        if (is_array($response) && isset($response['result'])) {
            return $response['result'];
        }

        return (array) $response;
    }

    // =========================================================================
    // 🧠 TACTICAL TRANSLATOR (تبدیل‌گر هوشمند)
    // =========================================================================

    /**
     * پارامترها را برای بله آماده می‌کند.
     * کیبوردها را ترجمه و فایل‌ها را آماده می‌کند.
     */
    protected function normalizePayload(array $params): array
    {
        // --- بخش ۱: ترجمه کیبورد (KrubiK -> Bale) ---
        // بله دقیقا ساختار تلگرام را دارد.

        // استانداردسازی کلیدها: KrubiK ممکن است 'keypad' بفرستد، بله 'reply_markup' می‌خواهد
        if (isset($params['keypad'])) {
            $params['reply_markup'] = $params['keypad'];
            unset($params['keypad']);
        }

        if (isset($params['reply_markup'])) {
            $markup = $params['reply_markup'];

            if ($markup instanceof KrubiKInlineKeyboard) {
                // تبدیل به JSON رشته‌ای طبق داکیومنت بله
                $params['reply_markup'] = json_encode($this->transformInlineKeyboard($markup));
            } elseif ($markup instanceof KrubiKReplyKeyboard) {
                $params['reply_markup'] = json_encode($markup->toArray());
            } elseif (is_array($markup)) {
                 // اگر آرایه دستی است، انکود کن
                 $params['reply_markup'] = json_encode($markup);
            }
        }

        return $params;
    }

    /**
     * تبدیل کیبورد شیشه‌ای KrubiK به فرمت Bale
     * (تبدیل action_id به callback_data)
     */
    protected function transformInlineKeyboard(KrubiKInlineKeyboard $keyboard): array
    {
        $data = $keyboard->toArray();
        $rows = $data['rows'] ?? [];
        $baleRows = [];

        foreach ($rows as $row) {
            $buttons = isset($row['buttons']) ? $row['buttons'] : $row;
            $baleRow = [];
            foreach ($buttons as $btn) {
                $baleBtn = ['text' => $btn['text']];

                if ((isset($btn['type']) && $btn['type'] === 'Link') || isset($btn['url'])) {
                    $baleBtn['url'] = $btn['url'] ?? ($btn['link_data']['url'] ?? '');
                } elseif (isset($btn['action_id'])) {
                    $baleBtn['callback_data'] = $btn['action_id'];
                } elseif (isset($btn['callback_data'])) {
                    $baleBtn['callback_data'] = $btn['callback_data'];
                } else {
                    $baleBtn['callback_data'] = 'NO_ACTION';
                }
                $baleRow[] = $baleBtn;
            }
            $baleRows[] = $baleRow;
        }

        return ['inline_keyboard' => $baleRows];
    }

    /**
     * تشخیص می‌دهد آیا در پارامترها فایلی برای آپلود وجود دارد یا خیر.
     * برای سوییچ بین post و multipart.
     */
    protected function detectFileInParams(array $params): bool
    {
        $fileKeys = ['photo', 'audio', 'document', 'video', 'animation', 'voice', 'sticker', 'certificate'];
        foreach ($fileKeys as $key) {
            if (isset($params[$key])) {
                // اگر فایل ریسورس باشد یا مسیر فایل لوکال باشد
                if (is_resource($params[$key]) || (is_string($params[$key]) && file_exists($params[$key]))) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * یک متد کمکی برای تبدیل آرایه خام پاسخ به آبجکت SDK
     */
    protected function hyd(string $class, array $data)
    {
        // فرض بر این است که آبجکت‌های EFive کانستراکتوری دارند که دیتا می‌گیرد
        // یا متدی برای مپ کردن. اگر ندارند، باید دستی ست شود.
        // معمولا در SDKهای تلگرامی: new Message($data)
        return new $class($data);
    }

    protected function convertRichBlocksToSimpleText(array $blocks): string
    {
        $text = '';
        foreach ($blocks as $block) {
            // فرض می‌کنیم هر بلاک آرایه‌ای است که حداقل 'text' دارد
            if (isset($block['text'])) {
                $text .= $block['text'] . "\n";
            }
        }
        return trim($text);
    }

    // =========================================================================
    // 🚜 IMPLEMENTATION OF BaleDriverInterface (Explicit & Strict)
    // =========================================================================

    public function getMe(): User
    {
        $response = $this->makeRequest('getMe');
        return $this->hyd(User::class, $response);
    }

    public function deleteWebhook(): bool // تغییر بر اساس اینترفیس نهایی کاربر
    {
        // در اینترفیس نهایی کاربر Turn 2، خروجی array درخواست شد ولی اینجا bool منطقی‌تر است
        // اما چون extend کردیم و strict هستیم، طبق داک بله عمل می‌کنیم.
        $res = $this->makeRequest('deleteWebhook');
        return (bool) ($res);
    }
    
    // متد array خواسته شده در آخرین فایل pasted-text کاربر (نسخه ۳):
    // public function deleteWebhook(): array; 
    // پس اگر اینترفیس آن باشد:
    /* 
    public function deleteWebhook(): array {
        return $this->makeRequest('deleteWebhook');
    }
    */

    public function getWebhookInfo(): WebhookInfo
    {
        return $this->hyd(WebhookInfo::class, $this->makeRequest('getWebhookInfo'));
    }

    public function getWebhookUpdate(): Update
    {
        // خواندن از ورودی php://input
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        return $this->hyd(Update::class, $data ?? []);
    }

    public function sendMessage(array $params): Message
    {
        return $this->hyd(Message::class, $this->makeRequest('sendMessage', $params));
    }

    public function forwardMessage(array $params): Message
    {
        return $this->hyd(Message::class, $this->makeRequest('forwardMessage', $params));
    }

    public function copyMessage(array $params): Message
    {
        return $this->hyd(Message::class, $this->makeRequest('copyMessage', $params));
    }

    public function sendPhoto(array $params): Message
    {
        return $this->hyd(Message::class, $this->makeRequest('sendPhoto', $params));
    }

    public function sendAudio(array $params): Message
    {
        return $this->hyd(Message::class, $this->makeRequest('sendAudio', $params));
    }

    public function sendDocument(array $params): Message
    {
        return $this->hyd(Message::class, $this->makeRequest('sendDocument', $params));
    }

    public function sendVideo(array $params): Message
    {
        return $this->hyd(Message::class, $this->makeRequest('sendVideo', $params));
    }

    public function sendAnimation(array $params): Message
    {
        return $this->hyd(Message::class, $this->makeRequest('sendAnimation', $params));
    }

    public function sendVoice(array $params): Message
    {
        return $this->hyd(Message::class, $this->makeRequest('sendVoice', $params));
    }

    public function getFile(array $params): File
    {
        return $this->hyd(File::class, $this->makeRequest('getFile', $params));
    }

    public function getChatAdministrators(array $params): Chat
    {
        // طبق داک بله لیستی از ممبر برمیگرداند اما اینترفیس Chat خواسته
        // اینجا باید طبق ساختار واقعی SDK عمل شود. فرض بر ChatObject است.
        return $this->hyd(Chat::class, $this->makeRequest('getChatAdministrators', $params));
    }

    public function getChatMembersCount(array $params): Chat
    {
        return $this->hyd(Chat::class, $this->makeRequest('getChatMembersCount', $params));
    }

    public function getChatMember(array $params): Chat
    {
        return $this->hyd(Chat::class, $this->makeRequest('getChatMember', $params));
    }

    public function setChatDescription(array $params): bool
    {
        return (bool) $this->makeRequest('setChatDescription', $params);
    }

    public function createChatInviteLink(array $params)
    {
        return $this->makeRequest('createChatInviteLink', $params);
    }

    public function revokeChatInviteLink(array $params)
    {
        return $this->makeRequest('revokeChatInviteLink', $params);
    }

    public function exportChatInviteLink(array $params): string
    {
        $res = $this->makeRequest('exportChatInviteLink', $params);
        return is_string($res) ? $res : ($res['result'] ?? '');
    }

    public function sendSticker(array $params): Message
    {
        return $this->hyd(Message::class, $this->makeRequest('sendSticker', $params));
    }

    public function getStickerSet(array $params): StickerSet
    {
        return $this->hyd(StickerSet::class, $this->makeRequest('getStickerSet', $params));
    }

    public function uploadStickerFile(array $params): File
    {
        return $this->hyd(File::class, $this->makeRequest('uploadStickerFile', $params));
    }

    public function createNewStickerSet(array $params): bool
    {
        return (bool) $this->makeRequest('createNewStickerSet', $params);
    }

    public function addStickerToSet(array $params): bool
    {
        return (bool) $this->makeRequest('addStickerToSet', $params);
    }

    public function setStickerPositionInSet(array $params): bool
    {
        return (bool) $this->makeRequest('setStickerPositionInSet', $params);
    }

    public function deleteStickerFromSet(array $params): bool
    {
        return (bool) $this->makeRequest('deleteStickerFromSet', $params);
    }

    public function sendInvoice(array $params): Message
    {
        return $this->hyd(Message::class, $this->makeRequest('sendInvoice', $params));
    }

    public function answerShippingQuery(array $params): bool
    {
        return (bool) $this->makeRequest('answerShippingQuery', $params);
    }

    public function answerPreCheckoutQuery(array $params): bool
    {
        return (bool) $this->makeRequest('answerPreCheckoutQuery', $params);
    }

    public function getMyCommands(): array
    {
        $res = $this->makeRequest('getMyCommands');
        $commands = [];
        foreach ($res as $cmdData) {
            $commands[] = $this->hyd(BotCommand::class, $cmdData);
        }
        return $commands;
    }

    public function setMyCommands(array $params): bool
    {
        return (bool) $this->makeRequest('setMyCommands', $params);
    }

    public function deleteMyCommands(array $params = []): bool
    {
        return (bool) $this->makeRequest('deleteMyCommands', $params);
    }

    public function getUpdates(array $params = []): array
    {
        $res = $this->makeRequest('getUpdates', $params);
        $updates = [];
        foreach ($res as $u) {
            $updates[] = $this->hyd(Update::class, $u);
        }
        return $updates;
    }

    public function editMessageText(array $params): Message|bool
    {
        $res = $this->makeRequest('editMessageText', $params);
        if (is_bool($res)) return $res;
        return $this->hyd(Message::class, $res);
    }

    public function editMessageCaption(array $params): Message|bool
    {
        $res = $this->makeRequest('editMessageCaption', $params);
        if (is_bool($res)) return $res;
        return $this->hyd(Message::class, $res);
    }

    public function editMessageReplyMarkup(array $params): Message|bool
    {
        $res = $this->makeRequest('editMessageReplyMarkup', $params);
        if (is_bool($res)) return $res;
        return $this->hyd(Message::class, $res);
    }

    public function deleteMessage(array $params): bool
    {
        return (bool) $this->makeRequest('deleteMessage', $params);
    }

    public function answerCallbackQuery(array $params): bool
    {
        return (bool) $this->makeRequest('answerCallbackQuery', $params);
    }

    public function sendChatAction(array $params): bool
    {
        return (bool) $this->makeRequest('sendChatAction', $params);
    }

    public function sendLocation(array $params): Message
    {
        return $this->hyd(Message::class, $this->makeRequest('sendLocation', $params));
    }

        // =========================================================================
    // بخش ۱۱: ادامه متدهای موقعیت و اطلاعات (Contact & User Info)
    // =========================================================================

    public function sendContact(array $params): Message
    {
        return $this->hyd(Message::class, $this->makeRequest('sendContact', $params));
    }

    public function getUserProfilePhotos(array $params)
    {
        // طبق داک بله/تلگرام، خروجی UserProfilePhotos است
        // اگر کلاس آن را در EFive ندارید، می‌توانید آرایه برگردانید یا کلاس مربوطه را ایمپورت کنید
        return $this->makeRequest('getUserProfilePhotos', $params);
    }

    public function sendMediaGroup(array $params): array
    {
        // این متد آرایه‌ای از پیام‌ها برمی‌گرداند
        $response = $this->makeRequest('sendMediaGroup', $params);
        $messages = [];
        if (is_array($response)) {
            foreach ($response as $msgData) {
                $messages[] = $this->hyd(Message::class, $msgData);
            }
        }
        return $messages;
    }

    // =========================================================================
    // بخش ۱۳: متدهای پیشرفته مدیریت چت (Chat Administration)
    // =========================================================================

    public function banChatMember(array $params): bool
    {
        return (bool) $this->makeRequest('banChatMember', $params);
    }

    public function unbanChatMember(array $params): bool
    {
        return (bool) $this->makeRequest('unbanChatMember', $params);
    }

    public function restrictChatMember(array $params): bool
    {
        return (bool) $this->makeRequest('restrictChatMember', $params);
    }

    public function promoteChatMember(array $params): bool
    {
        return (bool) $this->makeRequest('promoteChatMember', $params);
    }

    public function setChatTitle(array $params): bool
    {
        return (bool) $this->makeRequest('setChatTitle', $params);
    }

    public function setChatPhoto(array $params): bool
    {
        // چون عکس آپلود می‌شود، متد makeRequest ما هوشمندانه آن را multipart می‌کند
        return (bool) $this->makeRequest('setChatPhoto', $params);
    }

    public function deleteChatPhoto(array $params): bool
    {
        return (bool) $this->makeRequest('deleteChatPhoto', $params);
    }

    public function pinChatMessage(array $params): bool
    {
        return (bool) $this->makeRequest('pinChatMessage', $params);
    }

    public function unpinChatMessage(array $params): bool
    {
        return (bool) $this->makeRequest('unpinChatMessage', $params);
    }

    public function unpinAllChatMessages(array $params): bool
    {
        return (bool) $this->makeRequest('unpinAllChatMessages', $params);
    }

    public function leaveChat(array $params): bool
    {
        return (bool) $this->makeRequest('leaveChat', $params);
    }

    public function getChat(array $params): Chat
    {
        return $this->hyd(Chat::class, $this->makeRequest('getChat', $params));
    }

    public function setChatStickerSet(array $params): bool
    {
        return (bool) $this->makeRequest('setChatStickerSet', $params);
    }

    public function deleteChatStickerSet(array $params): bool
    {
        return (bool) $this->makeRequest('deleteChatStickerSet', $params);
    }

    // =========================================================================
    // بخش ۱۴ و ۱۵: مدیریت لینک‌های دعوت و درخواست‌های عضویت
    // =========================================================================

    public function editChatInviteLink(array $params)
    {
        // خروجی ChatInviteLink است
        return $this->makeRequest('editChatInviteLink', $params);
    }

    public function approveChatJoinRequest(array $params): bool
    {
        return (bool) $this->makeRequest('approveChatJoinRequest', $params);
    }

    public function declineChatJoinRequest(array $params): bool
    {
        return (bool) $this->makeRequest('declineChatJoinRequest', $params);
    }

    // =========================================================================
    // بخش ۱۷: تنظیمات پیش‌فرض (Rights)
    // =========================================================================

    public function setMyDefaultAdministratorRights(array $params): bool
    {
        return (bool) $this->makeRequest('setMyDefaultAdministratorRights', $params);
    }

    public function getMyDefaultAdministratorRights(array $params)
    {
        // خروجی ChatAdministratorRights است
        return $this->makeRequest('getMyDefaultAdministratorRights', $params);
    }
}