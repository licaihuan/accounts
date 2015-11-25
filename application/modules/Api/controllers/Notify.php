<?php

/**
 * @brief 异步通知处理
 */
class NotifyController extends ApibaseController
{
    /** 
    * @brief 充值成功回调
    * 
    * @author liuweidong
    * @date 2015-11-14
    * @return 
    */ 
    public function alipayAction()
    {/*{{{*/  	
    	$channelAlipayMobileObj = new ChannelAlipayMobile;
    	$ret = $channelAlipayMobileObj->processNotify ($_GET,$_POST,'php://input');
    	
    	if($ret['e'] != ErrorSvc::ERR_OK){
    		AlipayHelper::responseFail();
    	}
    	
    	if($ret['data']['state'] == AlipayHelper::TRADE_FINISHED){
    		AlipayHelper::responseSucc();
    	}
    	
    	$transid = $ret['data']['transid'];
    	$tradeno = $ret['data']['tradeno'];
		$amount = $ret['data']['amount'];
         
        $obj = TransactionSvc::getById($transid);
        if(!is_object($obj)) {
        	LogSvc::fileLog('Notify_'.__CLASS__.'.'.__METHOD__,"交易号[{$transid}]不存在");
        	AlipayHelper::responseFail();
        }
        if($obj->state == Transaction::STATE_SUCC){
        	AlipayHelper::responseSucc();
        }
        
        $_amount_ = $obj->tout > 0 ? $obj->tout ? $obj->tin;
        if($_amount_ != $amount){
        	LogSvc::fileLog('ERROR_'.__CLASS__.'.'.__FUNCTION__,"[amount:{$amount}]--[msg:金额不匹配]");
        	AlipayHelper::responseFail();
        }
        
        $uid = $obj->uid;
        $accountinfo = AccountsSvc::getByUidAndCat($uid);
        if(empty($accountinfo)){
        	LogSvc::fileLog('ERROR_'.__CLASS__.'.'.__FUNCTION__,"[uid:{$uid}]--[msg:账户不存在]");
        	AlipayHelper::responseFail();
        }
        $accountid = $accountinfo['id'];
        
        if($obj->btype == Transaction::BTYPE_RECHARGE){
        	$cat = Accountingrecord::CAT_RECHARGE;
	        if($ret['data']['state'] == AlipayHelper::TRADE_SUCCESS){
	        	$from = Accountingrecord::FROM_ALIPAY;
	        	$remark = '测试';
	        	
	        	$params = array(
	        		'tradeno'=>$tradeno,
	        		'channelid'=>PayChannel::CHANNEL_ALIPAY,
	        		'amount'=>$_amount_,
	        		'remark'=>$remark,
	        	);
	        	$ret = AccountsSvc::accountingProcess($params,$accountid,$transid,$cat,$from,$remark);
	        	if($ret['e'] == ErrorSvc::ERR_OK){
	        		AlipayHelper::responseSucc();
	        	}
	        }
        }
        //支付操作
        else{
        	//更新交易记录
        	$params = array(
        		'state'=>Transaction::STATE_SUCC,
        		'transno'=>$tradeno,
        		'channelid'=>PayChannel::CHANNEL_ALIPAY,
        	);
        	TransactionSvc::updateById($transid,$params);
        	AlipayHelper::responseSucc();
        }
        AlipayHelper::responseFail();
    }/*}}}*/



}
