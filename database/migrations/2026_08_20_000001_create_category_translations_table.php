<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('category_translations')) {
            Schema::create('category_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')
                    ->constrained('categories')
                    ->cascadeOnDelete();
                $table->string('locale', 10);
                $table->string('name');
                $table->timestamps();

                $table->unique(['category_id', 'locale']);
                $table->index('locale');
                $table->index('name');
            });
        }

        if (Schema::hasTable('categories')) {
            DB::table('categories')
                ->select(['id', 'name'])
                ->orderBy('id')
                ->chunkById(100, function ($categories) {
                    foreach ($categories as $category) {
                        DB::table('category_translations')->updateOrInsert(
                            [
                                'category_id' => $category->id,
                                'locale' => 'ar-EG',
                            ],
                            [
                                'name' => $category->name,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('category_translations');
    }
};
