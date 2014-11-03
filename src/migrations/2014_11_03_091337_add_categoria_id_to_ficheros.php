<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoriaIdToFicheros extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::table('ficheros', function($table)
            {
                $table->integer('categoria_id')->unsigned()->nullable();
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
                Schema::table('ficheros', function($table)
                {
                    $table->dropColumn('categoria_id');
                });
	}
}
