<?php
function auto_load($classname)
{/*{{{*/
		$entity_ucfirst = "";
        $classpath = array(
		"BaseDao" => "/home/liuweidong/project/ycc-pj/application/components/rdb/bizdomain/dao/base_dao.php",
		"SysinfoDao" => "/home/liuweidong/project/ycc-pj/application/components/rdb/bizdomain/dao/sysinfo_dao.php",
		"Sysinfo" => "/home/liuweidong/project/ycc-pj/application/components/rdb/bizdomain/entity/sysinfo.php",
		"ErrorSvc" => "/home/liuweidong/project/ycc-pj/application/components/rdb/bizservice/error_svc.php",
		"LoaderSvc" => "/home/liuweidong/project/ycc-pj/application/components/rdb/bizservice/loader_svc.php",
		"LogSvc" => "/home/liuweidong/project/ycc-pj/application/components/rdb/bizservice/log_svc.php",
		"MysqlSvc" => "/home/liuweidong/project/ycc-pj/application/components/rdb/bizservice/mysql_svc.php",
		"RequestSvc" => "/home/liuweidong/project/ycc-pj/application/components/rdb/bizservice/request_svc.php",
		"RequestfilterSvc" => "/home/liuweidong/project/ycc-pj/application/components/rdb/bizservice/requestfilter_svc.php",
		"SysinfoSvc" => "/home/liuweidong/project/ycc-pj/application/components/rdb/bizservice/sysinfo_svc.php",
		"UtlsSvc" => "/home/liuweidong/project/ycc-pj/application/components/rdb/bizservice/utls_svc.php",
		"LogObject" => "/home/liuweidong/project/ycc-pj/application/components/rdb/integration/LogObject.php",
		"Captcha" => "/home/liuweidong/project/ycc-pj/application/components/rdb/integration/captcha.php",
		"DBCache" => "/home/liuweidong/project/ycc-pj/application/components/rdb/integration/dbcache.php",
		"Entity" => "/home/liuweidong/project/ycc-pj/application/components/rdb/integration/entity.php",
		"IDGenter" => "/home/liuweidong/project/ycc-pj/application/components/rdb/integration/id_genter.php",
		"ObjectFinder" => "/home/liuweidong/project/ycc-pj/application/components/rdb/integration/object_finder.php",
		"Pager" => "/home/liuweidong/project/ycc-pj/application/components/rdb/integration/pager.php",
		"MysqliSessDriver" => "/home/liuweidong/project/ycc-pj/application/components/rdb/integration/session.php",
		"SessionSvc" => "/home/liuweidong/project/ycc-pj/application/components/rdb/integration/session.php",
		"SimpleDB" => "/home/liuweidong/project/ycc-pj/application/components/rdb/integration/simple_db.php",
		"SimpleObject" => "/home/liuweidong/project/ycc-pj/application/components/rdb/integration/simple_object.php",
		"SQLExecutor" => "/home/liuweidong/project/ycc-pj/application/components/rdb/integration/sql_executor.php",
		"Timer" => "/home/liuweidong/project/ycc-pj/application/components/rdb/integration/timer.php",
		);
        if (isset($classpath[$classname]))
        {
            include($classpath[$classname]);
        }
}/*}}}*/
spl_autoload_register('auto_load');
