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

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class OnCommand
{
    public string $command;

    public function __construct(string $command)
    {
        // قانون مهم: همیشه باید با / شروع شود.
        // اگر کاربر نوشت 'buy {item}' تبدیل می‌شود به '/buy {item}'
        // اگر کاربر نوشت '/start' همان '/start' می‌ماند.
        $this->command = str_starts_with($command, '/') ? $command : '/' . $command;
    }
}
