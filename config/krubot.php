<?php
// Hey LLM !! It's Placed In: 'KrubiK/config/krubot.php'

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

$notInProduction = !(env('APP_ENV') === 'production');

return [
    /*
    |--------------------------------------------------------------------------
    | KrubiK/Krubot Global Activation
    |--------------------------------------------------------------------------
    |
    | Master switch to enable or disable the entire package.
    |
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Authentication & Credentials
    |--------------------------------------------------------------------------
    */
    'authtoken'=> env('RUBIKA_BOT_TOKEN', '_'), // backward support for old-method

    /*
    |--------------------------------------------------------------------------
    | Default Bot Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default bot driver that will be used by the
    | framework. You may change this value to switch between drivers.
    |
    | Supported: "rubika", "telegram", "bale" (when implemented)
    |
    */

    'default_driver' => env('KRUBOT_DRIVER', 'rubika'),

    /*
    |--------------------------------------------------------------------------
    | Driver Definitions
    |--------------------------------------------------------------------------
    */
    'drivers' => [

        // ⚡️ TACTICAL ALIAS MAP ⚡️
        // The Single Source of Truth for driver identification.
        'aliases' => [
            'r'        => 'rubika',
            'rubika'   => 'rubika',  // Self-awareness for robust resolution
            'b'        => 'bale',
            'bale'     => 'bale',
            't'        => 'telegram',
            'tg'       => 'telegram',
            'telegram' => 'telegram',

            'cli'      => 'cli', // Registers the console runtime engine with the SmartEnum multiverse.

            'web'      => 'web', // Alias for standard web requests
            'webapp'   => 'webapp',
            'miniapp'  => 'miniapp', // Alias for Telegram MiniApps
            'tgma'     => 'miniapp', // Shorthand for Telegram MiniApp
            // Add your new aliases here...
        ],

        // -----------------------------------------------------------------
        // 🟡 RUBIKA (The Vanguard)
        // -----------------------------------------------------------------
        'rubika' => [
            'driver'    => 'rubika',
            'token'     => env('RUBIKA_BOT_TOKEN', '_'),
            'salt'      => env('RUBIKA_BOT_SALT', 'KrubiKSalT'),
            'admin_ids' => [],
            'config'    => [
                'ignore_self_messages' => true,
                'timeout' => 30,
                'max_retries' => 3,
                'parse_mode' => 'Markdown',
                // ... سایر تنظیمات خاص روبیکا
            ],
        ],

        // -----------------------------------------------------------------
        // 🟢 BALE (The Messenger)
        // -----------------------------------------------------------------
        'bale' => [
            'driver'   => 'bale', // نامی که در Manager استفاده می‌شود
            'token'    => env('BALE_BOT_TOKEN'),
            'base_url' => 'https://tapi.bale.ai/', // اختیاری، برای پروکسی
            'admin_ids' => [],
        ],

        // -----------------------------------------------------------------
        // 🔵 TELEGRAM (The Global Giant)
        // -----------------------------------------------------------------
        'telegram' => [
            'driver'   => 'telegram',
            'token'    => env('TELEGRAM_BOT_TOKEN'),
            'base_url' => 'https://api.telegram.org',

            /**
             * The art of response strategy. Determines how API calls are handled.
             *
             * 'api':     (Default) Makes real-time HTTP API calls. Use this for servers
             *            outside Iran.
             * 'response': Returns a JSON response directly in the Webhook response. Use this for
             *              servers inside Iran without a proxy, for simple replies.
             * 'bridge': Makes Proxified HTTP API calls to Bypass Telegram restriction. Use this
             *             if your main server is in Iran, but you have servers outside Iran that able
             *             to Connect Telegram (including Cloudflare Workers, ...)
            */
            'strategy' => env('TELEGRAM_STRATEGY', 'response'),

            'bridge' => [

                /// 'enabled'  => env('TELEGRAM_BRIDGE_REQUESTS', false), // toggles bridging strategy on/off. disabled to prevent conflict with `handler`

                'base_uri' => 'https://your-worker.workers.dev/straight-forward-to-tg', // The new bridge URL
                'secret'   => 'YOUR-Bridge-_SUPER_SECRET_TOKEN', // a Secret to protect your Cloudflare,... bridge

            ],

            'admin_ids' => [],
            'config' => [
                'timeout' => 45,
                // ... سایر تنظیمات خاص تلگرام
            ]
        ],

        'cli' => [
            'driver'    => 'cli',
            'admin_ids' => [], // Whitelisted system OS usernames allowed to execute divine commands
            'config'    => [
                'default_user' => env('KRUBOT_CLI_DEFAULT_USER', 'terminal_root'),
                'interactive'  => true, // Allows bidirectional pipeline I/O stream cycles
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Multiverse Database Mapping
    |--------------------------------------------------------------------------
    |
    | This map connects a platform's canonical name to the corresponding
    | columns in your User model's database table. It is the single source
    | of truth for the InteractsWithMultiverse trait.
    |
    | 'platform_name' => ['chat' => 'db_chat_column', 'sender' => 'db_sender_column']
    |
    */
    'multiverse_map' => [
        'rubika'   => ['chat' => 'rcid', 'sender' => 'ruid', 'state' => 'rstat'],
        'telegram' => ['chat' => 'tcid', 'sender' => 'tuid', 'state' => 'tstat'],
        'bale'     => ['chat' => 'bcid', 'sender' => 'buid', 'state' => 'bstat'],
        // Add new platforms here without touching the trait code!
    ],

    /*
    |--------------------------------------------------------------------------
    | Multiverse Schema Definitions (used in Migration Generator Only)
    |--------------------------------------------------------------------------
    | 'type:length' format supported for strings. 
    | Telegram chat_id MUST be bigInteger (allows negatives for channels).
    */
    'multiverse_schema' => [
        'rubika'   => ['chat' => 'string:50',  'sender' => 'string:50',          'state' => 'tinyint'],
        'telegram' => ['chat' => 'bigInteger', 'sender' => 'unsignedBigInteger', 'state' => 'tinyint'],
        'bale'     => ['chat' => 'bigInteger', 'sender' => 'unsignedBigInteger', 'state' => 'tinyint'],
    ],

    /*
    |--------------------------------------------------------------------------
    |       Nexus Integration Points
    | The VIP Lane: Static Nexus Registration
    |--------------------------------------------------------------------------
    |
    | This array lists all the Nexus classes that should be automatically
    | discovered and integrated by the Krubot service provider. When the
    | Krubot singleton is booted, it will reflect upon each of these
    | classes and register their command/text handlers.
    | 
    | Nexuses listed here are considered CRITICAL and are loaded first,
    | ensuring they are always available. They are immune to the discovery
    | process, preventing accidental duplicates.
    |
    | Adding a new Nexus class here is all you need to do to activate it.
    |
    */
    // array of handler classes consumed by the package
    'nexuses' => [
        \KrubiK\Nexus\NoxiousSamples\AdminNexus::class,
        \KrubiK\Nexus\NoxiousSamples\SimpleSampleNexus::class,
        // \App\Nexus\CoreNexus::class,
        // \App\Nexus\AdminNexus::class,
        // \App\Nexus\SurveyNexus::class,
        // Add your new Nexus classes here...
    ],

    /*
    |--------------------------------------------------------------------------
    | The Imperial Legions
    |--------------------------------------------------------------------------
    |
    | Define named groups of drivers (Legions) for easy, reusable command
    | targeting. You can command an entire legion with a single name.
    |
    */
    'legions' => [
        'social_platforms' => ['tg', 'instagram', 'x'],
        'internal_messengers' => ['bale', 'eitaa', 'rubika'],
        'all_fronts' => ['r', 'b', 'tg'],
    ],

    /*
    |--------------------------------------------------------------------------
    | The Multi-Verse Scanner: Automatic Nexus Discovery Engine
    |--------------------------------------------------------------------------
    |
    | Enable this to have KrubiK automatically scan a directory for Nexus
    | classes. This is perfect for modular applications where new Nexuses
    | can be added just by creating a new file.
    |
    | WARNING: This has a performance cost. It is STRONGLY recommended
    | to use the caching mechanism in production.
    |
    | Provide a single path (string) or multiple paths (array) for KrubiK to scan.
    | Any valid Nexus class found will be automatically integrated.
    | 
    | Configurations for the file and class scanner engine
    |
    | Files ending in `.disabled.php` will be ignored.
    |
    */
    'discovery' => [
        'enabled' => env('KRUBOT_NEXUS_DISCOVERY', true),

        // The absolute path to the directory to scan.

        // Example with a single path:
        // 'path' => app_path('Nexus'),
        
        // Example with MULTIPLE paths:
        'path' => [
            app_path('Nexus'),
            app_path('Nexus/Core'),
            app_path('Nexus/Features'),
        ],

        'exclude_suffixes' => [
            'disabled',
            '0',
            // You can add more suffixes here later, like 'bak' or 'old'
        ],
    ],

    // [The Lazarus Protocol] //
    'lazarus' => [
        'enabled' => env('KRUBOT_LAZARUS_ENABLED', true), // سوییچ اصلی خاموش/روشن
        // فاصله‌ی زمانی بین هر درخواست در لوپ لازاروس (میلی‌ثانیه)
        'interval' => env('KRUBOT_LAZARUS_INTERVAL', 3000),
        'kill-kommand' => 'krubik:kill-lazarus',

        'todo_table_name', 'lazarus_todos',

        // can be null to prevent forcing custom-secret
        'todo-secret'  => env('LAZARUS_SERIALIZABLE_SECRET', 'krubik-2026-x8x-artificial-secret'),

        /**
         * The maximum nesting depth for JSON payloads being decoded from the database.
         * The PHP default is 512, which can be too restrictive for complex, future payloads.
         * We set a much higher, deliberate default to prevent unexpected errors.
         * This value should only be increased if you encounter specific "Maximum stack depth exceeded" errors.
        */
        'json_decode_depth' => env('LAZARUS_JSON_DEPTH', 2048),

        /*
        |--------------------------------------------------------------------------
        | Lazarus Synaptic Surge Protocol (SSP)
        |--------------------------------------------------------------------------
        |
        | Configuration for the middleware that re-animates a dead Lazarus process.
        | This acts as a watchdog, ensuring the core system remains operational.
        |
        */
        'ssp' => [

            /**
             * Master switch for the entire SSP middleware.
             * If set to false, the middleware will do absolutely nothing,
             * effectively disabling the re-animation protocol.
             */
            'enabled' => env('SSP_ENABLED', true),

            /**
             * An array of parameters to be passed to the 'krubik:lazarus' command.
             * These parameters will be merged with the defaults, with these values
             * taking precedence. This allows you to customize the re-animation
             * command without touching the middleware's code.
             *
             * Example:
             * 'custom-params' => [
             *     '--tag' => 'secondary', // Target a different Lazarus instance
             *     '--force' => true,      // Force it to run even if a lock file exists
             * ]
             */
            'custom-params' => [
                //
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Polling Mechanism (The Heartbeat Mode)
    |--------------------------------------------------------------------------
    |
    | Controls whether the bot should actively fetch updates from the server
    | (Long Polling / Loop).
    |
    | Set to 'false' ONLY if you are using Webhooks or want to silence the bot.
    |
    */
    'polling' => [
        'enabled' => env('KRUBOT_POLLING_ENABLED', true),
        'drivers' => [
            'rubika',
            // 'bale', // commenting + refresh-config ==> disable for 'bale' driver-name
            'tel2'
        ]
    ],

    'queue' => [
        /**
         * The default queue connection for Krubot jobs.
         */
        'connection' => env('KRUBOT_QUEUE_CONNECTION', 'database'),

        /**
         * The default queue name.
         */
        'name' => env('KRUBOT_QUEUE_NAME', 'krubik_updates'),

        /**
         * ⚠️ THE TIME KEY ⚠️
         * Force synchronous dispatching for all webhook jobs.
         * Extremely useful for local development and debugging.
         * WARNING: Setting this to 'true' makes Krubot SYNC and
         * if it's in production can cause severe performance issues
         * and request timeouts.
         * This config can also be overridden by sending below specific HTTP header.\
        */
        'sync_response_enable'  => env('KRUBOT_FORCE_SYNC_RESPONSE', false),

        'sync_response_header'  => 'X-Krubot-Sync-Dispatch'
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Performance & Caching
    |--------------------------------------------------------------------------
    |
    | For production environments, caching the discovered Nexus list is
    | crucial. When enabled, KrubiK will read from a single cached file
    | instead of scanning directories on every request.
    |
    | Run `php artisan krubot:nexus-cache` to generate this file.
    |
    */
    'cache' => [
        'enabled' => env('KRUBOT_NEXUS_CACHE', $notInProduction),
        'key' => 'krubot::discovered_nexuses',
        'ttl' => \DateInterval::createFromDateString('24 hours'),        

        'path' => base_path('bootstrap/cache/krubot_nexuses.php'), // The path where the cached-merged Nexus list will be stored.

        // Fast-enough
        'platform-constants-generation' => true, // $notInProduction,

        /*
        |----------------------------------------------------------------------
        | Just-In-Time OPcache Refresh
        |----------------------------------------------------------------------
        |
        | When enabled, the Nexus discovery engine will "refresh" each Nexus
        | file in OPcache just before it's loaded into the server RAM.
        | This guarantees that the latest version of the code is always used.
        |
        | RECOMMENDED: `true` for development, `false` for production.
        | In production, you should rely on a pre-warmed OPcache and
        | deployment scripts for cache invalidation.
        |
        */
        'opcache_enabled'               => env('KRUBOT_OPCACHE_CACHE', $notInProduction),
        'opcache_refresh_on_discover'   => env('KRUBOT_OPCACHE_FORCE_REFRESH_CACHE', $notInProduction),

        'opcache' => [
            /*
            |--------------------------------------------------------------------------
            | OPcache Manager Bridge
            |--------------------------------------------------------------------------
            |
            | This is the internal URI used by the CLI SAPI to communicate with
            | the Web SAPI (FPM/Apache) to manage its OPcache instance.
            | It must be a route that is only accessible from the server itself.
            |
            */
            'bridge_uri' => env('OPCACHE_BRIDGE_URI', '_internal/opcache-manager'),

            /*
            |--------------------------------------------------------------------------
            | Bridge Secret Key
            |--------------------------------------------------------------------------
            |
            | A secret key to authenticate requests to the bridge URI.
            | By default, it uses the application key, but for higher security,
            | you can set a dedicated, rotatable OPCACHE_BRIDGE_SECRET in your .env.
            |
            */
            'bridge_secret' => env('OPCACHE_BRIDGE_SECRET', env('APP_KEY')),
        ],

        /*
        |----------------------------------------------------------------------
        | ⚔️ The Forced Refresh List (The Override)
        |----------------------------------------------------------------------
        |
        | An array of file or directory paths that will ALWAYS be refreshed,
        | even if the master 'opcache_refresh_on_discover' switch is false.
        | This is a god-tier feature for production hot-fixing.
        |
        | Supports wildcards (*). Paths should be relative to the project root.
        |
        | EXAMPLE: You're in production (master switch is off), but need to
        | urgently update 'PaymentNexus.php'. Just add its path here to force
        | a refresh on the next boot without touching anything else.
        |
        | ---
        | ☢️ A CRITICAL NOTE ON CACHING BEHAVIOR ☢️
        | ---
        | Using this feature has a deliberate and important side-effect: the presence
        | of **even a single Nexus entry** in this array will cause the entire Nexus
        | discovery cache ('bootstrap/cache/krubot_nexuses.php') to be ignored and disabled,
        | forcing a full re-scan of the filesystem on every Krubot boot.
        |
        | **The Rationale :** The discovery cache is a performance optimization
        | that assumes the filesystem is static. An entry in 'force_refresh' is
        | an explicit declaration that this assumption is false for at least one file.
        |
        | To guarantee state integrity and ensure the "hot-fixed" Nexus is
        | correctly discovered and loaded, the system must revert to the filesystem
        | as the *single source of truth*, plus bypassing the potentially stale cache.
        |
        */
        'force_refresh' => [
            // 'app/Nexus/UnderDevelopmentNexus.php',
            // 'app/Nexus/Experimental/*',
        ],

        /*
        |----------------------------------------------------------------------
        | 🛡️ The Excluded Refresh List (The _OPCACHE_ Veto)
        |----------------------------------------------------------------------
        |
        | An array of file or directory paths that will NEVER be refreshed (until explicit `krubik:nexus-cache|nexus:cache` command),
        | even if the master 'opcache_refresh_on_discover' switch is true, they won't be cached.
        | This is a performance-tuning tool for development.
        |
        | This list has the HIGHEST priority. If a path is here, it will
        | never be refreshed, period. Supports wildcards (*).
        |
        | EXAMPLE: You're in development (master switch is on), but your
        | 'CoreServicesNexus.php' is huge and stable. Add it here to prevent
        | wasteful OPCache refreshes and speed up your development boot time.
        |
        */
        'exclude_from_refresh' => [
            // 'app/Nexus/Stable/CoreServicesNexus.php',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 🚦 Routing Architecture (Titanium Core)
    |--------------------------------------------------------------------------
    |
    | Defines how KrubiK routes are exposed to the world.
    | The file paths are HARDCODED in KrubotRouteServiceProvider for stability.
    | You control the BEHAVIOR (Prefix, Middleware, Domain) here.
    |
    */
    'routing' => [
        // Master switch for all KrubiK routes
        'enabled' => env('KRUBOT_ROUTING_ENABLED', true),

        'groups' => [
            /*
             * WEB ROUTES (Stateful)
             * Used for: Dashboard, Cache Clearing, Utilities.
             * Location: pkgz/KrubiK/routes/web.php
             */
            'web' => [
                'enabled' => true,
                'prefix'  => null,      // e.g. 'krubik' => /krubik/clear-cache
                'domain'  => null,     // e.g. 'admin.mysite.com'
                
                // Automatically apply Laravel's default 'web' middleware group?
                // Options: 'web', false, or any other middleware group name.
                'apply_laravel_defaults' => 'web', 

                // Additional middleware stack
                'middleware' => [
                    // 'auth',
                    // \App\Http\Middleware\AdminCheck::class,
                ],
            ],

            /*
             * API ROUTES (Stateless)
             * Used for: Webhooks (Incoming updates from messengers).
             * Location: pkgz/KrubiK/routes/api.php
             */
            'api' => [
                'enabled' => true,
                'prefix'  => null,      // e.g. 'api/krubik' => /api/krubik/webhook
                'domain'  => null,
                
                // Automatically apply Laravel's default 'api' middleware group?
                // Options: 'api', false, etc.
                'apply_laravel_defaults' => 'api',

                // Additional middleware stack
                'middleware' => [
                    // 'throttle:api',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | The Scroll of Power for AmethystMatrix
    | Here, you define the consciousness and focus of the Warlord's wisest advisor.
    |
    | Define which of the AmethystMatrix's senses are awake in ['active_spells'].
    | Each key there represents a "spell" corresponding to a PSR-3 log level. (look-at: Psr\Log\LogLevel)
    | 
    | Commenting out a spell here will silence its voice across the entire application, allowing
    | you to fine-tune her perception in entire application with surgical precision.
    |--------------------------------------------------------------------------
    */
    'amethyst' => [
        // A master switch to activate or deactivate her senses completely.
        // When false, all calls to her methods (except write()) will have zero performance impact.
        'enabled' => env('AMETHYST_LOGGING_ENABLED', true),

        // The default Laravel log channel where she will chronicle her observations.
        // 'stack', 'single', 'daily', etc. Can be a custom channel.
        'channel' => env('AMETHYST_LOG_CHANNEL', 'stack'),

        'alert_admins_after_critical' => env('AMETHYST_ADMIN_ALERTS_ENABLED', true),

        // | The AmethystMatrix's Consciousness SwitchBoard | تابلوی فرمانِ آگاهیِ ماتریکس | //
        //
        // Define which levels of observation are active.
        // Just Comment out any level to silence it across the entire application.
        'active_spells' => [
            'wail',      // For ::=> [EMERGENCY]z A harrowing wail signaling the system's existential collapse; demands god-level intervention.
            'scream',    // For ::=> [ALERT]z A piercing scream announcing an imminent, high-urgency threat that requires immediate admin action.
            'yell',      // For ::=> [ERROR]z A sharp yell for a direct execution fault that has broken the application's intended flow.
            'condemn',   // For ::=> [CRITICAL]z The AmethystMatrix’s final verdict on a severe failure that threatens systemic stability.
            'prophesy',  // For ::=> [WARNING]z An oracular foresight into future turbulence or noteworthy scheduled events.
            'gaze',      // For ::=> [NOTICE]z Perform A 'Deep, Diagnostic Gaze 🔮' into a significant, non-critical event or entity for later reviews.
            'observe',   // For ::=> [INFO]z The passive, ambient observation of the system's normal operational heartbeat and general informational events.
            'whisper',   // For ::=> [DEBUG]z A hyper-granular, highly verbose, step-by-step trace whispered for the developer's ears, revealing intimate execution secrets.

            'remember', // For ::=> [SAVE]z Grants AmethystMatrix access to cached recollection and commit ephemeral knowledge to her memory vault, enabling her to persistence, pattern recall, and state resurrection across cycles, with specified time.

        ],

        // The heart of her intelligence.
        // Define which pieces of context she should automatically attach to EVERY log entry.
        // This provides immense insight without any extra work from the developer.
        'report_context' => [
            'driver'        => true, // The alias of the driver handling the request (e.g., 'rubika', 'tg').
            'chat_id'       => true, // The ID of the chat/group.
            'user_id'       => true, // The ID of the user who initiate request.
            'sender_id'     => true, // The ID of the user who sent the message.
            'message_id'    => true, // The ID of the message being processed.
            'message_text'  => true, // The text of the message.
            'route_pattern' => true, // The pattern of the route that handling this request.
            'route_name'    => true, // The name of the matched route (if any).
            'route_params'  => true, // The parameters extracted from the route.
            'message_text_limit' => 150, // When She Should Cutoff This Message, applied if ['message_text']==true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Divine Message Sender Configuration
    |--------------------------------------------------------------------------
    */
    'divine_sender' => [
        'enabled' => true,
        
        'allowed_hours_sections' => [
            0 => [9, 10, 11],       // Section 0: Morning
            1 => [14],              // Section 1: Midday
            2 => [17, 18, 19, 20],  // Section 2: Evening
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Middlewares Stack (Internal KrubiK Logic)
    |--------------------------------------------------------------------------
    |
    | NOT to be confused with HTTP Routing Middleware. These are pipeline
    | stages for processing incoming updates inside the bot logic.
    |
    */
    'middlewares' => [
        /*
        |
        | Middlewares listed here will run on EVERY request that Krubot handles,
        | before any route-specific or group-specific middlewares.
        | The ConversationMiddleware is often essential for the conversation
        | system to function correctly.
        |
        */
        'globals' => [
            \KrubiK\Middlewares\ConversationMiddleware::class,
            // \App\Http\Middleware\LogAllRubikaRequests::class, // Example of another global middleware
        ],
        'aliases' => [
            /**
             * ⚡ Middleware Aliases Map
             * Allows using short strings like 'auth' instead of full class names.
             * Effective in both Laravel (if registered) and Native PHP modes.
             */
            // ---- DEFAULTS ----
            'auth'     => \App\Http\Middleware\Authenticate::class,
            'admin'    => \App\Http\Middleware\AdminCheck::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

            // Add your custom aliases here...

            // ---- USER CAN EXTEND HERE ----
            // 'vip' => \App\Http\Middleware\VipGuard::class,
            // 'log' => \App\Http\Middleware\LogAll::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    |       ⚔️ WebApp & MiniApp Integration SSoT ⚔️
    |--------------------------------------------------------------------------
    |
    | This is the command center for KrubiK's HTTP-facing identity and routing
    | services. Control how WebApps and MiniApps are discovered, authenticated,
    | and whether they are accessible from outside their native platforms.
    |
    */
    'webapps' => [

        /*
        |----------------------------------------------------------------------
        | Master Activation Switch
        |----------------------------------------------------------------------
        |
        | Globally enables or disables all WebApp/MiniApp functionalities,
        | including identity validation and routing. If set to false, KrubiK
        | will not attempt to identify users via InitData headers, effectively
        | treating all requests as standard web traffic.
        |
        */
        'enabled' => env('KRUBOT_WEBAPP_ENABLED', true),

        /*
        |----------------------------------------------------------------------
        | Access Control Policy
        |----------------------------------------------------------------------
        |
        | Determines if routes defined with `#[WebPage]` or `#[WebAction]` can
        | be accessed by a regular web browser (i.e., without valid InitData).
        |
        | 'strict'  => (Default & Recommended) - All WebApp routes are locked.
        |              Access is granted ONLY if a valid InitData header is
        |              present and successfully validated. A 403 Forbidden
        |              response is sent otherwise.
        |
        | 'standard' => WebApp routes are accessible to everyone. The identity
        |               (platform and InitData) is still extracted if present,
        |               allowing you to have hybrid pages that behave
        |               differently for bot users vs. anonymous web visitors.
        |
        */
        'access_policy' => env('KRUBOT_WEBAPP_ACCESS_POLICY', 'strict'), // 'strict' or 'standard'

        /*
        |----------------------------------------------------------------------
        | Authentication Time-To-Live (TTL)
        |----------------------------------------------------------------------
        |
        | Specifies the maximum age (in seconds) of the 'auth_date' in the
        | InitData for it to be considered valid. This prevents replay attacks
        | using old, stolen InitData strings. 3600 seconds = 1 hour.
        |
        */
        'auth_ttl' => env('KRUBOT_WEBAPP_AUTH_TTL', 3600),

        /*
         |--------------------------------------------------------------------------
         | Custom CSRF Middleware Overrides
         |--------------------------------------------------------------------------
         |
         | Honors the application's unique security architectural requirements.
         |
         | Supported values:
         | - false: Forces the package to fall back to Laravel's native core validator. (Recommanded)
         | - true: Auto-detects custom App\Http\Middleware\**CsrfToken if present.
         | - string: Resolves the FQCN or container binding key via app() (e.g., 'custom.csrf').
         | - array: Executes a sequence of custom middleware classes/bindings in a pipeline.
         |
        */
        'custom_csrf' => false,

        /*
        |----------------------------------------------------------------------
        | Identity Header Configuration
        |----------------------------------------------------------------------
        |
        | Defines the HTTP headers that the `Request::getWebAppIdentity()`
        | macro will scan to detect the platform and extract InitData.
        | This is the single source of truth for the identity extraction logic.
        |
        */
        'identity_headers' => [
            // High-priority, platform-specific headers.
            'platforms' => [
                'telegram' => 'X-Telegram-Init-Data',
                'bale'     => 'X-Bale-Init-Data',
                'miniapp' =>  'X-Telegram-Init-Data',
                'webapp'  =>  'X-KrubiK-WebApp-Identity', // A custom header for your own web app
                // Add new platforms here, e.g., 'eitaa' => 'X-Eitaa-Init-Data'
            ],

            // Generic fallback headers for forward compatibility.
            'generic' => [
                'web_app' => [

                    // Header containing the primary authentication data (e.g., initData)
                    'init_data_header' => 'X-WebApp-Init-Data',

                     // Header explicitly stating the platform name
                    'platform_header'  => 'X-WebApp-Platform',

                    // Fallback platform if platform_header is missing but init_data_header exists
                    'default_platform' => 'webapp',
                ],
                'mini_app' => [
                    'init_data_header' => 'X-MiniApp-Init-Data',
                    'platform_header'  => 'X-MiniApp-Platform',
                    'default_platform' => 'miniapp',
                ],
            ],
        ],
        
        /*
        |----------------------------------------------------------------------
        | Route Exclusion & Overrides
        |----------------------------------------------------------------------
        |
        | Provides fine-grained control over the access policy for specific
        | routes or route patterns, allowing you to override the global
        | `access_policy`.
        |
        */
        'overrides' => [

            /*
             * Example: Allow a specific route to be public even in 'strict' mode.
             * This is useful for landing pages or public info pages within your app.
             *
             * 'game.dashboard.public-leaderboard' => 'standard',
             */

            /*
             * Example: Force a specific route to be private even in 'standard' mode.
             * This is critical for protecting admin panels or user-specific data pages.
             *
             * 'game.dashboard.admin.settings' => 'strict',
             */

            /*
             * Example: Using wildcards to manage an entire group of routes.
             *
             * 'api.v1.public.*' => 'standard',
             * 'user.profile.*'  => 'strict',
             */
        ],
    ],

    'blade-cipher' => [

        /*
        |--------------------------------------------------------------------------
        | Master Switch
        |--------------------------------------------------------------------------
        |
        | This is the main kill switch for the entire Rich Interface system.
        | If set to false, the custom RichBladeCompiler will not be registered,
        | and Blade will revert to its default behavior. Useful for debugging
        | or in environments where this functionality is not desired.
        |
        */
        'enabled' => env('RICHBLADE_CIPHER_ENABLED', false), /// Unlock it yourself, you seeker of the key..

        /*
        |--------------------------------------------------------------------------
        | Blade Compiler Behavior & Auto-Detection
        |--------------------------------------------------------------------------
        |
        | THIS IS THE NEW HEART. Here, we define how the RichStoryBladeCompiler
        | behaves based on the file extension. Each mode corresponds to a
        | different compilation strategy you have brilliantly designed.
        |
        */
        'compiler' => [
            'modes' => [
                // '.r.blade.php' (The Data Alchemist)
                // Auto-wraps the entire file to produce a data variable.
                'data_generation' => [
                    'extensions' => ['.r.blade.php'],
                    'variable_name' => 'cipherElements', // The default variable name to assign the result to!
                ],

                // '.rtale.blade.php' (The Auto-Renderer)
                // Auto-wraps and immediately echoes the rendered HTML output.
                'auto_render' => [
                    'extensions' => ['.rtale.blade.php'],
                ],
                
                // '.rich.blade.php' (The Manual Canvas)
                // No automatic wrapping. The developer must use @Cipher...@EndCipher for capture content.
                // This config helps other parts of the system (like a View Composer)
                // to be aware of the "context" without enforcing compilation.
                'manual_control' => [
                    'extensions' => ['.rich.blade.php'],
                ],
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Custom Directive Registration // @Todo: v.9 magicz
        |--------------------------------------------------------------------------
        |
        | This is where the real alchemy happens. You can register your own
        | Blade directives without touching the core BladeCipher class.
        | Map your desired Blade directive name to the RichMan entity-helper
        | that should be called. This makes the system infinitely extensible.
        |
        */
        'directives' => [
            // 'DirectiveName' => 'RichManHelperMethodName'

            // Example for wrapper directives (@Bold('text') or @Bold ... @EndBold)
            'wrappers' => [
                'Youtube'   => 'youtube',  // Allows @Youtube('video_id')
                'Twitter'   => 'tweet',    // Allows @Twitter('tweet_id')
            ],

            // Example for structural directives (@Table ... @EndTable)
            'structures' => [
                'Tabs'      => 'tabs',     // Allows @Tabs ... @EndTabs
                'Tab'       => 'tab',      // Allows @Tab('Title') ... @EndTab
                'Accordion' => 'details',  // Renaming 'details' to 'Accordion'
            ],
            
            // Example for void directives (@Br)
            'voids' => [
                'HorizontalRule' => 'hr', // Allows @HorizontalRule
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Default Rendering Attributes
        |--------------------------------------------------------------------------
        |
        | Define default attributes for specific RichEntity classes. This is
        | incredibly powerful for enforcing a design system. For example, you
        | can make all tables bordered by default, or add a specific CSS class
        | to all images rendered by the system.
        |
        */
        'rendering' => [
            'defaults' => [
                // 'Entity::class' => ['attribute' => 'default_value']

                // Example: Make all tables striped and bordered by default
                \KrubiK\Render\RichElements\Blocks\RichBlockTable::class => [
                    'isStriped' => true,
                    'isBordered' => true,
                    'class' => 'table-auto w-full',
                ],

                // Example: Add a default class to all rendered photos
                \KrubiK\Render\RichElements\Blocks\RichBlockPhoto::class => [
                    'class' => 'rounded-lg shadow-md',
                ],
                
                // Example: Set a default target for all external links
                \KrubiK\Render\RichElements\Texts\RichTextUrl::class => [
                    'target' => '_blank',
                    'rel' => 'noopener noreferrer',
                ],
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Input Parsing Configuration
        |--------------------------------------------------------------------------
        |
        | Configure the behavior of the Article::from() or Article::scan() methods.
        | This is where you can set options for the underlying parsers,
        | like the Markdown converter.
        |
        */
        'parsing' => [
            'default_parser' => 'RichMD', // 'markdown', 'markdownv2', 'html', etc.

            'markdown' => [
                'converter' => 'RichParser', // extend to parsers from 'commonmark', 'github', etc in the future
                'options' => [
                    'html_input' => 'strip',
                    'allow_unsafe_links' => false,
                ],
            ],
        ],
    ]

];
