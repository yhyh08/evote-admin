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
            $table->decimal('income', 10, 2);
            $table->string('marriage_status');
            $table->string('position');
            $table->string('short_biography');
            $table->string('manifesto');
            $table->string('status');
            $table->string('reason');
            $table->date('receive_date');
            $table->date('approve_date');
            $table->string('sign');
            $table->integer('votes_count')->default(0);
            $table->foreignId('election_id')->onDelete('cascade');
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
