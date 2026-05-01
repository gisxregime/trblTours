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
        Schema::table('tourist_requests', function (Blueprint $table): void {
            if (! Schema::hasColumn('tourist_requests', 'tourist_id')) {
                $table->unsignedBigInteger('tourist_id')->nullable();
            }

            if (! Schema::hasColumn('tourist_requests', 'title')) {
                $table->string('title')->nullable();
            }

            if (! Schema::hasColumn('tourist_requests', 'location')) {
                $table->string('location')->nullable();
            }

            if (! Schema::hasColumn('tourist_requests', 'preferred_date')) {
                $table->date('preferred_date')->nullable();
            }

            if (! Schema::hasColumn('tourist_requests', 'passenger_count')) {
                $table->unsignedInteger('passenger_count')->nullable();
            }

            if (! Schema::hasColumn('tourist_requests', 'budget_min')) {
                $table->decimal('budget_min', 10, 2)->nullable();
            }

            if (! Schema::hasColumn('tourist_requests', 'budget_max')) {
                $table->decimal('budget_max', 10, 2)->nullable();
            }

            if (! Schema::hasColumn('tourist_requests', 'description')) {
                $table->text('description')->nullable();
            }

            if (! Schema::hasColumn('tourist_requests', 'status')) {
                $table->string('status')->default('open');
            }

            if (! Schema::hasColumn('tourist_requests', 'message')) {
                $table->text('message')->nullable();
            }

            if (! Schema::hasColumn('tourist_requests', 'duration')) {
                $table->string('duration')->nullable();
            }

            if (! Schema::hasColumn('tourist_requests', 'region')) {
                $table->string('region')->nullable();
            }

            if (! Schema::hasColumn('tourist_requests', 'adults')) {
                $table->unsignedTinyInteger('adults')->nullable();
            }

            if (! Schema::hasColumn('tourist_requests', 'children')) {
                $table->unsignedTinyInteger('children')->nullable();
            }

            if (! Schema::hasColumn('tourist_requests', 'interests')) {
                $table->string('interests')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tourist_requests', function (Blueprint $table): void {
            if (Schema::hasColumn('tourist_requests', 'interests')) {
                $table->dropColumn('interests');
            }

            if (Schema::hasColumn('tourist_requests', 'children')) {
                $table->dropColumn('children');
            }

            if (Schema::hasColumn('tourist_requests', 'adults')) {
                $table->dropColumn('adults');
            }

            if (Schema::hasColumn('tourist_requests', 'region')) {
                $table->dropColumn('region');
            }

            if (Schema::hasColumn('tourist_requests', 'duration')) {
                $table->dropColumn('duration');
            }

            if (Schema::hasColumn('tourist_requests', 'message')) {
                $table->dropColumn('message');
            }

            if (Schema::hasColumn('tourist_requests', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('tourist_requests', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('tourist_requests', 'budget_max')) {
                $table->dropColumn('budget_max');
            }

            if (Schema::hasColumn('tourist_requests', 'budget_min')) {
                $table->dropColumn('budget_min');
            }

            if (Schema::hasColumn('tourist_requests', 'passenger_count')) {
                $table->dropColumn('passenger_count');
            }

            if (Schema::hasColumn('tourist_requests', 'preferred_date')) {
                $table->dropColumn('preferred_date');
            }

            if (Schema::hasColumn('tourist_requests', 'location')) {
                $table->dropColumn('location');
            }

            if (Schema::hasColumn('tourist_requests', 'title')) {
                $table->dropColumn('title');
            }

            if (Schema::hasColumn('tourist_requests', 'tourist_id')) {
                $table->dropColumn('tourist_id');
            }
        });
    }
};
