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
        if (! Schema::hasTable('tours') || Schema::hasColumn('tours', 'custom_inclusions')) {
            return;
        }

        Schema::table('tours', function (Blueprint $table): void {
            $table->json('custom_inclusions')->nullable()->after('inclusions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('tours') || ! Schema::hasColumn('tours', 'custom_inclusions')) {
            return;
        }

        Schema::table('tours', function (Blueprint $table): void {
            $table->dropColumn('custom_inclusions');
        });
    }
};
