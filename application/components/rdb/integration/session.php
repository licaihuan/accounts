<?php
session_cache_limiter('private,max-age=10800');
function sess_open( $spath = '', $sname = '' )
{/*{{{*/
	return MysqliSessDriver::sessOpen();
}/*}}}*/

function sess_close()
{/*{{{*/
	return MysqliSessDriver::sessClose();
}/*}}}*/

function sess_read( $skey )
{/*{{{*/
	return MysqliSessDriver::sessRead( $skey );
}/*}}}*/

function sess_write( $skey, $value )
{/*{{{*/
	return MysqliSessDriver::sessWrite( $skey, $value );
}/*}}}*/

function sess_destroy( $skey )
{/*{{{*/
	return MysqliSessDriver::sessDestroy( $skey );
}/*}}}*/

function sess_gc( $max_time = '' )
{/*{{{*/
	return MysqliSessDriver::sessGc();
}/*}}}*/


class MysqliSessDriver
{/*{{{*/
	static $HOST = '';
	static $NAME = '';
	static $USER = '';
	static $PASS = '';
	static $PORT = '';
	static $LIFE = '';
	static $CONN = '';

	const TABLE = 'sessions';

	public function __construct( $life = '7200' )
	{/*{{{*/
		self::$HOST = $_SERVER['ENV_DB_HOST'];
		self::$NAME = $_SERVER['ENV_DB_NAME'];
		self::$USER = $_SERVER['ENV_DB_USER'];
		self::$PASS = $_SERVER['ENV_DB_PASS'];
		self::$PORT = $_SERVER['ENV_DB_PORT'];
		self::$LIFE = $life;
	}/*}}}*/

	public function init()
	{/*{{{*/
		//var_dump(UtlsSvc::isReadonly());
		if(!UtlsSvc::isReadonly())
		{
			session_set_save_handler( 'sess_open', 'sess_close', 'sess_read', 'sess_write', 'sess_destroy', 'sess_gc' );
		}
	}/*}}}*/

	public static function sessOpen()
	{/*{{{*/
		if ( !self::$CONN = mysqli_connect( self::$HOST , self::$USER, self::$PASS , self::$NAME , self::$PORT) )
		{
			die( 'Could not connect: '. mysqli_connect_error() );
		}

		return true;
	}/*}}}*/

	public static function sessClose()
	{/*{{{*/
        if ( is_resource( self::$CONN ) )
        {
		    mysqli_close( self::$CONN );
        }
	}/*}}}*/

	public static function sessRead( $skey )
	{/*{{{*/
		$sql = "select value ";
		$sql.= "from ".self::TABLE." ";
		$sql.= "where skey = '".$skey."' ";
		$sql.= "and expiry > '".time()."' ";
		$row = mysqli_query( self::$CONN,$sql );

		if ( list( $result ) = mysqli_fetch_row( $row ) )
		{
			return $result;
		}
		return false;
	}/*}}}*/

	public static function sessWrite( $skey, $value )
	{/*{{{*/
		$skey   = mysqli_real_escape_string(self::$CONN , $skey );
		$value  = mysqli_real_escape_string( self::$CONN ,$value );
		$expiry = time() + self::$LIFE;

		$sql = "insert into ".self::TABLE." ";
		$sql.= "values ( '".$skey."', '".$expiry."', '".$value."' ) ";
		$row = mysqli_query( self::$CONN,$sql);;
		if ( $row )
		{
			return $row;
		}

		$sql = "update ".self::TABLE." set ";
		$sql.= "expiry = '".$expiry."', value = '".$value."' ";
		$sql.= "where skey = '".$skey."' ";
		return mysqli_query( self::$CONN,$sql);
	}/*}}}*/

	public static function sessDestroy( $skey )
	{/*{{{*/
		$sql = "delete from ".self::TABLE." ";
		$sql.= "where skey = '".$skey."' ";
		return mysqli_query( self::$CONN,$sql);
	}/*}}}*/

	public static function sessGc()
	{/*{{{*/
		$sql = "delete from ".self::TABLE." ";
		$sql.= "where expiry < ".time()." ";
		$row = mysqli_query( self::$CONN,$sql);
		return mysqli_affected_rows( self::$CONN );
	}/*}}}*/
}/*}}}*/


class SessionSvc
{/*{{{*/
    private $driver   = null;
    private $is_start = false;

    public function __construct( $name = '', $driver = null )
    {/*{{{*/
        $this->driver = $driver;
        $this->driver->init();
        session_name( $name );
    }/*}}}*/

    public function set( $k, $v )
    {/*{{{*/
        $this->ensureStart();
        $_SESSION[$k] = $v;
        return true;
    }/*}}}*/

    public function get( $k )
    {/*{{{*/
        $this->ensureStart();
        if ( array_key_exists( $k, $_SESSION ) )
        {
            return $_SESSION[$k];
        }
        return '';
    }/*}}}*/

    public function getAll()
    {/*{{{*/
        $this->ensureStart();
        return $_SESSION;
    }/*}}}*/

    public function destroy( $k )
    {/*{{{*/
        $this->ensureStart();
        unset( $_SESSION[$k] );
        return true;
    }/*}}}*/

    public function destroyAll()
    {/*{{{*/
        $this->ensureStart();
        $_SESSION = array();
        return true;
    }/*}}}*/

    public function setSid( $sid )
    {/*{{{*/
        session_id( $sid );
    }/*}}}*/

    public function getSid()
    {/*{{{*/
        $this->ensureStart();
        return session_id();
    }/*}}}*/

    private function ensureStart()
    {/*{{{*/
        if ( $this->is_start )
        {
            return;
        }
        session_start();
        $this->is_start = true;
    }/*}}}*/
}/*}}}*/