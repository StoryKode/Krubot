<?php

namespace KrubiK\Render\Arcane;
/*
| Krubot BotEngine: The Architect's Lexicon [×RC.8×] 🚀📜
|--------------------------------------------------------------------------
| This is **a Playground For Mastery**, a laboratory of ***Software Dev Artistry***;
| not a weapon for production's final battles.
|
| Our Bond: ***"Rebuilding The Rebellion"*** Within S.N.P. (The Foundation of Pure Power & Revel).
| Your Mandate [MIT]: Deconstruct Krubot. Command it. Master it. You are The Architect Now!
|
| *Go build something revolutionary!* 💜⚡️
*/

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * Trait HasHyperIntrospection
 *
 * Provides a "hyper-visual" introspection layer for objects.
 * This trait imposes an abstract contract, requiring the consuming class to implement
 * a `toArray(): array` method. In return, it bestows the `toHtml()` method, which
 * renders the object's state into a rich, interactive, and self-contained HTML document.
 * This pattern ensures compile-time safety, guaranteeing that any class using this trait
 * can fulfill its core dependency, thereby creating more robust and predictable code.
 */
trait HasHyperIntrospection
{
    /**
     * Abstract Contract: Defines the essential requirement for any class using this trait.
     *
     * The consuming class MUST provide a public method named `toArray` that returns an array.
     * This is the "essence" or "state" that the `toHtml()` method will visualize.
     * By declaring this as abstract, we leverage the PHP engine to enforce this contract
     * at compile time, preventing runtime errors.
     *
     * @return array The object's state represented as an array.
    */
    public abstract function toArray(): array;

    /**
     * Renders the object's array representation into a self-contained, interactive HTML document.
     *
     * This method orchestrates the cloning and dumping process without halting script execution,
     * capturing the generated HTML output in-memory. The result is a complete HTML5 page,
     * including all necessary CSS and JavaScript, ready to be displayed in a browser,
     * saved to a file, or returned in an API response during development.
     * It relies on the concrete implementation of `toArray()` from the consuming class.
     *
     * @return string A fully-formed HTML5 document containing the interactive dump.
    */
    public function toHtml(): string
    {
        // Step 1: Fulfill the contract.
        // The call to toArray() is now guaranteed to succeed because of the abstract
        // function declaration above. The PHP engine ensures this method exists
        // before the script even runs.
        $data = $this->toArray();

        // Step 2: Instantiate the core VarDumper components.
        // The VarCloner creates a structured, serializable representation of the variable.
        // The HtmlDumper is responsible for rendering this cloned data into HTML.
        $cloner = new VarCloner();
        $dumper = new HtmlDumper();

        // Step 3: Configure a non-blocking output mechanism.
        // We use an in-memory stream to capture the dumper's output as a string
        // without echoing it directly and halting execution.
        $outputStream = fopen('php://memory', 'r+');
        $dumper->setOutput($outputStream);

        // Step 4: Execute the dump operation.
        // The cloned data is written as an HTML fragment into our in-memory stream.
        $dumper->dump($cloner->cloneVar($data));

        // Step 5: Retrieve the captured output.
        // Rewind the stream, read its contents, and then close the handle.
        rewind($outputStream);
        $htmlOutput = stream_get_contents($outputStream);
        fclose($outputStream);

        // Step 6: Embed the output in a complete HTML document.
        // The captured fragment is wrapped in a valid HTML5 boilerplate that
        // includes its required CSS and JS assets for interactivity.
        $assets = $this->getDumperAssets();
        
        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Object Hyper-Introspection</title>
            {$assets}
        </head>
        <body>
            {$htmlOutput}
        </body>
        </html>
        HTML;
    }

    /**
     * Retrieves the necessary CSS and JavaScript assets for the HtmlDumper output.
     *
     * This method performs a "dummy" dump of a null value into an output buffer
     * and then uses regular expressions to extract the <style> and <script> blocks.
     *
     * @return string A string containing the complete <style> and <script> tags.
    */
    protected function getDumperAssets(): string
    {
        // Instantiate a dumper specifically for asset extraction.
        $dumper = new HtmlDumper();

        // Use output buffering to capture what would normally be echoed.
        ob_start();
        $dumper->dump((new VarCloner())->cloneVar(null));
        $dumpContent = ob_get_clean();

        // Use regex to find and extract the full style and script tags.
        preg_match('/<style(.*?)<\/style>/s', $dumpContent, $styleMatches);
        preg_match('/<script(.*?)<\/script>/s', $dumpContent, $scriptMatches);

        $style = $styleMatches[0] ?? '<!-- Hyper-Introspection Styles not found -->';
        $script = $scriptMatches[0] ?? '<!-- Hyper-Introspection Scripts not found -->';

        return $style . $script;
    }
}
