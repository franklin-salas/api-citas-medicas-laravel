<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoctorSchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('doctor_schedules')->insert([
            ['id' => 1, 'hour_start' => '08:00:00', 'hour_end' => '08:15:00', 'hour' => '08', 'created_at' => null, 'updated_at' => null],
            ['id' => 2, 'hour_start' => '08:15:00', 'hour_end' => '08:30:00', 'hour' => '08', 'created_at' => null, 'updated_at' => null],
            ['id' => 3, 'hour_start' => '08:30:00', 'hour_end' => '08:45:00', 'hour' => '08', 'created_at' => null, 'updated_at' => null],
            ['id' => 4, 'hour_start' => '08:45:00', 'hour_end' => '09:00:00', 'hour' => '08', 'created_at' => null, 'updated_at' => null],
            ['id' => 5, 'hour_start' => '09:00:00', 'hour_end' => '09:15:00', 'hour' => '09', 'created_at' => null, 'updated_at' => null],
            ['id' => 6, 'hour_start' => '09:15:00', 'hour_end' => '09:30:00', 'hour' => '09', 'created_at' => null, 'updated_at' => null],
            ['id' => 7, 'hour_start' => '09:30:00', 'hour_end' => '09:45:00', 'hour' => '09', 'created_at' => null, 'updated_at' => null],
            ['id' => 8, 'hour_start' => '09:45:00', 'hour_end' => '10:00:00', 'hour' => '09', 'created_at' => null, 'updated_at' => null],
            ['id' => 9, 'hour_start' => '10:00:00', 'hour_end' => '10:15:00', 'hour' => '10', 'created_at' => null, 'updated_at' => null],
            ['id' => 10, 'hour_start' => '10:15:00', 'hour_end' => '10:30:00', 'hour' => '10', 'created_at' => null, 'updated_at' => null],
            ['id' => 11, 'hour_start' => '10:30:00', 'hour_end' => '10:45:00', 'hour' => '10', 'created_at' => null, 'updated_at' => null],
            ['id' => 12, 'hour_start' => '10:45:00', 'hour_end' => '11:00:00', 'hour' => '10', 'created_at' => null, 'updated_at' => null],
            ['id' => 13, 'hour_start' => '11:00:00', 'hour_end' => '11:15:00', 'hour' => '11', 'created_at' => null, 'updated_at' => null],
            ['id' => 14, 'hour_start' => '11:15:00', 'hour_end' => '11:30:00', 'hour' => '11', 'created_at' => null, 'updated_at' => null],
            ['id' => 15, 'hour_start' => '11:30:00', 'hour_end' => '11:45:00', 'hour' => '11', 'created_at' => null, 'updated_at' => null],
            ['id' => 16, 'hour_start' => '11:45:00', 'hour_end' => '12:00:00', 'hour' => '11', 'created_at' => null, 'updated_at' => null],
            ['id' => 17, 'hour_start' => '12:00:00', 'hour_end' => '12:15:00', 'hour' => '12', 'created_at' => null, 'updated_at' => null],
            ['id' => 18, 'hour_start' => '12:15:00', 'hour_end' => '12:30:00', 'hour' => '12', 'created_at' => null, 'updated_at' => null],
            ['id' => 19, 'hour_start' => '12:30:00', 'hour_end' => '12:45:00', 'hour' => '12', 'created_at' => null, 'updated_at' => null],
            ['id' => 20, 'hour_start' => '12:45:00', 'hour_end' => '13:00:00', 'hour' => '12', 'created_at' => null, 'updated_at' => null],
            ['id' => 21, 'hour_start' => '13:00:00', 'hour_end' => '13:15:00', 'hour' => '13', 'created_at' => null, 'updated_at' => null],
            ['id' => 22, 'hour_start' => '13:15:00', 'hour_end' => '13:30:00', 'hour' => '13', 'created_at' => null, 'updated_at' => null],
            ['id' => 23, 'hour_start' => '13:30:00', 'hour_end' => '13:45:00', 'hour' => '13', 'created_at' => null, 'updated_at' => null],
            ['id' => 24, 'hour_start' => '13:45:00', 'hour_end' => '14:00:00', 'hour' => '13', 'created_at' => null, 'updated_at' => null],
            ['id' => 25, 'hour_start' => '14:00:00', 'hour_end' => '14:15:00', 'hour' => '14', 'created_at' => null, 'updated_at' => null],
            ['id' => 26, 'hour_start' => '14:15:00', 'hour_end' => '14:30:00', 'hour' => '14', 'created_at' => null, 'updated_at' => null],
            ['id' => 27, 'hour_start' => '14:30:00', 'hour_end' => '14:45:00', 'hour' => '14', 'created_at' => null, 'updated_at' => null],
            ['id' => 28, 'hour_start' => '14:45:00', 'hour_end' => '15:00:00', 'hour' => '14', 'created_at' => null, 'updated_at' => null],
            ['id' => 29, 'hour_start' => '15:00:00', 'hour_end' => '15:15:00', 'hour' => '15', 'created_at' => null, 'updated_at' => null],
            ['id' => 30, 'hour_start' => '15:15:00', 'hour_end' => '15:30:00', 'hour' => '15', 'created_at' => null, 'updated_at' => null],
            ['id' => 31, 'hour_start' => '15:30:00', 'hour_end' => '15:45:00', 'hour' => '15', 'created_at' => null, 'updated_at' => null],
            ['id' => 32, 'hour_start' => '15:45:00', 'hour_end' => '16:00:00', 'hour' => '15', 'created_at' => null, 'updated_at' => null],
            ['id' => 33, 'hour_start' => '16:00:00', 'hour_end' => '16:15:00', 'hour' => '16', 'created_at' => null, 'updated_at' => null],
            ['id' => 34, 'hour_start' => '16:15:00', 'hour_end' => '16:30:00', 'hour' => '16', 'created_at' => null, 'updated_at' => null],
            ['id' => 35, 'hour_start' => '16:30:00', 'hour_end' => '16:45:00', 'hour' => '16', 'created_at' => null, 'updated_at' => null],
            ['id' => 36, 'hour_start' => '16:45:00', 'hour_end' => '17:00:00', 'hour' => '16', 'created_at' => null, 'updated_at' => null],
            ['id' => 37, 'hour_start' => '17:00:00', 'hour_end' => '17:15:00', 'hour' => '17', 'created_at' => null, 'updated_at' => null],
            ['id' => 38, 'hour_start' => '17:15:00', 'hour_end' => '17:30:00', 'hour' => '17', 'created_at' => null, 'updated_at' => null],
            ['id' => 39, 'hour_start' => '17:30:00', 'hour_end' => '17:45:00', 'hour' => '17', 'created_at' => null, 'updated_at' => null],
            ['id' => 40, 'hour_start' => '17:45:00', 'hour_end' => '18:00:00', 'hour' => '17', 'created_at' => null, 'updated_at' => null],
        ]);
    }
}
