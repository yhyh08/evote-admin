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
        Schema::create('nominations', function (Blueprint $table) {
            $table->id();
            $table->string('religion');
            $table->string('job');
            $table->string('status');
            $table->date('status_date');
            $table->string('reason');
            $table->string('sign');
            $table->foreignId('candidate_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomination');
    }
};
