<?php

namespace KrubiK\Middlewares;
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
use KrubiK\Krubot;
use KrubiK\Helpers\AmethystMatrix;

class ConversationMiddleware
{
    public function handle(Krubot $bot, Closure $next)
    {
        $bot->continueConversation();
        $conv = $bot->getActiveConversation();

        if (!$conv || !method_exists($conv, 'getStopPatterns')) {
            return $next($bot);
        }

        $text = $bot->text();

        foreach (['stop', 'skip'] as $type) {
            $patterns = $type === 'stop' ? $conv->getStopPatterns() : $conv->getSkipPatterns();
            foreach ($patterns as $pattern => $condition) {

                // skip pattern completely if ss-state-flag is disabled
                $active = $conv->{"check" . ucfirst($type) . "Pattern"}($pattern);
                if (!$active)
                    continue;

                $matched = (str_starts_with($pattern, '/') && preg_match($pattern, $text))
                    || $text === $pattern;

                if ($matched) {
                    $callback = is_callable($condition) ? $condition : $conv->getMrYesMan();
                    // check condition logic
                    $shouldTrigger = call_user_func($callback, $bot);

                    if ($shouldTrigger) {
                        if ($type === 'stop') {
                            $bot->stopConversation();
                        } else {
                            $bot->skipConversation();
                        }
                        return; // terminate pipeline
                    }
                }
            }
        }

        return $next($bot);
    }
}
