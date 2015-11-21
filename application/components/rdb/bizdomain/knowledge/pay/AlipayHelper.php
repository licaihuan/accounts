<?php
class AlipayHelper
{
	const PAY_STATE_SUCC = 'SUCC';
	const PAY_STATE_FAIL = 'FAIL';
	const PAY_STATE_PROCESSING = 'PROCESSING';

	public static function responseSucc()
	{
		exit('SUCCESS');
	}
	
	public static function responseFail()
	{
		exit('FAIL');
	}

}
