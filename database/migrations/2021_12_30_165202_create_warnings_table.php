<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateWarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->text('event');
            $table->tinyInteger('lvl');
            $table->tinyInteger('messtype');
            $table->datetime('starttime')->nullable();
            $table->datetime('endtime')->nullable();
            $table->string('prob', 4);
            $table->text('how');
            $table->datetime('canceltime')->nullable();
            $table->text('cause')->nullable();
            $table->text('sms');
            $table->text('rso');
            $table->text('remarks');
            $table->string('file', 31);
            $table->datetime('downloaded_at');
            $table->timestamps();
        });
        DB::statement("DROP USER IF EXISTS `warn-scrap`@`localhost`");
        DB::statement("GRANT USAGE ON *.* TO `warn-scrap`@`localhost` IDENTIFIED BY 'FW2BL(@qHE)vS*nY'");
        DB::statement("GRANT SELECT, INSERT, DELETE ON `uvvv`.`warnings` TO `warn-scrap`@`localhost`;");
        DB::statement("GRANT SELECT ON `uvvv`.`places` TO `warn-scrap`@`localhost`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP USER IF EXISTS `warn-scrap`@`localhost`;");
        Schema::dropIfExists('warnings');
    }
}
