<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BudgetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('budgets', function(Blueprint $table)
		{
			$table->bigIncrements('budget_id');
			$table->bigInteger('account_id')->unsigned();
			$table->text('name')->nullable();
			$table->text('description')->nullable();
			$table->double('budget_limit');
			$table->text('entities');
			$table->timestamps();
			$table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('budgets');
	}

}
