<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $positions = [
            'عامل مخزن',
            'عامل تشغيل',
            'سائق',
            'عامل نظافة',
            'عامل إنتاج',
            'مشرف عمال',
            'فني صيانة',
            'حارس أمن',
            'عامل تحميل وتنزيل',
            'عامل مطعم',
        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(
                ['name' => $position],
                ['status' => 'active']
            );
        }
    }
}
