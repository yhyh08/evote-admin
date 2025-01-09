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
        Schema::create('elections', function (Blueprint $table) {
            $table->id('election_id');
            $table->string('election_topic');
            $table->string('type');
            $table->json('position');
            $table->text('description');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('nominate_period_start');
            $table->string('nominate_period_end');
            $table->date('result_release_date')->nullable()->change();
            $table->string('status');
            $table->foreignId('org_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elections');
    }
};
