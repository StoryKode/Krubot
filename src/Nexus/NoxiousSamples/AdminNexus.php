<?php

namespace KrubiK\Nexus\NoxiousSamples;
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
use KrubiK\Attributes\OnCommand;
use KrubiK\Attributes\OnText;
use KrubiK\Attributes\Middleware; // ایمپورت کلاس جدید
use KrubiK\Middlewares\AdminMiddleware;
use KrubiK\Middlewares\LogIncomingRequest;

// ✅ فقط ادمین می‌تواند این دستورات را بزند
#[Middleware(AdminMiddleware::class)] // Nexus-Level Middlewares
class AdminNexus
{
    #[OnCommand('ban')]
    //#[Middleware(AdminMiddleware::class)]
    public function banUser(Krubot $bot)
    {
        $bot->reply('User Banned!')->send();
    }

    // ✅ ترکیب چند میدل‌ور (هم ادمین باشد، هم لاگ شود)
    #[OnCommand('shutdown')]
    #[Middleware([LogIncomingRequest::class])]
    public function shutdown(Krubot $bot)
    {
        $bot->reply('Shutting down...')->send();
    }
    
    // ✅ استفاده روی متن (Regex)
    #[OnRegEx('^config set (.+)')] // ==> #[OnRegEx('/^config set (.+)/')]
    #[Middleware('auth')] /*
        merges, so ==> #[Middleware(AdminMiddleware::class, 'auth')]
        so also ==> #[Middleware(ConversationMiddleware::class, AdminMiddleware::class, 'auth')] // according to config-def-middlewares
    */ public function setConfig(Krubot $bot, string $value)
    {
        // ...
    }
}
