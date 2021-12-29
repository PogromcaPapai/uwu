<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

function randomDate($start_date, $end_date)
{
    // Convert to timetamps
    $min = strtotime($start_date);
    $max = strtotime($end_date);

    // Generate random number using above bounds
    $val = rand($min, $max);

    // Convert back to desired date format
    return date('Y-m-d', $val);
}

class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $start = randomDate('2021-12-01', '2022-01-31');
        $end = randomDate($start, '2022-01-31');
        return [
            'title' => Str::random(10),
            'start' => $start,
            'end'   => $end,
        ];
    }
}
