<?php

namespace KrubiK\Console;
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

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use KrubiK\Krubot;

class MakeMigrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'krubik:make-migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '⚡ Generate a high-performance, strictly-typed migration for Multiverse.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('Initiating Hyper-DX Strictly-Typed Migration Generator...');

        $map = config('krubot.multiverse_map', []);
        $schema = config('krubot.multiverse_schema', []);

        if (empty($map)) {
            $this->components->error('Multiverse map is empty. Please configure first.');
            return self::FAILURE;
        }

        // Memory pre-allocation for nanosecond string concatenation
        $upColumnsStr = '';
        $downColumnsArray = [];

        foreach ($map as $platform => $fields) {
            $upColumnsStr .= "\n            // --- {$platform} Multiverse Columns ---";

            foreach (['chat', 'sender', 'state'] as $fieldType) {
                if (!isset($fields[$fieldType])) continue;

                $colName = $fields[$fieldType];
                // Default fallback to string:255 if schema is not defined
                $colTypeRule = $schema[$platform][$fieldType] ?? 'string:255';
                
                $upColumnsStr .= "\n            " . $this->resolveSchemaMethod($colName, $colTypeRule, $platform, $fieldType);
                $downColumnsArray[] = "'{$colName}'";
            }
            $upColumnsStr .= "\n";
        }

        $downColumnsStr = "\n            \$table->dropColumn([" . implode(', ', $downColumnsArray) . "]);\n";

        // Assuming stub is in the same directory structure mentioned previously
        $stubPath = __DIR__ . '/Stubs/add_multiverse_columns.stub';
        
        if (!File::exists($stubPath)) {
            $this->components->error("Stub file missing at: {$stubPath}");
            return self::FAILURE;
        }

        // Nanosecond fast replacement
        $migrationContent = str_replace(
            ['{{ up_columns }}', '{{ down_columns }}'],
            [$upColumnsStr, $downColumnsStr],
            File::get($stubPath)
        );

        $timestamp = date('Y_m_d_His');
        $fileName = "{$timestamp}_add_multiverse_columns_to_users_table.php";
        $destinationPath = database_path("migrations/{$fileName}");

        File::put($destinationPath, $migrationContent);

        $this->components->info("✨ KrubiK Migrations successfully generated with accurate data types!");
        $this->line('File saved at: <options=bold>' . $destinationPath . '</>');

        return self::SUCCESS;
    }

    /**
     * Resolves the Laravel Blueprint method based on type definition.
     * Engineered for nanosecond execution and precise memory allocation.
     * 
     * @param string $colName
     * @param string $colTypeRule e.g., 'string:50', 'bigInteger', 'tinyint'
     * @param string $platform
     * @param string $fieldType
     * @return string
     */
    private function resolveSchemaMethod(string $colName, string $colTypeRule, string $platform, string $fieldType): string
    {
        $blueprintCode = '$table->';

        if (Str::startsWith($colTypeRule, 'string')) {
            $length = Str::contains($colTypeRule, ':') ? explode(':', $colTypeRule)[1] : 255;
            $blueprintCode .= "string('{$colName}', {$length})";
            
        } elseif ($colTypeRule === 'bigInteger') {
            // Negative IDs allowed for Telegram/Bale Channels
            $blueprintCode .= "bigInteger('{$colName}')";
            
        } elseif ($colTypeRule === 'unsignedBigInteger') {
            $blueprintCode .= "unsignedBigInteger('{$colName}')";
            
        } elseif ($colTypeRule === 'tinyint') {
            // Hyper-DX State Machine Optimization (1 Byte)
            // unsignedTinyInteger corresponds to TINYINT(3) UNSIGNED in MySQL (Max val 255).
            // Default 0 is injected for instant initialization.
            $blueprintCode .= "unsignedTinyInteger('{$colName}')->default(0)";
            
        } else {
            // Safety fallback
            $blueprintCode .= "string('{$colName}')";
        }
        /// if ($colTypeRule != 'tinyint')
        $blueprintCode .= '->nullable()';

        // Apply strict indexing rules for peak query performance
        if ($fieldType === 'chat' || $fieldType === 'sender') {
            // Using unique() instead of index() since a user mapping should be 1-to-1 internally
            $blueprintCode .= "->unique()";
        } elseif ($fieldType === 'state') {
            // States are highly queried, Indexing is mandatory for performance
            $blueprintCode .= "->index()";
        }

        $blueprintCode .= "->comment('KrubiK\'s " . ucfirst($platform) . " " . ucfirst($fieldType) . "');";

        return $blueprintCode;
    }
}
