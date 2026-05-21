<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('title_original')->nullable()->after('title');
            $table->string('title_translated')->nullable()->after('title_original');
            $table->string('title_original_language', 10)->nullable()->after('title_translated');
            $table->string('title_translated_language', 10)->nullable()->after('title_original_language');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'title_original',
                'title_translated',
                'title_original_language',
                'title_translated_language',
            ]);
        });
    }
};
