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

/**
 * #[Receive] — Declarative Sensory Route Attribute
 *
 * Binds a handler method to one or more content-type routes automatically.
 * Works in tandem with the Krubot's onType() engine and the Signal enum,
 * eliminating the need for manual $warlord->onType() calls in bot setup.
 *
 * Usage examples:
 *
 *   #[Receive('photo')]
 *   #[Receive(Signal::Visual)]
 *   #[Receive(['photo', 'video', 'document'])]
 *   #[Receive([Signal::Photo, Signal::Motion, Signal::Snapshot])]
 *   #[Receive([Signal::Sticker, 'animation'])]
 *
 *   System & Governance Events Example: Manage chat administration tasks.
 *   This method will trigger when a new user requests to join a chat.
 *      
 *   #[Receive(Signal::Request)] // Corresponds to 'chat_join_request'
 *   public function joinChecker(Update $update): bool
 *   {
 *       // Logic to automatically approve or decline a new member request.
 *   }
 *
 *   #[Receive(Signal::Callback)]   // Handles button presses from inline keyboards
 *   #[Receive(Signal::Query)]      // Handles incoming inline queries
 *   public function onUserInteraction(Message $message) // It IS_REPEATABLE, runs on both events
 *
 * @author DoKtor K.
 * @link https://StoryKo.de
 * @version Krubot: ×RC.8×
 * @license MIT
*/
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Receive // @TODO: implements ContextualAttribute #[TARGET_PARAMETER] // Let There Be CARNAGE !!!
{
    /**
     * The frequency type(s) this handler should respond to.
     * Accepts a single Signal constant/string or an array of them.
     *
     * e.g. 'photo', Signal::Visual, ['photo', 'video', Signal::Geo], [Signal::Audio, Signal::File, Signal::Speech]
     *
     * @var string|array<string>
     */
    public readonly string|array $frequency;

    /**
     * @param string|array<string> $frequency
     *   A single signal string or an array of signal strings.
     *   Values are passed directly to onType(), which prepends 'TYPE::' internally.
     *   You may use Signal constants (e.g. Signal::Visual) or raw strings ('photo').
     */
    public function __construct(string|array $frequency)
    {
        // Normalize: if a single string is given, keep as-is; arrays pass through untouched.
        // The router's onType() handles the 'TYPE::' prefix and strtolower() normalization.
        $this->frequency = $frequency;
    }
}
