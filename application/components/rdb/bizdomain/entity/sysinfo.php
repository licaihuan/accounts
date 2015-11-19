<?php
class Sysinfo extends Entity
{
	const ID_OBJ  = 'sysinfo';

	public static function createByBiz( $param )
	{
		$cls = __CLASS__;
		$obj = new $cls();
		$obj->id = LoaderSvc::loadIdGenter()->create( self::ID_OBJ );
		$obj->desc = isset($param['desc']) ? $param['desc'] : '';
		$obj->ctime = date('Y-m-d H:i:s');
		return $obj;
	}
}
