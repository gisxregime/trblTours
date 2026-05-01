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
        if (! Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tourist_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('guide_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('tour_id')->nullable()->constrained('tours')->nullOnDelete();
                $table->timestamp('last_message_at')->nullable();
                $table->timestamps();

                $table->unique(['tourist_id', 'guide_id', 'tour_id']);
                $table->index(['guide_id', 'last_message_at']);
                $table->index(['tourist_id', 'last_message_at']);
            });

            return;
        }

        $needsTouristId = ! Schema::hasColumn('conversations', 'tourist_id');
        $needsGuideId = ! Schema::hasColumn('conversations', 'guide_id');
        $needsTourId = ! Schema::hasColumn('conversations', 'tour_id');
        $needsLastMessageAt = ! Schema::hasColumn('conversations', 'last_message_at');

        if (! $needsTouristId && ! $needsGuideId && ! $needsTourId && ! $needsLastMessageAt) {
            return;
        }

        Schema::table('conversations', function (Blueprint $table) use ($needsTouristId, $needsGuideId, $needsTourId, $needsLastMessageAt): void {
            if ($needsTouristId) {
                $table->foreignId('tourist_id')->nullable()->constrained('users')->cascadeOnDelete();
            }

            if ($needsGuideId) {
                $table->foreignId('guide_id')->nullable()->constrained('users')->cascadeOnDelete();
            }

            if ($needsTourId) {
                $table->foreignId('tour_id')->nullable()->constrained('tours')->nullOnDelete();
            }

            if ($needsLastMessageAt) {
                $table->timestamp('last_message_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank because this migration only ensures compatibility.
    }
};
