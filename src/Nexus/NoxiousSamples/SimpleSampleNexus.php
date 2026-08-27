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

use KrubiK\Keyboard\Keyboard;
use KrubiK\Keyboard\PowerButton;

class SimpleSampleNexus
{
    #[OnCommand('start')]
    public function start(Krubot $bot)
    {
        $bot->reply("سلام! من آماده‌ام. 🚀\nاین پاسخ از طریق صف لاراول (HandleDriverUpdate Job) ارسال شد.")
            ->send();
    }

    #[OnCommand('info')]
    public function info(Krubot $bot)
    {
        $user = $bot->user();
        $bot->say("اطلاعات شما:\nنام: {$user['first_name']}")->send();
    }

    #[OnCommand('reverse {param1}')]
    public function zmod(Krubot $bot, string $param1)
    {
        $usertext = $bot->text() . '    <<=>>   ' . strrev($param1);
        $bot->reply($usertext)->send();
    }
    
    #[OnCommand('kb')]
    #[OnText('kb')]
    public function menu(Krubot $bot)
    {
        
        @ini_set('display_errors', 1);
        @ini_set('display_startup_errors', 1);
        @error_reporting(E_ALL);

        try {
            $kb = Keyboard::make()
                    ->rtl();
            $kb->buttons([
                PowerButton::simple('LGPS', '📍 Live GPS'),// ->requestLocation(),
                PowerButton::simple('LTPS', 'Hive TPS :)'),// ->requestLocation()
            ]);
            $kb->button('❌ Drop {item[name]}')->action('remove', ['id' => '-11']);
            if(0) dd([
                "#[OnCommand('keyboard')]",
                $kb
            ]);
            // dd($bot->keyboard($kb)); /// ->reply("کیبورد باز شد")->send();

            $bot->keyboard($kb)->reply("کیبورد باز شد")->send();
        }
        catch(\Throwable $e) {
            dd($e);
        }
    }
}
