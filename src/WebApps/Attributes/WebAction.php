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
 * @Annotation
 * @Target("METHOD")
 *
 * Marks a method as an action that can be invoked from a WebApp via an external gateway.
 * This attribute provides a public-facing name for the action, distinct from internal command patterns.
 *
 * Supports two syntaxes:
 * 1. Simple name: #[WebAction('my.action.name')]
 * 2. Named arguments: #[WebAction(name: 'my.action.name', description: '...', methods: [...])]
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class WebAction
{
    /**
     * The public-facing name for this web action. This is the identifier
     * that will be used when calling this action from a WebApp.
     *
     * @var string
     */
    public string $name;

    /**
     * Optional: Description for documentation or gateway purposes.
     *
     * @var string|null
     */
    public ?string $description = null;

    /**
     * Optional: Defines the HTTP method(s) allowed for this action if used with a RESTful gateway.
     *
     * @var array<string>|null
     */
    public ?array $methods = null;

    // Added to support inline authorization policies
    public ?string $accessPolicy = null;

    /**
     * Controls whether the routing engine automatically modifies the URI pattern
     * based on required parameters in the method signature.
     * @var bool
     */
    public bool $autoEnrich;

    /**
     * Constructor handles both simple string and named argument syntaxes.
     *
     * @param string|array $nameOrArguments If a string, it's treated as the action name.
     *                                      If an array, it should contain named arguments like ['name' => ..., 'description' => ..., 'methods' => ...].
     * @param string|null $description Optional description for documentation or API discovery, Only used if $nameOrArguments is a string.
     * @param array<string>|null $methods List of Allowed HTTP methods, Only used if $nameOrArguments is a string.
     * @param string|null $accessPolicy Optional access policy ('standard', 'strict').
     * @param bool $autoEnrich If true, appends required method parameters to the URI. Defaults to false for API actions to ensure body/query parameters are not mistakenly added to the path.
     *
     */
    public function __construct(
        string|array $nameOrArguments,
        ?string $description = null,
        ?array $methods = null,
        ?string $accessPolicy = null, // Added as an optional constructor argument
        ?bool $autoEnrich = false // Defaults to false, promoting explicit API design.
    ) {
        $this->autoEnrich = $autoEnrich;
        if (is_array($nameOrArguments)) {
            // Case 2: Named arguments like #[WebAction(name: '...', description: '...', methods: [...])]
            $this->name = $nameOrArguments['name'] ?? throw new \InvalidArgumentException("WebAction attribute requires a 'name' argument.");
            $this->description = $nameOrArguments['description'] ?? null;
            $this->methods = $nameOrArguments['methods'] ?? ['GET']; // Default to GET
            $this->accessPolicy = $nameOrArguments['accessPolicy'] ?? $nameOrArguments['access_policy'] ?? null;
            $this->autoEnrich = $nameOrArguments['autoEnrich'] ?? $nameOrArguments['auto_enrich'] ?? $autoEnrich;
        } elseif (is_string($nameOrArguments) || $nameOrArguments instanceof \Stringable) {
            // Case 1: Simple string like #[WebAction('myMethodName')]
            $this->name = (string) $nameOrArguments;
            $this->description = $description; // Use the second argument as description
            $this->methods = $methods;         // Use the third argument as methods
            $this->accessPolicy = $accessPolicy;
        } else {
            throw new \InvalidArgumentException('The first argument for WebAction must be a string or an array.');
        }

        // Ensure methods are set to default if null after parsing
        // Default to GET if no methods are specified and it makes sense for JSONP/simple gateways
        if ($methods === null) {
            // For JSONP, GET is standard. For a REST gateway, you might want to enforce it or allow more.
            $this->methods = ['GET'];
        }
    }

    /**
     * Helper to get the action name, useful for discovery.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Helper to get the allowed HTTP methods.
     *
     * @return array<string>
     */
    public function getMethods(): array
    {
        return $this->methods ?? ['GET']; // Default to GET if not set
    }

    /**
     * Helper to get the description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getAccessPolicy(): ?string
    {
        return $this->accessPolicy;
    }
}
