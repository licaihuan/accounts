<?php
class BaseDao
{/*{{{*/
    public function add( $obj = null )
    {/*{{{*/

        if ( empty( $obj ) || !is_object( $obj ) )
        {
            return false;
        }
        $result = $this->addImp( $obj );
        if ( $result )
        {
            return $obj;
        }
        return false;
    }/*}}}*/

	public function getPage( $options )
	{/*{{{*/
		return Pager::render( $options );
	}/*}}}*/

    public function getAll( $cls )
    {/*{{{*/
        $sql = "select * ";
        $sql.= "from ".strtolower( $cls )." order by id desc";
        $data = $this->getExecutor()->querys( $sql, array() );
		if(empty($data))
		{
			return array();
		}
		return $data;

    }/*}}}*/

    public function getById( $id = '0', $cls )
    {/*{{{*/
        if ( empty( $id ) || empty( $cls ) )
        {
            return null;
        }

        $sql = "select * ";
        $sql.= "from ".strtolower( $cls )." ";
        $sql.= "where id = ? ";
        $row = $this->getExecutor()->query( $sql, array( $id ) );
        if ( is_null( $row ) )
        {
            return null;
        }

        $obj = new $cls( $row );

        return $obj;
    }/*}}}*/


    public function listById( $ids = array(), $cls )
    {/*{{{*/
        if ( empty( $ids ) || empty( $cls ) || !is_array( $ids ) )
        {
            return array();
        }

        $objs = array();
        $mark = array();
        foreach ( $ids as $id )
        {
            $mark[]    = '?';
            $objs[$id] = null;
        }

        $sql = "select * ";
        $sql.= "from ".strtolower( $cls )." ";
        $sql.= "where id in (".implode( ',', $mark ).") ";
        $rows = $this->getExecutor()->querys( $sql, $ids );
        if ( empty( $rows ) )
        {
            return $objs;
        }
        foreach ( $rows as $row )
        {
            $objs[$row['id']] = new $cls( $row );
        }

        return $objs;
    }/*}}}*/

    private function addImp( $obj )
    {/*{{{*/
        $cols = array_keys( $obj->toAry() );
        $vals = array_values( $obj->toAry() );
        $hold = array_fill( 0, count( $cols ), '?' );
        $sql = 'insert '.$this->getTableName( $obj ).' ';
        $sql.= '( `'.implode( "`, `", $cols ).'` ) ';
        $sql.= 'values ';
        $sql.= '( '.implode( ", ", $hold ).' ); ';
        return $this->getExecutor()->exeNoQuery( $sql, $vals );
    }/*}}}*/

    public function adds( $objs = array() )
    {/*{{{*/
        if ( empty( $objs ) || !is_array( $objs ) )
        {
            return false;
        }
        $result = $this->addsImp( $objs );
        if ( $result )
        {
            return $objs;
        }
        return false;
    }/*}}}*/

	public function updateById($id, $param, $cls)
    {/*{{{*/

		$updkey = array();
		$updval = array();
		foreach($param as $k=>$v)
		{
			$updkey[] = '`'.$k.'`=?';
			$updval[] = $v;
		}
		$updval[] = $id;
		$sql = "update ".strtolower( $cls )." set ";
		$sql.= implode(',', $updkey);
		$sql.= " where id=?";

		return $this->getExecutor()->exeNoQuery( $sql, $updval );

	}/*}}}*/

    private function addsImp( $objs )
    {/*{{{*/
        $cols = array_keys( $objs[0]->toAry() );
        $hold = array_fill( 0, count( $cols ), '?' );
        $vals = array();
        foreach ( $objs as $obj )
        {
            $vals = array_merge( $vals, array_values( $obj->toAry() ) );
        }
        $len = count( $objs );
        $sql = 'insert '.$this->getTableName( $obj ).' ';
        $sql.= '( '.implode( ", ", $cols ).' ) ';
        $sql.= 'values ';
        for ( $i = 0; $i < $len; $i++ )
        {
            $sql.= '( '.implode( ", ", $hold ).' ), ';
        }
        $sql = rtrim( $sql, ', ').';';
        return $this->getExecutor()->exeNoQuery( $sql, $vals );
    }/*}}}*/

    private function getTableName( $obj )
    {/*{{{*/
        $hash_key   = $obj->hashKey();
        $table_name = strtolower( get_class( $obj ) );
        if ( '' !== $hash_key )
        {
            $table_name.= '_'.$hash_key;
        }
        return $table_name;
    }/*}}}*/

    protected function getExecutor()
    {/*{{{*/
        return LoaderSvc::loadExecutor();
    }/*}}}*/

	protected function getSlaveExecutor()
	{/*{{{*/
        return LoaderSvc::loadSlaveExecutor();
    }/*}}}*/


}/*}}}*/
