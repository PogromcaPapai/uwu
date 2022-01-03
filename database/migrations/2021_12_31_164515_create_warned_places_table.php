<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarnedPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warned_places', function (Blueprint $table) {
            $table->foreignId('place_id')->references('id')->on('places')->onDelete('cascade');
            $table->foreignId('warning_id')->references('id')->on('warnings')->onDelete('cascade');
            
        });
        DB::statement("GRANT SELECT, INSERT, DELETE ON `uvvv`.`warned_places` TO `warn-scrap`@`localhost`;");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warned_places');
    }
}
