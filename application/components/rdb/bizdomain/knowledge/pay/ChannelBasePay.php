<?php
abstract class ChannelBasePay
{

    /**
     * 支付请求
     *
     * @param  $transid            
     */
    public abstract function readyToPay($transid,$param);

    /**
     * 支付通知处理
     *
     * @param array $query            
     * @param array $post            
     * @param string $notify_data_stream            
     */
    public abstract function processNotify($query,$post,$notify_data_stream);
    /**
     * 支付前端页面返回处理
     *
     * @param array $query            
     * @param array $post            
     * @param string $notify_data_stream            
     */
    public abstract function processReturn($query,$post,$notify_data_stream);
}