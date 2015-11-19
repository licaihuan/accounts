<?php
class RoleSvc
{/*{{{*/
	const OBJ = 'Role';
	static public function add( $param )
	{
		$obj = Role::createByBiz( $param );
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
		return self::getDao()->delRecordById( $id );
	}

/*}}}*/
}