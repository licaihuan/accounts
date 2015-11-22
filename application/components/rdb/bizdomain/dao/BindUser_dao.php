<?php
class BindUserDao extends BaseDao
{
	const TABLE_NAME = 'binduser';

	private function getTableName()
	{
		return self::TABLE_NAME;
	}
	
	public function getRecord($sql_condition = array(),$sql_param = array(),$options= array())
	{
		$sql = "select SQL_CALC_FOUND_ROWS * ";
		$sql.= "from ".self::getTableName()." ";
		if(!empty( $sql_condition ))
		{
			$sql.= 'where '. implode(' and ', $sql_condition);
		}
		if($options['orderby'])
		{
			$sql.= " order by ".$options['orderby']." ";
		}else
		{
			$sql.= " order by `id` desc ";
		}
		
		if($options['offset'] >=0 && $options['len'] > 0)
		{
			$sql.= ' limit '.intval($options['offset']).','.intval($options['len']);
		}elseif($options['len'] > 0){
			$sql.= ' limit '.intval($options['len']);
		}
		
		$results = array();
		$result = $this->getExecutor()->querys( $sql , $sql_param);
		
		$sql = "SELECT FOUND_ROWS() as `total`;";
		$rs = $this->getExecutor()->query( $sql );
		
		$results = array(
			'total'=>$rs['total'],
			'record'=>(is_array($result)?$result:array()),
		);
		return $results;
	}
	
	public function getUidByMobile($mobile)
	{
		$sql = "select `id` as uid ";
		$sql.= "from ".self::getTableName()." ";
		$sql.= "where `mobile`= ?";
		$result = $this->getExecutor()->query($sql,array($mobile));
		$uid = is_array($result) && $result['uid'] > 0 ? $result['uid'] : null;
		return $uid;   
	}
}