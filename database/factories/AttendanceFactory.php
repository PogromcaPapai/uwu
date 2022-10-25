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
            "event"  => Event::all()->random()->id,
            "user"   => User::all()->random()->id,
        ];
    }
}
