<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prompts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('prompt_text');
            $table->text('description')->nullable();
            $table->string('language')->default('en');
            $table->string('tone')->nullable();
            $table->string('usage_type')->nullable();
            $table->boolean('is_template')->default(false);
            $table->json('variables_schema')->nullable();
            $table->json('example_input')->nullable();
            $table->longText('example_output')->nullable();
            $table->string('source')->nullable();
            $table->string('visibility')->default('private');
            $table->boolean('is_favorite')->default(false);
            $table->string('status')->default('draft');
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};
