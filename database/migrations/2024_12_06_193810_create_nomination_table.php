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
            $table->id('nominee_id');
            $table->string('nominee_name');
            $table->string('nominee_phone');
            $table->string('nominee_email');
            $table->string('status');
            $table->date('status_date');
            $table->string('reason');
            $table->foreignId('election_id');
            $table->foreignId('candidate_id');
            $table->foreignId('org_id');
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
