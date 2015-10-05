<?php namespace Lanin\Laravel\Hashids;

use Illuminate\Support\Facades\Facade;

class HashidsFacade extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'hashids';
	}

}