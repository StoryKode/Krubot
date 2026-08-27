<?php

namespace KrubiK\Arcane;
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

// مدیریت پیام‌ها (CanPin)
// شامل پین کردن و متدهای تکمیلی پیام به همراه شورتکات‌ها

trait CanPin
{
    // Trait 'InteractsWithApi' must be used in the host class (Krubot)

    /**
     * Pin a message in a supergroup or a channel.
     *
     * @param int $messageId Identifier of a message to pin.
     * @param bool $disableNotification Pass True if it is not necessary to send a notification to all chat members.
     * @param string|null $chatId Unique identifier for the target chat (optional).
     * @return array
     */
    public function pinChatMessage(int $messageId, bool $disableNotification = false, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        return $this->makeRequest('pinChatMessage', [
            'chat_id'              => $realChatId,
            'message_id'           => $messageId,
            'disable_notification' => $disableNotification
        ]);
    }

    /**
     * Unpin a message.
     *
     * @param int|null $messageId Identifier of a message to unpin. If not specified, the most recent pinned message (by sending date) will be unpinned.
     * @param string|null $chatId Unique identifier for the target chat (optional).
     * @return array
     */
    public function unpinChatMessage(?int $messageId = null, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        $params = ['chat_id' => $realChatId];
        
        if ($messageId) {
            $params['message_id'] = $messageId;
        }
        
        return $this->makeRequest('unpinChatMessage', $params);
    }

    /**
     * Clear all pinned messages.
     *
     * @param string|null $chatId Unique identifier for the target chat (optional).
     * @return array
     */
    public function unpinAllChatMessages(?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        return $this->makeRequest('unpinAllChatMessages', [
            'chat_id' => $realChatId
        ]);
    }

    // -------------------------------------------------------------------
    // SHORTCUTS / ALIASES
    // -------------------------------------------------------------------

    /**
     * Shortcut for pinChatMessage.
     *
     * @param int $messageId
     * @param bool $disableNotification
     * @param string|null $chatId
     * @return array
     */
    public function pinMessage(int $messageId, bool $disableNotification = false, ?string $chatId = null): array
    {
        return $this->pinChatMessage($messageId, $disableNotification, $chatId);
    }

    /**
     * Shortcut for pinChatMessage.
     *
     * @param int $messageId
     * @param bool $disableNotification
     * @param string|null $chatId
     * @return array
     */
    public function pin(int $messageId, bool $disableNotification = false, ?string $chatId = null): array
    {
        return $this->pinChatMessage($messageId, $disableNotification, $chatId);
    }

    /**
     * Shortcut for unpinChatMessage.
     *
     * @param int|null $messageId
     * @param string|null $chatId
     * @return array
     */
    public function unpinMessage(?int $messageId = null, ?string $chatId = null): array
    {
        return $this->unpinChatMessage($messageId, $chatId);
    }

    /**
     * Shortcut for unpinChatMessage.
     *
     * @param int|null $messageId
     * @param string|null $chatId
     * @return array
     */
    public function unpin(?int $messageId = null, ?string $chatId = null): array
    {
        return $this->unpinChatMessage($messageId, $chatId);
    }

    /**
     * Shortcut for unpinAllChatMessages.
     *
     * @param string|null $chatId
     * @return array
     */
    public function unpinAllMessages(?string $chatId = null): array
    {
        return $this->unpinAllChatMessages($chatId);
    }

    /**
     * Shortcut for unpinAllChatMessages.
     *
     * @param string|null $chatId
     * @return array
     */
    public function unpinAll(?string $chatId = null): array
    {
        return $this->unpinAllChatMessages($chatId);
    }
}
