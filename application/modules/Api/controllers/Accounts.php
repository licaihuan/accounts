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
    * @brief 获取充值交易号
    * 
    * @author liuweidong
    * @date 2015-11-14
    * @return 
    */ 
    public function rechargeAction()
    {/*{{{*/   
    	$ret = $this->initOutPut();
    	$orderid = RequestSvc::Request('orderid','');
    	$amount = floatval(RequestSvc::Request('amount',0));
    	if(empty($orderid)){
    		$ret['errno'] = '50102';
    		$this->outPut($ret);
    	}
    	
    	if($amount == 0){
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

}
