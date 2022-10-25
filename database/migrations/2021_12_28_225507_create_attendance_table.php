<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_admin');
            $table->foreignId('user')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('event')->references('id')->on('events')->onDelete('cascade');
            $table->timestamps();

            // $table->unique(['user_id', 'event_id']); // Ze względu na konstrukcję seedera, należy tą część wyłączyć.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance');
    }
}
