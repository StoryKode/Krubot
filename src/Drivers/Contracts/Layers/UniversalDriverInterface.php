<?php

namespace KrubiK\Drivers\Contracts\Layers;

/**
 * Layer 1: Universal Driver Interface
 * 
 * Shared methods available in Rubika, Bale, and Telegram.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
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
     * (Rubika: setCommands, T/B: setMyCommands - Adapter handles the mapping)
     */
    public function setMyCommands(array $params): mixed;
}
