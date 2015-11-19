<?php

class AdminuserDao extends BaseDao
{

    const TABLE_NAME = 'adminuser';

    const Login_LOG_TABLE = 'loginlog';

    private function getTableName()
    {
        return self::TABLE_NAME;
    }

    public function getByEmail($account)
    {
        $sql = "select * ";
        $sql .= "from " . self::getTableName() . " ";
        $sql .= "where `email` = ? ";
        
        return $this->getExecutor()->query($sql, array(
            $account
        ));
    }

    public function getRecord($sql_condition = array(), $sql_param = array(), $options = array())
    {
        $sql = "select SQL_CALC_FOUND_ROWS * ";
        $sql .= "from " . self::getTableName() . " ";
        if (! empty($sql_condition)) {
            $sql .= 'where ' . implode(' and ', $sql_condition);
        }
        if ($options['orderby']) {
            $sql .= " order by " . $options['orderby'] . " ";
        } else {
            $sql .= " order by `id` desc ";
        }
        
        if ($options['offset'] >= 0 && $options['len'] > 0) {
            $sql .= ' limit ' . intval($options['offset']) . ',' . intval($options['len']);
        } elseif ($options['len'] > 0) {
            $sql .= ' limit ' . intval($options['len']);
        }
        
        $results = array();
        $result = $this->getExecutor()->querys($sql, $sql_param);
        
        $sql = "SELECT FOUND_ROWS() as `total`;";
        $rs = $this->getExecutor()->query($sql);
        
        $results = array(
            'total' => $rs['total'],
            'record' => (is_array($result) ? $result : array())
        );
        return $results;
    }

    public function delRecordById($id)
    {
        $sql = "delete ";
        $sql .= "from " . self::TABLE_NAME . " ";
        $sql .= "where id = ? ";
        $this->getExecutor()->exeNoQuery($sql, array(
            $id
        ));
    }

    public function loginLog($params)
    {
        $sql = "insert into ";
        $sql .= self::Login_LOG_TABLE . "(`id`,`uid`,`ip`,`state`,`time`) ";
        $sql .= "values(?,?,?,?,?) ";
        
        $sqlv = array(
            LoaderSvc::loadIdGenter()->create('loginlog'),
            $params['uid'],
            $params['ip'],
            $params['state'],
            time()
        );
        
        $this->getExecutor()->exeNoQuery($sql, $sqlv);
    }

    public function getLoginLogRecord($sql_condition = array(), $sql_param = array(), $options = array())
    {
        $sql = "select SQL_CALC_FOUND_ROWS * ";
        $sql .= "from " . self::Login_LOG_TABLE . " ";
        if (! empty($sql_condition)) {
            $sql .= 'where ' . implode(' and ', $sql_condition);
        }
        if ($options['orderby']) {
            $sql .= " order by " . $options['orderby'] . " ";
        } else {
            $sql .= " order by `id` desc ";
        }
        
        if ($options['offset'] >= 0 && $options['len'] > 0) {
            $sql .= ' limit ' . intval($options['offset']) . ',' . intval($options['len']);
        } elseif ($options['len'] > 0) {
            $sql .= ' limit ' . intval($options['len']);
        }
        
        $results = array();
        $result = $this->getExecutor()->querys($sql, $sql_param);
        
        $sql = "SELECT FOUND_ROWS() as `total`;";
        $rs = $this->getExecutor()->query($sql);
        
        $results = array(
            'total' => $rs['total'],
            'record' => (is_array($result) ? $result : array())
        );
        return $results;
    }

    public function getRidById($uid)
    {
        $sql = "SELECT `rid` FROM {$this->getTableName()} WHERE `id`=?";
        return $this->getExecutor()->query($sql, array(
            $uid
        ));
    }

    public function getAllUser()
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE `status`= ?";
        return $this->getExecutor()->querys($sql, array(
            Adminuser::STATUS_ENABLE
        ));
    }

    public function getByRid($rid)
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE `rid` = ? AND `status` =?;";
        return $this->getExecutor()->querys($sql, array(
            $rid,
            Adminuser::STATUS_ENABLE
        ));
    }
}
