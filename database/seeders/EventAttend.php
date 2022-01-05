<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Database\Seeder;

class EventAttend extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Event::factory(10)->create();
        Attendance::factory(10)->create();
    }
}
