<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->integer('port')->nullable();
            $table->text('instructions')->nullable();
            $table->text('additional_info')->nullable();
            $table->boolean('is_visible_to_customer')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_credentials');
    }
};
