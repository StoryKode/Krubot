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
 * @method static void integrateNexus(string $nexusClass)
 * @method static array getIntegratedNexuses()
 * @method static \KrubiK\Krubot say(string $text)
 * @method static \KrubiK\Krubot reply(string $text)
 * @method static \KrubiK\Krubot to(string $targetChatId, string $text)
 * @method static \KrubiK\Krubot processUpdate(\KrubiK\DTOs\Message $message)
 * @method static \KrubiK\Krubot registerHandlers(object|string $target)
 * @method static mixed go(string $routeName, array $params = [], bool|array $middlewareStrategy = true)
 * @method static string|null resolvePattern(string $name, array $params = [])
 * @method static \KrubiK\Router\Route onCommand(string $command, array|callable $handler, array $attributes = [])
 * @method static \KrubiK\Router\Route onText(string $pattern, array|callable $handler, array $attributes = [])
 * // ... هر متد عمومی دیگری که در کلاس Krubot دارید را می‌توانید اینجا اضافه کنید
 *
 * @see \KrubiK\Krubot
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×vRC.8×
 * @license MIT
*/
class KruboT extends Facade
{
    /****
     * Get the registered name of the component.
     *
     * ✨ جادوی اصلی اینجاست! ✨
     * این متد به لاراول می‌گوید که این نما (Facade) به کدام سرویس
     * در Service Container متصل است. ما باید دقیقا همان کلیدی را برگردانیم
     * که در AppServiceProvider برای ثبت Singleton استفاده کردیم.
     *
     * @return string
     * /
     protected static function getFacadeAccessor(): string
    {
        // ما Krubot را با نام کلاس خودش در کانتینر ثبت کردیم.
        return \KrubiK\Krubot::class;
    } */

    /**
     * Get the registered name of the component in the container.
     *
     * This is the "secret handshake". This string MUST match the alias
     * or binding key we used in the service provider.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        // This is the "phone number" to the command center and "phone number" is enough.
        return 'krubot'; 
    }
    
    /**
     * ⭐️ The Omnipresent Decree (Static Commander) ⭐️
     *
     * Initiates a fluent command chain on a specific driver from anywhere in the application.
     *
     * @param string|array $aliases The target driver alias(es).
     * @return \KrubiK\Krubot
     */
    public static function yow(string|array $aliases): \KrubiK\Krubot
    {
        // Get the singleton instance and enter the `via` command center.
        return static::getFacadeRoot()->via($aliases);
    }
}
