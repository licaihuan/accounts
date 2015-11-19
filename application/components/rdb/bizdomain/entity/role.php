<?php
class Role extends Entity
{
	const ID_OBJ  = 'role';
	
	const STATUS_DISABLE       = '0';
	const STATUS_ENABLE        = '1';

	static $STATUS_STV = array(
		'STATUS_DISABLE' => self::STATUS_DISABLE,
		'STATUS_ENABLE' => self::STATUS_ENABLE,
	);

	static $STATUS_CONF = array(
		self::STATUS_DISABLE => array('NAME' => '禁用'),
		self::STATUS_ENABLE => array('NAME' => '启用'),
	);
	
	public static function createByBiz( $param )
	{
		$cls = __CLASS__;
		$obj = new $cls();
		$obj->id = LoaderSvc::loadIdGenter()->create( self::ID_OBJ );
		$obj->name = $param['name'];
		$obj->pid = $param['pid'];
		$obj->status = $param['status'];
		$obj->remark = $param['remark'];
		$obj->ename = $param['ename'];
		$obj->create_time = time();
		$obj->update_time = time();
		return $obj;
	}
}