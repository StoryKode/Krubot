<?php

namespace KrubiK\Helpers;
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

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Arr;
use Generator;
use Symfony\Component\Finder\Finder;
use RuntimeException;
use Throwable;

/**
 * A Hyper-DX, chainable, and SAPI-aware service for interacting with PHP OPcache.
 * This class abstracts away the complexity of separate CLI and Web OPcache instances.
*/
class OpcacheRuler
{
    use Macroable;

    protected string $targetSapi = 'current'; // 'current', 'web', or 'cli'
    protected bool $isAvailable;

    public function __construct()
    {
        $this->isAvailable = function_exists('opcache_get_status');
    }
    
    /**
     * Set the target SAPI context for the next operation.
     * This enables a fluent, chainable interface.
     *
     * @param string $sapi ('web' or 'cli')
     * @return self
     */
    public function on(string $sapi): self
    {
        if (!in_array($sapi, ['web', 'cli'])) {
            throw new \InvalidArgumentException("Invalid SAPI target. Must be 'web' or 'cli'.");
        }
        $this->targetSapi = $sapi;
        return $this;
    }

    /**
     * A Hyper-DX, dynamic, and high-performance magic invokable method.
     * Acts as an intelligent proxy to other methods within this service.
     * This is the engine of the `opcache()` global helper.
     *
     * @param mixed ...$parameters The dynamic parameters for the call.
     * @return mixed The result of the called method, or the instance itself for chaining.
     *
     * @throws \BadMethodCallException If the method does not exist or is not public.
    */
    public function __invoke(...$parameters)
    {
        // Case 1: No parameters passed.
        // opcache()
        // Returns the service instance itself, enabling method chaining.
        if (empty($parameters)) {
            return $this;
        }

        // Case 2: The first parameter is explicitly null.
        // opcache(null)
        // This is a special, high-performance shortcut to flush the OPcache.
        if ($parameters[0] === null) {
            return $this->flush();
        }

        // Case 3: Dynamic method invocation.
        // The first parameter must be a string representing the method name.
        $method = $parameters[0];
        if (!is_string($method)) {
            throw new \InvalidArgumentException('When invoking the Opcache service with parameters, the first parameter must be the method name (string) or null.');
        }

        // Security and robustness: ensure the method exists, is public, and is not this __invoke method.
        if (!is_callable([$this, $method]) || strtolower($method) === '__invoke') {
            throw new \BadMethodCallException(sprintf(
                'Method %s::%s does not exist or is not publicly callable.',
                static::class,
                $method
            ));
        }

        // Extract the arguments for the target method.
        $args = array_slice($parameters, 1);
        $argCount = count($args);

        // Case 3a: The method is called with no arguments.
        // e.g., opcache('config')
        if ($argCount === 0) {
            return $this->{$method}();
        }
        
        // Case 3b: The method is called with a single array argument.
        // This could be for named parameters or just a single array parameter.
        // e.g., opcache('invalidate', ['file' => '/path/to/script.php'])
        if ($argCount === 1 && is_array($args[0]) && Arr::isAssoc($args[0])) {
            // Use Reflection to map the associative array to named arguments.
            // This is extremely powerful for DX.
            $reflection = new ReflectionMethod($this, $method);
            $finalArgs = [];
            foreach ($reflection->getParameters() as $param) {
                $paramName = $param->getName();
                if (isset($args[0][$paramName])) {
                    $finalArgs[] = $args[0][$paramName];
                } elseif ($param->isDefaultValueAvailable()) {
                    $finalArgs[] = $param->getDefaultValue();
                } else {
                    throw new \ArgumentCountError("Missing required parameter: \${$paramName} for method {$method}");
                }
            }
            return $this->{$method}(...$finalArgs);
        }

        // Case 3c: The method is called with multiple positional arguments.
        // e.g., opcache('invalidate', '/path/to/script.php', true)
        return $this->{$method}(...$args);
    }

    /**
     * Get status, or check if a specific script is cached.
     * Opcache::status() -> Get full status array.
     * Opcache::status('path/to/file.php') -> Check if file is cached (bool).
     *
     * @param string|null $scriptPath
     * @return array|bool|null
     */
    public function status(string $scriptPath = null)
    {
        if (!$this->ensureAvailability()) return $scriptPath ? false : null;

        if ($scriptPath) {
            return $this->isCached($scriptPath);
        }

        return $this->execute('status');
    }

    /**
     * Get the OPcache configuration directives.
     *
     * @return array|null
     */
    public function config(): ?array
    {
        if (!$this->ensureAvailability()) return null;
        return $this->execute('config');
    }

    /**
     * Flushes the entire OPcache. This is the primary method for invalidation.
     * Chainable with sapi(). Example: $opcache->sapi('web')->flush();
     *
     * @return bool
    */
    public function reset(): bool
    {
        if (!$this->ensureAvailability()) return false;
        return $this->execute('reset')['success'] ?? false;
    }
    /**
     * alias for Invalidate the entire OPcache.
     *
     * @return bool
     */
    public function flush(): bool
    {
        return $this->reset();
    }

    /**
     * Invalidate a specific script from the OPcache.
     *
     * @param string $scriptPath The absolute path to the script.
     * @return bool
     */
    public function invalidate(string $scriptPath): bool
    {
        if (!$this->ensureAvailability()) return false;
        $realPath = realpath($scriptPath);
        if (!$realPath) return false;

        return $this->execute('invalidate', ['file' => $realPath])['success'] ?? false;
    }
    
    /**
     * Pre-compiles a script into OPcache without executing it.
     * This is the engine for preloading. It always runs on the current SAPI.
     *
     * @param string $scriptPath
     * @return bool
     */
    public function compile(string $scriptPath): bool
    {
        if (!$this->ensureAvailability()) return false;
        $realPath = realpath($scriptPath);
        if (!$realPath) return false;
        return opcache_compile_file($realPath);
    }

    /**
     * Check if a specific script is cached.
     *
     * @param string $scriptPath
     * @return bool
     */
    protected function isCached(string $scriptPath): bool
    {
        if (!$this->ensureAvailability()) return false;
        $realPath = realpath($scriptPath);
        if (!$realPath) return false;
        
        return $this->execute('check', ['file' => $realPath])['is_cached'] ?? false;
    }

    /**
     * "Refreshes" a script in OPcache by first invalidating and then recompiling it.
     * This is a convenient, atomic operation for ensuring the latest version of a file is cached.
     * This method is SAPI-aware and will target the correct OPcache instance (CLI or Web)
     * if the `on()` method was called previously.
     *
     * @param string $scriptPath The absolute path to the script.
     * @return self
    */
    public function fresh(string $scriptPath): self
    {

        $this->invalidate($scriptPath);
        $this->compile($scriptPath);

        return $this;
        
        /* -Obsolete- ::
            @return array An array containing the success status of both operations:
        //               ['invalidated' => bool, 'compiled' => bool]

            // We can't do anything if opcache is not on.
            if (!$this->ensureAvailability()) {
                return ['invalidated' => false, 'compiled' => false];
            }

            $realPath = realpath($scriptPath);
            if (!$realPath) {
                return ['invalidated' => false, 'compiled' => false];
            }

            // We leverage our own SAPI-aware methods! This is the beauty of the design.
            // `invalidate` will respect any prior `->on('web')` call.
            $invalidationSuccess = $this->invalidate($realPath);
            
            // `compile` always runs on the current SAPI, which is exactly the behavior
            // we want. If called via the bridge, this code executes within the FPM worker.
            $compilationSuccess = opcache_compile_file($realPath);

            return [
                'invalidated' => $invalidationSuccess,
                'compiled' => $compilationSuccess
            ];
        */

    }

    /**
     * Recursively find PHP files in a directory and compile them into OPcache.
     * Returns a Generator yielding the result for each file.
     * This is a high-performance, low-memory approach.
     *
     * @param string $directory The absolute path to the directory.
     * @param array $options {
     *     @var bool $invalidateFirst Whether to invalidate the script before compiling. Default: true.
     *     @var array $excludeSuffixes Suffixes to exclude (e.g., ['.disabled', '.off']). Default: [].
     * }
     * @return Generator Yields ['file' => string, 'success' => bool, 'invalidated' => ?bool]
    */
    public function warmDirectory(string $directory, array $options = []): Generator
    {
        if (!$this->ensureAvailability()) {
            return; // Yields nothing if OPcache is not available.
        }

        $realPath = realpath($directory);
        if (!$realPath || !is_dir($realPath)) {
            throw new \InvalidArgumentException("Directory not found or is not a directory: {$directory}");
        }
        
        // Default options
        $invalidateFirst = $options['invalidateFirst'] ?? true;
        $excludeSuffixes = $options['excludeSuffixes'] ?? [];

        // Use Symfony Finder for a powerful and readable way to find files.
        // Laravel includes this component, so it's readily available.
        $finder = new Finder();
        $finder->files()
            ->in($realPath)
            ->name('*.php');
            
        // Add exclusion logic if any suffixes are provided.
        foreach ($excludeSuffixes as $suffix) {
            $finder->notName('*' . $suffix);
        }
        
        // We are now working on the CURRENT SAPI context.
        // The `on('web')` must be called *before* this method.
        $targetExecutor = function(string $action, array $payload = []) {
            // This closure captures the execution context (local or bridge)
            return $this->execute($action, $payload);
        };
        
        foreach ($finder as $file) {
            $filePath = $file->getRealPath();
            $result = ['file' => $filePath, 'success' => false, 'invalidated' => null];

            try {
                if ($invalidateFirst) {
                    // We use our awesome SAPI-aware execution logic here!
                    $invalidationResult = $targetExecutor('invalidate', ['file' => $filePath]);
                    $result['invalidated'] = $invalidationResult['success'] ?? false;
                }
                
                // Compile always runs locally on the target SAPI. If we are on the bridge,
                // this `compile` call happens inside the FPM worker, which is what we want.
                // If we are on CLI targeting CLI, it happens right here.
                $result['success'] = opcache_compile_file($filePath);

            } catch (\Throwable $e) {
                // In case of any error, we mark it as failed but continue with the next file.
                $result['success'] = false;
            }
            
            yield $result;
        }
    }

    /**
     * Warms up a directory by compiling all its PHP files synchronously and returns the full report.
     * This method is a one-line cosmic harmony, leveraging the asynchronous power of its generator-based sibling.
     *
     * @param string $directory The absolute path to the directory to warm up.
     * @param array $options    An associative array of options, passed directly to warmDirectory.
     * @return array            An array containing the results for every file processed.
    */
    public function warmSync(string $directory, array $options = []): array
    {
        //// return iterator_to_array($this->warmDirectory($directory, $options), false);

        // Boost Nitrogen-Speed utilizing php Spread Operator =>
        return [...$this->warmDirectory($directory, $options)];
    }

    /**
     * The core execution logic. Determines whether to run locally or via the HTTP bridge.
     */
    private function execute(string $action, array $payload = [])
    {
        $sapi = php_sapi_name();
        
        // Determine if we need to cross the SAPI bridge.
        // We cross if the user explicitly asks for 'web' and we are currently in 'cli'.
        $needsBridge = ($this->targetSapi === 'web' && $sapi === 'cli');
        
        // Reset target SAPI for the next call.
        $this->targetSapi = 'current';

        if ($needsBridge) {
            return $this->executeViaBridge($action, $payload);
        }
        
        // Execute directly on the current SAPI.
        return $this->executeLocally($action, $payload);
    }

    /**
     * Executes an OPcache function on the CURRENT SAPI.
     */
    private function executeLocally(string $action, array $payload)
    {
        switch ($action) {
            case 'status': return opcache_get_status(true);
            case 'config': return opcache_get_configuration();
            case 'reset': return ['success' => opcache_reset()];
            case 'invalidate': return ['success' => opcache_invalidate($payload['file'], true)];
            case 'check': return ['is_cached' => opcache_is_script_cached($payload['file'])];
            default: throw new RuntimeException("Unknown local OPcache action: {$action}");
        }
    }
    
    /**
     * Executes an OPcache function on the Web SAPI via the HTTP bridge.
     */
    private function executeViaBridge(string $action, array $payload)
    {
        try {
            $response = Http::withHeaders(['X-Opcache-Secret' => config('krubot.cache.opcache.bridge_secret')])
                ->timeout(15)
                ->post(url(config('krubot.cache.opcache.bridge_uri')), [
                    'action' => $action,
                    ...$payload
                ]);

            if (!$response->successful()) {
                throw new RuntimeException("OPcache Bridge Error: " . $response->body());
            }

            return $response->json('data');

        } catch (Throwable $e) {
            throw new RuntimeException("Failed to contact OPcache Bridge: " . $e->getMessage(), 0, $e);
        }
    }

    private function ensureAvailability(): bool
    {
        if (!$this->isAvailable) {
            // throw new RuntimeException('OPcache extension is not available or enabled.');
            // Silent-fail if opacache extension is disabled or misconfigured
            return false;
        }
        return true;
    }
}
