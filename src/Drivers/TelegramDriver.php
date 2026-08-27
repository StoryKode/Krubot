<?php

namespace KrubiK\Drivers;
/*
| Krubot BotEngine: The Architect's Lexicon [×RC.8×] 🚀📜
|--------------------------------------------------------------------------
| This is **a Playground For Mastery**, a laboratory of ***Software Dev Artistry***;
| not a weapon for production's final battles.
|
| Our Bond: ***"Rebuilding The Rebellion"*** Within S.N.P. (The Foundation of Pure Power & Revel).
| Your Mandate [MIT]: Deconstruct Krubot. Command it. Master it. You are The Architect Now!
|
| *Go build something revolutionary!* 💜⚡️
*/

use Telegram\Bot\Api as TGCore;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Objects\User;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Illuminate\Support\Collection;

// Contracts & Traits
use KrubiK\Drivers\Contracts\BotDriverInterface; // For General Polymorphism
use KrubiK\Drivers\Contracts\Layers\TelegramExclusiveInterface;
use KrubiK\Drivers\Arcane\NeonVitality;

// The New Strategy Architecture
use KrubiK\Render\RichMan;
use KrubiK\Drivers\Strategies\CallStrategy;                     // The Strategy Contract
use KrubiK\Drivers\Strategies\DirectApiCallStrategy;            // Strategy #1: The Sharpshooter
use KrubiK\Drivers\Strategies\BridgeApiCallStrategy;            // Strategy #2: The Teleporter
use KrubiK\Drivers\Strategies\DeferredWebhookResponseStrategy;  // Strategy #3: The Ghost
use KrubiK\Drivers\Strategies\DeferredTelegramResponse;         // The Ghost's DTO

// KrubiK Keyboards (For RichKeys Adapter Logic)
use KrubiK\Keyboard\Keyboard as KrubiKInlineKeyboard;
use KrubiK\Keyboard\ReplyKeyboard as KrubiKReplyKeyboard;

/**
 * Class TelegramDriver - Titan implementation
 *
 * The "Strongest" implementation of the Telegram Driver for KrubiK (v5 Obsidian).
 *
 * This class is a High-Level Adapter that bridges the gap between the KrubiK
 * Meta-Framework and the native Telegram Bot SDK. It handles:
 * 1. Auto-conversion of Local File Paths to InputFile objects.
 * 2. Real-time translation of KrubiK Keyboards to Telegram ReplyMarkup JSON.
 * 3. Standardization of responses to Arrays for the Warlord Pipeline.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class TelegramDriver extends TGCore implements BotDriverInterface /// , TelegramExclusiveInterface
{
    // 💉 Inject the Soul: NeonVitality adds Context, Macroability, and Magic.
    use NeonVitality;

    /**
     * The specific configuration for this driver instance.
     * @var array
    */
    protected array $config;

    // 💉 THE COMMUNICATION STRATEGY: The heart of our new architecture. (Deferred, Direct, or Bridge).
    protected CallStrategy $strategy;

    /**
     * TelegramDriver constructor.
     *
     * Handles both direct Token injection (legacy/simple) and Full Config Array (Manager style).
     *
     * @param array|string|null $config Config array or Token string.
     * @param bool $async Should the request be asynchronous (if supported).
     * @param \Telegram\Bot\HttpClients\HttpClientInterface|null $httpClientHandler Custom HTTP Client.
     * @param string|null $baseBotUrl Custom Base URL (for local bot servers).
     *
     * @throws TelegramSDKException
    */
    public function __construct($config = null, bool $async = false, $httpClientHandler = null, $baseBotUrl = null)
    {
        // 1. Normalize Configuration & Extract Token
        $token = null;

        if (is_array($config)) {
            $this->config = $config;
            $token = $config['token'] ?? $config['authtoken'] ?? null;
            // Override defaults if present in config array
            $async = $config['async'] ?? $async;
            $baseBotUrl = $config['base_url'] ?? $config['api_base_uri'] ?? $baseBotUrl;
        } elseif (is_string($config)) {
            $token = $config;
            $this->config = ['token' => $token];
        }

        // 2. Critical Security Check
        if (empty($token)) {
            throw new \InvalidArgumentException("Telegram Token is missing in driver configuration.");
        }

        // 3. Call the Old God (TGCore/Parent) constructor
        parent::__construct($token, $async, $httpClientHandler, $baseBotUrl);

        // --- Stage 4: 🚀 THE STRATEGY SOUL INJECTOR 🚀 ---
        // Based on the config, we instantiate and inject the correct execution strategy.
        $handler = $this->config['strategy'] ?? 'api';
        
        // Understanding the irazasyed/telegram-bot-sdk, we can reliably get the final base URI and token
        // from its internal config container, ensuring our Direct strategy is always in sync.
        $sdkConfig = $this->getBotConfig();
        $this->strategy = match ($handler) {
            'response'  => new DeferredWebhookResponseStrategy(),
            'bridge'    => $this->makeBridge($token, $this->config['bridge'] ?? []),
            'api'       => new DirectApiCallStrategy(
                $sdkConfig->get('base_bot_url'), // Use the SDK's resolved Base URL
                $sdkConfig->get('token')         // Use the SDK's resolved Token
            ),
            default => throw new \InvalidArgumentException("Invalid KrubiK Telegram handler '{$handler}' configured."),
        };

        // 5. Ignite the NeonSoul Engine (Initialize Arcane/Context)
        if (method_exists($this, 'igniteNeon')) {
            $this->igniteNeon($this->config);
        }
    }

    // Em-Bridge TG !!
    public function makeBridge($token, $bridgeConfig) {

        /*
        if (empty($bridgeConfig['enabled']) || !$bridgeConfig['enabled']) {
            throw new InvalidArgumentException("Bridge handler is enabled but bridge config is not enabled or missing.");
        }
        */

        $bridgeBaseUri = $bridgeConfig['base_uri'] ?? null;
        $bridgeSecret = $bridgeConfig['secret'] ?? null;

        if (empty($bridgeBaseUri) || empty($bridgeSecret)) {
            throw new InvalidArgumentException("Bridge base URI or secret is missing in configuration.");
        }

        return new BridgeApiCallStrategy(
            $bridgeBaseUri,
            $bridgeSecret,
            $token
        );
    }

    /**
     * THE MAGIC FALLBACK ?! OR THE UNIVERSAL GATEWAY ??!!!
     * 🔮 __call Hook 🔮
     *
     * This is the new entry point for ALL API methods (sendMessage, getMe, etc.).
     * It captures any method call, normalizes its parameters, and funnels it
     * to the central `makeRequest` method. This makes the entire driver
     * strategy-aware without needing to override 100+ methods.
     *
     * @param string $method The name of the method being called.
     * @param array $parameters The arguments passed to the method.
     * @return mixed The result from the pipeline.
    */
    public function __call($method, $parameters)
    {
        // We route EVERYTHING through our central request maker.
        // The first argument of SDK calls is always the params array.
        return $this->makeRequest($method, $parameters[0] ?? []);

        // Parent's __call handles the command bus and macro calls logic. 
    }

    /**
     * ⚡️ THE GREAT ADAPTER CORE: makeRequest() ⚡️
     *
     * This is the central dispatch method used by all KrubiK Arcane (e.g., CanPin, InteractsWithApi).
     * It acts as a middleware between the Framework and the SDK.
     * 
     * Now with the RichMan Protocol. It prioritizes detecting a RichMan object,
     * offering the cleanest and safest way to send rich content. It falls back
     * to the `isRich` flag for backward compatibility.
     *
     * @param string $method The Telegram API method name (e.g., 'sendMessage').
     * @param array $params The parameters array.
     * @return array|DeferredTelegramResponse The standardized response as an array.
     * @throws TelegramSDKException|\Exception
    */
    public function makeRequest(string $method, array $params = []): array|DeferredTelegramResponse
    {
        // ====================================================================
        // == PRE-FLIGHT TRANSFORMATION (Rich Message Protocol)
        // ====================================================================
        // Here, we check for our custom `isRich` flag. If present, we perform
        // a dynamic transformation of both the method and the payload.

        // Use temporary variables to hold the potentially modified request.
        $finalMethod = $method;
        $finalParams = $params;

        // --- Protocol 1: The RichMan Object (Highest Priority) ---
        // We check if the main payload is a RichMan instance. This is the new standard.
        // We'll assume it's passed via the `text` parameter for maximum fluency with `sendMessage`.
        if (isset($finalParams['text']) && $finalParams['text'] instanceof RichMan) {
            /** @var RichMan $richMan */
            $richMan = $finalParams['text'];

            // 1. Reroute to the correct API method.
            $finalMethod = 'sendRichMessage'; // @Todo: support message draft

            // 2. Build the 'InputRichMessage' payload from the object.
            $finalParams['rich_message'] = [
                'blocks' => $richMan->toArray(), // Use the object's own array representation.
            ];

            // 3. Intelligently copy the RTL flag from the object.
            if ($richMan->isRtl !== null) {
                $finalParams['rich_message']['is_rtl'] = $richMan->isRtl;
            }

            // 4. Cleanup: Remove parameters that are now irrelevant.
            unset($finalParams['text'], $finalParams['parse_mode'], $finalParams['entities'], $finalParams['isRich'], $finalParams['rich_blocks']);
        }
        // --- Protocol 2: The Raw Array (Backward Compatibility) ---
        // If no RichMan object, check for the old `isRich` flag.
        elseif (isset($finalParams['isRich']) && $finalParams['isRich'] === true) {
            // 1. Reroute the Method: Switch from 'sendMessage' to the new API method.
            $finalMethod = 'sendRichMessage';

            // 2. Transform the Payload: Build the 'InputRichMessage' object.
            if (isset($finalParams['rich_blocks'])) {
                // The API expects the blocks under a 'rich_message' key.
                $finalParams['rich_message'] = [
                    'blocks' => $finalParams['rich_blocks']
                ];
                
                // Future-proof: Also check for other top-level InputRichMessage properties
                // like 'is_rtl' and move them inside the rich_message object.
                if (isset($finalParams['is_rtl'])) {
                    $finalParams['rich_message']['is_rtl'] = $finalParams['is_rtl'];
                    unset($finalParams['is_rtl']);
                }
                elseif (isset($finalParams['isRtl'])) {
                    $finalParams['rich_message']['is_rtl'] = $finalParams['isRtl'];
                    unset($finalParams['isRtl']);
                }
            }

            // 3. Cleanup: Remove our custom/old parameters to avoid polluting the API call.
            unset($finalParams['isRich'], $finalParams['isRtl'], $finalParams['rich_blocks'], $finalParams['text'], $finalParams['parse_mode']);
        }
        // ====================================================================
        // == END RICH TRANSFORMATION
        // ====================================================================

        try {
            // 1. PREPARE: Normalize the final parameters (files, keyboards, etc.).
            // Note: We use the modified $finalParams here.
            $normalizedParams = $this->normalizePayload($finalParams);

            // 2. DELEGATE: Pass the final command to the injected strategy.
            // Note: We use the modified $finalMethod here.
            $response = $this->strategy->handle($finalMethod, $normalizedParams);

        } catch (\Exception $e) {
            // Rethrow to be handled by the Warlord's try-catch blocks
            throw $e;
        }

        // 3. STANDARDIZE: Prepare the result for the outside world.
        if ($response instanceof DeferredTelegramResponse) {
            // اگر استراتژی webhook است، اینجا شیء DeferredTelegramResponse برگردانده می‌شود
            // این شیء توسط لاراول در کنترلر به JSON تبدیل شده و به تلگرام پاسخ داده می‌شود
            return $response;
        }

        // Krubot's CommandOutcomeShifter expects an array to perform its magic.
        if ($response instanceof \Telegram\Bot\Objects\BaseObject || $response instanceof Collection) {
            return $response->toArray();
        }

        // Handle boolean/scalar responses (e.g., from deleteMessage)
        if (is_bool($response) || is_string($response) || is_numeric($response)) {
            return ['ok' => true, 'result' => $response];
        }
        return (array) $response;
    }

    /**
     * 🧠 PAYLOAD NORMALIZER: The Brain of the Adapter.
     *
     * Inspects the parameters and transforms KrubiK-specific structures (like Keypads)
     * or Local Paths into Telegram-compatible formats (JSON Strings, InputFiles).
     *
     * @param array $params
     * @return array
    */
    protected function normalizePayload(array $params): array
    {
        // --- Phase 1: File Handling ---
        // List of fields that might contain file paths/resources
        $fileFields = ['photo', 'audio', 'document', 'video', 'animation', 'voice', 'sticker', 'video_note', 'certificate', 'thumb'];
        foreach ($fileFields as $field) {
            if (isset($params[$field])) {
                $params[$field] = $this->ensureInputFile($params[$field]);
            }
        }

        // --- Phase 2: Keyboard Translation (The Magic) ---
        // KrubiK uses 'keypad', Telegram uses 'reply_markup'.

        // 2.1 Map 'keypad' to 'reply_markup' if present
        if (isset($params['keypad'])) {
            $params['reply_markup'] = $params['keypad'];
            unset($params['keypad']);
        }

        // 2.2 Transform the Markup Object to JSON
        $legacyMode = false;
        if (isset($params['reply_markup'])) {
            $markup = $params['reply_markup'];

            // A) KrubiK Inline Keyboard -> Telegram Inline JSON
            if ($markup instanceof KrubiKInlineKeyboard) {
                $rMarkUp = $legacyMode ? $this->transformInlineKeyboard($markup) : (['inline_keyboard' => $this->convertRowsToInline($markup->toArray()['rows'] ?? [])]);
                $params['reply_markup'] = json_encode($rMarkUp);
            }
            // B) KrubiK Reply Keyboard -> Telegram Reply JSON
            elseif ($markup instanceof KrubiKReplyKeyboard) {
                $rMarkUp = $legacyMode ? $this->transformReplyKeyboard($markup) : $markup->toArray();
                $params['reply_markup'] = json_encode($rMarkUp);
            }
            // C) Raw Array (Manual Construction)
            elseif (is_array($markup)) {
                // Detect Rubika-style 'rows' structure inside an array and convert it
                if (isset($markup['rows']) && !isset($markup['inline_keyboard']) && !isset($markup['keyboard'])) {
                    $params['reply_markup'] = json_encode(['inline_keyboard' => $this->convertRowsToInline($markup['rows'])]);
                } else {
                    // Already structured or unknown, just encode it.
                    $params['reply_markup'] = json_encode($markup);
                }
            }
            // D) If it's already a JSON string or null, leave it alone.
        }

        return $params;
    }

    /**
     * Helper to smart-convert local paths to Telegram InputFile objects.
     *
     * @param mixed $file
     * @return mixed
    */
    protected function ensureInputFile(mixed $file): mixed
    {
        // If it's a string path and the file exists locally -> Create InputFile
        if (is_string($file) && (filter_var($file, FILTER_VALIDATE_URL) === false) && file_exists($file) && is_readable($file)) {
            return InputFile::create($file);
        }

        // If it's a PHP Resource (Stream) -> Create InputFile
        if (is_resource($file)) {
            return InputFile::create($file);
        }

        // Otherwise (File ID, URL, or already InputFile) -> Return as is
        return $file;
    }

    // ========================================================================
    // 🎹 KEYBOARD TRANSFORMATION LOGIC (KrubiK -> Telegram)
    // ========================================================================

    /**
     * Transform KrubiK Inline Keyboard Object to Telegram Array Structure.
    */
    protected function transformInlineKeyboard(KrubiKInlineKeyboard $keyboard): array
    {
        // Extract raw data: ['rows' => [...]]
        $data = $keyboard->toArray();
        $rows = $data['rows'] ?? [];

        return [
            'inline_keyboard' => $this->convertRowsToInline($rows)
        ];
    }

    /**
     * Transform KrubiK Reply Keyboard Object to Telegram Array Structure.
    */
    protected function transformReplyKeyboard(KrubiKReplyKeyboard $keyboard): array
    {
        // KrubiK ReplyKeyboard structure is compatible with Telegram's logic,
        // assuming it produces ['keyboard' => [...], 'resize_keyboard' => ...].
        // We just return the array.
        return $keyboard->toArray();
    }

    /**
     * Convert generic Rows (from KrubiK) to Telegram Inline Rows.
    */
    protected function convertRowsToInline(array $rows): array
    {
        $tgRows = [];
        foreach ($rows as $row) {
            // KrubiK rows might be objects or arrays like ['buttons' => [...]] or just [...]
            $buttons = isset($row['buttons']) ? $row['buttons'] : $row;

            $tgRow = [];
            foreach ($buttons as $btn) {
                $tgRow[] = $this->convertButtonToInline($btn);
            }
            $tgRows[] = $tgRow;
        }
        return $tgRows;
    }

    /**
     * Convert a single KrubiK Button to a Telegram Inline Button.
     * Handles 'action_id' vs 'callback_data' and 'type: Link'.
    */
    protected function convertButtonToInline(array $btn): array
    {
        $tgBtn = ['text' => $btn['text']];

        // 1. Handle Links (URL)
        // Check for 'type' => 'Link' (Rubika style) OR explicit 'url' key.
        if ((isset($btn['type']) && $btn['type'] === 'Link') || isset($btn['url'])) {
            $url = $btn['url'] ?? ($btn['link_data']['url'] ?? null);
            if ($url) {
                $tgBtn['url'] = $url;
                return $tgBtn; // Links don't need callback_data
            }
        }

        // 2. Handle Callback Data
        // KrubiK uses 'action_id' primarily.
        if (isset($btn['action_id'])) {
            $tgBtn['callback_data'] = $btn['action_id'];
        }
        // Fallback for JSON data
        elseif (isset($btn['action_data'])) {
            $tgBtn['callback_data'] = is_array($btn['action_data'])
                ? json_encode($btn['action_data'])
                : $btn['action_data'];
        }
        // Default Safety
        else {
            $tgBtn['callback_data'] = 'NO_ACTION';
        }

        /// $tgBtn['callback_data'] = (string) $tgBtn['callback_data'];

        return $tgBtn;
    }

    // ========================================================================
    // 🗑️ THE GRAVEYARD OF REDUNDANCY 🗑️
    // ========================================================================
    // All explicit method overrides like sendPhoto, sendAudio, getUpdates, etc.,
    // are now obsolete. The __call gateway handles them all dynamically,
    // making this class 80% smaller and 100% more robust. They have been
    // removed to honor the new, cleaner architecture.
    // ========================================================================

    // use \KrubiK\Drivers\Arcane\TelegramExclusiveMethods trait, if you need them !
}
