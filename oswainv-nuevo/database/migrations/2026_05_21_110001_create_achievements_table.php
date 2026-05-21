<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->default('bi-trophy-fill');
            $table->integer('xp_reward')->default(50);
            $table->string('criteria_type'); // stock_entries, stock_exits, products_registered, missions_completed, requisitions_made, login_streak, chat_messages, transfers_made
            $table->integer('criteria_value'); // threshold to unlock
            $table->boolean('hidden')->default(false);
            $table->timestamps();
        });

        Schema::create('user_achievement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained('achievements')->cascadeOnDelete();
            $table->timestamp('unlocked_at')->useCurrent();
            $table->unique(['user_id', 'achievement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_achievement');
        Schema::dropIfExists('achievements');
    }
};
