<?php

namespace KrubiK\DTOs;
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

readonly class CalendarConfig
{
    public function __construct(
        public string $title = 'انتخاب تاریخ',
        public string $type = 'Persian', // یا 'Gregorian'
        public ?string $minYear = null,
        public ?string $maxYear = null,
        public ?string $defaultDate = null
    ) {}

    /**
     * متد استاتیک برای حالت‌های پرکاربرد (DX بالا)
    */
    public static function jalali(string $title = 'تاریخ شمسی'): self
    {
        return new self($title, 'Persian', '1360', '1410');
    }

    /**
     * متد استاتیک برای حالت‌های پرکاربرد (DX بالا)
    */
    public static function western(string $title = 'تاریخ میلادی'): self
    {
        return new self($title, 'Gregorian', '1990', '2030');
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'type' => $this->type,
            'min_year' => $this->minYear,
            'max_year' => $this->maxYear,
            'default_value' => $this->defaultDate,
        ], fn($v) => !is_null($v));
    }
}
