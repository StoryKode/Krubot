<?php

namespace KrubiK\Conversations;
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

use KrubiK\Krubot;
use KrubiK\Keyboard\Keyboard;
use KrubiK\Conversations\Attributes\Rule as AttributeRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use KrubiK\Helpers\AmethystMatrix as Log;
use Illuminate\Support\Facades\Validator;
use Laravel\SerializableClosure\SerializableClosure;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionException;
use Closure;
use Stringable;
use Throwable;

/**
 * THE ULTIMATE CONVERSATION CLASS v9.1 (MASTER MERGED VERSION).
 *
 * This class is the definitive amalgamation of files A, B, C, and X.
 * It strictly follows PHP 8.2 standards while maintaining 100% backward compatibility and hyper perfromances.
 *
 * INTEGRATED FEATURES:
 * 1. Hybrid Validation System:
 *    - Closure Pre-flight checks (Files A/B/C).
 *    - PHP 8 Attributes on Methods (Files A/B/C/X).
 *    - PHP 8 Attributes on Classes (File B specific feature).
 *    - Manual Laravel Rules (Files A/B/C).
 *
 * 2. Advanced Action Engine:
 *    - JSON Payloads & 'callback_query' wrappers (Files A/C).
 *    - Custom Regex 'action:name|key=val' (Files A/C/X).
 *    - Query String Parsing (Files A/B/C).
 *    - Return type normalization (Collection/Array support).
 *
 * 3. Intelligent Flow Control:
 *    - Loose typing on API methods for compatibility (File B/C approach).
 *    - Strict typing on internal logic for performance (File A approach).
 *    - Cascading Auto-Answer Logic (Property -> Config -> Default,  File X approach).
 *
 * 4. Omni-Keyboard Support:
 *    - Supports `toKrubotKeyboard` (Legacy File A).
 *    - Supports `getButtons` (Legacy File 1/B).
 *    - Supports `toKeyboard` (Modern Standard).
 *    - Supports Raw Arrays & Builder Closures.
 *
 * @property-read Collection $data Access the persistent data store.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
abstract class Conversation
{
    use ManageConversations;

    // =========================================================================
    //  CORE STATE PROPERTIES (Serialized)
    // =========================================================================

    /**
     * Name of the current step method or a serialized closure.
     * @var string|SerializableClosure
     */
    protected string|SerializableClosure $step = 'start';

    /**
     * Unique cache key for this user in this chat.
     */
    protected ?string $key = null;

    /**
     * Conversation expiration time in seconds (Default: 1 hour).
     * If null, persists forever.
     */
    protected ?int $ttl = 3600;

    /**
     * Context Identifiers.
     */
    protected ?string $userId = null;
    protected ?string $chatId = null;
    protected ?string $messageId = null;

    // =========================================================================
    //  NON-SERIALIZED DEPENDENCIES
    // =========================================================================

    /**
     * The Bot instance. Excluded from serialization via __sleep.
     */
    protected ?Krubot $bot = null;

    /**
     * Raw callback query object/array. Excluded from serialization via __sleep.
     */
    protected mixed $callbackQuery = null;

    // =========================================================================
    //  DATA & CONFIGURATION PROPERTIES
    // =========================================================================

    /**
     * Persistent data store.
     * Initialized in setContext to ensure availability.
     */
    protected Collection $data;

    /**
     * Automatic CallbackQuery Answer Strategy.
     * - true: Always answer.
     * - false: Never answer.
     * - null: Use cascading config.
     */
    protected ?bool $autoAnswerCallback = null;

    // =========================================================================
    //  VALIDATION & FLOW MEMORY
    // =========================================================================

    /**
     * The last question asked (for repeat functionality).
     * Type is mixed to support String, Object, Stringable.
     */
    protected mixed $lastQuestion = null;

    /**
     * The handler method for the last question (for repeat functionality).
     */
    protected mixed $lastQuestionMethod = null;

    /**
     * Holds validation logic for the NEXT answer.
     * Supports: Rule String, Rule Array, Closure, SerializableClosure.
     */
    protected mixed $nextValidator = null;

    // =========================================================================
    //  ABSTRACT METHODS
    // =========================================================================

    abstract public function start(Krubot $bot);

    // =========================================================================
    //  PUBLIC API (FLOW CONTROL)
    // =========================================================================

    /**
     * Asks a question and sets the next step.
     *
     * INTELLIGENT MERGE NOTE:
     * Logic combines File A's `toKrubotKeyboard` handling, File B's loose typing,
     * and File C's normalization.
     *
     * @param mixed $question Question text, Question Object, or Stringable.
     * @param mixed $next Method name (string), Closure, or Array callable.
     * @param mixed $validator Laravel rules (string/array) or Closure.
    */
    public function ask(mixed $question, mixed $next, mixed $validator = null): void
    {
        // 1. Store State for Repeat
        $this->lastQuestion = $question;
        $this->lastQuestionMethod = $next;

        // 2. Validator Storage Logic (Merged)
        if ($validator) {
            if ($validator instanceof Closure) {
                $this->nextValidator = new SerializableClosure($validator);
            } else {
                // Strings and Arrays are stored directly
                $this->nextValidator = $validator;
            }
        } else {
            // Explicit null clears the validator
            $this->nextValidator = null;
        }

        // 3. Message Preparation & Sending (The "Omni-Support" Logic)
        
        // CASE A: Legacy "Question Object" from File A (Specific Signature)
        if (is_object($question) && method_exists($question, 'getText') && method_exists($question, 'toKrubotKeyboard')) {
            $msg = $this->bot->reply($question->getText());
            $kbData = $question->toKrubotKeyboard();

            if (!empty($kbData)) {
                $msg->replyKeyboard(function($kb) use ($kbData) {
                    foreach($kbData as $rowBtns) {
                        $kb->row(function($row) use ($rowBtns) {
                           foreach($rowBtns as $btn) {
                               // Handle both string buttons and ['text' => '...'] format
                               $text = is_array($btn) ? ($btn['text'] ?? '') : $btn;
                               $row->simple($text);
                           }
                        });
                    }
                });
            }
            $msg->send();
        }
        // CASE B: Standard String or Modern Object (File B/C + File A fallback)
        else {
            $msg = $this->bot->reply((string) $question);

            if (is_object($question)) {
                $kb = null;

                // Priority 1: Modern Interface
                if (method_exists($question, 'toKeyboard')) {
                    $kb = $question->toKeyboard();
                }
                // Priority 2: Legacy Accessor (File 2/B)
                elseif (method_exists($question, 'getButtons')) {
                    $kb = $question->getButtons();
                }
                // Priority 3: Fallback for File A objects used in this context
                elseif (method_exists($question, 'toKrubotKeyboard')) {
                    $kb = $question->toKrubotKeyboard();
                }

                // Render Keyboard
                if ($kb instanceof Keyboard) {
                    // Fastest path: Native Injection
                    $rawData = $kb->toArray();
                    $msg->inlineKeypad($rawData['rows'] ?? $rawData);
                } elseif (is_array($kb)) {
                    // Legacy path: Manual Construction via Closure
                    $msg->attachKeyboard(function($builder) use ($kb) {
                        foreach ($kb as $row) {
                            $builder->row(fn($r) => $r->add($row));
                        }
                    });
                }
            }
            $msg->send();
        }

        // 4. Set Next Step
        $this->next($next);
    }

    /**
     * Repeats the last question asked.
     * Preserves the original validator.
    */
    public function repeat(): void
    {
        if ($this->lastQuestion && $this->lastQuestionMethod) {
            $this->ask($this->lastQuestion, $this->lastQuestionMethod, $this->nextValidator);
        }
    }

    /**
     * Advances the conversation state.
     *
     * @param mixed $stepMethod string|callable|array
    */
    protected function next(mixed $stepMethod): void
    {
        // Handle Callable (Closure or Array like [Controller::class, 'method'])
        if (is_callable($stepMethod) && !is_string($stepMethod)) {
            $closure = $stepMethod instanceof Closure
                        ? $stepMethod
                        : Closure::fromCallable($stepMethod);

            $this->step = new SerializableClosure($closure);
        }
        // Handle String (Method Name)
        elseif (is_string($stepMethod)) {
             if (!method_exists($this, $stepMethod)) {
                // Log but don't crash, following File B/C approach
                Log::error("Conversation Error: Method '{$stepMethod}' does not exist in " . get_class($this));
             }
             $this->step = $stepMethod;
        }
        else {
             // Fallback/Legacy
             $this->step = $stepMethod;
        }

        $this->save();
    }

    // =========================================================================
    //  THE CONVERSATION ENGINE: RUN (K-MASTERPIECE)  
    // =========================================================================

    /**
     * Executes the conversation flow.
     *
     * PIPELINE:
     * 1. Action Detection (Priority 1) -> Bypass Validation -> Auto Answer -> Execute.
     * 2. Text Input Processing -> Create Answer Object.
     * 3. Hybrid Validation:
     *    a. Pre-flight Closure.
     *    b. Attribute Rules (Method + Class).
     *    c. Manual Rules.
     *    d. Laravel Validator.
     * 4. Step Handler Execution (Reflection).
    */
    public function run(): void
    {
        if (!$this->bot) return;

        $inputText = $this->bot->text();

        // -------------------------------------------------------------
        // PHASE 1: ACTION / CALLBACK ENGINE (Files 4, 6, 7 Logic)
        // -------------------------------------------------------------
        $detectedAction = $this->detectActionFromInput($inputText);

        if ($detectedAction !== null) {
            // Normalize action payload (Handles Collection return from File 7 logic if overridden)
            $actionPayload = ($detectedAction instanceof Collection)
                ? $detectedAction->toArray()
                : (array) $detectedAction;

            $actionName = $actionPayload['action'] ?? null;
            $payloadData = $actionPayload['data'] ?? [];

            // Context Hydration
            $this->callbackQuery = $actionPayload['raw'] ?? $this->bot->callbackQuery();
            $this->data = $this->data->merge($payloadData);

            if ($actionName && method_exists($this, $actionName)) {
                // Auto-Answer Logic (Merged from A & C)
                $this->attemptAutoAnswer();

                // Execution (Bypassing Text Validation)
                $answerObj = new Answer($inputText, $payloadData);
                $this->invokeCallableWithReflection([$this, $actionName], $answerObj);
                return; // STOP EXECUTION HERE
            }
        }

        // -------------------------------------------------------------
        // PHASE 2: HYBRID VALIDATION (Files 1, 2, 3 Logic)
        // -------------------------------------------------------------
        $answer = new Answer($inputText);
        $inputValue = $answer->getValue();

        // A. Closure Pre-flight Check (Files A/B/C)
        if ($this->nextValidator instanceof SerializableClosure || $this->nextValidator instanceof Closure) {
            $closure = ($this->nextValidator instanceof SerializableClosure)
                        ? $this->nextValidator->getClosure()
                        : $this->nextValidator;

            $result = call_user_func($closure, $answer);

            if ($result !== true) {
                $errorMsg = is_string($result) ? $result : '❌ پاسخ نامعتبر است.';
                $this->bot->reply($errorMsg)->send();
                $this->repeat();
                return; // HALT
            }
        }

        // B. Attribute Rules (File B Powerful Logic: Method + Class)
        $attributeRules = $this->resolveAttributeRules();

        // C. Manual Rules (Passed to ask)
        $manualRules = [];
        if (is_string($this->nextValidator) || is_array($this->nextValidator)) {
            $manualRules = $this->nextValidator;
        }

        // D. Merge Rules (File B/X logic)
        $mergedRules = $this->mergeRules($manualRules, $attributeRules);

        // E. Laravel Validator Execution
        if (!empty($mergedRules)) {

            // 🔓 گارد بازگشایی: تبدیل SerializableClosure به Closure خالص برای لاراول
            $executableRules = array_map(function ($rule) {
                return ($rule instanceof SerializableClosure) ? $rule->getClosure() : $rule;
            }, $mergedRules);

            $validator = Validator::make(
                ['answer' => $inputValue],
                ['answer' => $executableRules], // $mergedRules
                [], // Messages could be customized here
                ['answer' => 'پاسخ'] // Attribute name
            );

            if ($validator->fails()) {
                $this->bot->reply($validator->errors()->first())->send();
                $this->repeat();
                return; // HALT
            }
        }

        // ✅ Validation Passed
        $this->nextValidator = null; // Clear one-time validator
        $this->save();

        // -------------------------------------------------------------
        // PHASE 3: STEP HANDLER EXECUTION
        // -------------------------------------------------------------
        $this->executeStepHandler($answer);
    }

    // =========================================================================
    //  INTERNAL MECHANICS & HELPERS
    // =========================================================================

    /**
     * Executes the handler for the current step.
    */
    protected function executeStepHandler(Answer $answer): void
    {
        $handler = null;

        if (is_string($this->step) && method_exists($this, $this->step)) {
            $handler = [$this, $this->step];
        } elseif ($this->step instanceof SerializableClosure) {
            $handler = $this->step->getClosure();
        }

        if ($handler) {
            $this->invokeCallableWithReflection($handler, $answer);
        } else {
            Log::error("Conversation Step Error: Could not resolve step handler in " . get_class($this));
        }
    }

    /**
     * Resolves Validation Rules from PHP 8 Attributes.
     * MERGED LOGIC (File B): Checks both Method and Class attributes.
    */
    protected function resolveAttributeRules(): array
    {
        $rules = [];
        // Only applicable if step is a string method name
        if (is_string($this->step) && method_exists($this, $this->step)) {
            try {
                $refMethod = new ReflectionMethod($this, $this->step);

                // 1. Method Attributes (Files A/B/C)
                $methodAttributes = $refMethod->getAttributes(AttributeRule::class);
                foreach ($methodAttributes as $attr) {
                    $inst = $attr->newInstance();
                    // Support both 'rules' property (File C) and toArray (File A/B)
                    $extracted = isset($inst->rules) ? $this->normalizeRules($inst->rules) : ($inst->toArray() ?? []);
                    $rules = array_merge($rules, $extracted);
                }

                // 2. Class Attributes (File B Specific - Powerful inheritance)
                $refClass = $refMethod->getDeclaringClass();
                $classAttributes = $refClass->getAttributes(AttributeRule::class);
                foreach ($classAttributes as $attr) {
                    $inst = $attr->newInstance();
                    $extracted = isset($inst->rules) ? $this->normalizeRules($inst->rules) : ($inst->toArray() ?? []);
                    $rules = array_merge($rules, $extracted);
                }

            } catch (ReflectionException $e) {
                // Silent fail is intended
            }
        }
        return $rules;
    }

    /**
     * Merges Manual and Attribute rules into a flat array.
    */
    protected function mergeRules(mixed $manual, array $attributeRules): array
    {
        $out = [];
        $manual = $this->normalizeRules($manual);
        
        // Merge Manual
        foreach ($manual as $m) $out[] = $m;
        
        // Merge Attributes
        foreach ($attributeRules as $a) {
            if (is_array($a)) {
                foreach ($a as $i) $out[] = $i;
            } else {
                $out[] = $a;
            }
        }
        
        return array_values(array_filter($out, fn($v) => $v !== null && $v !== ''));
    }

    /**
     * Normalizes string rules (pipe-separated) to array.
     */
    protected function normalizeRules(mixed $rules): array
    {
        if (is_string($rules)) {
            return explode('|', $rules);
        }
        if (is_array($rules)) {
            return $rules;
        }
        if ($rules === null) {
            return [];
        }
        return [$rules];
    }

    /**
     * Advanced Action Detection.
     * Combines logic from Files A, C (JSON/Regex) and X (Restructure).
     *
     * @return array|null Returns ['action' => string, 'data' => array, 'raw' => mixed] or null.
    */
    protected function detectActionFromInput(mixed $input): ?array
    {
        if (!is_string($input) || empty($input)) return null;

        $trim = trim($input);

        // 1. JSON Strategy (Files A/C)
        $decoded = json_decode($trim, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            if (isset($decoded['action'])) {
                return [
                    'action' => (string) $decoded['action'],
                    'data' => $decoded['data'] ?? [],
                    'raw' => $decoded
                ];
            }
            // 'callback_query' wrapper support
            if (isset($decoded['callback_query'])) {
                $cq = $decoded['callback_query'];
                return [
                    'action' => $cq['data'] ?? $cq['action'] ?? null,
                    'data' => $cq,
                    'raw' => $decoded
                ];
            }
        }

        // 2. Custom Regex Format "action:name|key=val" (Files A/C)
        if (preg_match('/^action:([^|]+)(|.*)$/', $trim, $matches)) {
            $result = ['action' => $matches[1], 'data' => [], 'raw' => $trim];
            if (!empty($matches[2])) {
                parse_str(str_replace('|', '&', ltrim($matches[2], '|')), $params);
                $result['data'] = $params;
            }
            return $result;
        }

        // 3. Query String Strategy (Files A/B/C)
        // Must contain '=' and ('&' or 'action=')
        if (str_contains($trim, '=') && (str_contains($trim, '&') || str_contains($trim, 'action='))) {
            parse_str($trim, $qs);
            if (isset($qs['action'])) {
                return [
                    'action' => (string) $qs['action'],
                    'data' => $qs,
                    'raw' => $qs
                ];
            }
        }

        return null;
    }

    /**
     * Smart Dependency Injection via Reflection.
     * Supports: Answer, Krubot, Conversation instance, Static Class.
    */
    protected function invokeCallableWithReflection(callable $handler, Answer $answer): void
    {
        try {
            if (is_array($handler)) {
                $reflector = new ReflectionMethod($handler[0], $handler[1]);
            } elseif ($handler instanceof Closure || (is_string($handler) && function_exists($handler))) {
                $reflector = new ReflectionFunction($handler);
            } elseif (is_object($handler) && method_exists($handler, '__invoke')) {
                $reflector = new ReflectionMethod($handler, '__invoke');
            } else {
                // Last resort fallback
                call_user_func($handler, $answer, $this->bot);
                return;
            }
        } catch (ReflectionException $e) {
            Log::error("Reflection failed in Conversation: " . $e->getMessage());
            call_user_func($handler, $answer, $this->bot);
            return;
        }

        $parameters = $reflector->getParameters();
        $args = [];

        if (empty($parameters)) {
            // Legacy default arguments
            $args = [$this->bot];
        } else {
            foreach($parameters as $param) {
                $type = $param->getType();
                if ($type && !$type->isBuiltin()) {
                    $typeName = $type->getName();
                    if ($typeName === Answer::class || is_subclass_of($typeName, Answer::class)) {
                        $args[] = $answer;
                    } elseif ($typeName === Krubot::class || is_subclass_of($typeName, Krubot::class)) {
                         $args[] = $this->bot;
                    } elseif ($typeName === static::class || is_subclass_of($typeName, static::class) || $typeName === self::class){
                        $args[] = $this;
                    }
                }
            }
            // Fallback if no specific types matched the signature
            if (empty($args)){
                 $args = [$answer, $this->bot];
            }
        }

        call_user_func_array($handler, $args);
    }

    /**
     * Attempts to Auto-Answer the callback query and hide loading circle on button.
     * Combines Config Logic (File A) + Robust ID Extraction (File C).
    */
    protected function attemptAutoAnswer(): void
    {
        // 1. Resolve Config (File A Logic)
        $shouldAnswer = $this->autoAnswerCallback;
        if ($shouldAnswer === null) {
            $shouldAnswer = config('krubik.conversations.auto_answer_default', false);
        }

        if (!$shouldAnswer) return;

        // 2. Perform Answer (File C Logic - Best Effort)
        try {
            if (method_exists($this->bot, 'answerCallbackQuery')) {
                // Try to find the ID if needed by the bot method
                $id = null;
                if ($this->callbackQuery) {
                     if (is_object($this->callbackQuery)) {
                         $id = method_exists($this->callbackQuery, 'id')
                             ? $this->callbackQuery->id()
                             : ($this->callbackQuery->id ?? null);
                     } elseif (is_array($this->callbackQuery)) {
                         $id = $this->callbackQuery['id'] ?? null;
                     }
                }
                
                // Invoke method. Some bot libs don't need ID passed if state is loaded, some do.
                // We pass ID if we found one.
                if ($id) {
                    $this->bot->answerCallbackQuery($id, null, false);
                } else {
                    $this->bot->answerCallbackQuery();
                }
            } elseif (method_exists($this->bot, 'callApi') && $this->callbackQuery) {
                // Manual Fallback via API
                $id = is_array($this->callbackQuery) ? ($this->callbackQuery['id'] ?? null) : ($this->callbackQuery->id ?? null);
                if ($id) {
                    $this->bot->callApi('answerCallbackQuery', [
                        'callback_query_id' => $id,
                        'text' => null,
                        'show_alert' => false
                    ]);
                }
            }
        } catch (Throwable $e) {
            Log::warning("Conversation AutoAnswer failed: " . $e->getMessage());
        }
    }

    /**
     * Public wrapper for manual callback answering (File A/B API).
    */
    public function answerCallbackQuery(?string $text = null, bool $showAlert = false): static
    {
        try {
            if (method_exists($this->bot, 'answerCallbackQuery')) {
                $this->bot->answerCallbackQuery($text, $showAlert);
            }
        } catch (Throwable $e) {}
        return $this;
    }

    // =========================================================================
    //  PERSISTENCE & MAGIC
    // =========================================================================

    /**
     * Sets the bot context and hydrates the object.
    */
    public function setContext(Krubot $bot): void
    {
        $this->bot = $bot;
        $this->userId = $bot->user()['id'] ?? 'guest';
        $this->chatId = $bot->chatId() ?? 'global';
        $this->messageId = $bot->findMessageId();
        $this->key = "kgx_conv_{$this->chatId}_{$this->userId}";

        // Hydrate callback query context if available on bot
        if (method_exists($bot, 'callbackQuery')) {
            try { $this->callbackQuery = $bot->callbackQuery(); } catch(Throwable $e) {}
        }

        // Ensure data collection is initialized
        if (!isset($this->data) || !$this->data instanceof Collection) {
            $this->data = new Collection();
        }
    }

    /**
     * Persists the conversation state to cache.
    */
    public function save(): void
    {
        if ($this->key) {
            $dataToCache = serialize($this);
            if ($this->ttl === null) {
                Cache::forever($this->key, $dataToCache);
            } else {
                Cache::put($this->key, $dataToCache, $this->ttl);
            }
        }
    }

    /**
     * Terminates the conversation.
    */
    public function end(): void
    {
        if ($this->key) {
            Cache::forget($this->key);
        }
    }

    /**
     * Persist forever (remove TTL).
    */
    public function persistForever(): void
    {
        $this->ttl = null;
        $this->save();
    }

    /**
     * Magic method to exclude heavy/runtime objects from serialization.
     */
    public function __sleep(): array
    {
        $props = array_keys(get_object_vars($this));
        return array_diff($props, ['bot', 'callbackQuery']);
    }

    // --- Magic Data Access (Fluent API) ---

    public function __get(string $key)
    {
        return $this->data->get($key);
    }

    public function __set(string $key, mixed $value): void
    {
        $this->data->put($key, $value);
    }

    public function __isset(string $key): bool
    {
        return $this->data->has($key);
    }

    public function __unset(string $key): void
    {
        $this->data->forget($key);
    }
}
