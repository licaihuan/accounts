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
			case self::CHANNEL_BALANCE_PAY :
				$cls = 'ChannelBalancePay';
				break;
			case self::CHANNEL_ALIPAY_MOBILE :
				$cls = 'ChannelAlipayMobile';
				break;
			case self::CHANNEL_UNKNOWN :
			default:
				break;
		}
		
		return $cls;
	}
	
	private static function getRechargeChannelClass($channel)
	{
		$cls = '';
		switch($channel){
			case self::CHANNEL_ALIPAY_MOBILE :
				$cls = 'ChannelAlipayMobile';
				break;
			case self::CHANNEL_UNKNOWN :
			default:
				break;
		}
		return $cls;
	}
	
	static $RECHARGE_CHANNEL_OPTIONS = array(
		self::CHANNEL_ALIPAY_MOBILE,
	);

	static $PAY_CHANNEL_OPTIONS = array(
		self::CHANNEL_BALANCE_PAY,
		self::CHANNEL_ALIPAY_MOBILE,
	);

	static $CHANNEL_CONF = array(
		self::CHANNEL_BALANCE_PAY =>array(
			'NAME'=>'余额支付',
			'CODE'=>'BALANCE_PAY',
		),
		self::CHANNEL_ALIPAY_MOBILE =>array(
			'NAME'=>'支付宝移动支付',
			'CODE'=>'ALIPAY_MOBILE',
		),
		self::CHANNEL_UNKNOWN =>array(
			'NAME'=>'未知渠道',
			'CODE'=>'UNKNOWN',
		),
	);
	
	static function getChannelNameByChannelId($channelid)
	{
		return self::$CHANNEL_CONF["{$channelid}"]['NAME'];
	}
	
	static function getChannelCodeByChannelId($channelid)
	{
		return self::$CHANNEL_CONF["{$channelid}"]['CODE'];
	}
	
	public function getChannelIns($channel)
	{
		$cls = self::getPayChannelClass($channel);
		return LoaderSvc::loadPayChannel($cls);
	}
	
   
}
