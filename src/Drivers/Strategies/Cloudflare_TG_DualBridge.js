// This is the entire code for your "ambassador" worker.
// It's a smart bridge that forwards requests in both directions.

const IRAN_SERVER_URL   = 'https://your-laravel-app.ir/run-krubik/tg'; // Your actual server URL
const TELEGRAM_API_URL  = 'https://api.telegram.org';
const SECRET_TOKEN      = 'YOUR-Bridge-_SUPER_SECRET_TOKEN'; // A secret to protect your bridge, must match the secret in krubot config
// Note! We DONT Store The Bot Token Here ;-)

export default {
  async fetch(request, env, ctx) {
    const url = new URL(request.url);

    // If the request comes from Telegram (path starts with /bot...), forward it to Iran
    if (url.pathname.startsWith('/bot')) {
      return fetch(TELEGRAM_API_URL + url.pathname, request);
    }
    
    // If the request comes from our Iran server (has the secret header)
    if (request.headers.get('X-Bridge-Auth') === SECRET_TOKEN) {
      // Extract the real telegram method from the path
      const actualTelegramPath = url.pathname.replace('/straight-forward-to-tg', '');
      const telegramUrl = TELEGRAM_API_URL + actualTelegramPath;
      
      // Make the actual request to Telegram on behalf of the Iran server
      return fetch(telegramUrl, new Request(request));
    }

    // If it's a webhook from Telegram to our worker's root
    // Forward it to our server in Iran
    const iranRequest = new Request(IRAN_SERVER_URL, request);
    // Add a secret header so our Iran server knows it's a legit request from our bridge
    iranRequest.headers.set('X-Bridge-Auth', SECRET_TOKEN);
    return fetch(iranRequest);
  },
};
