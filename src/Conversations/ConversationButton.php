<?php

namespace KrubiK\Conversations;
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

use KrubiK\Keyboard\PowerButton;

/**
 * این کلاس به عنوان یک Wrapper هوشمند عمل می‌کند.
 * تمام قدرت کلاس اصلی Keyboard را دارد اما سینتکس ساده UniChatKit را هم پشتیبانی می‌کند.
 */
class ConversationButton extends PowerButton
{
    /**
     * سازگاری با سینتکس Conversation: ساخت سریع دکمه
     * در مکالمات معمولاً دکمه‌ها از نوع متن ساده (Reply Keyboard) هستند.
     * 
     * @param string $text متن دکمه
     * @return static
     */
    public static function create(string $text): static
    {
        // نوع پیش‌فرض را روی 'Text' یا 'Simple' می‌گذاریم تا رفتار قبلی حفظ شود
        // در کلاس PowerButton نوع پیش‌فرض 'Button' (اینلاین) است، پس اینجا Override می‌کنیم.
        return new static($text, null, 'Text');
    }

    /**
     * سازگاری با سینتکس Conversation: تنظیم مقدار
     * 
     * @param string $value مقداری که دکمه برمی‌گرداند
     * @return static
     */
    public function value(string $value): static
    {
        // در منطق پیشرفته، "value" همان داده‌ای است که ارسال می‌شود (action_data یا id)
        // اگر دکمه متنی باشد، معمولاً ولیو همان تکست است، اما اینجا آن را به عنوان ID/Data ست می‌کنیم
        return $this->action($value);
    }

    /**
     * متد کمکی برای تبدیل سریع به فرمت ساده‌ی مورد نیاز Keypad
     * (اگر انجین مکالمه انتظار ساختار ساده‌تری دارد)
     */
    public function toArray(): array
    {
        // استفاده از منطق قدرتمند والد
        $data = parent::toArray();

        // اطمینان از اینکه اگر نوع Text است، ساختار اضافی نداشته باشد که API را گیج کند
        // در کیبورد پایین (Keypad)، معمولاً فقط text و type مهم هستند.
        if (($data['type'] ?? '') === 'Text' || ($data['type'] ?? '') === 'Simple') {
            return [
                'text' => $data['text'],
                'type' => 'Text', // اجبار به فرمت استاندارد متن
            ];
        }

        return $data;
    }
}
