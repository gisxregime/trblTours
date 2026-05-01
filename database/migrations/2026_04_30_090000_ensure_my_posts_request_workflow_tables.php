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
        if (Schema::hasTable('tourist_requests')) {
            Schema::table('tourist_requests', function (Blueprint $table): void {
                if (! Schema::hasColumn('tourist_requests', 'selected_guide_id')) {
                    $table->foreignId('selected_guide_id')->nullable()->after('tourist_id')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('tourist_requests', 'selected_comment_id')) {
                    $table->unsignedBigInteger('selected_comment_id')->nullable()->after('selected_guide_id');
                }

                if (! Schema::hasColumn('tourist_requests', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('status');
                }
            });
        }

        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table): void {
                if (! Schema::hasColumn('comments', 'tourist_request_id')) {
                    $table->foreignId('tourist_request_id')->nullable()->after('id')->constrained('tourist_requests')->cascadeOnDelete();
                }

                if (! Schema::hasColumn('comments', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('tourist_request_id')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('comments', 'body')) {
                    $table->text('body')->nullable()->after('user_id');
                }

                if (! Schema::hasColumn('comments', 'offer_price')) {
                    $table->decimal('offer_price', 10, 2)->nullable()->after('body');
                }

                if (! Schema::hasColumn('comments', 'parent_id')) {
                    $table->unsignedBigInteger('parent_id')->nullable()->after('offer_price');
                }

                if (! Schema::hasColumn('comments', 'is_reported')) {
                    $table->boolean('is_reported')->default(false)->after('parent_id');
                }

                if (! Schema::hasColumn('comments', 'report_reason')) {
                    $table->string('report_reason')->nullable()->after('is_reported');
                }

                if (! Schema::hasColumn('comments', 'reported_at')) {
                    $table->timestamp('reported_at')->nullable()->after('report_reason');
                }

                if (Schema::hasColumn('comments', 'tourist_request_id')) {
                    $table->index(['tourist_request_id', 'created_at'], 'comments_request_created_idx');
                }

                if (Schema::hasColumn('comments', 'parent_id')) {
                    $table->index('parent_id', 'comments_parent_idx');
                }
            });
        }

        if (Schema::hasTable('conversations')) {
            Schema::table('conversations', function (Blueprint $table): void {
                if (! Schema::hasColumn('conversations', 'tourist_request_id')) {
                    $table->foreignId('tourist_request_id')->nullable()->after('tour_id')->constrained('tourist_requests')->nullOnDelete();
                }

                if (Schema::hasColumn('conversations', 'tourist_request_id')) {
                    $table->index('tourist_request_id', 'conversations_request_idx');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('conversations')) {
            Schema::table('conversations', function (Blueprint $table): void {
                if (Schema::hasColumn('conversations', 'tourist_request_id')) {
                    $table->dropIndex('conversations_request_idx');
                    $table->dropConstrainedForeignId('tourist_request_id');
                }
            });
        }

        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table): void {
                if (Schema::hasColumn('comments', 'tourist_request_id')) {
                    $table->dropIndex('comments_request_created_idx');
                }

                if (Schema::hasColumn('comments', 'parent_id')) {
                    $table->dropIndex('comments_parent_idx');
                }

                if (Schema::hasColumn('comments', 'reported_at')) {
                    $table->dropColumn('reported_at');
                }

                if (Schema::hasColumn('comments', 'report_reason')) {
                    $table->dropColumn('report_reason');
                }

                if (Schema::hasColumn('comments', 'is_reported')) {
                    $table->dropColumn('is_reported');
                }

                if (Schema::hasColumn('comments', 'parent_id')) {
                    $table->dropColumn('parent_id');
                }

                if (Schema::hasColumn('comments', 'offer_price')) {
                    $table->dropColumn('offer_price');
                }

                if (Schema::hasColumn('comments', 'body')) {
                    $table->dropColumn('body');
                }

                if (Schema::hasColumn('comments', 'user_id')) {
                    $table->dropConstrainedForeignId('user_id');
                }

                if (Schema::hasColumn('comments', 'tourist_request_id')) {
                    $table->dropConstrainedForeignId('tourist_request_id');
                }
            });
        }

        if (Schema::hasTable('tourist_requests')) {
            Schema::table('tourist_requests', function (Blueprint $table): void {
                if (Schema::hasColumn('tourist_requests', 'completed_at')) {
                    $table->dropColumn('completed_at');
                }

                if (Schema::hasColumn('tourist_requests', 'selected_comment_id')) {
                    $table->dropColumn('selected_comment_id');
                }

                if (Schema::hasColumn('tourist_requests', 'selected_guide_id')) {
                    $table->dropConstrainedForeignId('selected_guide_id');
                }
            });
        }
    }
};
