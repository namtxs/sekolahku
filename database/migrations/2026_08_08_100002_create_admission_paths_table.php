<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // mis. "Zonasi", "Prestasi", "Afirmasi"
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('quota');
            $table->text('requirements')->nullable(); // syarat pendaftaran (HTML)
            $table->dateTime('opens_at');
            $table->dateTime('closes_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_paths');
    }
};
