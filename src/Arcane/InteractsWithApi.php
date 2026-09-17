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

use KrubiK\Helpers\AmethystMatrix; // ⚡ Import the Sorceress
use KrubiK\Drivers\Strategies\DeferredResponse;
use KrubiK\Drivers\Contracts\MultiverseEnforcer;
use KrubiK\Helpers\JackPoint; // Import "JackPoint" - The Tactical EventHook System

trait InteractsWithApi
{
 
    public function dispatchApi(string $method, array $params = [], ?MultiverseEnforcer $driver = null): array|DeferredResponse
    {
        return $this->pulseApi($method, $params, $driver);
    }

    public function pulseApi(string $method, array $params = [], ?MultiverseEnforcer $driver = null): array|DeferredResponse
    {
        // ⚡ Veto / Mock gate
        $verdict = JackPoint::fire('api.dispatch.before', $method, $params, $driver, $this);
        if ($verdict === false) {
            return ['ok' => false, 'error' => 'vetoed_by_plugin'];
        }
        if ($verdict instanceof DeferredResponse || is_array($verdict)) {
            return $verdict;
        }

        $driver ??= $this->core();
        $driver = JackPoint::transform('api.dispatch.driver', $driver, $method, $params, $this);

        /*if(!$driver) {
            throw new \Exception('pulseApi Error: Driver not Booted Up Yet!');
        }*/

        $baseUrl = null;
        if($driver && method_exists($driver, 'getBaseUrl'))
            $baseUrl = $driver->getBaseUrl();
        else {
            // Consume 'base_url'
            if(isset($params['base_url'])) {
                $baseUrl = $params['base_url'];
                unset($params['base_url']);
            }
            elseif(isset($params['baseUrl'])) {
                $baseUrl = $params['baseUrl'];
                unset($params['baseUrl']);
            }
        }

        $baseUrl = JackPoint::transform('api.dispatch.base_url', $baseUrl, $driver, $method, $this);

        if(!$baseUrl) {
            throw new \Exception('pulseApi Error: $baseUrl can\'t be determined');
        }

        $url = $baseUrl . $method;
        $url = JackPoint::transform('api.dispatch.url', $url, $method, $params, $this);

        $retry = 0;
        $max_retries = config('krubot.http.max_retries', 3);
        while ($retry < $max_retries) {
            $ch = curl_init($url);
            try {

                $verdict = JackPoint::fire('api.dispatch.attempt', $retry, $url, $method, $params, $this);
                if ($verdict === false) {
                    return ['ok' => false, 'error' => 'vetoed_by_plugin'];
                }
                if ($verdict instanceof DeferredResponse || is_array($verdict)) {
                    return $verdict;
                }

                $paramsStr = json_encode($params);
                if($paramsStr == '[]')
                    $paramsStr = '';

                $headersArr = [
                    'Content-Type: application/json',
                    'Cache-Control: no-cache, no-store, must-revalidate',
                    'Pragma: no-cache',
                    'Expires: 0'
                ];

                $headersArr = JackPoint::transform('api.dispatch.headers', $headersArr, $url, $method, $params, $this);

                $curlOptions = [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_HTTPHEADER => $headersArr,
                    CURLOPT_POSTFIELDS => $paramsStr,
                    CURLOPT_TIMEOUT => 15,
                ];

                $curlOptions = JackPoint::transform('api.dispatch.curl.options', $curlOptions, $url, $method, $params, $this);

                curl_setopt_array($ch, $curlOptions);

                // --- Advanced Options for Ultimate Freshness ---

                // 1. Force a new TCP connection and disable connection reuse.
                // This ensures you are not talking over an old, possibly stale connection.
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                curl_setopt($ch, CURLOPT_FORBID_REUSE, true);

                // 2. Set a low timeout to avoid getting stuck on a non-responsive server.
                curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 seconds timeout

                $response = curl_exec($ch);
    
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($response === false) {
                    $err = curl_error($ch);
                    throw new \Exception("cURL error: {$err}");
                }

                if ($httpCode >= 200 && $httpCode < 300) {
                    curl_close($ch);

                    $decoded = json_decode($response, true) ?? [];
                    $decoded = JackPoint::transform('api.resolve.response', $decoded, $response, $httpCode, $url, $method, $params, $this);

                    JackPoint::fire('api.dispatch.success', $decoded, $httpCode, $method, $this);

                    return $decoded;
                }

                throw new \Exception("API Error: HTTP {$httpCode} - " . ($response ?: 'No response'));
            } catch (\Exception $e) {
                curl_close($ch);
                $retry++;

                $shouldRetry = ($retry < $max_retries);

                $verdict = JackPoint::fire('api.dispatch.error', $e, $retry, $shouldRetry, $url, $method, $params, $this);

                if (!$shouldRetry) {
                    if($verdict !== false)
                        throw $e;
                }
                usleep(767_767);
            }
        }

        JackPoint::fire('api.dispatch.failed', $method, $params, $this);

        return ['ok' => false, 'error' => 'Request failed'];
    }

    /**
     * The Bridge Method.
     * Executes requests by accessing the private 'apiRequest' method of the parent class.
     * 
     * Note: Previously handled via local ReflectionClass, now delegated to InteractsWithLockedProperties
     * to maintain cleaner code architecture.
     *
     * @param string $method API Method name
     * @param array $params API Parameters
     * @return array JSON decoded response
    */
    protected function makeRequest(string $method, array $params = []): array|DeferredResponse
    {

        // ⚡ Veto / Mock gate
        $verdict = JackPoint::fire('api.request.before', $method, $params, $this);
        if ($verdict === false) {
            return ['ok' => false, 'error' => 'vetoed_by_plugin'];
        }
        if ($verdict instanceof DeferredResponse || is_array($verdict)) {
            return $verdict;
        }

        // [Original Logic Explanation]:
        // چون Krubot از Bot ارث‌بری کرده، متد private در کلاس پدر (RubikaBot\Bot) تعریف شده است.
        // ما باید دقیقاً روی کلاس پدر Reflection بزنیم.
        // متد را روی $this (که همان Krubot است) اجرا می‌کنیم.

        // [Refactored Logic]:
        // ما به جای Reflection مستقیم در اینجا، از متد کمکی تریت استفاده می‌کنیم.
        //// return $this->forceCallMethod('apiRequest', [$method, $params], $this->core()); // OldName Was : forceCallParentMethod

        if(isset($params['chat_keypad']) || isset($params['inline_keypad'])) {
            $myChatKeypad = $params['chat_keypad'] ?? null;
            if($myChatKeypad) {

                if(isset($myChatKeypad['keyboard']))
                    unset($params['chat_keypad']['keyboard']);

                if(isset($myChatKeypad['selective']))
                    unset($params['chat_keypad']['selective']);
            }
        }

        $core = $this->core();
        $result = $core->makeRequest($method, $params);

        AmethystMatrix::debug($method.'() has been #called with', [$params, 'so' => $result]);

        // Ensure strict return type compliance + actionable error
        if (!(is_array($result) || $result instanceof DeferredResponse)) {
            $targetClass = is_object($core) ? $core::class : get_debug_type($core);

            throw new \RuntimeException(sprintf(
                "Krubot::makeRequest() expected array|DeferredResponse from %s::makeRequest(), got %s. Method=%s",
                $targetClass,
                get_debug_type($result),
                $method
            ));
        }

        return $result;
    }
}
