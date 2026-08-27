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

use KrubiK\Krubot;
use Closure;

/**
 * این کلاس یک ظرف پویا برای مکالماتی است که مستقیماً
 * از طریق $bot->ask() ایجاد می‌شوند.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class ShadowConversation extends Conversation
{
    protected $initialQuestion;
    protected $initialNext;
    protected $initialValidator = null;

    /**
     * تنظیم داده‌های اولیه که از متد ask ربات می‌آیند
     */
    public function setInitialStep($question, $next, $validator = null)
    {
        $this->initialQuestion = $question;
        $this->initialNext = $next;
        $this->initialValidator = $validator;
    }

    /**
     * اجرای استاندارد مکالمه
     * اینجا ما دستی متد ask والد را صدا می‌زنیم تا چرخه شروع شود.
     */
    public function start(Krubot $bot)
    {
        // پاس دادن سوال و هندلر به متد قدرتمند ask در کلاس والد
        $this->ask($this->initialQuestion, $this->initialNext, $this->initialValidator);
    }
}
