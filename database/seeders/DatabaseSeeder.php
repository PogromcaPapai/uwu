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

define("SMALL_PLACES", true)

class DatabaseSeeder extends Seeder
{
    /**
     * Domyślny seeder
     *
     * @return void
     */
    public function run()
    {
        // Uzupełnienie tabeli na podstawie listy miejsc
        $file = fopen('database\miejsca.csv', 'r');
        $value = fgetcsv($file, 0, ';');
        $counter = 0
        while ($value)
        {
            DB::table('places')->insert([
                'name'  => $value[0],
                'desc'  => $value[1],
                'gmina' => $value[2],
                'powiat'=> $value[3],
                'wojew' => $value[4],
                'lat' => $value[5],
                'lon' => $value[6],
            ]);
            $value = fgetcsv($file, 0, ';');
            
            $counter++;
            if ($counter >=1000 && SMALL_PLACES) break;
        }
        fclose($file);
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
