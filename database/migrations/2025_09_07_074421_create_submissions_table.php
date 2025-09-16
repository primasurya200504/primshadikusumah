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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('submission_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('data_type');
            $table->string('category')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('purpose');
            $table->string('cover_letter_path')->nullable();
            $table->string('status')->default('Menunggu Verifikasi');
            $table->string('payment_status')->default('Belum Dibayar');
            $table->string('rejection_note')->nullable();
            $table->string('ebilling_path')->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
