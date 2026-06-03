<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_suggestions', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_suggestions', 'context_hash')) {
                $table->string('context_hash', 64)->nullable()->after('suggested_language');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ai_suggestions', function (Blueprint $table) {
            if (Schema::hasColumn('ai_suggestions', 'context_hash')) {
                $table->dropColumn('context_hash');
            }
        });
    }
};
