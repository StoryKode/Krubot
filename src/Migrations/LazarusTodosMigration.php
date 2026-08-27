<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    /**
     * Table name resolved from package config with a safe fallback.
    */
    protected function tableName(): string
    {
        return (string) config('krubot.lazarus.todo_table_name', 'lazarus_todos');
    }

    public function up(): void
    {
        Schema::create($this->tableName(), function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->timestamp('due_at', 6); // TIMESTAMP MaxVal is 2038-01-19, NotEnough ?! For No 2038 ceiling, use `dateTime` instead.
                $table->text('payload');        // what:can be Closure & how:can be array-params||Closure
                $table->tinyInteger('status')->default(0);
                
                // Composite index aligned with the most common lookup: pending jobs due now
                $table->index(['status', 'due_at']);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName());
    }
};
