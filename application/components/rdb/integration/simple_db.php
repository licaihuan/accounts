<?php
class SimpleDB
{
	public static $_db=null;
	public static $_conf=null;
	public function __construct($conf)
	{
		$this->_conf = $conf;
		$this->connect();
	}

	private function connect()
	{
		$conf = $this->_conf;
		if(!isset($conf['port']))
		{
			$conf['port'] = 3306;
		}
		$this->_db = mysql_connect($conf['host'].':'.$conf['port'], $conf['user'],$conf['pass']);
        mysql_select_db($conf['name'],$this->_db);
        mysql_query("SET NAMES utf8",$this->_db);
	}

	public function query($sql)
	{
		if(!isset($this->_db) || @mysql_ping($this->_db) === false)
		{
			$this->connect();
		}
		mysql_query("SET NAMES utf8",$this->_db);
		return mysql_query($sql,$this->_db);
	}
	function select($sql)
	{
		if(!isset($this->_db) || @mysql_ping($this->_db) === false)
		{
			echo "reconnect\n";
			$this->connect();
		}

		$result=array();
		mysql_query("SET NAMES utf8",$this->_db);
		$re = mysql_query($sql,$this->_db);
		while($row=mysql_fetch_assoc($re))
		{
			$result[]=$row;
		}
		return $result;
	}
	function close()
	{
		mysql_close($this->_db);
	}
	function get_error()
	{
		return mysql_error();
	}

}
?>