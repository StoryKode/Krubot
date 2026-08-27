<?php

namespace KrubiK\Drivers\Contracts\Layers;
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
 * Layer 3: Telegram Exclusive Interface
 * 
 * Features unique to Telegram (Games, Passport, Forum Topics, Inline Mode, etc).
 * Extends Standard because Telegram includes everything from Layers 1 & 2.
*/

interface TelegramExclusiveInterface extends StandardDriverInterface
{
    // --- Session Management ---
    public function logOut(): mixed;
    public function close(): mixed;

    // --- Advanced Media & Interactions ---
    public function sendVideoNote(array $params): mixed;
    public function sendVenue(array $params): mixed;
    public function sendDice(array $params): mixed;
    public function editMessageMedia(array $params): mixed;
    public function editMessageLiveLocation(array $params): mixed;
    public function stopMessageLiveLocation(array $params): mixed;
    public function setMessageReaction(array $params): mixed;

    // --- Advanced Chat Administration ---
    public function setChatAdministratorCustomTitle(array $params): mixed;
    public function banChatSenderChat(array $params): mixed;
    public function unbanChatSenderChat(array $params): mixed;
    public function setChatPermissions(array $params): mixed;
    public function deleteMessages(array $params): mixed; // Batch delete

    // --- Forum / Topics (The Full Suite) ---
    public function createForumTopic(array $params): mixed;
    public function editForumTopic(array $params): mixed;
    public function closeForumTopic(array $params): mixed;
    public function reopenForumTopic(array $params): mixed;
    public function deleteForumTopic(array $params): mixed;
    public function unpinAllForumTopicMessages(array $params): mixed;
    public function getForumTopicIconStickers(array $params = []): mixed;
    
    public function editGeneralForumTopic(array $params): mixed;
    public function closeGeneralForumTopic(array $params): mixed;
    public function reopenGeneralForumTopic(array $params): mixed;
    public function hideGeneralForumTopic(array $params): mixed;
    public function unhideGeneralForumTopic(array $params): mixed;

    // --- Payments (Telegram Style Specifics) ---
    // Telegram's specific invoice signature
    // public function sendInvoice(array $params): mixed; 
    public function createInvoiceLink(array $params): mixed;

    // --- Inline Mode & Web Apps ---
    public function answerInlineQuery(array $params): mixed;
    public function answerWebAppQuery(array $params): mixed;

    // --- Games ---
    public function sendGame(array $params): mixed;
    public function setGameScore(array $params): mixed;
    public function getGameHighScores(array $params): mixed;

    // --- Passport ---
    public function setPassportDataErrors(array $params): mixed;

    // --- Poll Management ---
    public function stopPoll(array $params): mixed;

    // --- Bot Identity (Newer Telegram Features) ---
    // --- Bot Identity & Menu (Newer Features) ---
    public function setMyName(array $params): mixed;
    public function getMyName(array $params): mixed;

    public function setMyDescription(array $params): mixed;
    public function getMyDescription(array $params): mixed;

    public function setMyShortDescription(array $params): mixed;
    public function getMyShortDescription(array $params): mixed;

    // --- Menu & Buttons ---
    public function setChatMenuButton(array $params): mixed;
    public function getChatMenuButton(array $params): mixed;

    // --- Advanced Sticker Management & Manipulations ---
    public function setStickerSetThumb(array $params): mixed;

    /* -------------------------------------------------------------------------- */
    /*              THE 5 MISSING STANDARD METHODS (Counter-VIPs)                 */
    /*                                                                            */
    /*          THE 5 STANDARD COUNTERPARTS WITH: (Bale's VIP Origins)            */
    /*    (These are Standard signatures, different from BaleVipInterface)        */
    /* -------------------------------------------------------------------------- */
    /* -------------------------------------------------------------------------- */
    /*           VIP COUNTERPARTS & ADVANCED STICKER MANAGEMENT                   */
    /*                                                                            */
    /*   1. Methods that are Standard in Telegram but VIP (Modified) in Bale.     */
    /*   2. Advanced sticker methods not present in Bale/Rubika.                  */
    /* -------------------------------------------------------------------------- */

    /**
     * Standard Invoice (Telegram Signature).
     * (Note: Bale uses a different signature/provider logic).
    */
    public function sendInvoice(array $params): mixed;

    /**
     * Standard Upload Sticker File.
     * (Note: Bale supports direct zip upload differently).
    */
    public function uploadStickerFile(array $params): mixed;

    /**
     * Standard Create New Sticker Set.
     * (Bale has unique package naming rules).
    */
    public function createNewStickerSet(array $params): mixed;

    /**
     * Standard Add Sticker To Set.
    */
    public function addStickerToSet(array $params): mixed;

    /**
     * Standard Set Chat Sticker Set.
     * Set a new group sticker set.
     */
    public function setChatStickerSet(array $params): mixed;
}



