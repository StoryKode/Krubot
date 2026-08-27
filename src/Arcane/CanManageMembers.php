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

trait CanManageMembers
{
    // ========================================================================
    // Main Methods (Standardized)
    // ========================================================================

    /**
     * Ban/Kick a user from the group/channel.
     *
     * @param int $userId Unique identifier of the target user.
     * @param string|null $chatId Unique identifier for the target chat (optional).
     * @return array
     */
    public function banChatMember(int $userId, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        return $this->makeRequest('banChatMember', [
            'chat_id' => $realChatId,
            'user_id' => $userId
        ]);
    }

    /**
     * Unban a user.
     *
     * @param int $userId Unique identifier of the target user.
     * @param string|null $chatId Unique identifier for the target chat (optional).
     * @return array
     */
    public function unbanChatMember(int $userId, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        return $this->makeRequest('unbanChatMember', [
            'chat_id' => $realChatId,
            'user_id' => $userId
        ]);
    }

    /**
     * Use this method to promote or demote a user in a supergroup or a channel.
     *
     * @param int $userId Unique identifier of the target user.
     * @param array $privileges Array of privileges (e.g. ['can_change_info' => true]).
     * @param string|null $chatId Unique identifier for the target chat (optional).
     * @return array
     */
    public function promoteChatMember(int $userId, array $privileges = [], ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        $params = array_merge([
            'chat_id' => $realChatId,
            'user_id' => $userId,
        ], $privileges);

        return $this->makeRequest('promoteChatMember', $params);
    }

    /**
     * Use this method to restrict a user in a supergroup.
     *
     * @param int $userId Unique identifier of the target user.
     * @param array $permissions A JSON-serialized object for new user permissions.
     * @param int $untilDate Date when restrictions will be lifted (Unix time).
     * @param string|null $chatId Unique identifier for the target chat (optional).
     * @return array
     */
    public function restrictChatMember(int $userId, array $permissions, int $untilDate = 0, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        return $this->makeRequest('restrictChatMember', [
            'chat_id'     => $realChatId,
            'user_id'     => $userId,
            'permissions' => $permissions,
            'until_date'  => $untilDate,
        ]);
    }

    /**
     * Use this method to set default chat permissions for all members.
     *
     * @param array $permissions New default chat permissions.
     * @param string|null $chatId Unique identifier for the target chat (optional).
     * @return array
     */
    public function setChatPermissions(array $permissions, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        return $this->makeRequest('setChatPermissions', [
            'chat_id'     => $realChatId,
            'permissions' => $permissions
        ]);
    }

    /**
     * Use this method to approve a chat join request.
     *
     * @param int $userId Unique identifier of the target user.
     * @param string|null $chatId Unique identifier for the target chat (optional).
     * @return array
     */
    public function approveChatJoinRequest(int $userId, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        return $this->makeRequest('approveChatJoinRequest', [
            'chat_id' => $realChatId,
            'user_id' => $userId
        ]);
    }

    /**
     * Use this method to decline a chat join request.
     *
     * @param int $userId Unique identifier of the target user.
     * @param string|null $chatId Unique identifier for the target chat (optional).
     * @return array
     */
    public function declineChatJoinRequest(int $userId, ?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        return $this->makeRequest('declineChatJoinRequest', [
            'chat_id' => $realChatId,
            'user_id' => $userId
        ]);
    }

    // ========================================================================
    // Shortcuts / Aliases (Cleaner Syntax)
    // ========================================================================

    /**
     * Alias for banChatMember
     */
    public function banMember(int $userId, ?string $chatId = null): array
    {
        return $this->banChatMember($userId, $chatId);
    }

    /**
     * Alias for banChatMember
     */
    public function ban(int $userId, ?string $chatId = null): array
    {
        return $this->banChatMember($userId, $chatId);
    }

    /**
     * Alias for banChatMember
     */
    public function kick(int $userId, ?string $chatId = null): array
    {
        return $this->banChatMember($userId, $chatId);
    }

    /**
     * Alias for unbanChatMember
     */
    public function unbanMember(int $userId, ?string $chatId = null): array
    {
        return $this->unbanChatMember($userId, $chatId);
    }

    /**
     * Alias for unbanChatMember
     */
    public function unban(int $userId, ?string $chatId = null): array
    {
        return $this->unbanChatMember($userId, $chatId);
    }

    /**
     * Alias for promoteChatMember
     */
    public function promoteMember(int $userId, array $privileges = [], ?string $chatId = null): array
    {
        return $this->promoteChatMember($userId, $privileges, $chatId);
    }

    /**
     * Alias for promoteChatMember
     */
    public function promoteMemberTo(int $userId, array $privileges = [], ?string $chatId = null): array
    {
        return $this->promoteChatMember($userId, $privileges, $chatId);
    }

    /**
     * Alias for restrictChatMember
     */
    public function restrictMember(int $userId, array $permissions, int $untilDate = 0, ?string $chatId = null): array
    {
        return $this->restrictChatMember($userId, $permissions, $untilDate, $chatId);
    }

    /**
     * Alias for restrictChatMember
     */
    public function restrictMemberFrom(int $userId, array $permissions, int $untilDate = 0, ?string $chatId = null): array
    {
        return $this->restrictChatMember($userId, $permissions, $untilDate, $chatId);
    }

    /**
     * Alias for setChatPermissions
     */
    public function setPermissions(array $permissions, ?string $chatId = null): array
    {
        return $this->setChatPermissions($permissions, $chatId);
    }

    /**
     * Alias for setChatPermissions
     */
    public function setGlobalPermissions(array $permissions, ?string $chatId = null): array
    {
        return $this->setChatPermissions($permissions, $chatId);
    }

    /**
     * Alias for approveChatJoinRequest
     */
    public function approveJoinRequest(int $userId, ?string $chatId = null): array
    {
        return $this->approveChatJoinRequest($userId, $chatId);
    }

    /**
     * Alias for approveChatJoinRequest
     */
    public function approveJoin(int $userId, ?string $chatId = null): array
    {
        return $this->approveChatJoinRequest($userId, $chatId);
    }

    /**
     * Alias for approveChatJoinRequest
     */
    public function acceptJoin(int $userId, ?string $chatId = null): array
    {
        return $this->approveChatJoinRequest($userId, $chatId);
    }

    /**
     * Alias for approveChatJoinRequest
     */
    public function welcomeTo(int $userId, ?string $chatId = null): array
    {
        return $this->approveChatJoinRequest($userId, $chatId);
    }

    /**
     * Alias for approveChatJoinRequest
     */
    public function letIn(int $userId, ?string $chatId = null): array
    {
        return $this->approveChatJoinRequest($userId, $chatId);
    }

    /**
     * Alias for declineChatJoinRequest
     */
    public function declineJoinRequest(int $userId, ?string $chatId = null): array
    {
        return $this->declineChatJoinRequest($userId, $chatId);
    }

    /**
     * Alias for declineChatJoinRequest
     */
    public function declineJoin(int $userId, ?string $chatId = null): array
    {
        return $this->declineChatJoinRequest($userId, $chatId);
    }

    /**
     * Alias for declineChatJoinRequest
     */
    public function cancelJoin(int $userId, ?string $chatId = null): array
    {
        return $this->declineChatJoinRequest($userId, $chatId);
    }

    /**
     * Alias for declineChatJoinRequest
     */
    public function ignoreJoinRequest(int $userId, ?string $chatId = null): array
    {
        return $this->declineChatJoinRequest($userId, $chatId);
    }

    /**
     * Alias for declineChatJoinRequest
     */
    public function ignoreJoin(int $userId, ?string $chatId = null): array
    {
        return $this->declineChatJoinRequest($userId, $chatId);
    }
}
