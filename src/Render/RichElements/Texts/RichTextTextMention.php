<?php
namespace KrubiK\Render\RichElements\Texts;
use KrubiK\Render\RichElements\RichEntity;

use KrubiK\Render\DTOs\User;

class RichTextTextMention extends RichTextEntity
{
    /**
     * @param RichEntity|string|array $text The visible text of the mention.
     * @param User|array $user The user object.
     */
    public function __construct(public RichEntity|string|array $text, public User|array $user) {
        // [Defensive Programming] Ensure the user property is not empty on creation.
        if (empty($user)) {
            throw new InvalidArgumentException('The User object or array cannot be empty for a TextMention.');
        }
    }

    /**
     * Static factory for a RichTextTextMention instance (inline user mention).
     *
     * @param RichEntity|callable|string|array $text The visible text of the mention.
     * @param UserDTO|array $user The UserDTO or array of the user being mentioned.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text, UserDTO|array $user): self
    {
        // Resolve the display text, as it might be a closure.
        return new self(self::resolveContent($text), $user);
    }

    /**
     * Converts the object to its array representation for the Telegram API.
     * Intelligently handles the 'user' property, converting User objects to arrays.
     *
     * @return array The array representation of this object.
     */
    public function toArray(): array
    {
        return [
            'type' => 'text_mention',
            'text' => $this->normalize($this->text),
            // The normalize method will automatically call toArray() on the User object
            // if it's an object, or just return the array if it's already an array.
            // This is the peak of performance and elegance.
            'user' => $this->normalize($this->user),
        ];
    }

    public function toHtml(): string
    {
        // Creates a tg:// link to mention a user by their ID.
        return '<a href="tg://user?id=' . $this->user->id . '">' . $this->renderHtml($this->text) . '</a>';
    }

    public function toMd()
    {
        $userId =  ($this->user instanceof User) ? $this->user->id() : $this->user['id'];
        return '[' . $this->renderText($this->text) . '](tg://user?id=' . $userId . ')';
    }
}
