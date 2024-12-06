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
        Schema::create('candidate_docs', function (Blueprint $table) {
            $table->id('cand_doc_id');
            $table->string('title');
            $table->text('description');
            $table->string('uploadFile');
            $table->foreignId('candidate_id')->constrained('candidates');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_docs');
    }
};
