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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id('candidate_id');
            $table->string('candidate_name');
            $table->string('candidate_image');
            $table->string('candidate_phone');
            $table->string('candidate_email');
            $table->string('candidate_gender');
            $table->string('candidate_ic');
            $table->string('candidate_dob');
            $table->string('candidate_address');
            $table->string('nationality');
            $table->string('religion');
            $table->string('job');
            $table->string('income');
            $table->string('marriage_status');
            $table->string('position');
            $table->string('short_biography',1000);
            $table->string('manifesto',1000);
            $table->string('status');
            $table->string('reason',500);
            $table->string('sign');
            $table->integer('votes_count')->default(0);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('election_id');
            $table->json('nominee_id');
            $table->json('cand_doc_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
