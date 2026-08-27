<?php

namespace KrubiK\WebApps\Attributes;
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

use Attribute;

/**
 * Declares an isolated/nested HTML web page.
 * MUST be applied to Method level only.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class WebPage
{
    /**
     * Declares a method as a route that serves a web page.
     *
     * @param string $name The unique routing identifier.
     * @param string|null $path Optional custom route URI. If null, it's derived from the dot-notated name.
     * @param array<string> $methods Allowed HTTP methods for this page.
     * @param string|null $accessPolicy Optional access policy ('standard', 'strict').
     * @param bool $autoEnrich If true, automatically appends required method parameters to the URI. Defaults to true for convenience, as pages commonly use path parameters (e.g., /products/{id}).
    */
    public function __construct(
        public string $name,
        public ?string $path = null,
        public array $methods = ['GET', 'POST'],
        public ?string $accessPolicy = null, // Added to support inline authorization policies
        public bool $autoEnrich = true // Defaulting to true enhances DX for typical page routes.
    ) {}

    public function getAccessPolicy(): ?string
    {
        return $this->accessPolicy;
    }
}
