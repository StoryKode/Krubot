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
 * Layer 1: Universal Driver Interface
 * 
 * Shared methods available in Rubika, Bale, and Telegram.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
 */
interface UniversalDriverInterface
{
    /**
     * Receive incoming updates (Long Polling).
     */
    public function getUpdates(array $params = []): mixed;

    /**
     * Send text message.
     */
    public function sendMessage(array $params): mixed;

    /**
     * Forward message.
     */
    public function forwardMessage(array $params): mixed;

    /**
     * Edit text message.
     */
    public function editMessageText(array $params): mixed;

    /**
     * Delete message.
     */
    public function deleteMessage(array $params): mixed;

    /**
     * Set bot commands.
     * (Rubika: setCommands, Telegram/Bale: setMyCommands - Adapter must handle the mapping)
     */
    public function setMyCommands(array $params): mixed;
}
