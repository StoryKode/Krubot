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

// استفاده از Enum برای مدیریت وضعیت‌های داخلی کامپوننت
enum State: string {
    case RequestContact = 'contact';
    case VerifyOtp = 'otp';
    case Complete = 'complete';
}

/**
 * کامپوننت ثبت‌نام یکپارچه.
 * این کلاس تمام لاجیک‌های بصری مراحل ثبت نام را کپسوله می‌کند.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class RegistrationKeyboard
{
    public function __construct(
        protected State $currentState = State::RequestContact
    ) {}

    /**
     * فکتوری متد برای تنظیم وضعیت در لحظه
     */
    public static function mode(State $state): static
    {
        return new static($state);
    }

    /**
     * رندر کردن کیبورد بر اساس وضعیت فعلی
     * این متد جادویی است که در کنترلر صدا زده می‌شود.
     */
    public function render(): array
    {
        return match($this->currentState) {
            self::State::RequestContact => $this->contactKeyboard(),
            self::State::VerifyOtp => $this->otpKeyboard(),
            self::State::Complete => $this->mainMenu(),
        };
    }

    // --- Private Methods (Implementation Details) ---

    private function contactKeyboard(): array
    {
        return Keyboard::make()
            ->hybridRow([
                ReplyButton::make('📱 ارسال شماره تماس')->requestContact()
            ])
            ->hybridRow(['❌ انصراف'])
            ->resize()
            ->toArray();
    }

    private function otpKeyboard(): array
    {
        // در مرحله کد تایید، شاید بخواهیم یک کیبورد اینلاین برای "ارسال مجدد" داشته باشیم
        // یا یک متن راهنما (Placeholder) در اینپوت
        return Keyboard::make()
            ->placeholder('کد ۵ رقمی را وارد کنید...')
            ->hybridRow(['تغییر شماره', 'ارسال مجدد کد'])
            ->toArray();
    }

    private function mainMenu(): array
    {
        return Keyboard::make()
            ->hybridRow(['ناحیه کاربری 👤', 'پشتیبانی 🎧'])
            ->hybridRow(['محصولات جدید ✨', 'کیف پول 💰'])
            ->toArray();
    }
}
