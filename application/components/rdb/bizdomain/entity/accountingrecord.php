<?php
class Accountingrecord extends Entity
{
	const ID_OBJ  = 'accountingrecord';

	const TYPE_IN  	 	  = 1;
	const TYPE_OUT  	  = -1;

	static $TYPE_OPTIONS = array(
		self::TYPE_IN,
		self::TYPE_OUT,
	);

	static $TYPE_CONF = array(
		self::TYPE_IN =>array('NAME'=>'收入'),
		self::TYPE_OUT =>array('NAME'=>'支出'),
	);

	const CAT_REFUND      = 11;
	const CAT_CASH		  = 12;
	const CAT_RECHARGE    = 13;

	static $CAT_OPTIONS = array(
		self::CAT_REFUND,
		self::CAT_CASH,
		self::CAT_RECHARGE,
	);

	static $CAT_CONF = array(
		self::CAT_REFUND =>array('NAME'=>'退款'),
		self::CAT_CASH =>array('NAME'=>'取现'),
		self::CAT_RECHARGE =>array('NAME'=>'充值'),
	);

	const STATE_NORMAL   = 1;
	const STATE_REVERSAL = 2;

	static $STATE_OPTIONS = array(
		self::STATE_NORMAL,
		self::STATE_REVERSAL,
	);

	static $STATE_CONF = array(
		self::STATE_NORMAL =>array('NAME'=>'正常'),
		self::STATE_REVERSAL =>array('NAME'=>'交易冲正'),
	);
	
	public static function createByBiz( $param )
	{
		$cls = __CLASS__;
		$obj = new $cls();
		$obj->id = isset($param['id']) ? $param['id'] : LoaderSvc::loadIdGenter()->create(self::ID_OBJ);
		$obj->ctime = date('Y-m-d H:i:s');
		$obj->utime = date('Y-m-d H:i:s');
		$obj->datetime = $param['datetime'];
		$obj->type = in_array($param['type'],self::$TYPE_OPTIONS) ? $param['type'] : self::TYPE_IN;
		$obj->in = isset($param['in']) ? $param['in'] : 0;
		$obj->out = isset($param['out']) ? $param['out'] : 0;
		$obj->fee = isset($param['fee']) ? $param['fee'] : 0;
		$obj->accountid = $param['accountid'];
		$obj->remark = isset($param['remark']) ? $param['remark'] : '';
		$obj->cat = in_array($param['cat'],self::$CAT_OPTIONS) ? $param['cat'] : self::CAT_RECHARGE;
		$obj->from = isset($param['from']) ? $param['from'] : 'Sys';
		$obj->transid = isset($param['transid']) ? $param['transid'] : '';
		$obj->uid = isset($param['uid']) ? $param['uid'] : '';
		$obj->state = in_array($param['state'],self::$STATE_OPTIONS) ? $param['state'] : self::STATE_NORMAL;
		return $obj;
	}
}
