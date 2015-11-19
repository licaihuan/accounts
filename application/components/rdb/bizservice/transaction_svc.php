<?php
class TransactionSvc
{/*{{{*/
	const OBJ = 'Transaction';
	static public function add($param)
	{
		$obj = Transaction::createByBiz($param);
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

		if(isset($request['id']) && $request['id']>1)
		{
			$request_param[] = '`id`=' . $request['id'];
			$sql_condition[] = '`id` = ? ';
			$sql_param[] = $request['id'];
		}
		if('' != $request['fee']){
			$request_param[] = 'fee=' . $request['fee'];
			$sql_condition[] = '`fee` = ?';
			$sql_param[]	 = $request['fee'];
		}
		if('' != $request['type']){
			$request_param[] = 'type=' . $request['type'];
			$sql_condition[] = '`type` = ?';
			$sql_param[]	 = $request['type'];
		}
		if('' != $request['daystart'])
		{
			$request_param[] = 'daystart=' . $request['daystart'];

			$sql_condition[] = '`datetime` >= ?';
			$sql_param[]	 = $request['daystart'].' '.'00:00:00';
		}
		
		if('' != $request['dayend'])
		{
			$request_param[] = 'dayend=' . $request['dayend'];

			$sql_condition[] = '`datetime` <= ?';
			$sql_param[]	 = $request['dayend'].' '.'23:59:59';
		}

		if('' != $request['uid']){
			$request_param[] = 'uid=' . $request['uid'];
			$sql_condition[] = '`uid` = ?';
			$sql_param[]	 = $request['uid'];
		}

		if('' != $request['btype']){
			$request_param[] = 'btype=' . $request['btype'];
			$sql_condition[] = '`btype` = ?';
			$sql_param[]	 = $request['btype'];
		}

		if('' != $request['state']){
			$request_param[] = 'state=' . $request['state'];
			$sql_condition[] = '`state` = ?';
			$sql_param[]	 = $request['state'];
		}

		if('' != $request['sstate']){
			$request_param[] = 'sstate=' . $request['sstate'];
			$sql_condition[] = '`sstate` = ?';
			$sql_param[]	 = $request['sstate'];
		}

		$option = array();
		$option['len'] = ($options['len'] > 0) ? $options['len'] : PER_PAGE;
		if($options['page'] > 0){
			$option['offset'] = ($options['page'] - 1) * $option['len'];
		}
		$option['orderby'] = isset($options['orderby']) ? $options['orderby'] : '';
		
		$results = self::getDao()->getRecord($sql_condition,$sql_param,$options);
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
		$results['pagenums'] = ceil($total / $option['len']);

		return $results;
	}/*}}}*/
	
	static public function getByUid($uid)
	{
		return self::getDao()->getByUid($uid);
	}

	static public function getByOrderid($orderid)
	{
		return self::getDao()->getByOrderid($orderid);
	}

	static public function getTransByUid($uid,$params = array(),$option = array())
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
