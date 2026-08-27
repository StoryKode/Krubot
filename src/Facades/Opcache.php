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

/**
 * @method static \App\Services\OpcacheService on(string $sapi)
 * @method static array|bool|null status(string $scriptPath = null)
 * @method static array|null config()
 * @method static bool reset()
 * @method static bool flush() Alais for reset
 * @method static bool invalidate(string $scriptPath)
 * @method static bool compile(string $scriptPath)
 * @method static array fresh(string $scriptPath)
 * @method static \Generator warmDirectory(string $directory, array $options = [])
 * @method static array warmSync(string $directory, array $options = [])
 *
 * @see \KrubiK\Helpers\OpcacheRuler
 */
class Opcache extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        // This must match the alias/binding in the service provider.
        return 'opcache.ruler';
    }
}
