<?php

declare(strict_types=1);

namespace KrubiK\Conversations\Fields;

use Closure;
use Illuminate\Support\Facades\Validator;
use KrubiK\Conversations\Answer;
use KrubiK\Conversations\Fields\InteractiveField;
use KrubiK\Keyboard\Keyboard;
use KrubiK\Keyboard\PowerButton;
use KrubiK\Krubot;
use Laravel\SerializableClosure\SerializableClosure;
use Stringable;
use Throwable;

abstract class HyperField implements InteractiveField, Stringable
{
    protected string $key;
    protected string $prompt;

    /** @var array<int, mixed> */
    protected array $rules = [];

    protected string $errorMessage = '❌ پاسخ نامعتبر است. لطفاً دوباره وارد کنید.';

    protected bool $retrying = false;
    protected bool $complete = false;

    /** idle|pending|invalid|complete */
    protected string $status = 'idle';

    protected ?string $lastError = null;
    protected ?string $promptMessageId = null;

    /**
     * Extra state intentionally lives inside the field so the whole field
     * remains serializable as a part of the parent Conversation/Form.
    */
    protected array $meta = [];

    public function __construct(
        string $key,
        string $prompt,
        mixed $rules = null,
        string $errorMessage = '❌ پاسخ نامعتبر است. لطفاً دوباره وارد کنید.',
    ) {
        $this->key = $key;
        $this->prompt = $prompt;
        $this->errorMessage = $errorMessage;

        if ($rules !== null) {
            $this->rules($rules);
        }
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;
        return $this;
    }

    public function prompt(string $prompt): static
    {
        $this->prompt = $prompt;
        return $this;
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function errorMessage(string $message): static
    {
        $this->errorMessage = $message;
        return $this;
    }

    public function rules(mixed ...$rules): static
    {
        $rules = count($rules) === 1 && is_array($rules[0])
            ? $rules[0]
            : $rules;

        foreach ($rules as $rule) {
            $this->rules[] = $rule instanceof Closure
                ? new SerializableClosure($rule)
                : $rule;
        }

        return $this;
    }

    public function validate(mixed $rule): static
    {
        return $this->rules($rule);
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function setPromptMessageId(string|int|null $messageId): static
    {
        $this->promptMessageId = $messageId === null ? null : (string) $messageId;
        return $this;
    }

    public function getPromptMessageId(): ?string
    {
        return $this->promptMessageId;
    }

    public function meta(string|array $key, mixed $value = null): mixed
    {
        if (is_array($key)) {
            $this->meta = array_replace($this->meta, $key);
            return $this;
        }

        if (func_num_args() === 1) {
            return $this->meta[$key] ?? null;
        }

        $this->meta[$key] = $value;
        return $this;
    }

    public function retrying(): bool
    {
        return $this->retrying;
    }

    public function isComplete(): bool
    {
        return $this->complete;
    }

    public function isInvalid(): bool
    {
        return $this->status === 'invalid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function lastError(): ?string
    {
        return $this->lastError;
    }

    public function reset(): static
    {
        $this->retrying = false;
        $this->complete = false;
        $this->status = 'idle';
        $this->lastError = null;
        $this->promptMessageId = null;
        return $this;
    }

    public function retry(): static
    {
        $this->retrying = true;
        return $this;
    }

    public function __toString(): string
    {
        if (!$this->retrying) {
            return $this->prompt;
        }

        $message = $this->lastError ?: $this->errorMessage;

        return "⚠️ {$message}\n\n{$this->prompt}";
    }

    public function getText(): string
    {
        return (string) $this;
    }

    /**
     * Default for text-like fields. Interactive descendants should override this.
    */
    public function toKeyboard(): ?Keyboard
    {
        return null;
    }

    /**
     * Core field lifecycle. A null result means "not completed yet".
     * Concrete fields only implement response decoding; validation and
     * lifecycle bookkeeping stay centralized here.
    */
    final public function process(Answer $answer, Krubot $bot): mixed
    {
        $this->status = 'pending';
        $this->lastError = null;

        try {
            $value = $this->processResponse($answer, $bot);
        } catch (Throwable $e) {
            $this->status = 'invalid';
            $this->retrying = true;
            $this->lastError = $e->getMessage() ?: $this->errorMessage;
            return null;
        }

        if ($value === null) {
            return null;
        }

        if (!$this->passesValidation($value)) {
            $this->status = 'invalid';
            $this->retrying = true;
            return null;
        }

        $this->complete = true;
        $this->retrying = false;
        $this->status = 'complete';

        return $value;
    }

    abstract protected function processResponse(Answer $answer, Krubot $bot): mixed;

    /**
     * Mark the field invalid and make the next Form retry render its latest error.
    */
    protected function fail(string $message): mixed
    {
        $this->status = 'invalid';
        $this->retrying = true;
        $this->complete = false;
        $this->lastError = $message;

        return null;
    }

    protected function passesValidation(mixed $value): bool
    {
        if (!$this->rules) {
            return true;
        }

        $executable = [];
        foreach ($this->rules as $rule) {
            $executable[] = $rule instanceof SerializableClosure
                ? $rule->getClosure()
                : $rule;
        }

        $validator = Validator::make(
            ['value' => $value],
            ['value' => $executable],
            [],
            ['value' => $this->key]
        );

        if ($validator->fails()) {
            $this->lastError = $validator->errors()->first();
            return false;
        }

        return true;
    }

    /**
     * Build a structured action that is consumed by Form::processFieldAnswer().
     * The PowerButton action compiler handles the serialization.
    */
    protected function button(
        string|Stringable $text,
        string $event,
        mixed $value = null,
        float $width = 1.0,
    ): PowerButton {
        $payload = [
            'field' => $this->key,
            'event' => $event,
        ];

        if (func_num_args() >= 3) {
            $payload['value'] = $value;
        }

        return PowerButton::make((string) $text)
            ->width($width)
            ->action('processFieldAnswer', $payload);
    }

    protected function keyboard(array $buttons, int $columns = 1): Keyboard
    {
        return Keyboard::make()
            ->buttons($buttons)
            ->chunk(max(1, $columns));
    }

    protected function displayText(mixed $value): string
    {
        if ($value instanceof Stringable) {
            return (string) $value;
        }

        if (is_scalar($value) || $value === null) {
            return (string) $value;
        }

        return (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function editPromptText(Krubot $bot, string $text): void
    {
        if (!$this->promptMessageId) {
            return;
        }

        try {
            $bot->chat($bot->chatId())
                ->messageId($this->promptMessageId)
                ->message($text)
                ->editMessage();
        } catch (Throwable) {
            // Best-effort UX operation.
        }
    }

    protected function editPromptKeyboard(Krubot $bot, ?Keyboard $keyboard): void
    {
        if (!$this->promptMessageId || !$keyboard) {
            return;
        }

        try {
            $data = $keyboard->toArray();
            $bot->chat($bot->chatId())
                ->messageId($this->promptMessageId)
                ->inlineKeypad($data['rows'] ?? $data)
                ->editKeyboard();
        } catch (Throwable) {
            // Best-effort UX operation.
        }
    }
}
