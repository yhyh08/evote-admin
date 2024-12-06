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
        Schema::create('election_committees', function (Blueprint $table) {
            $table->id('com_id');
            $table->string('com_name');
            $table->string('com_phone');
            $table->string('com_email');
            $table->string('com_sign');
            $table->boolean('isApprove');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_committee');
    }
};
