<?php

//错误代码表
//20502
//服务级错误（1为系统级错误） 05为服务务模块代码 02具体错误代码
class Error
{/*{{{*/
    //系统级错误码
    public static function error4SysCode()
    {/*{{{*/
        return array(
            '0'     =>     'succ',
            '10001' => '参数错误',
            '10002' => '数据格式错误',
            '10003' => '操作错误',
        );
    }/*}}}*/

    //服务级错误码
    public static function error4SerCode()
    {/*{{{*/
        //01为needer
        //02为mearchant
        //03为order
        //04为admin
        //05为gold
        return array(
            '20101' => '需求服务错误',
            '20102' => '司机信息或者需求不存在(或者已完成)',
            '20103' => '已经被抢光',
            '20501' => '余额不足，操作错误',
        );
    }/*}}}*/

    //服务级错误码
    public static function error5SerCode()
    {/*{{{*/
        //01为needer
        //02为mearchant
        //03为order
        //04为admin
        //05为gold
        return array(
            '50101' => '用户未登录',
         	'50102' => '订单号不能为空',
        	'50103' => '输入金额有误',
        	'50104' => '创建交易单号失败',
        	'50105' => '支付渠道不存在',
        
        	'50106' => '业务类别错误',
        	'50107' => '账户余额不足',
        	'50108' => '账务处理失败',

        );
    }/*}}}*/

    public static function getMsg($code, $type = 'sys')
    {/*{{{*/
        if (!$code) {
            return '';
        }
        //$arr = array_merge_recursive(self::error4SysCode(),self::error4SerCode());
        $arr = self::error4SysCode() + self::error4SerCode() + self::error5SerCode();

        return isset($arr[$code]) ? $arr[$code] : '';
    }/*}}}*/

    public static function getErrMsg($code, $data = '')
    {/*{{{*/
        $ret = array();
        $arr = self::error4SysCode() + self::error4SerCode();
        $msg = isset($arr[$code]) ? $arr[$code] : '';
        if ($msg) {
            $ret['errno'] = $code;
            $ret['msg'] = $msg;
            $ret['data'] = $data;

            return $ret;
        }
    }/*}}}*/
}/*}}}*/
