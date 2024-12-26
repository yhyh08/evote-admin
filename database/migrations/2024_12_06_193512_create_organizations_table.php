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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id('org_id');
            $table->string('org_name');
            $table->string('org_desc');
            $table->string('org_cat');
            $table->string('org_address');
            $table->string('org_website');
            $table->string('org_email');
            $table->string('org_size');
            $table->string('org_img')->nullable();
            $table->string('pic_name');
            $table->string('pic_phone');
            $table->string('pic_email');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
