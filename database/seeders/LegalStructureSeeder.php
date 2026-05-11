<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Company;
use App\Models\Lawyer;
use App\Models\Worker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class LegalStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
   {
        $admin = Admin::first();

        if (! $admin) {
            $admin = Admin::create([
                'name' => 'أدمن المحارب',
                'email' => 'admin@almuharib.com',
                'password' => Hash::make('12345678'),
                'admin_type' => 'main_admin',
                'status' => 'active',
            ]);
        }

        $lawyer1 = Lawyer::updateOrCreate(
            ['email' => 'ahmed.lawyer@almuharib.com'],
            [
                'admin_id' => $admin->id,
                'name' => 'أحمد الشريف',
                'phone' => '0501111111',
                'password' => Hash::make('12345678'),
                'status' => 'active',
                'preferred_language' => 'ar',
                'created_by' => $admin->id,
            ]
        );

        $lawyer2 = Lawyer::updateOrCreate(
            ['email' => 'layla.lawyer@almuharib.com'],
            [
                'admin_id' => $admin->id,
                'name' => 'ليلى رشيد',
                'phone' => '0502222222',
                'password' => Hash::make('12345678'),
                'status' => 'active',
                'preferred_language' => 'ar',
                'created_by' => $admin->id,
            ]
        );

        $company1 = Company::updateOrCreate(
            ['email' => 'company1@almuharib.com'],
            [
                'lawyer_id' => $lawyer1->id,
                'company_name' => 'شركة القافري للمحاماة',
                'password' => Hash::make('12345678'),
                'phone' => '0111111111',
                'tax_number' => '3000000001',
                'address' => 'الرياض - حي العليا',
                'status' => 'active',
                'created_by' => $admin->id,
            ]
        );

        $company2 = Company::updateOrCreate(
            ['email' => 'company2@almuharib.com'],
            [
                'lawyer_id' => $lawyer2->id,
                'company_name' => 'مجموعة مكة للخدمات',
                'password' => Hash::make('12345678'),
                'phone' => '0112222222',
                'tax_number' => '3000000002',
                'address' => 'جدة - حي السلامة',
                'status' => 'active',
                'created_by' => $admin->id,
            ]
        );

        Worker::updateOrCreate(
            ['phone' => '0551000001'],
            [
                'company_id' => $company1->id,
                'name' => 'محمد خان',
                'email' => 'worker1@example.com',
                'password' => Hash::make('12345678'),
                'iqama_number' => '2500000001',
                'nationality' => 'باكستاني',
                'preferred_language' => 'ur',
                'position' => 'عامل نظافة',
                'created_by' => $company1->id,
                'status' => 'active',
            ]
        );

        Worker::updateOrCreate(
            ['phone' => '0551000002'],
            [
                'company_id' => $company1->id,
                'name' => 'جون سانتوس',
                'email' => 'worker2@example.com',
                'password' => Hash::make('12345678'),
                'iqama_number' => '2500000002',
                'nationality' => 'فلبيني',
                'preferred_language' => 'en',
                'position' => 'سائق',
                'created_by' => $company1->id,
                'status' => 'active',
            ]
        );

        Worker::updateOrCreate(
            ['phone' => '0551000003'],
            [
                'company_id' => $company2->id,
                'name' => 'عبد الرحمن علي',
                'email' => 'worker3@example.com',
                'password' => Hash::make('12345678'),
                'iqama_number' => '2500000003',
                'nationality' => 'مصري',
                'preferred_language' => 'ar',
                'position' => 'فني صيانة',
                'created_by' => $company2->id,
                'status' => 'active',
            ]
        );
    }
}
