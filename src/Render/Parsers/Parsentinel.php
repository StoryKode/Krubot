<?php

namespace KrubiK\Render\Parsers;
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

use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;

use KrubiK\Render\Kernel\RichBladeFragmentParser;  // The In-Memory Oracle
use KrubiK\Render\Parsers\RichMDParser;            // The Markdown Titan
use KrubiK\Render\Parsers\RichHTMLParser;          // The DOM Archon

/**
 * [THE NEXUS OF CREATION - THE FORGE OF ARTISANS]
 * 
 * Meet The Grand Commander of Parsers!!!
 *
 * This is not a class. This is a CONCLAVE. A singularity point where the greatest
 * parsing minds in the multiverse are summoned on command. He holds the sacred keys
 * to awaken the Markdown Titan, the DOM Archon, and the In-Memory Oracle.
 * 
 * This is the evolution of the Factory, a stateless conductor orchestrating the
 * Laravel's Service Container. He holds no instances, no state, no memory.
 * He is a pure, hyper-efficient conduit to the Container's infinite power.
 *
 * He translates mortal keys into divine requests, commanding the Container
 * to forge a FRESH, PRISTINE artisan for every single call. This architecture
 * ANNIHILATES the threat of stateful leaks across requests, ensuring each
 * operation is perfectly isolated in his own quantum reality.
 * 
 * He knows every artisan by name and summons them upon request.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
 */
class Parsentinel
{
    /**
     * The Master Registry. A read-only map of keys to the artisan's class identity.
     * This configuration is immutable, forged at the dawn of the Manager's existence.
     * @var array<string, class-string<SyntaxWarden>>
     */
    private readonly array $registry;

    /**
     * The Manager is forged with a direct link to the heart of the application.
     * He wields the Container itself as his primary toolbox.
     *
     * @param Application $app The divine core of the Laravel framework.
     */
    public function __construct(private Application $app)
    {
        /**
         * The Master Registry. Maps the mortal keys to the immortal souls of the artisans.
         * @var array<string, class-string<SyntaxWarden>>
        */
        $this->registry = [
            'RichMD'    => RichMDParser::class,
            'RichHTML'  => RichHTMLParser::class,
            'RichBlade' => RichBladeFragmentParser::class,
        ];
    }

    /**
     * Normalize parser aliases.
    */
    private function normalizeType(?string $type): string
    {
        return match (strtolower(trim((string) $type))) {

            // The default path, the most travelled road. Also handles 'auto'.
            '',
            '`',
            'auto',
            'md',
            'markdown',
            'markdownv2',
            'markdownmode',
            'richmd' => 'RichMD',

            '<',
            '>',
            'html',
            'richhtml' => 'RichHTML',

            '@',   // The secret sigil for the Memory-Oracle.
            'blade',
            'richblade' => 'RichBlade',

            default => (string) $type,
        };
    }

    /**
     * Summons a specialized parser artisan for the given type.
     *
     * He translates the key, finds the corresponding class in his immutable registry,
     * and commands the Laravel Container to resolve and build a new instance.
     * The Container guarantees a clean, untainted parser, born just for this task.
     *
     * @param string|null $type The mortal key ('RichMD', 'RichHTML', 'RichBlade', '@', 'md', 'html', etc.).
     *                          'auto' or null defaults to the 'RichMD' artisan.
     * @return SyntaxWarden The summoned parser instance, pure and ready for duty. (the ritual of `::parse()`)
     * @throws InvalidArgumentException If the key is not found in the sacred registry.
    */
    public function summon(?string $type = 'md'): SyntaxWarden
    {
        $key = $this->normalizeType($type);

        if (!isset($this->registry[$key])) {
            throw new InvalidArgumentException("Heresy! No Warden/parser is known by the key '{$key}'. The Container denies your plea.");
        }

        // --- THE RITUAL OF CONTAINER-POWERED CREATION ---
        // Command the Laravel Container to forge a new, pristine instance.
        // This is the core of the solution. No static cache. No state leaks. Pure power.
        return $this->app->make($this->registry[$key]);
    }

    /**
     * Register any application services.
     *
     * This is where the magic is bound. We define how our artisans are born.
    */
    public static function registerOnSP(Application $thisApp): void
    {
        // --- The Grand Ritual of Binding: The Manager is Eternal and Stateless ---
        // 
        // We declare that there shall be only ONE Parsentinel Commander,
        // ensuring its authority is absolute and singular.
        // The Parsentinel itself has no state, so it's safe to register it as a singleton.
        //
        // A single, immortal Parse-conductor for the entire application lifecycle.
        $thisApp->singleton(self::class, fn ($app) => new self($app));

        // --- The Artisans are Ephemeral and Pure ---
        // THIS IS THE CRITICAL FIX. We use `bind` instead of `singleton`.
        // This command ensures that every time a parser is requested, a NEW instance
        // is created. This completely prevents state from leaking between parsing requests.
        // It is the ultimate purification ritual.
        $thisApp->bind(RichMDParser::class, fn () => new RichMDParser());
        $thisApp->bind(RichHTMLParser::class, fn () => new RichHTMLParser());
        $thisApp->bind(RichBladeFragmentParser::class, fn () => new RichBladeFragmentParser());
    }
}
