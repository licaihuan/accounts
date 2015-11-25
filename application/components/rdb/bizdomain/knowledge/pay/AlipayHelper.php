<?php
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
		

}
