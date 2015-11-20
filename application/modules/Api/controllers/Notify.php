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
    public function alipayCallAction()
    {/*{{{*/
    	
        $transid = RequestSvc::Request('transid','');
        $state = 'SUCC';
         
        $obj = TransactionSvc::getById($transid);
        if(!is_object($obj)) return;
        if($obj->state == Transaction::STATE_SUCC){
        	//AlipayHelper::responseSucc();
        }
        $uid = $obj->uid;
        $accountinfo = AccountsSvc::getByUidAndCat($uid);
        
       
        if(empty($accountinfo)){
        	LogSvc::fileLog('ERROR_'.__CLASS__.'.'.__FUNCTION__,"[uid:{$uid}]--[msg:账户不存在]");
        	exit(-1);
        }
        $accountid = $accountinfo['id'];
        if($state == AlipayHelper::PAY_STATE_SUCC){
        	$cat = Accountingrecord::CAT_RECHARGE;
        	$from = Accountingrecord::FROM_ALIPAY;
        	$remark = '测试';
        	
        	$params = array(
        		'out_trans_id'=>SnSvc::createSerialNum(),
        		'fee'=>0,
        		'amount'=>sprintf("%.2f",0.1),
        		'remark'=>$remark,
        	);
        	$ret = AccountsSvc::accountingProcess($params,$accountid,$transid,$cat,$from,$remark);
        	if($ret['e'] == ErrorSvc::ERR_OK){
        		AlipayHelper::responseSucc();
        	}
        	
        	var_dump($ret);die;
        }
        AlipayHelper::responseFail();
    }/*}}}*/



}
