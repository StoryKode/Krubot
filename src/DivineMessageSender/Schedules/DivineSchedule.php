<?php

namespace KrubiK\DivineMessageSender\Schedules;
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

use Illuminate\Console\Scheduling\Schedule;

/**
 * DivineSchedule
 * ارتباط‌دهنده بین Cron Laravel و فرمان معنوی krubik:dispatch-divine-nudge
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class DivineSchedule
{
    /**
     * ثبت رویدادها در شیء Schedule
     * 
     * این متد از طرف Kernel مرکزی فراخوانده می‌شود (app/Console/Kernel.php)
     *
     * @param Schedule $schedule
     * @return void
     */
    public static function register(Schedule $schedule): void
    {
        // فراخوان معنوی هر ده دقیقه با مهارِ تداخل همزمان
        $schedule->command('krubik:dispatch-divine-nudge')
            ->everyTenMinutes()
            ->withoutOverlapping() // dont re-run if task isn't stopped yet!
            ->runInBackground() // dont block other scripts, this allows us to use more from CPU & RAM
            ->appendOutputTo(storage_path('logs/divine-nudge.log'));
    }
}
