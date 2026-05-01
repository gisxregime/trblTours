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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable()->after('bio');
            }

            if (! Schema::hasColumn('users', 'cover_photo_path')) {
                $table->string('cover_photo_path')->nullable()->after('profile_photo_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('users', 'profile_photo_path')) {
                $columns[] = 'profile_photo_path';
            }

            if (Schema::hasColumn('users', 'cover_photo_path')) {
                $columns[] = 'cover_photo_path';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
