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

trait PHPRBK_Methods
{
    /**
     * Use this method to send answers to an inline query.
     *
     * @param string $inlineQueryId Unique identifier for the answered query
     * @param array $results A JSON-serialized array of results for the inline query
     * @param int $cacheTime The maximum amount of time in seconds that the result of the inline query may be cached on the server. Defaults to 300.
     * @param bool $isPersonal Pass True if results may be cached on the server side only for the user that sent the query. By default, results may be returned to any user who sends the same query.
     * @param string $nextOffset Pass the offset that a client should send in the next query with the same text to receive more results. Pass an empty string if there are no more results or if you don't support pagination. Offset length can't exceed 64 bytes.
     * @return array
     */
    public function answerInlineQuery(string $inlineQueryId, array $results, int $cacheTime = 300, bool $isPersonal = false, string $nextOffset = ''): array
    {
        return $this->makeRequest('answerInlineQuery', [
            'inline_query_id' => $inlineQueryId,
            'results'         => json_encode($results), // API typically expects JSON string for complex objects
            'cache_time'      => $cacheTime,
            'is_personal'     => $isPersonal,
            'next_offset'     => $nextOffset
        ]);
    }

    /**
     * Use this method to get a list of profile pictures for a user.
     *
     * @param int $userId Unique identifier of the target user
     * @param int $offset Sequential number of the first photo to be returned. By default, all photos are returned.
     * @param int $limit Limits the number of photos to be retrieved. Values between 1-100 are accepted. Defaults to 100.
     * @return array
     */
    public function getUserProfilePhotos(int $userId, int $offset = 0, int $limit = 100): array
    {
        return $this->makeRequest('getUserProfilePhotos', [
            'user_id' => $userId,
            'offset'  => $offset,
            'limit'   => $limit
        ]);
    }

    /**
     * Use this method for your bot to leave a group, supergroup or channel.
     *
     * @param string|null $chatId Unique identifier for the target chat (optional)
     * @return array
     */
    public function leaveChat(?string $chatId = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        return $this->makeRequest('leaveChat', [
            'chat_id' => $realChatId
        ]);
    }

    /**
     * Shortcut for leaveChat.
     *
     * @param string|null $chatId
     * @return array
     */
    public function leave(?string $chatId = null): array
    {
        return $this->leaveChat($chatId);
    }
}
