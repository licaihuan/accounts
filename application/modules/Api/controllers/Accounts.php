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
        
        $amount = sprintf("%.2f",(RequestSvc::Request('amount',0)));
   		if($amount <= 0){
    		$ret['errno'] = '50103';
    		$this->outPut($ret);
    	}
    	
    	if($accountinfo['balance'] < $amount){
    		$ret['errno'] = '50107';
    		$this->outPut($ret);
    	}
    	
    	$orderid = SnSvc::createSerialNum(SnSvc::CHANNEL_ID_MOBILE,SnSvc::MODULE_ID_CASH);
	    $params = array(
	    	'orderid'=>$orderid,
	    	'btype'=>Transaction::BTYPE_CASH,
	    	'uid'=>$uid,
	    	'type'=>Transaction::TYPE_OUT,
	    	'amount'=>$amount,
	    );
	    $r = TransactionSvc::addTrans($params);
		if(!$r){
      	    $data = array_merge($response,$params);
      		LogSvc::fileLog('Freezes_Transid_Create_Fail['.__CLASS__.'_'.'__FUNCTION__'.']',$data);
    		$ret['errno'] = '50104';
    		$this->outPut($ret);
		}
    	$accountinfo = AccountsSvc::getByUidAndCat($uid);
        $accountid = $accountinfo['id'];
    	$transid = $r;
    	$cat = Accountingrecord::CAT_CASH;
    	$remark = '客户取现';
    
        $response = AccountsSvc::freezes($accountid,$amount,$cat,$remark,$transid);
        if($response['e'] != ErrorSvc::ERR_OK){
        	 TransactionSvc::setProcessResult($orderid,Transaction::STATE_FAIL);
        	 $ret['errno'] = '50108';
        	 $ret['msg'] = ErrorSvc::getMsg($response['e']);
        }else{
        	 TransactionSvc::setProcessResult($orderid,Transaction::STATE_PROCESSING);
        	 $ret['msg'] = '取现申请提交成功，等待审核处理';
        }
        
        $this->outPut($ret);
    }/*}}}*/
    
    public function testAction()
    {
    	/*
    	$freezesid = RequestSvc::Request('freezesid');
    	$ret = AccountsSvc::unfreeze($freezesid);
    	var_dump($ret);
    	*/
    	
    	$uid = '18310293307';
    	//$accountinfo = AccountsSvc::transfers(8,9,1);
    	//var_dump($accountinfo);
    	SysinfoSvc::log($uid);
    	
    	
    }
    
    
    
    
    
    
    

}
