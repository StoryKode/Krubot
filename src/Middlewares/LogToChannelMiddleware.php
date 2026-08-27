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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;
use KrubiK\Helpers\AmethystMatrix;
use Carbon\Carbon;

class LogIncomingRequest
{
    /**
     * Handle an incoming request.
     * This middleware intercepts, logs, and then passes the request along.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure(\Illuminate\Http\Request) $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // --- The Logging Logic Starts Here ---
        try {
            // 1. Define the log file path. Placing it in storage/logs is standard practice.
            $logFilePath = storage_path('logs/rubika_webhooks.txt');
            $logDirectory = dirname($logFilePath);

            // 2. Ensure the directory exists. This is a robust way to prevent errors on the first run.
            if (!File::isDirectory($logDirectory)) {
                File::makeDirectory($logDirectory, 0755, true, true);
            }

            // 3. Prepare the comprehensive data payload for logging.
            // We capture everything: IP, headers, and the full body.
            $logData = [
                'timestamp_utc'   => now()->setTimezone('UTC')->toDateTimeString(),
                'timestamp_local' => Carbon::now('Asia/Tehran')->toDateTimeString(), // Using a specific timezone for clarity.
                'ip_address'      => $request->ip(),
                'method'          => $request->getMethod(),
                'url'             => $request->fullUrl(),
                'headers'         => $request->headers->all(),
                'payload'         => $request->all() // The entire request body.
            ];

            // 4. Format the data into a human-readable, pretty-printed JSON string.
            // JSON_UNESCAPED_UNICODE is crucial for Persian characters.
            // JSON_UNESCAPED_SLASHES makes URLs look cleaner.
            $formattedLog = json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            // 5. Create a visually distinct entry for each request in the log file.
            $logEntry = "==================== [ Incoming Webhook @ " . $logData['timestamp_local'] . " ] ====================\n";
            $logEntry .= $formattedLog;
            $logEntry .= "\n\n";

            // 6. Append the data to the log file. This is atomic and efficient.
            File::append($logFilePath, $logEntry);

        } catch (\Exception $e) {
            // If logging fails for any reason (e.g., permissions issue),
            // log the error to the default channel but DO NOT stop the request flow.
            AmethystMatrix::yell('Failed to log Incoming webhook request: ' . $e->getMessage());
        }
        // --- The Logging Logic Ends Here ---


        // VERY IMPORTANT: Pass the request to the next middleware in the stack or to the controller.
        return $next($request);
    }
}
