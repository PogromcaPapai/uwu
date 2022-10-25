<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Stworzenie tabeli
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40+10);
            $table->string('desc', 48+10);
            $table->string('gmina', 23+10);
            $table->string('powiat', 24+10);
            $table->string('wojew', 19+10);
            $table->float('lat', 12, 8);
            $table->float('lon', 12, 8);
        });
        
        // Uzupełnienie tabeli na podstawie listy miejsc
        $file = fopen('database\miejsca.csv', 'r');
        $value = fgetcsv($file, 0, ';');
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
            $value = fgetcsv($file, 0, ',');
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
