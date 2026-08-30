<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Company;
use App\Models\Lawyer;
use App\Models\Worker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class LegalStructureSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::query()->first();

        if (! $admin) {
            $admin = Admin::create([
                'name' => 'أدمن myaman',
                'email' => 'admin@myaman.com',
                'password' => Hash::make('12345678'),
                'admin_type' => 'main_admin',
                'status' => 'active',
            ]);
        }

        $category = Category::updateOrCreate(
            ['name' => 'قضايا عمالية'],
            [
                'admin_id' => $admin->id,
                'status' => Category::STATUS_ACTIVE,
            ]
        );

        $lawyer = Lawyer::updateOrCreate(
            ['email' => 'ahmed.lawyer@myaman.com'],
            [
                'admin_id' => $admin->id,
                'name' => 'أحمد الشريف',
                'phone' => '0502222222',
                'password' => Hash::make('12345678'),
                'status' => 'active',
                'preferred_language' => 'ar',
                'created_by' => $admin->id,
            ]
        );

        $companyData = [
            'company_name' => 'شركة القافري للخدمات',
            'password' => Hash::make('12345678'),
            'phone' => '0122222222',
            'tax_number' => '3000000001',
            'address' => 'الرياض - حي العليا',
            'status' => 'active',
            'created_by' => $admin->id,
        ];

        if (Schema::hasColumn('companies', 'lawyer_id')) {
            $companyData['lawyer_id'] = null;
        }

        $company = Company::updateOrCreate(
            ['email' => 'company1@myaman.com'],
            $companyData
        );

        DB::table('lawyers_categories')->updateOrInsert(
            [
                'company_id' => $company->id,
                'lawyer_id' => $lawyer->id,
                'category_id' => $category->id,
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Worker::updateOrCreate(
            ['phone' => '0551000001'],
            [
                'company_id' => $company->id,
                'name' => 'محمد خان',
                'email' => 'worker1@example.com',
                'password' => Hash::make('12345678'),
                'iqama_number' => '2500000001',
                'nationality' => 'باكستاني',
                'preferred_language' => 'ur',
                'position' => 'عامل نظافة',
                'created_by' => $company->id,
                'status' => 'active',
            ]
        );
    }
}
