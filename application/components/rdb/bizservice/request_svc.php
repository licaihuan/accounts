<?php
class RequestSvc
{
	static public function Get($key,$default = '',$type = '')
	{
		$GET = array();

		switch ($type){
			case '':
			case 'string':
				$GET[$key] = (string) (isset($_GET[$key]) ? $_GET[$key] : $default);
				break;
			case 'int':
				$GET[$key] = intval(isset($_GET[$key]) ? $_GET[$key] : $default);
				break;
			default:
				$GET[$key] = $_GET[$key];
				break;
		}
		return $GET[$key];
	}
	
	static public function Post($key,$default = '',$type = '')
	{
		$POST = array();

		switch ($type){
			case '':
			case 'string':
				$POST[$key] = (string) (isset($_POST[$key]) ? $_POST[$key] : $default);
				break;
			case 'int':
				$POST[$key] = intval(isset($_POST[$key]) ? $_POST[$key] : $default);
				break;
			default:
				$POST[$key] = $_POST[$key];
				break;
		}
		return $POST[$key];
	}
	
	static public function Request($key,$default = '',$type = '')
	{
		$REQUEST = array();

		$REQUEST[$key] = isset($_GET[$key]) ? self::Get($key,$default,$type) : self::Post($key,$default,$type);
		return $REQUEST[$key];
	}

}