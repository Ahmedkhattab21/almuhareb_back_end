<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('workers', function (Blueprint $table) {
            if (! Schema::hasColumn('workers', 'city_id')) {
                $table->foreignId('city_id')
                    ->nullable()
                    ->after('position_id')
                    ->constrained('cities')
                    ->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('workers', function (Blueprint $table) {
            if (Schema::hasColumn('workers', 'city_id')) {
                $table->dropConstrainedForeignId('city_id');
            }
        });
    }
};
