<?php

namespace KrubiK\Nexus\NoxiousSamples\Conversations;
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
use KrubiK\Conversations\InlineMenu;

class ColorMenu extends InlineMenu
{
    public function start(Krubot $bot)
    {
        $this->menuText('🎨 لطفاً یک رنگ را انتخاب کنید:')
             ->clearButtons()
             ->addButtonRow(
                 ['text' => '🔴 قرمز', 'data' => 'color_red', 'method' => 'handleRed'],
                 ['text' => '🔵 آبی', 'data' => 'color_blue', 'method' => 'handleBlue']
             )
             ->addButtonRow(
                 ['text' => '❌ بستن', 'data' => 'close_menu', 'method' => 'handleClose']
             )
             ->showMenu();
    }

    public function handleRed(Krubot $bot)
    {
        // Show feedback (Update the existing menu text)
        $this->menuText('شما رنگ ❤️ قرمز را انتخاب کردید! دوباره انتخاب کنید:')
             ->showMenu(); 
    }

    public function handleBlue(Krubot $bot)
    {
        $this->menuText('شما رنگ 💙 آبی را انتخاب کردید! دوباره انتخاب کنید:')
             ->showMenu();
    }

    public function handleClose(Krubot $bot)
    {
        $bot->reply('منو بسته شد. خداحافظ! 👋')->send();
        $this->closeMenu(); // Delete the menu message and clear state
    }
}
