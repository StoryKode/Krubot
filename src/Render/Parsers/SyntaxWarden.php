<?php

namespace KrubiK\Render\Parsers;

use KrubiK\Render\RichElements\RichEntity;

/**
 * The SyntaxWarden Pact. 🛡️
 * The sacred contract for all parsers.
 * 
 * This is the sacred title and contract for any artisan summoned by the Parsentinel.
 * A Warden is not merely a parser; they are the sworn guardian of syntactical integrity.
 * They stand as the final authority on the rules of scripture.
 * 
 * Any class that deconstructs a string into our holy entities MUST adhere to this pact.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
interface SyntaxWarden
{
    /**
     * Transforms a raw string into an array of pure RichEntity objects.
     * This is the Warden's primary duty, performed with unwavering precision.
     *
     * @param string $input The raw content to deciphered.
     * @return RichEntity[] The resulting, processed artifact.
     */
    public function decipher(string $input): array;
}
