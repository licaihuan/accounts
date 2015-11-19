<?php
class LoaderSvc
{/*{{{*/
    private static $_db		= null;
    //private static $_db_r	= null;

    //private static $_db_f	= null;

    private static function init()
    {/*{{{*/
        self::setExecutorConf();		
	   //self::setSlaveExecutorConf();
    }/*}}}*/

    private static function setExecutorConf()
    {/*{{{*/
        self::$_db = array(
			'host' => Yaf_Registry::get('config')->DB_MASTER->host,
			'port' => Yaf_Registry::get('config')->DB_MASTER->port,
			'user' => Yaf_Registry::get('config')->DB_MASTER->user,
			'pass' => Yaf_Registry::get('config')->DB_MASTER->pass,
			'name' => Yaf_Registry::get('config')->DB_MASTER->name,
		);
    }/*}}}*/

    private static function setSlaveExecutorConf()
    {/*{{{*/
        self::$_db_r = array(
	    );
    }/*}}}*/

    public static function loadExecutor()
    {/*{{{*/
        $obj = ObjectFinder::find( 'SQLExecutor' );
        if ( is_object( $obj ) )
        {
            return $obj;
        }

        self::init();

        if ( is_null( self::$_db ) )
        {
            return null;
        }

        $obj = new SQLExecutor( self::$_db );
        $obj->regLogObj( LogSvc::getSqlLog() );
        ObjectFinder::register( 'SQLExecutor', $obj );
        return $obj;
    }/*}}}*/
    public static function loadSlaveExecutor()
    {/*{{{*/
        $obj = ObjectFinder::find( 'SQLExecutorSlave' );
        if ( is_object( $obj ) )
        {
            return $obj;
        }

        self::init();

        if ( is_null( self::$_db_r ) )
        {
            return null;
        }

        $obj = new SQLExecutor( self::$_db_r );
        $obj->regLogObj( LogSvc::getSqlLog() );
        ObjectFinder::register( 'SQLExecutorSlave', $obj );
        return $obj;
    }/*}}}*/
    
    public static function loadIdGenter()
    {/*{{{*/
        $obj = ObjectFinder::find( 'IDGenter' );
        if ( is_object( $obj ) )
        {
            return $obj;
        }

        $obj = new IDGenter( self::loadExecutor() );
        ObjectFinder::register( 'IDGenter', $obj );
        return $obj;
    }/*}}}*/

    public static function loadDao( $entity )
    {/*{{{*/
        $cls = $entity.'Dao';
        $dao = ObjectFinder::find( $cls );
        if ( is_object( $dao ) )
        {
            return $dao;
        }

        $dao = new $cls();
        ObjectFinder::register( $cls, $dao );
        return $dao;
    }/*}}}*/

    public static function regSess( $name )
    {/*{{{*/
        $obj = new MysqliSessDriver();
        $svc = new SessionSvc( $name, $obj );
        ObjectFinder::register( 'SessSvc', $svc );
    }/*}}}*/

    public static function loadSmarty()
    {/*{{{*/
        $obj = ObjectFinder::find('Smarty');
        if ( is_object( $obj ) )
        {
            return $obj;
        }

        $obj = new Smarty();
        $obj->setTemplateDir(Yaf_Registry::get('config')->smarty->template_dir);
        $obj->setCompileDir(Yaf_Registry::get('config')->smarty->compile_dir);
        //LoaderSvc::loadSmarty()->setConfigDir(ROOT_PATH.'/src/application/view/configs/');
        $obj->setCacheDir(Yaf_Registry::get('config')->smarty->cache_dir);
        //LoaderSvc::loadSmarty()->force_compile = true;
        //LoaderSvc::loadSmarty()->debugging = true;
        $obj->caching = false;
        //LoaderSvc::loadSmarty()->cache_lifetime = 120;
        $obj->left_delimiter = Yaf_Registry::get('config')->smarty->left_delimiter;
        $obj->right_delimiter = Yaf_Registry::get('config')->smarty->right_delimiter;

        $obj->assign('_STATIC_',Yaf_Registry::get('config')->smarty->_assigns._STATIC_);
        $obj->assign('_ROOT_URL_',Yaf_Registry::get('config')->smarty->_assigns._ROOT_URL_);

        ObjectFinder::register( 'Smarty', $obj );
        return $obj;
    }/*}}}*/
	
    public static function loadSess()
    {/*{{{*/
        return ObjectFinder::find('SessSvc');
    }/*}}}*/

    public static function loadDBCache()
    {/*{{{*/
    	$obj = ObjectFinder::find('DBCache');
        if ( is_object( $obj ) )
        {
            return $obj;
        }
    	$obj = new DBCache();
        ObjectFinder::register( 'DBCache', $obj );
        return $obj;
    }/*}}}*/

	

}/*}}}*/
