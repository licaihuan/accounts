<?php

/**
 * @brief 账户接口
 */
class AccountsController extends ApibaseController
{
	/**
	 * @apiVersion 1.0.0
	 * @apiGroup Accounts
	 * @api {post} /api/accounts/preview 账户预览
	 *
	 * @apiSuccess (data) {number} balnace 可用余额
	 * @apiSuccess (data) {number} freezes 冻结总额
	 * @apiSuccess (data) {number} total   账户总额
	 *
	 * @apiUse mySuccArr
	 * @apiUse myErrRet
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
	 * @apiVersion 1.0.0
	 * @apiGroup Accounts
	 * @api {post} /api/accounts/transaction 交易明细
	 * @apiParam {Number} page 当前第几页
	 * @apiParam {Number} len  分页显示条目数
	 *
	 * @apiSuccess (data) 略
	 *
	 * @apiUse mySuccArr
	 * @apiUse myErrRet
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
	 * @apiVersion 1.0.0
	 * @apiGroup Accounts
	 * @api {post} /api/accounts/details 帐务明细
	 * @apiParam {Number} page 当前第几页
	 * @apiParam {Number} len  分页显示条目数
	 *
	 * @apiSuccess (data) 略
	 *
	 * @apiUse mySuccArr
	 * @apiUse myErrRet
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
	 * @apiVersion 1.0.0
	 * @apiGroup Accounts
	 * @api {post} /api/accounts/recharge 账户充值
	 * @apiParam {Number} orderid 订单号
	 * @apiParam {Number} amount  充值金额，保留两位小数，精确到分（如：25.06）
	 *paydata
	 * @apiSuccess (data) {number} transid  支付交易号
	 * @apiSuccess (data) {paydata[]} paydata 明细
	 * @apiSuccess (data) {paydata[]} ..
	 *
	 * @apiUse mySuccArr
	 * @apiUse myErrRet
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
	 * @apiVersion 1.0.0
	 * @apiGroup Accounts
	 * @api {post} /api/accounts/cash 取现申请
	 * @apiParam {Number} amount  取现金额
	 *
	 * @apiSuccess (data) 略
	 *
	 * @apiUse mySuccArr
	 * @apiUse myErrRet
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
