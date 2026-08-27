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

trait VanguardBuilder
{
    
    protected array $chat = [];
    protected ?string $builder_chat_id = null;
    protected ?string $builder_text = null;
    protected ?string $builder_reply_to = null;
    protected ?string $builder_file_path = null;
    protected ?string $builder_caption = null;
    protected ?string $builder_file_id = null;
    protected ?string $builder_file_type = null;
    protected ?string $builder_message_id = null;
    protected ?string $builder_from_chat_id = null;
    protected ?string $builder_to_chat_id = null;
    protected ?string $builder_question = null;
    protected array  $builder_options = [];
    protected ?float  $builder_lat = null;
    protected ?float  $builder_lng = null;
    protected ?string $builder_contact_first = null;
    protected ?string $builder_contact_phone = null;
    protected ?array $builder_inline_keypad = null;
    protected ?array $builder_chat_keypad = null;
    protected ?string $builder_chat_keypad_type = null;

    public function chat(string $chat_id): static
    {
        $this->builder_chat_id = $chat_id;
        return $this;
    }

    /*
    public function message(string $text, ?string $parse_mode = null): static
    {
        $this->builder_text = $text;
        if ($parse_mode && is_callable([$this, 'setParseMode'])) {
            $this->setParseMode($parse_mode);
        }
        return $this;
    }

    public function location(float $lat, float $lng): static
    {
        $this->builder_lat = $lat;
        $this->builder_lng = $lng;
        return $this;
    }

    public function contact(string $first_name, string $phone_number): static
    {
        $this->builder_contact_first = $first_name;
        $this->builder_contact_phone = $phone_number;
        return $this;
    }
    */

    public function replyTo(string $message_id): static
    {
        $this->builder_reply_to = $message_id;
        return $this;
    }

    public function file(string $path): static
    {
        $this->builder_file_path = $path;
        $this->builder_file_id = null;
        $this->builder_file_type = null;
        return $this;
    }

    public function file_id(string $file_id): static
    {
        $this->builder_file_id = $file_id;
        return $this;
    }

    public function file_type(string $file_type): static
    {
        $this->builder_file_type = $file_type;
        return $this;
    }

    public function caption(string $caption, ?string $parse_mode = null): static
    {
        $this->builder_caption = $caption;
        if ($parse_mode && is_callable([$this, 'setParseMode'])) {
            $this->setParseMode($parse_mode);
        }
        return $this;
    }

    public function poll(string $question, array $options): static
    {
        $this->builder_question = $question;
        $this->builder_options = $options;
        return $this;
    }

    public function inlineKeypad(array $keypad): static
    {
        $this->builder_inline_keypad = $keypad;
        return $this;
    }

    public function chatKeypad(array $keypad, ?string $keypad_type = 'New'): static
    {
        $this->builder_chat_keypad = $keypad;
        $this->builder_chat_keypad_type = $keypad_type;
        return $this;
    }

    public function forwardFrom(string $from_chat_id): static
    {
        $this->builder_from_chat_id = $from_chat_id;
        return $this;
    }

    public function forwardTo(string $to_chat_id): static
    {
        $this->builder_to_chat_id = $to_chat_id;
        return $this;
    }

    public function messageId(string $message_id): static
    {
        $this->builder_message_id = $message_id;
        return $this;
    }

    private function resetBuilder(): void
    {
        $this->builder_text = null;
        $this->builder_reply_to = null;
        $this->builder_file_path = null;
        $this->builder_caption = null;
        $this->builder_file_id = null;
        $this->builder_file_type = null;
        $this->builder_message_id = null;
        $this->builder_from_chat_id = null;
        $this->builder_to_chat_id = null;
        $this->builder_question = null;
        $this->builder_options = [];
        $this->builder_lat = null;
        $this->builder_lng = null;
        $this->builder_contact_first = null;
        $this->builder_contact_phone = null;
        $this->builder_inline_keypad = null;
        $this->builder_chat_keypad = null;
        $this->builder_chat_keypad_type = null;
    }
}
