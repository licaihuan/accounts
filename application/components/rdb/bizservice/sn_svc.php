<?php
class SnSvc
{
    const PADDING_LEN = 20;

	const CHANNEL_ID_LOCAL = '00';
    const CHANNEL_ID_UNKNOWN = '99';

    static $CHANNEL_OPTIONS = array(
        self::CHANNEL_ID_LOCAL,
        self::CHANNEL_ID_UNKNOWN,
    );

    static $CHANNEL_ID_CONF = array(
        self::CHANNEL_ID_LOCAL=>array(
            'NAME'=>'本地',
        ),
        self::CHANNEL_ID_UNKNOWN=>array(
            'NAME'=>'未知渠道',
        ),
    );
    
    const MODULE_ID_RECHARGE      = '01';
    const MODULE_ID_PAY           = '02';
    const MODULE_ID_CASH          = '03';
    const MODULE_ID_REFUND        = '04';
    const MODULE_ID_UNKNOWN       = '99';

     static $MODULE_OPTIONS = array(
        self::MODULE_ID_RECHARGE,
        self::MODULE_ID_PAY,
        self::MODULE_ID_CASH,
        self::MODULE_ID_REFUND,
        self::MODULE_ID_UNKNOWN,
    );

    static $MODULE_ID_CONF = array(
        self::MODULE_ID_RECHARGE=>array(
            'NAME'=>'充值',
        ),
        self::MODULE_ID_PAY=>array(
            'NAME'=>'支付',
        ),
        self::MODULE_ID_CASH=>array(
            'NAME'=>'取现',
        ),
        self::MODULE_ID_REFUND=>array(
            'NAME'=>'退款',
        ),
        self::MODULEL_ID_UNKNOWN=>array(
            'NAME'=>'未知业务',
        ),
    );

    static public function createSerialNum($channelid = '',$moduleid = '')
    {
        $OBJ = 'SN'.date('Y');
        $id = LoaderSvc::loadIdGenter()->create( $OBJ );
        $sn = '';
        if(in_array($channelid,self::$CHANNEL_OPTIONS)) $sn .= $channelid;
        else  $sn .= self::CHANNEL_ID_UNKNOWN;

        if(in_array($moduleid,self::$MODULE_OPTIONS)) $sn .= $moduleid;
        else  $sn .= self::MODULE_ID_UNKNOWN;

        for($i = 0;$i < (self::PADDING_LEN - strlen($id));$i++){
            $sn .='0';
        }
        $sn .= "{$sn}"."{$id}".substr(date('YmdHis'),2,12).mt_rand(1000,9999);
        return $sn;
    }
}
