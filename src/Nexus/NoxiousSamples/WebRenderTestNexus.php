<?php

namespace App\Nexus;
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

use KrubiK\Krubot;
use KrubiK\WebApps\Attributes\WebPage;
use KrubiK\Keyboard\PowerButton;
use Article;

// in your products, you can import any helper func, that you really need
use function KrubiK\Render\Helpers\{
    anchor,
    anchorLink,
    animation,
    audio,
    blockQuotation,
    bold,
    botCommand,
    caption,
    cashtag,
    cell,
    code,
    collage,
    customEmoji,
    dateTime,
    details,
    divider,
    newLine,   // not telegram original
    space,     // not telegram original
    emailAddress,
    footnoteDefinition,
    footer,
    hashtag,
    heading,
    italic,
    listBlock,
    listItem,
    map,
    marked,
    mathematicalExpression,
    mention,
    paragraph,
    phoneNumber,
    photo,
    plain,
    pre,
    pullQuotation,
    reference,
    referenceLink,
    slideshow,
    spoiler,
    strikethrough,
    subscript,
    superscript,
    table,
    tableCell,
    textMention,
    thinking,
    underline,
    href, // Renamed to prevent conflicts with Laravel url().
    video,
    voiceNote,
    bankCard,

    // TG-API 10.3 Support
    button,
    buttons,
    buttonRow,
    expandableBlockQuotation
};

// ✅ فقط ادمین می‌تواند این دستورات را بزند
class WebRenderTestNexus
{
    #[WebPage('rich-test', methods: ['GET'])] // domain.com/webapps/rich-test
    public function showRichPage(Krubot $bot, int $productId, int $quantity = 1, string $optionalDumbMsg = 'H! There')
    {
        $totalCost = 1000 * $quantity;

        $orderId = 'ORD-' . strtoupper(uniqid());
        $myArticle = Article::headline($optionalDumbMsg, 2)
            ->bold(italic("✅ Order Confirmed: {$orderId}"))
            ->line("Greetings! Your divine order from the web store has been processed:")
            ->expandableBlockQuotation(
                [plain('Revealed line one'), newLine(), plain('Revealed line two'), paragraph([
                    bold(italic('bold italic')),
                    plain(' normal '),
                    underline(strikethrough('underline strike')),
                    plain(' '),
                    spoiler('hidden'),
                    plain(' '),
                    code('inline_code')
                ])],
                plain('Tap to expand')
            )
            ->buttons(fn(\KrubiK\Render\RenderAura $context) => [ // every callable supports Laravel IoC
                PowerButton::make('✅ X-Confirm ')->action('confirm')->col(1)->style('success')->css(['color' => 'blue']), // or `css('color:blue')`
                PowerButton::make('✅ 2-Confirm ')->action('confirm')->col(2)->style('primary')->addClass('richy-italic'), // Max: `col(6)` == 100% width
                PowerButton::make('✅ Z-Confirm '.$context->lang)->action('confirm')->width(.2), // Set Precisionly, Max: `width(1.0)` == 100% width
            ])
            ->listBlock([
                listItem("A", blocks: [space(2), bold("1"), space(3), italic("2"), space(6), code("3")]),
                listItem("B", [space(3), bold(italic('9')), plain('-'), strikethrough('6'), plain('-'), bold(underline('3'))]),
                listItem(label: "C"),
            ])
            ->pre('SELECT * FROM assets WHERE threat_level > 9;', 'sql')
            ->space(5)->bankCard('12345 5656 7878787', '12345 5656 7878787')
            ->space(5)->botCommand('Say Salam', 'start')
            ->space(5)->hashtag('#Laravel')
            ->space(5)->cashtag('$KRUB')
            ->line(code("Item: " . '$product->name'))
            ->line("Quantity: " . $quantity)
            ->line("Total Cost: " . number_format($totalCost) . " Gold Coins")
            ->space(5)
            ->strikethrough('New TxT')
            ->spoiler("Thank you for your patronage. May your path be everlit.")
            ->paragraph([
                bold(italic('bold italic')),
                plain(' normal '),
                underline(strikethrough('underline strike')),
                plain(' '),
                spoiler('hidden'),
                plain(' '),
                code('inline_code')
            ])
            ->blockQuotation([
                paragraph(bold('Bold inside quote')),
                paragraph('Second line'),
            ], 'Author Name')
            ->pullQuotation(thinking(
                bold(fn($p) => $p->strikethrough("Hello")->space(25)->add("K"))
            ), spoiler("Kiyan"))
            ->table([
                [tableCell(italic('XAsset'), isHeader: true),   tableCell('Status', isHeader: true)],
                [tableCell('Alpha'),                   tableCell('✅ Clear')],
                [tableCell('Bravo'),                   tableCell('⚠️ Standby')],
            ], isBordered: true, isStriped: true);

        return $myArticle; // you should return Article [RichMan instance] directly, its Responsable
    }
}
