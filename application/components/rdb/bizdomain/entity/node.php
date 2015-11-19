<?php
class Node extends Entity
{
	const ID_OBJ  = 'node';
	
	const STATUS_DISABLE       = '0';
	const STATUS_ENABLE      = '1';
	
	public static function createByBiz( $param )
	{
		$cls = __CLASS__;
		$obj = new $cls();
		$obj->id = LoaderSvc::loadIdGenter()->create( self::ID_OBJ );
		$obj->name = $param['name'];
		$obj->action = $param['action'];
		$obj->type = $param['type'];
		$obj->ismenu = $param['ismenu'] == 1 ? $param['ismenu'] : 0;
		$obj->title = $param['title'];
		$obj->status = $param['status'];
		$obj->remark = $param['remark'];
		$obj->sort = $param['sort'];
		$obj->pid = $param['pid'];
		$obj->module = $param['module'];
		return $obj;
	}
}