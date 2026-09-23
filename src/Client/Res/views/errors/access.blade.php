<!doctype html>
<html lang="{{ $language }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $statusCode }} — {{ $message }}</title>
    <style>
        :root {
            color-scheme: dark;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        * { box-sizing: border-box; }
        html, body { min-height: 100%; }
        body {
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            background:
                radial-gradient(circle at 20% 20%, rgba(124, 58, 237, .18), transparent 34%),
                radial-gradient(circle at 80% 80%, rgba(14, 165, 233, .16), transparent 32%),
                #070b14;
            color: #f8fafc;
        }
        .gate {
            width: min(720px, 100%);
            padding: 42px;
            border: 1px solid rgba(148, 163, 184, .20);
            border-radius: 28px;
            background: rgba(15, 23, 42, .76);
            box-shadow: 0 24px 80px rgba(0, 0, 0, .42), inset 0 1px rgba(255, 255, 255, .04);
            backdrop-filter: blur(18px);
            text-align: center;
        }
        .status {
            margin: 0 0 10px;
            font-size: clamp(4rem, 12vw, 7rem);
            line-height: .9;
            font-weight: 900;
            letter-spacing: -.06em;
            opacity: .96;
        }
        .message {
            margin: 18px auto 0;
            max-width: 58ch;
            font-size: clamp(1.05rem, 2vw, 1.25rem);
            line-height: 1.8;
            color: #cbd5e1;
            overflow-wrap: anywhere;
        }
        .code {
            display: inline-flex;
            margin-top: 28px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(148, 163, 184, .10);
            border: 1px solid rgba(148, 163, 184, .16);
            color: #94a3b8;
            font: 600 .78rem/1.2 ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
    </style>
</head>
<body>
    <main class="gate" role="alert" aria-live="assertive">
        <p class="status">{{ $statusCode }}</p>
        <p class="message">{{ $message }}</p>
        <span class="code">{{ $code }}</span>
    </main>
</body>
</html>
