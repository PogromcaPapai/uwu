<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Database\Seeder;

class EventAttend extends Seeder
{
    /**
     * Seeder bez dodawania użytkowników
     *
     * @return void
     */
    public function run()
    {
        Event::factory(10)->create();
        Attendance::factory(10)->create();
    }
}
