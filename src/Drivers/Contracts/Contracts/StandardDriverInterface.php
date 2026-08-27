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
 * Layer 2: Standard Driver Interface
 * 
 * Methods available in Bale and Telegram, but NOT in Rubika.
 * Extends Universal because B & T include R's features too.
 */
interface StandardDriverInterface extends UniversalDriverInterface
{
    // --- Webhooks ---
    public function deleteWebhook(array $params = []): mixed;
    public function getWebhookInfo(): mixed;

    // --- Media Types (Rubika uses single sendFile) ---
    public function sendPhoto(array $params): mixed;
    public function sendAudio(array $params): mixed;
    public function sendDocument(array $params): mixed;
    public function sendVideo(array $params): mixed;
    public function sendAnimation(array $params): mixed;
    public function sendVoice(array $params): mixed;
    public function sendMediaGroup(array $params): mixed;
    public function sendSticker(array $params): mixed; // Sending is standard, management is not

    // --- Chat Management ---
    public function getChatAdministrators(array $params): mixed;
    public function getChatMemberCount(array $params): mixed;
    public function getChatMember(array $params): mixed;
    public function setChatTitle(array $params): mixed;
    public function setChatDescription(array $params): mixed;
    public function setChatPhoto(array $params): mixed;
    public function deleteChatPhoto(array $params): mixed;
    public function pinChatMessage(array $params): mixed;
    public function unpinChatMessage(array $params): mixed;
    public function unpinAllChatMessages(array $params): mixed;
    public function leaveChat(array $params): mixed;
    
    // --- Admin Actions ---
    public function banChatMember(array $params): mixed;
    public function unbanChatMember(array $params): mixed;
    public function restrictChatMember(array $params): mixed;
    public function promoteChatMember(array $params): mixed;

    // --- Invite Links ---
    public function exportChatInviteLink(array $params): mixed;
    public function createChatInviteLink(array $params): mixed;
    public function revokeChatInviteLink(array $params): mixed;
    public function approveChatJoinRequest(array $params): mixed;
    public function declineChatJoinRequest(array $params): mixed;

    // --- Interaction ---
    public function answerCallbackQuery(array $params): mixed;
    public function sendChatAction(array $params): mixed;
    public function getUserProfilePhotos(array $params): mixed;

    // --- Editing ---
    public function editMessageCaption(array $params): mixed;
    public function editMessageReplyMarkup(array $params): mixed;
    
    // --- Basic Commerce (Shipping/PreCheckout) ---
    // Note: sendInvoice is removed (VIP specific), but answering queries is often standard logic
    public function answerShippingQuery(array $params): mixed;
    public function answerPreCheckoutQuery(array $params): mixed;

        // --- Webhook Management (Missing Piece) ---
    /**
     * Set a Webhook.
     * Shared by T & B (Rubika uses updateBotEndpoints).
     */
    public function setWebhook(array $params): mixed;

    // --- Message Mechanics ---
    /**
     * Copy messages (Text/Media) without forwarding label.
     * Shared by T & B.
     */
    public function copyMessage(array $params): mixed;

    // --- Invite Links (Missing Edit) ---
    public function editChatInviteLink(array $params): mixed;

    // --- Command Management (Read/Delete) ---
    public function getMyCommands(array $params): mixed;
    public function deleteMyCommands(array $params): mixed;

    // --- Admin Rights (Bale Supports This) ---
    public function setMyDefaultAdministratorRights(array $params): mixed;
    public function getMyDefaultAdministratorRights(array $params): mixed;

    // --- Standard Sticker Management (Shared by T & B) ---
    /**
     * Get a sticker set.
     */
    public function getStickerSet(array $params): mixed;

    /**
     * Standard Set Chat Sticker Set.
     * Set a new group sticker set.
     */
    public function setChatStickerSet(array $params): mixed;

    /**
     * Delete a group sticker set.
     */
    public function deleteChatStickerSet(array $params): mixed;

    // --- Advanced Sticker Management & Manipulations ---
    public function setStickerPositionInSet(array $params): mixed;
    public function deleteStickerFromSet(array $params): mixed;

}
