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

use Closure;
use KrubiK\Conversations\Conversation;
use KrubiK\Conversations\Form;
use KrubiK\Conversations\ShadowConversation;
use Illuminate\Support\Facades\Cache;
use KrubiK\Helpers\AmethystMatrix as Log;
use Laravel\SerializableClosure\SerializableClosure;

/**
 * Trait CanInitConversations
 * 
 * Provides a powerful state machine for handling conversations in a bot.
 * This trait consolidates and enhances functionalities from multiple development versions.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait CanInitConversations
{
    // =========================================================================
    //  CONVERSATION LIFECYCLE & ENTRY POINTS
    // =========================================================================

    /**
     * Starts a new conversation state machine.
     *
     * This enhanced method supports both a class name and a pre-instantiated object,
     * offering a Modern Superior Developer Experience;
     *
     * Accepts either:
     *  - A class-string (e.g., MyConversation::class) which will be instantiated automatically.
     *  - An instantiated Conversation object (e.g., new MyConversation(...)) for maximum flexibility.
     *
     * @param string|\KrubiK\Conversations\Conversation $conversation The conversation to start.
     */
    public function beginConversation(string|Conversation $conversation): void
    {
        // If caller passed a class name, instantiate it.
        if (is_string($conversation)) {
            if (!class_exists($conversation)) {
                Log::error("Conversation class not found: {$conversation}");
                return;
            }
            /** @var Conversation $instance */
            $instance = new $conversation();
        }
        // If caller passed an instance, use it directly (e.g., from Form Builder).
        elseif ($conversation instanceof Conversation) {
            $instance = $conversation;
        } else {
            Log::error('beginConversation: invalid argument. Expecting class-string or Conversation instance.');
            return;
        }

        // Inject fresh bot context (Dependency Injection) and start the conversation flow.
        $instance->setContext($this);
        $instance->start($this);

        // Initial save to the persistence layer (Cache).
        $instance->save();
    }

    public function input(string|Conversation $conversation): void
    {
        $this->beginConversation($conversation);
    }

    /**
     * Start a fluent Form Builder.
     *
     * Returns a configured Form instance ready for fluent API usage.
     * The built form is a Conversation itself and can be started via ->run().
     *
     * @param string|null $name Optional form name (for logs/debug).
     * @return \KrubiK\Conversations\Form
     */
    public function form(?string $name = null): Form
    {
        $form = new Form();

        // Inject Bot Context immediately. This gives the form access to the bot instance,
        // so its ->run() method can later call beginConversation on the bot object.
        $form->setContext($this);
        
        if ($name && method_exists($form, 'setName')) {
            $form->setName($name);
        }

        return $form;
    }
    
    /**
     * شبیه‌سازی متد محبوب UniChatKit مستقیماً روی آبجکت ربات.
     * این متد یک مکالمه آنی (Inline) و تک-مرحله‌ای ایجاد می‌کند.
     *
     * @param string|\KrubiK\Questions\Question $question متن سوال یا آبجکت سوال
     * @param Closure|string|array $next هندلر پاسخ (کلوژر یا نام متد)
     */
    public function ask($question, $next): void
    {
        // 1. ساخت یک مکالمه‌ی "ظرف" پویا
        $conversation = new ShadowConversation();

        // 2. تزریق کانتکست (آیدی کاربر، چت و ...)
        $conversation->setContext($this);

        // 3. تنظیم اولین قدم (سوال و هندلر) داخل ظرف
        $conversation->setInitialStep($question, $next);

        // 4. شروع مکالمه (ارسال سوال به کاربر)
        $conversation->start($this);

        // 5. ذخیره وضعیت در کش (برای دریافت پاسخ بعدی)
        $conversation->save();
    }
    
    /**
     * Checks if the user is currently in a conversation.
     * If yes, it resumes the conversation from its saved state and blocks other routes.
     * This is the primary method for continuing an active conversation.
     *
     * @return bool Returns true if a conversation was found and resumed, false otherwise.
     */
    public function continueConversation(): bool
    {
        $userId = $this->senderId();
        $chatId = $this->chatId() ?? null;

        if (!$userId || !$chatId) {
            return false;
        }

        $key = "kgx_conv_{$chatId}_{$userId}";

        if (Cache::has($key)) {
            try {
                $serialized = Cache::get($key);
                /** @var Conversation $conversation */
                $conversation = unserialize($serialized);

                // Re-inject the fresh Bot instance to ensure the conversation has access
                // to the latest request data (Dependency Injection).
                $conversation->setContext($this);

                // Run the current step of the conversation.
                $conversation->run();
                return true;
            } catch (\Throwable $e) {
                Log::error("Conversation Resume Error: " . $e->getMessage(), ['exception' => $e]);
                Cache::forget($key); // Clear corrupted state to prevent infinite loops.
            }
        }

        return false;
    }

    // =========================================================================
    //  CONVERSATION CONTROLS
    // =========================================================================

    /**
     * 🪄 پرش از سؤال فعلی و رفتن به مرحله بعد.
     * این متد مرحله بعدی مکالمه را بدون انتظار برای پاسخ کاربر اجرا می‌کند.
     * اکنون از هر نوع Step پشتیبانی می‌کند: string، Closure و SerializableClosure.
     */
    public function skipConversation(): void
    {
        try {
            $conv = $this->getActiveConversation();

            if ($conv && (is_string($conv->step) || is_callable($conv->step) || $conv->step instanceof SerializableClosure)) {
                // $this->reply(__('conversation.skipped'))->send();
                // اجرای مرحله بعدی بدون انتظار پاسخ کاربر
                $conv->run();
            }
        } catch (\Throwable $e) {
            Log::error("KrubiK skipConversation failed: {$e->getMessage()}", ['exception' => $e]);
        }
    }

    /**
     * 💣 توقف کامل مکالمه فعال و پاکسازی استیت.
     * این متد مکالمه را خاتمه داده و وضعیت آن را از حافظه (Cache) پاک می‌کند.
     */
    public function stopConversation(): void
    {
        try {
            $conv = $this->getActiveConversation();
            if ($conv) {
                $conv->end();
                // $this->reply(__('conversation.stopped'))->send();
            }
        } catch (\Throwable $e) {
            Log::error("KrubiK stopConversation failed: {$e->getMessage()}", ['exception' => $e]);
        }
    }

    // =========================================================================
    //  EVENT HANDLERS
    // =========================================================================
    
    /** @var array<string, callable> */
    protected array $exceptionHandlers = [];
    
    /** @var ?callable */
    protected $missingHandler = null;

    /**
     * ⚙️ ثبت هندلر برای استثناهای خاص در طول مکالمه.
     *
     * @param string $exception The fully qualified class name of the exception.
     * @param callable $callback The function to call when the exception is caught.
     * @return static
    */
    public function exception(string $exception, callable $callback): static
    {
        $this->exceptionHandlers[$exception] = $callback;
        return $this;
    }

    /**
     * 🧩 ثبت هندلر Missing برای زمانی که پیامی دریافت می‌شود ولی هیچ مکالمه فعالی برای کاربر وجود ندارد.
     *
     * @param callable $callback The function to call.
     * @return static
    */
    public function missing(callable $callback): static
    {
        $this->missingHandler = $callback;
        return $this;
    }

    // =========================================================================
    //  CORE HELPERS (Protected)
    // =========================================================================

    /**
     * بازیابی مکالمه فعال کاربر از Cache.
     *
     * @return \KrubiK\Conversations\Conversation|null
    */
    protected function getActiveConversation(): ?Conversation
    {
        $chatId = $this->chatId();
        $userId = $this->senderId();
        $key = "kgx_conv_{$chatId}_{$userId}";

        if (!cache()->has($key)) {
            return null;
        }

        $data = cache()->get($key);
        try {
            /** @var Conversation $conv */
            $conv = unserialize($data);
            if ($conv instanceof Conversation) {
                $conv->setContext($this); // تزریق مجدد Context برای دسترسی به متدهای ربات
                return $conv;
            }
        } catch (\Throwable $e) {
            Log::warning("Conversation restoration failed: {$e->getMessage()}", ['exception' => $e]);
            // Optional: Delete the corrupted cache key
            // cache()->forget($key);
        }

        return null;
    }

    /**
     * اجرای Exception Handlers اگر برای استثناء رخ داده، تعریف شده باشد.
     *
     * @param \Throwable $e
    */
    protected function handleException(\Throwable $e): void
    {
        $exceptionClass = get_class($e);
        foreach ($this->exceptionHandlers as $type => $handler) {
            if ($exceptionClass === $type || is_subclass_of($exceptionClass, $type)) {
                call_user_func($handler, $this, $e);
                return;
            }
        }

        // Fallback generic error log if no specific handler is found.
        Log::error("Unhandled KrubiK Exception in conversation: {$e->getMessage()}", ['exception' => $e]);
        // $this->reply(__('conversation.exception_generic', ['error' => $e->getMessage()]))->send();
    }

    /**
     * اجرای Missing Handler وقتی هیچ مکالمه‌ای برای کاربر یافت نشود.
    */
    protected function handleMissingConversation(): void
    {
        if (is_callable($this->missingHandler)) {
            call_user_func($this->missingHandler, $this);
        } else {
            // Default behavior if no missing handler is set.
            // $this->reply(__('conversation.missing'))->send();
        }
    }
}
