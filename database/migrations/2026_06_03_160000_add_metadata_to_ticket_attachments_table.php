<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_attachments', function (Blueprint $table) {
            if (! Schema::hasColumn('ticket_attachments', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('file_type');
            }

            if (! Schema::hasColumn('ticket_attachments', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ticket_attachments', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_attachments', 'file_size')) {
                $table->dropColumn('file_size');
            }

            if (Schema::hasColumn('ticket_attachments', 'mime_type')) {
                $table->dropColumn('mime_type');
            }
        });
    }
};
