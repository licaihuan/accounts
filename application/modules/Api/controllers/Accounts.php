<?php

/**
 * @brief 账户接口
 */
class AccountsController extends ApibaseController
{
    /** 
    * @brief 账户预览
    * 
    * @author liuweidong
    * @date 2015-11-14
    * @return 
    */ 
    public function previewAction()
    {/*{{{*/
        $uid = $this->uid;
        $ret = $this->initOutPut();
        $accountinfo = AccountsSvc::getAccountsInfo($uid);
        $ret['data'] = $accountinfo;
        $this->outPut($ret);
    }/*}}}*/
    
    /** 
    * @brief 交易明细
    * 
    * @author liuweidong
    * @date 2015-11-14
    * @return 
    */ 
    public function transactionAction()
    {/*{{{*/
        $uid = $this->uid;
        $option = array(
        	'page'=>RequestSvc::Request('page',1),
        	'len'=>RequestSvc::Request('len',10),
        );
        $results = TransactionSvc::getTransByUid($uid,array(),$option);
        $ret['data'] = $results;
        $this->outPut($ret);
    }/*}}}*/
    
    /** 
    * @brief 帐务明细
    * 
    * @author liuweidong
    * @date 2015-11-14
    * @return 
    */ 
    public function detailsAction()
    {/*{{{*/
        $uid = $this->uid;
        $option = array(
        	'page'=>RequestSvc::Request('page',1),
        	'len'=>RequestSvc::Request('len',10),
        );
        $results = AccountingrecordSvc::getAccountsRecordByUid($uid,array(),$option);
        $ret['data'] = $results;
        $this->outPut($ret);
    }/*}}}*/
    

    /** 
    * @brief 账户充值
    * 
    * @author liuweidong
    * @date 2015-11-14
    * @return 
    */ 
    public function rechargeAction()
    {/*{{{*/   
    	$ret = $this->initOutPut();
    	$orderid = RequestSvc::Request('orderid','');
    	$amount = sprinf("%.2f",(RequestSvc::Request('amount',0)));
    	if(empty($orderid)){
    		$ret['errno'] = '50102';
    		$this->outPut($ret);
    	}
    	
    	if($amount <= 0){
    		$ret['errno'] = '50103';
    		$this->outPut($ret);
    	}
    	
    	//$sn = SnSvc::createSerialNum(SnSvc::CHANNEL_ID_MOBILE,SnSvc::MODULE_ID_RECHARGE);
    	//echo $sn;die;
    	
    	$params = array(
    		'orderid'=>$orderid,
    		'btype'=>Transaction::BTYPE_RECHARGE,
    		'uid'=>$this->uid,
    		'type'=>Transaction::TYPE_IN,
    		'amount'=>$amount,
    	);
    	$r = TransactionSvc::addTrans($params);
    	if(!$r){
    		$ret['errno'] = '50104';
    		$this->outPut($ret);
    	}
    	
    	$ret['data'] = array(
    		'transid'=>$r,
    		'paydata'=>[],
    	);
    	$this->outPut($ret);
    }/*}}}*/
    
    
    /** 
    * @brief 取现申请
    * 
    * @author liuweidong
    * @date 2015-11-14
    * @return 
    */ 
    public function cashAction()
    {/*{{{*/
        $uid = $this->uid;
        $ret = $this->initOutPut();
        $accountinfo = AccountsSvc::getAccountsInfo($uid);
        $amount = sprinf("%.2f",(RequestSvc::Request('amount',0)));
   		if($amount <= 0){
    		$ret['errno'] = '50103';
    		$this->outPut($ret);
    	}
    	
    	if($accountinfo['balance'] < $amount){
    		$ret['errno'] = '50107';
    		$this->outPut($ret);
    	}
    	
    	$sn = SnSvc::createSerialNum(SnSvc::CHANNEL_ID_MOBILE,SnSvc::MODULE_ID_CASH);
    	$params = array(
    		'orderid'=>$sn,
    		'btype'=>Transaction::BTYPE_CASH,
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
    	$cat = Accountingrecord::CAT_CASH;
    	$remark = '客户取现';
        $response = AccountsSvc::freezes($accountid,$amount,$cat,$remark,$transid);
        if($response['e'] != ErrorSvc::Err_OK){
        	 $msg = ErrorSvc::getMsg($response['e']);
        	 $ret['errno'] = '50108';
        	 $ret['msg'] = $msg;
        }
        $this->outPut($ret);
    }/*}}}*/
    
    
    
    

}
