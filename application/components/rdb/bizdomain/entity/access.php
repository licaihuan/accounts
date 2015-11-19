<?php
class Access extends Entity
{
	const ID_OBJ  = 'access';
	
	const STATUS_DISABLE       = '0';
	const STATUS_ENABLE        = '1';
	
	public static function createByBiz( $param )
	{
		$cls = __CLASS__;
		$obj = new $cls();
		
		$obj->id = LoaderSvc::loadIdGenter()->create( self::ID_OBJ );
		$obj->rid = $param['rid'];
		$obj->pid = $param['pid'];
		$obj->nid = $param['nid'];
		$obj->fields = $param['fields'];

		return $obj;
	}
}