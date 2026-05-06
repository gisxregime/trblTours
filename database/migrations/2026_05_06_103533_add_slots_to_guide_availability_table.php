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
        if (! Schema::hasTable('guide_availability') || Schema::hasColumn('guide_availability', 'slots')) {
            return;
        }

        Schema::table('guide_availability', function (Blueprint $table) {
            $table->unsignedInteger('slots')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('guide_availability') || ! Schema::hasColumn('guide_availability', 'slots')) {
            return;
        }

        Schema::table('guide_availability', function (Blueprint $table) {
            $table->dropColumn('slots');
        });
    }
};
