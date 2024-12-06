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
            $table->id('id');
            $table->string('category');
            $table->string('nationality');
            $table->string('religion');
            $table->string('job');
            $table->decimal('income', 10, 2);
            $table->string('marriage_status');
            $table->string('status');
            $table->date('receive_date');
            $table->date('approve_date');
            $table->string('sign');
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
