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

use KrubiK\GamifyDices\DicesReflector as Dices;
// use KrubiK\GamifyDices\Types\DiceVariant;
use KrubiK\GamifyDices\Types\DiceResult;
use Illuminate\Support\Facades\File;

trait CanPlayDiceGames
{
    // Ensure this trait has access to Chat ID resolution logic

    /**
     * Retrieve the list of available dice emojis and their max values from the Helper.
     *
     * @return array<int, array{0: string, 1: int}> [['🎲', 6], ...]
     */
    public function getAvailableDices(): array
    {
        return Dices::getAvailableList();
    }

    /**
     * Use this method to send a dice, which will have a random value from 1 to 6.
     *
     * @param mixed $chatId
     * @param string $emoji Emoji on which the dice throw animation is based. Currently, must be one of "🎲", "🎯", "🏀", "⚽", "🎳", or "🎰". Dice can have values 1-6 for "🎲", "🎯" and "🎳", values 1-5 for "🏀" and "⚽", and values 1-64 for "🎰". Defaults to "🎲"
     * @param int|null $replyToMessageId
     * @return array
     */
    /**
     * Send a dice to a specific chat.
     * 
     * @param string|\Stringable $emoji The dice emoji (e.g., '🎲' or Dices::Soccer)
     * @param int|null $replyToMessageId Optional message ID to reply to.
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null).
     * @return array Raw API response.
     */
    public function sendDice(string|\Stringable $emoji = '🎲', ?int $replyToMessageId = null, ?string $chatId = null, ?string $langLocalCode = null): array
    {
        $realChatId = $this->resolveChatId($chatId);

        // 1. لود کردن ترجمه‌ها از فایل JSON
        $this->loadDiceTranslations($langLocalCode);

        $params = [
            'chat_id' => $realChatId,
            'emoji'   => (string) $emoji
        ];

        if ($replyToMessageId) {
            $params['reply_to_message_id'] = $replyToMessageId;
        }

        return $this->makeRequest('sendDice', $params);
    }

    /**
     * "Dealer Bot" Logic: Rolls the dice and announces the result with a localized message.
     * Handles updates where Bot sent a dice, generating a reaction text.
     * 
     * @param string $userMention Name/Mention of the user triggering the game.
     * @param string|null $chatId Target chat ID.
     * @param string|\Stringable $emoji The dice variant to play with.
     * @param string|null $langLocalCode leave_default or set something like 'en', 'fa', ...
     * @return void
     */
    public function playDiceGame(string $userMention, ?string $chatId = null, string|\Stringable $emoji = '🎲', ?string $langLocalCode = null): void
    {
        $realChatId = $this->resolveChatId($chatId);

        // 1. Send Dice & Dice Request
        $response = $this->sendDice($emoji, null, $realChatId, $langLocalCode);

        // 2. Parse Result of CurrentSentDice safely using DTO
        $result = DiceResult::fromResponse($response);

        if (!$result) {
            // Fallback for API errors with localized message
            $this->chat($realChatId)
                ->message(__('dice.errors.fetch_failed'))
                ->send();
            return;
        }

        // 3. Generate Context-Aware Response
        $replyText = $this->generateDiceResponseText($result->emoji, $result->value, $userMention);

        // 4. Wait for animation (UX improvement)
        sleep(2); 

        // 5. Send the commentary replying to the dice message
        $this->chat($realChatId)
            ->message($replyText)
            ->replyTo((int)$result->messageId)
            ->send();
    }

    /**
     * Handles updates where a USER sent a dice, generating a reaction text.
     * 
     * @param array $message The 'message' array from the Update object.
     * @return string|null Generated text or null if not a valid dice message.
     */
    public function handleUserDiceRoll(array $message): ?string 
    {
        if (!isset($message['dice']['value'], $message['dice']['emoji'])) {
            return null;
        }

        // Get localized generic user label
        $userLabel = __('dice.generic.user_label'); 

        return $this->generateDiceResponseText(
            $message['dice']['emoji'], 
            $message['dice']['value'], 
            $userLabel
        );
    }

    /**
     * Generates gamified text based on the dice result.
     * Uses PHP 8.2 Match Expression and Laravel Localization helper `__()`.
     */
    protected function generateDiceResponseText(string $emoji, int $value, string $userMention): string
    {
        return match ($emoji) {
            // Standard Dice: Cube, Dart, Bowling (Range: 1-6)
            '🎲', '🎯', '🎳' => match ($value) {
                6 => __('dice.standard.win', ['user' => $userMention]),
                1 => __('dice.standard.lose', ['user' => $userMention]),
                default => __('dice.standard.result', ['user' => $userMention, 'value' => $value]),
            },
            
            // Sports: Basketball, Soccer (Range: 1-5)
            '🏀', '⚽' => match ($value) {
                4, 5 => __('dice.sports.goal', ['user' => $userMention, 'value' => $value]),
                1, 2 => __('dice.sports.miss', ['value' => $value]),
                3 => __('dice.sports.close', ['value' => $value]),
                default => __('dice.sports.score', ['value' => $value]),
            },
            
            // Slot Machine (Range: 1-64)
            '🎰' => match (true) {
                $value === 64 => __('dice.slot.jackpot', ['user' => $userMention]),
                $value > 40   => __('dice.slot.big_win', ['user' => $userMention]),
                default       => __('dice.slot.retry')
            },
            
            // Fallback for unknown emojis
            default => __('dice.generic.result', ['emoji' => $emoji, 'value' => $value]),
        };
    }

    /**
     * Load Dice translations from the local JSON file.
     * @param string|null $langLocalCode leave_default or set something like 'en', 'fa', ...
     * @return void
     */
    protected function loadDiceTranslations(?string $langLocalCode = null): void
    {

        $current_locale = $langLocalCode ?? app()->getLocale();

        // برای جلوگیری از خواندن فایل در هر بار فراخوانی، چک می‌کنیم آیا کلید وجود دارد یا خیر
        if (Lang::has('dice.generic.result', $current_locale)) {
            return;
        }

        // مسیر فایل JSON در کنار همین فایل Trait
        $jsonPath = __DIR__ . "/../GamifyDices/Lang/{$current_locale}.json";

        if (File::exists($jsonPath)) {

            // واندن فایل JSON و تبدیل به آرایه (بسیار کاربردی در نسخه 10)
            // این متد خودش decode می‌کند و مدیریت خطا دارد
            // روش ایمن با File Facade
            try {
                $translations = File::json($jsonPath);
                Lang::addLines($translations, $current_locale);
            } catch (\Throwable $e) {
                // مدیریت خطا در صورت خراب بودن JSON
                // اگر فایل نبود یا جیسون خراب بود، نادیده بگیر (یا لاگ کن)
                // Log::error("Dice translation error: " . $e->getMessage());
            }
        }
    }

    /**
     * Helper to retrieve a dice translation message.
     *
     * @param string $key (e.g., 'standard.win')
     * @param array $replace Variables to replace
     * @param string|null $langLocalCode leave_default or set something like 'en', 'fa', ...
     * @return string
     */
    public function getDiceMessage(string $key, array $replace = [], ?string $langLocalCode = null): string
    {
        $this->loadDiceTranslations($langLocalCode);
        
        // چون در فایل JSON کلید اصلی "dice" است، اینجا به صورت dice.key دسترسی پیدا می‌کنیم
        return __("dice.$key", $replace, ($langLocalCode ?? app()->getLocale()));
    }
}
