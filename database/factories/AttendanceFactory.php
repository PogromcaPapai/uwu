<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Event;
use App\Models\User;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "is_admin"  => random_int(0, 1),
            "event_id"  => Event::all()->random()->id,
            "user_id"   => User::all()->random()->id,
        ];
    }
}
