<?php
class ErrorSvc
{/*{{{*/
    const SPR = '{SPR}';
    const SHOW_KEY = 'd4fc3d9f';
    const SHOW_TIMEOUT = '60';

	const TEMPLATE_TYPE_DEFAULT       = '0';
	const TEMPLATE_TYPE_JS            = '1';
	const TEMPLATE_TYPE_JSON		  = '2';

    const ERR_OK                  = '9999';
	const ERR_SYSTEM_ERROR        = '9000';
	const ERR_LOGININFO_ERROR     = '9001';
	const ERR_AUTHINFO_ERROR      = '9002';
    const ERR_NO_LOGIN            = '9003';
    const ERR_UID                 = '9004';
    const ERR_MYSQL_GET_LOCK      = '9005';
	const ERR_BUSY				  = '9007';
	const ERR_NOT_FOUND			  = '9008';


	const USER_CLIENT_OK          = '999';
	const COMM_CLIENT_OK          = '999';

    const ERR_PARAM_EMPTY         = '1003';
    const ERR_PARAM_TYPE          = '1004';
	const ERR_PARAM_MONEY         = '1005';
	const ERR_PARAM_UID           = '1006';
	const ERR_PARAM_INVALID       = '1007';
    const ERR_INSERT_FAIL         = '1008';

    //账户部分
    const ERR_ACCOUNTS_NOT_FOUND  = '2000';
    const ERR_TRANSACTION_NOT_FOUND = '2001';
    const ERR_TRANSACTION_RESPONSE_REPEAT = '2002';
    const ERR_ACCOUNTS_BALANCE_SHORTAGE   = '2003';
    const ERR_RESPONSE_NOT_MACHED  = '2004';
    
    const ERR_UNFREEZE_FAIL  = '2005';
    const ERR_ACCOUNTING_PROCESS_FAIL = '2006';
    
    const ERR_BIND_USER_FAIL = '3000';
    const ERR_BIND_USER_EXIST = '3001';
    
    //支付部分
    const ERR_PAY_VERIFY_SIGN = '4001';

    static $MSG = array(
		self::ERR_INSERT_FAIL			=>'写入错误',
		self::ERR_BUSY					=>'系统繁忙',
		self::ERR_NOT_FOUND				=>'数据不存在',
				
		self::ERR_ACCOUNTS_NOT_FOUND       		 	  =>'账户不存在',
		self::ERR_TRANSACTION_NOT_FOUND    		 	  =>'交易记录不存在',
		self::ERR_TRANSACTION_RESPONSE_REPEAT   	  =>'操作回放被拒绝',
		self::ERR_ACCOUNTS_BALANCE_SHORTAGE   		  =>'账户余额不足',
		self::ERR_RESPONSE_NOT_MACHED				  =>'第三方响应数据不匹配',
		
		self::ERR_UNFREEZE_FAIL						  =>'解冻资金失败',
		self::ERR_ACCOUNTING_PROCESS_FAIL			  =>'帐务处理失败',
		self::ERR_BIND_USER_FAIL                      =>'绑定用户失败',
		self::ERR_BIND_USER_EXIST					  =>'绑定用户已存在',
		
		self::ERR_PAY_VERIFY_SIGN					  =>'验证签名失败',
		
    );

    //提现支付密码错误
    static $REQUIRE_RESETPWD = array( '1704', '1608', '1309', );


	static public function desc( $result )
	{/*{{{*/
		if($result['e'])
		{
			$result['m'] =  self::getMsg($result['e']);
		}
		return $result;
	}/*}}}*/

    static public function getMsg( $errno )
    {/*{{{*/
        if ( empty( $errno ) )
        {
            return '';
        }
        if ( !array_key_exists( $errno, self::$MSG ) )
        {
            return '未知错误';
        }

        return self::$MSG[$errno];
    }/*}}}*/

	static public function showMsg($result,$url='')
	{
		UtlsSvc::showMsg(ErrorSvc::getMsg($result['e']),$url);
	}

    static public function show( $errno , $templates_type = 1 )
    {/*{{{*/
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $templates_type==self::TEMPLATE_TYPE_JSON)
		{
			echo json_encode(array('e'=>$errno,'m'=>self::getMsg($errno)));
			exit;
		}else
		{
			UtlsSvc::reDirect( '/error/', self::formatShowParam( $errno , $templates_type ) );
		}
    }/*}}}*/

	static public function apiOutput($param=array())
    {/*{{{*/
		echo json_encode(
			array(
				'result'=>array(
							'status'=>array(
										'code'=>$param['code'],
										'msg'=>$param['msg']
											)
								),
				'data'=>$param['list']
				)
			);
		exit;
    }/*}}}*/

	static public function api($e,$data = array())
    {/*{{{*/
		//header('Content-type: application/json');
		echo json_encode(
			array(
				'result'=>array(
					'status'=>array(
								'code'=>$e,
								'msg'=>self::getMsg($e)
							)
						),
				'data'=>$data,
				)
			);
		exit;
    }/*}}}*/

    static private function formatShowParam( $e , $templates_type )
    {/*{{{*/
        $t = time();
        $s = self::makeShowSign( $e, $t );
        return array( 'e' => $e, 't' => $t, 's' => $s , 'tpl' => $templates_type );
    }/*}}}*/

    static private function makeShowSign( $e, $t )
    {/*{{{*/
        return md5( $e.'|'.$t.'|'.self::SHOW_KEY );
    }/*}}}*/

    static public function checkShowSign( $e, $t, $s )
    {/*{{{*/
        if ( '' == $s )
        {
            return false;
        }

        if ( self::makeShowSign( $e, $t ) == $s )
        {
            return true;
        }

        return false;
    }/*}}}*/

    static public function writeLog( $errno, $input, $log_name, $lock_key = '' )
    {/*{{{*/
        if ( '' != $lock_key )
        {
			if(is_array($lock_key))
			{
				foreach($lock_key as $key)
				MysqlSvc::releaseLock( $key );
			}else
			{
				MysqlSvc::releaseLock( $lock_key );
			}
        }
        LogSvc::get( $log_name )->log( self::formatLogInfo( $errno, $input ) );
        return array( 'e' => $errno,'m'=>ErrorSvc::$MSG[$errno]);
    }/*}}}*/

    static public function writeXmlLog( $errno, $input, $log_name, $lock_key = '' )
    {/*{{{*/
        LogSvc::get( $log_name )->log( self::formatLogInfo( $errno, $input ) );
        return array( 'result_code' => $errno );
    }/*}}}*/

    static private function formatLogInfo( $errno, $input )
    {/*{{{*/
        $result = self::SPR.'errno='.$errno;
		if(is_array($input))
		{
			foreach ( $input as $k => $v )
			{
				if ( is_array( $v) || is_object( $v ) )
				{
					$v = serialize( $v );
				}
				$result.= self::SPR.$k.'='.$v;
			}
		}elseif( is_object($input) )
		{
			$result = serialize($input);
		}else
		{
			$result = $input;
		}
        return $result;
    }/*}}}*/
}/*}}}*/
?>