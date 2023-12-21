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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('entity')->nullable();
            $table->string('organ')->nullable();
            $table->string('council')->nullable();
            $table->string('acronym')->nullable();
            $table->string('team')->nullable();
            $table->string('unit')->nullable();
            $table->string('email')->nullable();
            $table->string('office_requested')->nullable();
            $table->string('office_indicated')->nullable();
            $table->string('internal_concierge')->nullable();
            $table->longText('observations')->nullable();
            $table->unsignedBigInteger('creator_user_id');
            $table->unsignedBigInteger('type_group_id');
            $table->unsignedBigInteger('representative_id');

            $table->foreign('creator_user_id')->references('id')->on('users');
            $table->foreign('type_group_id')->references('id')->on('type_groups');
            $table->foreign('representative_id')->references('id')->on('representatives');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
