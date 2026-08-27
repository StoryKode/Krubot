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
 * Rubika VIP Interface
 * 
 * The "Rebel" methods unique to Rubika architecture.
 */
interface RubikaVipInterface
{
    /**
     * Unified method for sending any media (Photo, Video, File, Voice).
     */
    public function sendFile(array $params): mixed;

    /**
     * Request permission to upload a file (Pre-upload step).
     */
    public function requestSendFile(array $params): mixed;

    /**
     * Edit the inline keyboard (Keypad).
     * Replaces editMessageReplyMarkup.
     */
    public function editMessageKeypad(array $params): mixed;

    /**
     * Manage bot webhooks and endpoints internally.
     */
    public function updateBotEndpoints(array $params): mixed;
}
