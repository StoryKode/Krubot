<?php

namespace KrubiK\DTOs;
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

use RubikaBot\Message as BaseMessage;
use KrubiK\DTOs\UniversalInboundUpdate;
use KrubiK\WebApps\DTOs\WebRequest; // ⚡ Our Sacred WebRequest HyperDTO

/**
 * The Message Entity. The Cybernetic Body. The Citadel Guardian.
 * This class is the vessel that receives the data stream from the Nexus.
 */
class Message extends BaseMessage // we use VanGuard-Rubika Message Entity cause it's vast enough to support other messangers, however it's not the bestway possible. for now we just leave it until Revolution.
{
	/**
     * Holds data received from a Web App interaction.
     * This property is populated by a platform driver when a user sends data
     * back to the bot from an opened Web App (Like Telegram Apps).
     * It is null if the message does not contain Web App data.
     *
     * @var array{
     *      data: string,
     *      button_text?: string,
     *      _raw?: array
     * }|null
     */
    public ?array $web_app_data = null;

    /**
     * Holds the WebRequest DTO if this Message was originated from a WebAction.
     * This is the bridge between the HTTP world and the Bot processing world.
    */
    public ?WebRequest $web_request = null; // ✨ THE BRIDGE ✨

    /**
     * ✨ THE HEART ✨
     * The Cybernetic, living core of this message. It's the pure, unified DTO
     * representing the absolute source of truth from the Request.
     * The Message entity is the body 🏭️; This is its Heart, its Reactor-Core ☢️💟.
     *
     * @var UniversalInboundUpdate|null
     */
    public ?UniversalInboundUpdate $heart = null;

    /**
     * ✨ THE CORONATION CYBERNETIC CEREMONY ✨
     * 
     * Initiates the data stream from the Payload DTO to transplant the Message's Cybernetic Heart
     * This sacred ritual brings the entity to life with rich, structured, and
     * divine data, making it worthy of dwelling within the Cyber Citadel.
     * This is the moment of awakening, where the entity gains consciousness
     * and becomes operational.
     *
     *
     * @param UniversalInboundUpdate $dto The living heart / data stream, the absolute source of truth.
     * @return self Returns the now-living fully conscious instance for command chaining.
     */
    public function uplinkHeart(UniversalInboundUpdate $dto): self
    {
        // 1. Establishing the uplink... Heart is now online.
        $this->heart = $dto;

        // 2. The body's limbs and senses are awakened by the heart's power.
        if ($dto->webAppData !== null) {
            $this->web_app_data = $dto->webAppData;
        }

        // ... any other properties that draw life directly from the heart ...
        
        // 3. The entity is fully conscious and operational, Reborn and Returns, and Ready for command.
        return $this;
    }

	/**
	 * Build legacy Message from normalized UniversalInboundUpdate DTO.
	 * Backward-compatible bridge for queue-driven pipeline.
	*/
	public static function fromInboundPayload(UniversalInboundUpdate $dto): self
	{
		$instance = null;

	    // Build a constructor-compatible shape expected by __construct(array $updateData)
	    // We intentionally keep this compact for performance and compatibility.
	    if ($dto->source === 'inline') {
	        $payload = [
	            'inline_message' => array_merge(
	                $dto->effectiveData,
	                [
	                    // Ensure required fallback keys exist
	                    'type' => $dto->type,
	                    'chat_id' => $dto->chatId,
	                    'sender_id' => $dto->senderId,
	                    'text' => $dto->text,
	                    'message_id' => $dto->messageId,
	                    'aux_data' => array_merge(
	                        $dto->effectiveData['aux_data'] ?? [],
	                        ['button_id' => $dto->auxData['button_id'] ?? null]
	                    ),
	                ]
	            ),
	        ];

	        $instance = new self($payload);

	    }
	    else {

		    // update / fallback path
		    $newMessage = array_merge(
		        $dto->effectiveData,
		        [
		            'sender_id' => $dto->senderId,
		            'text' => $dto->text,
		            'message_id' => $dto->messageId,
		            'time' => $dto->timestamp,
		        ]
		    );

		    // Propagate optional button_id if present (for action routing)
		    if (!isset($newMessage['aux_data']) || !is_array($newMessage['aux_data'])) {
		        $newMessage['aux_data'] = [];
		    }
		    if (!isset($newMessage['aux_data']['button_id']) && isset($dto->auxData['button_id'])) {
		        $newMessage['aux_data']['button_id'] = $dto->auxData['button_id'];
		    }

		    $payload = [
		        'update' => [
		            'type' => $dto->type,
		            'chat_id' => $dto->chatId,
		            'new_message' => $newMessage,
		        ],
		        // Keep compatibility with constructor fallback read:
		        'chat_id' => $dto->chatId,
		    ];

		    $instance = new self($payload);
		}

	    // ✨ THE BRIDGE: Transfer Web App Data from DTO to Message Entity ✨

		$instance->uplinkHeart($dto); // The uplink is established.

        // This happens after the instance is created, ensuring full compatibility
        // with the parent constructor, regardless of its implementation.

        return $instance;
	}
}
