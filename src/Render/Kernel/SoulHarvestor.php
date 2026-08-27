<?php

namespace KrubiK\Render\Kernel;
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

use KrubiK\Render\RichMan;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Traits\Macroable;

/**
 * The Sentient Receptacle. A living vessel for the final alchemical result.
 *
 * This entity does not exist in a vacuum. It is a Golem, lifeless until summoned.
 * It cannot be 'newed up' like a common object; its existence is conditional. It
 * must be FED the fully-realized essence of a story stream to manifest. Once
 * awakened, it holds the core story and all captured echoes (harvests) within it,
 * ready to unleash its final form upon the world.
 * 
 * Technically A high-level container for the results of a BladeCipher build process.
 * This object is the "treasure chest" returned by BladeCipher::end().
 * It's Renderable, but also allows access to multiple, named output channels.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
 */
class SoulHarvestor implements Renderable, \Stringable /// old-name was: StoryHarvest
{
    use Macroable;

    /**
     * The Core Essence. The primary, default story content. This is The spinal cord of the narrative, itself.
     */
    public readonly RichMan $main;

    /**
     * The Captured Echoes. Soul-shards harvested from specific points in the stream. A dictionary of named Harvest outputs.
     * @var array<string, RichMan>
     */
    private readonly array $harvested;

    /**
     * The Heart of the Beast. Sealed from the outside world.
     * A SoulHarvestor cannot be born. It must be summoned via ::feed().
     */
    private function __construct(RichMan $main, array $harvested = [])
    {
        $this->main = $main;
        $this->harvested = $harvested;
    }

    /**
     * The Summoning Ritual. This is how the Golem awakens.
     * Feed it the lifeblood of the story and the sacrificed souls of the harvest,
     * and in return, it will manifest as a tangible, renderable entity.
     *
     * @param RichMan $main The core narrative essence from the stream's end.
     * @param array<string, RichMan> $harvested The named echoes captured along the way.
     * @return self The living, breathing SoulHarvestor, ready for judgment.
     */
    public static function feed(RichMan $main, array $harvested = []): self
    {
        return new self($main, $harvested);
    }

    /**
     * [DX MAGIC] To get a named story, simply access it as a property.
     * Commune with a captured echo by its sacred name.
     * e.g., $story->sidebar
     */
    public function __get(string $name): ?RichMan
    {
        return $this->harvested[$name] ?? null;
    }

    /**
     * [DX MAGIC] Answers isset() correctly
     * Inquire if an echo with the given name was captured.
     * e.g., isset($story->header)
     */
    public function __isset(string $name): bool
    {
        return isset($this->harvested[$name]);
    }

    /**
     * Get a named story explicitly.
     */
    public function get(string $name): ?RichMan
    {
        return $this->harvested[$name] ?? null;
    }

    /**
     * [ULTRA-DX] For simple cases, rendering the harvest object
     * automatically renders the MAIN story content.
     */
    public function render(): string
    {
        return $this->main->render();
    }

    public function __toString(): string
    {
        // Ensure no exception bubbles through __toString in production
        try {
            return $this->render();
        } catch (\Throwable $e) {
            report($e);
            return '';
        }
    }
}
