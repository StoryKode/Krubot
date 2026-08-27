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

/**
 * ReplyButton 1.7
 * کلاس پیشرفته برای ساخت دکمه‌های منوی زیرین (Reply Keyboard).
 * این کلاس تمام قابلیت‌های دکمه‌های ساده، درخواست تماس و موقعیت مکانی را تجمیع می‌کند.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class ReplyButton extends PowerButton
{
    /**
     * ثابت‌های نوع دکمه برای جلوگیری از خطای تایپی و استانداردسازی.
    */
    public const TYPE_TEXT = 'Text';
    public const TYPE_REQUEST_CONTACT = 'RequestContact';
    public const TYPE_REQUEST_LOCATION = 'RequestLocation';

    /**
     * سازنده کلاس.
     * ما پارامترهای والد را فراخوانی می‌کنیم اما مقادیر پیش‌فرض را برای دکمه منو تنظیم می‌کنیم.
     * 
     * @param string $text متن دکمه
    */
    public function __construct(string $text = '')
    {
        // نوع پیش‌فرض دکمه‌های منو معمولاً 'Text' است.
        // آرگومان دوم (action_id) برای دکمه‌های منو null است چون اکشن سروری ندارند.
        parent::__construct($text, null, self::TYPE_TEXT);
    }

    /**
     * متد فکتوری استاتیک برای شروع زنجیره (Fluent Chain).
     * 
     * @param string $text متن دکمه
     * @return static
    */
    public static function make(string $text): static
    {
        return new static($text);
    }

    /**
     * تنظیم دکمه برای درخواست شماره تماس کاربر.
     * 
     * @return static
    */
    public function requestContact(): static
    {
        // در API رسمی روبیکا و تلگرام، دکمه درخواست کانتکت رفتار خاصی دارد.
        // ما تایپ را روی حالت استاندارد روبیکا ست می‌کنیم.
        $this->type = self::TYPE_REQUEST_CONTACT; 
        return $this;
    }

    /**
     * تنظیم دکمه برای درخواست موقعیت مکانی کاربر.
     * 
     * @return static
    */
    public function requestLocation(): static
    {
        $this->type = self::TYPE_REQUEST_LOCATION;
        return $this;
    }

    /**
     * خروجی نهایی آرایه جهت ارسال به API.
     * این متد `toArray` والد را بازنویسی می‌کند تا ساختار ساده و صحیح `ReplyButton` را تولید کند.
     * 
     * @return array
    */
    public function toArray(): array
    {
        // ساختار دکمه‌های ریپلای (منو) در روبیکا شامل متن و تایپ است.
        // برخلاف دکمه‌های اینلاین، نیازی به action_id یا data ندارند.
        $base = [
            'text' => $this->text,
            'type' => $this->type,
        ];

       // ادغام با پی‌لودهای اضافی (اگر ست شده باشند)
        return array_merge($base, $this->extraPayload);
    }
}
