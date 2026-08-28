<?php
// FILE: src/Render/RenderHelpers.php

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

namespace KrubiK\Render\Helpers;
/**
 * This file contains a set of helper functions designed to provide a fluent and expressive
 * Domain-Specific Language (DSL) for creating RichText element instances.
 * Each function is a direct, camelCased proxy to the ::make() static constructor
 * of its corresponding RichEntity class, offering a more concise and readable syntax.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/

use KrubiK\Render\DTOs\User as UserDTO;
use KrubiK\Render\DTOs\Animation as AnimationDTO;
use KrubiK\Render\DTOs\Audio as AudioDTO;
use KrubiK\Render\DTOs\Location as LocationDTO;
use KrubiK\Render\DTOs\PhotoSize as PhotoSizeDTO;
use KrubiK\Render\DTOs\Video as VideoDTO;
use KrubiK\Render\DTOs\Voice as VoiceDTO;

// Import all the RichEntity classes that these helpers will instantiate.
// This ensures proper type-hinting and return type declarations for IDE support.
use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\RichElements\Blocks\RichBlockEntity;

use KrubiK\Render\RichElements\Texts\RichText;
use KrubiK\Render\RichElements\Texts\RichTextAnchorLink;
use KrubiK\Render\RichElements\Texts\RichTextBankCardNumber;
use KrubiK\Render\RichElements\Texts\RichTextBold;
use KrubiK\Render\RichElements\Texts\RichTextBotCommand;
use KrubiK\Render\RichElements\Texts\RichTextCashtag;
use KrubiK\Render\RichElements\Texts\RichTextCode;
use KrubiK\Render\RichElements\Texts\RichTextCustomEmoji;
use KrubiK\Render\RichElements\Texts\RichTextDateTime;
use KrubiK\Render\RichElements\Texts\RichTextEmailAddress;
use KrubiK\Render\RichElements\Texts\RichTextHashtag;
use KrubiK\Render\RichElements\Texts\RichTextItalic;
use KrubiK\Render\RichElements\Texts\RichTextMarked;
use KrubiK\Render\RichElements\Texts\RichTextMathematicalExpression;
use KrubiK\Render\RichElements\Texts\RichTextMention;
use KrubiK\Render\RichElements\Texts\RichTextPhoneNumber;
use KrubiK\Render\RichElements\Texts\RichTextPlain;
use KrubiK\Render\RichElements\Texts\RichTextPre;
use KrubiK\Render\RichElements\Texts\RichTextReference;
use KrubiK\Render\RichElements\Texts\RichTextReferenceLink;
use KrubiK\Render\RichElements\Texts\RichTextSpoiler;
use KrubiK\Render\RichElements\Texts\RichTextStrikethrough;
use KrubiK\Render\RichElements\Texts\RichTextSubscript;
use KrubiK\Render\RichElements\Texts\RichTextSuperscript;
use KrubiK\Render\RichElements\Texts\RichTextTextMention;
use KrubiK\Render\RichElements\Texts\RichTextUnderline;
use KrubiK\Render\RichElements\Texts\RichTextUrl;

use KrubiK\Render\RichElements\Texts\RichTextButton;
use KrubiK\Render\RichElements\Blocks\RichBlockButtons;
use KrubiK\Render\RichElements\Blocks\RichBlockButtonRow;
use KrubiK\Render\RichElements\Components\RichButton;
use KrubiK\Keyboard\PowerButton;
use KrubiK\Enums\ButtonType;

use KrubiK\Render\RichElements\Components\RichBlockCaption;
use KrubiK\Render\RichElements\Components\RichBlockListItem;
use KrubiK\Render\RichElements\Components\RichBlockTableCell;

use KrubiK\Render\RichElements\Blocks\RichBlockAnchor;
use KrubiK\Render\RichElements\Blocks\RichBlockAnimation;
use KrubiK\Render\RichElements\Blocks\RichBlockAudio;
use KrubiK\Render\RichElements\Blocks\RichBlockBlockQuotation;
use KrubiK\Render\RichElements\Blocks\RichBlockCollage;
use KrubiK\Render\RichElements\Blocks\RichBlockDetails;
use KrubiK\Render\RichElements\Blocks\RichBlockDivider;
use Krubot\Render\RichElements\Blocks\RichBlockSeparator; // Not Telegram Original !
use KrubiK\Render\RichElements\Blocks\RichBlockFooter;
use KrubiK\Render\RichElements\Blocks\RichBlockHeading;
use KrubiK\Render\RichElements\Blocks\RichBlockList;
use KrubiK\Render\RichElements\Blocks\RichBlockMap;
use KrubiK\Render\RichElements\Blocks\RichBlockParagraph;
use KrubiK\Render\RichElements\Blocks\RichBlockPhoto;
use KrubiK\Render\RichElements\Blocks\RichBlockPullQuotation;
use KrubiK\Render\RichElements\Blocks\RichBlockSlideshow;
use KrubiK\Render\RichElements\Blocks\RichBlockTable;
use KrubiK\Render\RichElements\Blocks\RichBlockThinking;
use KrubiK\Render\RichElements\Blocks\RichBlockVideo;
use KrubiK\Render\RichElements\Blocks\RichBlockVoiceNote;
use KrubiK\Render\RichElements\Blocks\RichBlockFootnoteDefinition; // Not Telegram Original !

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

/**
 * Creates a generic RichText container instance.
 * Serves as a base wrapper for other rich elements or plain text.
 *
 * @param RichEntity|callable|string|array $text The content to be wrapped.
 * @return RichText
 */
function text(RichEntity|callable|string|array $text): RichText
{
    // Delegates object creation to the static factory method for consistency.
    return RichText::make($text);
}

/**
 * Creates a RichBlockAnchor instance.
 * Represents a named anchor point within the document for internal linking.
 *
 * @param string $name The unique name for the anchor.
 * @return RichBlockAnchor
 */
function anchor(string $name): RichBlockAnchor
{
    return RichBlockAnchor::make($name);
}

/**
 * Creates a RichTextAnchorLink instance.
 * Represents an internal link to an anchor within the document.
 *
 * @param RichEntity|callable|string|array $text The visible, clickable text.
 * @param string $anchorName The name of the anchor (e.g., "section-2") to link to.
 * @return RichTextAnchorLink
 */
function anchorLink(RichEntity|callable|string|array $text, string $anchorName): RichTextAnchorLink
{
    return RichTextAnchorLink::make($text, $anchorName);
}

/**
 * Creates a RichTextBankCardNumber instance.
 * Represents a formatted bank card number.
 *
 * @param RichEntity|callable|string|array $text The visible text, often a masked version of the card number.
 * @param string $bankCardNumber The full bank card number string.
 * @return RichTextBankCardNumber
 */
function bankCardNumber(RichEntity|callable|string|array $text, string $bankCardNumber): RichTextBankCardNumber
{
    return RichTextBankCardNumber::make($text, $bankCardNumber);
}

/**
 * Creates a RichTextBold instance.
 * Wraps content to be displayed in bold.
 *
 * @param RichEntity|callable|string|array $text The content to render as bold.
 * @return RichTextBold
 */
function bold(RichEntity|callable|string|array $text): RichTextBold
{
    return RichTextBold::make($text);
}

/**
 * Creates a RichTextBotCommand instance.
 * Represents a clickable bot command (e.g., /start).
 *
 * @param RichEntity|callable|string|array $text The visible text of the command.
 * @param string $botCommand The actual command string to be executed (e.g., "/help").
 * @return RichTextBotCommand
 */
function botCommand(RichEntity|callable|string|array $text, string $botCommand): RichTextBotCommand
{
    return RichTextBotCommand::make($text, $botCommand);
}

/**
 * Creates a RichTextCashtag instance.
 * Represents a cashtag (e.g., $KRUB).
 *
 * @param RichEntity|callable|string|array $text The visible text of the cashtag.
 * @param string $cashtag The cashtag identifier (e.g., "KRUB").
 * @return RichTextCashtag
 */
function cashtag(RichEntity|callable|string|array $text, string $cashtag): RichTextCashtag
{
    return RichTextCashtag::make($text, $cashtag);
}

/**
 * Creates a RichTextCode instance.
 * Wraps content to be displayed as inline, monospaced code.
 *
 * @param RichEntity|callable|string|array $text The code snippet.
 * @return RichTextCode
 */
function code(RichEntity|callable|string|array $text): RichTextCode
{
    return RichTextCode::make($text);
}

/**
 * Creates a RichTextCustomEmoji instance.
 * Represents a custom emoji, requiring an ID for rendering.
 *
 * @param string $customEmojiId The unique identifier for the custom emoji.
 * @param string $alternativeText The fallback text description of the emoji (for accessibility).
 * @return RichTextCustomEmoji
 */
function customEmoji(string $customEmojiId, string $alternativeText): RichTextCustomEmoji
{
    return RichTextCustomEmoji::make($customEmojiId, $alternativeText);
}

/**
 * Creates a RichTextDateTime instance.
 * Represents a formatted date and time.
 *
 * @param RichEntity|callable|string|array $text The visible text representing the date/time.
 * @param int $unixTime The timestamp in Unix epoch format.
 * @param string $dateTimeFormat A string describing the format (e.g., "YYYY-MM-DD HH:mm").
 * @return RichTextDateTime
 */
function dateTime(RichEntity|callable|string|array $text, int $unixTime, string $dateTimeFormat): RichTextDateTime
{
    return RichTextDateTime::make($text, $unixTime, $dateTimeFormat);
}

/**
 * Creates a RichTextEmailAddress instance.
 * Represents a clickable email link (mailto:).
 *
 * @param RichEntity|callable|string|array $text The visible text of the email link. Can be a simple string or other RichElement entities.
 * @param string $email_address The actual email address (e.g., 'user@example.com').
 * @return RichTextEmailAddress
 */
function emailAddress(RichEntity|callable|string|array $text, string $email_address): RichTextEmailAddress
{
    // This helper function uses 'email_address' as the parameter name to align with
    // the public property name in the final entity, providing a consistent DSL.
    // It correctly passes this value to the ::make method which expects a camelCase argument.
    return RichTextEmailAddress::make($text, $email_address);
}

/**
 * Creates a RichTextHashtag instance.
 * Represents a hashtag (e.g., #php).
 *
 * @param RichEntity|callable|string|array $text The visible text of the hashtag.
 * @param string $hashtag The hashtag string without the '#' prefix.
 * @return RichTextHashtag
 */
function hashtag(RichEntity|callable|string|array $text, string $hashtag): RichTextHashtag
{
    return RichTextHashtag::make($text, $hashtag);
}

/**
 * Creates a RichTextMarked instance.
 * Wraps content to be highlighted or marked. Often rendered with a yellow background.
 *
 * @param RichEntity|callable|string|array $text The content to be marked/highlighted.
 * @return RichTextMarked
 */
function marked(RichEntity|callable|string|array $text): RichTextMarked
{
    return RichTextMarked::make($text);
}

/**
 * Creates a RichTextItalic instance.
 * Wraps content to be displayed in italics.
 *
 * @param RichEntity|callable|string|array $text The content to render as italic.
 * @return RichTextItalic
 */
function italic(RichEntity|callable|string|array $text): RichTextItalic
{
    return RichTextItalic::make($text);
}

/**
 * Creates a RichTextMention instance.
 * Represents a user mention using their username (e.g., @johndoe).
 *
 * @param RichEntity|callable|string|array $text The visible text of the mention.
 * @param string $username The username being mentioned, without the '@' prefix.
 * @return RichTextMention
 */
function mention(RichEntity|callable|string|array $text, string $username): RichTextMention
{
    return RichTextMention::make($text, $username);
}

/**
 * Creates a RichTextPhoneNumber instance.
 * Represents a clickable phone number.
 *
 * @param RichEntity|callable|string|array $text The visible text for the phone number.
 * @param string $phoneNumber The phone number in a callable format (e.g., "+15551234567").
 * @return RichTextPhoneNumber
 */
function phoneNumber(RichEntity|callable|string|array $text, string $phoneNumber): RichTextPhoneNumber
{
    return RichTextPhoneNumber::make($text, $phoneNumber);
}

/**
 * Creates a RichTextPlain instance.
 * Represents a simple, unformatted string of text.
 *
 * @param callable|string $text The plain text content.
 * @return RichTextPlain
 */
function plain(string $text): RichTextPlain
{
    return RichTextPlain::make($text);
}

/**
 * Creates a RichTextMathematicalExpression instance.
 * Represents a mathematical formula or expression, often rendered with specific formatting (e.g., LaTeX).
 *
 * @param callable|string $expression The mathematical expression as a string.
 * @return RichTextMathematicalExpression
 */
function mathematicalExpression(string $expression): RichTextMathematicalExpression
{
    return RichTextMathematicalExpression::make($expression);
}

/**
 * Creates a RichTextPre instance.
 * Represents a pre-formatted block of text or code, preserving whitespace.
 *
 * @param RichEntity|callable|string|array $text The content of the pre-formatted block.
 * @param string|null $language The programming language for syntax highlighting (e.g., "php", "javascript").
 * @return RichTextPre
 */
function pre(RichEntity|callable|string|array $text, ?string $language = null): RichTextPre
{
    return RichTextPre::make($text, $language);
}

/**
 * Creates a RichTextReference instance.
 * Represents a reference to another part of the content.
 *
 * @param RichEntity|callable|string|array $text The visible text of the reference.
 * @param string $name The unique name or ID of the item being referenced.
 * @return RichTextReference
 */
function reference(RichEntity|callable|string|array $text, string $name): RichTextReference
{
    return RichTextReference::make($text, $name);
}

/**
 * Creates a RichTextReferenceLink instance.
 * Represents a clickable link to a reference.
 *
 * @param RichEntity|callable|string|array $text The visible, clickable text.
 * @param string $referenceName The name of the reference to link to.
 * @return RichTextReferenceLink
 */
function referenceLink(RichEntity|callable|string|array $text, string $referenceName): RichTextReferenceLink
{
    return RichTextReferenceLink::make($text, $referenceName);
}

/**
 * Creates a RichBlockFootnoteDefinition instance.
 * Represents the definition block for a footnote, typically placed at the document's end.
 *
 * @param string $name The unique identifier for the footnote (e.g., "fn-1", "fn:identifier"). This must match the name used in the corresponding footnote referenceLink.
 * @param RichBlockEntity[] $blocks The content blocks of the footnote.
 * @return RichBlockFootnoteDefinition
 */
function footnoteDefinition(string $name, array|Arrayable $blocks): RichBlockFootnoteDefinition
{
    return RichBlockFootnoteDefinition::make($name, $blocks);
}

/**
 * Creates a RichTextSpoiler instance.
 * Wraps content that should be hidden until the user interacts with it.
 *
 * @param RichEntity|callable|string|array $text The content to be concealed.
 * @return RichTextSpoiler
 */
function spoiler(RichEntity|callable|string|array $text): RichTextSpoiler
{
    return RichTextSpoiler::make($text);
}

/**
 * Creates a RichTextStrikethrough instance.
 * Wraps content to be displayed with a line through it.
 *
 * @param RichEntity|callable|string|array $text The content to strike through.
 * @return RichTextStrikethrough
 */
function strikethrough(RichEntity|callable|string|array $text): RichTextStrikethrough
{
    return RichTextStrikethrough::make($text);
}

/**
 * Creates a RichTextSubscript instance.
 * Renders text as a subscript.
 *
 * @param RichEntity|callable|string|array $text The content to be subscripted.
 * @return RichTextSubscript
 */
function subscript(RichEntity|callable|string|array $text): RichTextSubscript
{
    return RichTextSubscript::make($text);
}

/**
 * Creates a RichTextSuperscript instance.
 * Renders text as a superscript.
 *
 * @param RichEntity|callable|string|array $text The content to be superscripted.
 * @return RichTextSuperscript
 */
function superscript(RichEntity|callable|string|array $text): RichTextSuperscript
{
    return RichTextSuperscript::make($text);
}

/**
 * Creates a RichTextTextMention instance.
 * Represents an inline mention of a user, often using their full name as clickable text.
 *
 * @param RichEntity|callable|string|array $text The visible text of the mention (e.g., "John Doe").
 * @param UserDTO|array $user The UserDTO or an array representation of the user being mentioned.
 * @return RichTextTextMention
 */
function textMention(RichEntity|callable|string|array $text, UserDTO|array $user): RichTextTextMention
{
    return RichTextTextMention::make($text, $user);
}

/**
 * Creates a RichTextUnderline instance.
 * Wraps content to be displayed with an underline.
 *
 * @param RichEntity|callable|string|array $text The content to underline.
 * @return RichTextUnderline
 */
function underline(RichEntity|callable|string|array $text): RichTextUnderline
{
    return RichTextUnderline::make($text);
}

/**
 * Creates a RichTextUrl instance.
 * Represents a standard hyperlink.
 *
 * @param RichEntity|callable|string|array $text The visible, clickable text.
 * @param string $url The destination URL.
 * @return RichTextUrl
 */
function href(RichEntity|callable|string|array $text, string $url): RichTextUrl // non-standard name, because Laravel aleardy uses `url()` helper-func
{
    return RichTextUrl::make($text, $url);
}

/**
 * Creates a RichBlockCaption instance.
 * Represents a caption for a block-level element like a table or image.
 *
 * @param RichEntity|callable|string|array $text The main caption text.
 * @param RichEntity|callable|string|array|null $credit Optional credit text, like a citation.
 * @return RichBlockCaption
 */
function caption(RichEntity|callable|string|array $text, RichEntity|callable|string|array|null $credit = null): RichBlockCaption
{
    // Delegates object creation to the static factory method, directly passing all arguments.
    return RichBlockCaption::make($text, $credit);
}

/**
 * Creates a RichBlockListItem instance for use in ordered or unordered lists.
 *
 * @param string $label Label of the item. For unordered lists, often a bullet point. For ordered, the number/letter.
 * @param RichBlockEntity[] $blocks The content of the list item, which can be other rich blocks.
 * @param bool|null $hasCheckbox True if the item should have a checkbox.
 * @param bool|null $isChecked True if the item's checkbox should be checked.
 * @param int|null $value For ordered lists, the numeric value of the item label (e.g., 5).
 * @param string|null $type For ordered lists, the type of the item label; must be one of “a” (lowercase letters), “A” (uppercase), “i” (lowercase Roman), “I” (uppercase Roman), or “1” (numbers).
 * @return RichBlockListItem
 * @throws InvalidArgumentException if the provided 'type' is invalid.
 */
function listItem(
    string $label,
    array|Arrayable $blocks,
    ?bool $hasCheckbox = null,
    ?bool $isChecked = null,
    ?int $value = null,
    ?string $type = null
): RichBlockListItem {
    return RichBlockListItem::make($label, $blocks, $hasCheckbox, $isChecked, $value, $type);
}

/**
 * Creates a RichBlockTableCell instance for use within table rows.
 *
 * @param RichEntity|callable|string|array|null $text The content inside the table cell.
 * @param bool|null $isHeader Set to true if this cell is a header cell (<th>).
 * @param int|null $colspan The number of columns this cell should span.
 * @param int|null $rowspan The number of rows this cell should span.
 * @param string $align The horizontal alignment of the cell's content ('left', 'center', 'right').
 * @param string $valign The vertical alignment of the cell's content ('top', 'middle', 'bottom').
 * @return RichBlockTableCell
 * @throws InvalidArgumentException if alignment values are invalid.
 */
function tableCell(
    RichEntity|callable|string|array|null $text = null,
    ?bool $isHeader = null,
    ?int $colspan = null,
    ?int $rowspan = null,
    string $align = 'left',
    string $valign = 'top'
): RichBlockTableCell {
    return RichBlockTableCell::make($text, $isHeader, $colspan, $rowspan, $align, $valign);
}
function cell(
    RichEntity|callable|string|array|null $text = null,
    ?bool $isHeader = null,
    ?int $colspan = null,
    ?int $rowspan = null,
    string $align = 'left',
    string $valign = 'top'
): RichBlockTableCell {
    return RichBlockTableCell::make($text, $isHeader, $colspan, $rowspan, $align, $valign);
}


/**
 * Creates a RichBlockAnimation instance.
 * Represents an embedded animation (e.g., GIF).
 *
 * @param AnimationDTO|array $animation The Animation model object or its array representation.
 * @param bool|null $hasSpoiler If true, the animation is hidden behind a spoiler overlay.
 * @param RichBlockCaption|null $caption An optional caption for the animation.
 * @return RichBlockAnimation
 */
function animation(AnimationDTO|array $animation, ?bool $hasSpoiler = null, RichBlockCaption|RichEntity|callable|string|null $caption = null): RichBlockAnimation
{
    // Note: The helper function's parameter name ($hasSpoiler) is normalized to snake_case
    // for DSL consistency, matching the readonly property name. The underlying ::make() must
    // correctly map this to its expected argument.
    return RichBlockAnimation::make($animation, $hasSpoiler, $caption);
}

/**
 * Creates a RichBlockAudio instance.
 * Represents an embedded audio file.
 *
 * @param AudioDTO|array $audio The Audio model object or its array representation.
 * @param RichBlockCaption|null $caption An optional caption for the audio file.
 * @return RichBlockAudio
 */
function audio(AudioDTO|array $audio, RichBlockCaption|RichEntity|callable|string|null $caption = null): RichBlockAudio
{
    return RichBlockAudio::make($audio, $caption);
}

/**
 * Creates a RichBlockBlockQuotation instance.
 * Represents a long, indented quotation.
 *
 * @param RichBlockEntity[] $blocks An array of block entities that form the content of the quote.
 * @param RichEntity|callable|string|array|null $credit Optional attribution or credit for the quote.
 * @return RichBlockBlockQuotation
 */
function blockQuotation(array|Arrayable $blocks, RichEntity|callable|string|array|null $credit = null): RichBlockBlockQuotation
{
    return RichBlockBlockQuotation::make($blocks, $credit);
}

/**
 * Creates a RichBlockCollage instance.
 * Represents a collection of other blocks (often images or videos) arranged in a collage format.
 *
 * @param RichBlockEntity[] $blocks An array of block entities to include in the collage.
 * @param RichBlockCaption|null $caption An optional caption for the entire collage.
 * @return RichBlockCollage
 */
function collage(array|Arrayable $blocks, RichBlockCaption|RichEntity|callable|string|null $caption = null): RichBlockCollage
{
    return RichBlockCollage::make($blocks, $caption);
}

/**
 * Creates a RichBlockDetails instance.
 * Represents a collapsible "details" block with a summary.
 *
 * @param RichEntity|callable|string|array $summary The visible summary text the user clicks to expand.
 * @param RichBlockEntity[] $blocks The content that is hidden until the block is opened.
 * @param bool|null $isOpen If true, the details block is initially rendered in an open state.
 * @return RichBlockDetails
 */
function details(RichEntity|callable|string|array $summary, array|Arrayable $blocks, ?bool $isOpen = null): RichBlockDetails
{
    return RichBlockDetails::make($summary, $blocks, $isOpen);
}

/**
 * Creates a RichBlockDivider instance.
 * Represents a horizontal rule or thematic break.
 *
 * @return RichBlockDivider
 */
function divider(): RichBlockDivider
{
    return RichBlockDivider::make();
}

/**
 * Creates a new text-based separator block entity.
 *
 * @param string $char The character to repeat.
 * @param int $length The number of repetitions.
 * @return RichBlockSeparator The constructed separator entity.
 */
function separator(string $char = '—', int $length = 20): RichBlockSeparator
{
    return RichBlockSeparator::make($char, $length);
}

/**
 * Creates a RichBlockHeading instance.
 *
 * @param RichEntity|callable|string|array $text The text of the heading.
 * @param int $size The heading level (e.g., 1 for <h1>, 2 for <h2>).
 * @return RichBlockHeading
 */
function heading(RichEntity|callable|string|array $text, int $size): RichBlockHeading
{
    return RichBlockHeading::make($text, $size);
}

/**
 * Creates a RichBlockFooter instance.
 * Represents a footer section, typically for an article or page.
 *
 * @param RichEntity|callable|string|array $text The content of the footer.
 * @return RichBlockFooter
 */
function footer(RichEntity|callable|string|array $text): RichBlockFooter
{
    return RichBlockFooter::make($text);
}

/**
 * Creates a RichBlockList instance.
 * Represents an ordered ('ordered') or unordered ('bullet') list.
 * NOTE: The function is named 'listBlock' to avoid conflict with the PHP 'list' language construct.
 *
 * @param RichBlockListItem[] $items An array of RichBlockListItem objects created with the listItem() helper.
 * @param string $style The list style. Must be either 'bullet' or 'ordered'.
 * @return RichBlockList
 * @throws InvalidArgumentException if an invalid style is provided.
 */
function listBlock(array|Arrayable $items, string $style = 'bullet'): RichBlockList
{
    return RichBlockList::make($items, $style);
}

/**
 * Creates a RichBlockMap instance.
 * Represents an embedded map.
 *
 * @param LocationDTO|array $location The central Location object or its array representation.
 * @param int $zoom The map zoom level (typically 13-20).
 * @param int $width The expected width of the map in pixels.
 * @param int $height The expected height of the map in pixels.
 * @param RichBlockCaption|null $caption An optional caption for the map.
 * @return RichBlockMap
 */
function map(LocationDTO|array $location, int $zoom, int $width, int $height, RichBlockCaption|RichEntity|callable|string|null $caption = null): RichBlockMap
{
    return RichBlockMap::make($location, $zoom, $width, $height, $caption);
}

/**
 * Creates a RichBlockParagraph instance.
 * Represents a standard paragraph of text.
 *
 * @param RichEntity|callable|string|array $text The content of the paragraph.
 * @return RichBlockParagraph
 */
function paragraph(RichEntity|callable|string|array $text): RichBlockParagraph
{
    return RichBlockParagraph::make($text);
}

/**
 * Creates a RichBlockPhoto instance.
 * Represents an embedded photo.
 *
 * @param PhotoSizeDTO|array $photo The PhotoSize model object or its array representation.
 * @param bool|null $hasSpoiler If true, the photo is hidden behind a spoiler overlay.
 * @param RichBlockCaption|null $caption An optional caption for the photo.
 * @return RichBlockPhoto
 */
function photo(PhotoSizeDTO|array $photo, ?bool $hasSpoiler = null, RichBlockCaption|RichEntity|callable|string|null $caption = null): RichBlockPhoto
{
    return RichBlockPhoto::make($photo, $hasSpoiler, $caption);
}

/**
 * Creates a RichBlockPullQuotation instance.
 * Represents a short, pull-out quotation.
 *
 * @param RichEntity|callable|string|array $text The text of the pull quote.
 * @param RichEntity|callable|string|array|null $credit Optional attribution or credit for the quote.
 * @return RichBlockPullQuotation
 */
function pullQuotation(RichEntity|callable|string|array $text, RichEntity|callable|string|array|null $credit = null): RichBlockPullQuotation
{
    return RichBlockPullQuotation::make($text, $credit);
}

/**
 * Creates a RichBlockSlideshow instance.
 *
 * @param RichBlockEntity[] $blocks An array of block entities that form the slides.
 * @param RichBlockCaption|null $caption An optional caption for the entire slideshow.
 * @return RichBlockSlideshow
 */
function slideshow(array|Arrayable $blocks, RichBlockCaption|RichEntity|callable|string|null $caption = null): RichBlockSlideshow
{
    return RichBlockSlideshow::make($blocks, $caption);
}

/**
 * Creates a RichBlockTable instance.
 *
 * @param RichBlockTableCell[][] $cells A 2D array|Arrayable of RichBlockTableCell objects.
 * @param bool|null $isBordered If true, the table should have borders.
 * @param bool|null $isStriped If true, table rows should be striped (alternating background colors).
 * @param RichEntity|callable|string|array|null $caption An optional caption for the table (distinct from RichBlockCaption).
 * @return RichBlockTable
 */
function table(array|Arrayable $cells, ?bool $isBordered = null, ?bool $isStriped = null, RichEntity|callable|string|array|null $caption = null): RichBlockTable
{
    return RichBlockTable::make($cells, $isBordered, $isStriped, $caption);
}

/**
 * Creates a RichBlockThinking instance.
 * Can be used to show a "user is typing" or "bot is thinking" indicator.
 *
 * @param RichEntity|callable|string|array $text The text content, often empty or a placeholder.
 * @return RichBlockThinking
 */
function thinking(RichEntity|callable|string|array $text): RichBlockThinking
{
    return RichBlockThinking::make($text);
}

/**
 * Creates a RichBlockVideo instance.
 *
 * @param VideoDTO|array $video The Video model object or its array representation.
 * @param bool|null $hasSpoiler If true, the video is hidden behind a spoiler overlay.
 * @param RichBlockCaption|null $caption An optional caption for the video.
 * @return RichBlockVideo
 */
function video(VideoDTO|array $video, ?bool $hasSpoiler = null, RichBlockCaption|RichEntity|callable|string|null $caption = null): RichBlockVideo
{
    return RichBlockVideo::make($video, $hasSpoiler, $caption);
}

/**
 * Creates a RichBlockVoiceNote instance.
 * Represents an embedded voice note message.
 *
 * @param VoiceDTO|array $voiceNote The Voice model object or its array representation.
 * @param RichBlockCaption|null $caption An optional caption for the voice note.
 * @return RichBlockVoiceNote
 */
function voiceNote(VoiceDTO|array $voiceNote, RichBlockCaption|RichEntity|callable|string|null $caption = null): RichBlockVoiceNote
{
    return RichBlockVoiceNote::make($voiceNote, $caption);
}

// New! Support 10.3 Button & ButtonRow

function button(PowerButton|RichButton|string|RichEntity|callable $text, ?string $actionId = null, string|ButtonType|null $type = null, ?array $payload = [], ?float $width = 1.0): RichTextButton
{
    $btn = ($text instanceof RichButton || $text instanceof PowerButton) ?
        $text
    :
        RichButton::make($text, $actionId, $type, $payload, $width);

    return RichTextButton::make($btn);
}

function buttons(array $buttons, ?string $align = null): RichBlockButtons
{
    return RichBlockButtons::make($buttons, $align);
}

function buttonRow(array $buttons, ?string $align = null): RichBlockButtonRow // exactly-same as RichBlockButtons
{
    return RichBlockButtonRow::make($buttons, $align);
}

/// Custom-Helpers

function filterNulls(array $data): array
{
    return array_filter(
        $data,
        static fn($value): bool => $value !== null
    );
}

/**
 * Recursively renders any content into a rich, formatted text string.
 * This hyper-intelligent engine is the text-based counterpart to `renderHtml`.
 * It centralizes the logic for unwrapping nested structures and allows entities
 * to provide platform-specific text formatting (e.g., Markdown for Telegram).
 *
 * It follows a similar recursive pattern to `renderHtml` but prioritizes the `toText()` contract.
 *
 * @param mixed $content The content to render (object, array, string).
 * @param ?RichEntity $host
 * @return string The resulting formatted text string.
*/
function renderAsText(mixed $content, ?RichEntity $host = null, bool $preserveKeys = false): string|array|null
{
    if($content === null)
        return $preserveKeys ? null : '';

    // Handle string inputs safely
    if(is_string($content) && (!is_object($content))) { // Not Stringable (eg, RichEntity, ...)

        if($content === '')
            return $preserveKeys ? null : '';

        return trim($content);
    }

    // Handle arrays robustly with recursion
    if(is_array($content)) {

        if (empty($content))
            return $preserveKeys ? null : '';

        // If it's a single-element array, unwrap and recursively render it
        if ((!$preserveKeys) && count($content) === 1)
            return renderAsText(reset($content), $host);

        // For multi-element arrays, map and recursively render each item
        $result = [];
        foreach ($content as $key => $item) {
            $rendered = renderAsText($item, $host, $preserveKeys);
            
            if ($preserveKeys) {
                $result[$key] = $rendered;
            } else {
                $result[] = $rendered;
            }
        }

        return $result;
    }

    // Delegate to host entity if provided
    if($host)
        return $host->renderText($content);

    // Fallback for any unknown or unrenderable types.
    if (app()->isProduction())
        return '';

    return '[Cannot render type to text: ' . gettype($content) . ' without providing $host]';
}
