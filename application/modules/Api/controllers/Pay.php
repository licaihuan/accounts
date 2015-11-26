<?php

/**
 * @brief 支付
 */
class PayController extends ApibaseController
{

   /**
	 * @apiVersion 1.0.0
	 * @apiGroup Pay
	 * @api {post} /api/pay/do 支付请求
	 * @apiParam {Number} orderid 订单号
	 * @apiParam {Number} amount  充值金额，保留两位小数，精确到分（如：25.06）
	 * @apiParam {String} [paychannel] 支付渠道 (1-余额支付,2-支付宝移动支付)
	 * @apiParam {String} [btype]  业务类别 (16-支付信息费,17-支付运输费)
	 * 
	 * 
	 * @apiSuccess (data) {Number} transid  支付交易号
	 * @apiSuccess (data) {[]} data  渠道相关数据
	 * 
	 * @apiUse mySuccArr
	 * @apiUse myErrRet
	 */
    public function doAction()
    {/*{{{*/
        $ret = $this->initOutPut();
    	$orderid = RequestSvc::Request('orderid',SnSvc::createSerialNum());
    	$amount = sprintf("%.2f",(RequestSvc::Request('amount',0)));
    	
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
    	if(!in_array($paychannel,PayChannel::$PAY_CHANNEL_OPTIONS)){
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
        if($payChannelObj instanceof ChannelBalancePay){
        	$accountinfo = AccountsSvc::getByUidAndCat($this->uid);
        	$accountid = $accountinfo['id'];
        	$errno = $payChannelObj->pay($accountid,$transid,$amount);
        	$msg = ErrorSvc::getMsg($errno);
        	$ret['msg'] = $msg;
        }
        //第三方支付
        else{
        	$res = $payChannelObj->readyToPay($transid);
        	$ret['data'] = array(
	    		'transid'=>$r,
	    		'data'=>$res['data'],
    		);
        }
    
    	$this->outPut($ret);
    }/*}}}*/
    
     /**
	 * @apiVersion 1.0.0
	 * @apiGroup Pay
	 * @api {get} /api/pay/channel 获取支付渠道
	 *
	 * @apiSuccess (data[]) {Number} channelid 渠道号
	 * @apiSuccess (data[]) {String} code  渠道代码
	 * @apiSuccess (data[]) {String} name  渠道名称
	 * 
	 * @apiUse mySuccArr
	 * @apiUse myErrRet
	 */
    public function channelAction()
    {
    	$ret = $this->initOutPut();
    	$ret['data'] = array(
    		array(
    			'channelid'=>PayChannel::CHANNEL_ALIPAY_MOBILE,
    			'code'=>PayChannel::getChannelCodeByChannelId(PayChannel::CHANNEL_ALIPAY_MOBILE),
    			'name'=>PayChannel::getChannelNameByChannelId(PayChannel::CHANNEL_ALIPAY_MOBILE),
    		),
    	);
    	
    	//余额支付
    	$this->outPut($ret);
    }
   

}
