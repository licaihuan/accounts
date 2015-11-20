<?php

class Bootstrap extends Yaf_Bootstrap_Abstract{

    private $_config;
    private $_memcache;

    //test
    public function _initConfig() {
        date_default_timezone_set('PRC');
        ini_set("yaf.use_spl_autoload",1);
        $this->_config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $this->_config);
    }

    public function _initErrors(){
        if($this->_config->application->showErrors){
            ini_set('display_errors',"On");
            ini_set('error_reporting',E_ALL & ~E_NOTICE & ~ E_STRICT);
        }else{
            error_reporting (0);
            ini_set('display_errors','Off');
        }
    }

    public function _initRegisterAutoload()
    {/*{{{*/
        $components_dir = Yaf_Registry::get('config')->application->components_dir;
        $components = Yaf_Registry::get('config')->application->components;
        $components_arr = explode(',', $components);
        foreach ($components_arr as $component) {
            include_once($components_dir . $component . '/auto_load.php');
        }
    }/*}}}*/

    public function _initFilerRequest(){
        $_GET = RequestfilterSvc::addslashes_deep($_GET);
        $_POST = RequestfilterSvc::addslashes_deep($_POST);
        $_COOKIE = RequestfilterSvc::addslashes_deep($_COOKIE);

        $_GET = RequestfilterSvc::htmlspecialcharsRecursive($_GET);
        $_POST = RequestfilterSvc::htmlspecialcharsRecursive($_POST);
        $_COOKIE = RequestfilterSvc::htmlspecialcharsRecursive($_COOKIE);
    }

    /*
     *注册memcache对象
     * useage:
     * set: Yaf_Registry::get('memcache')->set("abc","1111111111");
     * get: Yaf_Registry::get('memcache')->get("abc");
     */
    /*
    public function _initMemcaches(){
        ini_set('memcache.hash_strategy', 'consistent');

        $cacheconfig = explode("|",$this->_config->application->memcacheconfig);
        $memcache = new Memcache;
        foreach($cacheconfig as $cache){
            list($host,$port) = explode(":",$cache);
            $memcache->addServer($host, $port);
        }
        $this->_memcache = $memcache;
        Yaf_Registry::set('memcache', $memcache);
    }
    
    public function _initSessionHandler(){
        $config = Yaf_Registry::get('config');
        session_name($config->application->session_name);
        session_set_save_handler(
            new SessionHandler(Yaf_Registry::get('memcache'), $config->application->session_expire),
            true
        );
        session_set_cookie_params($config->application->session_expire);
        session_start();
    }*/

    public function _initSwitch(){
        Yaf_Application::app()->getDispatcher()->disableview();
        Yaf_Application::app()->getDispatcher()->autoRender(false);
        Yaf_Application::app()->getDispatcher()->flushInstantly(false);
    }

}
