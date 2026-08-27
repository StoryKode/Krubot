<?php

namespace KrubiK\Attributes;
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

use KrubiK\Krubot;
use Attribute;
use Illuminate\Contracts\Container\ContextualAttribute;
use Illuminate\Contracts\Container\Container;

#[Attribute(Attribute::TARGET_PARAMETER)]
class WarLord implements ContextualAttribute
{
 
    // This marker attribute has no any properties.
    // Because Its mere presence, triggers WarLord injection logic.

    /**
     * Resolve the Krubot singleton instance.
     *
     * @param  self|WarLord  $attribute
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return \KrubiK\Krubot
     */
    public static function resolve(self $attribute, Container $container): Krubot
    {
        // برگرداندن همان instance سینگلتون (مطابق تابع warlord() شما)
        return $container->make(Krubot::class);
        // یا اگر هلپر دارید: return warlord();
    }

    /**
     * اعتبارسنجی دستی (اختیاری) – می‌توانید بیرون از کانتینر هم صدا بزنید.
     *
     * Optional :: Validate that this attribute is only used for Krubot injection
    */
    public function validateType(?string $parameterType): void
    {
        // No type-hinting (Laravel sends null) //eg: public function show(#[WarLord] $bot)
        if ($parameterType === null) {
            return;
        }

        // Type Just Must Be The `Krubot` //eg: public function show(#[WarLord] Krubot $bot)
        if ($parameterType !== Krubot::class) {
            throw new \RuntimeException(
                '#[WarLord] can only be used with KrubiK\\Krubot type-hints, got: ' . $parameterType
            );
        }
    }
}