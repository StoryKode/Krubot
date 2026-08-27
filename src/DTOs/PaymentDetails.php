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

use KrubiK\Enums\Currency;

readonly class PaymentDetails
{
    public function __construct(
        public string $paymentId,    // شناسه پرداخت در سیستم شما
        public int $amount,          // مبلغ
        public Currency $currency = Currency::IRT, // پیش‌فرض تومان
        public string $title = 'پرداخت'
    ) {}

    public static function toman(string $id, int $amount): self
    {
        return new self($id, $amount, Currency::IRT);
    }
}
