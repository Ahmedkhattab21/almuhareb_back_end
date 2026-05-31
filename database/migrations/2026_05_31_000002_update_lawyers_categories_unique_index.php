<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lawyers_categories', function (Blueprint $table) {
            $table->index('lawyer_id', 'lawyers_categories_lawyer_id_lookup');
        });

        Schema::table('lawyers_categories', function (Blueprint $table) {
            $table->dropUnique(['lawyer_id', 'category_id']);
            $table->unique(['company_id', 'lawyer_id', 'category_id'], 'lawyer_company_category_unique');
        });
    }

    public function down(): void
    {
        Schema::table('lawyers_categories', function (Blueprint $table) {
            $table->dropUnique('lawyer_company_category_unique');
            $table->unique(['lawyer_id', 'category_id']);
            $table->dropIndex('lawyers_categories_lawyer_id_lookup');
        });
    }
};
