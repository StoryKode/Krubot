<?php

namespace KrubiK\Drivers\Strategies;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Implements the CallStrategy for making direct, real-time HTTP requests to the Telegram Bot API.
 * Supports HTTP/HTTPS proxy via constructor config or environment variables.
*/
class DirectApiCallStrategy implements CallStrategy
{
    public function __construct(
        private readonly string $apiBaseUri,
        private readonly string $botToken,
        private readonly ?string $proxy = null,          // مثلاً 'http://127.0.0.1:8585'
        private readonly array $httpOptions = []         // گزینه‌های اضافی Guzzle/Laravel Http
    ) {}

    public function handle(string $method, array $parameters): Response
    {
        $url = rtrim(str_replace('/bot', '', $this->apiBaseUri), '/') . "/bot{$this->botToken}/{$method}";

        $request = $this->buildRequest();

        return $request->post($url, $parameters);

        dd([$method, $parameters, $url, $request, $request->post($url, $parameters)]);
    }

    /**
     * ساخت PendingRequest با پشتیبانی کامل از پروکسی
    */
    protected function buildRequest(): PendingRequest
    {
        // timeoutهای منطقی (قابل override از $httpOptions)
        $options = array_merge([
            'timeout'         => 30,
            'connect_timeout' => 15,
            'http_errors'     => false,   // خودتان وضعیت را چک کنید
        ], $this->httpOptions);

        // پروکسی‌ای که از کانفیگ آمده
        $proxy = $this->proxy;

        if (!empty($proxy)) {
            // Laravel Http از Guzzle استفاده می‌کند و این شکل کاملاً پشتیبانی می‌شود
            $options['proxy'] = $proxy;

            // برای ویندوز ۷ + برخی نسخه‌های قدیمی‌تر cURL این گزینه خیلی کمک می‌کند
            /*$options['curl'] = array_merge($options['curl'] ?? [], [
                CURLOPT_HTTPPROXYTUNNEL => true,
                CURLOPT_PROXY           => $proxy,
            ]);*/
        }

        return Http::withOptions($options);
    }
}