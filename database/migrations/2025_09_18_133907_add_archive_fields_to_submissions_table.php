<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('payment_status');
            $table->timestamp('archived_at')->nullable()->after('is_archived');
            $table->string('final_document_path')->nullable()->after('archived_at');
            $table->text('admin_notes')->nullable()->after('final_document_path');
        });
    }

    public function down()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['is_archived', 'archived_at', 'final_document_path', 'admin_notes']);
        });
    }
};
