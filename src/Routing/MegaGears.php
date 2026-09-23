<?php

namespace KrubiK\Routing;
/*
| Krubot BotEngine: The Architect's Lexicon [×vRC.9×] 🚀📜
|--------------------------------------------------------------------------
| This is **a Playground For Mastery**, a laboratory of ***Software Dev Artistry***;
| not a weapon for production's final battles.
|
| Our Bond: ***"Rebuilding The Rebellion"*** Within S.N.P. (The Foundation of Pure Power & Revel).
| Your Mandate [MIT]: Deconstruct Krubot. Command it. Master it. You are The Architect Now!
|
| *Go build something revolutionary!* 💜⚡️
*/

use KrubiK\Helpers\JackPoint; // Import "JackPoint" - The Tactical Event Cortex
use Traversable;
use Illuminate\Contracts\Support\Arrayable;

use InvalidArgumentException;

/**
 * 🌌 [ MEGA GEARS ] ⚙️ THE CYBER-NEXUS EXECUTION ENGINE🦾 
 * 
 * The supreme cybernetic nervous system and central routing conduit of the KrubiK architecture. 
 * Forged in the orbital foundries and optimized for zero-latency execution, this trait 
 * serves as the ultimate tactical drive for narrative programming. It seamlessly intertwines 
 * High-Velocity Intent Resolution, Quantum State Memory (`now`), and Context-Safe Middleware 
 * Pipelines into an unstoppable, hyper-threaded routing matrix.
 * 
 * ⚡ CURRENT DIRECTIVES & SYSTEMS:
 * [X] UNIVERSAL INTENT RESOLVER - O(1) hyper-speed target acquisition and precision parameter injection.
 * [X] THE ULTIMATE ROUTER (GO)  - Bulletproof execution matrix with deep-stack context restoration and fallback pipelines.
 * [X] JACKPOINT INTEGRATION     - Tactical EventHooks deployed across all critical neural pathways.
 * [X] NARRATIVE STATE CONTROL   - Fluent, fluid, and hyper-dynamic user memory manipulation.
 * 
 * "Engage the thrusters, override the limiters, and let the MegaGears grind the fabrics of the server."
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.9×
 * @license MIT
*/
trait MegaGears
{
    /**
     * ⚡️ THE UNIVERSAL INTENT RESOLVER (v4.6 - HyperDX/HyperPerformant Edition) ⚡️
     * Translates the developer's will into a concrete URL or Command string with absolute precision.
     * It intelligently separates path parameters from query parameters for web routes.
     *
     * @param string|array $target The route identifier:
     *                             - ['ClassName', 'methodName']  (Handler Array)
     *                             - 'route.name'                 (Custom Name from #[Name])
     *                             - 'web.path.name'              (WebPage/WebAction Name)
     *                             - 'command'                    (Command trigger, e.g., 'start')
     * @param array $params Key-value pairs for ALL required and optional parameters.
     *                      e.g., ['productId' => 123, 'utm_source' => 'menu']
     * @return string The fully resolved, ready-to-use URL or Command string.
     * @throws InvalidArgumentException If the target is not found or a required parameter is missing.
    */
    public function resolvePattern(string|array $target, array $params = []): string
    {
        // -----------------------------------------------------------------
        // PHASE 1: UNIFIED O(1) LOOKUP - Find the Route object instantly.
        // -----------------------------------------------------------------
        $route = null;

        if (is_array($target)) {

            // Lookup by Handler: ['ClassName', 'methodName']
            $handlerKey = trim(
                (($target[0] ?? '') . '::' . ($target[1] ?? '')),
            ':');

            $route = $this->handlerToRouteMap[$handlerKey] ?? null;

        } elseif (is_string($target)) {

            // Lookup by Name/Path/Command string in a prioritized order
            // Priority: Custom Name > Web Path > Command
            $route = $this->namedRoutes[$target]
                ?? $this->webPathToRouteMap[$target]
                ?? $this->commandToRouteMap[$target]
                ?? null;

        }

        if (!$route) {
            $id = is_array($target) ? implode('::', $target) : $target;
            throw new InvalidArgumentException("Unresolved Path: The route target '{$id}' is not defined in any integrated Nexus.");
        }

        // -----------------------------------------------------------------
        // PHASE 2: PATTERN & PARAMETER PREPARATION
        // -----------------------------------------------------------------
        $pattern = $route->getPattern();
        
        // For Bot Commands (non-web), the pattern might have internal prefixes. Clean them up.
        // This is crucial for commands like `onAction` which uses 'CBK::'.
        if (!in_array($route->type, [self::RT_WEB_APP, self::RT_WEB_PAGE, self::RT_WEB_ACTION], true)) {
            $pos = strpos($pattern, '::');
            if ($pos !== false) {
                $pattern = substr($pattern, $pos + 2);
            }
        }
        
        // For command routes, patterns might start with '/'.
        // For the final string, we might not want it, depending on the platform.
        // Let's assume for now we keep it as it is, as it's part of the defined pattern.
        // Example: onCommand('start') -> '/start'. resolvePattern should return '/start'.

        // All provided parameters start in the query pool.
        // We will pull them out as we inject them into the path.
        $queryParams = $params;

        // Get the list of expected path parameters that we discovered during `integrateNexus`.
        $pathParameters = $route->pathParameters ?? [];

        // -----------------------------------------------------------------
        // PHASE 3: PATH PARAMETER INJECTION
        // -----------------------------------------------------------------
        foreach ($pathParameters as $paramName) {
            $placeholder = '{' . $paramName . '}';
            $optionalPlaceholder = '{' . $paramName . '?}';

            $hasPlaceholder = str_contains($pattern, $placeholder);
            $hasOptionalPlaceholder = str_contains($pattern, $optionalPlaceholder);

            if ($hasPlaceholder || $hasOptionalPlaceholder) {
                if (array_key_exists($paramName, $params)) {
                    // Parameter provided, inject it into the path.
                    // For URLs, we MUST encode. For bot commands, we can use the raw value.
                    $value = (string) $params[$paramName];
                    $encodedValue = $route->type >= self::RT_WEB_APP ? rawurlencode($value) : $value;
                    
                    if ($hasPlaceholder) {
                        $pattern = str_replace($placeholder, $encodedValue, $pattern);
                    } else {
                        $pattern = str_replace($optionalPlaceholder, $encodedValue, $pattern);
                    }

                    // It's a path parameter, so remove it from the query string pool.
                    unset($queryParams[$paramName]);
                } else {
                    // Parameter NOT provided.
                    // If it's required (non-optional placeholder), this is a fatal error.
                    if ($hasPlaceholder) {
                        $id = is_array($target) ? implode('::', $target) : $target;
                        throw new InvalidArgumentException("Missing required path parameter '{$paramName}' for route target '{$id}'.");
                    }
                }
            }
        }
        
        // -----------------------------------------------------------------
        // PHASE 4: CLEANUP & QUERY STRING ASSEMBLY (for Web Routes)
        // -----------------------------------------------------------------
        
        // Remove any unfilled optional parameter placeholders (e.g., /path/to/{optional?})
        $finalUrl = preg_replace('/\/\{[a-zA-Z0-9_]+\?\}/', '', $pattern);
        
        // For web routes, append any remaining parameters as a query string.
        if ($route->type >= self::RT_WEB_APP && !empty($queryParams)) {
            // Append with '?' or '&' depending on whether a query string already exists.
            $separator = str_contains($finalUrl, '?') ? '&' : '?';
            $finalUrl .= $separator . http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
        }

        return $finalUrl;
    }

    /**
     * ⚡ REVERSE ROUTING HELPER
     * Find the raw pattern/command for a named route and inject parameters.
     * 
     * Example: resolvePattern('product.show', ['id' => 50]) => "/product 50"
     * 
     * @param string $name The route name defined via ->name()
     * @param array $params Key-value pairs to replace in the pattern
     * @return string|null The ready-to-use command string or null if not found.
    */
    public function resolvePattern_alpha7(string $name, array $params = []): ?string
    {
        // 1. Lookup Route
        if (!isset($this->namedRoutes[$name])) {
            return null;
        }
        
        /** @var \KrubiK\Routing\Route $route */
        $route = $this->namedRoutes[$name];
        $pattern = $route->pattern; // e.g. "/product {id}"
        
        // 2. Return raw if no params
        if (empty($params)) {
            return $pattern;
        }
        
        // 3. Inject Parameters
        foreach ($params as $key => $value) {
            // Handles {id} and :{id} variations
            $pattern = str_replace(
                ['{' . $key . '}', ':{' . $key . '}'], 
                $value, 
                $pattern
            );
        }
        
        return $pattern;
    }

    // =========================================================================
    //  🚀 CORE EXECUTION ENGINE: THE "GO" SYSTEM (v8.1 ULTIMATE)
    // =========================================================================

    /**
     * 🚀 GO v8.1 (THE ULTIMATE ROUTER): Execute a named route immediately.
     * Performs an internal forward with complete context safety, dynamic middleware injection,
     * and robust error handling.
     *
     * 💎 Consolidated Powers:
     * 1. **Context Safety:** Backs up & restores caller state (Safe for nested calls).
     * 2. **Tri-State Middleware:**
     *    - `true`: Run target's original stack (Default).
     *    - `false`: Run NO middleware (Direct Action - Fastest).
     *    - `array`: Run a custom injected stack for this call only.
     * 3. **Hybrid Logging:** Detects environment for proper error reporting without crashing.
     * 4. **Native Pipeline:** Supports Aliases, Closures, Invokables, and Classes without Laravel dependency.
     * 5. **Smart Parameter Merging:** Inherits caller params unless overwritten.
     *
     * @param string $routeName The name defined via ->name('...')
     * @param array $params Parameters to inject/overwrite (e.g. ['id' => 5])
     * @param bool|array $middlewareStrategy Strategy for middleware execution (Default: true)
     * @return mixed The result of the executed action or null if failed/blocked.
    */
    public function go(string $routeName, array $params = [], bool|array $middlewareStrategy = true): mixed
    {
        // ---------------------------------------------------------------------
        // 1. LOOKUP & VALIDATION (O(1) HashMap Access)
        // ---------------------------------------------------------------------
        if (!isset($this->namedRoutes[$routeName])) {
            $errorMsg = "Krubot was Unable To Redirect: Route named [{$routeName}] not found.";

            // Intelligent Logging: Check available loggers without crashing
            AmethystMatrix::warning($errorMsg);
            /* if (function_exists('logger')) {
                logger()->warning($errorMsg);
            } elseif (class_exists(Log::class)) {
                Log::warning($errorMsg);
            } */

            // Dev-Mode Feedback: Tell the admin directly in chat
            /* if (config('app.debug') === true) {
                $this->to(XBot::Admins[0], "⚠️ System Error: Route '{$routeName}' not found."); // to() == auto send()
            } */
            return null;
        }

        /** @var Route $route */
        $route = $this->namedRoutes[$routeName];

        // ---------------------------------------------------------------------
        // 2. CONTEXT BACKUP (Save State) 🛡️
        // ---------------------------------------------------------------------
        // We must save the current state because 'go' might be called inside another route.
        // When 'go' finishes, the previous route must continue exactly where it left off.
        $backupHandler = $this->activeRoute;
        $backupParams = $this->currentRouteParams;

        // Prepare New Context: Merge current params with new overrides
        // Priority: New Params > Old Params
        $finalParams = array_merge($this->currentRouteParams ?? [], $params);

        // ---------------------------------------------------------------------
        // 3. EXECUTION BLOCK (The Safe Runner)
        // ---------------------------------------------------------------------
        try {
            // A) Switch Context to the Target Route
            $this->activeRoute = $route;
            $this->currentRouteParams = $finalParams;

            // B) Define the Final Destination (The Action Caller)
            // This closure actually runs the controller method via DI.
            $destination = function ($bot) use ($route, $finalParams) {
                return $this->callAction($route->getAction(), $this->currentMessage, $finalParams);
            };

            // C) DETERMINE MIDDLEWARE STRATEGY
            $stackToRun = [];

            if (is_array($middlewareStrategy)) {
                // MODE: Custom Injection (Run exactly what is passed via argument)
                $stackToRun = $middlewareStrategy;
            } elseif ($middlewareStrategy === true) {
                // MODE: Standard (Run route's own defined stack + Globals)
                // We fetch the calculated stack from the Route object itself.
                $stackToRun = $route->getMiddlewareStack($this->globalMiddlewares);
            }
            // MODE: False -> $stackToRun stays empty (Direct Execution).

            // D) FAST PATH OPTIMIZATION
            // If there are no middlewares to run, skip the pipeline overhead completely.
            if (empty($stackToRun)) {
                return $destination($this);
            }

            // E) RUN THE PIPELINE (Merged & Reinforced)
            
            // Method 1: Laravel Pipeline (Preferred & Most Compatible)
            if (class_exists(Pipeline::class) && function_exists('app')) {
                return app(Pipeline::class)
                    ->send($this)
                    ->through($stackToRun)
                    ->then($destination);
            }

            // Method 2: Native PHP Pipeline (Robust Fallback)
            // Iterates through the stack in reverse, wrapping the destination in onion layers.
            $pipeline = array_reduce(
                array_reverse($stackToRun),
                function ($next, $middleware) {
                    return function ($bot) use ($next, $middleware) {
                        
                        // 1. RESOLVE ALIASES
                        // Allows passing strings like 'auth' instead of full class names.
                        if (is_string($middleware) && property_exists($this, 'middlewareAliases') && isset($this->middlewareAliases[$middleware])) {
                            $middleware = $this->middlewareAliases[$middleware];
                        }

                        // 2. INSTANTIATE & EXECUTE
                        
                        // Case I: String Class Name
                        if (is_string($middleware) && class_exists($middleware)) {
                            $instance = new $middleware;
                            
                            // Sub-Case: Standard 'handle' method
                            if (method_exists($instance, 'handle')) {
                                return $instance->handle($bot, $next);
                            } 
                            // Sub-Case: Invokable Class (__invoke)
                            elseif (is_callable($instance)) {
                                return $instance($bot, $next);
                            }
                        }

                        // Case II: Closure Middleware
                        if ($middleware instanceof \Closure) {
                            return $middleware($bot, $next);
                        }

                        // Case III: Instantiated Object
                        if (is_object($middleware)) {
                            if (method_exists($middleware, 'handle')) {
                                return $middleware->handle($bot, $next);
                            } elseif (is_callable($middleware)) {
                                return $middleware($bot, $next);
                            }
                        }

                        // Fail-Safe: If middleware is invalid/unresolvable, don't crash.
                        // Just proceed to the next step.
                        return $next($bot);
                    };
                },
                $destination
            );

            // Ignite the Native Pipeline
            return $pipeline($this);

        } finally {
            // -----------------------------------------------------------------
            // 4. CONTEXT RESTORE (Restore State) 🛡️
            // -----------------------------------------------------------------
            // This runs ALWAYS, even if the destination controller throws an Exception.
            // Ensures the bot never gets stuck in the "wrong" route context.
            $this->activeRoute = $backupHandler;
            $this->currentRouteParams = $backupParams;
        }
    }

    /**
     * Sets, updates, or flushes a user's current state(s) for Narrative Programming.
     * This is the primary "write" method for the #[When] attribute's "read" logic.
     *
     * It's designed to be a fluent, intuitive, and powerful interface for state management.
     * Supports single key/value, batch operations (via array, Arrayable, or Traversable),
     * and null-based deletion within batches.
     *
     * @param string|array|Arrayable|Traversable $stateKey The key for the state (e.g., 'level')
     *                                                     OR an associative data structure of states to set or flush.
     *                                                     e.g., collect(['level' => 10, 'class' => 'Mage', 'old_quest' => null])
     *                                                     In this example, 'level' and 'class' are set, and 'old_quest' is forgotten.
     * @param mixed $value The value to associate with the state if $stateKey is a string.
     *                     - Provide a value (string, int, array, etc.) to set it.
     *                     - Provide NO value (or true) to set a simple existence flag.
     *                     - Provide NULL to completely flush/delete the state.
     *                     This parameter is IGNORED if $stateKey is a batch data structure.
     * @return self Returns the bot instance for method chaining ($this).
    */
    public function now(string|array|Arrayable|Traversable $stateKey, mixed $value = true): self
    {
        // ⚡ دروازه‌ی ورود — veto / reroute
        $verdict = JackPoint::fire('state.now.before', $stateKey, $value, $this->userStorage(), $this);
        if ($verdict === false) {
            return $this;
        }

        // Case 1: Batch Operation (The most flexible path)
        // We check if the input is a data structure intended for batch processing.
        if (is_array($stateKey) || $stateKey instanceof Arrayable || $stateKey instanceof Traversable) {
            
            // --- Data Normalization ---
            // The goal here is to convert any acceptable input type into a standard PHP array
            // so the rest of the logic can work with it consistently.
            $batchData = [];
            if ($stateKey instanceof Arrayable) {
                // Priority 1: If the object explicitly follows Laravel's Arrayable contract,
                // we honor it by calling the toArray() method. This is the most reliable way
                // for objects like Illuminate\Support\Collection.
                $batchData = $stateKey->toArray();
            } elseif (is_array($stateKey)) {
                // Priority 2: A simple, plain array. No conversion needed.
                $batchData = $stateKey;
            } elseif ($stateKey instanceof Traversable) {
                // Priority 3 (Fallback): For any other iterable object (like a custom iterator),
                // we convert it to an array. Collections would also be caught here if not for the
                // Arrayable check above, but checking Arrayable first is more explicit.
                $batchData = iterator_to_array($stateKey);
            }

            // ⚡ پلاگین می‌تواند کل batch را بازنویسی کند
            $batchData = JackPoint::transform('state.now.batch.put', $batchData, $this);

            // --- The Separation Logic ---
            // Now that we have a guaranteed array ($batchData), we can process it.
            // We iterate through the batch data once and separate operations into two groups:
            // 1. dataToSet: for keys that need a value.
            // 2. keysToForget: for keys whose value is explicitly null, signaling deletion.
            // We separate keys for setting/updating from keys for deletion.
            $dataToSet = [];
            $keysToForget = [];
            foreach ($batchData as $key => $val) {
                if ($val === null) {
                    $keysToForget[] = $key;
                } else {
                    $dataToSet[$key] = $val;
                }
            }

            // ⚡ پلاگین می‌تواند split را دستکاری کند (مثلاً key جدید اضافه کند)
            [$dataToSet, $keysToForget] = JackPoint::transform(
                'state.now.batch.split',
                [$dataToSet, $keysToForget],
                $batchData,
                $this
            );

            // Step 1: Perform the batch update/set operation if there's anything to set.
            // This is efficient as it calls `put` (and subsequently `save`) only once for all updates.
            if (!empty($dataToSet)) {

                $dataToSet = JackPoint::transform('state.put.before', $dataToSet, $this);

                // verdict => transformer can clear the array entirely
                if (!empty($dataToSet)) {
                    $this->userStorage()->put($dataToSet);
                    JackPoint::fire('state.put.after', $dataToSet, $this);
                }

            }

            // Step 2: Perform deletions.
            // Instead of a loop, we now make a single, efficient, {SRP|SoC}-Based call.
            if (!empty($keysToForget)) {
                $keysToForget = JackPoint::transform('state.forget.before', $keysToForget, $this);

                // verdict => transformer can clear the array entirely
                if (!empty($keysToForget)) {
                    $this->userStorage()->forget($keysToForget);
                    JackPoint::fire('state.forget.after', $keysToForget, $this);
                }
            }

            JackPoint::fire('state.now.batch.completed', $dataToSet, $keysToForget, $batchData, $this);

        } else {

            // ⚡ کلید و مقدار هر دو قابل transform هستند (single path)
            $stateKey = JackPoint::transform('resolve.state.key', $stateKey, $value, $this);
            $value    = JackPoint::transform('resolve.state.value', $value, $stateKey, $this);

            // Case 2: Single Key/Value Operation (Original Logic)
            // This path remains for single, direct state modifications.
            if ($value === null) {

                $forgetKey = JackPoint::transform('state.forget.before', $stateKey, $this);

                // for example prevent forget system keys
                if ($forgetKey !== -1) {

                    // e.g., $bot->now('is_registering', null); -> Deletes the state.
                    $this->userStorage()->forget($forgetKey);
                    JackPoint::fire('state.forgotten', $forgetKey, $stateKey, $this);

                }

            } else {

                [$putKey, $putValue] = JackPoint::transform(
                    'state.now.put.before',
                    [$stateKey, $value],
                    $this
                );

                if ($putKey !== -1) {

                    // e.g., $bot->now('is_admin'); or $bot->now('level', 99); -> Sets the state.
                    $this->userStorage()->put($putKey, $putValue);
                    JackPoint::fire('state.put.after', $putKey, $putValue, $this);
                    JackPoint::fire('state.written', $putKey, $putValue, $stateKey, $value, $this);

                }

            }
        }

        // ⚡ دروازه‌ی خروج — trace / metrics
        JackPoint::fire('state.now.after', $stateKey, $value, $this->userStorage(), $this);

        // Always return $this to maintain the beautiful fluent API.
        // e.g., $bot->now('level', 10)->now('class', 'Mage')->reply('You are now a Level 10 Mage!')->send();
        return $this;
    }
}
