<?php

namespace KrubiK\Drivers\Contracts\Layers;
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

/**
 * Layer 1: DeFluent Driver Interface
 * 
 * Shared methods in UniversalDriverInterface & FluentDriverInterface
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
*/
interface DeFluentDriverInterface
{    
    // UniversalDriverInterface & FluentDriverInterface conflictz
    /**
     * Test token & get bot info.
     */
    public function getMe(): mixed;
    /**
     * Get chat info (User/Group/Channel).
     */
    public function getChat(array $params): mixed;
    /**
     * Get file info & path.
     */
    public function getFile(array $params): mixed;
    /**
     * Send Poll.
     */
    public function sendPoll(array $params): mixed;
    /**
     * Send Location.
     */
    public function sendLocation(array $params): mixed;
    /**
     * Send Contact.
     */
    public function sendContact(array $params): mixed;
    //  / FxU ConflicT   */
}
