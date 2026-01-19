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
        Schema::create('comments', function (Blueprint $table) {
            $table->id(); 
            $table->text('message')->nullable();
            $table->foreignId('thread_id')->constrained('threads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Threading (nested replies)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('comments')
                ->nullOnDelete();

            $table->unsignedTinyInteger('depth')->default(0);     // 0 = direct reply to thread

           

            // Engagement
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('replies_count')->default(0); // only if you want to cache sub-replies

            $table->timestamps();
            $table->softDeletes();

            // Important indexes for performance
            $table->index(['thread_id', 'parent_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
