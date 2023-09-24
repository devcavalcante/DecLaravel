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
        Schema::create(
            'users',
            function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->unsignedBigInteger('type_user_id');
                $table->unsignedBigInteger('creator_user_id')->nullable(); // Novo campo para o criador do usuário
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('type_user_id')->references('id')->on('type_users');
                $table->foreign('creator_user_id')->references('id')->on('users');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
