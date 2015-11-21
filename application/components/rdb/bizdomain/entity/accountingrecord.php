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

	const CAT_REFUND         = 11;
	const CAT_CASH		     = 12;
	const CAT_RECHARGE       = 13;
	const CAT_BALANCE_PAY    = 14;

	static $CAT_OPTIONS = array(
		self::CAT_REFUND,
		self::CAT_CASH,
		self::CAT_RECHARGE,
		self::CAT_BALANCE_PAY,
	);

	static $CAT_CONF = array(
		self::CAT_REFUND =>array('NAME'=>'退款'),
		self::CAT_CASH =>array('NAME'=>'取现'),
		self::CAT_RECHARGE =>array('NAME'=>'充值'),
		self::CAT_BALANCE_PAY =>array('NAME'=>'余额支付'),
	);
	
	const FROM_SYS         = 11;
	const FROM_ALIPAY      = 12;

	static $FROM_OPTIONS = array(
		self::FROM_SYS,
		self::FROM_ALIPAY,
	);

	static $FROM_CONF = array(
		self::FROM_SYS =>array('NAME'=>'系统内'),
		self::FROM_ALIPAY =>array('NAME'=>'支付宝'),
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
		$obj->datetime = is_null($param['datetime']) ? date('Y-m-d H:i:s') : $param['datetime'];
		$obj->type = in_array($param['type'],self::$TYPE_OPTIONS) ? $param['type'] : self::TYPE_IN;
		$obj->in = isset($param['in']) ? $param['in'] : 0;
		$obj->out = isset($param['out']) ? $param['out'] : 0;
		$obj->fee = isset($param['fee']) ? $param['fee'] : 0;
		$obj->accountid = $param['accountid'];
		$obj->remark = isset($param['remark']) ? $param['remark'] : '';
		$obj->cat = in_array($param['cat'],self::$CAT_OPTIONS) ? $param['cat'] : self::CAT_RECHARGE;
		$obj->from = in_array($param['from'],self::$FROM_OPTIONS) ? $param['from'] : self::FROM_SYS;
		$obj->transid = isset($param['transid']) ? $param['transid'] : '';
		$obj->uid = isset($param['uid']) ? $param['uid'] : '';
		$obj->state = in_array($param['state'],self::$STATE_OPTIONS) ? $param['state'] : self::STATE_NORMAL;
		$obj->balance = isset($param['balance']) ? $param['balance'] : 0;
		return $obj;
	}
}
