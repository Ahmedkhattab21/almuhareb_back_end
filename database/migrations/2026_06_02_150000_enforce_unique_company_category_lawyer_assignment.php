<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            DELETE lc_old FROM lawyers_categories lc_old
            INNER JOIN lawyers_categories lc_new
                ON lc_old.company_id = lc_new.company_id
                AND lc_old.category_id = lc_new.category_id
                AND lc_old.id < lc_new.id
        ');

        Schema::table('lawyers_categories', function (Blueprint $table) {
            $table->dropUnique('lawyer_company_category_unique');
            $table->unique(['company_id', 'category_id'], 'company_category_unique_lawyer_assignment');
        });
    }

    public function down(): void
    {
        Schema::table('lawyers_categories', function (Blueprint $table) {
            $table->dropUnique('company_category_unique_lawyer_assignment');
            $table->unique(['company_id', 'lawyer_id', 'category_id'], 'lawyer_company_category_unique');
        });
    }
};
