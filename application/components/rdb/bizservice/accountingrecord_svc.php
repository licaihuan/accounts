<?php
class AccountingrecordSvc
{/*{{{*/
	const OBJ = 'Accountingrecord';
	static public function add($param)
	{
		$obj = Accountingrecord::createByBiz($param);
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

		if('' != $request['remark']){
			$request_param[] = 'remark=' . $request['remark'];
			$sql_condition[] = '`remark` = ?';
			$sql_param[]	 = $request['remark'];
		}
		if('' != $request['from']){
			$request_param[] = 'from=' . $request['from'];
			$sql_condition[] = '`from` = ?';
			$sql_param[]	 = $request['from'];
		}
		if('' != $request['daystart']){
			$request_param[] = 'daystart=' . $request['daystart'];
			$sql_condition[] = '`datetime` >= ?';
			$sql_param[]	 = $request['daystart'].' 00:00:00';
		}
		if('' != $request['dayend']){
			$request_param[] = 'dayend=' . $request['dayend'];
			$sql_condition[] = '`datetime` <= ?';
			$sql_param[]	 = $request['dayend'].' 23:59:59';
		}

		if('' != $request['accountid']){
			$request_param[] = 'accountid=' . $request['accountid'];
			$sql_condition[] = '`accountid` = ?';
			$sql_param[]	 = $request['accountid'];
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
	
	static public function getByUid($uid)
	{
		return self::getDao()->getByUid($uid);
	}

	static public function getByTransid($transid)
	{
		return self::getDao()->getByTransid($transid);
	}

	static public function getAccountsRecordByUid($uid,$params = array(),$option = array())
        {
                $request = array(
                        'uid'=>$uid,
                );

                $options = [];
		$page = intval($option['page']);
		$page = $page >= 1 ? $page : 1;
                $len = intval($option['len']);
		$len = $len > 0 ? $len : 10; 
		
                $options['len'] = $len;
                $options['offset'] = ($page - 1) * len;
                if(isset($option['orderby'])) $options['orderby'] = $option['orderby'];
                $request = array_merge($request,$params);
                $results = self::lists($request,$options,true);
                return $results;
        }
	
}
