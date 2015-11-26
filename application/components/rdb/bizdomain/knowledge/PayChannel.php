<?php
class PayChannel
{
	const CHANNEL_UNKNOWN = -1;
	const CHANNEL_BALANCE_PAY = 1;
	const CHANNEL_ALIPAY_MOBILE  = 2;
	
	const CHANNEL_BALANCE_PAY_CLS = 'ChannelBalancePay';
	private static function getPayChannelClass($channel)
	{
		$cls = '';
		switch($channel){
			case self::CHANNEL_BALANCE :
				$cls = 'ChannelBalancePay';
				break;
			case self::CHANNEL_ALIPAY_MOBILE :
				$cls = 'ChannelAlipayMobile';
				break;
			case self::CHANNEL_UNKNOWN,
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
		self::CHANNEL_ALIPAY_MOBILE =>array('NAME'=>'支付宝移动支付'),
		self::CHANNEL_UNKNOWN =>array('NAME'=>'未知渠道'),
	);
	
	public function getChannelIns($channel)
	{
		return LoaderSvc::loadPayChannel($channel);
	}
	
   
}
