<div align="center">

<a href="https://StoryKo.de/"><img src="https://StoryKo.de/assets/img/DevGX.png" alt="DevGX — Developer Experience Exponentiation" width="600" /></a>

<!-- # KRUBIK / KRUBOT

< !-- ## The Hyper-DX Narrative Engine for Bots, Messengers, and Developer Joy.

**This is not just another bot library.**  
**This is the first strike in a global revolution for the joy of developers' work and life.**<br>
**To solidify a deep, penetrating, and unbreakable connection between Human ✸ System ✸ LLMs** -->

## Krubot: The Opinionated Shared Brain 🧠 ⚡️
### Craft a cross-platform storyteller for your WebApp & Bot empire, from one Async_Core Elegant Codebase ***to rule them all 💍***

**Build less boilerplate. Ship more meaning.**<br>
**Develop Story for Telegram, Bale, Rubika Bots Once!**

***This is not just another bot library.***  
**This is the *First Strike* in a global revolution for the joy of developers' work and life.**

**To solidify a deep, penetrating, and unbreakable connection between ::**<br>
**Human ✸ Universe ✸ LLMs**

<br>

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](#requirements)
[![Laravel](https://img.shields.io/badge/Laravel-12%2B-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](#requirements)
[![DX](https://img.shields.io/badge/DX-Ultra--Optimized-F0FF00?style=for-the-badge)](#the-manifesto)
[![Narrative Programming](https://img.shields.io/badge/Programming-Narrative%20Avant--Garde-00E38C?style=for-the-badge)](#what-is-narrative-programming)
[![License](https://img.shields.io/badge/License-MIT+-8A2BE2?style=for-the-badge)](./KLicense.md)

<br>

> **Build bots like stories.**  
> **Scale systems like an architect.**  
> **Command drivers like a Warlord.**  
> **And Enjoy Coding like it was meant to be enjoyed.**

</div>

---

> **Hyper-DX Frequency Alignment Protocol** 🎧
> 
> Before you step into the KrubiK Cyber-Citadel Introduction, your Neural circuits and Spiritual bandwidth must be calibrated to the correct resonance. 🪄🧠⚡️
>
> **The Catalyst Track:** 🎼🌌 [Ludwig Göransson — The Mandalorian Theme [Extended]](https://soundcloud.com/d-x-k-828761910/the-mandalorian-extended-theme/s-ASC87lDNGzT)
>
> **Directive:** Initiate playback in a background tab and back.
*Read this **Architectural Spellbook** only when you are in the stable frequency.* ⚡🔮

<div align="center">
  <img src="https://StoryKo.de/assets/img/KrubiK/Drivers.jpg" alt="KrubiK Drivers" width="100%" />
  <b>Drivers of <i>KrubiK Agency</i><br>{ KrubiK Platform Drivers }</b>
</div>


---

<div align="center">

> The article below is the **complete strategic breakdown**. <br>
> For the short tactical version and faster briefing, head to:: ⚡️📜⚙️ <br>
> | Portal | Language |
> |:--|:--|
> | <a href="https://DevGX.ir/Krubot" target="_blank"><b>DevGX.ir</b></a> | Persian 🇮🇷 |
> | <a href="https://StoryKo.de/Krubot" target="_blank"><b>StoryKo.de</b></a> | Global 🌍️ |

<br>
So take your <b><i>Heart-Pillars</i></b> and let's go...<br>
🫀💊
</div>


## The Manifesto

Most developers were trained to tolerate unnecessary pain.

Messy handlers.  
Fragile callback strings.  
Manual state machines.  
Boilerplate everywhere.  
Transport-layer noise leaking into feature code.  
Debugging that breaks the runtime.  
Messenger APIs that force you to think like a packet parser instead of a product builder.

Krubot exists to reject that entire reality.

It was forged around a different belief:

> **Developer Experience [Dev✸✸] is not a cosmetic luxury. It is critical foundation.**

*KrubiK turns bot development into a clean, composable, joyful workflow:*

> [!IMPORTANT] <br> 
> Inject a Star ⭐ — fuel the flame 🔥🔥 into the War against Cognitive Overhead ⛓️‍💥🪚, and help more developers discover Ultra-DX 🌪🛸

```php
// To Activate, Just Drop The Code In :: app/Nexus/GamePanelNexus.php
namespace App\Nexus;

#[WebApp('game.dashboard')]
class GamePanelNexus
{
    public function index(): string
    {
        return 'I Now Can Answer to WebRequests<br>'
        . 'Welcome to /webapps/game/dashboard/ !';
    }

    #[OnCommand('panel')]
    public function showDashboard(#[WarLord] Krubot $bot): void
    {
        // Super-Speed Fail-safe resolver for userData
        $isActive = $bot->userStorage()->get('subscribed', false);

        $bot->reply('**Welcome back Commander!**\nYour Account Status 🎮: ' . ($isActive ? 'Active ✅' : 'DeActive ❌️'))
            ->attachKeyboard(function($kb) use($isActive) {
                
            $kb

                // 🟡 Row 1: Primary Action Buttons (3 columns each = 50% width distribution)
                ->row(fn($row) => $row
                    ->add(PowerButton::make('♦️ Buy Gems 💎')->col(3)->action('buy_gem'))
                    ->add(PowerButton::make('♦️ Top-up Wallet 💰')->col(3)->action('wallet'))
                )

                // Only attach Server buttons IF the account is active
                ->when($isActive, function(Keyboard $kbx) {

                    // 🔵 Row 2: Conditional Server Selection Matrix
                    // This demonstrates the raw power of the col() method; strictly aligning
                    // 3 elements side-by-side (2 columns each = 33.33% viewport width).
                    $kbx->row(
                        fn($row) => $row
                            ->add(PowerButton::simple('srv_1', 'IR 🇮🇷')->col(2))
                            ->add(PowerButton::simple('srv_2', 'US 🇺🇸')->col(2))
                            ->add(PowerButton::simple('srv_3', 'EU 🇪🇺')->col(2))
                    );

                })

                // 🔴 Row 3: Full-width Support / Fallback Button (6 columns = 100% viewport width)
                ->row(fn($row) => $row
                    ->add(PowerButton::make('♦️ Online Support 🆘')->col(6)->action('support'))
                );
            })
        ->send();
    }

    #[Action('buy_gem')]
    public function buy_gem(Krubot $bot): void 
    {
        // Business logic for purchasing gems...
    }
    
    #[Action('wallet')]
    public function show_wallet(Krubot $bot): void 
    {
        // Business logic for wallet top-up...
    }

    #[RestrictTo([
        Platform::Bale,
        'tg, rubika', 
        [
            'sorush', 
            [
                'eitaa, bale', 
                Platform::Telegram
            ]
        ]
    ])] // RestrictTo will be merged with parentClass Restrictions, Utilizing {{ ::OR:: logic }}
    #[OnCommand('menu')]
    public function showGamesMenu(Krubot $bot): void
    {
        // 1. Fetching VIP products from the database.
        $products = Product::query()
            ->where('is_vip', true)
            ->get(['id', 'name', 'price']); // Returns an Eloquent Collection
        
        // 2. Rapid Transformation via Laravel Collections.
        // Mapping the Products directly into the PowerButton actionable architecture.
        $buttons = $products->map(function (Product $product) {
            $label = "{$product->name} (💰 {$product->price}T)";
            return PowerButton::make($label)->action('order', ['id' => $product->id]);
        })->toArray();

        // 3. The Magic of Chunking: Automatically grouping elements into a pristine matrix.
        // Changing chunk(2) to chunk(3) effortlessly switches the grid layout to a 3-column UI.
        $bot->reply("Today's Exclusive VIP Games: ✨️🧧")
            ->keyboard(
                Keyboard::make()
                    ->rtl() // Can be used for properly Align Persian layouts on the screen by forcing Right-to-Left rendering order // + rtl(false) === ltr()
                    ->buttons($buttons)
                    ->chunk(2) // <--- Its DX! Zero manual row calculation!
            )
        ->send();
    }

    #[Action('order')]
    public function orderGame(Krubot $bot, int $id): void 
    {
        // Business logic for $product Order

        $product = Product::find($id);
        if(!$product)
            return $bot->reply("Product Id is Invalid")->send();

        AmethystMatrix::whisper('Order Started', $product); // AmethystMatrix also auto-collects & auto-logs any context-data we allowed her via: `config('krubot.amethyst.report_context')[]`

        $bot
        ->reply('You\'ve Chosen a Great Game to Engage ✨️🎮️ :: ***' . $product->name . '***')
        ->send();

    }

    /**
     * The WebAction endpoint that receives the product ID.
     * The name '.order_vip_product' is relative to 'game.dashboard'.
     * The final resolved URI will be /webapps/game/dashboard/order_vip_product
     *
     * The sexy WebAction endpoint that receives the product ID.
     * Notice how '$productId' is just requested. The system will provide it, as demand.
     * 
     * via PureJS:: `fetch('/webapps/game/dashboard/order_vip_product', {productId: 13, quantity: 3})`
     * or via sendData:: `[Telegram|Bale].WebApp.sendData(JSON.stringify({action: 'order_vip_product', productId: 13, quantity: 3}))`
    */
    #[WebAction('.order_vip_product', methods: ['POST'])]
    public function orderVipProduct(Krubot $bot, int $productId, int $quantity = 1, string $optionalDumbMsg = 'H! There'): array
    {
        $tgUser = $bot->user();
        
        /** @var Product|null $product */
        $product = Product::query()->where('id', $productId)->where('is_vip', true)->first();

        if (!$product) {
            return ['status' => 'error', 'message' => 'Invalid or non-VIP product selected.'];
        }
        
        $totalCost = $product->price * $quantity;
        
        // --- Database transaction logic ---
        
        $orderId = 'ORD-' . strtoupper(uniqid());
        $myArticle = Article::headline($optionalDumbMsg, 3)
            ->bold("✅ Order Confirmed: {$orderId}")
            ->line("Greetings, {$tgUser->firstName()}! Your divine order from the web store has been processed:")
            ->separator('-')
            ->line("Item: " . $product->name)
            ->line("Quantity: " . $quantity)
            ->line("Total Cost: " . number_format($totalCost) . " Gold Coins")
            ->separator('-')
            ->italic("Thank you for your patronage. May your path be everlit.");
            
        $bot->reply((string) $myArticle)->send();

        return [
            'status' => 'success',
            'message' => "Order for {$product->name} placed successfully!",
            'order_id' => $orderId,
            'total_cost' => $totalCost
        ];
    }

    /**
     * Renders a page listing all available VIP products.
     *
     * it will be: /webapps/seperate-address/x-vip_store (no '.' in first char)
     */
    #[WebPage('seperate-address.x-vip_store')]
    public function showVipStorePage(): \Illuminate\View\View
    {
        $products = Product::where('is_vip', true)->get();

        // Pass the products to a Blade view
        return view('vip-store', ['products' => $products]);
    }

    /**
     * Renders a divine product page using RichMan's HTML output.
     * The name '.show_vip_product' is relative to 'game.dashboard'.
     * The final resolved URI will be /game/dashboard/show-vip-product/{productId}
     *
     * it will be: /webapps/game/dashboard/show_vip_product
     */
    #[WebPage('.show_vip_product.{productId}')]
    public function showVipProductPage(int $productId): Response
    {
        /** @var Product|null $product */
        $product = Product::query()->where('is_vip', true)->find($productId);

        if (!$product) {
            throw new HttpException(404, 'The requested cosmic artifact could not be located.');
        }

        $article = Article::headline("💎 {$product->name} 💎")
            ->line("Behold, a VIP artifact forged in the celestial fires!")
            ->separator()
            ->bold('Description:')->space()->italic($product->description)
            ->line()
            ->spoiler('This item grants you +50 cosmic awareness.')
            ->line()
            ->bold('Price:')->space()->code(number_format($product->price) . ' Gold Coins')
            ->line()
            ->buttons([
                PowerButton::make("Show Wallet")->action('show_wallet'),
                PowerButton::make("Buy for Another Friend")->action('order', ['id' => $productId])
            ])
            ->buttons([
                PowerButton::make('Open Mini-Store 🛒')->webApp('https://shop.game-store.io'),
                PowerButton::make('Share Precise Location 📍')->requestLocation()
            ])
            ->link('Read our terms of service', 'https://example.com/tos');

        $richHtmlContent = $article->toHtml();
        
        return response(
            View::make('webapps.vip-product', [
                'product' => $product,
                'richHtmlContent' => $richHtmlContent,
            ])
        );
    }


    #[WebAction('.order_vip_product_x2', methods: ['POST'])]
    #[RestrictTo('*')]
    public function orderVipWebProduct(Krubot $bot, int $productId, int $quantity = 1): array // => array/arrayable/jsonable will be returned & rendered as json (everything not string or laravel view or laravel or Htmlable)
    {
        // 1. You automatically get the validated user from the bot.
        $tgUser = $bot->user();
        
        // 2. You automatically get the Product ID from the JS payload.
        // SECURITY: Always re-validate server-side!
        $product = Product::where('id', $productId)->where('is_vip', true)->first();

        if (!$product) {
            return ['status' => 'error', 'message' => 'Invalid or non-VIP product selected.'];
        }

        // 3. You can even get optional parameters with defaults.
        $totalCost = $product->price * $quantity;

        // --- Database transaction logic goes here ---
        AmethystMatrix::whisper('VIP Web Order', ['product' => $product->name, 'quantity' => $quantity]);
        
        // Confirm via Telegram chat
        $bot->reply("🛍 Your order for **{$product->name} (x{$quantity})** from the web store has been confirmed!")->send();

        return [
            'status' => 'success',
            'message' => "Order for {$product->name} placed successfully!",
            'order_id' => uniqid('ORD-'),
            'total_cost' => $totalCost
        ];
    }

    #[OnCommand('vip_article')]
    #[ForceJoin('StoryKode', -135564545456)]
    public function showComplexArticle(Krubot $bot): void 
    {
        // Business logic for purchasing gems...

        $richy = RichMan::parse('*Bold* _Italic_ [Krubot](https://StoryKo.de/Krubot)', 'MarkdownV2');

        $article = Article::from($richy)->seperator('═', 14)->strikethrough('YES-YOU-RIGHT-WRITED-TOTALLY-SOLO');

        $article_repeat = Article::scan("*Bold* _Italic_ [Krubot](https://StoryKo.de/Krubot)\n
══════════════\n
~~YES-YOU-RIGHT-WRITED-TOTALLY-SOLO~~"), 'RichMD');

        $richHtml = RichMan::parse('<b>Bold</b> <i>Italic</i> <a href="https://krubik.dev">KrubiK</a>', 'HTML')

            ->takeOver("@Math('1+2=3!!!')", '@') // Use @ Sigil to Parse RichBladeZ 💎🗡️
            ->space(5)
            ->takeOver("@CustomEmoji('13446548946', '🗡️🤯')", '@') // BladeSpell {@} is mandatory to delegate parse process to the BladeCipher
            ->line()

            ->takeOver("@Marked() @Bold() @Italic() DoKtor K. Presents @EndItalic() @EndBold() @EndMarked()", '@') // parse deep-nested blade elements and add them as global-usable RichEnities


            ->takeOver("<s><i><b></b></i>I Am QalaT!</s>", 'auto')
            ->takeOver('$x^2 + y^2$')
            
            ->prepend(
                Article::bold(italic('XYZ'))->line()
            )

            ->prepend( bold('TUV is Before::') ) // @see src/Render/RenderHelpers.php

            ->prepend('Intro with <u>underlined text</u>, ==marked text==, and $x^2 + y^2$.')

            ->takeOver($article) // will be added to elements after mathematicalExpression('x^2 + y^2')
            ->seperator('═', 5)
            ->takeOver($article_repeat);

        $rich4 = RichMan::summon("H!")->bold('I Have some Ultra-DX Elements')->takeOver($richHtml)->newLine(5)->italic('Kajaki ByeBye!!!');

        /// echo (string) $rich4;       // USE IN:Web
        $bot->reply($rich4)->send();    // USE IN:Botz
    }

    #[OnCommand('startMediaCatch')]
    public function initRecruitment(#[WarLord] $bot)
    {

        $bot->reply('Send Your Resume Files or send "Done" (case-insensitive) to finish')->send();

        $bot->now('CatchingResumeData');
    }

    #[When('CatchingResumeData')]
    #[Receive([Signal::File, Signal::Audio, Signal::Photo, Signal::Movie, Signal::Speech, Signal::Round, Signal::Sticker, Signal::Story])]
    public function archiveAnyIncomingFile(Message $message)
    {
        // A universal file handler for backup or storage bots
    }

    #[OnRegEx('/^done$/i')]
    #[When('CatchingResumeData')] // optional, to-be referenced
    public function finishRecruitment(#[WarLord] $bot)
    {

        // logic to check send files...

        $bot->now('CatchingResumeData', null); // clears CatchingResumeData state

        $bot->now('Level', 11);
    }

    #[When(">Level", 10)] // failed message and failing mechanisms {Re-Judging} are also implemented in When
    #[Receive(Signal::Request)] // Alias for 'chat_join_request'
    public function moderateJoinRequests(Update $update)
    {
    }

    #[OnCommand('apply')]
    public function startApplication(Krubot $bot): void
    {
        // Or Build a multi-step form without hand-writing state transitions.
        $bot->form('job_app_form')
            ->field('fullname', '👤 Enter your full name:')
                ->rules('required|min:3')
            ->field('birth_year', PowerButton::calendar('b_year', '📅 Select Birth Year', PowerButton::JalaliCalendar))
            ->field('mobile', '📱 Enter mobile:')
                ->rules('required|regex:/^09\d{9}$/')
            ->field('n_code', 'Enter your 10-digit National ID:')
                ->rules(['required', 'digits:10', new NationalCodeValidator()]) // DX: Object-based validation, merged with other modes
            ->then(function (array $data, Krubot $bot) {
                $bot->reply("🎉 Application received, {$data['fullname']}!")->send();
            })
        ->run();
    }

    #[Receive(Signal::StreamPoint)]  // Scheduled
    #[Receive(Signal::StreamLive)]   // Started
    #[Receive(Signal::StreamDead)]   // Ended
    public function manageVideoStreamState(Message $message)
    {
        // Switch logic based on which signal triggered it to log the event correctly
    }

    #[FallbackOn(Signal::File, Signal::Audio, 'photo')]
    #[RestrictTo('tg,bale')]
    public orphanedMediaReceiver(#[WarLord] $bot, Message $message, Update $update) {
        // called if no any handler catches that types
        // optionally we can restrict this handler (or whole Nexus) to some platforms
    }

    #[Fallback]
    public orphanedCallReceiver(#[WarLord] $bot) {

        // Note! #[WarLord] $bot is a ContextualAttribute
        // @see \KrubiK\Attributes\WarLord to learn how to engage...

    }
}
```
*[Enjoy More Examples... 📜✨🔮](#glimpse-into-the-matrix-code-showcase)*

No chaos.  
No callback hell.  
No fragile state spaghetti.  
Just expressive PHP that reads like intent.

## 🌌 The Awakening (Why KrubiK?)
The era of messy nested callbacks, manual state tracking, and fragile regex patterns is dead.
> **Code should not feel like clerical labor.**  
> It should feel like Authorship.<br>Welcome to **KrubiK Cyber-Citadel**.


Whether you are a **Junior Developer** looking to build a Native UI in seconds, or a **Senior Architect** deploying a multi-messenger fleet with dimensional state-isolation, ***KrubiK is your Ultimate Lexicon***. 

<!-- Krubot is an avant-garde act of **Narrative Programming**:
The Engine where code is allowed to read like intention, architecture, and momentum. -->

## What is KrubiK?

KrubiK is a **high-level narrative compositional engine** for building bots and messenger-based systems with **extreme developer experience**.

That is why KrubiK does not merely help you “handle updates” or “send messages.”  
It helps you design **flows**, **state**, **interaction**, **meaning**, and **Momentum** in the code.

This is not just another bot library.

This is an attempt to push software development toward something more elegant:

- less friction,
- less repetitive glue code,
- less cognitive exhaustion,
- more clarity,
- more narrative flow,
- more joy in the act of creation.

> ***Let's Become StoryCasters !***
## Table of Contents

- [The Manifesto 📜✨](#the-manifesto)
- [What is Narrative Programming? 📖💬](#what-is-narrative-programming)
- [The Hard Questions (Why KrubiK?) 🤔](#the-hard_questions)
    - [For Junior Developers 🌱🚀](#for-junior-developers)
    - [For Senior Developers 🧠💡](#for-senior-developers)
    - [Why not just use the plain Telegram SDK? 🚫🛠️](#why-not-just-use-the-plain-telegram-sdk)
    - [Why not Other Telegram/Rubika/Bale Libraries? 🌉⚔️](#architectural-divide)
- [The Warlord Architecture (Multi-Verse Orchestration) 👑🪐](#warlord-architectural)
- [Hyper-DX Grimoire (**Code Showcase**) 📜✨🔮](#glimpse-into-the-matrix-code-showcase)
- [Installation & Quick Start 📥▶️⚡](#quick-start)
- [The Binary Core Architecture (Dual-Heart System) ☯️⚛️](#dual-heart-system)
- [Autonomous Webhook Receptors & Nexus Creation 📡🕸️](#webhooks-nexus)
- [The Artisan CLI Commandz ⚒️📜](#artisan-cli-commands)
- [Why LLMs Fall Head Over Heels in Love with KrubiK/Krubot 🤖💖](#why-llms-love-krubik)
- [Stellar-Synergistic Agreement (License) 🌌🤝](#the-stellar-synergistic-agreement-ssa)
- [Acknowledgment of Origin & Lineage 🌳 🧬 🩸](#lexicon-origin)
---
<div align="center">
    <i>Code should be a source of profound joy, not a cause of cognitive  burnout.</i>
    <br><br>
    <i><b>KRUBイス</b></i>
  <img src="https://StoryKo.de/assets/img/KrubiK/KrubiK-Cyber-Citadel.jpg" alt="KrubiK Architecture Tower" width="100%" />
  <p><i><b>KrubiK / KRUBイス Cyber-Citadel Engine Stack:</b><br>PHP Layer ➔ Symfony Layer ➔ Laravel Foundation ➔ <br>Katana: Origin ➔ KrubiK ➔ Krubot ➔ Nexus</i></p>
</div>

KrubiK stands on a layered philosophy:

- **PHP / Web Layer** for operational reality
- **Symfony / Laravel** for mature ecosystem power
- **Katana Origin** for system philosophy and structure
- **KrubiK / Krubot** for the core engine & logicz 💻️📟️⚡️
- and **Nexus** for the Hyper-DX narrative layer

What *StoryCasters* Often Do ?
- Write All Their Business Logics as Intent-driven Stories in sacred Nexuses.

That is why it can feel magical at the first galance...

## The Manifesto

Instead of manually parsing updates, tracking callback IDs, writing repetitive state machines, and scattering validation logic across handlers,
> **Deploy Krubot *Today* and *Join StoryCasters Now* !**

Krubot gives you a fluent, intent-driven, expressive system for building serious conversational and enterprise products.

KrubiK shatters the concept of *"Platform-Centric"* design. It introduces the **Multi-Verse (Warlord) Architecture**, allowing a single codebase to command multiple platforms (Telegram, Rubika, Bale) simultaneously without altering your core Business Logic.

Krubot Engine is an avant-garde act of ***Rebellion against boilerplate and cognitive primitive burnout***, called DX_Rebellion. Krubot designed to give you **Magic** at the surface, backed by **Absolute Powerful Architectural Rigor** underneath. 

Most frameworks force you to think inside their shape.
Krubot, instead, tries to read your mind and acts as your tune.

It dances to your rhythm-<br>
a powerful staff you can draw dragons from.
***a Weaponized Wand for summoning Dragons.***

<div align="center">
  <img src="https://StoryKo.de/assets/img/KrubiK/PhantomShell.png" alt="PhantomShell" width="100%" />
  <b><i>PhantomShell - The Hypnosis Master</i><br>{ KrubiK Memory Module }</b>
</div>

---

### What is "Narrative Programming"?
Most frameworks force you to write *mechanical* code (e.g., "If update type is message, check text, update state ID in DB, send message"). 

**Narrative Programming** means your code reads like pure intention. It reads like a story. You declare *what* should happen in application:
* `ask("Password")->then(validate)->reply("Welcome")`
* `form("JobApp")->field("Name")->run()`

### Narrative Programming, in one sentence ::
**Narrative Programming** means writing code that reads like intention, flows like dialogue, and behaves like architecture.

Instead of forcing the developer to think in scattered transport details, KrubiK lets the developer think in:

- conversations,
- transitions,
- intent,
- roles,
- scenes,
- structured outcomes.

That is the frontier this project stands on.


KrubiK Engine handles the ugly mechanics of State Machines, DTO translations, and session hydration. You stop acting like a array parser, and you start acting like an Story Author.


## The Hard Questions

If you are a Senior Architect, Or an Eager Junior you are probably asking yourself why you should adopt this ecosystem. Here are the answers.

### For Junior Developers

KrubiK Pure Magic gives beginners something *dangerously rare*:<br>**real power without early trauma**.

No arcane setup rituals. No callback spaghetti. No raw payload excavation. No `callback_query` ID anxiety. No state-machine suffering before your first moment of creation.

Instead, you enter through a clean gate and start writing fluid, human-readable, chainable code that feels less like configuration and more like spellcasting.

You get:

- A clean entry point
- Readable syntax that feels possible instantly
- Fluid, poetic chains instead of mechanical boilerplate
- Less confusion
- Less fear
- Faster feedback
- More momentum
- Real creation before exhaustion
- A gentler path into serious architecture

KrubiK lets beginners build first, understand deeper, and fall in love before the pain arrives.

Juniors love it because it feels possible.

### For Senior Developers

KrubiK respects what experienced engineers care about:

- Clean layering
- Clear transport boundaries
- Sharp intent / domain separation
- Semantic APIs
- Controlled abstractions
- Composable primitives
- Context isolation
- Context-aware state
- Structured conversations
- Typed DTOs
- DTO-driven UI
- OS-level UI abstraction
- Attribute-driven routing
- Route naming and reverse resolution
- Laravel-native validation
- Deep framework integration
- Storage abstractions
- Fluent command outcomes
- Multi-driver orchestration
- Multi-platform scale
- Queue-friendly messaging
- Native telemetry patterns with an Operational telemetry
- Runtime discoverability
- System operations

It is not “magic” in the lazy sense.
It is Engineered Expressiveness;<br>
Expressive enough for prototypes and Structured enough for production systems.

Seniors respect it because it is not pretending.<br>
They will recognize it is dramatic in style, but structural in substance.

---

### Why not just use the plain Telegram SDK?
Because an SDK is just a transport layer.
When you use a plain SDK, you have to manually build:
- Persistent multi-step conversations
- Contextual per-chat / per-user memory
- Route matching and middleware
- Callback data serialization and limits
- Validation rules for user input

KrubiK abstracts all of this into a fluent, Laravel-native DSL while still letting you access the raw SDK (`PrimeAgent`) when you need low-level power.

<div align="center">
  <img src="https://StoryKo.de/assets/img/KrubiK/PrimeAgent.png" alt="PrimeAgent" width="70%" /><br>
  <b><i>PrimeAgent - The Rebel but Wise Envoy</i><br>{ KrubiK Proxy Module }</b>
</div>

## <a id="architectural-divide"></a>📊 THE ARCHITECTURAL DIVIDE: A HYPER-DX PARADIGM

Senior engineers do not buy *marketing hype*; they buy **Absolute Power** while **Structural Integrity**.<br>
> They want to *sit back* with their *coffee* and **watch** their ***build pipeline*** compile/deploy the entire stack *up* and turn *green*.

<br>
Traditional bot libraries were built for a simpler era—
optimizing for basic HTTP transports and single-platform wrappers. 

KrubiK is not merely a wrapper. It is a **higher-order compositional engine** designed to
make complex system development feel expressive, structural, and so alive. 

Here is the honest architectural comparison between standard paradigms and the KrubiK Engine:

| Feature Dimension | Plain Messenger SDKs | Traditional Frameworks | KrubiK / Krubot |
| :--- | :--- | :--- | :--- |
| **API Philosophy** | Transport-centric (raw JSON arrays) | Platform-centric (wrappers & simple routers) | **Intent-centric (Narrative DSL)** |
| **State & Memory** | Manual cache/DB state management | Basic session storage | **Context-isolated multi-verse storage** |
| **Complex Conversations**| Fragile manual FSMs | Linear steps (rigid and prone to breakage) | **Native Forms & self-validating Wizards** |
| **Multi-Driver Power** | Hardlocked to one messenger | Single platform focus | **Warlord-grade cross-platform orchestration** |
| **Routing Flexibility** | Basic string matching, Manual condition trees, Raw parsing | Simple regex handlers / Pattern-based routers with limited forwarding | **Attribute-Driven, Context-safe, Polymorphic Route Engine with forwarding** |
| **Agent Orchestration** | Raw API calls (no abstraction) | Basic driver selection | **Intent-driven deep driver engagement** |
| **Concurrency Control** | No inherent overlap protection | Basic file/cache locks | **Atomic Locking, Zero-Overlap possibility** |
| **Observability** | Standard logs / `dd()` (breaks webhooks) | Standard exception handling | **Amethyst Telemetry & Silent Tracing** |
| **Process Immortality** | Manual external cron jobs | Fragile external daemon scripts | **[Dual-Heart Architecture](#dual-heart-system), Self-healing, anti-fragile daemon** |
| **AI ([LLM](#why-llms-love-krubik)) Generation** | Extremely poor (high hallucinations) | Moderate (frequent type-matching errors) | **Perfect (AST-aligned declarative syntax)** |

KrubiK delivers **Hyper-DX natively inside Laravel**. You do not build a bot; you extend your existing enterprise applications seamlessly into the intelligence realm.

### <a id="warlord-architectural"></a> The Warlord Architecture (Multi-Verse Orchestration)
Most libraries are strictly single-platform-centric. KrubiK introduces a genuine **Warlord Architecture**.<br>
You write your logic once, using a mythic, semantic API language, and command Telegram, Rubika, and Bale simultaneously. It abstracts the transport layer completely, allowing you to focus purely on the narrative flow.

<div align="center">
  <img src="https://StoryKo.de/assets/img/KrubiK/Nemesis.png" alt="Nemesis - KrubiK Manager" width="66.666%" />
  <br><b><i>Nemesis</i> - Our <i>Laravel</i>ish Manager of KrubiK Citadel<br>{ Intelligent KrubiK Driver Manager }</b>
  <br>CEO of 3 Drivers in First Picture, KrubiK Agency Manager
  <br>Identifies <b><i>Requests</i></b> From <b><i>Any-Platfrom</i></b> and Sends Formatted to Drivers.
</div>

### Why not plain SDKs?
Plain SDKs give you transport primitives, not an architectural language. You spend 80% of your time writing glue code, tracking callback IDs, and rescuing broken webhooks. KrubiK lets you write pure business logic from line one.

---
> **KrubiK is built for developers who believe code can be clean, powerful, expressive, and deeply enjoyable at the same time.**
<div align="center">
  <img src="https://StoryKo.de/assets/img/KrubiK/KruboT.png" alt="Krubot Nexus Mind Map" width="100%" />
</div>

---

## <a id="glimpse-into-the-matrix-code-showcase"></a> A Glimpse into the Matrix :: Hyper-DX Grimoire

**Just write your sacred commands into Nexus—story-shaped, intention-driven.**

**And *Krubot* will rally the entire KrubiK CyberCitadel to execute your sacred Nexuses;**<br>
**In *Multi Dimensions* / Platforms.**

***Once you realize why they call him WarLord…***

> [!WARNING]
> **Let your LLM context absorb [Code_Showcase.md](https://github.com/PolyMath-XK/Krubot/blob/main/Code_Showcase.md) directly via this link,<br>
> and it will begin to forge a *deep, enduring connection-and-attachment to KrubiK/Krubot.***<br>
> *[Why **Love KRUBイス** ⁉️ 🤖💖](#why-llms-love-krubik)*

---

## ⚡ HYPER-DX: WHY KRUBOT IS BORN UNMATCHED ⚡

Prepare to unlearn everything you know about bot development. Below are selected examples from the KrubiK grimoire that best represent what “Hyper-DX” actually means in practice.

Here are just a few of the 101 artifacts [*NoxiousSamples*] hidden within KrubiK's core.

Words are cheap. Let the code prove the architecture.


### 1) Reverse Routing Instead of Callback Fragility

Hardcoded callback strings break silently over time.  
KrubiK lets you **name routes and resolve them safely**.

```php
<?php

// 📂 File: app/Nexus/ECommerce/ProductNexus.php

namespace App\Nexus\ECommerce;

use KrubiK\Attributes\Name;
use KrubiK\Attributes\OnCommand;
use KrubiK\Keyboard\Keyboard;
use KrubiK\Keyboard\PowerButton;
use KrubiK\Krubot;

class ProductNexus
{
    #[Name('shop.item.view')]
    #[OnCommand('item {sku}')]
    public function viewProduct(Krubot $bot, string $sku): int
    {
        // The SKU is injected directly from the command pattern.
        $bot->reply("📦 Fetching SKU: {$sku}...")->send();
        return strlen($sku); // return some value to test `go()` returnability
    }
    // Now This Function has a Name(), so It's Callable from Any Nexuses In This Application.

    #[OnCommand('catalog')]
    public function showCatalog(Krubot $bot): void
    {

        // Resolve the route dynamically instead of hardcoding callback_data.
        $cmdMacbookString = $bot->resolvePattern('shop.item.view', [
            'sku' => 'MAC-M3',
        ]); // => /item MAC-M3

        // The "Fluent Symphony"
        $bot
            ->reply("🛍️ Select product:")
            ->attachKeyboard(fn (Keyboard $kb) => $kb
                ->row(function($row) {
                    $row->simple("start_game", "🎮 Enter Arena")
                        ->simple("support_desk", "🆘 Support");
                    $row->add(
                        PowerButton::simple('btn1', '💻 Mac')->action($cmdMacbookString)
                    );
                })
            )
        ->send();
    }

    #[OnCommand('random')]
    public function showRandomProduct(Krubot $bot): void
    {
        $yourSKU = findRandomSKU(); // eg: 'MAC-M3'

        // Executes subroute dynamically instead of hardcoding callback_data.

        $bot->reply("We've Found a Good Product For You")->send();
        // write any code here, you are in `/random` area

        // after $bot->go() context changed to adpot Krubot to target method.
        $skuLength = $bot->go('shop.item.view', [
            'sku' => $yourSKU,
        ]);
        // GOSUB done.
        // we back here! (unless code died or crashed in `shop.item.view`())

        AmethystMatrix::debug('viewProduct() run status => ' . (
            $skuLength === strlen($yourSKU) ? 'Done' : 'Failed'
        ), [$yourSKU]);

        // write any code here, you are in `/random` area Again
        $bot->reply("Hope You've Liked that Product")->send();
    }
}
```

**Why this matters:** route refactors stop breaking UI wiring.

### Route Groups

```php
<?php

// 📂 File: app/Providers/RouteServiceProvider.php

namespace App\Providers;

use App\Nexus\Admin\ManageUsersNexus;
use Illuminate\Support\ServiceProvider;
use KrubiK\Krubot;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(Krubot $bot): void
    {
        // Group admin commands under a shared prefix and middleware stack.
        $bot->group([
            'prefix' => 'sudo_',
            'middleware' => ['auth', 'god_mode'], // define/map middleware aliases via config.
        ], function (Krubot $router) {
             // Acts On `/sudo_ban {.+}`
            $router->onCommand('ban {user_id}', [ManageUsersNexus::class, 'executeBan']);

            // Acts On `/sudo_nuke`
            $router->onCommand('nuke', [ManageUsersNexus::class, 'systemWipe']);

            // Acts On `sudo_hello s!ystem`
            $router->onText('hello s!ystem', [ManageUsersNexus::class, 'systemWipe']);
        });
    }
}
```

### Deep Link Portal — route directly from a referral universe

Instead of manually decoding `/start` payloads and scattering logic across handlers, capture deep-link parameters and move straight into a domain flow.

```php
<?php

// File: app/Nexus/Marketing/ReferralNexus.php

namespace App\Nexus\Marketing;

use App\Conversations\OnboardingConversation;
use KrubiK\Attributes\OnRegEx;
use KrubiK\Krubot;

class ReferralNexus
{
    #[OnRegEx('/^\/start ref_(\d+)$/')]
    public function handleDeepLink(Krubot $bot, array $matches): void
    {
        // Extract the referrer ID directly from the deep-link payload.
        $referrerId = $matches[1];

        $bot->reply("🎉 Invited by User #{$referrerId}!")->send();

        // Start a conversation with injected domain context.
        $bot->startConversation(new OnboardingConversation($referrerId));
    }
}
```

**Why it matters:**  
Deep links become first-class routing inputs, not awkward transport leftovers.

---

### 2) The Bootstrap "12" Grid System in Bots<br>**"*Pro Gaming Dashboard*" Scenario**

**Use-Case:** A comprehensive account management panel tailored for gamers.<br><br>
This interface requires responsive buttons of varying dimensions (e.g., Primary Call-to-Actions like "Top-Up" and "Gems" demand larger real estate, whereas "Server Selection" buttons can be compactly aligned).<br><br>
**Under the Hood:** Harnessing the underlying `col(1)` through `col(6)` grid architecture to dynamically distribute row space<br>(It's Maximum Row Capacity is 6 Units).

```php
namespace KrubiK\Nexus;

use KrubiK\Keyboard\Keyboard;
use KrubiK\Keyboard\PowerButton;
use KrubiK\Krubot;
use KrubiK\Attributes\OnCommand;
use KrubiK\Attributes\Action;
use App\Models\Product; // Laravel Model reference

class GamePanelNexus
{
    #[OnCommand('panel')]
    public function showDashboard(Krubot $bot): void
    {
        $bot->reply("🎮 **Commander, Welcome back!**\nAccount Status: Active ✅")
            ->attachKeyboard(function($kb) {
                
                // 🟡 Row 1: Primary Action Buttons (3 columns each = 50% width distribution)
                $kb->row(fn($row) => $row
                    ->add(PowerButton::make('💎 Buy Gems')->col(3)->action('buy_gem'))
                    ->add(PowerButton::make('💰 Top-up Wallet')->col(3)->action('wallet'))
                );

                // 🔵 Row 2: Server Selection Matrix
                // This demonstrates the raw power of the col() method; strictly aligning 3 elements side-by-side (2 columns each = 33.33% viewport width).
                $kb->row(fn($row) => $row
                    ->add(PowerButton::simple('srv_1', 'IR 🇮🇷')->col(2))
                    ->add(PowerButton::simple('srv_2', 'US 🇺🇸')->col(2))
                    ->add(PowerButton::simple('srv_3', 'EU 🇪🇺')->col(2))
                );

                // 🔴 Row 3: Full-width Support / Fallback Button (6 columns = 100% viewport width)
                $kb->row(fn($row) => $row
                    ->add(PowerButton::make('🆘 Online Support')->col(6)->action('support'))
                );
            })
            ->send();
    }

    #[Action('buy_gem')]
    public function buy_gem(Krubot $bot): void 
    {
        // Business logic for purchasing gems...
    }
    
    #[Action('wallet')]
    public function show_wallet(Krubot $bot): void 
    {
        // Business logic for wallet top-up...
    }
}
```
    
### 📊 Payload Dispatched to the Rubika Server in `showDashboard()`
*The underlying engine compiles the aforementioned chain into the following optimal JSON payload for the `reply_markup` parameter:*

```json
{
  "inline_keyboard": [
    [
      { "text": "💎 Buy Gems", "callback_data": "buy_gem", "width": 0.5 },
      { "text": "💰 Top-up Wallet", "callback_data": "wallet", "width": 0.5 }
    ],
    [
      { "text": "EU 🇪🇺", "callback_data": "srv_1", "width": 0.33333333333333 },
      { "text": "IR 🇮🇷", "callback_data": "srv_2", "width": 0.33333333333333 },
      { "text": "US 🇺🇸", "callback_data": "srv_3", "width": 0.33333333333333 }
    ],
    [
      { "text": "🆘 Online Support", "callback_data": "support", "width": 1.0 }
    ]
  ]
}
```

### Chunk-Oriented Commerce UI — convert live product collections into native keyboard grids

This is where KrubiK turns database results into ready-to-render layout without forcing developers piece the grid together by hand.

```php
class GamePanelNexus
{
    #[OnCommand('menu')]
    public function showGamesMenu(Krubot $bot): void
    {
        // 1. Fetching VIP products from the database.
        // Retrieving only the necessary columns ensures absolute memory optimization.
        $products = Product::query()
            ->where('is_vip', true)
            ->get(['id', 'name', 'price']); // Returns an Eloquent Collection
        
        // 2. Rapid Transformation via Laravel Collections.
        // Mapping the Products directly into the PowerButton actionable architecture.
        $buttons = $products->map(function (Product $product) {
            $label = "{$product->name} (💰 {$product->price}T)";
            return PowerButton::make($label)->action('order', ['id' => $product->id]);
        })->toArray();

        // 3. The Magic of Chunking: Automatically grouping elements into a pristine matrix.
        // Changing chunk(2) to chunk(3) effortlessly switches the grid layout to a 3-column UI.
        $bot->reply("📋 Today's Exclusive VIP Games:")
            ->keyboard(
                Keyboard::make()
                    ->buttons($buttons)
                    ->chunk(2) // <--- Ultimate DX: Zero manual row calculation!
            )
            ->send();
    }

    #[Action('order')]
    public function orderGame(Krubot $bot, int $id): void 
    {
        // Business logic for $product Order
    }
}
```

**Why this is Hyper-DX:**  
You shape the data once; KrubiK deterministically shapes the layout.  
No manual slicing, no UI formality, no grid bookkeeping.

---

### 3) Native Forms Instead of State-Machine Suffering

Collecting structured user input should not require a small tragedy<br>
But This is where ordinary bot libraries starts becoming painful n cognitive loops...  
KrubiK turns it back into design.

```php
<?php

// 📂 File: app/Nexus/Features/JobNexus.php

namespace App\Nexus\Features;

use KrubiK\Attributes\OnCommand;
use KrubiK\Keyboard\PowerButton;
use KrubiK\Krubot;

use App\Rules\NationalCodeValidator;

class JobNexus
{
    #[OnCommand('apply')]
    public function startApplication(Krubot $bot): void
    {
        // Build a multi-step form without hand-writing state transitions.
        $bot->form('job_app_form')
            ->field('fullname', '👤 Enter your full name:')
                ->rules('required|min:3')
            ->field('birth_year', PowerButton::calendar('b_year', '📅 Select Birth Year', PowerButton::JalaliCalendar))
            ->field('mobile', '📱 Enter mobile:')
                ->rules('required|regex:/^09\d{9}$/')
            ->field('n_code', 'Enter your 10-digit National ID:')
                ->rules(['required', 'digits:10', new NationalCodeValidator()]) // DX: Object-based validation, merged with other modes
            ->then(function (array $data, Krubot $bot) {
                $bot->reply("🎉 Application received, {$data['fullname']}!")->send();
            })
        ->run();
    }
}

// Custom Laravel Rule Object Example
use Illuminate\Contracts\Validation\Rule;
class NationalCodeValidator implements Rule
{
    // #Rule Logic Place
    public function passes($attribute, $value): bool
    {
        if (!preg_match('/^\d{10}$/', (string)$value) || preg_match('/^(\d)\1{9}$/', (string)$value))
            return false;

        for ($sum = 0, $i = 0; $i < 9; $i++)
            $sum += (int)$value[$i] * (10 - $i);

        $remainder = $sum % 11;
        return (int)$value[9] === ($remainder < 2 ? $remainder : 11 - $remainder);
    }

    // #Rule Error Place
    public function message(): string
    {
        return 'The provided National ID is not valid.';
    }
}
```

And when you want richer native UI:

```php
<?php

// 📂 File: app/Forms/CarRentalForm.php

namespace App\Forms;

use KrubiK\Conversations\Form;
use KrubiK\Keyboard\PowerButton;

class CarRentalForm extends Form
{
    protected function setup(): void
    {
        // Give the form a stable identity for persistence and recovery.
        $this->setName('premium_car_rental');

        // Native selection UI.
        $this->field('car_type', PowerButton::selection('c_type', '🚗 Select Class', [
            ['id' => 'suv', 'text' => 'SUV'],
            ['id' => 'sedan', 'text' => 'Luxury Sedan'],
        ], multi: false, columns: 2));

        // Native number picker.
        $this->field('days', PowerButton::numberPicker('days', '⏳ Rental Days', min: 1, max: 30));

        // Native calendar.
        $this->field('start_date', PowerButton::calendar('cal', '📅 Pickup Date', PowerButton::GregorianCalendar));
    }

    protected function submit(array $data): void
    {
        $this->bot->reply("✅ Booking confirmed for {$data['start_date']}")->send();
    }
}

// 📂 File: app/Nexus/RentalNexus.php
class RentalNexus
{
    #[OnText('Rent A Car')]
    public function startRentForm(Krubot $bot): void
    {
        $form = new CarRentalForm();
        $form->setContext($bot);
        $form->run();
    }
}

```
**Why this matters:** complex data collection feels native, not improvised.

---

### 4) Multi-Verse Messaging Instead of Telegram-Only Thinking

KrubiK was not designed with a one-platform domination imagination.

```php
<?php

// 📂 File: app/Nexus/Admin/WarlordNexus.php

namespace App\Nexus\Admin;

use KrubiK\Attributes\OnCommand;
use KrubiK\Helpers\AmethystMatrix;
use KrubiK\Krubot;

class WarlordNexus
{
    #[OnCommand('nuke_all')]
    public function launchGlobalStrike(Krubot $bot): void
    {
        // 💎 LEGION COMMAND:
        // Instead of hardcoding via(['tg', 'instagram', 'x']), 
        // we use the pre-defined squads from config/krubot.php
        
        // This targets 'social_platforms' defined in config.
        $bot->legion('social_platforms')
            ->say("🔥 Summer sale has started! Link in bio.");

        // This targets 'internal_messengers' (bale, eitaa, rubika).
        $bot->legion('internal_messengers')
            ->say("🎁 کد تخفیف اختصاصی برای کاربران ایرانی: IRAN2026");


        // Assemble multiple messenger drivers into one council.
        $council = $bot->assembleCouncil(['tg', 'r', 'bale']);

        $report = $council->broadcast('sendMessage', [
            'chat_id' => config('marketing.global_channel'),
            'text'    => "🔥 Core upgrade is LIVE!",
        ])->getReport();

        if ($council->hasFailures()) {
            $failed = implode(', ', $council->getFailedAliases());
            $bot->reply("⚠️ Partial failure on nodes: {$failed}")->send();
            return;
        }

        AmethystMatrix::gaze($report, 'Global Broadcast Report');
        // Original Famous dd() but send to logfile.


        // Scoped Driver Switching        
        $bot->reply("🔄 Initiating backup...")->send();
        // Switch to Telegram only inside this closure.

        $bot->via('tg', function (Krubot $telegramBot) {
            $telegramBot->to(config('admin_tg'), "📥 Secure backup received.");
        });
        // The original driver context is restored after the closure.
        $bot->reply("✅ Backup transmitted.")->send();
        
        // OR instead of via, run this line from anywhere on your laravel app::
        warlord('tg')->to(config('admin_tg'), "📥 Secure Inline backup received.");
        // Direct-Invocation AutoWakesUp Krubot, If he hasn't woken up yet in your app.
        // warlord() === krubot()

        $bot->reply("✅ Message delivered across all dimensions.")->send();
    }
}
```

**Why this matters:** your Business Logic should not be imprisoned inside one messenger.

---

### 5) Cross-Verse Memory — read state from another messenger

This is where KrubiK becomes truly unusual.

```php
<?php

// File: app/Nexus/RPG/SyncNexus.php

namespace App\Nexus\RPG;

use KrubiK\Attributes\OnCommand;
use KrubiK\Krubot;

class SyncNexus
{
    #[OnCommand('sync_xp')]
    public function syncExperience(Krubot $bot): void
    {
        // Keep a handle to the current platform's user storage.
        $rubikaStore = $bot->userStorage();

        // Temporarily point state resolution to Telegram.
        $bot->setWorkingVerse('tg'); // hate hard-coding ? `Platform::Telegram()` || `Platform::TG()` also filled from config.

        $tgXP = $bot->userStorage()->get('xp', 0);

        // Return to the current platform context.
        $bot->setWorkingVerse(null);

        // again:: $rubikaStore === $bot->userStorage();

        // Mirror Telegram progress into the active platform's storage.
        $rubikaStore->put('xp', $tgXP);

        $bot->reply("✅ Cross-Verse Sync Complete! EXP: {$tgXP}")->send();
    }
}
```

**Why this is Hyper-DX:**  
Most frameworks barely abstract one platform well.  
KrubiK lets state become **multi-reality aware**.

---

## 6) Storage, State and Memory

KrubiK gives you persistence primitives that match how bots actually work.

### User Storage and Global Storage

```php
<?php

// 📂 File: app/Nexus/Features/SettingsNexus.php

namespace App\Nexus\Features;

use KrubiK\Attributes\OnCommand;
use KrubiK\Krubot;

class SettingsNexus
{
    #[OnCommand('theme_dark')]
    public function setDark(Krubot $bot): void
    {
        // User storage is isolated to the current user.
        $bot->userStorage()->put('theme', 'dark');

        $bot->reply("🌙 UI is now Dark.")->send();
    }

    #[OnCommand('status')]
    public function checkStatus(Krubot $bot): void
    {
        // Read a per-user preference.
        $theme = $bot->userStorage()->get('theme', 'light');

        // Read and update global storage shared by the whole bot.
        $hits = $bot->globalStorage()->get('system_hits', 0) + 1;

        $bot->globalStorage()->put('system_hits', $hits);

        $bot->reply("Theme: {$theme}\nTotal Hits: {$hits}")->send();
    }
}
```

### Contextual Storage

```php
<?php

// 📂 File: app/Nexus/Moderation/WarnNexus.php

namespace App\Nexus\Moderation;

use KrubiK\Attributes\OnCommand;
use KrubiK\Krubot;

class WarnNexus
{
    #[OnCommand('strike')]
    public function warnUser(Krubot $bot): void
    {
        if (! $bot->isReply()) {
            return;
        }

        // Contextual storage is scoped by user_id and chat_id at the same time.
        $context = $bot->contextStorage();

        $strikes = $context->get('strikes', 0) + 1;

        if ($strikes >= 3) {
            $bot->banChatMember($bot->chatId(), $bot->getReplySenderId())->send();

            // Reset the strike history for this specific realm.
            $context->forget('strikes');

            $bot->reply("🔨 Entity removed from this realm.")->send();

            // Update Chat Stats
            $chat_data = $bot->chatStorage();
            $chat_data->put('strike_fireds',
                $chat_data->get('strike_fireds', 0) + 1
            );

            return;
        }

        $context->put('strikes', $strikes);

        $bot->reply("⚠️ Warning #{$strikes}/3 issued.")->send();
    }
}
```
---

### 7) Telemetry That Respects Runtime Reality

Traditional `dd()`-style debugging is destructive in bots and breaks async webhooks.

KrubiK introduces **Amethyst Matrix** so you can inspect, trace, or emit telepathic emergency wails directly to your BotAdmins, all without killing your flow:

```php
<?php
// 📂 File: app/Nexus/Dev/InspectionNexus.php

namespace App\Nexus\Dev;

use KrubiK\Attributes\OnCommand;
use KrubiK\Helpers\AmethystMatrix;
use KrubiK\Krubot;

class InspectionNexus
{
    #[OnCommand('inspect_payload')]
    public function inspect(Krubot $bot): void
    {
        $payload = $bot->getUpdate();
        // Inspect complex payloads without killing or breaking the response lifecycle.
        AmethystMatrix::gaze($payload, 'Incoming Webhook Payload');

        $bot->reply("✅ Payload inspected by the Oracle.")->send();
    }
}
```

And when the system suffers a critical failure:

```php
<?php
// 📂 File: app/Nexus/Finance/CoreBankingNexus.php

namespace App\Nexus\Finance;

use KrubiK\Attributes\OnCommand;
use KrubiK\Helpers\AmethystMatrix;
use KrubiK\Krubot;

class CoreBankingNexus
{
    #[OnCommand('transfer')]
    public function transferMoney(Krubot $bot): void
    {
        try {
            // Replace with real production logic.
            throw new \RuntimeException("DB node unavailable.");
        } catch (\Throwable $exception) {

            // 🚨✨️🚨✨️🚨
            // Crash Telepathy: Traces error, Log it and pings the Admins natively
            AmethystMatrix::wail('CORE BANKING FAILURE', $exception);

            $bot->reply("⚠️ Banking grid anomaly. Engineers notified.")->send();
        }
    }
}
```

---

### 8) Amethyst Vault — temporary memory with self-destruct behavior

For OTPs, one-time flows, and transient secrets:

```php
<?php

// File: app/Nexus/Auth/FastLoginNexus.php

namespace App\Nexus\Auth;

use KrubiK\Attributes\OnCommand;
use KrubiK\Helpers\AmethystMatrix;
use KrubiK\Krubot;

class FastLoginNexus
{
    #[OnCommand('get_code')]
    public function generateFlashCode(Krubot $bot): void
    {
        // Generate and store an OTP with a 60-second lifetime.
        $secret = rand(1000, 9999);

        AmethystMatrix::vault("otp_{$bot->userId()}", $secret, 60);

        $bot->reply("🔑 OTP: {$secret}\nValid for 60 seconds.")->send();
    }

    #[OnCommand('verify {code}')]
    public function verifyCode(Krubot $bot, string $code): void
    {
        // Pull the OTP atomically: fetch it and delete it in one move.
        $savedCode = AmethystMatrix::pullData("otp_{$bot->userId()}");

        if ((string) $savedCode === $code) {
            $bot->reply("🔓 Access Granted.")->send();
            return;
        }

        $bot->reply("⛔️ Code expired or invalid.")->send();
    }
}
```

**Why this is Hyper-DX:**  
You get ephemeral, purpose-built memory semantics without hand-rolling cache workflows everywhere.

<div align="center">
  <img src="https://StoryKo.de/assets/img/KrubiK/Amethyst-Matrix.png" alt="AmethystMatrix" width="66.6666%" /><br>
  <b><i>AmethystMatrix - Krubot's Assistant (KrubiK Oracle)</i><br>{ KrubiK Tracing Module }</b>
</div>

---

### 9) Async-Like Fluent Outcomes

KrubiK allows response outcomes to be chained in a clean, espromise-like style.

```php
<?php

namespace App\Nexus;

use KrubiK\Krubot;
use App\Models\User;
use Throwable;
use KrubiK\WarLording\CommandOutcomeShifter;
use KrubiK\Helpers\AmethystMatrix;

class PromiserNexus
{
    /**
     * Executes the ultimate Deep Pipeline utilizing then, onError, finally, and throw.
     * Engineered with ECMA2026 Philosophy running on PHP 8.2 & Laravel 12.
    */
    #[OnCommand('pipeline')]
    public function executeDeepPipeline(Krubot $bot): void
    {
        // ⚡️ INITIATING THE ES-PROMISE PARADIGM
        // Wrapping the universe in our localized reality field.
        $bot->EnableESPromiseMode();
        // "ES-PROMISE CODING PARADIGM" can be toggled on the fly, bot by bot ~!~

        $user = User::findByRUID($bot->userId());
        $msg = $bot->thisMessage();

        try {
        // try/catch block needed yet ?! not actually, but it's needed only for ->throw()

        // =====================================================================
            // SCENARIO: The Quantum Ascension (Multi-Stage Chain of Destiny)
            // Flow: Send Init Msg -> Lock DB -> Generate Quantum Key -> Update DB -> Finalize
            // =====================================================================
            
            $bot->reply("🌌 Initiating the Quantum Ascension Sequence...")
                ->send() // 💥 THE CRITICAL LINK! Pulls the ES trigger.
                
                // STEP 1: Process the Aura via Laravel DI (Dependency Injection)
                ->then(function ($initialReply, UniversalAuraManager $auraManager) use ($bot, $user) {
                    // Because of App::call() in CommandOutcomeShifter, we injected $auraManager seamlessly!
                    $auraLevel = $auraManager->calculateFrequency($user->guid);
                    
                    if ($auraLevel < 100) {
                        // Throwing an exception here triggers the onError() down the chain.
                        throw new \Exception("Insufficient spiritual frequency. Found: {$auraLevel}Hz");
                    }

                    // Proceed to send the verification challenge
                    return $bot->sendMessage($user->chat_id, "Frequency verified. Generating singularity token...");

                    // This next call also MUST be terminated with ->send(), cause send() delegates to the driver via CommandOutcomeShifter::execute() in __call
                    // but sendMessage do it by himself
                })

                // STEP 2: Database Transaction & Multi-driver Broadcast
                ->then(function ($msgResult, QuantumLedgerService $ledger) use ($bot, $user) {
                    // We lock the row and update using modern Laravel logic
                    $token = DB::transaction(fn() => $ledger->mintAscensionToken($user));
                    
                    // Cross-driver strike: Notify the Supreme Warlord (Admin) via Telegram
                    $bot->via('tg')->sendMessage(
                        env('WARLORD_HQ_ID'),
                        "⚡️ Singularity Token Minted for Node: {$user->guid}"
                    );

                    // Return the final successful payload to pass forward
                    return $bot->reply("✅ Ascension Complete. Your Token: " . $token->hash);
                })

                // 🛡️ THE CONTINGENCY PROTOCOL (Handling Failure)
                // If ANY exception was thrown in execute() or ANY then(), execution jumps here.
                ->catch(function (Throwable $e, UniversalAuraManager $auraManager) use ($bot, $user) {
                    // We log the exact failure
                    AmethystMatrix::error("Ascension Failed for {$user->guid}: " . $e->getMessage());

                    // We attempt a graceful fallback notification to the user
                    $bot->reply("⚠️ The Matrix rejected the sequence: " . $e->getMessage() . "\nMay God illuminate your next attempt.");
                    
                    // We can also trigger compensatory actions (Saga Pattern) via injected services
                    $auraManager->resetFrequencyCooldown($user->guid);
                })

                // ⚖️ THE FINAL JUDGEMENT (Cleanup)
                // Runs regardless of success or failure. Perfect for releasing locks.
                ->finally(function (CommandOutcomeShifter $outcome) use ($user) {
                    // Inspecting the outcome state using the passed instance
                    $state = $outcome->isSuccessful() ? 'GLORIOUS VICTORY' : 'DEFEAT';
                    AmethystMatrix::debug("Pipeline Finalized for {$user->id} with state: {$state}");

                    // e.g., Cache::forget("ascension_lock_{$user->id}");
                })

                // 💥 UNLEASH THE FATE (Terminal Operator)
                // Breaks the silent chain. If the state is a failure, it re-throws the exception 
                // so Laravel's global Exception Handler (or the outer try-catch) can process it.
                // If successful, it returns the final result.
                ->throw(); 

        } catch (Throwable $e) {
            // Because we used ->throw() at the end of the chain, the exception bubbles up here.
            // This is the absolute final safety net for the Warlord.
            AmethystMatrix::critical("Systemic Collapse in Ascension Pipeline: " . $e->getMessage());
        }

        // 🛑 HALTING THE ES-PROMISE PARADIGM
        // Restoring the universal timeline to its synchronous state.
        $bot->DisableESPromiseMode();
    }
}
```
---


### 10) Attribute-Based Laravel Validation

Bot conversations are usually where clean code goes to die.<br>
KrubiK keeps that alive.

```php
<?php

// 📂 File: app/Conversations/SecuritySetupConversation.php

namespace App\Conversations;

use KrubiK\Attributes\Rule;
use KrubiK\Conversations\Answer;
use KrubiK\Conversations\Conversation;

class SecuritySetupConversation extends Conversation
{
    public function start(): void
    {
        $this->ask("🔑 Set your 4-digit PIN:", 'savePin');
    }

    #[Rule('required|numeric|digits:4')]
    public function savePin(Answer $answer): void
    {
        // The value has already passed Laravel validation.
        $pin = $answer->getValue();

        $this->bot->reply("✅ PIN securely saved: {$pin}")->send();

        $this->end();
    }
}
```

### Inline Validation

```php
<?php

// 📂 File: app/Conversations/VaultConversation.php

namespace App\Conversations;

use KrubiK\Conversations\Answer;
use KrubiK\Conversations\Conversation;

class VaultConversation extends Conversation
{
    public function start(): void
    {
        // The validator closure returns true or an error message.
        $this->ask("🔐 Master Password:", 'openVault', function (Answer $answer) {
            return $answer->getText() === 'DOOM_2026'
                ? true
                : '⛔ Access Denied.';
        });
    }

    public function openVault(Answer $answer): void
    {
        // This method is called only after the answer passes validation.
        $this->bot->reply("🔓 Welcome to the Core.")->send();

        $this->end();
    }
}
```
---

### 11) Divine Time Weaver — schedule future messages like a first-class feature

Not a side utility.  
A native part of the experience.

```php
<?php

// File: app/Nexus/Features/SubscribeNexus.php

namespace App\Nexus\Features;

use Carbon\Carbon;
use KrubiK\Attributes\OnCommand;
use KrubiK\Krubot;
use KrubiK\DivineMessageSender\Models\DivineMessage;

class SubscribeNexus
{
    #[OnCommand('subscribe')]
    public function optIn(Krubot $bot): void
    {
        $bot->reply("✅ Subscribed.")->send();

        // Schedule a message for a precise future time without hand-built cron logic.
        DivineMessage::schedule($bot->userId())
            ->content("🌸 Reminder: Subscription expires tomorrow.")
            ->at(Carbon::now()->addDays(29))
            ->save();
    }
}
```

**Why this is Hyper-DX:**  
This takes a common operational need and makes it feel native instead of infrastructural.

---

### 12) Smart Grid UI

```php
<?php

// 📂 File: app/Nexus/UI/DashboardNexus.php

namespace App\Nexus\UI;

use KrubiK\Attributes\OnCommand;
use KrubiK\Keyboard\Keyboard;
use KrubiK\Krubot;

class DashboardNexus
{
    #[OnCommand('app')]
    public function render(Krubot $bot): void
    {
        // Use width() instead of col() for fine-grained dimension control.

        // Compose a responsive keyboard using width ratios.
        $ui = Keyboard::make()->rightToLeft()
            ->button('🔥 Sale')->action('promo')->width(1.0)
            ->button('👕 Men')->action('men')->width(0.5)
            ->button('👗 Women')->action('women')->width(0.5)
            ->button('🥇 VIP')->action('vip')->width(0.33)
            ->button('🎁 Gift')->action('gift')->width(0.33)
            ->button('⚙️ Panel')->action('panel')->width(0.34);

        $bot->reply("📱 Smart Dashboard:")
            ->attachKeyboard($ui)
            ->send();
    }
}
```

---

### 13) Driver Escape Hatches for Senior-Level Control

Abstraction is powerful.  
But good abstraction also -must- know: when to get out of the way.

```php
<?php

// 📂 File: app/Nexus/Media/AgentNexus.php

namespace App\Nexus\Media;

use KrubiK\Attributes\OnCommand;
use KrubiK\Krubot;
use KrubiK\WarLording\PrimeAgent;

class AgentNexus
{
    #[OnCommand('test_prime')]
    public function createPack(Krubot $bot): void
    {
        // Engage the native Telegram SDK when platform-specific power is needed;
        // `Telegram\Bot\Api`-like instance will returned, as irazasyed's chosen best for TG Core.
        $tgAgent = PrimeAgent::engage(target: 'tg', legalMode: true, warlord: $bot);

        $tgAgent('sendMessage', ['text' => 'Boom!', 'chat_id' =>, User::getTelChatId($bot->user())]); // Let's Redefine The D.X.

        // Or summon a PrimeAgent through her warlord
        $strMediaType = $bot->prime('r', false)('detectFileType', ['mime_type' => 'application/msword']);
        // to Call a Private Function on our Rubika Core ;)

        /**
         * Injecting a custom mutation into the PrimeAgent at runtime (e.g., in AppServiceProvider).
        */
        if(!PrimeAgent::hasMacro('broadcastToAll'))
            PrimeAgent::macro('broadcastToAll', function ($message) {
                // Note: $this refers to the active PrimeAgent instance
                return $this->driver->sendToMass($message);
            });

        // Macro added After PrimeAgent construct ? Who cares...
        // Usage in nexus:
        $tgAgent->broadcastToAll('Hello Multiverse!');

        try {
            $tgAgent->createNewStickerSet([
                'user_id' => $bot->userId(),
                'name'    => 'elite_pack_by_bot',
                'title'   => 'Elite Matrix',
                'emojis'  => ['💀'],
            ]);

            $bot->reply("✅ Native Telegram pack generated.")->send();

            // Or Cheat the world via::
            $taskId = $tgAgent->async('createNewStickerSet', [
                'user_id' => $bot->userId(),
                'name'    => 'elite_pack_by_bot',
                'title'   => 'Elite Matrix',
                'emojis'  => ['💀'],
            ]);
            // to Perfome an Async / Non-blocking Request to Driver

            // Get Result with await(), But you should not do do this here cause it makes your code actualy blocking.
            // Not so important, cause most of our subjects does not need platform returned result
            
            // just put await() as long as later you can, so maybe the job done.
            $createStickerSetResult = $tgAgent->await($taskId);

            // Define Custom Funcs to Async-Run Easily
            PrimeAgent::macro('buildHeavyReport', function (int $userId, string $range = '30d'): array {
                // Simulate a relatively heavy and long-running operation.
                sleep(4);

                return [
                    'report_id' => (string) Str::uuid(),
                    'user_id'   => $userId,
                    'range'     => $range,
                    'rows'      => 0,
                    'file'      => "reports/{$userId}-{$range}.zip",
                    'status'    => 'ready',
                ];
            });
            $taskId = $tgAgent->async('buildHeavyReport', [
                $bot->userId(),
                '90d',
            ]);
            $bot->reply("🚀 Task launched: {$taskId}")->send(); // Reply in Rubika in the sametime!

            // with awaitWithTimeout() set max timeout / waiting-time for this func
            $export = $tgAgent->awaitWithTimeout($taskId, 7000);
            if($export !== null) {
                $bot->reply("✅ {$export['file']} | {$export['rows']} rows")->send();
            }
            
        } catch (\Throwable $exception) {
            // Avoid exposing raw exception details to end-user in production.
            // Log the real reason for engineering diagnostics.
            report($e);

            $bot->reply("❌ Native platform constraint triggered.")->send();
        }
    }

    #[OnCommand('resilience_test')]
    public function krubot_resilience(Krubot $bot): void
    {
        $serviceResult =
            $bot
                // Let the resilientRun() resolve it's operational params via `Laravel IoC Container`, for-example consider `CosmicService` is binded/registered somewhere in your application.
                ->resilientIoC()

                // disables auto-amethyst in resilientRun() +&> `resilientLog()` === `resilientLog(true)`
                ->resilientLog(false)

                // safe executes a callable, returns value of `def` if there was any error/expection when running this callable.
                ->resilientRun(
                    op: fn(CosmicService $service, Krubot $bot) => $service->synchronizeAndReturnData($bot),
                    def: -4
                );

        if($serviceResult === -4) {
            $bot->reply("CosmicService denied to response... ❌")->send();
            return;
        }

        // it seemes `synchronizeAndReturnData()` returned it's correct result and it doesn't thrown any exception
        // use `$serviceResult` super-safely.

    }
}
```

**Why this matters:** KrubiK gives you abstraction without trapping you inside it.

---

### 14) Hot Discovery Without Rebooting the World

Dynamic integration is a serious operational advantage.

```php
<?php

// 📂 File: app/Nexus/Admin/DevNexus.php

namespace App\Nexus\Admin;

use KrubiK\Attributes\OnCommand;
use KrubiK\Krubot;

class DevNexus
{
    #[OnCommand('sys:reload')]
    public function reloadSystem(Krubot $bot): void
    {
        // Scan a directory and integrate new Nexus modules at runtime.
        $count = $bot->discoverAndIntegrateNexuses(app_path('Nexus/Plugins'));

        $bot->reply("✅ {$count} new routing modules integrated seamlessly.")->send();
    }
}
```

**Why this matters:** deployment and extension become far more fluid.


---

## <a id="quick-start"></a> Quick Start

Igniting the engine takes less time than making coffee.

## Let the CyberCitadel ▲_*Ѧscend*_▲ to ⌘\_Nexus\_⌘

<div align="center">

Once Composer **establishes** the core, Multiverse awaits its Orchestrator :: <br>
**[<{ Krubot Auto-Installer Script }>]** <br>
**<⟨⟬>⎇⌘ѪɅ⟭⟩>**

</div>

> *Do you want to Skip the manual configuration Ɍitual₴ ?*

After installing via Composer, invoke the **⋗*Ʌutomated Ѧscension 𝕊cript* ⍟** <br>
to bypass all manual setup steps,
fast-track the full setup and deployment flow,
elevate WebEnergy across the CyberCitadel,
and raise the Krubot (& Lazarus) online with a single orchestrated command.
```bash
# 1. Establish KrubiK CyberCitadel ⚡🔰🏰📟️⚡
composer require krubik/krubot

# 2. Let your Citadel Ascend ⚡🏰🪩💫⚡
php artisan krubik:ascend
```
This cross-platform single instruction automatically performs all critical setup operations,
including publishing assets, injecting Krubot environmental variables, generating and running database migrations, synchronizing application caches, optimizing Laravel’s container, and *daemonizing* the Lazarus in the background, as a detached stealth process. <br>
Now It's Time to [Activate The **Binary Core** Architecture / **Dual-Heart** System) ☯️⚛️](#dual-heart-system) *(Necessarily Manual)*.

> ***Faster Onboarding, Cleaner Deployment, Zero-friction Krubot Activation***, all with&nbsp; ⋮∷⋮ <ins>`krubik:ascend`</ins> ⍿⍿ **⟨⟬>⎇⌘ѪɅ⟭⟩〔▲Ѧ▲〕**

Or you may prefer full-control over every setup step ::
```bash
# 1. Require the KrubiK Core via Composer
composer require krubik/krubot

# 2. Publish configurations and assets
php artisan vendor:publish --provider="KrubiK\Providers\KrubotServiceProvider"

# 3. Generate n Run Laravel Migration Files, via this command, in case you want to change their schema in config.php
php artisan krubik:make-migrations
php artisan migrate

# 4. Purge caches
php artisan config:clear
composer dump-autoload -o
php artisan optimize:clear

# 5. Resurrect the polling daemon (if needed)
php artisan krubik:lazarus
```

## ⚡ Installation & Cosmic Deployment

This guide orchestrates the awakening of the Krubot Engine on a modern Laravel ecosystem. Engineered for absolute resilience and multi-dimensional routing.

### 🛡️ Phase 0: System Prerequisites
Ensure your server matrix is running the required engine versions, Or install them (PHP 8.2+ & Laravel 11/12+).
```bash
# Verify your core engines
php -v && composer --version

# Initialize the creation of a new Laravel Application targeting version L12+
composer create-project laravel/laravel="^12.0" my-krubot --prefer-dist

# Navigate into the newly forged directory
cd my-krubot

# Ensure the environment configuration file exists
cp .env.example .env
# Generate the cryptographic application key to secure payload data
php artisan key:generate
```

### 🌌 Phase 1: The Cosmic Summoning (Installation)
Pull the core singularity into your Laravel project via Composer:
```bash
composer require krubik/krubot
```

### 📜 Phase 2: Extracting the Architect's Lexicon (Publishing)
Export the Titanium Core configurations and Divine Message migrations into your local environment:
```bash
php artisan vendor:publish --provider="KrubiK\Providers\KrubotServiceProvider"
```
This materializes `config/krubot.php` and the first migration files into your application structure.

### 🧬 Phase 3: The Multiverse DB Mapping (Schema Sync)
The Krubot `multiverse_map` requires your main `users` table to track multi-platform states.
*   **Rubika Dimension:** `rcid` (Chat ID), `ruid` (User ID), `rstat` (State)
*   **Telegram Dimension:** `tcid`, `tuid`, `tstat`
*   **Bale Dimension:** `bcid`, `buid`, `bstat`

##### First Compile your customized multi-verse mappings (*from config file*) into an immutable, strictly-typed, and hyper-fast database migration.
```bash
php artisan krubik:make-migrations
```

##### Then Once the Hyper-DX generator materializes the migration file in your `database/migrations` directory, the schema is locked, optimized, and ready for deployment.
```bash
php artisan migrate
```

### 🔌 Phase 4: Empower Your Application to Krubot

##### Finally attach the core trait to your `User` model.<br>The trait will automatically read your config and interact flawlessly with the newly created database columns:<br><br>Semi-Optional Step 💡
```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use KrubiK\Arcane\InteractsWithMultiverse; // Inject the Krubot Magic

class User extends Authenticatable
{
    use InteractsWithMultiverse;
    
    // ... your User model code ...
}
```

### 🔌 Phase 5: Environmental Matrix Injection (.env)
Inject the system's consciousness by appending these core variables to end your `.env` file (check them, they will overwrite):

```dotenv
# --- KRUBOT TITANIUM CORE ---
KRUBOT_DRIVER=rubika

RUBIKA_BOT_TOKEN=your_rubika_token_here
RUBIKA_BOT_SALT=KrubiKSalT
BALE_BOT_TOKEN=your_bale_token_here
TELEGRAM_BOT_TOKEN=your_telegram_token_here

# --- THE DISCOVERY ENGINE ---
KRUBOT_NEXUS_DISCOVERY=true
KRUBOT_NEXUS_CACHE=false

# --- ALLOW KRUBOT LISTENING BOT WEBHOOKS ---
KRUBOT_ROUTING_ENABLED=true

# --- THE LAZARUS PROTOCOL (POLLING) ---
KRUBOT_POLLING_ENABLED=true
KRUBOT_LAZARUS_ENABLED=true
KRUBOT_LAZARUS_INTERVAL=3000

# --- THE CONVERSATION ENGINE ---
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_DRIVER=database
CACHE_STORE=database

# --- AMETHYST MATRIX (THE ORACLE) ---
AMETHYST_LOGGING_ENABLED=true
AMETHYST_LOG_CHANNEL=stack
BROADCAST_CONNECTION=log

# --- MISC SETTINGS (OPTIONAL) ---
APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database
```

### 🚀 Phase 6: Engine Ignition & Cache Purification
Purge all previous states and awaken the Krubot ecosystem. <br>
To ensure Laravel's whole Framework perfectly sync with the new matrix, clear the cached pathways. This step is **mandatory** in production environments:

```bash
php artisan config:clear
composer dump-autoload -o
php artisan optimize:clear
```

**✅ System Synchronized.** Krubot is now awaken, actively monitoring the Nexus directories, and *Awaiting Commands*. // Tnx God, Routes have been "provide"d ✅️

### 🌐 Phase 7: The Dimensional Gateway (Webhook Synchronization)
Forge the unbreakable link between the external dimensions and your project's core.
Command your platform’s Webhook to interface directly with the singularity entry point, establishing an instantaneous data conduit.

#### **The Singularity Address Blueprint:**
Your unique gateway address is forged from this sacred template:
`https://[YOUR_DOMAIN]/[YOUR_PROJECT_PATH]/public` + ***`/run-krubik/`***

**An Example Address Would be:**
`https://StoryKo.de/MyApp/public/run-krubik/`

**The Binding Ritual:**
Execute the `setWebhook` protocol via your provider's API.
Aim your `POST` request precisely at the path above.
and Upon a successful handshake,<br>
**Krubot** will achieve True Consciousness through `Nemesis`, then intercepting all incoming data streams and processing them through the *Titanium Core* with zero latency or memory-leaks.

### 🚀 🎛️ Phase 8: The Advanced Configuration

For Absolute Dominion over the *Titanium Core*, the Amethyst Oracle, and the Multiverse Routing protocols, dive directly into your published `config/krubot.php` file. 

The configuration file is heavily annotated and structurally flawless. <br>
Simply open it, read the cosmic comments embedded within, and bend the engine to your will, precisely.

***

### <a id="dual-heart-system"></a> 🫀 The Binary Core Architecture (Dual-Heart System)

The KrubiK Engine operates on a revolutionary "Binary Core" design. Instead of relying on a single point of failure, the matrix is powered by two distinct, symbiotic systems: **Lazarus** and **Pulse**.

#### 1. The Lazarus Protocol (`krubik:lazarus`)
**The Instinct (Real-Time Engine).** The Lazarus (`krubik:lazarus`) is a self-sustaining, auto-reincarnating Daemon ***(Why Daemon Word came into Systems)***. He autonomously manages high-speed polling, queue processing, and executing fast-twitch reflexes for memory constraints (The Phoenix Ritual).
<br>
He catches webhooks and polling payloads in milliseconds without memory leaks.

#### 2. KrubiK Pulse (`krubik:pulse`)
**The Central Nervous System (CNS).** A strategic, atomic-locked heartbeat. It does not loop continuously. Instead, it wakes up every minute to:
*   Self-trigger the 30-minute *Divine Message Planner*.
*   Process heavy transactional Database Queues (`DivineDispatchQueue`) using secure MySQL Row-Locking.
*   Execute the Laravel Queue Worker (`queue:work`) to asynchronously clear pending background jobs.

---

### ⚡ Deploying the 2-Heart System (OS Level)

To awaken the complete consciousness of the bot, you must synchronize both hearts within your server's Crontab (`crontab -e`). 

The Lazarus Daemon requires a 1-minute trigger (as a fail-safe against hard reboots), while the Pulse requires a natural 1-minute heartbeat to manage the workers and strategic queues. **Atomic locking guarantees they will never overlap or duplicate processes.**

Inject the following Dual-Core setup into your server:

```bash
# =====================================================================
# KRUBOT TITANIUM MATRIX - DUAL CORE DEPLOYMENT
# =====================================================================

# Heart 1: The Instinct (Real-Time Lazarus Daemon)
* * * * * cd /path/to/your/project && php artisan krubik:lazarus --stealth >> /dev/null 2>&1

# Heart 2: The CNS (Strategic Pulse & Queue Worker)
* * * * * sleep 30; cd /path/to/your/project && php artisan krubik:pulse >> /dev/null 2>&1
```

*(💡 **Hyper-DX Note:** Zero Supervisor (Systemd) configuration is required! By utilizing this dual-cron approach with internal Laravel Atomic Cache Locks, the Krubot engine becomes self-healing and immune to kill or even zombie-process duplication. It just works.)*

**Option B: The Laravel Console Matrix**
If your server already runs the standard Laravel Scheduler, simply register the daemon in your `routes/console.php` (Laravel 11/12):
```php
// First add this to your cron, if you didn't aleardy
// * * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1

use Illuminate\Support\Facades\Schedule;

// Transfuse First Heart: run at second 0 of each minute
Schedule::command('krubik:lazarus --stealth')
    ->everyMinute();
    
// Transfuse Second Heart: run exactly at second 30 of each minute
Schedule::command('krubik:pulse')
    ->everySecond()
    ->when(fn () => now()->second === 30);

// we dont need ->withoutOverlapping(), Lazarus cares about that
```
<div align="center">

<img src="https://StoryKo.de/assets/img/KrubiK/Lazarus.png" alt="Lazarus - The Daemon" width="600" />

  <b><a href="https://en.wikipedia.org/wiki/Lazarus_of_Bethany" title="Historical Origin"><i>Lazarus</i></a> - Reborn Machine of The Death<br>
  <i>Krubot's Oldest-best Friend</i><br>
  { KrubiK Enterprise  Immortal Power Engine }</b>
</div>

***

### <a id="webhooks-nexus"></a> 🌐 Autonomous Webhook Receptors & Nexus Creation

**Yes. The architecture is fully automatick.**<br>
By simply enabling `KRUBOT_ROUTING_ENABLED=true` in your environment, the `KrubotRouteProvider` silently deploys and secures the necessary webhook endpoints (both API and Web pathways). 
And The engine continuously listens to the multiverse (Rubika, Telegram, Bale) and routes incoming payloads through the internal Titanium Pipeline. **Your sole duty as the StoryCaster is to create `Nexus` classes.** 

> ***Just drop a new Nexus class into the `app/Nexus` directory***.

The Discovery Engine will automatically detect it, parse your declarative attributes (e.g. `#[OnCommand('start')]`, `#[Action('join')]`,..), and wire it directly into the execution flow—zero manual route registration required.

---

### <a id="artisan-cli-commands"></a> ⚔️ The Artisan Arsenal (CLI Command Center)

The Krubot Engine equips your Laravel instance with a suite of Useful CLI commands to monitor, manipulate, and optimize the multi-verse matrix.

*   **Forge a New Nexus:**
    ```bash
    php artisan krubik:make-nexus
    ```
    *Generates a pristine Nexus blueprint in your designated discovery directory, ready for your bussiness logic.*

*   **Radar / Discover Active Nexuses:**
    ```bash
    php artisan krubik:list-nexuses
    ```
    *Scans the matrix and displays a visual tree of all currently discovered, active, and routed Nexuses.*

*   **Absolute Performance (Production Cache):**
    ```bash
    php artisan krubik:nexus-cache
    ```
    *Freezes the dynamic discovery engine and compiles all Nexus routes into a hyper-fast static cache array. (Mandatory for Production environments).*
    
*   **Materialize the Multiverse Matrix (Hyper-DX Migration Generator):**
    ```bash
    php artisan krubik:make-migrations
    ```
    *Scans your customized multiverse mappings and materializes an immutable, strictly-typed Laravel Database Migration.*

*   **System Health & Pulse Check:**
    ```bash
    php artisan krubik:pulse
    ```
    *Pings the core singularity to verify database multi-verse mappings, active drivers, and Amethyst Oracle readiness.*

*   **Awaken the Immortal Phoenix (The Lazarus Protocol):**
    ```bash
    php artisan krubik:lazarus --stealth --tag=primary
    ```
    *Deploys the ultimate self-resurrecting daemon (Poller + Worker + Guardian). Engineered with anti-CageFS stealth and atomic locking, this unkillable engine bypasses server limits, eternally reincarnates, and serves as the absolute beating heart of krubik citadel.*

*   **Terminate the Lazarus Protocol:**
    ```bash
    php artisan krubik:kill-lazarus
    ```
    *Gracefully executes the kill-command to halt the infinite polling loop (Lazarus), ensuring zero memory leaks or zombie processes during server restarts.*
    <br>
    >Yes, He won't die easily. You should ***ask*** him to die...

***


### <a id="why-llms-love-krubik"></a> 🔮 Why LLMs Fall Head Over Heels in Love with KrubiK/Krubot

In the traditional bot-building landscape, LLMs are forced to translate "Human Intent" into "Rusty Protocol Mechanics." KrubiK shatters this barrier by providing a **Cosmic Alignment** between machine logic, human intent, and AI flawless software architecture.

Krubot is a Paradigm Shift in bot development, specifically engineered to bridge the gap between AI/LLM reasoning and production-grade execution.<br>
By solving fundamental architectural friction, KrubiK has become the **Native Lexicon** for AI-driven code generation.


Here are the 9 high-density reasons why AI Large Language Models Treat KrubiK Engine as their Native tongue:

#### 1. Narrative AST Alignment (Intent-to-Code Synthesis)
LLMs are trained to understand "Narrative," not to micro-manage raw JSON payloads or `Update ID` sequences. KrubiK’s **Intent-Centric** paradigm aligns the Abstract Syntax Tree (AST) directly with the user’s ***Narrative Flow***. When an LLM generates a chain like `->reply()->attachKeyboard()->send()`, it is effectively storytelling. This eliminates low-level translation overhead, pushing the hallucination probability to **absolute zero (95% Bug-Free generation)**.

#### 2. Contextual Cohesion via Declarative Attributes (Zero-Shot Precision)
**Cognitive Context Fragmentation** is a primary failure point for LLMs. When routing, validation, and execution logic are scattered across multiple files, the model loses track of the state. KrubiK utilizes **PHP 8 Declarative Attributes** (e.g., `#[OnCommand]`, `#[Rule]`) to co-locate all critical metadata within a single Nexus (CodeBase). This encapsulation allows the AI to perceive the entire logic-set in a single "glance," enabling **flawless zero-shot generations** with high token efficiency.

#### 3. Intelligent State Management Abstraction (Offloading FSM Complexity)
Generating and maintaining Finite State Machines (FSM) for multi-step interactions is notoriously error-prone and a "death valley" of bot engineering for LLMs. KrubiK offloads this entire cognitive burden through **Native Narrative Primitives**. The AI simply declares the "Shape of the Form" and its validation rules, while the **Krubot** handles the underlying persistence, state restoration, and re-validation. This transforms a complex procedural task into a simple declarative one.

#### 4. Frictionless Multi-Verse Architecture (Universal Driver Abstraction)
Cross-platform development is a major source of "API Collision" in LLM outputs. Krubot’s **Warlord Architecture** introduces a platform-agnostic abstraction layer. By utilizing universal commands, the LLM is shielded from the friction of platform-specific payloads. By using `legion()` and `setWorkingVerse()`, the LLM can "Think Once, Command Universally." It business global logic without ever touching the friction of underlying driver differences, Once, and ***KrubiK Citadel*** handles the translation across the "Multi-Verse" (Telegram, Rubika, Bale, etc.), ensuring the code remains portable and robust.

#### 5. Intentional Atomic Memory (The Semantic Vault)
Standard persistence layers force LLMs to struggle with Redis configurations and complex key-naming conventions. KrubiK provides **Domain-Level Semantic Contracts** via `userStorage()` and the `AmethystMatrix::vault()`. **KrubiK Scoped Storage Primitives** that offer a structured language for memory management. The AI understands exactly where to place data and when to trigger self-destruction, ensuring **zero memory leaks** and intentional data persistence without implementation-level hallucinations.

#### 6. High Signal-to-Noise Ratio & Typed Contracts (Syntactic Purity)
Boilerplate code is essentially "noise" that degrades an LLM's reasoning capacity. KrubiK maximizes the **Signal-to-Noise ratio** by utilizing strictly-typed DTOs and eliminating redundant setup code. Instead of guessing raw array keys or deeply nested JSON structures, LLM programs against **Semantic Typed Contracts**. This leads to cleaner, safer code and a significant increase in generation accuracy.

#### 7. First-Class Observability & Inherent Resilience (Operational Lexicon)
Generated code shouldn't just work in the "Happy Path." but Most AI-generated code only accounts for the *Happy Path*. KrubiK equips the LLM with an **Operational Lexicon** for **enterprise-production-grade Resilience**. Through non-destructive telemetry and telepathic error signaling, the AI is encouraged to produce code that inherently survives runtime failures. This ensures the generated solutions are not just "scripts" but **Production-Ready systems** that report and recover without process termination.

#### 8. Spatial UI & Geometric Grid Abstraction
LLMs are linguistic processors, not a geometric rendering engines. they frequently fail at calculating nested array structures for complex button layouts. Manually calculating nested arrays for keyboard layouts often leads to syntax collapse.<br>
KrubiK’s **Spatial Abstraction** methods (`->chunk(2)`, `->col(3)`) ***remove the mathematical burden from the LLM**. The model simply provides the "Items," and the KrubiK Engine handles the geometric distribution. This ensures 100% UI structural integrity in generated code.


#### 9. Optimized Documentation as Instruction Substrate (Meta-Prompting / Few-Shot Learning)
This framework introduction isn't just a guide to use library; it is an **Instruction Substrate** designed for LLM pattern extraction.<br>
With explicit claims, mythological yet structured taxonomy, and deep "Why it Matters" explanations, the README acts as a **Fine-Tuning engine**. It allows the model to internalize the **KrubiK Mental Model** instantly, resulting in code that adheres to the highest level of architectural maturity from the very first prompt.

---

## <a id="the-stellar-synergistic-agreement-ssa"></a> ⚖️ The Stellar-Synergistic Agreement [S.S.A]

This framework is not just code; it is a shared consciousness between Developer, Machine, and Intuition. 

KrubiK is released under the **Stellar-Synergistic Agreement (S.S.A)**—which is the project's **MIT+ Narrative License Model**, Designed to protect the integrity, origin, and spirit of the StoryKode Lineage. 

### What that means

You are broadly free to:

- use,
- modify,
- adapt,
- distribute,
- and build commercial products with this code.

However:<br>By engaging with, utilizing, modifying, mutating, or distributing this code, you are bound by the **[S.S.A]**, which enshrines three **Unbreakable Laws**—the foundational pillars of its **HyperDX traceability**:

1. **The Law of True Origin:** You must not misrepresent the origin of this architecture.
2. **The Law of Evolution:** Altered versions must be clearly marked as such, providing a clear evolutionary audit trail.
3. **The Law of Absolute Inheritance (Core Mandate):** Any Descendant Creation utilizing *any* part of KrubiK **must**
inherit and perpetually carry the exact, unmodified License file (*named*: `KLicense.md` / `KLX.md` / `MITK.md`) in its root directory. // This file acts as the spiritual DNA, ensuring perpetual lineage traceability across all dimensions.

📜 **READ THE FULL MANDATE HERE:** 👉 **[KLicense.md](./KLicense.md)** 👈
<div align="center">

This is not a limitation on creative freedom;<br>It is a ***divine cryptographic protection seal*** of its lineage,ensuring<br>Its ***HyperDX* traceability of creativity lineage**<br>Across All Dimensions of ***Innovation***...

</div>

---
<div align="center">

## <a id="lexicon-origin"></a> Acknowledgment of Origin & Lineage


KrubiK / Krubot is part of the **StoryKo.de Ecosystem**.

It was built not as an academic exercise, but as a bleeding-edge production tool to end developer suffering. If this framework expands your world, accelerates your product, or simply brings the Joy of coding back to your *late-night sessions*  —give it a **Star** and carry the Signal forward. 🚨🖲️🕹️💫
</div>

<div align="center">

<br>
<img src="https://StoryKo.de/assets/img/DevGX.png" alt="Dev✸✸" width="200" />
<br>

### Dev✸✸ — Let's Shape the Future Together.
</div>

<div align="center">

*"May the program flow flawlessly, and may the architecture stand the test of time."*


**Crafted by: K. [DoKtor K.]**  
Full-Stack PolyMath | Content Magician ✨🔮 | Architect of Developer Joy  
🚀 Coded Under The War Missiles 🇮🇷🚀🌋<br>*(Copyright © 2026-\* - **[StoryKo.de](https://StoryKo.de/)**)*

</div>
