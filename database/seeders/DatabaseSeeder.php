<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\WarnedPlace;
use App\Models\Warning;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Domyślny seeder
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => Hash::make('12345678'),
        ]);
        DB::table('users')->insert([
            'name' => 'abc',
            'email' => 'abc@test.com',
            'password' => Hash::make('12345678'),
        ]);
        Event::factory(10)->create();
        Attendance::factory(10)->create();
    }
}
