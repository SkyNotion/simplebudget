<?php namespace App\Custom;

use Ramsey\Uuid\Uuid as Ruuid;

class Uuid{
	public static function uuid4(){
		return str_replace('-', '', Ruuid::uuid4()->toString());
	}
}