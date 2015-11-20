<?php
use \rdb\integration;

class LogSvc
{/*{{{*/
    static $_box = array();

    public static function getSqlLog()
    {/*{{{*/
        return self::getLogTpl( 'sql' );
    }/*}}}*/

    public static function getFinanceSqlLog()
    {/*{{{*/
        return self::getLogTpl( 'financesql' );
    }/*}}}*/

    public static function getBizErrLog()
    {/*{{{*/
        return self::getLogTpl( 'biz_err', true );
    }/*}}}*/

    public static function getSysErrLog()
    {/*{{{*/
        return self::getLogTpl( 'sys_err', true );
    }/*}}}*/

    public static function getQuerySecurityLog()
    {/*{{{*/
        return self::getLogTpl( 'admin_query_security', true );
    }/*}}}*/

    public static function getPayNotifyLog()
    {/*{{{*/
        return self::getLogTpl( 'pay_notify', true );
    }/*}}}*/

    public static function get( $name ,$only = false)
    {/*{{{*/
        return self::getLogTpl( $name ,$only);
    }/*}}}*/

    private static function getLogTpl( $type, $only = false )
    {/*{{{*/
        $obj_name = '_'.$type.'_log';
        if ( array_key_exists( $type, self::$_box ) )
        {
            return self::$_box[$type];
        }

        $fname = Yaf_Registry::get('config')->application->log_path.'/'.$type.'.log';
        
    	if(isset($_SERVER['SHELL']))
    	{
    	    $fname.= '_shell';
    	}
    	if ( !$only )
        {
            $fname.= '.'.date('Ymd').'.log';
        }
        self::$_box[$type] = new LogObject( $fname );
        return self::$_box[$type];
    }/*}}}*/

	
    public static function fileLog($name,$log)
    {
    	if(is_array($log)){
    		$log = var_export($log,TRUE);
    	}
    	$log = '['.date('Y-m-d H:i:s').']'.PHP_EOL.$log;
    	$log .= PHP_EOL;
        $fname = Yaf_Registry::get('config')->application->log_path.'/'.$name.'.log';
    	file_put_contents($fname.'-'.date('Ymd').'.log',$log,FILE_APPEND);
    }

    public static function info($log)
    {
	   self::get('Info_')->log($log);
    }

    public static function warn($log)
    {
	   self::get('Warn_')->log($log);
    }

    public static function debug($log)
    {
	   self::get('Debug_')->log($log);
    }

    public static function error($log)
    {
        self::get('Error_')->log($log);
    }






}
