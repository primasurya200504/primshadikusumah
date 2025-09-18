<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('submission_number');
            $table->string('data_type');
            $table->string('status');
            $table->text('admin_notes')->nullable();
            $table->string('cover_letter_path')->nullable();
            $table->string('final_document_path')->nullable();
            $table->timestamp('archived_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('archives');
    }
};
