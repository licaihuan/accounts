<?php

class AdminuserSvc
{

    /* {{{ */
    const OBJ = 'Adminuser';

    const LOGIN_SUCC = 1;

    const LOGIN_FAIL = 0;

    static public function add($param)
    {
        $obj = Adminuser::createByBiz($param);
        return self::getDao()->add($obj);
    }

    static public function getById($id = '0')
    {
        if (empty($id)) {
            return null;
        }
        return self::getDao()->getById($id, self::OBJ);
    }

    static public function getByEmail($accout)
    {
        if (empty($accout)) {
            return null;
        }
        return self::getDao()->getByEmail($accout);
    }

    static public function updateById($id, $param)
    {
        return self::getDao()->updateById($id, $param, self::OBJ);
    }

    static public function getByUid($uid = '0')
    {
        return self::getDao()->getByUid($uid);
    }

    static private function getDao()
    {
        return LoaderSvc::loadDao(self::OBJ);
    }

    static public function lists($request = array(), $options = array(), $export = false)
    { /* {{{ */
        $request_param = array();
        $sql_condition = array();
        $sql_param = array();
        if ('' != $request['rid']) {
            $sql_condition[] = 'rid = ? ';
            $sql_param[] = $request['rid'];
            
            $request_param[] = '`rid`=' . $request['rid'];
        }
        
        if ('' != $request['name']) {
            $sql_condition[] = 'name = ? ';
            $sql_param[] = $request['name'];
            $request_param[] = '`name`=' . $request['name'];
        }
        
        $option = array();
        $option['len'] = ($options['len'] > 0) ? $options['len'] : PER_PAGE;
        if ($options['page'] > 0) {
            $option['offset'] = ($options['page'] - 1) * $option['len'];
        }
        $option['orderby'] = isset($options['orderby']) ? $options['orderby'] : '';
        
        $results = self::getDao()->getRecord($sql_condition, $sql_param, $option);
        
        $pages = '';
        $total = $results['total'];
        $total = $results['total'];
        if ($total > 0) {
            $temp = stristr($options['baseurl'], '?');
            if ($temp === false)
                $options['baseurl'] .= '?';
            $options['baseurl'] .= implode('&', $request_param);
            if (count($request_param))
                $options['baseurl'] .= '&';
            $pages = Pager::getPageStr($options['page'], $option['len'], $total, $options['baseurl']);
        }
        $results['pages'] = $pages;
        $results['offset'] = $option['offset'] + 1;
        $results['len'] = $option['len'];
        $results['pagenums'] = ceil($total / $option['len']);
        
        return $results;
    }

    /* }}} */
    static public function auth($uid)
    {
        $rs = false;
        $record = self::getById($uid);
        if (! is_object($record))
            return $rs;
        
        $rid = $record->rid;
        
        $nids = AccessSvc::getNidsByRoleId($rid);
        
        // var_dump($nids);
        $except = array();
        
        $c = $_SERVER['_C_'];
        $a = $_SERVER['_A_'];
        $t = in_array($_SERVER['_T_'], $except) ? '' : $_SERVER['_T_'];
        
        $params = array(
            'name' => $c,
            'action' => $a,
            'type' => $t
        );
        $nid = NodeSvc::getNodeIdByParam($params);
        if (in_array($nid, $nids)) {
            $rs = true;
        }
        return $rs;
    }

    static public function delRecordById($id)
    {
        self::getDao()->delRecordById($id);
    }

    static public function loginLog($uid, $sign)
    {
        $ip = UtlsSvc::getClientIP();
        $params = array(
            'ip' => $ip,
            'state' => $sign,
            'uid' => $uid
        );
        
        self::getDao()->loginLog($params);
    }

    static public function loginLogLists($request = array(), $options = array(), $export = false)
    { /* {{{ */
        $request_param = array();
        $sql_condition = array();
        if (isset($request['rid']) && $request['rid'] > 0) {
            $sql_condition[] = 'rid = ? ';
            $sql_param[] = $request['rid'];
            
            $options['baseurl'] .= 'rid=' . $request['rid'];
        }
        
        // print_r($sql_condition);print_r($sql_param);exit;
        $option = array();
        $option['len'] = ($options['len'] > 0) ? $options['len'] : PER_PAGE;
        if ($options['page'] > 0) {
            $option['offset'] = ($options['page'] - 1) * $option['len'];
        }
        $option['orderby'] = isset($options['orderby']) ? $options['orderby'] : '';
        
        $results = self::getDao()->getLoginLogRecord($sql_condition, $sql_param, $option);
        
        $pages = '';
        $total = $results['total'];
        if ($total > 0) {
            $temp = stristr($options['baseurl'], '?');
            if ($temp != false && strlen($temp) > 1) {
                $options['baseurl'] .= '&';
            }
            $pages = Pager::getPageStr($options['page'], $option['len'], $total, $options['baseurl']);
        }
        $results['pages'] = $pages;
        $results['offset'] = $option['offset'] + 1;
        $results['len'] = $option['len'];
        $results['page'] = $option['page'];
        $results['pagenums'] = ceil($total / $option['len']);
        
        return $results;
    }/* }}} */
    
    public static function getAllUser()
    {/* {{{ */
        return self::getDao()->getAllUser();
    }/* }}} */

    public static function getByRid($rid)
    {/* {{{ */
        return self::getDao()->getByRid($rid);
    }/* }}} */

}