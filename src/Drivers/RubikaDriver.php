<?php

namespace KrubiK\Drivers;
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

use KrubiK\Drivers\Contracts\BotDriverInterface;
use KrubiK\Drivers\Contracts\VanguardInterface;
use RubikaBot\Bot as VanguardCore; // کتابخانه اصلی
use KrubiK\Drivers\Arcane\NeonVitality;   // سوپر تریت

class RubikaDriver extends VanguardCore implements BotDriverInterface, VanguardInterface
{
    // 💉 Inject the Soul
    use NeonVitality;

    /**
     * RubikaDriver constructor.
     *
     * @param array $config The specific configuration for this driver.
     */
    public function __construct(array $config)
    {
        // 1. Call the Old God (VanguardCore) constructor
        // This sets up the Token and BaseUrl naturally.
        parent::__construct($config['token'], $config['config'] ?? []);

        // 2. Ignite the NeonSoul Engine (Arcane)
        $this->igniteNeon($config);
    }

    // =========================================================================
    // 🚜 IMPLEMENTATION OF BotDriverInterface
    // =========================================================================
    // اینجا ما متدهای اینترفیس (مثل send) را به متدهای Vanguard (مثل sendText) وصل می‌کنیم.
    // از متغیرهای Context که توسط NeonVitality مدیریت می‌شوند استفاده می‌کنیم.

    public function legacySend(): array
    {
        // Example mapping:
        // If we have text, use sendText. If we have file_id, use sendFile, etc.
        
        // Using properties from InteractsWithContext: $this->chat_id, $this->text_content
        return $this->sendText($this->chat_id, $this->text_content);
    }

    public function editMessage(): array
    {
        return $this->editMessageText($this->message_id, $this->text_content);
    }
    
    // و بقیه متدها...
    // نکته مهم: چون VanguardCore را اکستند کردیم، اگر متدی در اینترفیس نباشد
    // ولی در Vanguard باشد (مثلا getBannedUsers)، مستقیماً قابل صدا زدن است!
}
