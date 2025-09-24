<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Shift 1',
                'start_time' => '07:00:00',
                'end_time' => '16:00:00',
                'description' => 'Shift pagi (07:00 - 16:00)',
                'is_active' => true,
            ],
            [
                'name' => 'Shift 2',
                'start_time' => '15:00:00',
                'end_time' => '00:00:00',
                'description' => 'Shift sore (15:00 - 00:00)',
                'is_active' => true,
            ],
            [
                'name' => 'Shift 3',
                'start_time' => '23:00:00',
                'end_time' => '08:00:00',
                'description' => 'Shift malam (23:00 - 08:00)',
                'is_active' => true,
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::create($shift);
        }
    }
}
