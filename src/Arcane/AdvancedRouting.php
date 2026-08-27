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

trait AdvancedRouting
{
    // =========================================================================
    //  🔮 THE METAPHYSICAL CORE: CENTRALIZED REFLECTION & AUTO-WIRING (v10.0 Ultimate)
    //  Google/Microsoft Architect Level. Zero DRY Violations. PHP 8.2.30 Optimized.
    // =========================================================================

    // =========================================================================
    //  🔮 THE METAPHYSICAL CORE: CENTRALIZED REFLECTION & AUTO-WIRING
    //  No more DRY violations! Every component uses this core engine.
    // =========================================================================

    /**
     * @var array<string, Route> O(1) Map for Handler lookup [Class::method => Route]
    */
    protected array $handlerToRouteMap = [];

    /**
     * @var array<string, Route> O(1) Map for WebPath lookup [web.path.name => Route]
    */
    protected array $webPathToRouteMap = [];

    /**
     * @var array<string, Route> O(1) Map for Command lookup [command_name => Route]
    */
    protected array $commandToRouteMap = [];

    /**
     * Static Memory Cache for Reflection Data.
     * In RoadRunner/Swoole/Octane, this persists across requests for TRUE O(1) speed!
     * @var array<string, array<string, ReflectionMethod>> 
    */
    private static array $actionMethodCache = [];

    /**
     * ⚡ Universal Action Discoverer (O(1) after first scan).
     * Scans any class ONCE to find the method matching an #[Action] or naming convention.
    */
    public function discoverActionMethod(object $targetInstance, string $actionName): ?ReflectionMethod
    {
        $className = $targetInstance::class;
        $cacheKey = $className . '::' . $actionName;

        // O(1) Memory Cache Return
        if (isset(self::$actionMethodCache[$cacheKey])) {
            return self::$actionMethodCache[$cacheKey];
        }

        $reflection = new ReflectionClass($className);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // 1. Primary Strategy: PHP 8 Attributes
            $attributes = $method->getAttributes(Action::class);
            if (!empty($attributes)) {
                if ($attributes[0]->newInstance()->name === $actionName) {
                    self::$actionMethodCache[$cacheKey] = $method;
                    return $method;
                }
            }

            // 2. Legacy / Fallback Strategy
            $expectedName = 'on' . str_replace('_', '', ucwords($actionName, '_'));
            if ($method->getName() === $expectedName || $method->getName() === $actionName) {
                self::$actionMethodCache[$cacheKey] = $method;
                return $method;
            }
        }

        return null;
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
        $backupHandler = $this->currentResolvedHandler;
        $backupParams = $this->currentRouteParams;

        // Prepare New Context: Merge current params with new overrides
        // Priority: New Params > Old Params
        $finalParams = array_merge($this->currentRouteParams ?? [], $params);

        // ---------------------------------------------------------------------
        // 3. EXECUTION BLOCK (The Safe Runner)
        // ---------------------------------------------------------------------
        try {
            // A) Switch Context to the Target Route
            $this->currentResolvedHandler = $route;
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
            $this->currentResolvedHandler = $backupHandler;
            $this->currentRouteParams = $backupParams;
        }
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
    public function resolvePatternOld(string $name, array $params = []): ?string
    {
        // 1. Lookup Route
        if (!isset($this->namedRoutes[$name])) {
            return null;
        }
        
        /** @var \KrubiK\Router\Route $route */
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
     * @throws \InvalidArgumentException If the target is not found or a required parameter is missing.
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
            throw new \InvalidArgumentException("Unresolved Path: The route target '{$id}' is not defined in any integrated Nexus.");
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
                        throw new \InvalidArgumentException("Missing required path parameter '{$paramName}' for route target '{$id}'.");
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
}
