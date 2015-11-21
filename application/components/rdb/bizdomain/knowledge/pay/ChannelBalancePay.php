<?php
class ChannelBalancePay
{
	public function pay($accountid,$transid,$amount)
	{
		$cat = Accountingrecord::CAT_BALANCE_PAY;
        $from = Accountingrecord::FROM_SYS;
        $remark = '测试-余额支付';
        
        $params = array(
        	'out_trans_id'=>SnSvc::createSerialNum(SnSvc::CHANNEL_ID_LOCAL,SnSvc::MODULE_ID_BALANCE_PAY),
        	'fee'=>0,
        	'amount'=>sprintf("%.2f",$amount),
        	'remark'=>$remark,
        );
        $ret = AccountsSvc::accountingProcess($params,$accountid,$transid,$cat,$from,$remark);
        return $ret['e'];
	}
	
   
}
