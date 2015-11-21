<?php
class PayChannel
{
	const CHANNEL_BALANCE_PAY = 1;
	const CHANNEL_ALIPAY  = 2;
	
	const CHANNEL_BALANCE_PAY_CLS = 'ChannelBalancePay';
	private static function getPayChannelClass($channel)
	{
		$cls = '';
		switch($channel){
			case self::CHANNEL_BALANCE :
				$cls = 'ChannelBalancePay';
				break;
			case self::CHANNEL_ALIPAY :
				$cls = 'ChannelAliPay';
				break;
			default:
				break;
		}
		return $cls;
	}

	static $CHANNEL_OPTIONS = array(
		self::CHANNEL_BALANCE_PAY,
		self::CHANNEL_ALIPAY,
	);

	static $STATE_CONF = array(
		self::CHANNEL_BALANCE_PAY =>array('NAME'=>'余额支付'),
		self::CHANNEL_ALIPAY =>array('NAME'=>'支付宝'),
	);
	
	public function getChannelIns($channel)
	{
		return LoaderSvc::loadPayChannel($channel);
	}
	
   
}
