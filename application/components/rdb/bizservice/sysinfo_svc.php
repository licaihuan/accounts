<?php
class SysinfoSvc
{/*{{{*/
	const OBJ = 'Sysinfo';
	
	static private function add( $param )
	{
		$obj = Sysinfo::createByBiz( $param );
		return self::getDao()->add( $obj );
	}

	static public function log( $desc )
	{
		self::add(array('desc'=>$desc));
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
		
		if(isset($request['keyword']) && strlen($request['keyword']) > 0)
		{
			$sql_condition[] = "`desc` like '%".$request['keyword']."%' ";
			$options['baseurl'] .= 'keyword='.$request['keyword'];
		}
		
		$option = array();
		$option['len'] = ($options['len'] > 0) ? $options['len'] : PER_PAGE;
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
		$results['page'] = $option['page'];
		$results['pagenums'] = ceil($total / $option['len']);
		
		return $results;
	}/*}}}*/

	static public function handleFatal()
	{
	    $error = error_get_last();
	    if (isset($error['type']))
	    {
	        switch ($error['type'])
	        {
	            case E_ERROR :
	            case E_PARSE :
	            case E_DEPRECATED:
	            case E_CORE_ERROR :
	            case E_COMPILE_ERROR :
	                $message = $error['message'];
	                $file = $error['file'];
	                $line = $error['line'];
	                $log = "$message ($file:$line)\nStack trace:\n";
	                $trace = debug_backtrace();
	                foreach ($trace as $i => $t)
	                {
	                    if (!isset($t['file']))
	                    {
	                        $t['file'] = 'file-unknown';
	                    }
	                    if (!isset($t['line']))
	                    {
	                        $t['line'] = 0;
	                    }
	                    if (!isset($t['function']))
	                    {
	                        $t['function'] = 'function-unknown';
	                    }
	                    $log .= "#$i {$t['file']}({$t['line']}): ";
	                    if (isset($t['object']) && is_object($t['object']))
	                    {
	                        $log .= get_class($t['object']) . '->';
	                    }
	                    $log .= "{$t['function']}()\n";
	                }
	                if (isset($_SERVER['REQUEST_URI']))
	                {
	                    $log .= '[QUERY] ' . $_SERVER['REQUEST_URI'];
	                }
	                //error_log($log,0);
	                self::log($log);
	        }
	    }
	}


}
