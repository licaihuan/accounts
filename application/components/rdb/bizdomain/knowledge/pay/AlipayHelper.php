<?php
require_once(__DIR__."/alipay/lib/alipay_core.function.php");
require_once(__DIR__."/alipay/lib/alipay_rsa.function.php");

class AlipayHelper
{	
	const TRADE_FINISHED = 'TRADE_FINISHED';
	const TRADE_SUCCESS = 'TRADE_SUCCESS';
	
	const TRADE_UNKNOWN = 'TRADE_UNKNOWN';
	
	public static function responseSucc()
	{
		exit('success');
	}
	
	public static function responseFail()
	{
		exit('fail');
	}
	
	public static function sign($data,$private_key_path)
	{
		$data = argSort($data);
		$data = createLinkstring($data);
		return rsaSign($data, $private_key_path);
	}
		

}
