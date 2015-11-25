<?php
class ChannelAlipayMobile extends ChannelBasePay
{

    public function processNotify ($query,$post,$notify_data_stream)
    {
    	$ret = array(
    		'e'=>ErrorSvc::ERR_PAY_VERIFY_SIGN,
    		'data'=>array(
    			'state'=>AlipayHelper::TRADE_UNKNOWN,
    		),
    	);
    	
    	LogSvc::fileLog('Notify_'.__CLASS__.'_'.__FUNCTION__,$post);
   		$alipayNotify = new AlipayNotify(AlipayConfig::init());
   		
		$verify_result = $alipayNotify->verifyNotify();
		
		if($verify_result){
			//商户订单号
			$out_trade_no = $_POST['out_trade_no'];
			//支付宝交易号
			$trade_no = $_POST['trade_no'];
			$trade_status = $_POST['trade_status'];
			if(in_array($trade_status,array(AlipayHelper::TRADE_FINISHED,AlipayHelper::TRADE_SUCCESS))){
				$state = $trade_status;
			}
			
			$ret['e'] = ErrorSvc::ERR_OK;
			$ret['data'] = array(
				'transid'=>$out_trade_no,
				'tradeno'=>$trade_no,
				'state'=>$state,
			);
		}
		return $ret;
    }

    public function readyToPay ($transid,$params = array())
    {
        
    }

    public function processReturn ($query,$post,$notify_data_stream)
    {
    }
}
