<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->onDelete('cascade');
            
            // Billing info (dari admin)
            $table->string('billing_file_path')->nullable();
            $table->string('billing_filename')->nullable();
            $table->decimal('billing_amount', 15, 2)->nullable();
            $table->text('billing_note')->nullable();
            $table->date('billing_date')->nullable();
            
            // Payment proof (dari user)
            $table->string('payment_proof_path')->nullable();
            $table->string('payment_proof_filename')->nullable();
            $table->date('payment_date')->nullable();
            $table->text('payment_note')->nullable();
            
            // Status tracking
            $table->enum('status', [
                'Menunggu Pembayaran',
                'Dibayar', 
                'Terverifikasi',
                'Selesai',
                'Ditolak'
            ])->default('Menunggu Pembayaran');
            
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->timestamp('uploaded_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran');
    }
};
