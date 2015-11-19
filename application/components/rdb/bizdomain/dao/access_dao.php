<?php
class AccessDao extends BaseDao
{
	const TABLE_NAME = 'access';
	const NODE_TABLE = 'node';

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
	
	public function getNidsByRoleId($rid)
	{
	    $nids = array();
		
		$sql = "select * ";
		$sql.= "from ".self::TABLE_NAME." ";
		$sql.= "where `rid` = ? ";
		
		$results = $this->getExecutor()->querys( $sql, array( $rid ) );
		
		foreach($results as $row){
			$temprecord = NodeSvc::getById($row['nid']);
			if($temprecord->status == Node::STATUS_ENABLE){
				array_push($nids,$row['nid']);
			}
		}
		
		return $nids;
	}
	
	public function getNodeInfoByRole($params,$orderby = '')
	{
		$rid = $params['rid'];
		$pid = $params['pid'];
		
		$sql = "select A.*,B.rid,B.nid from ".self::NODE_TABLE." A,".self::getTableName()." B where A.id=B.nid and B.rid= ? and B.pid= ? and A.status= ? and A.ismenu='1' ";
		
		if($orderby != ''){
			$sql .= "order by $orderby ";
		}else{
			$sql .= 'order by `sort` asc ';
		}
		
		//echo $sql;die();
		$results = $this->getExecutor()->querys( $sql, array( $rid,$pid,Node::STATUS_ENABLE ) );
		return $results;
	}
	
	public function getByParams($params,$orderby = '')
	{
		$where = array();
		$v = array();
		foreach ($params as $key => $value) {
			$where[] = "`$key`= ? ";
			$v[] = $value;
		}
		$where = count($where)>0?implode("and ", $where):'1';
		
		$sql = "select * ";
		$sql.= "from ".self::getTableName()." where $where ";
		
		if($orderby != ''){
			$sql.= "order by $orderby ";
		}
		//echo $sql;die();
		//echo "select * from access where `pid`= '".$params['pid']."' and `rid`= '".$params['rid']."'";
	    //die();
		return $this->getExecutor()->querys( $sql ,$v);	
	}
	
	public function delByParams($params)
	{
		$fields = array();
		$v = array();
		foreach ($params as $key => $value) {
			$fields[] = "`$key`= ? ";
			$v[] = $value;
		}
		if(count($fields)){
			$fields = implode("and ", $fields);
			$sql = "delete ";
			$sql.= "from ".self::getTableName()." where $fields";
			//echo $sql;die();
			return $this->getExecutor()->exeNoQuery( $sql ,$v);	
		}
		return false;
	}
	
	
}