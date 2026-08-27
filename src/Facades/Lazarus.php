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
use KrubiK\Console\LazarusProtocol;
use KrubiK\DTOs\LazarusTask;

/**
 * @method static LazarusTask todo(\DateTimeInterface|\DateInterval|int|string $when, callable $what, array|\Closure|null $how = null)
 *
 * @see \KrubiK\Console\LazarusProtocol
 */
class Lazarus extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * By returning the fully qualified class name, we adhere to modern
     * Laravel standards, enabling auto-discovery, better IDE support,
     * and a clearer architectural signal.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        // This is the modern, "HyperDX" bridge.
        return LazarusProtocol::class;
    }
}
