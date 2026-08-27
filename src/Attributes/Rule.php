<?php

namespace KrubiK\Attributes;
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

use Attribute;
use Illuminate\Contracts\Validation\Rule as LaravelRule; // <-- IMPORT THE INTERFACE

/**
 * ☢️ The Hyper-Powered Smart Validation Rule Attribute
 * Attribute to declare validation rules on Conversation methods (or classes).
 * 
 * این اتریبیوت قدرتمند اجازه می‌دهد قوانین اعتبارسنجی را مستقیماً بالای متدها تعریف کنید.
 * از سینتکس Variadic پشتیبانی می‌کند و هر فرمتی را می‌پذیرد:
 * 
 * 1. String:          #[Rule('required|min:5')]
 * 2. Array:           #[Rule(['required', 'email'])]
 * 3. Custom Objects:  #[Rule(new MyCustomRule())] // <-- NOW SUPPORTED!
 * 4. Mixed:           #[Rule('required', new MyCustomRule(), ['max:255'])]
 * 
 * Accepts:
 *  - string (pipe syntax)
 *  - array of rules
 *  - one or more instances of Illuminate\Contracts\Validation\Rule
 * 
 * Examples:
 *  #[Rule('required|min:6')]
 *  #[Rule(['required','numeric','min:10'])]
 *  #[Rule(new MatchesSecretCode())]
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class Rule
{
    /**
     * The final standardized ruleset.
     * Can contain strings ('required') and objects (new MyCustomRule).
     * 
     * مخزن نهایی قوانین استاندارد شده.
     *
     * Normalized rules array (keeps original items: strings, arrays, Rule objects)
     *
     * @var array<int, mixed>
     */
    protected array $rules = [];

    /**
     * Smart constructor with Variadic Arguments support, now accepting custom Rule objects.
     * It flattens all inputs into a single, unified array.
     * 
     * کانستراکتور هوشمند با پشتیبانی از Variadic Arguments.
     * تمام ورودی‌ها را می‌بلعد و به یک آرایه تخت تبدیل می‌کند.
     * 
     * @param string|array|LaravelRule ...$rules
     */
    public function __construct(string|array|LaravelRule ...$rules)
    {
        // Normalize into a flat array where each element is either:
        foreach ($rules as $rule) {
            if (is_string($rule)) {
                // Handle pipe-separated strings
                if (str_contains($rule, '|')) {
                    // اگر استرینگ پایپ‌دار بود، تبدیل به آرایه کن
                    $this->rules = array_merge($this->rules, explode('|', $rule));
                } else {
                    $this->rules[] = $rule;
                }
            } elseif (is_array($rule)) {
                // اگر آرایه بود، آن را مرج کن (Flatten)
                $this->rules = array_merge($this->rules, $rule); // Flatten arrays
            } elseif ($rule instanceof LaravelRule) {
                // --- THIS IS THE NEW LOGIC ---
                // If it's a Laravel Rule object, add it directly.
                // The Laravel Validator knows how to handle these objects.
                $this->rules[] = $rule;
            }
        }
    }

    /**
     * Returns the final array, ready to be fed into Validator::make.
     * 
     * خروجی نهایی به صورت آرایه استاندارد لاراول.
     * آماده برای خوراک دادن به Validator::make.
     * 
     * Return the raw normalized rules array.
     * @return array<int, mixed>
     */
    public function toArray(): array
    {
        return $this->rules;
    }
}
