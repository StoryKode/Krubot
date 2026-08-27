<?php

namespace KrubiK\Facades;
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

use Illuminate\Support\Facades\Facade;
use KrubiK\Render\Parsers\Parsentinel as ParsentinelCore;

/**
 * The glorious static gateway to the Parsentinel's power.
 * All hail the Commander!
 *
 * @method static \KrubiK\Render\Parsers\SyntaxWarden summon(string $type = 'md')
 *
 * @see \KrubiK\Render\Parsers\Parsentinel
*/
class Parsentinel extends Facade
{
    /**
     * Get the registered name of the component.
     * This is the sacred key that unlocks the Parsentinel's instance
     * from the heart of the service container.
     *
     * @return string
    */
    protected static function getFacadeAccessor(): string
    {
        // We bind the core class itself into the container.
        return ParsentinelCore::class;
    }
}
