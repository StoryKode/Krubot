<?php

namespace KrubiK\Conversations;
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

use KrubiK\Krubot;
use KrubiK\DTOs\FormField;
use KrubiK\Conversations\Answer;
use Illuminate\Contracts\Validation\Rule;
use Laravel\SerializableClosure\SerializableClosure;
use Closure;
use LogicException;
use RuntimeException;

/**
 * Class Form
 *
 * The Ultimate Hybrid Form Engine for KrubiK. (v2 - Now with Confirmation Magic)
 * Supports both Fluent Builder API and Class-Based Inheritance,
 * plus automatic handling of the 'confirmed' validation rule.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×vRC.8×
 * @license MIT
*/
class Form extends Conversation
{
    /**
     * @var FormField[]
     * List of fields/questions to process in the queue.
     * صف فیلدهایی که باید پرسیده شوند.
     */
    protected array $fields = [];

    /**
     * @var int
     * Pointer to the current step index.
     * اشاره‌گر به مرحله فعلی.
     */
    protected int $currentIndex = 0;

    /**
     * @var array
     * Storage for collected user answers ['key' => 'value'].
     * مخزن داده‌های جمع‌آوری شده.
     */
    protected array $collectedData = [];

    /**
     * @var SerializableClosure|null
     * Logic to execute after form completion (Fluent API).
     * منطقی که پس از پایان فرم اجرا می‌شود.
     */
    protected ?SerializableClosure $onComplete = null;

    /**
     * @var string
     * Name of the form context (useful for logging/debugging).
     * نام فرم (جهت دیباگ یا لاگ).
     */
    protected string $formName = 'dynamic_form';

    // =========================================================================
    //  FLUENT BUILDER API 🏗️ (100% UNCHANGED - AGTP-v1 COMPLIANT)
    // =========================================================================

    /**
     * Set a custom name for this form instance.
     * تنظیم نام فرم (اختیاری).
     *
     * @param string $name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->formName = $name;
        return $this;
    }

    /**
     * Add a field to the form flow.
     * Supports simple text questions or complex Question objects.
     * افزودن یک فیلد به فرم.
     *
     * @param string $key The key to store data under / کلید ذخیره‌سازی داده
     * @param string|\KrubiK\Conversations\Question $question Question text or Object / متن سوال یا آبجکت
     * @return static
     */
    public function field(string $key, mixed $question, mixed $validate_rules = null): static
    {
        // Create new FormField object (Clean OOP approach)
        $this->fields[] = new FormField($key, $question);

        if($validate_rules)
            $this->rules($validate_rules);

        return $this;
    }

    // Delegate to field() to maintain compatibility
    public function addField(string $key, mixed $question, mixed $validate_rules = null): static
    {
        return $this->field($key, $question, $validate_rules);
    }

    /**
     * Add validation rules to the LAST added field.
     * Supports String, Array, or Custom Rule Objects in a Variadic way.
     * افزودن قوانین اعتبارسنجی به **آخرین** فیلد اضافه شده.
     *
     * @param string|array|Rule|callable|Closure ...$rules e.g. rules('required', 'min:3') or rules(['required', new Rule])
     * @return static
     * @throws LogicException If called before adding any fields.
     */
    public function rules(string|array|Rule|callable|Closure ...$rules): static
    {
        if (empty($this->fields)) {
            throw new LogicException("❌ Logical Error: Call field() before defining rules().");
        }

        // Get the last field index efficiently
        $lastIndex = array_key_last($this->fields);
        $field = $this->fields[$lastIndex];

        // Normalize rules:
        // If passed as variadic arguments rules('required', 'email') -> $rules is ['required', 'email']
        // If passed as single string rules('required|email') -> $rules is ['required|email'] (keep as is inside array)
        // Logic: if user passed 1 argument and it's an array/string, use that directly.
        // Otherwise use the variadic array.
        $finalRules = count($rules) === 1 ? $rules[0] : $rules;

        // نرمال‌سازی به آرایه جهت پیمایش
        if (!is_array($finalRules)) {
            $finalRules = [$finalRules];
        }

        // 🛡️ گارد سریالایز: تبدیل کلوژرها به آبجکت قابل ذخیره
        foreach ($finalRules as &$rule) {
            if ($rule instanceof Closure) {
                $rule = new SerializableClosure($rule);
            }
        }

        $field->rules = $finalRules;

        $field->rules = $finalRules;

        // Save back the modified field object
        $this->fields[$lastIndex] = $field;

        return $this;
    }

    /**
     * Define the callback to execute when the form is finished.
     * تعریف عملیات پایان فرم.
     *
     * @param callable $callback Function receiving ($data, $bot)
     * @return static
    */
    public function then(callable $callback): static
    {
        // Wrap in SerializableClosure to ensure it survives serialization if needed
        $closure = $callback instanceof Closure
            ? $callback
            : Closure::fromCallable($callback);

        $this->onComplete = new SerializableClosure($closure);
        return $this;
    }

    // =========================================================================
    //  ENGINE IMPLEMENTATION ⚙️ (SURGICALLY UPGRADED)
    // =========================================================================

    /**
     * Entry point for the Fluent Builder.
     * Registers the conversation with the Bot.
     * نقطه شروع اجرای فرم (مخصوص بیلدر).
     *
     * @throws RuntimeException If Bot context is missing.
     */
    public function run(): void
    {
        if (!$this->bot) {
            throw new RuntimeException("Form must be initialized via \$bot->form() to have context.");
        }

        // Delegate execution to the Bot engine
        // Passing $this allows the bot to cache/serialize the whole form object
        $this->bot->beginConversation($this);
    }

    /**
     * The Standard Conversation Start Method.
     * Called by Bot->beginConversation().
     * متد استاندارد شروع مکالمه (از کلاس والد Conversation).
     *
     * @param Krubot $bot
     */
    public function start(Krubot $bot)
    {
        // [Hybrid Support]: If class is extended and has setup(), run it.
        // اگر کسی کلاس را اکستند کرده باشد، اینجا فیلدها را ستاپ می‌کنیم.
        if (method_exists($this, 'setup')) {
            $this->setup();
        }

        // Start the recursive asking loop
        $this->askNextField();
    }

    /**
     * Logic to determine and ask the next question.
     * (Recursive Engine).
     * منطق هوشمند پرسش سوال بعدی.
     */
    protected function askNextField(): void
    {
        // 1. Check if we are done (Boundary Check)
        // بررسی پایان فرم
        if (!isset($this->fields[$this->currentIndex])) {
            $this->finalize();
            return;
        }

        // 2. Get current field config
        // دریافت فیلد جاری
        $field = $this->fields[$this->currentIndex];

        // 3. Use the POWERFUL existing ask method from Conversation parent!
        // We pass 'processAnswer' as the specific handler name.
        // We pass the field's rules directly to the validation engine here.
        // اعتبارسنجی به صورت خودکار توسط Conversation::ask انجام می‌شود.
        $this->ask(
            $field->question,
            'processAnswer', // The generic handler method name
            $field->rules    // Validation rules for this specific step
        );
    }

    /**
     * Generic handler for ALL form steps.
     * Since this is called via `ask`, we know validation has ALREADY passed!
     * This method now includes the logic branch for 'confirmed' rule.
     * هندلر مرکزی تمام پاسخ‌ها. وقتی به اینجا می‌رسیم یعنی اعتبارسنجی پاس شده است.
     *
     * @param Answer $answer The validated answer object
     */
    public function processAnswer(Answer $answer)
    {
        // 1. Identify current field
        $field = $this->fields[$this->currentIndex];

        // 2. Store the valid answer
        // We use getValue() to support both text inputs and button payloads universally.
        // ذخیره دیتا با پشتیبانی از دکمه و متن
        $this->collectedData[$field->key] = $answer->getValue();

        // 3. ✨ MAGIC IMPLANT: 'confirmed' rule handling
        // After storing the value, check if it needs confirmation before proceeding.
        if ($this->isConfirmedRulePresent($field)) {
            // If the rule exists, divert the conversation flow to a confirmation step.
            $this->askForConfirmation($answer->getValue());
            // Halt the normal flow. The next step will be handled by 'processConfirmationAnswer'.
            return;
        }
        
        // 4. Move pointer forward (original logic)
        // حرکت به جلو
        $this->currentIndex++;

        // 5. Trigger next step (FIFO Loop)
        // پرسش بعدی
        $this->askNextField();
    }

    /**
     * Finalize the form execution.
     * پایان کار و اجرای کالبک یا متد نهایی.
     */
    protected function finalize(): void
    {
        // Priority 1: Fluent Callback (via `then`)
        // اگر کاربر از متد then استفاده کرده باشد
        if ($this->onComplete) {
            $closure = $this->onComplete->getClosure();
            // Execute closure injecting Data and Bot instance
            call_user_func($closure, $this->collectedData, $this->bot);
        }
        // Priority 2: Class Method Override (Hybrid Mode)
        // اگر کاربر کلاس را اکستند کرده و متد submit دارد
        elseif (method_exists($this, 'submit')) {
            $this->submit($this->collectedData);
        }

        // Clean up conversation state
        // پاکسازی وضعیت مکالمه
        $this->end();
    }
    
    // =========================================================================
    //  ✨ NEW CONFIRMATION MAGIC METHODS (IMPORTED FROM AI SUGGESTION)
    // =========================================================================

    /**
     * Asks the user to confirm the value they just entered.
     * This is triggered when a field has the 'confirmed' validation rule.
     *
     * @param mixed $originalValue The value from the original field to check against.
     */
    protected function askForConfirmation(mixed $originalValue): void
    {
        // The confirmation question text. Could be made configurable in the future.
        $confirmationQuestion = "لطفاً برای تایید، دوباره وارد کنید:";

        // The validation rule for this temporary confirmation step.
        // The input MUST match the original value. The parent Conversation's
        // `ask` method will handle the validation failure and repeat the question.
        $confirmationRules = ['required', 'in:'.$originalValue];

        // Use the parent's `ask` method to ask the temporary question.
        // Crucially, the handler is set to `processConfirmationAnswer`, a new dedicated handler.
        $this->ask($confirmationQuestion, 'processConfirmationAnswer', $confirmationRules);
    }
    
    /**
     * Handles the answer from the virtual 'confirmation' step.
     * This method is only called if the confirmation value was valid (matched the original).
     *
     * @param Answer $answer The validated answer object (which is guaranteed to be correct here).
     */
    public function processConfirmationAnswer(Answer $answer)
    {
        // The validation was successful, so we don't need the answer value itself.
        // We just need to move the form to the next *real* field.
        
        // 1. Move main pointer forward
        $this->currentIndex++;

        // 2. Trigger next step in the main flow
        $this->askNextField();
    }
    
    /**
     * Checks if a field's rules contain the 'confirmed' rule.
     *
     * @param FormField $field The field to check.
     * @return bool
     */
    private function isConfirmedRulePresent(FormField $field): bool
    {
        $rules = $field->rules;
        if (is_string($rules) && str_contains($rules, 'confirmed')) {
            return true;
        }
        if (is_array($rules) && in_array('confirmed', $rules, true)) {
            return true;
        }
        return false;
    }

    // =========================================================================
    //  HYBRID HOOKS (Optional Overrides) 🪝 (100% UNCHANGED)
    // =========================================================================

    /**
     * Hook to define fields in a class-based Form.
     * Override this method in child classes.
     */
    protected function setup(): void
    {
        // Logic to be implemented by child class
    }

    /**
     * Hook to handle submission in a class-based Form.
     * Override this method in child classes.
     *
     * @param array $data The fully collected data
     */
    protected function submit(array $data): void
    {
        // Logic to be implemented by child class
    }
}
