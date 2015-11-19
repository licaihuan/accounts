<?php
class NodeDao extends BaseDao
{
	const TABLE_NAME = 'node';

	private function getTableName()
	{
		return self::TABLE_NAME;
	}

	public function delRecordById($id)
	{
		$sql = "delete ";
		$sql.= "from ".self::TABLE_NAME." ";
		$sql.= "where id = ? ";
		$this->getExecutor()->exeNoQuery( $sql, array( $id ) );
	}
	
	public function getByParams($params)
	{
		$where = array();
		$v = array();
		foreach ($params as $key => $value) {
			$where[] = "`$key`= ? ";
			$v[] = $value;
		}
		$where = count($where)>0?implode("and ", $where):'1';
		
		$sql = "select * ";
		$sql.= "from ".self::getTableName()." where $where";
		//echo $sql;die();
		return $this->getExecutor()->querys( $sql ,$v);	
	}
	
	public function getNodeIdByParam($params)
	{
		$name   = $params['name'];
		$action = $params['action'];
		$type   = $params['type'];
		
		$sql = "select * ";
		$sql.= "from ".self::getTableName()." where `name`= ? and `action`= ? and `type`= ? ";
		
		$rs = $this->getExecutor()->query( $sql ,array($name,$action,$type) );	
		
		$nid = -1;
		if(count($rs) > 0){
			$nid = $rs['id'];
		}
		return $nid;
	
	}
}