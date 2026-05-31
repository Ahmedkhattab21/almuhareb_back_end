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
            'قضايا عمالية',
            'قضايا الرواتب والأجور',
            'قضايا الفصل والتعويض',
            'قضايا العقود',
            'قضايا إصابات العمل',
            'قضايا التأمينات الاجتماعية',
            'قضايا الإقامة والعمل',
            'قضايا المخالفات الإدارية',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['name' => $name],
                [
                    'admin_id' => $admin?->id,
                    'status' => Category::STATUS_ACTIVE,
                ]
            );
        }
    }
}
