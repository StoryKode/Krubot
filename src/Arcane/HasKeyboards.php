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

use KrubiK\Keyboard\Keyboard;
use KrubiK\Keyboard\PowerButton;
use RubikaBot\Keyboard\Keypad;
use RubikaBot\Keyboard\KeypadRow;

trait HasKeyboards
{

    /**
     * Fluent Keyboard Builder (Closure Style).
     * Allows building complex keyboards using a clean syntax.
     */
    public function attachKeyboard(array|Keyboard|callable $callback): static
    {
        if($callback instanceof Keyboard)
            $callback = $callback->toArray();

        if(is_array($callback))
            return $this->chatKeypad($callback);

        $keypad = Keypad::make();
        
        $builder = new class($keypad) {
            private $keypad;
            public function __construct($keypad) { $this->keypad = $keypad; }

            public function row(callable $rowCallback) {
                $realRow = new KeypadRow();
                
                $rowBuilder = new class($realRow) {
                    private $row;
                    public function __construct($row) { $this->row = $row; }

                    public function simple($text, $id = null) {
                        $id = $id ?? md5($text);
                        $this->row->add(PowerButton::simple($id, $text));
                        return $this;
                    }

                    public function link($text, $url, $id = null) {
                        // Note: PowerButton::link implementation depends on VanguardCore version,
                        // using simple as fallback or implementation detail here.
                        // Assuming custom PowerButton wrapper or simple button for now based on KeypadRow.
                         $this->row->add(PowerButton::simple($id ?? md5($text), $text)); 
                         return $this;
                    }
                };

                $rowCallback($rowBuilder);
                $this->keypad->addRow($realRow);
            }
        };

        $callback($builder);

        return $this->chatKeypad($keypad->toArray());
    }
    
    /**
     * Answer a callback query (Essential for removing the loading clock icon from buttons).
     *
     * @param string $callbackQueryId
     * @param string|null $text Notification text
     * @param bool $showAlert Show alert instead of toast
     * @param string|null $url Open URL
     * @return array
     */
    public function answerCallbackQuery(string $callbackQueryId, ?string $text = null, bool $showAlert = false, ?string $url = null): array
    {
        $params = [
            'callback_query_id' => $callbackQueryId,
            'show_alert'        => $showAlert,
        ];

        if ($text) $params['text'] = $text;
        if ($url)  $params['url'] = $url;

        return $this->makeRequest('answerCallbackQuery', $params);
    }

    /**
     * Edit caption of a media message.
     *
     * @param int $messageId
     * @param string $caption
     * @param array|null $inlineKeypad
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null).
     * @return array
     */
    public function editMessageCaption(int $messageId, string $caption, ?array $inlineKeypad = null, ?string $chatId = null): array
    {
        // Resolve the Chat ID using the helper method
        $realChatId = $this->resolveChatId($chatId);

        $params = [
            'chat_id'    => $realChatId,
            'message_id' => $messageId,
            'caption'    => $caption,
        ];

        if ($inlineKeypad) {
            $params['reply_markup'] = ['inline_keyboard' => $inlineKeypad];
        }

        return $this->makeRequest('editMessageCaption', $params);
    }

    /**
     * Edit Reply Markup (Keyboard) only.
     *
     * @param int $messageId
     * @param array $inlineKeypad
     * @param string|null $chatId Optional Chat ID (uses resolveChatId if null).
     * @return array
     */
    public function editMessageReplyMarkup(int $messageId, array $inlineKeypad, ?string $chatId = null): array
    {
        // Resolve the Chat ID using the helper method
        $realChatId = $this->resolveChatId($chatId);

        return $this->makeRequest('editMessageReplyMarkup', [
            'chat_id'      => $realChatId,
            'message_id'   => $messageId,
            'reply_markup' => ['inline_keyboard' => $inlineKeypad]
        ]);
    }
}
