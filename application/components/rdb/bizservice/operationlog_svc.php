<?php
class OperationlogSvc
{/*{{{*/
	const OBJ = 'Operationlog';
	static public function add( $param )
	{
		$obj = Operationlog::createByBiz( $param );
		return self::getDao()->add( $obj );
	}
	
	static public function getById( $id = '0' )
	{
		if ( empty( $id ) )
		{
			return null;
		}
		return self::getDao()->getById( $id , self::OBJ );
	}
	
	static public function getAll( $cls = self::OBJ )
	{
		return self::getDao()->getAll( $cls );
	}
	
	static public function updateById( $id, $param )
	{
		return self::getDao()->updateById( $id, $param, self::OBJ );
	}

	static private function getDao()
	{
		return LoaderSvc::loadDao( self::OBJ );
	}

	static public function delRecordById($id)
	{
		self::getDao()->delRecordById( $id );
	}
	
	static public function lists( $request = array(), $options = array(), $export = false)
	{/*{{{*/
		$request_param = array();
		$sql_condition = array();
		/*
		if( isset($request['rid']) && $request['rid']>0 )
		{
			$sql_condition[] = 'rid = ? '  ;
			$sql_param[]	 = $request['rid'];
			
			$options['baseurl'] .= 'rid='.$request['rid'];
		}*/
		
		//print_r($sql_condition);print_r($sql_param);exit;
		$option = array();
		$option['len'] = ($options['len'] > 0)?$options['len']:PER_PAGE;
		if($options['page'] > 0){
			$option['offset'] = ($options['page'] - 1) * $option['len'];
		}
		$option['orderby'] = isset($options['orderby']) ? $options['orderby'] : '';
		
		$results = self::getDao()->getRecord($sql_condition,$sql_param ,$option);
		
		$pages = '';
		$total = $results['total'];
		if($total > 0)
		{
			$temp = stristr($options['baseurl'],'?');
			if($temp != false && strlen($temp)>1){
				$options['baseurl'] .= '&';
			}
			$pages = Pager::getPageStr($options['page'],$option['len'],$total,$options['baseurl']);
		}
		$results['pages'] = $pages;
		$results['offset'] = $option['offset'] + 1;
		$results['len'] = $option['len'];
		$results['pagenums'] = ceil($total / $option['len']);
		
		return $results;
	}/*}}}*/

}