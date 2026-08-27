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

// Chat and Member Management (CanManageChats)
// Includes banning, getting links, and managing administrators

trait CanManageChats
{
    // ========================================================================
    // Main Methods (Standardized)
    // ========================================================================

    /**
     * Get chat administrators list.
     *
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null).
     * @return array
     */
    public function getChatAdministrators(?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);
        return $this->makeRequest('getChatAdministrators', ['chat_id' => $realChatId]);
    }

    /**
     * Get member count.
     *
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null).
     * @return int|array
     */
    public function getChatMembersCount(?string $chatId = null)
    {
        $realChatId = $this->resolveChatId($chatId);
        return $this->makeRequest('getChatMembersCount', ['chat_id' => $realChatId]);
    }

    /**
     * Get specific member info in a chat.
     *
     * @param int $userId Target User ID
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null).
     * @return array
     */
    public function getChatMember(int $userId, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);
        return $this->makeRequest('getChatMember', [
            'chat_id' => $realChatId,
            'user_id' => $userId
        ]);
    }

    /**
     * Change Chat Title.
     *
     * @param string $title New chat title
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null).
     * @return array
     */
    public function setChatTitle(string $title, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);
        return $this->makeRequest('setChatTitle', [
            'chat_id' => $realChatId,
            'title'   => $title
        ]);
    }

    /**
     * Change Chat Description.
     *
     * @param string $description New chat description
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null).
     * @return array
     */
    public function setChatDescription(string $description, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);
        return $this->makeRequest('setChatDescription', [
            'chat_id'     => $realChatId,
            'description' => $description
        ]);
    }

    /**
     * Use this method to set a new profile photo for the chat.
     *
     * @param string $photo New chat photo (file_id)
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null).
     * @return array
     */
    public function setChatPhoto(string $photo, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);
        return $this->makeRequest('setChatPhoto', [
            'chat_id' => $realChatId,
            'photo'   => $photo
        ]);
    }

    /**
     * Use this method to delete a chat photo.
     *
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null).
     * @return array
     */
    public function deleteChatPhoto(?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);
        return $this->makeRequest('deleteChatPhoto', ['chat_id' => $realChatId]);
    }

    /**
     * Generate or get invite link.
     *
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null).
     * @return array
     */
    public function exportChatInviteLink(?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);
        return $this->makeRequest('exportChatInviteLink', ['chat_id' => $realChatId]);
    }

    /**
     * Use this method to revoke an invite link created by the bot.
     *
     * @param string $inviteLink The invite link to revoke
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null).
     * @return array
     */
    public function revokeChatInviteLink(string $inviteLink, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);
        return $this->makeRequest('revokeChatInviteLink', [
            'chat_id'     => $realChatId,
            'invite_link' => $inviteLink
        ]);
    }

    // ========================================================================
    // Shortcuts / Aliases (Cleaner Syntax)
    // ========================================================================

    /**
     * Alias for revokeChatInviteLink
     */
    public function revokeInviteLink(string $inviteLink, ?string $chatId = null): array
    {
        return $this->revokeChatInviteLink($inviteLink, $chatId);
    }

    /**
     * Alias for exportChatInviteLink
     */
    public function exportInviteLink(?string $chatId = null): array
    {
        return $this->exportChatInviteLink($chatId);
    }

    /**
     * Alias for setChatTitle
     */
    public function setTitle(string $title, ?string $chatId = null): array
    {
        return $this->setChatTitle($title, $chatId);
    }

    /**
     * Alias for setChatDescription
     */
    public function setDescription(string $description, ?string $chatId = null): array
    {
        return $this->setChatDescription($description, $chatId);
    }

    /**
     * Alias for setChatPhoto
    */
    public function setPhoto(string $photo, ?string $chatId = null): array
    {
        return $this->setChatPhoto($photo, $chatId);
    }

    /**
     * Alias for setChatPhoto
    */
    public function updatePhoto(string $photo, ?string $chatId = null): array
    {
        return $this->setChatPhoto($photo, $chatId);
    }

    /**
     * Alias for setChatPhoto
    */
    public function setCImage(string $photo, ?string $chatId = null): array
    {
        return $this->setChatPhoto($photo, $chatId);
    }

    /**
     * Alias for setChatPhoto
    */
    public function setCPic(string $photo, ?string $chatId = null): array
    {
        return $this->setChatPhoto($photo, $chatId);
    }

    /**
     * Alias for setChatPhoto
    */
    public function updateCImage(string $photo, ?string $chatId = null): array
    {
        return $this->setChatPhoto($photo, $chatId);
    }

    /**
     * Alias for deleteChatPhoto
    */
    public function deletePhoto(?string $chatId = null)
    {
        return $this->deleteChatPhoto($chatId);
    }

    /**
     * Alias for deleteChatPhoto
    */
    public function removePhoto(?string $chatId = null)
    {
        return $this->deleteChatPhoto($chatId);
    }

    /**
     * Alias for deleteChatPhoto
    */
    public function removeCImage(?string $chatId = null)
    {
        return $this->deleteChatPhoto($chatId);
    }

    /**
     * Alias for deleteChatPhoto
    */
    public function removeCPic(?string $chatId = null)
    {
        return $this->deleteChatPhoto($chatId);
    }

    /**
     * Alias for getChatMember
     */
    public function getMember(int $userId, ?string $chatId = null): array
    {
        return $this->getChatMember($userId, $chatId);
    }

    /**
     * Alias for getChatMembersCount
     */
    public function getMembersCount(?string $chatId = null)
    {
        return $this->getChatMembersCount($chatId);
    }

    /**
     * Alias for getChatMembersCount
     */
    public function getCMC(?string $chatId = null)
    {
        return $this->getChatMembersCount($chatId);
    }

    /**
     * Alias for getChatAdministrators
     */
    public function getAdministrators(?string $chatId = null): array
    {
        return $this->getChatAdministrators($chatId);
    }

    /**
     * Alias for getChatAdministrators
     */
    public function getAdmins(?string $chatId = null): array
    {
        return $this->getChatAdministrators($chatId);
    }
}
