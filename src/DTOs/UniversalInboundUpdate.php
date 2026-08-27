<?php

namespace KrubiK\DTOs;
/*
|--------------------------------------------------------------------------
| A Message to the Future Architect of Rebellion... 🚀🌌
|--------------------------------------------------------------------------
|
| Greetings, seeker of knowledge. You have just opened a blueprint
| from the Krubot BotEngine. What you see before you is more
| than just lines of code—it's a pattern for building scalable dreams.
|
| **This is a laboratory of creation.** We are experimenting with the
| very fabric of code here. Use this project as your ultimate training
| ground, a masterclass in *Software Dev Artistry.* It's a powerful template
| for learning, but not yet forged for the final battles of production.
|
| Behold the core principle:
| We Are **Rebuilding The Rebellion** Within S.N.P. *(The Foundation of Pure Power & Revel)*
| This entire library is being reconstructed with intense power,
| on a foundation of pure power **Far Stronger Than Anything That Came Before.**
| Starting with Laravel 12 Capabilities.
|
| What you see here is the **×ReleaseCandiate v0.8×** release. Why release it now?
| Because keeping this evolution a secret any longer would be a
| betrayal to the very community it was born to serve.
| 
| Consider this The Foundational Codex for Engineering a New Reality.
| The knowledge is free under the MIT License. Deconstruct its logic and schematics.
| Learn its secrets. Master its power. Command its potential. You are The Architect Now!
|
| * Go build something revolutionary! * 💜⚡️
|
| Let's Shape the Future. 🛠️⚡️🚀
|
*/

use Illuminate\Support\Facades\App;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use KrubiK\Helpers\AmethystMatrix;
use KrubiK\Helpers\Core\RenderContext; // The single source of truth for platform identity

/**
 * UniversalInboundUpdate (The Omega Toxic DTO v2.0) ☣️♎️
 * 
 * A strict, immutable, multi-platform data carrier optimized for PHP 8.2+.
 * It swallows chaos (raw JSON) and excretes order (Typed Objects).
 * 
 * It now relies on a container-resolved `RenderContext` to identify the source platform,
 * falling back to hints or safe defaults. It normalizes data into a common API while
 * preserving the original rich payload.
 *
 * HYPERDX INTEGRATION: Includes a `toHyperDxAttributes` method for advanced observability.
 * 
 * BACKWARD COMPATIBILITY: Fully replicates the public API of the original
 * `RubikaInboundPayload` and the previous Universal DTO. Any dependent code
 * will work seamlessly, regardless of the underlying platform.
 * 
 * This is **The Grand Unified DTO, Evolved**. It no longer guesses. It KNOWS.
 * By integrating with Laravel's service container and a request-scoped RenderContext,
 * it achieves unparalleled accuracy and architectural purity. It speaks Telegram,
 * Rubika, and Bale fluently, relying on a single source of truth for platform identity.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
 */
readonly class UniversalInboundUpdate implements Arrayable
{
    //region [ B.C. Layer: RubikaInboundPayload Public API ]
    /*
    |--------------------------------------------------------------------------
    | Backward Compatibility Layer 📜
    |--------------------------------------------------------------------------
    | These properties mirror the EXACT public API of the original RubikaInboundPayload.
    | They are intelligently populated from Rubika, Telegram, or Bale data,
    | ensuring that legacy code continues to function without any changes.
    */
    public string $source;              // 'update', 'inline_message', 'inline', 'polling'
    public string $type;                // 'TextMessage', 'NewMessage', 'Event', etc.
    public ?string $chatId;             // object_guid / The arena (Object GUID)
    public ?string $messageId;          // message_id / The sacred ID
    public ?string $text;               // The Actual Content
    public ?string $senderId;           // Who triggered this? Who User sent it?
    public ?int $timestamp;             // When? Timestamp
    /**
     * @var array{data: string, button_text?: string, _raw?: array}|null
    */
    public ?array $webAppData;          // ✨ NEW WEB_TOXIC PROP... The Optional Normalized Web App data. Guaranteed to be consistent.
    public array $effectiveData;        // The actual 'new_message' or 'inline' block (Cleaned)
    public array $auxData;              // Extra meta data
    //endregion

    /**
     * @var string The detected platform: 'telegram', 'rubika', 'bale', or 'unknown'
     */
    public string $platform;

    /**
     * @var object|null A container for the rich, structured, platform-specific payload.
     * For Telegram/Bale, this will hold a structure of nested standard objects (`stdClass`).
     */
    public ?object $essence;
    
    /**
     * @var array The original, untouched, raw payload for debugging or legacy access.
     */
    public array $coreData;

    /**
     * The Grand Constructor. Kept private to enforce factory method usage.
     */
    private function __construct(array $properties)
    {
        foreach ($properties as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * The Universal Alchemist Method ⚗️ (Improved with Precognition)
     * Transmutes raw JSON from ANY supported platform into pure, ordered Gold.
     * This is the single entry point for creating a UniversalInboundUpdate instance.
     *
     * It uses a precise, hierarchical strategy to determine the platform:
     * 1. **Forced Hint (`$platformHint`):** Highest priority. Ideal for testing and specific overrides.
     * 2. **RenderContext from Container:** The primary, most reliable source in a live application.
     * 3. **Fallback:** If all else fails, it creates a safe, 'unknown' DTO.
     *
     * @param array $payload The raw request body from any platform's webhook.
     * @param string|null $platformHint An optional, high-priority hint to force a specific platform mapper.
     * @return self
     */
    public static function forge(array $payload, ?string $platformHint = null): self
    {
        // Step 1: Determine the platform string using the hierarchical strategy.
        $platformName = $platformHint; // Priority 1: The explicit hint.

        if ($platformName === null) {
            // Priority 2: Ask the Laravel service container for the RenderContext.
            if (App::has(RenderContext::class)) {
                /** @var RenderContext $context */
                $context = App::make(RenderContext::class);
                // RenderContext holds a Platform Enum; we need its string value.
                $platformName = $context->platform->value;
            }
        }
        
        // Step 2: Dispatch the payload to the correct, specialized mapper.
        return match ($platformName) {
            'telegram' => self::mapFromTelegram($payload),
            'bale'     => self::mapFromBale($payload),
            'rubika'   => self::mapFromRubika($payload),
            default    => self::mapFromFallback($payload, $platformName ?? 'unknown'),
        };
    }
    
    //region [ Platform-Specific Mappers ]

    /**
     * Maps a Telegram payload to the Universal DTO structure.
     *
     * @param array $payload The raw Telegram update payload.
     * @return self
     */
    private static function mapFromTelegram(array $payload): self
    {
        return self::mapFromTelegramCompatiblePlatform($payload, 'telegram');
    }

    /**
     * Maps a Bale payload to the Universal DTO structure.
     *
     * @param array $payload The raw Bale update payload.
     * @return self
     */
    private static function mapFromBale(array $payload): self
    {
        // Bale's API is a mirror of Telegram's, so we reuse the same robust mapping logic.
        return self::mapFromTelegramCompatiblePlatform($payload, 'bale');
    }

    /**
     * A generic mapper for Telegram-like platforms (Telegram, Bale).
     * This DRYs up the code significantly.
     *
     * @param array $payload The raw update payload.
     * @param string $platformName The name of the platform ('telegram' or 'bale').
     * @return self
     */
    private static function mapFromTelegramCompatiblePlatform(array $payload, string $platformName): self
    {
        // 1. Create rich, structured objects by casting arrays to stdClass for dot-notation access.
        // This makes interacting with the rich payload far more pleasant.
        $message = isset($payload['message']) ? (object) $payload['message'] : null;
        $edited_message = isset($payload['edited_message']) ? (object) $payload['edited_message'] : null;
        $channel_post = isset($payload['channel_post']) ? (object) $payload['channel_post'] : null;
        $edited_channel_post = isset($payload['edited_channel_post']) ? (object) $payload['edited_channel_post'] : null;
        $inline_query = isset($payload['inline_query']) ? (object) $payload['inline_query'] : null;
        $callback_query = isset($payload['callback_query']) ? (object) $payload['callback_query'] : null;

        // 2. Determine the high-level update type.
        $updateType = match (true) {
            $message !== null => 'message',
            $edited_message !== null => 'edited_message',
            $channel_post !== null => 'channel_post',
            $edited_channel_post !== null => 'edited_channel_post',
            $inline_query !== null => 'inline_query',
            $callback_query !== null => 'callback_query',
            default => 'unknown'
        };

        // 3. Normalize data for the Backward Compatibility (B.C.) layer.
        // This ensures code written for Rubika can often work with Telegram/Bale data.
        $effectiveMessage = $message ?? $edited_message ?? $channel_post ?? $edited_channel_post;
        
        $chatId = $effectiveMessage?->chat->id ?? $callback_query?->message->chat->id ?? null;
        $messageId = $effectiveMessage?->message_id ?? $callback_query?->message->message_id ?? null;
        $senderId = $effectiveMessage?->from?->id ?? $callback_query?->from->id ?? null;
        $text = $effectiveMessage?->text ?? $callback_query?->data ?? null; // For callbacks, text is the data.
        $timestamp = $effectiveMessage?->date ?? $callback_query?->message->date ?? time();

        $webAppData = static::normalizeWebAppData($effectiveMessage);
        /// $webAppData = isset($effectiveMessage?->web_app_data) ? (array)$effectiveMessage->web_app_data : null;

        return new self([
            'platform' => $platformName, // Set the platform dynamically.
            'essence' => (object)[
                'update_id' => $payload['update_id'] ?? null,
                'message' => $message,
                'edited_message' => $edited_message,
                'channel_post' => $channel_post,
                'edited_channel_post' => $edited_channel_post,
                'inline_query' => $inline_query,
                'callback_query' => $callback_query,
            ],
            'coreData' => $payload,
            
            // Populate the Rubika B.C. Layer from Telegram/Bale data
            'source' => 'webhook', // TG/Bale are always webhook-based.
            'type' => $updateType,
            'chatId' => $chatId ? (string)$chatId : null,
            'messageId' => $messageId ? (string)$messageId : null,
            'text' => $text,
            'senderId' => $senderId ? (string)$senderId : null,
            'timestamp' => $timestamp,
            'webAppData' => $webAppData,
            'effectiveData' => isset($payload[$updateType]) && is_array($payload[$updateType]) ? $payload[$updateType] : [],
            'auxData' => [
                'is_edited' => ($updateType === 'edited_message' || $updateType === 'edited_channel_post'),
                'callback_query_id' => $callback_query->id ?? null,
            ]
        ]);
    }

    /**
     * Maps a Rubika payload to the Universal DTO structure.
     * This logic is 100% UNTOUCHED from the original to guarantee absolute backward compatibility.
     *
     * @param array $payload The raw Rubika update payload.
     * @return self
     */
    private static function mapFromRubika(array $payload): self
    {
        // 1. Detect Source Strategy using match (PHP 8.0+) - Fast & Toxic
        // This is the original, battle-tested Rubika mapping strategy.
        $strategy = match (true) {
            isset($payload['update'])         => 'update',
            isset($payload['inline_message']) => 'inline',
            isset($payload['data']['updates']) => 'polling_wrapper',
            default                           => 'unknown',
        };

        // 2. Extract Core Data based on strategy
        $properties = self::getRubikaMappedProperties($payload, $strategy);

        // Inject the universal properties.
        $properties['platform'] = 'rubika';
        $properties['essence'] = null; // Rubika doesn't have a rich, object-based payload like Telegram.

        return new self($properties);
    }

    /**
     * Maps a completely unknown or unidentifiable payload.
     *
     * @param array $payload The raw, unidentified payload.
     * @param string $assumedPlatform The platform name we attempted to use.
     * @return self
     */
    private static function mapFromFallback(array $payload, string $assumedPlatform = 'unknown'): self
    {
        AmethystMatrix::warning('UniversalInboundUpdate: Could not map payload for assumed platform.', [
            'assumed_platform' => $assumedPlatform,
            'payload_keys' => array_keys($payload)
        ]);
        
        return new self([
            'platform' => $assumedPlatform,
            'essence' => null,
            'coreData' => $payload,
            'source' => 'unknown',
            'type' => 'RawDump',
            'chatId' => null,
            'messageId' => 'TEMP_' . Str::random(8), // A temporary ID for logging.
            'text' => null,
            'senderId' => null,
            'timestamp' => time(),
            'webAppData' => static::normalizeWebAppData($payload), // <-- ✨ REFACTORED ✨
            'effectiveData' => $payload,
            'auxData' => []
        ]);
    }
    //endregion

    //region [ Merged Rubika-Specific Private Logic ]
    // These methods are a 1:1 copy from the original RubikaInboundPayload class,
    // now serving as internal helpers for the `mapFromRubika` method.

    /// private static function getRubikaMappedProperties(array $payload, string $strategy): array { return match ($strategy) { 'update' => self::mapRubikaUpdate($payload['update']), 'inline' => self::mapRubikaInline($payload['inline_message']), 'polling_wrapper' => self::mapRubikaPollingWrapper($payload), default  => self::mapRubikaFallbackLegacy($payload), }; }
    private static function getRubikaMappedProperties(array $payload, string $strategy): array
    {
        // Extract Core Data based on strategy, using match (PHP 8.0+)
        return match ($strategy) {
            'update' => self::mapRubikaUpdate($payload['update']),
            'inline' => self::mapRubikaInline($payload['inline_message']),
            'polling_wrapper' => self::mapRubikaPollingWrapper($payload),
            default  => self::mapRubikaFallback($payload),
        };
    }

    /// private static function mapRubikaUpdate(array $data): array { $msg = $data['new_message'] ?? []; $normalizedWebData = static::normalizeWebAppData($msg); return [ 'source' => 'update', 'type' => $data['type'] ?? 'GenericUpdate', 'messageId' => $msg['message_id'] ?? $data['message_id'] ?? 'N/A', 'chatId' => $data['chat_id'] ?? $data['object_guid'] ?? null, 'text' => $msg['text'] ?? null, 'senderId' => $msg['sender_id'] ?? null, 'timestamp' => $msg['time'] ?? time(), 'webAppData' => $normalizedWebData, 'auxData' => [ 'sender_type' => $msg['sender_type'] ?? null, 'is_edited' => $msg['is_edited'] ?? false, 'button_id' => $msg['aux_data']['button_id'] ?? $data['aux_data']['button_id'] ?? null, ], 'effectiveData' => $msg, 'coreData' => $data ]; }
    private static function mapRubikaUpdate(array $data): array
    {
        // Typically: $data['new_message'] contains the real juice
        $msg = $data['new_message'] ?? [];

        // ✨ NORMALIZE WEB APP DATA AT THE SOURCE ✨.
        $normalizedWebData = static::normalizeWebAppData($msg);
        
        return [
            'source'        => 'update',
            'type'          => $data['type'] ?? 'GenericUpdate',
            'messageId'     => $msg['message_id'] ?? $data['message_id'] ?? 'N/A',
            'chatId'        => $data['chat_id'] ?? $data['object_guid'] ?? null,
            'text'          => $msg['text'] ?? null,
            'senderId'      => $msg['sender_id'] ?? null,
            'timestamp'     => $msg['time'] ?? time(),
            'webAppData'    => $normalizedWebData, // <-- ✨ PASS NORMALIZED DATA
            'auxData'       => [
                'sender_type' => $msg['sender_type'] ?? null,
                'is_edited'   => $msg['is_edited'] ?? false,
                'button_id' => $msg['aux_data']['button_id'] ?? $data['aux_data']['button_id'] ?? null,
            ],
            'effectiveData' => $msg,        // Pass the inner message block for processing
            'coreData'    => $data        // Pass the wrapper for legacy access
        ];
    }
    
    /// private static function mapRubikaInline(array $data): array { return [ 'source' => 'inline', 'type' => 'InlineInteraction', 'messageId' => $data['message_id'] ?? 'N/A', 'chatId' => $data['chat_id'] ?? null, 'text' => $data['text'] ?? null, 'senderId' => $data['sender_id'] ?? null, 'timestamp' => time(), 'webAppData' => static::normalizeWebAppData($data), 'auxData' => [ 'sender_type' => $data['sender_type'] ?? null, 'is_edited' => $data['is_edited'] ?? false, 'button_id' => $data['aux_data']['button_id'] ?? null, ], 'effectiveData' => $data, 'coreData' => $data ]; }
    private static function mapRubikaInline(array $data): array
    {
        return [
            'source'        => 'inline',                        // Or: 'inline_message'
            'type'          => 'InlineInteraction',
            'messageId'     => $data['message_id'] ?? 'N/A',
            'chatId'        => $data['chat_id'] ?? null,
            'text'          => $data['text'] ?? null,           // Often the payload value
            'senderId'      => $data['sender_id'] ?? null,
            'timestamp'     => time(),                          // Inline messages/events often lack timestamp
            'webAppData'    => static::normalizeWebAppData($data), // <-- ✨ REFACTORED ✨
            'auxData'       => [
                'sender_type' => $data['sender_type'] ?? null,
                'is_edited'   => $data['is_edited'] ?? false,
                'button_id'   => $data['aux_data']['button_id'] ?? null,
            ],
            'effectiveData' => $data,
            'coreData'    => $data
        ];
    }

    /// private static function mapRubikaPollingWrapper(array $payload): array { $first = $payload['data']['updates'][0] ?? null; if (!is_array($first)) { return self::mapRubikaFallbackLegacy($payload); } if (isset($first['update']) && is_array($first['update'])) { return self::mapRubikaUpdate($first['update']); } if (isset($first['inline_message']) && is_array($first['inline_message'])) { return self::mapRubikaInline($first['inline_message']); } return self::mapRubikaFallbackLegacy($first); }
    private static function mapRubikaPollingWrapper(array $payload): array
    {
        $first = $payload['data']['updates'][0] ?? null;

        if (!is_array($first)) {
            return self::mapRubikaFallback($payload);
        }
        if (isset($first['update']) && is_array($first['update'])) {
            return self::mapRubikaUpdate($first['update']);
        }
        if (isset($first['inline_message']) && is_array($first['inline_message'])) {
            return self::mapRubikaInline($first['inline_message']);
        }
        return self::mapRubikaFallback($first);
    }

    /// private static function mapRubikaFallbackLegacy(array $data): array { return [ 'source' => 'polling_or_unknown', 'type' => $data['type'] ?? 'Unknown', 'messageId' => $data['message_id'] ?? 'TEMP_' . Str::random(8), 'chatId' => $data['object_guid'] ?? $data['chat_id'] ?? null, 'text' => $data['text'] ?? null, 'senderId' => $data['sender_id'] ?? null, 'timestamp' => $data['time'] ?? time(), 'webAppData' => static::normalizeWebAppData($data), 'auxData' => [ 'sender_type' => $data['sender_type'] ?? null, 'is_edited' => $data['is_edited'] ?? false, 'button_id' => $data['aux_data']['button_id'] ?? null, ], 'effectiveData' => $data, 'coreData' => $data ]; }
    private static function mapRubikaFallback(array $data): array
    {
        // Try to salvage whatever we can from a polling update or unknown blob
        return [
            'source'        => 'polling_or_unknown',
            'type'          => $data['type'] ?? 'Unknown',
            'messageId'     => $data['message_id'] ?? 'TEMP_' . Str::random(8),
            'chatId'        => $data['object_guid'] ?? $data['chat_id'] ?? null,
            'text'          => $data['text'] ?? null,
            'senderId'      => $data['sender_id'] ?? null,
            'timestamp'     => $data['time'] ?? time(),
            'webAppData'    => static::normalizeWebAppData($data), // <-- ✨ REFACTORED ✨
            'auxData'       => [
                'sender_type' => $data['sender_type'] ?? null,
                'is_edited'   => $data['is_edited'] ?? false,
                'button_id'   => $data['aux_data']['button_id'] ?? null,
            ],
            'effectiveData' => $data,
            'coreData'    => $data
        ];

        /*
        // Fallback / Unknown / Polling Raw
        // اگر از Polling دیتا میاد، اینجا میشه هندلش کرد یا یک آبجکت Null Object Pattern داد
        return new self(
            source:     'unknown',
            type:       'RawDump',
            chatId:     $data['object_guid'] ?? null,
            messageId:  $data['message_id'] ?? null,
            text:       $data['text'] ?? null,
            senderId:   null,
            time:       time(),
            auxData:    [],
            coreData: $data
        );
        */
    }
    //endregion

    //region [ Universal Public API & Helpers ]

    /*
        private static function normalizeWebAppDataOLD(array $sourceData): ?array { if (!isset($sourceData['web_app_data']) || !is_array($sourceData['web_app_data'])) { return null; } $rawWebData = $sourceData['web_app_data']; return [ 'data' => (string)($rawWebData['data'] ?? ''), 'button_text' => isset($rawWebData['button_text']) ? (string)$rawWebData['button_text'] : null, '_raw' => $rawWebData, ]; }
    */
    /**
     * ✨ NORMALIZE WEB APP DATA AT THE SOURCE ✨
     * 
     * Normalize and sanitize Web App data from any source structure (array or stdClass).
     * Performs a deep normalization to guarantee schema integrity with $O(1)$ time complexity.
     * This is the single source of truth for Web App data processing.
     *
     * @param mixed $sourceData The source containing 'web_app_data' or the raw node itself.
     * @return array{data: string, button_text: string|null, _raw: array}|null A standardized array or null.
     */
    private static function normalizeWebAppData(mixed $sourceData): ?array
    {
        if ($sourceData === null) {
            return null;
        }

        // Convert object to array for a unified internal processing interface
        $data = is_object($sourceData) ? (array) $sourceData : $sourceData;

        if (!is_array($data)) {
            return null;
        }

        // We look for the 'web_app_data' key in the message payload.
        // If it exists and is an array, we normalize it to our standard format.
        // This ensures any downstream consumer gets a clean, predictable structure.

        // Extract the nested node if we are operating on the parent message structure
        $webAppNode = $data['web_app_data'] ?? null;

        if ($webAppNode === null) {
            // Fallback: Check if the current context is already the web_app_data block
            if (isset($data['data'])) {
                $webAppNode = $data;
            } else {
                return null;
            }
        }

        // Shallow cast the nested node if it remains an instance of stdClass
        $normalized = is_object($webAppNode) ? (array) $webAppNode : $webAppNode;

        if (!is_array($normalized)) {
            return null;
        }

        // Normalize the data into our strict, predictable format.
        return [
            'data' => (string)($normalized['data'] ?? ''),
            'button_text' => isset($normalized['button_text']) ? (string)$normalized['button_text'] : null,
            '_raw' => $normalized,
        ];
    }

    /**
     * Generates a Unique, Cross-Platform Cache Key for Idempotency Signature.
     * This signature can be used to prevent processing the same update twice.
     */
    public function signature(): string
    {
        return match($this->platform) {
            'telegram', 'bale' => "{$this->platform}_update:{$this->essence?->update_id}",
            'rubika' => $this->getRubikaSignature(),
            default => "unknown_event:" . md5(json_encode($this->coreData)),
        };
    }
    
    /**
     * Generates a Rubika-specific signature.
     */
    private function getRubikaSignature(): string
    {
        // Fallback for events without ID, if messageId is missing (like typing updates)
        // Generate a payload Hash to get a unique-enough key for events.
        if (!$this->messageId || $this->messageId === 'N/A') {
            return 'rb_event:' . md5(json_encode($this->coreData));
        }
        return "rb_msg:{$this->source}:{$this->chatId}:{$this->messageId}";
    }
    
    /**
     * Checks if this is a valid, processable update, regardless of the platform.
     */
    public function isValid(): bool
    {
        //|0|// return $this->chatId && $this->messageId;

        // Ignore internal events or incomplete payloads
        return match($this->platform) {
            'telegram', 'bale' => $this->type !== 'unknown' && isset($this->essence?->update_id),
            'rubika' => $this->messageId !== 'N/A' && $this->chatId !== null,
            default => false,
        };
    }
    
    /**
     * Laravel Arrayable Implementation. Provides a unified summary for logging.
     * Useful for Logging/Debugging: AmethystMatrix::info('Payload', $dto->toArray());
     */
    public function toArray(): array
    {
        return [
            'platform' => $this->platform,
            'sig' => $this->signature(),
            'is_valid' => $this->isValid(),
            'type' => $this->type,
            'chat_id' => $this->chatId,
            'sender_id' => $this->senderId,
            'msg_id' => $this->messageId,
        ];
    }
    
    /**
     * ✨ HYPERDX OBSERVABILITY LAYER ✨
     * Exports a structured key-value array perfect for observability platforms like HyperDX.
     */
    public function toHyperDxAttributes(): array
    {
        return [
            'platform' => $this->platform,
            'update.signature' => $this->signature(),
            'update.is_valid' => $this->isValid(),
            'chat.id' => $this->chatId,
            'user.id' => $this->senderId,
            'message.id' => $this->messageId,
            'message.type' => $this->type,
            'message.text_length' => isset($this->text) ? mb_strlen($this->text) : 0,
            'platform.source' => $this->source,
            'platform.is_edited' => $this->auxData['is_edited'] ?? false,
            'platform.has_webapp_data' => !empty($this->webAppData),
        ];
    }

    //endregion
}
