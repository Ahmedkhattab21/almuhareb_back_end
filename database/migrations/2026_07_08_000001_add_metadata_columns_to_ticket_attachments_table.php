<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ticket_attachments')) {
            return;
        }

        if (! Schema::hasColumn('ticket_attachments', 'mime_type')) {
            Schema::table('ticket_attachments', function (Blueprint $table) {
                $table->string('mime_type')->nullable()->after('file_type');
            });
        }

        if (! Schema::hasColumn('ticket_attachments', 'file_size')) {
            Schema::table('ticket_attachments', function (Blueprint $table) {
                $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('ticket_attachments')) {
            return;
        }

        if (Schema::hasColumn('ticket_attachments', 'file_size')) {
            Schema::table('ticket_attachments', function (Blueprint $table) {
                $table->dropColumn('file_size');
            });
        }

        if (Schema::hasColumn('ticket_attachments', 'mime_type')) {
            Schema::table('ticket_attachments', function (Blueprint $table) {
                $table->dropColumn('mime_type');
            });
        }
    }
};
