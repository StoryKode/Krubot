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

use Closure;

trait HasInteractiveUtils
{
    /**
     * نمایش اکشن چت (مانند Typing...)
     * 
     * @param string $action typing, uploading_photo, etc.
    */
    public function chatAction(string $action, ?string $chatId = null): array
    {
        return $this->makeRequest('sendChatAction', [
            'chat_id' => $this->resolveChatId($chatId),
            'action' => $action
        ]);
    }

    public function location(float $latitude, float $longitude, ?string $chatId = null): array
    {
        return $this->makeRequest('sendLocation', [
            'chat_id' => $this->resolveChatId($chatId),
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);
    }

    public function contact(string $phoneNumber, string $firstName, ?string $lastName = null, ?string $chatId = null): array
    {
        $params = [
            'chat_id' => $this->resolveChatId($chatId),
            'phone_number' => $phoneNumber,
            'first_name' => $firstName
        ];
        if ($lastName) $params['last_name'] = $lastName;

        return $this->makeRequest('sendContact', $params);
    }

    /**
     * شرط گذاری زنجیره‌ای (Fluent Conditional).
     * اگر شرط برقرار بود، کلوژر روی آبجکت فعلی ($this) اجرا می‌شود.
     * 
     * @param bool|Closure $condition
     * @param callable $callback function($bot) { ... }
     * @return static
    */
    public function when(bool|Closure $condition, callable $callback): static
    {
        $result = $condition instanceof Closure ? $condition($this) : $condition;

        if ($result) {
            $callback($this);
        }

        return $this;
    }
}
