<?php

namespace Database\Factories;

use App\Models\Place;
use App\Models\Warning;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarnedPlaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "place_id"  => 10,
            "warning_id"   => Warning::all()->random()->id,
        ];
    }
}
