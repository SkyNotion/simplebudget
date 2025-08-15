<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Notifications extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notifications', function(Blueprint $table)
		{
			$table->bigIncrements('notification_id');
			$table->bigInteger('user_id')->unsigned();
			$table->string('source', 255);
			$table->bigInteger('source_id');
			$table->text('content');
			$table->timestamps();
			$table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('notifications');
	}

}
