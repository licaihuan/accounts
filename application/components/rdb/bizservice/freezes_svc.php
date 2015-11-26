<?php
class FreezesSvc
{/*{{{*/
	const OBJ = 'Freezes';
	static public function add($param)
	{
		$obj = Freezes::createByBiz($param);
		return self::getDao()->add($obj);
	}

	static public function getById($id = '0')
	{
		if (empty($id))
		{
			return null;
		}
		return self::getDao()->getById($id,self::OBJ);
	}

	static public function updateById($id,$param)
	{
		return self::getDao()->updateById($id,$param,self::OBJ);
	}

	static private function getDao()
	{
		return LoaderSvc::loadDao(self::OBJ);
	}

	static public function lists($request = array(),$options = array(),$export = false)
	{/*{{{*/
		$request_param = array();
		$sql_condition = array();
		$sql_param = array();

		if('' != $request['accountid']){
			$request_param[] = 'accountid=' . $request['accountid'];
			$sql_condition[] = '`accountid` = ?';
			$sql_param[]	 = $request['accountid'];
		}

		if('' != $request['cat']){
			$request_param[] = 'cat=' . $request['cat'];
			$sql_condition[] = '`cat` = ?';
			$sql_param[]	 = $request['cat'];
		}
		
		$option = array();
		$option['len'] = ($options['len'] > 0) ? $options['len'] : PER_PAGE;
		if($options['page'] > 0){
			$option['offset'] = ($options['page'] - 1) * $option['len'];
		}
		$option['orderby'] = isset($options['orderby']) ? $options['orderby'] : '';
		$results = self::getDao()->getRecord($sql_condition,$sql_param ,$option);
		if($export) return $results;

		$pages = '';
		$total = $results['total'];
		if($total > 0){
			$temp = stristr($options['baseurl'],'?');
			if($temp === false) $options['baseurl'] .= '?';
			$options['baseurl'] .= implode('&',$request_param);
			if(count($request_param)) $options['baseurl'] .= '&';
			$pages = Pager::getPageStr($options['page'],$option['len'],$total,$options['baseurl']);
		}
		$results['pages'] = $pages;
		//$results['offset'] = $option['offset'] + 1;
		//$results['len'] = $option['len'];
		//$results['pagenums'] = ceil($total / $option['len']);

		return $results;
	}/*}}}*/
	
	static public function getByAccountid($accountid,$state = '')
	{
		return self::getDao()->getByAccountid($accountid,$state = '');
	}

	static public function getFreezesByAccounts($accountid)
	{
		return self::getDao()->getFreezesByAccounts($accountid);
	}

	static public function getFreezesSum($accountid)
	{
		return self::getDao()->getFreezesSum($accountid);
	}

}

