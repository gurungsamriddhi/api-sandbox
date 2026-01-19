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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->boolean('password_set')->default(false); // to check if password is set
            $table->timestamp('last_login_at')->nullable();


            //otp columns
            $table->string('otp_hash')->nullable();
            $table->timestamp('otp_created_at')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->index('otp_hash');

            //Social login
            $table->string('social_provider')->nullable();
            $table->string('social_id')->nullable();

            // 4. Performance Indexes
            // This makes searching for a user by their Social ID lightning fast
            $table->unique(['social_provider', 'social_id']);

            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
