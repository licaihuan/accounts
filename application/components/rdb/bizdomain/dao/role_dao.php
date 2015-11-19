<?php

class RoleDao extends BaseDao
{
	const TABLE_NAME = 'role';

	private function getTableName()
	{
		return self::TABLE_NAME;
	}

	public function delRecordById($id)
	{
		$sql = "delete ";
		$sql .= "from " . self::TABLE_NAME . " ";
		$sql .= "where id = ? ";
		return $this->getExecutor()->exeNoQuery($sql, array($id));
	}
}