<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40+10);
            $table->string('desc', 48+10);
            $table->string('gmina', 23+10);
            $table->string('powiat', 24+10);
            $table->string('wojew', 19+10);
        });
        
        $file = fopen('database\miejsca.csv', 'r');
        $csv = fgetcsv($file, 0, ';');
        $value = fgetcsv($file, 0, ';');
        while ($value)
        {
            DB::table('places')->insert([
                'name'  => $value[0],
                'desc'  => $value[1],
                'gmina' => $value[2],
                'powiat'=> $value[3],
                'wojew' => $value[4],
            ]);
            $value = fgetcsv($file, 0, ';');
        }
        fclose($file);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('places');
    }
}
