<?php
class MysqlSvc
{/*{{{*/
	const REMOTE_KEY = 'jljfdvcxf^&*(';

	static public function getLock($key,$timeout = 30)
	{
		$key = md5(self::REMOTE_KEY.'-'.$key);
		$sql = "SELECT GET_LOCK('{$key}', {$timeout}) AS get_lock";
		$result = LoaderSvc::loadExecutor()->query($sql);
		if(!empty($result)){
			return $result['get_lock'];
		}
		return null;
	}
	
	static public function releaseLock($key)
	{
		$key = md5(self::REMOTE_KEY.'-'.$key);
		$sql = "SELECT RELEASE_LOCK('{$key}') AS release_lock";
		$result = LoaderSvc::loadExecutor()->query($sql);
		if(!empty($result)){
			return $result['release_lock'];
		}
		return null;
	}
}