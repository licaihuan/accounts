<?php
class NodeSvc
{/*{{{*/
	const OBJ = 'Node';
	static public function add( $param )
	{
		$obj = Node::createByBiz( $param );
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

	
	static public function getByParams($params)
	{
		return self::getDao()->getByParams($params);
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
	
	static public function getNodeIdByParam($params)
	{
		return self::getDao()->getNodeIdByParam($params);
	
	}

/*}}}*/
}