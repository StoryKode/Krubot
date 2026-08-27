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

readonly class LayoutConfig
{
    /**
     * @param int[] $pattern الگوی چیدمان. مثال: [2, 1, 2]
     */
    public function __construct(
        public array $pattern = [],
        public bool $rtl = false
    ) {}

    /**
     * چیدمان اتوماتیک (مثلاً همه ردیف‌ها 2 تایی)
     */
    public static function grid(int $cols): self
    {
        // اینجا یک الگوی تکرارشونده تعریف می‌کنیم (منطق پردازش در Keyboard خواهد بود)
        return new self(pattern: [$cols], rtl: false);
    }
    
    /**
     * چیدمان خاص (مثلاً دکمه اول بزرگ، دوتا زیرش)
     */
    public static function featured(): self
    {
        return new self(pattern: [1, 2, 2], rtl: true);
    }
}
