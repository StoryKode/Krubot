<?php

namespace KrubiK\Nexus\NoxiousSamples\Conversations; // /Scripts
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
use KrubiK\Conversations\Conversation;

class SurveyConversation extends Conversation
{
    // Properties will be PERSISTED automatically!
    public ?string $name = null;
    public ?string $age = null;

    /**
     * Step 1: Start
     */
    public function start(Krubot $bot)
    {
        $bot->reply("👋 سلام! بیایید شروع کنیم. اسم شما چیست؟")
            ->send(); 
        
        // Point to the next step handler
        $this->next('askAge');
    }

    /**
     * Step 2: Handle Name & Ask Age
     */
    public function askAge(Krubot $bot)
    {
        $text = $bot->text();
        
        // Validation
        if (mb_strlen($text) < 2) {
            $bot->reply("⛔ نام باید حداقل ۲ حرف باشد. دوباره تلاش کنید:")
                ->send();
            return; // Stay in this step (don't call next)
        }

        // Save property
        $this->name = $text;

        $bot->reply("خوشبختم {$this->name}! 🌹\nحالا سن خود را وارد کنید:")
            ->send();

        $this->next('finish');
    }

    /**
     * Step 3: Finish
     */
    public function finish(Krubot $bot)
    {
        $age = $bot->text();

        if (!is_numeric($age)) {
            $bot->reply("⛔ لطفاً سن را به عدد وارد کنید:")
                ->send();
            return; 
        }

        $this->age = $age;

        // Logic finished
        $bot->reply("✅ ثبت نام تکمیل شد!\n\n👤 نام: {$this->name}\n🎂 سن: {$this->age}")
            ->send();

        // End conversation and clear cache
        $this->end();
    }
}
