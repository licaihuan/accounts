<?php
class Transaction extends Entity
{
	const STATE_INIT = 99;
	const STATE_SUCC = 1;
	const STATE_PROCESSING = 2;
	const STATE_FAIL = 3;

	static $STATE_OPTIONS = array(
		self::STATE_INIT,
		self::STATE_PROCESSING,
		self::STATE_SUCC,
		self::STATE_FAIL,
	);

	static $STATE_CONF = array(
		self::STATE_INIT =>array('NAME'=>'初始'),
		self::STATE_SUCC =>array('NAME'=>'成功'),
		self::STATE_PROCESSING =>array('NAME'=>'处理中'),
		self::STATE_FAIL =>array('NAME'=>'失败'),
	);
	
    const BTYPE_UNKNOWN     = 10;
	const BTYPE_REFUND      = 11;
	const BTYPE_CASH		= 12;
	const BTYPE_RECHARGE    = 15;

	static $BTYPE_CONF = array(
		self::BTYPE_UNKNOWN =>array('NAME'=>'未知'),
		self::BTYPE_REFUND =>array('NAME'=>'退款'),
		self::BTYPE_CASH =>array('NAME'=>'取现'),
		self::BTYPE_RECHARGE =>array('NAME'=>'充值'),
	);

	static $BTYPE_OPTIONS = array(
		self::BTYPE_REFUND,
		self::BTYPE_CASH,
		self::BTYPE_RECHARGE,
	);

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


	const SSTATE_UNSETTLED = 100;
	const SSTATE_SETTLED = 101;

	static $SSTATE_OPTIONS = array(
		self::SSTATE_UNSETTLED,
		self::SSTATE_SETTLED,
	);
	static $SSTATE_CONF = array(
		self::SSTATE_UNSETTLED => array('NAME'=>'未结算'),
		self::SSTATE_SETTLED => array('NAME'=>'已结算'),
	);

	const ID_OBJ  = 'transaction';

	public static function createByBiz( $param )
	{
		$cls = __CLASS__;
		$obj = new $cls();
		$obj->id = LoaderSvc::loadIdGenter()->create(self::ID_OBJ);
		$obj->ctime = date('Y-m-d H:i:s');
		$obj->utime = date('Y-m-d H:i:s');
		$obj->orderid = strlen($param['orderid']) > 0 ? $param['orderid'] : 'H'.$obj->id;
		$obj->tin = is_null($param['tin']) ? 0 : (double)$param['tin'];
		$obj->tout = is_null($param['tout']) ? 0 : (double)$param['tout'];
		$obj->fee = is_null($param['fee']) ? 0 : (double)$param['fee'];
		$obj->type = in_array($param['type'],self::$TYPE_OPTIONS) ? $param['type'] : self::TYPE_IN;
		$obj->datetime = is_null($param['datetime']) ? '0000-00-00 00:00:00' : $param['datetime'];
		$obj->uid = is_null($param['uid']) ? -1 : $param['uid'];
		$obj->remark = is_null($param['remark']) ? '' : $param['remark'];
		$obj->btype = in_array($param['btype'],self::$BTYPE_OPTIONS) ? $param['btype'] : self::BTYPE_UNKNOWN;
		$obj->state = in_array($param['state'],self::$STATE_OPTIONS) ? $param['state'] : self::STATE_INIT;
		$obj->sstate = in_array($param['sstate'],self::$SSTATE_OPTIONS) ? $param['sstate'] : self::SSTATE_UNSETTLED;
		return $obj;
	}
}
