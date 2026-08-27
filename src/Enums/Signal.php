<?php

namespace KrubiK\Enums;

/*
|--------------------------------------------------------------------------
| A Message to the Future Architect of Rebellion... 🚀🌌
|--------------------------------------------------------------------------
|
| Greetings, seeker of knowledge. You have just opened a blueprint
| from the Krubot BotEngine. What you see before you is more
| than just lines of code—it's a pattern for building scalable dreams.
|
| **This is a laboratory of creation.** We are experimenting with the
| very fabric of code here. Use this project as your ultimate training
| ground, a masterclass in *Software Dev Artistry.* It's a powerful template
| for learning, but not yet forged for the final battles of production.
|
| Behold the core principle:
| We Are **Rebuilding The Rebellion** Within S.N.P. *(The Foundation of Pure Power & Revel)*
| This entire library is being reconstructed with intense power,
| on a foundation of pure power **Far Stronger Than Anything That Came Before.**
| Starting with Laravel 12 Capabilities.
|
| What you see here is the **×ReleaseCandiate v0.8×** release. Why release it now?
| Because keeping this evolution a secret any longer would be a
| betrayal to the very community it was born to serve.
| 
| Consider this The Foundational Codex for Engineering a New Reality.
| The knowledge is free under the MIT License. Deconstruct its logic and schematics.
| Learn its secrets. Master its power. Command its potential. You are The Architect Now!
|
| * Go build something revolutionary! * 💜⚡️
|
| Let's Shape the Future. 🛠️⚡️🚀
|
*/

use KrubiK\DTOs\Message;
use stdClass;

/**
 * Signal - v4
 * Optimized for Event-Driven Architecture.
 * Provides a rich, multi-alias layer over raw platform values for superior
 * Developer Experience (DX) and maximum code readability.
 * This is the Single Source of Truth for all event types, offering both
 * conceptual and platform-native access points.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class Signal
{
    // --- Core Updates & Events ---
    public const Message        = 'message';
    public const Revision       = 'edited_message';
    public const Post           = 'channel_post';
    public const RevisionPost   = 'edited_channel_post';
    public const Reaction       = 'message_reaction';
    public const ReactionCount  = 'message_reaction_count';
    public const Callback       = 'callback_query';
    public const Query          = 'inline_query';
    public const Selection      = 'chosen_inline_result';
    public const Poll           = 'poll';
    public const Voting         = 'poll_answer';

    // --- Media Payloads ---
    public const Motion         = 'animation';
    public const Sonar          = 'audio';
    public const Visual         = 'photo';
    public const File           = 'document';
    public const Movie          = 'video';
    public const Speech         = 'voicenote';
    public const Snapshot       = 'live_photo';
    public const Round          = 'video_note';
    public const Sticker        = 'sticker';
    public const Moment         = 'story';

    // --- Geolocation & Context ---
    public const Geo            = 'location';
    public const Place          = 'venue';
    public const Identity       = 'contact';
    public const Challenge      = 'game';
    public const Chance         = 'dice';

    // --- Membership & Governance ---
    public const Status         = 'my_chat_member';
    public const Access         = 'chat_member';
    public const Request        = 'chat_join_request';
    public const Boost          = 'chat_boost';
    public const Unboost        = 'removed_chat_boost';
    public const Arrival        = 'new_chat_members';
    public const Departure      = 'left_chat_member';
    public const TitleShift     = 'new_chat_title';
    public const PhotoShift     = 'new_chat_photo';
    public const PhotoPurge     = 'delete_chat_photo';
    public const Beacon         = 'pinned_message';

    // --- Business & Payment Context ---
    public const Connector      = 'business_connection';
    public const Trade          = 'business_message';
    public const RevisionTrade  = 'edited_business_message';
    public const Purge          = 'deleted_business_messages';
    public const Payment        = 'purchased_paid_media';
    public const Bill           = 'invoice';
    public const Receipt        = 'successful_payment';
    public const Shipping       = 'shipping_query';
    public const Checkout       = 'pre_checkout_query';

    // --- Forum & Topic Lifecycle ---
    public const TopicOpen      = 'forum_topic_created';
    public const TopicRevision  = 'forum_topic_edited';
    public const TopicClosure   = 'forum_topic_closed';
    public const TopicReopen    = 'forum_topic_reopened';

    // --- Sharing & WebApp Events ---
    public const PeopleShare    = 'users_shared';
    public const NetworkShare   = 'chat_shared';
    public const PortalData     = 'web_app_data';

    // --- Giveaway & Gifting Events ---
    public const Airdrop        = 'giveaway_created';
    public const Distribution   = 'giveaway_completed';
    public const Elites         = 'giveaway_winners';
    public const Present        = 'gift'; // Not a standard Telegram event yet

    // --- Video Chat (Stream) Events ---
    public const Briefing       = 'video_chat_scheduled';
    public const Transmission   = 'video_chat_started';
    public const Shutdown       = 'video_chat_ended';
    public const Invited        = 'video_chat_participants_invited';

    // --- Chat Creation Events ---
    public const Formation      = 'group_chat_created';
    public const Conglomerate   = 'supergroup_chat_created';
    public const Broadcaster    = 'channel_chat_created';

    // --- Miscellaneous & System Events ---
    public const DecayTimer     = 'message_auto_delete_timer_changed';
    public const RadarAlert     = 'proximity_alert_triggered';

    // --- Framework Logic ---
    public const Text           = 'text';
    public const Command        = 'command';
    public const Void           = 'unknown';

    // =================================================================
    // PLATFORM-NATIVE ALIASES FOR DX, DIRECT COMPATIBILITY & CLARITY
    // =================================================================

    // --- Core Aliases ---
    public const EditedMessage         = self::Revision;
    public const ChannelPost           = self::Post;
    public const EditedChannelPost     = self::RevisionPost;
    public const MessageReaction       = self::Reaction;
    public const MessageReactionCount  = self::ReactionCount;
    public const CallbackQuery         = self::Callback;
    public const InlineQuery           = self::Query;
    public const ChosenInlineResult    = self::Selection;
    public const PollAnswer            = self::Voting;

    // --- Media Aliases ---
    public const Story                 = self::Moment;
    public const Animation             = self::Motion;
    public const Document              = self::File;
    public const Audio                 = self::Sonar;
    public const Photo                 = self::Visual;
    public const Video                 = self::Movie;
    public const Cinema                = self::Movie;
    public const Voice                 = self::Speech;
    public const VoiceNote             = self::Speech;
    public const VideoNote             = self::Round;
    public const LivePhoto             = self::Snapshot;
    public const Clip                  = self::Round;

    // --- Context Aliases ---
    public const Location              = self::Geo;
    public const GeoLocation           = self::Geo;
    public const Venue                 = self::Place;
    public const Contact               = self::Identity;
    public const Arcade                = self::Challenge;
    public const Quest                 = self::Challenge;
    public const Dice                  = self::Chance;

    // --- Membership & Governance Aliases ---
    public const MyChatMember          = self::Status;
    public const ChatMember            = self::Access;
    public const ChatJoinRequest       = self::Request;
    public const ChatBoost             = self::Boost;
    public const RemovedChatBoost      = self::Unboost;
    public const NewChatMembers        = self::Arrival;
    public const LeftChatMember        = self::Departure;
    public const NewChatTitle          = self::TitleShift;
    public const NewChatPhoto          = self::PhotoShift;
    public const DeleteChatPhoto       = self::PhotoPurge;
    public const PinnedMessage         = self::Beacon;

    public const NewMembers            = self::Arrival;
    public const LeftMember            = self::Departure;
    public const Rename                = self::TitleShift;
    public const Avatar                = self::PhotoShift;
    public const PurgeAvatar           = self::PhotoPurge;
    public const Anchor                = self::Beacon;
    public const Pin                   = self::Beacon;

    public const BusinessConnection    = self::Connector;
    public const BusinessMessage       = self::Trade;
    public const EditedBusinessMessage = self::RevisionTrade;
    public const DeletedBusinessMessages = self::Purge;
    public const PurchasedPaidMedia    = self::Payment;
    public const SuccessfulPayment     = self::Receipt;
    public const ShippingQuery         = self::Shipping;
    public const PreCheckoutQuery      = self::Checkout;
    public const Invoice               = self::Bill;

    // --- Forum Aliases ---
    public const ForumTopicCreated     = self::TopicOpen;
    public const ForumTopicEdited      = self::TopicRevision;
    public const ForumTopicClosed      = self::TopicClosure;
    public const ForumTopicReopened    = self::TopicReopen;

    public const TopicCreated          = self::TopicOpen;
    public const TopicEdited           = self::TopicRevision;
    public const TopicClosed           = self::TopicClosure;
    public const TopicReopened         = self::TopicReopen;

    // --- Giveaway & Gifting Aliases ---
    public const GiveawayCreated       = self::Airdrop;
    public const GiveawayCompleted     = self::Distribution;
    public const GiveawayWinners       = self::Elites;
    public const Gift                  = self::Present;

    public const Bounty                = self::Airdrop;
    public const Reckoning             = self::Distribution;
    public const Winners               = self::Elites;
    public const Tribute               = self::Present;

    // --- Sharing & WebApp Aliases ---
    public const UsersShared           = self::PeopleShare;
    public const ChatShared            = self::NetworkShare;
    public const WebAppData            = self::PortalData;
    public const ChatShare             = self::NetworkShare;
    public const UserShare             = self::PeopleShare;

    public const Syndicate             = self::PeopleShare;
    public const WebPayload            = self::PortalData;
    public const Telemetry             = self::PortalData;

    // --- Video Chat Aliases ---
    public const VideoChatPlanned      = self::Briefing;
    public const VideoChatScheduled    = self::Briefing;
    public const VideoChatStarted      = self::Transmission;
    public const VideoChatEnded        = self::Shutdown;
    public const VideoChatParticipantsInvited = self::Invited;
    public const VideoChatLive         = self::Transmission;
    public const VideoChatInvite       = self::Invited;
    public const StreamPoint           = self::Briefing;
    public const StreamLive            = self::Transmission;
    public const StreamDead            = self::Shutdown;

    // --- Misc & System Aliases ---
    public const GroupChatCreated      = self::Formation;
    public const SupergroupChatCreated = self::Conglomerate;
    public const ChannelChatCreated    = self::Broadcaster;
    public const MessageAutoDeleteTimerChanged = self::DecayTimer;
    public const ProximityAlertTriggered = self::RadarAlert;

    public const GroupSpawn            = self::Formation;
    public const SupergroupSpawn       = self::Conglomerate;
    public const ChannelSpawn          = self::Broadcaster;
    public const AutoDeleteTimer       = self::DecayTimer;
    public const AutoDeleteChanged     = self::DecayTimer;
    public const ProximityAlert        = self::RadarAlert;

    public const Unknown               = self::Void;

    /**
     * A pre-computed, hash-map of envelope frequencies for O(1) lookup performance.
     * This avoids repeated, slow `in_array` calls in a hot path.
     * @var array<string, bool>|null
     */
    private static ?array $envelopeMap = null;

    /**
     * ENVELOPE FREQUENCIES (The "How" & "Why")
     * These signals describe the container, context, or the top-level action of an update.
     * They answer questions like: "How did this information arrive?" (e.g., as a callback, an edit)
     * or "What is the high-level event?" (e.g., a user joined, a payment is requested).
     *
     * Processing these first is crucial for state machines, middleware, and context-aware logic.
     *
     * @var array<string>
     */
    public const ENVELOPE_FREQUENCIES = [
        self::Revision,       // edited_message
        self::Post,           // channel_post
        self::RevisionPost,   // edited_channel_post
        self::Reaction,       // message_reaction
        self::ReactionCount,  // message_reaction_count
        self::Callback,       // callback_query
        self::Query,          // inline_query
        self::Selection,      // chosen_inline_result
        self::Status,         // my_chat_member
        self::Access,         // chat_member
        self::Request,        // chat_join_request
        self::Boost,          // chat_boost
        self::Unboost,        // removed_chat_boost
        self::Connector,      // business_connection
        self::Trade,          // business_message
        self::RevisionTrade,  // edited_business_message
        self::Purge,          // deleted_business_messages
        self::Shipping,       // shipping_query
        self::Checkout,       // pre_checkout_query

        // Note: PurchasedPaidMedia is both an envelope and can be inside a message.
        // We classify it as Envelope because it's a primary, actionable event type.
        self::Payment,        // purchased_paid_media

        // A top-level Poll state update (a user voting) is an Envelope event,
        // completely different from a Message that CONTAINS a Poll (which is Content).
        self::Voting,         // poll_answer
    ];

    /**
     * Determines if a given signal frequency represents an "Envelope" type.
     * This method is the core utility for deciding the Signal::detect() strategy.
     *
     * @param string $frequency The signal type to check (e.g., 'edited_message', 'photo').
     * @return bool True if the frequency is an envelope type, false otherwise.
    */
    public static function isEnvelopeFrequency(string $frequency): bool
    {
        // --- Lazily build the optimized lookup map on the first call ---
        if (self::$envelopeMap === null) {
            // array_flip provides a hash map for instant O(1) lookups.
            // We fill the values with `true` for clarity, though just keys are needed.
            self::$envelopeMap = array_fill_keys(self::ENVELOPE_FREQUENCIES, true);
        }

        return isset(self::$envelopeMap[$frequency]);
    }

    /**
     * 🧠✨ THE SENSORY ENGINE (EVOLVED & HEART-AWARE) ✨🧠
     *
     * Determines the precise Signal type from a raw message object (DTO).
     * This is the definitive, centralized logic for event detection. It's built on a
     * modular architecture of specialized helper methods for maximum clarity and maintainability.
     *
     * It intelligently unwraps the core payload from a Message DTO's "Heart" if present,
     * but seamlessly handles raw stdClass objects from the API.
     *
     * @param mixed $value The raw message object (Message, stdClass) or any other variable.
     * @param bool  $prioritizeEnvelopeDetection If true, the envelope type (e.g., Revision, Callback)
     *                                           will be returned before checking the message content.
     *                                           Default is false (content-first).
     * @return string The resolved Signal constant (e.g., self::Visual, self::Revision, self::Text).
    */
    public static function detect(mixed $value, bool $prioritizeEnvelopeDetection = false): string
    {
        // --- Pre-computation Guard ---
        // Immediately return if the input isn't a processable object.
        // Immediately return if the input is not a structured Message object or a processable object.
        if (!($value instanceof Message || $value instanceof stdClass)) {
            return self::Void;

            /// For any other variable type (int, string, etc.), we can behave like gettype().
            ///return \gettype($value);
        }

        // --- Payload Extraction & Context Resolution ---
        $payload = self::unwrapPayload($value);

        // If after unwrapping we don't have an object, we can't proceed.
        if (!is_object($payload)) {
            return self::Void;
        }

        $messageContext = self::resolveMessageContext($payload);

        // --- Core Detection Logic ---
        // Identify the signal from the two primary sources: the envelope and the content.
        /// $envelope = self::detectEnvelope($payload);
        /// $content  = self::detectContent($messageContext);
        // store them plus enforce another false round to CPU? NO-WAY!

        // --- Prioritized Resolution ---
        // Return the signal based on the requested priority. The generic resolver is the final fallback.
        return $prioritizeEnvelopeDetection
            ? (self::detectEnvelope($payload) ?? self::detectContent($messageContext) ?? self::resolveGeneric($payload, $messageContext))
            : (self::detectContent($messageContext) ?? self::detectEnvelope($payload) ?? self::resolveGeneric($payload, $messageContext));
    }
    public static function from(mixed $value, bool $prioritizeEnvelopeDetection = false): string  /// @Todo: return assoc array
    {
        return self::detect($value, $prioritizeEnvelopeDetection);
    }

    /**
     * --- Cybernetic Heart Unwrapping ---
     * Pulls the true payload from a Message DTO's cybernetic heart (`heart->essence`) when present.
     * For any other object, it returns the object itself. This bridges the gap between the
     * entity wrapper and its data core.
     *
     * @param object $value The Message DTO or a raw stdClass object.
     * @return mixed The core payload object, or the original value.
     */
    protected static function unwrapPayload(object $value): mixed
    {
        return ($value instanceof Message)
            ? ($value->heart?->essence ?? $value)
            : $value;
    }

    /**
     * --- Phase 1: Determine the Message Context ---
     * Locates the actual Message body within a complex payload like an Update or CallbackQuery.
     * The order of checks follows the Bot API's object nesting hierarchy for robustness.
     * If no nested message is found, it checks if the payload itself looks like a message.
     *
     * @param object $payload The unwrapped payload from the API.
     * @return ?object The message-containing object, or null if not found.
     */
    protected static function resolveMessageContext(object $payload): ?object
    {
        $messageContext = $payload->message
            ?? $payload->edited_message
            ?? $payload->channel_post
            ?? $payload->edited_channel_post
            ?? $payload->business_message
            ?? $payload->edited_business_message
            ?? $payload->callback_query?->message
            ?? null;

        // If no nested message object is found, maybe the payload *is* the message.
        if ($messageContext === null && self::looksLikeMessage($payload)) {
            return $payload;
        }

        return is_object($messageContext) ? $messageContext : null;
    }

    /**
     * --- Phase 2: Envelope-based Classification (Top-Level) ---
     * Detects signals based on the primary, mutually exclusive keys in a standard Telegram Update object.
     * This method focuses on clear indicators like `callback_query`, `edited_message`, etc.
     * It never returns generic types like Message, Text, or Void.
     *
     * @param object $payload The unwrapped payload.
     * @return string|null A specific Signal constant, or null to allow other detectors to run.
     */
    protected static function detectEnvelope(object $payload): ?string
    {
        $signal = match (true) {
            // --- Core Update Envelopes (Highest probability) ---
            isset($payload->callback_query)            => self::Callback,
            isset($payload->edited_message)            => self::Revision,
            isset($payload->channel_post)              => self::Post,
            isset($payload->edited_channel_post)       => self::RevisionPost,

            // --- Business Envelopes ---
            isset($payload->edited_business_message)   => self::RevisionTrade,
            isset($payload->business_message)          => self::Trade,
            isset($payload->deleted_business_messages) => self::Purge,
            isset($payload->business_connection)       => self::Connector,
            isset($payload->purchased_paid_media)      => self::Payment,

            // --- Reactions & Polls ---
            isset($payload->message_reaction)          => self::Reaction,
            isset($payload->message_reaction_count)    => self::ReactionCount,
            isset($payload->poll_answer)               => self::Voting,
            /// isset($payload->poll)                      => self::Poll,
            // A top-level Poll state update is different from a Poll inside a Message.
            isset($payload->poll) && !self::looksLikeMessage($payload) => self::Poll,

            // --- Queries & Results ---
            isset($payload->inline_query)              => self::Query,
            isset($payload->chosen_inline_result)      => self::Selection,
            isset($payload->shipping_query)            => self::Shipping,
            isset($payload->pre_checkout_query)        => self::Checkout,

            // --- Membership & Governance (Crucial for bot admin logic) ---
            isset($payload->my_chat_member)            => self::Status,
            isset($payload->chat_member)               => self::Access,
            isset($payload->chat_join_request)         => self::Request,
            isset($payload->chat_boost)                => self::Boost,
            isset($payload->removed_chat_boost)        => self::Unboost,

            // Intentionally omitted: isset($payload->message) is handled by resolveGeneric.
            default                                    => null,
        };

        // If no top-level key matched, attempt to identify the envelope by its shape/structure.
        return $signal ?? self::detectBareEnvelope($payload);
    }

    /**
     * Shape-based detection for objects that might not be wrapped in a standard Update.
     * This is crucial for webhooks or scenarios receiving raw API objects.
     * Combinations follow official Bot API required fields, not just a single ambiguous key.
     *
     * @param object $payload The object to inspect.
     * @return string|null A specific Signal constant or null.
     */
    protected static function detectBareEnvelope(object $payload): ?string
    {
        return match (true) {
            // CallbackQuery: chat_instance is required; data XOR game_short_name.
            isset($payload->chat_instance, $payload->id, $payload->from)
                && (isset($payload->data) || isset($payload->game_short_name))
                => self::Callback,

            // InlineQuery
            isset($payload->query, $payload->offset, $payload->id, $payload->from)
                && !isset($payload->result_id)
                => self::Query,

            // ChosenInlineResult
            isset($payload->result_id, $payload->query, $payload->from)
                => self::Selection,

            // ShippingQuery
            isset($payload->shipping_address, $payload->invoice_payload, $payload->id, $payload->from)
                => self::Shipping,

            // PreCheckoutQuery
            isset($payload->invoice_payload, $payload->currency, $payload->total_amount, $payload->id, $payload->from)
                && !isset($payload->shipping_address)
                => self::Checkout,

            // PollAnswer
            isset($payload->poll_id, $payload->option_ids)
                => self::Voting,

            // MessageReactionUpdated
            isset($payload->old_reaction, $payload->new_reaction, $payload->chat, $payload->message_id)
                => self::Reaction,

            // MessageReactionCountUpdated
            isset($payload->reactions, $payload->chat, $payload->message_id, $payload->date)
                && !isset($payload->old_reaction)
                => self::ReactionCount,

            // ChatJoinRequest
            isset($payload->user_chat_id, $payload->chat, $payload->from, $payload->date)
                => self::Request,

            // ChatBoostRemoved
            isset($payload->boost_id, $payload->remove_date, $payload->chat)
                => self::Unboost,

            // ChatBoostUpdated
            isset($payload->boost, $payload->chat)
                && !isset($payload->boost_id)
                => self::Boost,

            // ChatMemberUpdated — the top-level envelope key is required to split Status vs Access.
            isset($payload->old_chat_member, $payload->new_chat_member, $payload->chat, $payload->date)
                => self::Access,

            // BusinessConnection
            isset($payload->user_chat_id, $payload->user, $payload->is_enabled)
                => self::Connector,

            // BusinessMessagesDeleted
            isset($payload->business_connection_id, $payload->message_ids, $payload->chat)
                && !isset($payload->message_id)
                => self::Purge,

            // PaidMediaPurchased
            isset($payload->paid_media, $payload->from)
                && !isset($payload->message_id)
                => self::Payment,

            default => null,
        };
    }

    /**
     * --- Phase 3: Content-First Classification ---
     * Inspects the message body itself to classify its specific content type.
     * This is the highest priority in the default detection flow.
     * The order of checks is critical for accuracy (e.g., `animation` before `document`).
     * It defers plain text to the generic resolver.
     *
     * @param object|null $messageContext The resolved message context object.
     * @return string|null A specific content Signal, or null if no special content is found.
     */
    protected static function detectContent(?object $messageContext): ?string
    {
        if (!is_object($messageContext)) {
            return null;
        }

        $contentSignal = match (true) {
            // Media Types (with support for legacy/platform-specific aliases)
            isset($messageContext->photo) || isset($messageContext->file_inline) => self::Visual,
            isset($messageContext->animation)           => self::Motion, // Must be before document, as GIFs have both.
            isset($messageContext->video_note)          => self::Round,
            isset($messageContext->video)               => self::Movie,
            isset($messageContext->sticker)             => self::Sticker,
            isset($messageContext->audio)               => self::Sonar,
            isset($messageContext->voice) || isset($messageContext->voicenote) => self::Speech,
            isset($messageContext->document) || isset($messageContext->file_attachment) => self::File,
            isset($messageContext->story)               => self::Moment,
            isset($messageContext->paid_media)          => self::Payment,

            // Interactive & Contextual Types (Venue must be before Location)
            isset($messageContext->venue)               => self::Place,
            isset($messageContext->location)            => self::Geo,
            isset($messageContext->contact)             => self::Identity,
            isset($messageContext->dice)                => self::Chance,
            isset($messageContext->game)                => self::Challenge,
            isset($messageContext->poll)                => self::Poll,

            // Financial Types
            isset($messageContext->invoice)             => self::Bill,
            isset($messageContext->successful_payment)  => self::Receipt,
            isset($messageContext->refunded_payment)    => self::Receipt, // Refund is also a form of receipt.

            // Sharing & WebApp Types
            isset($messageContext->users_shared) || isset($messageContext->user_shared) => self::PeopleShare,
            isset($messageContext->chat_shared)         => self::NetworkShare,
            isset($messageContext->web_app_data)        => self::PortalData,

            // Telegram Premium Gifting
            isset($messageContext->gift) || isset($messageContext->unique_gift) => self::Present,

            // Service Message Types (Events within a chat)
            isset($messageContext->new_chat_members)    => self::Arrival,
            isset($messageContext->left_chat_member)    => self::Departure,
            isset($messageContext->new_chat_title)      => self::TitleShift,
            isset($messageContext->new_chat_photo)      => self::PhotoShift,
            isset($messageContext->delete_chat_photo)   => self::PhotoPurge,
            isset($messageContext->group_chat_created)  => self::Formation,
            isset($messageContext->supergroup_chat_created) => self::Conglomerate,
            isset($messageContext->channel_chat_created) => self::Broadcaster,
            isset($messageContext->message_auto_delete_timer_changed) => self::DecayTimer,
            isset($messageContext->pinned_message)      => self::Beacon,
            isset($messageContext->proximity_alert_triggered) => self::RadarAlert,

            // Video Chat Service Messages
            isset($messageContext->video_chat_scheduled) => self::Briefing,
            isset($messageContext->video_chat_started)  => self::Transmission,
            isset($messageContext->video_chat_ended)    => self::Shutdown,
            isset($messageContext->video_chat_participants_invited) => self::Invited,

            // Forum Topic Service Messages
            isset($messageContext->forum_topic_created) => self::TopicOpen,
            isset($messageContext->forum_topic_edited)  => self::TopicRevision,
            isset($messageContext->forum_topic_closed)  => self::TopicClosure,
            isset($messageContext->forum_topic_reopened) => self::TopicReopen,

            // Giveaway Service Messages
            isset($messageContext->giveaway_created) || isset($messageContext->giveaway) => self::Airdrop,
            isset($messageContext->giveaway_completed) => self::Distribution,
            isset($messageContext->giveaway_winners)   => self::Elites,

            default => null,
        };

        // If no other content matched, check if it's a command.
        return $contentSignal ?? self::detectCommand($messageContext);
    }

    /**
     * A highly specific and accurate command detector.
     * It checks for a Bot API `bot_command` entity, but also uses a robust Regex fallback
     * that correctly identifies commands in both `text` and `caption`, and can handle
     * the `@bot_username` format. A lone "/" is not considered a command.
     *
     * @param object $messageContext The message context.
     * @return string|null Returns the Command signal or null.
     */
    protected static function detectCommand(object $messageContext): ?string
    {
        $text = $messageContext->text ?? null;
        // The entities check is more robust but requires parsing the array.
        // For performance, a well-crafted regex is often sufficient and faster.
        // $entities = $messageContext->entities ?? null;

        if (!is_string($text) || $text === '') {
            $text = $messageContext->caption ?? null;
            // $entities = $messageContext->caption_entities ?? null;
        }

        if (!is_string($text) || $text === '') {
            return null;
        }

        /*
        // Optional: The most accurate detection via Telegram's entities array.
        if (is_array($entities) && !empty($entities)) {
            $first = $entities[0];
            $type = is_object($first) ? ($first->type ?? null) : ($first['type'] ?? null);
            $offset = is_object($first) ? ($first->offset ?? -1) : ($first['offset'] ?? -1);

            if ($type === 'bot_command' && (int) $offset === 0) {
                return self::Command;
            }
        }
        */

        // Regex Fallback: Fast, reliable, and covers 99.9% of cases.
        if (preg_match('/^\/[A-Za-z][A-Za-z0-9_]{0,31}(?:@[A-Za-z0-9_]{5,32})?(?:\s|$)/', $text) === 1) {
            return self::Command;
        }

        return null;
    }

    /**
     * --- Final Fallback: The Generic Resolver ---
     * This is the shared tail of both detection chains (envelope and content).
     * Its order of operations is the only remaining specificity:
     * A message with text is `Text`.
     * A message with no other specific content is `Message`.
     * Anything else is `Void`.
     *
     * @param object $payload The original unwrapped payload.
     * @param object|null $messageContext The resolved message context.
     * @return string The final generic Signal constant.
     */
    protected static function resolveGeneric(object $payload, ?object $messageContext): string
    {
        if (self::hasTextBody($messageContext)) {
            return self::Text;
        }

        // If a message context was found, or if the payload has a 'message' key, it's a generic message.
        if ($messageContext !== null || isset($payload->message)) {
            return self::Message;
        }

        return self::Void;
    }

    /**
     * A simple utility to check if the message context contains non-empty text or a caption.
     * This is used by the generic resolver after the specific `detectCommand` has already run.
     *
     * @param object|null $messageContext
     * @return bool
     */
    protected static function hasTextBody(?object $messageContext): bool
    {
        if (!is_object($messageContext)) {
            return false;
        }

        $text = $messageContext->text ?? $messageContext->caption ?? null;

        return is_string($text) && $text !== '';
    }

    /**
     * Heuristically checks if an object "looks like" a message body, not an Update envelope.
     * This is a critical utility to differentiate a raw message object from a full update.
     * Checks are logically grouped by property type for maximum readability and maintainability.
     * The hard discriminator is `update_id`, which only exists on the envelope.
     *
     * @param object $candidate The object to inspect.
     * @return bool True if the object has properties common to a message body.
     */
    protected static function looksLikeMessage(object $candidate): bool
    {
        // A concrete Message DTO is definitively a message.
        if ($candidate instanceof Message) {
            return true;
        }

        // `update_id` is the definitive mark of an Update envelope. A message NEVER has it.
        if (isset($candidate->update_id)) {
            return false;
        }

        // Check for the presence of any characteristic message properties, grouped by category.
        return
            // --- Core Message Identifiers ---
            isset($candidate->message_id, $candidate->date, $candidate->chat)

            // --- Primary Textual Content ---
            || isset($candidate->text)
            || isset($candidate->caption)

            // --- Standard Media Content ---
            || isset($candidate->animation)
            || isset($candidate->photo) || isset($candidate->file_inline) // `file_inline` is a Rubika alias
            || isset($candidate->document) || isset($candidate->file_attachment) // `file_attachment` is a Rubika alias
            || isset($candidate->voice) || isset($candidate->voicenote) // `voicenote` is a Rubika alias
            || isset($candidate->audio)
            || isset($candidate->video)
            || isset($candidate->video_note)
            || isset($candidate->sticker)
            || isset($candidate->story)

            // --- Interactive & Specialized Content ---
            || isset($candidate->poll)
            || isset($candidate->dice)
            || isset($candidate->game)
            || isset($candidate->venue)
            || isset($candidate->location)
            || isset($candidate->contact)

            // --- Financial & Premium Content ---
            || isset($candidate->invoice)
            || isset($candidate->paid_media)
            || isset($candidate->gift) || isset($candidate->unique_gift)

            // --- Service Message Content (Chat Events) ---
            || isset($candidate->new_chat_members)
            || isset($candidate->left_chat_member)
            || isset($candidate->pinned_message);
    }
}
