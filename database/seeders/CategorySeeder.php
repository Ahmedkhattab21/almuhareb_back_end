<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::query()->first();

        $categories = [
            1 => 'القضايا العمالية',
            2 => 'الرواتب والمستحقات',
            3 => 'قضايا الفصل والتعويض',
            4 => 'قضايا عقد العمل',
            5 => 'قضايا إصابات العمل',
            6 => 'قضايا التأمينات الاجتماعية ونهاية الخدمة',
            7 => 'قضايا الإقامة ورخص العمل',
            8 => 'قضايا الشكاوي والمخالفات',
            9 => 'قضايا الدوام والإجازات',
            10 => 'استشارات أخرى',
        ];

        foreach ($categories as $id => $name) {
            $category = Category::query()->find($id)
                ?? Category::query()->firstOrNew(['name' => $name]);

            $category->fill([
                'admin_id' => $category->admin_id ?? $admin?->id,
                'name' => $name,
                'status' => $category->status ?? Category::STATUS_ACTIVE,
            ])->save();
        }
    }
}
