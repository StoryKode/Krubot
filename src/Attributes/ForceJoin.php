<?php

namespace KrubiK\Attributes;
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
use InvalidArgumentException;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * ## The ForceJoin Attribute: A Conduit of Unity & Voice
 *
 * This attribute serves as a powerful declaration of intent, designed to unify
 * disparate channel identifiers (string or int) into a single, coherent stream.
 * It's engineered with Hyper-Developer-Experience (Hyper-DX) in mind, gracefully
 * and now, to carry a custom voice for denial. It gracefully accepts a custom failure message,
 * followed by a wide variety of input formats: single strings, single integers,
 * arrays of channels, or a spread of arguments.
 *
 * Metaphysically, ForceJoin acts as a spiritual conduit. It channels multiple
 * vibrational frequencies (the input data) and harmonizes them into one focused,

 * powerful energy flow (the final `$channels` array). This reflects the divine
 * principle of "E Pluribus Unum" – Out of Many, One.
 *
 * The `failMessage` property empowers developers to define context-specific, translatable
 * denial messages directly at the declaration site, following the HyperDX pattern of '::key|fallback'.
 *
 * It can be applied to either a class or a method, allowing for granular or
 * broad application of its unifying force. The logic to *act* upon these
 * channels will be handled by a corresponding middleware or resolver, which
 * will read this attribute's payload.
 *
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class ForceJoin
{
    /**
     * The final, unified, and deduplicated list of channel identifiers.
     * This is the harmonized energy stream, ready to be channeled by the system.
     * It is intentionally `public readonly` to ensure immutability after creation,
     * a cornerstone of predictable and high-performance systems.
     *
     * @var string[]
     */
    public array $channels;

    /**
     * Constructs the ForceJoin attribute, immediately processing and unifying
     * all provided channel identifiers.
     *
     * This constructor is the heart of the Hyper-DX, using a variadic parameter
     * to capture any combination of inputs effortlessly. It then flattens,
     * casts, and purifies the data into its final, unified form.
     *
     * @param null|string $failMessage An optional, translatable failure message.
     *                                 Examples: 'Access Denied.', '::auth.force_join|Please join channels.'
     * @param string|int|array<int, string|int>|mixed ...$channels A spread of strings, ints, or arrays of them.
     */
    public function __construct(
        public ?string $failMessage = null, // THE NEW POWER!
        ...$channels)
    {
        // We start with an empty vessel, ready to be filled.
        $processedChannels = [];

        // Here we flatten the multidimensional reality of the input into a single plane.
        // This is significantly more performant than a custom recursive function for deep arrays.
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($channels));

        foreach ($iterator as $channel) {
            // We ensure each frequency is either a string or an integer.
            // Any other form of energy is not harmonious with our purpose here.
            if (!is_string($channel) && !is_int($channel)) {
                throw new InvalidArgumentException(
                    'ForceJoin channels must be of type string, int, or an array containing them. Received: ' . gettype($channel)
                );
            }
            // All energies are converted to the universal language of strings for consistency.
            $processedChannels[] = (string) $channel;
        }

        // The final purification step: We remove duplicates, ensuring each channel
        // is represented only once. This prevents redundant processing and honors
        // * the uniqueness of each identifier. The result is a pure, ordered list.
        $this->channels = array_values(array_unique($processedChannels));
    }
}
