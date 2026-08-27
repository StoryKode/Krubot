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
 * Declares a polymorphic WebApplication endpoint controller.
 * Automatically resolves URL path and response types.
 *
 * MUST be applied to Class level only.
 * The system automatically routes to 'index' or 'handle' method.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class WebApp
{
    /**
     * @var string|null The system-wide default access policy, cached to prevent repeated config lookups.
     * @internal This is our Hyper-DX Static Prop, managed by the Nexus Integrator.
    */
    public static ?string $systemDefaultAccessPolicy = null;

    /**
     * @param string $name The unique routing identifier for this application view/endpoint.
     * @param string|null $path Optional Custom route URL. If null, derived from dot notation (e.g. 'game.panel' -> '/game/panel').
     * @param array<string> $methods Allowed HTTP protocols.
     * @param bool $autoEnrich If true, automatically appends required method parameters to the URI. Defaults to false to prevent unintended URL modification for API-style class-based endpoints.
    */
    public function __construct(
        public string $name,
        public ?string $path = null,
        public array $methods = ['GET', 'POST'],
        public ?string $accessPolicy = null, // Added to support inline authorization policies
        public bool $autoEnrich = false // Defaulting to false provides a predictable, explicit-only behavior.
    ) {}

    public function getAccessPolicy(): ?string
    {
        return $this->accessPolicy;
    }
}

