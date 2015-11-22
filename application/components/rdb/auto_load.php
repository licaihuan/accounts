<?php
function auto_load($classname)
{/*{{{*/
		$entity_ucfirst = "";
        $classpath = array(
		"BindUserDao" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/dao/BindUser_dao.php",
		"AccountingrecordDao" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/dao/accountingrecord_dao.php",
		"AccountsDao" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/dao/accounts_dao.php",
		"BaseDao" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/dao/base_dao.php",
		"FreezesDao" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/dao/freezes_dao.php",
		"SysinfoDao" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/dao/sysinfo_dao.php",
		"TransactionDao" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/dao/transaction_dao.php",
		"BindUser" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/entity/BindUser.php",
		"Access" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/entity/access.php",
		"Accountingrecord" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/entity/accountingrecord.php",
		"Accounts" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/entity/accounts.php",
		"Adminuser" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/entity/adminuser.php",
		"Freezes" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/entity/freezes.php",
		"Node" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/entity/node.php",
		"Operationlog" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/entity/operationlog.php",
		"Role" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/entity/role.php",
		"Sysinfo" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/entity/sysinfo.php",
		"Transaction" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/entity/transaction.php",
		"PayChannel" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/knowledge/PayChannel.php",
		"AlipayHelper" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/knowledge/pay/AlipayHelper.php",
		"ChannelAlipayMobile" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/knowledge/pay/ChannelAlipayMobile.php",
		"ChannelBalancePay" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/knowledge/pay/ChannelBalancePay.php",
		"ChannelBasePay" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizdomain/knowledge/pay/ChannelBasePay.php",
		"BindUserSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/BindUser_svc.php",
		"AccountingrecordSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/accountingrecord_svc.php",
		"AccountsSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/accounts_svc.php",
		"EntitySvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/entity_svc.php",
		"".$entity_ucfirst."Svc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/entity_svc.php",
		"ErrorSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/error_svc.php",
		"FreezesSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/freezes_svc.php",
		"LoaderSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/loader_svc.php",
		"LogSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/log_svc.php",
		"MysqlSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/mysql_svc.php",
		"RequestSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/request_svc.php",
		"RequestfilterSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/requestfilter_svc.php",
		"SnSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/sn_svc.php",
		"SysinfoSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/sysinfo_svc.php",
		"TransactionSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/transaction_svc.php",
		"UtlsSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/bizservice/utls_svc.php",
		"LogObject" => "/home/liuweidong/htdocs/accounts/application/components/rdb/integration/LogObject.php",
		"Captcha" => "/home/liuweidong/htdocs/accounts/application/components/rdb/integration/captcha.php",
		"DBCache" => "/home/liuweidong/htdocs/accounts/application/components/rdb/integration/dbcache.php",
		"Entity" => "/home/liuweidong/htdocs/accounts/application/components/rdb/integration/entity.php",
		"IDGenter" => "/home/liuweidong/htdocs/accounts/application/components/rdb/integration/id_genter.php",
		"ObjectFinder" => "/home/liuweidong/htdocs/accounts/application/components/rdb/integration/object_finder.php",
		"Pager" => "/home/liuweidong/htdocs/accounts/application/components/rdb/integration/pager.php",
		"MysqliSessDriver" => "/home/liuweidong/htdocs/accounts/application/components/rdb/integration/session.php",
		"SessionSvc" => "/home/liuweidong/htdocs/accounts/application/components/rdb/integration/session.php",
		"SimpleDB" => "/home/liuweidong/htdocs/accounts/application/components/rdb/integration/simple_db.php",
		"SimpleObject" => "/home/liuweidong/htdocs/accounts/application/components/rdb/integration/simple_object.php",
		"SQLExecutor" => "/home/liuweidong/htdocs/accounts/application/components/rdb/integration/sql_executor.php",
		"Timer" => "/home/liuweidong/htdocs/accounts/application/components/rdb/integration/timer.php",
		);
        if (isset($classpath[$classname]))
        {
            include($classpath[$classname]);
        }
}/*}}}*/
spl_autoload_register('auto_load');
