<?php

namespace KrubiK\Keyboard;

use Illuminate\Contracts\Support\Arrayable;
use function KrubiK\Render\Helpers\filterNulls;

/**
 * @see https://core.telegram.org/bots/api#switchinlinequerychosenchat
*/
readonly class SwitchInlineQueryChosenChat implements Arrayable
{
    public function __construct(
        public ?string $query = null,
        public ?bool $allowUserChats = null,
        public ?bool $allowBotChats = null,
        public ?bool $allowGroupChats = null,
        public ?bool $allowChannelChats = null,
    ) {}

    // super-simple Delegation Factory
    public static function make(
        ?string $query = null,
        ?bool $allowUserChats = null,
        ?bool $allowBotChats = null,
        ?bool $allowGroupChats = null,
        ?bool $allowChannelChats = null,
    ) {
        return new self(
            $query,
            $allowUserChats,
            $allowBotChats,
            $allowGroupChats,
            $allowChannelChats,
        );
    }

    /**
     * @return array<string, mixed>
    */
    public function toArray(): array
    {
        return filterNulls([
            'query' => $this->query,
            'allow_user_chats' => $this->allowUserChats,
            'allow_bot_chats' => $this->allowBotChats,
            'allow_group_chats' => $this->allowGroupChats,
            'allow_channel_chats' => $this->allowChannelChats,
        ]);
    }
}
