<?php

class AccessSvc
{

    /* {{{ */
    const OBJ = 'Access';

    static public function add($param)
    {
        $obj = Access::createByBiz($param);
        return self::getDao()->add($obj);
    }

    static public function getById($id = '0')
    {
        if (empty($id)) {
            return null;
        }
        return self::getDao()->getById($id, self::OBJ);
    }

    static public function getAll($cls = self::OBJ)
    {
        return self::getDao()->getAll($cls);
    }

    static public function updateById($id, $param)
    {
        return self::getDao()->updateById($id, $param, self::OBJ);
    }

    static private function getDao()
    {
        return LoaderSvc::loadDao(self::OBJ);
    }

    static public function delRecordById($id)
    {
        self::getDao()->delRecordById($id);
    }

    static public function getNidsByRoleId($rid)
    {
        return self::getDao()->getNidsByRoleId($rid);
    }

    static public function getByParams($params, $orderby = '')
    {
        return self::getDao()->getByParams($params, $orderby = '');
    }

    static public function getNodeInfoByRole($params)
    {
        return self::getDao()->getNodeInfoByRole($params, $orderby = '');
    }

    static public function getModules($rid)
    {
        $params = array(
            'pid' => 0,
            'rid' => $rid
        );
        return self::getNodeInfoByRole($params);
    }

    static public function delByParams($params)
    {
        return self::getDao()->delByParams($params);
    }

    static public function getModuleDetails($rid, $pid)
    {
        $params = array(
            'pid' => $pid,
            'rid' => $rid
        );
        return self::getNodeInfoByRole($params);
    }

    static public function getMenuByUid($uid, $c = '', $a = '', $t = '')
    {
        $record = AdminuserSvc::getById($uid);
        $rid = $record->rid;
        $c = strlen($c) > 0 ? $c : $_SERVER['_C_'];
        $a = strlen($a) > 0 ? $a : $_SERVER['_A_'];
        $t = strlen($t) > 0 ? $t : $_SERVER['_T_'];

        $modules = self::getModules($rid);
        foreach ($modules as &$v1) {
            $v1['childen'] = self::getModuleDetails($v1['rid'], $v1['nid']);
            if ($v1['childen']) {
                foreach ($v1['childen'] as &$v2) {
                    if ($c == $v2['name']) {
                        $v1['menu'] = 'current';
                        if ($a == $v2['action']) {
                            $v2['menu'] = 'current';
                        } 
                    }
                }
            }
            
        }

        return $modules;
    }
    
    // static public function getMenuByUid($uid)
    // {
    // $record = AdminuserSvc::getById($uid);
    
    // $rid = $record->rid;
    
    // $c = $_SERVER['_C_'];
    // $a = $_SERVER['_A_'];
    // $t = $_SERVER['_T_'];
    
    // $icons = array(
    // '1' => '<i class="icon icon-darkgray icon-home"></i>'
    // );
    
    // $menu = '<ul id="main-nav">';
    // $modules = self::getModules($rid);
    // foreach ($modules as $row) {
    // $menu .= '<li><a href="" class="nav-top-item">' . $row['title'] . ' </a>';
    // $details = self::getModuleDetails($row['rid'], $row['nid']);
    // $menu .='<ul>';
    // foreach ($details as $item) {
    // $active = ($c == $item['name'] && $a == $item['action'] && $t == $item['type']) ? 'class="nav-top-item current"' : '';
    // $url = '';
    // if ($item['name'] == 'index') {
    // $url = '/';
    // } else {
    // if ($item['action'] == 'index') {
    // $url = '/' . $item['name'] . '/';
    // } else {
    // $url = '/' . $item['name'] . '/' . $item['action'] . '/';
    // }
    // }
    
    // if (strlen($item['type']) > 0) {
    // $url .= '?type=' . $item['type'];
    // }
    // $menu .= '<li ' . $active . '>
    // <a href="' . $url . '">' . $icons[$item['id']] . $item['title'] . ' </a>
    // </li>';
    // }
    // $menu .='</ul>';
    // }
    // $menu .= '</ul></li>';
    
    // return $menu;
    // }
}/*}}}*/