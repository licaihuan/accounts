<?php

/**
 * @brief 账户接口
 */
class PayController extends ApibaseController
{

    /** 
    * @brief 支付交易
    * 
    * @author liuweidong
    * @date 2015-11-14
    * @return 
    */ 
    public function doAction()
    {/*{{{*/
        $ret = $this->initOutPut();
    	$orderid = RequestSvc::Request('orderid','');
    	$amount = sprinf("%.2f",(RequestSvc::Request('amount',0)));
    	
    	$paychannel = RequestSvc::Request('paychannel','');
    	$btype = RequestSvc::Request('btype','');
    	
    	if(empty($orderid)){
    		$ret['errno'] = '50102';
    		$this->outPut($ret);
    	}
    	
    	if($amount <= 0){
    		$ret['errno'] = '50103';
    		$this->outPut($ret);
    	}
    	
    	if(!in_array($paychannel,PayChannel::$CHANNEL_OPTIONS)){
    		$ret['errno'] = '50105';
    		$this->outPut($ret);
    	}
    	
    	$btypes = array(
    		Transaction::BTYPE_PAY_INFO_FEE,
    		Transaction::BTYPE_PAY_SHIPPING_FEE,
    	);
    	
    	if(!in_array($btype,$btypes)){
    		$ret['errno'] = '50106';
    		$this->outPut($ret);
    	}

    	$params = array(
    		'orderid'=>$orderid,
    		'btype'=>$btype,
    		'uid'=>$this->uid,
    		'type'=>Transaction::TYPE_OUT,
    		'amount'=>$amount,
    	);
    	$r = TransactionSvc::addTrans($params);
    	if(!$r){
    		$ret['errno'] = '50104';
    		$this->outPut($ret);
    	}
    	$transid = $r;
        $payChannelObj = PayChannel::getChannelIns($paychannel);
        //账户余额支付
        if($payChannelObj instanceof 'ChannelBalancePay'){
        	$accountinfo = AccountsSvc::getByUidAndCat($this->uid);
        	$accountid = $accountinfo['id'];
        	$errno = $payChannelObj->pay($accountid,$transid,$amount);
        	$msg = ErrorSvc::getMsg($errno);
        	$ret['msg'] = $msg;
        }
        //第三方支付
        else{
        	/*
        	$response = $payChannelObj->readyToPay($transid,$amount);
        	*/
        	$ret['data'] = array(
	    		'transid'=>$r,
	    		'paydata'=>[],
    		);
        }
    
    	$this->outPut($ret);
    }/*}}}*/
   

}
