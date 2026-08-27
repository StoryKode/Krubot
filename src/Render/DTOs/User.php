<?php
// File: src/KrubiK/Render/DTOs/User.php

declare(strict_types=1);

namespace KrubiK\Render\DTOs;

/**
 * This object represents a Telegram user or bot.
 *
 * Source: https://core.telegram.org/bots/api#user
 */
class User extends BaseObject
{
    /**
     * @param int $id Unique identifier for this user or bot. This number may have more than 32 significant bits and some programming languages may have difficulty/silent defects in interpreting it. But it has at most 52 significant bits, so a 64-bit integer or double-precision float type are safe for storing this identifier.
     * @param bool $is_bot True, if this user is a bot.
     * @param string $first_name User's or bot's first name.
     * @param string|null $last_name Optional. User's or bot's last name.
     * @param string|null $username Optional. User's or bot's username.
     * @param string|null $language_code Optional. IETF language tag of the user's language (e.g., "en-US").
     * @param bool|null $is_premium Optional. True, if this user is a Telegram Premium user.
     * @param bool|null $added_to_attachment_menu Optional. True, if this user added the bot to the attachment menu.
     * @param bool|null $can_join_groups Optional. True, if the bot can be invited to groups. Returned only in getMe.
     * @param bool|null $can_read_all_group_messages Optional. True, if privacy mode is disabled for the bot. Returned only in getMe.
     * @param bool|null $supports_guest_queries Optional. True, if the bot supports guest queries from chats it is not a member of. Returned only in getMe.
     * @param bool|null $supports_inline_queries Optional. True, if the bot supports inline queries. Returned only in getMe.
     * @param bool|null $can_connect_to_business Optional. True, if the bot can be connected to a user account to manage it. Returned only in getMe.
     * @param bool|null $has_main_web_app Optional. True, if the bot has a main Web App. Returned only in getMe.
     * @param bool|null $has_topics_enabled Optional. True, if the bot has forum topic mode enabled in private chats. Returned only in getMe.
     * @param bool|null $allows_users_to_create_topics Optional. True, if the bot allows users to create and delete topics in private chats. Returned only in getMe.
     * @param bool|null $can_manage_bots Optional. True, if other bots can be created to be controlled by the bot. Returned only in getMe.
     * @param bool|null $supports_join_request_queries Optional. True, if the bot supports join request queries and can be assigned to process them. Returned only in getMe.
     */
    public function __construct(
        protected int $id,
        protected bool $is_bot,
        protected string $first_name,
        protected ?string $last_name = null,
        protected ?string $username = null,
        protected ?string $language_code = null,
        protected ?bool $is_premium = null,
        protected ?bool $added_to_attachment_menu = null,
        protected ?bool $can_join_groups = null,
        protected ?bool $can_read_all_group_messages = null,
        protected ?bool $supports_guest_queries = null,
        protected ?bool $supports_inline_queries = null,
        protected ?bool $can_connect_to_business = null,
        protected ?bool $has_main_web_app = null,
        protected ?bool $has_topics_enabled = null,
        protected ?bool $allows_users_to_create_topics = null,
        protected ?bool $can_manage_bots = null,
        protected ?bool $supports_join_request_queries = null,
    ) {}

    // [Hi-DX] Auto-Generated Methods

    /**
     * [Hi-DX] Fluent getter/setter for the 'id' property.
     *
     * @param int|null $value If provided, sets the 'id'. Otherwise, returns the current value.
     * @return static|int The instance for chaining (setter) or the value (getter).
     */
    public function id(?int $value = null): static|int
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->id = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->id;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'is_bot' property.
     *
     * @param bool|null $value If provided, sets the 'is_bot'. Otherwise, returns the current value.
     * @return static|bool The instance for chaining (setter) or the value (getter).
     */
    public function isBot(?bool $value = null): static|bool
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->is_bot = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->is_bot;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'first_name' property.
     *
     * @param string|null $value If provided, sets the 'first_name'. Otherwise, returns the current value.
     * @return static|string The instance for chaining (setter) or the value (getter).
     */
    public function firstName(?string $value = null): static|string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->first_name = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->first_name;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'last_name' property.
     *
     * @param string|null $value If provided, sets the 'last_name'. Otherwise, returns the current value.
     * @return static|string|null The instance for chaining (setter) or the value (getter).
     */
    public function lastName(?string $value = null): static|?string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->last_name = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->last_name;
    }
    public function fullName(string $firstName, ?string $lastName = null): static|?string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->first_name = $firstName;
            $this->last_name = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'username' property.
     *
     * @param string|null $value If provided, sets the 'username'. Otherwise, returns the current value.
     * @return static|string|null The instance for chaining (setter) or the value (getter).
     */
    public function username(?string $value = null): static|?string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->username = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->username;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'language_code' property.
     *
     * @param string|null $value If provided, sets the 'language_code'. Otherwise, returns the current value.
     * @return static|string|null The instance for chaining (setter) or the value (getter).
     */
    public function languageCode(?string $value = null): static|?string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->language_code = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->language_code;
    }
    public function language(?string $value = null): static|?string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->language_code = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->language_code;
    }
    public function lang(?string $value = null): static|?string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->language_code = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->language_code;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'is_premium' property.
     *
     * @param bool|null $value If provided, sets the 'is_premium'. Otherwise, returns the current value.
     * @return static|bool|null The instance for chaining (setter) or the value (getter).
     */
    public function isPremium(?bool $value = null): static|?bool
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->is_premium = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->is_premium;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'added_to_attachment_menu' property.
     *
     * @param bool|null $value If provided, sets the 'added_to_attachment_menu'. Otherwise, returns the current value.
     * @return static|bool|null The instance for chaining (setter) or the value (getter).
     */
    public function addedToAttachmentMenu(?bool $value = null): static|?bool
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->added_to_attachment_menu = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->added_to_attachment_menu;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'can_join_groups' property.
     *
     * @param bool|null $value If provided, sets the 'can_join_groups'. Otherwise, returns the current value.
     * @return static|bool|null The instance for chaining (setter) or the value (getter).
     */
    public function canJoinGroups(?bool $value = null): static|?bool
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->can_join_groups = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->can_join_groups;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'can_read_all_group_messages' property.
     *
     * @param bool|null $value If provided, sets the 'can_read_all_group_messages'. Otherwise, returns the current value.
     * @return static|bool|null The instance for chaining (setter) or the value (getter).
     */
    public function canReadAllGroupMessages(?bool $value = null): static|?bool
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->can_read_all_group_messages = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->can_read_all_group_messages;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'supports_guest_queries' property.
     *
     * @param bool|null $value If provided, sets the 'supports_guest_queries'. Otherwise, returns the current value.
     * @return static|bool|null The instance for chaining (setter) or the value (getter).
     */
    public function supportsGuestQueries(?bool $value = null): static|?bool
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->supports_guest_queries = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->supports_guest_queries;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'supports_inline_queries' property.
     *
     * @param bool|null $value If provided, sets the 'supports_inline_queries'. Otherwise, returns the current value.
     * @return static|bool|null The instance for chaining (setter) or the value (getter).
     */
    public function supportsInlineQueries(?bool $value = null): static|?bool
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->supports_inline_queries = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->supports_inline_queries;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'can_connect_to_business' property.
     *
     * @param bool|null $value If provided, sets the 'can_connect_to_business'. Otherwise, returns the current value.
     * @return static|bool|null The instance for chaining (setter) or the value (getter).
     */
    public function canConnectToBusiness(?bool $value = null): static|?bool
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->can_connect_to_business = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->can_connect_to_business;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'has_main_web_app' property.
     *
     * @param bool|null $value If provided, sets the 'has_main_web_app'. Otherwise, returns the current value.
     * @return static|bool|null The instance for chaining (setter) or the value (getter).
     */
    public function hasMainWebApp(?bool $value = null): static|?bool
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->has_main_web_app = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->has_main_web_app;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'has_topics_enabled' property.
     *
     * @param bool|null $value If provided, sets the 'has_topics_enabled'. Otherwise, returns the current value.
     * @return static|bool|null The instance for chaining (setter) or the value (getter).
     */
    public function hasTopicsEnabled(?bool $value = null): static|?bool
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->has_topics_enabled = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->has_topics_enabled;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'allows_users_to_create_topics' property.
     *
     * @param bool|null $value If provided, sets the 'allows_users_to_create_topics'. Otherwise, returns the current value.
     * @return static|bool|null The instance for chaining (setter) or the value (getter).
     */
    public function allowsUsersToCreateTopics(?bool $value = null): static|?bool
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->allows_users_to_create_topics = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->allows_users_to_create_topics;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'can_manage_bots' property.
     *
     * @param bool|null $value If provided, sets the 'can_manage_bots'. Otherwise, returns the current value.
     * @return static|bool|null The instance for chaining (setter) or the value (getter).
     */
    public function canManageBots(?bool $value = null): static|?bool
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->can_manage_bots = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->can_manage_bots;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'supports_join_request_queries' property.
     *
     * @param bool|null $value If provided, sets the 'supports_join_request_queries'. Otherwise, returns the current value.
     * @return static|bool|null The instance for chaining (setter) or the value (getter).
     */
    public function supportsJoinRequestQueries(?bool $value = null): static|?bool
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->supports_join_request_queries = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->supports_join_request_queries;
    }

    // End [Hi-DX] Auto-Generated Methods

    public function toArray(): array
    {
        return $this->filterEmpty([
            'id' => $this->id,
            'is_bot' => $this->is_bot,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'language_code' => $this->language_code,
            'is_premium' => $this->is_premium,
            'added_to_attachment_menu' => $this->added_to_attachment_menu,
            'can_join_groups' => $this->can_join_groups,
            'can_read_all_group_messages' => $this->can_read_all_group_messages,
            'supports_guest_queries' => $this->supports_guest_queries,
            'supports_inline_queries' => $this->supports_inline_queries,
            'can_connect_to_business' => $this->can_connect_to_business,
            'has_main_web_app' => $this->has_main_web_app,
            'has_topics_enabled' => $this->has_topics_enabled,
            'allows_users_to_create_topics' => $this->allows_users_to_create_topics,
            'can_manage_bots' => $this->can_manage_bots,
            'supports_join_request_queries' => $this->supports_join_request_queries,
        ]);
    }
}
