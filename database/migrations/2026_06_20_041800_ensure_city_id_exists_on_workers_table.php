<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('workers') || Schema::hasColumn('workers', 'city_id')) {
            return;
        }

        Schema::table('workers', function (Blueprint $table) {
            $table->foreignId('city_id')
                ->nullable()
                ->after('position_id')
                ->constrained('cities')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        if (! Schema::hasTable('workers') || ! Schema::hasColumn('workers', 'city_id')) {
            return;
        }

        Schema::table('workers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
        });
    }
};
