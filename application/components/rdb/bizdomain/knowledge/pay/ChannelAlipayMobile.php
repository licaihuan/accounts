<?php
class ChannelAlipayMobile extends ChannelBasePay
{

    public function processNotify ($query,$post,$notify_data_stream)
    {
   
    }

    public function readyToPay ($transid,$params = array())
    {
        
    }

    public function processReturn ($query,$post,$notify_data_stream)
    {
    }
}
