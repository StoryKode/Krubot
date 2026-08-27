<?php

namespace KrubiK\DTOs;
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

// use Illuminate\Contracts\Validation\Rule;

class FormField
{
    /**
     * @param string $key کلید ذخیره‌سازی در آرایه دیتا (مثلاً 'email')
     * @param string|\KrubiK\Conversations\Question $question متن یا آبجکت سوال
     * @param string|array|null $rules قوانین اعتبارسنجی
     */
    public function __construct(
        public string $key,
        public string $question,
        public string|array|null $rules = null
    ) {}
}
