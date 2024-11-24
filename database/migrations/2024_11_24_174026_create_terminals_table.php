<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terminals', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->string('country');
            $table->string('city');
            $table->string('state');
            $table->string('neighborhood');
            $table->string('street');
            $table->string('street_number');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terminals');
    }
};
