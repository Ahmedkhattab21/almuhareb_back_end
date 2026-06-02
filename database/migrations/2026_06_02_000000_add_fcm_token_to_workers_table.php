<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('workers', 'fcm_token')) {
            Schema::table('workers', function (Blueprint $table) {
                $column = $table->text('fcm_token')->nullable();

                if (Schema::hasColumn('workers', 'phone')) {
                    $column->after('phone');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('workers', 'fcm_token')) {
            Schema::table('workers', function (Blueprint $table) {
                $table->dropColumn('fcm_token');
            });
        }
    }
};
