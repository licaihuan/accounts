<?php
class Freezes extends Entity
{
	const CAT_REFUND      = 11;
	const CAT_CASH		  = 12;

	static $CAT_CONF = array(
		self::CAT_REFUND =>array('NAME'=>'退款冻结'),
		self::CAT_CASH =>array('NAME'=>'取现冻结'),
	);

	static $CAT_OPTIONS = array(
		self::CAT_REFUND,
		self::CAT_CASH,
	);

	const STATE_FREEZE_IN = 100;
	const STATE_DEFROSTED = 101;
	const STATE_OVER      = 102;

	static $STATE_OPTIONS = array(
		self::STATE_FREEZE_IN,
		self::STATE_DEFROSTED,
		self::STATE_OVER,
	);
	static $STATE_CONF = array(
		self::STATE_FREEZE_IN => array('NAME'=>'冻结中'),
		self::STATE_DEFROSTED => array('NAME'=>'已解冻'),
		self::STATE_OVER => array('NAME'=>'已解除'),
	);


	const ID_OBJ  = 'freezes';

	public static function createByBiz( $param )
	{
		$cls = __CLASS__;
		$obj = new $cls();
		$obj->id = LoaderSvc::loadIdGenter()->create(self::ID_OBJ);
		$obj->ctime = date('Y-m-d H:i:s');
		$obj->utime = date('Y-m-d H:i:s');
		$obj->accountid = $param['accountid'];
		$obj->transid = $param['transid'];
		$obj->amount = $param['amount'];
		$obj->remark = is_null($param['remark']) ? '' : $param['remark'];
		$obj->cat = in_array($param['cat'],self::$CAT_OPTIONS) ? $param['cat'] : self::CAT_CASH;
		$obj->state = in_array($param['state'],self::$STATE_OPTIONS) ? $param['state'] : self::STATE_FREEZE_IN;
		$obj->ip = is_null($param['ip']) ? UtlsSvc::getClientIP() : $param['ip'];
		return $obj;
	}
}
