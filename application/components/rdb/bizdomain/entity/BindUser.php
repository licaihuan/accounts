<?php
class BindUser extends Entity
{
	const ID_OBJ  = 'binduser';
	const STATE_ENABLED = 1;
	const STATE_DISABLED = 2;
	
	static $STATE_OPTIONS = array(
		self::STATE_ENABLED,
		self::STATE_DISABLED,
	);
	
	static $STATE_CONF = array(
		self::STATE_ENABLED => array(
			'NAME'=>'已启用',
		),
		self::STATE_DISABLED => array(
			'NAME'=>'已禁用',
		),
	);

	public static function createByBiz( $param )
	{
		$cls = __CLASS__;
		$obj = new $cls();
		$obj->id = LoaderSvc::loadIdGenter()->create( self::ID_OBJ );
		$obj->mobile = $param['mobile'];
		$obj->state = in_array($param['state'],self::$STATE_OPTIONS) ? $param['state'] : self::STATE_ENABLED;
		$obj->ctime = date('Y-m-d H:i:s');
		$obj->utime = isset($param['utime']) ? $param['utime'] : date('Y-m-d H:i:s');
		return $obj;
	}
}
