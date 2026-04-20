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
        if (Schema::hasTable('tourists_profile')) {
            Schema::table('tourists_profile', function (Blueprint $table): void {
                if (! Schema::hasColumn('tourists_profile', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }

                if (! Schema::hasColumn('tourists_profile', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        if (Schema::hasTable('tour_guides_profile')) {
            Schema::table('tour_guides_profile', function (Blueprint $table): void {
                if (! Schema::hasColumn('tour_guides_profile', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }

                if (! Schema::hasColumn('tour_guides_profile', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tourists_profile')) {
            Schema::table('tourists_profile', function (Blueprint $table): void {
                $dropColumns = [];

                if (Schema::hasColumn('tourists_profile', 'created_at')) {
                    $dropColumns[] = 'created_at';
                }

                if (Schema::hasColumn('tourists_profile', 'updated_at')) {
                    $dropColumns[] = 'updated_at';
                }

                if ($dropColumns !== []) {
                    $table->dropColumn($dropColumns);
                }
            });
        }

        if (Schema::hasTable('tour_guides_profile')) {
            Schema::table('tour_guides_profile', function (Blueprint $table): void {
                $dropColumns = [];

                if (Schema::hasColumn('tour_guides_profile', 'created_at')) {
                    $dropColumns[] = 'created_at';
                }

                if (Schema::hasColumn('tour_guides_profile', 'updated_at')) {
                    $dropColumns[] = 'updated_at';
                }

                if ($dropColumns !== []) {
                    $table->dropColumn($dropColumns);
                }
            });
        }
    }
};
