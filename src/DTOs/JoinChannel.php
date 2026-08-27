<?php
namespace KrubiK\DTOs;
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

readonly class JoinChannel implements LinkTarget {
    public function __construct(public string $username) {}

    /**
     * متد استاتیک پایه برای شروع زنجیره ساخت
     * 
     * نقطه شروع ساخت (Factory Method)
     * با بازگشت دقیق(Return Type)
    */
    public static function make(string $username): static
    {
        return new static($username);
    }
    
    public function toPayload(): array {
        return [
            'type' => 'JoinChannel', 
            'joinchannel_data' => ['username' => $this->username]
        ];
    }
}
