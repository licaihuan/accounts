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
    	$res = array(
    		'e'=>ErrorSvc::ERR_OK,
    		'data'=>array(),
    	);
        $transObj = TransactionSvc::getById($transid);
        
        $alipayconfig = AlipayConfig::init();
        $res['data'] = array(
        	'service'=>'mobile.securitypay.pay',
        	'partner'=>$alipayconfig['partner'],
            '_input_charset'=>$alipayconfig['input_charset'],
        	'sign_type'=>$alipay_config['sign_type'],
        	'notify_url'=>Yaf_Registry::get('config')->alipay->notify_url,
        	'out_trade_no'=>$transid,
        	'subject'=>'测试移动支付',
        	'payment_type'=>1,
        	'seller_id'=>'myj@dongyijt.com',
        	'total_fee'=>$transObj->tout,
        	'body'=>'测试',
        );
        
        $sign = AlipayHelper::sign($res['data'],$alipayconfig['private_key_path']);
        $res['data']['sign'] = $sign;
        return $res;
    }

    public function processReturn ($query,$post,$notify_data_stream)
    {
    }
}
