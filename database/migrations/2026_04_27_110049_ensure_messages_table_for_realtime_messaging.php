<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->text('body');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['conversation_id', 'created_at']);
                $table->index(['conversation_id', 'read_at']);
            });

            return;
        }

        $needsBody = ! Schema::hasColumn('messages', 'body');
        $needsReadAt = ! Schema::hasColumn('messages', 'read_at');

        if ($needsBody || $needsReadAt) {
            Schema::table('messages', function (Blueprint $table) use ($needsBody, $needsReadAt): void {
                if ($needsBody) {
                    $table->text('body')->nullable()->after('sender_id');
                }

                if ($needsReadAt) {
                    $table->timestamp('read_at')->nullable()->after('body');
                }
            });
        }

        if (Schema::hasColumn('messages', 'message') && Schema::hasColumn('messages', 'body')) {
            DB::statement('UPDATE messages SET body = message WHERE body IS NULL');
        }

        if (Schema::hasColumn('messages', 'is_read') && Schema::hasColumn('messages', 'read_at')) {
            DB::statement('UPDATE messages SET read_at = COALESCE(updated_at, created_at) WHERE is_read = 1 AND read_at IS NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank because this migration only ensures compatibility.
    }
};
