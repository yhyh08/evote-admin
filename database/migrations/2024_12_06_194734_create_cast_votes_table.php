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
        Schema::create('cast_votes', function (Blueprint $table) {
            $table->id('ballot_id');
            $table->boolean('isValid');
            $table->string('status');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('election_id')->constrained('elections');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cast_votes');
    }
};
