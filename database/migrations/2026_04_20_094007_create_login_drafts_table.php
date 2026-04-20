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
        Schema::create('login_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('token', 80)->unique();
            $table->string('email')->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('role')->nullable();
            $table->timestamp('role_selected_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_drafts');
    }
};
