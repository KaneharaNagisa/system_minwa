<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipality_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('title');
            $table->string('title_kana')->nullable();
            $table->text('summary')->nullable();
            $table->longText('body_markdown');
            $table->string('main_image_path')->nullable();
            $table->string('spot_name')->nullable();
            $table->text('spot_address')->nullable();
            $table->text('spot_map_url')->nullable();
            $table->text('source')->nullable();
            $table->boolean('source_citation_checked')->default(false);
            $table->boolean('expression_rewritten_checked')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
