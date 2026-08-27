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

use Stringable;
use KrubiK\DTOs\Message;

class Answer implements Stringable
{
    protected string $text;
    protected ?string $value = null;
    protected ?Message $message = null;

    /**
     * Constructor supports both raw text and Message object.
     * 
     * @param string|Message $data Text or Message object
     * @param string|null $value Explicit value (useful for buttons)
     */
    public function __construct($data, ?string $value = null)
    {
        if ($data instanceof Message) {
            $this->message = $data;
            $this->text = $data->getText() ?? '';
        } else {
            $this->text = (string) $data;
        }

        // If value is passed explicitly, use it. Otherwise default to text.
        $this->value = $value ?? $this->text;
    }

    /**
     * Get the text response.
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Get the value (useful for payload buttons).
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Check if the answer came from an interactive element (like a button)
     * where text might differ from value.
     */
    public function isInteractiveMessageReply(): bool
    {
        return $this->text !== $this->value;
    }

    /**
     * Get the original Message object (if available).
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }
    
    public function __toString()
    {
        return $this->text;
    }
}
