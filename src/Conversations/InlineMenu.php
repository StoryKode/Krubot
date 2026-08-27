<?php

namespace KrubiK\Conversations;
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

use KrubiK\Krubot;
use KrubiK\Keyboard\PowerButton;
use KrubiK\Helpers\AmethystMatrix as Log;

/**
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
abstract class InlineMenu extends Conversation
{
    protected string $menuText = '';
    
    /**
     * Array of button rows (Final structure for API).
     */
    protected array $menuRows = [];
    
    /**
     * Map button data to method name.
     * Format: ['unique_btn_id' => 'methodName']
     */
    protected array $callbackMap = [];

    /**
     * Menu message ID for later editing.
     */
    public ?string $messageId = null;

    /**
     * Set menu text.
     */
    public function menuText(string $text): static
    {
        $this->menuText = $text;
        return $this;
    }

    /**
     * Clear buttons and callbacks.
     */
    public function clearButtons(): static
    {
        $this->menuRows = [];
        $this->callbackMap = [];
        return $this;
    }

    /**
     * Add a button row with handler method definition.
     * 
     * Example:
     * ->addButtonRow(
     *    ['text' => 'Confirm', 'method' => 'doApprove', 'data' => 'yes_123'],
     *    ['text' => 'Cancel', 'method' => 'doCancel']
     * )
     * 
     * @param array ...$buttons List of buttons
     */
    public function addButtonRow(array ...$buttons): static
    {
        $row = [];
        foreach ($buttons as $btnConfig) {
            $text = $btnConfig['text'];
            // If data is not set, use text hash to be unique
            $data = $btnConfig['data'] ?? md5($text);
            $method = $btnConfig['method'] ?? 'start'; // Default method if not set

            // Store data to method mapping
            $this->callbackMap[$data] = $method;

            // Create a PowerButton and convert to array
            $row[] = PowerButton::simple($data, $text)->toArray();
        }
        
        $this->menuRows[] = $row;
        return $this;
    }

    /**
     * Show or update the menu.
     * This method intelligently decides whether to edit the message or send a new one.
     */
    public function showMenu(bool $forceNew = false): void
    {
        if (!$this->bot) return;

        $sent = false;

        // If we have a previous message and forceNew is false, try to edit
        if ($this->messageId && !$forceNew) {
            try {
                // Using Krubot method chaining
                $this->bot->chat($this->chatId) // Using parent class properties
                          ->messageId($this->messageId)
                          ->message($this->menuText)
                          ->inlineKeypad($this->menuRows)
                          ->editMessage();
                
                $sent = true;
            } catch (\Throwable $e) {
                // If edit failed (e.g., user deleted the message), ignore to send a new one
                Log::warning("Failed to edit menu message: " . $e->getMessage());
            }
        }

        // If edit failed or was forced new, send a new message
        if (!$sent) {
            $this->sendNewMenu();
        }

        // Set the next step to the interaction handler
        // This ensures handleInteraction is executed next time run() is called
        $this->step = 'handleInteraction';
        $this->save();
    }

    /**
     * Send a new message and store its ID.
     */
    private function sendNewMenu(): void
    {
        $res = $this->bot->chat($this->chatId)
                  ->message($this->menuText)
                  ->inlineKeypad($this->menuRows)
                  ->send();
        
        // Extract MessageID from API response for future use
        if (isset($res['data']['message_update']['message_id'])) {
            $this->messageId = $res['data']['message_update']['message_id'];
        }
    }

    /**
     * Central Router: This method is called by Conversation::run() in the next step.
     * Its job is to identify the pressed button and route to the corresponding method.
     */
    public function handleInteraction(Krubot $bot): void
    {
        $input = $bot->text(); // Get text or click data
        
        // Check if input exists in our map
        if (isset($this->callbackMap[$input])) {
            $method = $this->callbackMap[$input];
            
            if (method_exists($this, $method)) {
                // Execute the corresponding method
                $this->$method($bot);
                return;
            } else {
                Log::error("InlineMenu: Method '$method' not found in class " . get_class($this));
            }
        }

        // If input was invalid or method not found, show menu again (Feedback Loop)
        // We could also show an "Invalid Option" message here
        $this->showMenu(); 
    }
    
    /**
     * Close the menu (delete message and end conversation).
     */
    public function closeMenu(): void
    {
        if ($this->messageId && $this->bot) {
            try {
                $this->bot->chat($this->chatId)
                          ->messageId($this->messageId)
                          ->delete();
            } catch (\Throwable $e) {
                // It doesn't matter if deletion fails
            }
        }
        $this->end();
    }
}
