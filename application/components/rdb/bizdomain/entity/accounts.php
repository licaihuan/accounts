<?php
class Accounts extends Entity
{
	const ID_OBJ  = 'accounts';	

	const CAT_CASH = 1;
	static $CAT_OPTIONS = array(
		self::CAT_CASH,
	);
	static $CAT_CONF = array(
		self::CAT_CASH => array('NAME'=>'钱包账户'),
	);

	public static function createByBiz( $param )
	{
		$cls = __CLASS__;
		$obj = new $cls();
		$obj->id = LoaderSvc::loadIdGenter()->create(self::ID_OBJ);
		$obj->ctime = date('Y-m-d H:i:s');
		$obj->utime = date('Y-m-d H:i:s');
		$obj->cat = in_array($param['cat'],self::$CAT_OPTIONS) ? $param['cat'] : self::CAT_CASH;
		$obj->uid = $param['uid'];
		$obj->balance = isset($param['balance']) ? $param['balance'] : 0;
		return $obj;
	}
}
