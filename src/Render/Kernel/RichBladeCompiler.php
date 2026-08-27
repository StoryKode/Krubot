<?php

namespace KrubiK\Render\Kernel;
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

use Illuminate\View\Compilers\BladeCompiler;

/** 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class RichBladeCompiler extends BladeCompiler
{
    /**
     * Override the main compile method to implement our auto-capture logic.
     *
     * @param  string|null  $path
     * @return void
     */
    public function compile($path = null): void
    {
        $realPath = $path ?? $this->getPath();

        // Step 1: Read the sacred scrolls (the config file)
        $dataGenExts = config(BladeCipher::ConfigPrefix . 'data_generation.extensions', ['.r.blade.php']);
        $variableName = config(BladeCipher::ConfigPrefix . 'data_generation.variable_name', 'cipherElements');
        $autoRenderExts = config(BladeCipher::ConfigPrefix . 'auto_render.extensions', ['.rtale.blade.php']);

        // A helper function to check if the path matches any of the configured extensions
        $pathEndsWithAny = function (string $haystack, array $needles): bool {
            foreach ($needles as $needle) {
                if (str_ends_with($haystack, $needle)) {
                    return true;
                }
            }
            return false;
        };

        // Step 2: Execute commands based on the configuration

        // COMMAND 1: If the path matches any "Data Generation" extension...        
        // COMMAND : If the file is a pure soul file (`.r.blade.php`),
        // we automatically wrap its entire compiled output in our builder logic.
        if ($realPath && $pathEndsWithAny($realPath, $dataGenExts)) {

            // First, get the standard compiled PHP string from the parent compiler.
            $standardContent = parent::compileString($this->files->get($realPath));

            // ...wrap it, using the VARIABLE NAME defined in the config!
            // Now, wrap it with the begin() and end() calls from our BladeCipher.
            // The final array of entities will be available in the VARIABLE NAME defined in the config!
            $wrappedContent = '<?php ' . self::BUILDER_CLASS . '::stream()->begin(); ?>'
                            . $standardContent
                            . "<?php $" . $variableName . ' = ' . self::BUILDER_CLASS . '::stream()->end(); ?>'; // OBEDIENT!

            $this->save($realPath, $wrappedContent);
        }
         // [!!! THE NEW FEATURE !!!]
        // COMMAND 2: If the path matches any "Auto Render" extension...
        // The "Auto-Rendered Story" convention: directly echoes the output
        elseif ($realPath && $pathEndsWithAny($realPath, $autoRenderExts)) {
            $standardContent = parent::compileString($this->files->get($realPath));

            // ...wrap it with the auto-echo logic.
            $wrappedContent = '<?php ' . self::BUILDER_CLASS . '::stream()->begin(); ?>'
                            . $standardContent
                            . '<?php echo ' . self::BUILDER_CLASS . '::stream()->end(); ?>';

            $this->save($realPath, $wrappedContent);
        }
        // COMMAND 3: For all other files, remain a humble servant.
        else {

            // For all other files (.blade.php, .rich.blade.php), behave as normal.
            parent::compile($path);

        }
    }

    // Save the newly forged, powerful content to the compiled file path.
    private function save(string $path, string $content): void
    {
        $this->ensureCompiledDirectoryExists($this->getCompiledPath($path));
        $this->files->put($this->getCompiledPath($path), $content);
    }
}
