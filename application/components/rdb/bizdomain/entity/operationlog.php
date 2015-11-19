<?php
class Operationlog extends Entity
{
	const ID_OBJ  = 'operationlog';

	public static function createByBiz( $param )
	{
		$cls = __CLASS__;
		$obj = new $cls();
		$obj->id = LoaderSvc::loadIdGenter()->create( self::ID_OBJ );
		$obj->entity = $param['entity'];
		$obj->pkid = $param['pkid'];
		$obj->desc = $param['desc'];
		$obj->action = $param['action'];
		$obj->status = $param['status'];
		$obj->uid = $param['uid'];
		$obj->ip = UtlsSvc::getClientIP();
		$obj->time = time();
		return $obj;
	}
}