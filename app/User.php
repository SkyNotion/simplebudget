<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {
	use Authenticatable, CanResetPassword;
	
	protected $table = 'users';
	protected $primaryKey = 'user_id';
	protected $hidden = ['password', 'telegram_username', 'remember_token'];
	protected $fillable = ['name', 'email', 'password', 'telegram_username'];
}
