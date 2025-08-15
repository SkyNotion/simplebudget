<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
	protected $table = 'users';
	protected $primaryKey = 'user_id';
	protected $hidden = ['password', 'telegram_username'];
	protected $fillable = ['name', 'email', 'password', 'telegram_username'];
}
