<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraduccionesi18nTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		
       
            
            
            Schema::create('traducciones_i18n', function(Blueprint $table)
            {
                $table->increments('id');
                
                $table->integer('item_id')->unsigned();
                //$table->foreign('item_id')->references('id')->on('traducciones');
                
                $table->string('idioma', 4);
                $table->text('texto');
                
                $table->timestamps();
            });
            
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		
                Schema::drop('traducciones_i18n');
	}

}
