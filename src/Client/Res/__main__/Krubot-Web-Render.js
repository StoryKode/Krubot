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

/**
 * Krubot Web Renderer
 * StoryKode\Krubot\Drivers\WebApp
 *
 * Requires: jQuery 3.x (or cash-dom 8 for a lighter build)
 * All selectors scoped to .richy-root to support multiple
 * message bubbles / isolated renders on the same page.
*/

window.manifestToWeb = (function ($) {
    'use strict';

    // ─────────────────────────────────────────────────────────
    // 1. SPOILER — reveal on click
    // ─────────────────────────────────────────────────────────
    $(document).on('click', '.richy-spoiler:not(.richy-spoiler--revealed)', function () {
        $(this).addClass('richy-spoiler--revealed').attr('title', '');
    });

    // Photo / animation / video spoilers
    $(document).on('click', '.richy-photo--spoiler:not(.richy-revealed), .richy-animation--spoiler:not(.richy-revealed), .richy-video--spoiler:not(.richy-revealed)', function () {
        $(this).addClass('richy-revealed');
    });

    // ─────────────────────────────────────────────────────────
    // 2. DETAILS — toggle open/close
    // ─────────────────────────────────────────────────────────
    $(document).on('click', '.richy-details__summary', function () {
        var $details = $(this).closest('.richy-details');
        $details.toggleClass('richy-open');
    });

    // Auto-open if [data-open] attribute is set
    $(function () {
        $('[data-richy-open="true"]').addClass('richy-open');
    });

    // ─────────────────────────────────────────────────────────
    // 3. EXPANDABLE BLOCKQUOTE — toggle
    // ─────────────────────────────────────────────────────────
    $(document).on('click', '.richy-expandable-quote__trigger', function () {
        $(this).closest('.richy-expandable-quote').toggleClass('richy-open');
    });

    // ─────────────────────────────────────────────────────────
    // 4. SLIDESHOW — touch & click navigation
    // ─────────────────────────────────────────────────────────
    $(function () {
        initSlideshows();
    });

    // Re-init if new slideshows are injected dynamically (e.g. stream rendering)
    $(document).on('krubot:mounted', function () {
        initSlideshows();
    });

    function initSlideshows() {
        $('.richy-slideshow').not('[data-richy-init]').each(function () {
            var $ss       = $(this).attr('data-richy-init', '1');
            var $track    = $ss.find('.richy-slideshow__track');
            var $slides   = $ss.find('.richy-slideshow__slide');
            var $dots     = $ss.find('.richy-slideshow__dot');
            var $counter  = $ss.find('.richy-slideshow__counter');
            var count     = $slides.length;
            var current   = 0;

            if (count <= 1) {
                $ss.find('.richy-slideshow__arrow, .richy-slideshow__dots').hide();
                return;
            }

            function goTo(n, animate) {
                if (animate === false) {
                    $track.css('transition', 'none');
                    // Force reflow
                    $track[0].offsetHeight; // jshint ignore:line
                }
                current = ((n % count) + count) % count;
                $track.css('transform', 'translateX(-' + (current * 100) + '%)');
                if (animate === false) {
                    setTimeout(function () {
                        $track.css('transition', '');
                    }, 20);
                }
                $dots.removeClass('richy-slideshow__dot--active')
                     .eq(current).addClass('richy-slideshow__dot--active');
                $counter.text((current + 1) + ' / ' + count);
            }

            // Arrow clicks
            $ss.on('click', '.richy-slideshow__arrow--prev', function (e) {
                e.stopPropagation();
                goTo(current - 1);
            });
            $ss.on('click', '.richy-slideshow__arrow--next', function (e) {
                e.stopPropagation();
                goTo(current + 1);
            });

            // Dot clicks
            $ss.on('click', '.richy-slideshow__dot', function () {
                goTo($dots.index(this));
            });

            // Touch swipe
            var touchStartX = 0;
            $ss[0].addEventListener('touchstart', function (e) {
                touchStartX = e.changedTouches[0].clientX;
            }, { passive: true });
            $ss[0].addEventListener('touchend', function (e) {
                var dx = e.changedTouches[0].clientX - touchStartX;
                if (Math.abs(dx) > 40) {
                    goTo(dx < 0 ? current + 1 : current - 1);
                }
            }, { passive: true });

            // Keyboard (when focused)
            $ss.attr('tabindex', '0').on('keydown', function (e) {
                if (e.key === 'ArrowLeft')  goTo(current - 1);
                if (e.key === 'ArrowRight') goTo(current + 1);
            });

            // Init
            goTo(0);
        });
    }

    // ─────────────────────────────────────────────────────────
    // 5. PRE / CODE BLOCK — copy to clipboard button
    // ─────────────────────────────────────────────────────────
    $(function () {
        $('.richy-pre').each(function () {
            if ($(this).find('.richy-pre__copy').length) return; // already added

            var $btn = $('<button class="richy-pre__copy" title="Copy code" aria-label="Copy code">')
                .text('Copy');

            $(this).append($btn);
        });
    });

    $(document).on('click', '.richy-pre__copy', function () {
        var $btn  = $(this);
        var $pre  = $btn.closest('.richy-pre');
        var code  = $pre.find('code').text();

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code).then(function () {
                krbFlashCopy($btn);
            }).catch(function () {
                krbFallbackCopy(code);
                krbFlashCopy($btn);
            });
        } else {
            krbFallbackCopy(code);
            krbFlashCopy($btn);
        }
    });

    function krbFlashCopy($btn) {
        var original = $btn.text();
        $btn.text('Copied!').addClass('richy-pre__copy--done');
        setTimeout(function () {
            $btn.text(original).removeClass('richy-pre__copy--done');
        }, 1800);
    }

    function krbFallbackCopy(text) {
        var $ta = $('<textarea>').val(text)
            .css({ position: 'fixed', opacity: 0 })
            .appendTo('body');
        $ta[0].select();
        try { document.execCommand('copy'); } catch (e) { /* noop */ }
        $ta.remove();
    }

    // ─────────────────────────────────────────────────────────
    // 6. BANK CARD — copy number on click
    // ─────────────────────────────────────────────────────────
    $(document).on('click', '.richy-bank-card', function () {
        var num = $(this).data('richy-card') || $(this).text();
        krbFallbackCopy(num.replace(/\s/g, ''));

        var $el = $(this);
        $el.addClass('richy-bank-card--copied');
        setTimeout(function () { $el.removeClass('richy-bank-card--copied'); }, 1500);
    });

    // ─────────────────────────────────────────────────────────
    // 7. BOT COMMAND — dispatch event on click
    // ─────────────────────────────────────────────────────────
    $(document).on('click', '.richy-bot-command', function () {
        var cmd = $(this).data('richy-cmd') || $(this).text();
        $(document).trigger('krubot:command', [cmd]);
    });

    $(document).on('click', '[data-js-event="krubot:command"]', function (e) {
        // اگر نمی‌خوای لینک واقعی اجرا بشه (مثلاً در وب) می‌تونی preventDefault کنی
        // e.preventDefault();

        const cmd = $(this).data('command');
        const username = $(this).data('username') || null;

        // رویداد سفارشی را صدا بزن
        $(document).trigger('krubot:command', [cmd, username]);
    });

    // ─────────────────────────────────────────────────────────
    // 8. BUTTONS — dispatch action event
    // ─────────────────────────────────────────────────────────
    $(document).on('click', '.richy-btn[data-richy-action]', function (e) {
        var $btn    = $(this);
        var action  = $btn.data('richy-action');
        var payload = $btn.data('richy-payload') || {};
        var type    = $btn.data('richy-type')    || 'callback';

        if (type === 'url') {
            // handled natively via <a href>
            return;
        }

        e.preventDefault();

        // Prevent double-fire during pending request
        if ($btn.hasClass('richy-btn--loading')) return;
        $btn.addClass('richy-btn--loading');

        $(document).trigger('krubot:action', [{
            action:  action,
            payload: payload,
            type:    type,
            $btn:    $btn
        }]);

        // The host application resolves the action and should call:
        // $(document).trigger('krubot:action:done', [$btn]);
    });

    $(document).on('krubot:action:done', function (e, $btn) {
        if ($btn && $btn.jquery) {
            $btn.removeClass('richy-btn--loading');
        }
    });

    // ─────────────────────────────────────────────────────────
    // 9. REFERENCE LINKS — scroll to footnote
    // ─────────────────────────────────────────────────────────
    $(document).on('click', '.richy-reference-link', function (e) {
        e.preventDefault();
        var name = $(this).data('richy-ref');
        var $target = $('[data-richy-footnote="' + name + '"]');
        if ($target.length) {
            $target[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
            $target.addClass('richy-footnote-def--highlight');
            setTimeout(function () {
                $target.removeClass('richy-footnote-def--highlight');
            }, 1800);
        }
    });

    // ─────────────────────────────────────────────────────────
    // 10. ANCHOR LINKS — smooth scroll
    // ─────────────────────────────────────────────────────────
    $(document).on('click', '.richy-anchor-link', function (e) {
        e.preventDefault();
        var name = $(this).data('richy-anchor');
        var $target = $('[data-richy-anchor-name="' + name + '"]');
        if ($target.length) {
            $target[0].scrollIntoView({ behavior: 'smooth' });
        }
    });

    /**
     * JS Telegram Keyboard Renderer
     * Compatible with: PowerButton.toHtml() / RichTextButton
     *
     * Events dispatched:
     *   krubot:action   → { action, payload, type, btn }
     *   krubot:action:done → ($btn)  — call this from your host to unload button
     *
     * Copy text:
     *   Buttons with data-richy-btn-type="copy_text" handle clipboard automatically.
     *
     * Switch inline query:
     *   Buttons with data-richy-btn-type="switch_inline_query*" dispatch krubot:action.
     *   Your host should handle the switch or deep-link into Telegram.
    */
    (function () {
      'use strict';

      /* ─── Utility: debounce double-fire ─────────────────────────── */
      function isLoading(btn) {
        return $(btn).hasClass('richy-btn-button--loading');
      }
      function startLoading(btn) {
        $(btn).addClass('richy-btn-button--loading');
      }
      function stopLoading(btn) {
        $(btn).removeClass('richy-btn-button--loading');
      }

      /* ─── Utility: dispatch krubot:action ──────────────────────────── */
      function dispatchAction(btn, overrides) {
        var detail = Object.assign({
          action:  $(btn).attr('data-richy-btn-action')  || '',
          type:    $(btn).attr('data-richy-btn-type') || 'callback_data',
          query:   $(btn).attr('data-richy-btn-query')   || '',
          btn:     btn,
        }, overrides);

        /* Parse payload safely */
        try {
          detail.payload = JSON.parse($(btn).attr('data-richy-btn-payload') || '{}');
        } catch (_) {
          detail.payload = {};
        }

        document.dispatchEvent(new CustomEvent('krubot:action', { detail: detail, bubbles: true }));
      }

      /* ─── Copy text ──────────────────────────────────────────────── */
      function handleCopy(btn) {
        var text = $(btn).attr('data-richy-btn-copy') || '';
        if (!text) return;

        var originalHtml = $(btn).html();
        var originalClass = $(btn).attr('class');

        function showSuccess() {
          $(btn).html('✅ کپی شد').addClass('richy-btn-copied');
          setTimeout(function () {
            $(btn).html(originalHtml).attr('class', originalClass);
          }, 2000);
        }

        if (navigator.clipboard) {
          navigator.clipboard.writeText(text).then(showSuccess).catch(function () {
            legacyCopy(text);
            showSuccess();
          });
        } else {
          legacyCopy(text);
          showSuccess();
        }
      }

      function legacyCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.cssText = 'position:fixed;left:-9999px;top:-9999px';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (_) {}
        document.body.removeChild(ta);
      }

      /* ─── Main click handler (delegated) ────────────────────────── */
      $(document).on('click', '.richy-btn-button[data-richy-btn-type], .richy-btn-inline-button[data-richy-btn-type]', function (e) {
        var btn = this; // the matched DOM element (delegation target)

        var type = $(btn).attr('data-richy-btn-type') || '';

        /* URL / web_app / login_url → let browser handle <a href> */
        if (type === 'url' || type === 'web_app' || type === 'login_url') return;

        e.preventDefault();

        /* Copy text — handle internally */
        if (type === 'copy_text') {
          handleCopy(btn);
          return;
        }

        /* Everything else → dispatch action */
        if (isLoading(btn)) return;
        startLoading(btn);
        dispatchAction(btn);

        /* Auto-unload after 8s as safety net (host should call krubot:action:done) */
        setTimeout(function () { stopLoading(btn); }, 8000);
      });

      /* ─── krubot:action:done — host calls this to stop loading ─────── */
      document.addEventListener('krubot:action:done', function (e) {
        var btn = e.detail && (e.detail.$btn || e.detail.btn);
        /* Support both jQuery-style ($btn.jquery) and plain element */
        if (!btn) return;
        if (btn.jquery) btn = btn[0];
        if (btn && btn.classList) stopLoading(btn);
      });

      /* ─── Public API ─────────────────────────────────────────────── */
      window.TgKeyboard = {
        /**
         * Manually stop loading on a button.
         * Call from your host after handling krubot:action.
         * @param {Element} btn
         */
        done: function (btn) {
          if (btn && btn.jquery) btn = btn[0];
          if (btn && btn.classList) stopLoading(btn);
        },

        /**
         * Manually trigger a copy (useful for programmatic copy).
         * @param {Element} btn
         */
        copy: function (btn) {
          if (btn && btn.jquery) btn = btn[0];
          if (btn && btn.dataset.richyBtnType === 'copy_text') handleCopy(btn);
        },
      };

    })();

});

// ─────────────────────────────────────────────────────────────
// Supplemental CSS injected by JS (copy button, copy flash, etc.)
// ─────────────────────────────────────────────────────────────
(function () {
    var css = [
        /* Copy button */
        '.richy-pre { position: relative; }',
        '.richy-pre__copy {',
        '    position: absolute;',
        '    top: 0.5rem; right: 0.5rem;',
        '    background: var(--richy-surface);',
        '    color: var(--richy-text-secondary);',
        '    border: 1px solid var(--richy-border);',
        '    border-radius: var(--richy-radius-sm);',
        '    font-size: 0.6875rem;',
        '    padding: 0.2em 0.6em;',
        '    cursor: pointer;',
        '    font-family: var(--richy-font-body);',
        '    transition: background var(--richy-transition), color var(--richy-transition);',
        '}',
        '.richy-pre__copy:hover { background: var(--richy-surface-hover); color: var(--richy-text); }',
        '.richy-pre__copy--done { background: var(--richy-success) !important; color: #fff !important; border-color: var(--richy-success) !important; }',

        /* Bank card copy flash */
        '.richy-bank-card--copied { background: var(--richy-accent-dim); border-radius: 3px; }',

        /* Button loading state */
        '.richy-btn--loading { opacity: 0.65; pointer-events: none; cursor: wait; }',

        /* Footnote highlight */
        '.richy-footnote-def--highlight {',
        '    background: var(--richy-accent-dim);',
        '    border-radius: var(--richy-radius-sm);',
        '    transition: background 400ms ease;',
        '}',
    ].join('\n');

    var style = document.createElement('style');
    style.id  = 'richy-js-styles';
    style.textContent = css;
    document.head.appendChild(style);
}());

// ECMA2026 - PHP8.2.30 - Laravel 12.62
(function(window, document) {
    'use strict';

    /**
     * @description The core function that ensures a jQuery-like library is available
     *              before executing the main callback logic.
     * @param {Function} callback - The function to execute once the library is ready.
     */
    const ensureJQueryLike = function(callback) {
        // Check if jQuery or the '$' alias already exists in the global scope.
        if (typeof window.jQuery === 'function' && typeof window.$ === 'function') {
            console.group('%cjQuery::Ensure ✅', 'color: lime');
            console.info('jQuery از قبل موجود بود. در حال اجرای کد اصلی...');
            console.log('jQuery is already loaded. Running the app directly.');
            console.groupEnd();
            // Execute the callback immediately with the existing jQuery instance.
            callback(window.jQuery);
        } else {
            console.group('%cjQuery::Ensure ♋️', 'color: darkorange');
            console.log('%cـ jQuery یافت نشد. در حال تلاش برای بارگذاری cash-dom... ـ', 'background: linear-gradient(45deg, red, orange, yellow, green, blue, purple); color: #000; border-radius: 4px;');
            console.log('jQuery not found. Attempting to load cash-dom as a fallback.');
            console.groupEnd();
            // If jQuery is not found, start the fallback loading process.
            loadScriptFallback(callback);
        }
    };

    /**
     * @description Tries to load the cash-dom script from a list of CDN URLs, one by one.
     *              This provides redundancy if one CDN is down.
     * @param {Function} finalCallback - The main application logic to run on successful load.
     */
    const loadScriptFallback = function(finalCallback) {
        // A list of reliable CDNs for the latest version of cash-dom.
        //          The function will try them in this order.
        const latestVersion = '8.1.5';

        const cashDomCDNs = ([
            // Exactly Load 8.1.5
            'https://cdn.jsdelivr.net/npm/cash-dom@{version}/dist/cash.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/cash/{version}/cash.min.js',
            'https://unpkg.com/cash-dom@{version}/dist/cash.min.js',
            // You can add more fallback URLs here if needed.

            // --- Latest ---
            'https://cdn.jsdelivr.net/npm/cash-dom@latest/dist/cash.min.js',
            'https://fastly.jsdelivr.net/npm/cash-dom@latest/dist/cash.min.js',
            'https://gcore.jsdelivr.net/npm/cash-dom@latest/dist/cash.min.js',
            'https://unpkg.com/cash-dom@latest/dist/cash.min.js',

            // --- Fallback: exactly 8.1.5 (highly available) ---
            'https://fastly.jsdelivr.net/npm/cash-dom@{version}/dist/cash.min.js',
            'https://gcore.jsdelivr.net/npm/cash-dom@{version}/dist/cash.min.js',
            'https://cdn.jsdelivr.net/gh/fabiospampinato/cash@{version}/dist/cash.min.js',

            // --- Extra regional / mirror CDNs ---
            'https://cdn.bootcdn.net/ajax/libs/cash/{version}/cash.min.js',
            'https://lib.baomitu.com/cash/{version}/cash.min.js',
            'https://npm.elemecdn.com/cash-dom@{version}/dist/cash.min.js',
            'https://registry.npmmirror.com/cash-dom/{version}/files/dist/cash.min.js',
            'https://cdn.staticfile.org/cash/{version}/cash.min.js',
            'https://cdn.npmmirror.com/packages/cash-dom/{version}/dist/cash.min.js'
        ]).map(url => url.replaceAll('{version}', latestVersion));

        let cdnIndex = 0; // Start with the first CDN in the list.

        const tryNextCDN = () => {
            // If we have exhausted all CDN options, log an error and stop.
            if (cdnIndex >= cashDomCDNs.length) {
                console.group('%cjQuery::Ensure 📛', 'color: red');
                console.error('%cخطای حاد: بارگذاری کتابخانه از هیچ‌کدام از سرورها ممکن نبود.', 'background: red; color: white');
                console.error('Fatal: Could not load cash-dom from any of the provided CDNs.');
                console.groupEnd();
                return;
            }

            const url = cashDomCDNs[cdnIndex];
            const script = document.createElement('script');
            script.src = url;
            script.async = true; // Load the script asynchronously to not block page rendering.
            
            // Set up the success handler. This runs when the script is loaded and executed.
            script.onload = () => {
                console.group('%c✅', 'color: lime')
                console.log(
                    '%cjQuery::Ensure 🚀 %c🔰 Successfully loaded cash-dom from cdn 🔰 %c✅',
                    'background: #4CAF50; color: white; padding: 4px 8px; border-radius: 4px 0 0 4px;',
                    'background: #2196F3; color: white; padding: 4px 8px;',
                    'background: #FF9800; color: white; padding: 4px 8px; border-radius: 0 4px 4px 0;'
                );
                console.group(`%c❇️ ${url} ✅`, 'color: lime');
                console.groupEnd();
                
                // cash-dom loads itself into `window.cash`. We need to alias it to `window.$`
                //          for compatibility with code that expects the dollar sign.
                if (typeof window.cash === 'function') {
                    window.$ = window.cash;
                    // Now that the library is loaded and aliased, execute the final callback.
                    finalCallback(window.$);
                } else {
                    // This is a sanity check, should not happen if the script is valid.
                    console.error('%ccash-dom loaded but window.cash is not a function. Trying next CDN.', 'color: darkorange');
                    tryNextCDN();
                }
            };

            // Set up the error handler. This runs if the script fails to load (e.g., 404, network error, CORS issue).
            script.onerror = () => {
                console.warn(`%cFailed to load script from: ${url}. Trying the next CDN.`, 'color: darkorange');
                cdnIndex++; // Move to the next CDN in the list.
                tryNextCDN(); // Recursively call the function to try the next source.
            };

            // Append the script element to the document's head to initiate the download.
            document.head.appendChild(script);
        };

        // Start the loading process with the first CDN.
        tryNextCDN();
    };

    // Entry point of the entire logic.
    //          It ensures the library is ready, then executes our main application.
    ensureJQueryLike(window.manifestToWeb);

})(window, document);
