<?php

namespace KrubiK\Drivers\Arcane;
/*
| Krubot BotEngine: The Architect's Lexicon [×0.7 ALPHA×] 🚀📜
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
 * Trait TelegramExclusiveMethods
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×v0.7ALPHA×
 * @license MIT
*/
trait TelegramExclusiveMethods
{
    // ========================================================================
    // 🚀 EXPLICIT METHOD OVERRIDES (For Strict Type Handling)
    // ========================================================================
    // While makeRequest handles most things, overriding these ensures that even
    // direct calls to $driver->sendPhoto() use our File Logic.

    /**
     * {@inheritDoc}
     */
    public function sendPhoto(array $params): Message
    {
        if (isset($params['photo'])) {
            $params['photo'] = $this->ensureInputFile($params['photo']);
        }
        return parent::sendPhoto($params);
    }

    /**
     * {@inheritDoc}
     */
    public function sendAudio(array $params): Message
    {
        if (isset($params['audio'])) {
            $params['audio'] = $this->ensureInputFile($params['audio']);
        }
        return parent::sendAudio($params);
    }

    /**
     * {@inheritDoc}
     */
    public function sendDocument(array $params): Message
    {
        if (isset($params['document'])) {
            $params['document'] = $this->ensureInputFile($params['document']);
        }
        return parent::sendDocument($params);
    }

    /**
     * {@inheritDoc}
     */
    public function sendVideo(array $params): Message
    {
        if (isset($params['video'])) {
            $params['video'] = $this->ensureInputFile($params['video']);
        }
        return parent::sendVideo($params);
    }

    /**
     * {@inheritDoc}
     */
    public function sendVoice(array $params): Message
    {
        if (isset($params['voice'])) {
            $params['voice'] = $this->ensureInputFile($params['voice']);
        }
        return parent::sendVoice($params);
    }

    /**
     * {@inheritDoc}
     */
    public function sendAnimation(array $params): Message
    {
        if (isset($params['animation'])) {
            $params['animation'] = $this->ensureInputFile($params['animation']);
        }
        return parent::sendAnimation($params);
    }

    /**
     * {@inheritDoc}
     */
    public function sendSticker(array $params): Message
    {
        if (isset($params['sticker'])) {
            $params['sticker'] = $this->ensureInputFile($params['sticker']);
        }
        return parent::sendSticker($params);
    }

    /**
     * {@inheritDoc}
     */
    public function sendVideoNote(array $params): Message
    {
        if (isset($params['video_note'])) {
            $params['video_note'] = $this->ensureInputFile($params['video_note']);
        }
        return parent::sendVideoNote($params);
    }

    /**
     * {@inheritDoc}
     */
    public function setWebhook(array $params): bool
    {
        // مدیریت هوشمند آپلود سرتیفیکیت
         if (isset($params['certificate'])) {
            $params['certificate'] = $this->ensureInputFile($params['certificate']);
        }
        return parent::setWebhook($params);
    }

    /* -------------------------------------------------------------------------- */
    /*                            1. UPDATES & WEBHOOK                            */
    /* -------------------------------------------------------------------------- */

    public function getUpdates(array $params = []): array
    {
        return parent::getUpdates($params);
    }

    public function deleteWebhook(array $params = []): bool
    {
        return parent::deleteWebhook($params);
    }

    public function getWebhookInfo(): object
    {
        return parent::getWebhookInfo();
    }

    /* -------------------------------------------------------------------------- */
    /*                             2. BASE & AUTH                                 */
    /* -------------------------------------------------------------------------- */

    public function getMe(): object
    {
        return parent::getMe();
    }

    public function logOut(): bool
    {
        return parent::logOut();
    }

    public function close(): bool
    {
        return parent::close();
    }

    public function getFile(array $params): object
    {
        return parent::getFile($params);
    }

    public function getUserProfilePhotos(array $params): object
    {
        return parent::getUserProfilePhotos($params);
    }

    /* -------------------------------------------------------------------------- */
    /*                          3. SENDING MESSAGES                               */
    /* -------------------------------------------------------------------------- */

    public function sendMessage(array $params): object
    {
        return parent::sendMessage($params);
    }

    public function forwardMessage(array $params): object
    {
        return parent::forwardMessage($params);
    }

    public function copyMessage(array $params): object
    {
        return parent::copyMessage($params);
    }

    public function sendPhoto(array $params): object
    {
        if (isset($params['photo'])) {
            $params['photo'] = $this->ensureInputFile($params['photo']);
        }
        return parent::sendPhoto($params);
    }

    public function sendAudio(array $params): object
    {
        if (isset($params['audio'])) {
            $params['audio'] = $this->ensureInputFile($params['audio']);
        }
        return parent::sendAudio($params);
    }

    public function sendDocument(array $params): object
    {
        if (isset($params['document'])) {
            $params['document'] = $this->ensureInputFile($params['document']);
        }
        return parent::sendDocument($params);
    }

    public function sendVideo(array $params): object
    {
        if (isset($params['video'])) {
            $params['video'] = $this->ensureInputFile($params['video']);
        }
        return parent::sendVideo($params);
    }

    public function sendAnimation(array $params): object
    {
        if (isset($params['animation'])) {
            $params['animation'] = $this->ensureInputFile($params['animation']);
        }
        return parent::sendAnimation($params);
    }

    public function sendVoice(array $params): object
    {
        if (isset($params['voice'])) {
            $params['voice'] = $this->ensureInputFile($params['voice']);
        }
        return parent::sendVoice($params);
    }

    public function sendVideoNote(array $params): object
    {
        if (isset($params['video_note'])) {
            $params['video_note'] = $this->ensureInputFile($params['video_note']);
        }
        return parent::sendVideoNote($params);
    }

    public function sendMediaGroup(array $params): object
    {
        // در اینجا فرض بر این است که InputMediaها قبلاً ساخته شده‌اند
        // یا کاربر آرایه خام فرستاده که SDK هندل می‌کند.
        return parent::sendMediaGroup($params);
    }

    public function sendLocation(array $params): object
    {
        return parent::sendLocation($params);
    }

    public function sendVenue(array $params): object
    {
        return parent::sendVenue($params);
    }

    public function sendContact(array $params): object
    {
        return parent::sendContact($params);
    }

    public function sendPoll(array $params): object
    {
        return parent::sendPoll($params);
    }

    public function sendDice(array $params): object
    {
        return parent::sendDice($params);
    }

    public function sendChatAction(array $params): bool
    {
        return parent::sendChatAction($params);
    }

    public function setMessageReaction(array $params): bool
    {
        return parent::setMessageReaction($params);
    }

    /* -------------------------------------------------------------------------- */
    /*                          4. EDITING MESSAGES                               */
    /* -------------------------------------------------------------------------- */

    public function editMessageText(array $params): mixed
    {
        return parent::editMessageText($params);
    }

    public function editMessageCaption(array $params): mixed
    {
        return parent::editMessageCaption($params);
    }

    public function editMessageMedia(array $params): mixed
    {
        return parent::editMessageMedia($params);
    }

    public function editMessageReplyMarkup(array $params): mixed
    {
        return parent::editMessageReplyMarkup($params);
    }

    public function stopPoll(array $params): object
    {
        return parent::stopPoll($params);
    }

    public function deleteMessage(array $params): bool
    {
        return parent::deleteMessage($params);
    }

    public function deleteMessages(array $params): bool
    {
        // اگر SDK متد deleteMessages را نداشت، از __call والد استفاده می‌کند
        return parent::deleteMessages($params);
    }

    /* -------------------------------------------------------------------------- */
    /*                          5. CHAT ADMINISTRATION                            */
    /* -------------------------------------------------------------------------- */

    public function banChatMember(array $params): bool
    {
        return parent::banChatMember($params);
    }

    public function unbanChatMember(array $params): bool
    {
        return parent::unbanChatMember($params);
    }

    public function restrictChatMember(array $params): bool
    {
        return parent::restrictChatMember($params);
    }

    public function promoteChatMember(array $params): bool
    {
        return parent::promoteChatMember($params);
    }

    public function setChatAdministratorCustomTitle(array $params): bool
    {
        return parent::setChatAdministratorCustomTitle($params);
    }

    public function banChatSenderChat(array $params): bool
    {
        return parent::banChatSenderChat($params);
    }

    public function unbanChatSenderChat(array $params): bool
    {
        return parent::unbanChatSenderChat($params);
    }

    public function setChatPermissions(array $params): bool
    {
        return parent::setChatPermissions($params);
    }

    public function exportChatInviteLink(array $params): string
    {
        return parent::exportChatInviteLink($params);
    }

    public function createChatInviteLink(array $params): object
    {
        return parent::createChatInviteLink($params);
    }

    public function editChatInviteLink(array $params): object
    {
        return parent::editChatInviteLink($params);
    }

    public function revokeChatInviteLink(array $params): object
    {
        return parent::revokeChatInviteLink($params);
    }

    public function approveChatJoinRequest(array $params): bool
    {
        return parent::approveChatJoinRequest($params);
    }

    public function declineChatJoinRequest(array $params): bool
    {
        return parent::declineChatJoinRequest($params);
    }

    public function setChatPhoto(array $params): bool
    {
        if (isset($params['photo'])) {
            $params['photo'] = $this->ensureInputFile($params['photo']);
        }
        return parent::setChatPhoto($params);
    }

    public function deleteChatPhoto(array $params): bool
    {
        return parent::deleteChatPhoto($params);
    }

    public function setChatTitle(array $params): bool
    {
        return parent::setChatTitle($params);
    }

    public function setChatDescription(array $params): bool
    {
        return parent::setChatDescription($params);
    }

    public function pinChatMessage(array $params): bool
    {
        return parent::pinChatMessage($params);
    }

    public function unpinChatMessage(array $params): bool
    {
        return parent::unpinChatMessage($params);
    }

    public function unpinAllChatMessages(array $params): bool
    {
        return parent::unpinAllChatMessages($params);
    }

    public function leaveChat(array $params): bool
    {
        return parent::leaveChat($params);
    }

    public function getChat(array $params): object
    {
        return parent::getChat($params);
    }

    public function getChatAdministrators(array $params): array
    {
        return parent::getChatAdministrators($params);
    }

    public function getChatMemberCount(array $params): int
    {
        return parent::getChatMemberCount($params);
    }

    public function getChatMember(array $params): object
    {
        return parent::getChatMember($params);
    }

    public function setChatStickerSet(array $params): bool
    {
        return parent::setChatStickerSet($params);
    }

    public function deleteChatStickerSet(array $params): bool
    {
        return parent::deleteChatStickerSet($params);
    }

    /* -------------------------------------------------------------------------- */
    /*                          6. FORUM & TOPICS                                 */
    /* -------------------------------------------------------------------------- */

    public function getForumTopicIconStickers(array $params = []): array
    {
        return parent::getForumTopicIconStickers($params);
    }

    public function createForumTopic(array $params): object
    {
        return parent::createForumTopic($params);
    }

    public function editForumTopic(array $params): bool
    {
        return parent::editForumTopic($params);
    }

    public function closeForumTopic(array $params): bool
    {
        return parent::closeForumTopic($params);
    }

    public function reopenForumTopic(array $params): bool
    {
        return parent::reopenForumTopic($params);
    }

    public function deleteForumTopic(array $params): bool
    {
        return parent::deleteForumTopic($params);
    }

    public function unpinAllForumTopicMessages(array $params): bool
    {
        return parent::unpinAllForumTopicMessages($params);
    }

    public function editGeneralForumTopic(array $params): bool
    {
        return parent::editGeneralForumTopic($params);
    }

    public function closeGeneralForumTopic(array $params): bool
    {
        return parent::closeGeneralForumTopic($params);
    }

    public function reopenGeneralForumTopic(array $params): bool
    {
        return parent::reopenGeneralForumTopic($params);
    }

    public function hideGeneralForumTopic(array $params): bool
    {
        return parent::hideGeneralForumTopic($params);
    }

    public function unhideGeneralForumTopic(array $params): bool
    {
        return parent::unhideGeneralForumTopic($params);
    }

    /* -------------------------------------------------------------------------- */
    /*                             7. STICKERS                                    */
    /* -------------------------------------------------------------------------- */

    public function sendSticker(array $params): object
    {
        if (isset($params['sticker'])) {
            $params['sticker'] = $this->ensureInputFile($params['sticker']);
        }
        return parent::sendSticker($params);
    }

    public function getStickerSet(array $params): object
    {
        return parent::getStickerSet($params);
    }

    public function uploadStickerFile(array $params): object
    {
        if (isset($params['png_sticker'])) {
            $params['png_sticker'] = $this->ensureInputFile($params['png_sticker']);
        }
        // پشتیبانی از فرمت TGS برای استیکرهای متحرک
        if (isset($params['tgs_sticker'])) {
            $params['tgs_sticker'] = $this->ensureInputFile($params['tgs_sticker']);
        }
        if (isset($params['webm_sticker'])) {
            $params['webm_sticker'] = $this->ensureInputFile($params['webm_sticker']);
        }
        return parent::uploadStickerFile($params);
    }

    public function createNewStickerSet(array $params): bool
    {
        if (isset($params['png_sticker'])) $params['png_sticker'] = $this->ensureInputFile($params['png_sticker']);
        if (isset($params['tgs_sticker'])) $params['tgs_sticker'] = $this->ensureInputFile($params['tgs_sticker']);
        if (isset($params['webm_sticker'])) $params['webm_sticker'] = $this->ensureInputFile($params['webm_sticker']);
        return parent::createNewStickerSet($params);
    }

    public function addStickerToSet(array $params): bool
    {
        if (isset($params['png_sticker'])) $params['png_sticker'] = $this->ensureInputFile($params['png_sticker']);
        if (isset($params['tgs_sticker'])) $params['tgs_sticker'] = $this->ensureInputFile($params['tgs_sticker']);
        if (isset($params['webm_sticker'])) $params['webm_sticker'] = $this->ensureInputFile($params['webm_sticker']);
        return parent::addStickerToSet($params);
    }

    public function setStickerPositionInSet(array $params): bool
    {
        return parent::setStickerPositionInSet($params);
    }

    public function deleteStickerFromSet(array $params): bool
    {
        return parent::deleteStickerFromSet($params);
    }

    public function setStickerSetThumb(array $params): bool
    {
        if (isset($params['thumb'])) {
            $params['thumb'] = $this->ensureInputFile($params['thumb']);
        }
        return parent::setStickerSetThumb($params);
    }

    /* -------------------------------------------------------------------------- */
    /*                       8. INLINE, WEBAPPS & CALLBACKS                       */
    /* -------------------------------------------------------------------------- */

    public function answerCallbackQuery(array $params): bool
    {
        return parent::answerCallbackQuery($params);
    }

    public function answerInlineQuery(array $params): bool
    {
        return parent::answerInlineQuery($params);
    }

    public function answerWebAppQuery(array $params): object
    {
        return parent::answerWebAppQuery($params);
    }

    /* -------------------------------------------------------------------------- */
    /*                             9. PAYMENTS                                    */
    /* -------------------------------------------------------------------------- */

    public function sendInvoice(array $params): object
    {
        return parent::sendInvoice($params);
    }

    public function createInvoiceLink(array $params): string
    {
        return parent::createInvoiceLink($params);
    }

    public function answerShippingQuery(array $params): bool
    {
        return parent::answerShippingQuery($params);
    }

    public function answerPreCheckoutQuery(array $params): bool
    {
        return parent::answerPreCheckoutQuery($params);
    }

    /* -------------------------------------------------------------------------- */
    /*                        10. GAMES & PASSPORT                                */
    /* -------------------------------------------------------------------------- */

    public function sendGame(array $params): object
    {
        return parent::sendGame($params);
    }

    public function setGameScore(array $params): mixed
    {
        return parent::setGameScore($params);
    }

    public function getGameHighScores(array $params): array
    {
        return parent::getGameHighScores($params);
    }

    public function setPassportDataErrors(array $params): bool
    {
        return parent::setPassportDataErrors($params);
    }

    /* -------------------------------------------------------------------------- */
    /*                          11. LOCATION (LIVE)                               */
    /* -------------------------------------------------------------------------- */

    public function editMessageLiveLocation(array $params): mixed
    {
        return parent::editMessageLiveLocation($params);
    }

    public function stopMessageLiveLocation(array $params): mixed
    {
        return parent::stopMessageLiveLocation($params);
    }

    /* -------------------------------------------------------------------------- */
    /*                        12. BOT COMMANDS & MENUS                            */
    /* -------------------------------------------------------------------------- */

    public function setMyCommands(array $params): bool
    {
        return parent::setMyCommands($params);
    }

    public function deleteMyCommands(array $params): bool
    {
        return parent::deleteMyCommands($params);
    }

    public function getMyCommands(array $params): array
    {
        return parent::getMyCommands($params);
    }

    public function setMyName(array $params): bool
    {
        return parent::setMyName($params);
    }

    public function getMyName(array $params): object
    {
        return parent::getMyName($params);
    }

    public function setMyDescription(array $params): bool
    {
        return parent::setMyDescription($params);
    }

    public function getMyDescription(array $params): object
    {
        return parent::getMyDescription($params);
    }

    public function setMyShortDescription(array $params): bool
    {
        return parent::setMyShortDescription($params);
    }

    public function getMyShortDescription(array $params): object
    {
        return parent::getMyShortDescription($params);
    }

    public function setChatMenuButton(array $params): bool
    {
        return parent::setChatMenuButton($params);
    }

    public function getChatMenuButton(array $params): object
    {
        return parent::getChatMenuButton($params);
    }

    public function setMyDefaultAdministratorRights(array $params): bool
    {
        return parent::setMyDefaultAdministratorRights($params);
    }

    public function getMyDefaultAdministratorRights(array $params): object
    {
        return parent::getMyDefaultAdministratorRights($params);
    }

}
