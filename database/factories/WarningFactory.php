<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PhpOption\None;

class WarningFactory extends Factory
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
            'event' => "Incoming ".$this->faker->title(),
            'lvl' => 1,
            'messtype' => 0,
            'starttime' => $start,
            'endtime' => $end,
            'prob' => "0%",
            'how' => $this->faker->paragraph(),
            'canceltime' => NULL,
            'cause' => NULL,
            'sms' => $this->faker->paragraph(),
            'rso' => $this->faker->paragraph(),
            'remarks' => $this->faker->paragraph(),
            'file' => "abc.pdf",
            'downloaded_at' => now(),
        ];
    }
}
