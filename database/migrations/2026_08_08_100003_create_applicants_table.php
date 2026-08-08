<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique(); // "PPDB-2026-0001"
            $table->foreignId('admission_path_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('nisn', 10); // unik per tahun ajaran, divalidasi di aplikasi
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place');
            $table->date('birth_date');
            $table->string('religion');
            $table->text('address');
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->string('school_origin');
            $table->string('parent_name');
            $table->string('parent_phone', 30);
            $table->decimal('avg_score', 5, 2)->nullable(); // rata-rata nilai rapor (jalur prestasi)
            $table->string('status')->default('pending')->index(); // pending → verified → accepted/rejected
            $table->text('verification_note')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->dateTime('decided_at')->nullable();
            $table->timestamps();

            $table->index('nisn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
