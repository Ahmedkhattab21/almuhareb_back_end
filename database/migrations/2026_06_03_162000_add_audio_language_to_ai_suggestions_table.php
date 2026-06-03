<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_suggestions', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_suggestions', 'audio_language')) {
                $table->string('audio_language', 10)->nullable()->after('audio_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ai_suggestions', function (Blueprint $table) {
            if (Schema::hasColumn('ai_suggestions', 'audio_language')) {
                $table->dropColumn('audio_language');
            }
        });
    }
};
