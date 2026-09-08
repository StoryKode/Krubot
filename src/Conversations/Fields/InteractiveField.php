<?php

declare(strict_types=1);

namespace KrubiK\Conversations\Fields;

use KrubiK\Krubot;
use KrubiK\Keyboard\Keyboard;
use KrubiK\Conversations\Answer;
use Stringable;

interface InteractiveField extends Stringable
{
    public function getKey(): string;

    public function setKey(string $key): static;

    public function rules(mixed ...$rules): static;

    public function errorMessage(string $message): static;

    public function toKeyboard(): ?Keyboard;

    public function process(Answer $answer, Krubot $bot): mixed;

    public function isComplete(): bool;

    public function isInvalid(): bool;
}
