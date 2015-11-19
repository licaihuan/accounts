<?php
class EntitySvc
{
	public function createEntityFile($f_type,$f_name,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start)
	{
		$entity_file = "<?php\nclass ".$entity_ucfirst." extends Entity\n{\n\tconst ID_OBJ  = '{$entity}';";
		$entity_file.= "\n\tpublic static function createByBiz( \$param )\n";
		$entity_file.="\t{\n";
		$entity_file.="\t\t\$cls = __CLASS__;\n";
		$entity_file.="\t\t\$obj = new \$cls();";
		foreach($f_type as $k=>$type)
		{
			$name = $f_name[$k];
			switch($type)
			{
				case "id_genter":
					$entity_file.="\n\t\t\$obj->id = LoaderSvc::loadIdGenter()->create(self::ID_OBJ);";
					break;
				case "ctime":
				case "utime":
					$entity_file.="\n\t\t\$obj->$name = date('Y-m-d H:i:s');";
					break;
				default:
					$entity_file.="\n\t\t\$obj->$name = \$param['$name'];";
			}

		}
		$entity_file .= "\n\t\treturn \$obj;\n";
		$entity_file .= "\n\t}\n}";
		return $entity_file;
	}

	public function createSQLFile($f_type,$f_name,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start)
	{
		$create_sql="insert into id_genter(obj,id,step)values('".$entity."',".$id_genter_start.",1);\n";
		$create_sql.= "DROP TABLE IF EXISTS $entity;\ncreate table `$entity` (\n";
		foreach($f_type as $k=>$type)
		{
			$name = $f_name[$k];
			switch($type)
			{
				case "id_genter":
					$create_sql.= "`id` int unsigned NOT NULL,\n";
					break;
				case "ctime":
				case "utime":
					$create_sql.= "`$name` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',\n";
					break;
				case "int unsigned":
				case "int":
				case "float":
				case "tinyint unsigned":
				case "datetime":
				case "date":
				case "time":
					$create_sql.= "`$name` $type NOT NULL DEFAULT '".$f_default[$k]."',\n";
					break;
				case "decimal":
					$create_sql.= "`$name` decimal(".$f_attr[$k].") NOT NULL DEFAULT ".$f_default[$k].",\n";
					break;
				case "char":
				case "varchar":
					$create_sql.= "`$name` $type(".$f_attr[$k].") NOT NULL DEFAULT '".$f_default[$k]."',\n";
					break;
				case "text":
				case "mediumtext":
					$create_sql.= "`$name` $type NOT NULL  ,\n";
					break;
				default:
					break;
			}
		}
		$create_sql.="PRIMARY KEY (`id`)\n";
		$create_sql.=") ENGINE=InnoDB DEFAULT CHARSET=utf8;";


		return $create_sql;
	}

	public function createSvcFile($f_type,$f_name,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start)
	{


		$svc_file = "<?php
class ".$entity_ucfirst."Svc
{/*{{{*/
\tconst OBJ = '".$entity_ucfirst."';
\tstatic public function add(\$param)
\t{
\t\t\$obj = ".$entity_ucfirst."::createByBiz(\$param);
\t\treturn self::getDao()->add(\$obj);
\t}
\tstatic public function getById(\$id = '0')
\t{
\t\tif (empty(\$id))
\t\t{
\t\t\treturn null;
\t\t}
\t\treturn self::getDao()->getById(\$id,self::OBJ);
\t}

\tstatic public function updateById(\$id,\$param)
\t{
\t\treturn self::getDao()->updateById(\$id,\$param,self::OBJ);
\t}

\tstatic private function getDao()
\t{
\t\treturn LoaderSvc::loadDao(self::OBJ);
\t}

\tstatic public function lists(\$request = array(),\$options = array(),\$export = false)
\t{/*{{{*/
\t\t\$request_param = array();
\t\t\$sql_condition = array();
\t\t\$sql_param = array();

\t\tif(isset(\$request['id']) && \$request['id']>1)
\t\t{
\t\t\t\$request_param[] = '`id`=' . \$request['id'];
\t\t\t\$sql_condition[] = '`id` = ? ';
\t\t\t\$sql_param[] = \$request['id'];
\t\t}";

foreach($f_type as $k=>$type)
{
	$name = $f_name[$k];
	switch($type)
	{
		case "int unsigned":
		case "int":
		case "float":
		case "decimal":
		case "tinyint unsigned":
		case "char":
		case "varchar":
		case "datetime":
		case "date":
		case "time":
			$svc_file.="\n\t\tif('' != \$request['$name']){\n\t\t\t\$request_param[] = '$name=' . \$request['$name'];\n\t\t\t\$sql_condition[] = '`$name` = ?';\n\t\t\t\$sql_param[]	 = \$request['$name'];\n\t\t}";
			break;
		case "text":
		case "mediumtext":
			$svc_file.="\n\t\tif('' != \$request['$name']){\n\t\t\t\$request_param[] = '$name=' . \$request['$name'];\n\t\t\t\$sql_condition[] = '`$name` like ?';\n\t\t\t\$sql_param[]	 = '%'.\$request['$name'].'%';\n\t\t}";
			break;
		default:
			break;
	}
}

$svc_file .="
\t\t\$option = array();
\t\t\$option['len'] = (\$options['len'] > 0) ? \$options['len'] : PER_PAGE;
\t\tif(\$options['page'] > 0){
\t\t\t\$option['offset'] = (\$options['page'] - 1) * \$option['len'];
\t\t}
\t\t\$option['orderby'] = isset(\$options['orderby']) ? \$options['orderby'] : '';
\t\t\$results = self::getDao()->getRecord(\$sql_condition,\$sql_param ,\$option);
\t\t\$pages = '';
\t\t\$total = \$results['total'];
\t\tif(\$total > 0){
\t\t\t\$temp = stristr(\$options['baseurl'],'?');
\t\t\tif(\$temp === false) \$options['baseurl'] .= '?';
\t\t\t\$options['baseurl'] .= implode('&',\$request_param);
\t\t\tif(count(\$request_param)) \$options['baseurl'] .= '&';
\t\t\t\$pages = Pager::getPageStr(\$options['page'],\$option['len'],\$total,\$options['baseurl']);
\t\t}
\t\t\$results['pages'] = \$pages;
\t\t//\$results['offset'] = \$option['offset'] + 1;
\t\t//\$results['len'] = \$option['len'];
\t\t\$results['pagenums'] = ceil(\$total / \$option['len']);

";
		
		$svc_file.="\t\treturn \$results;
	}/*}}}*/
	
	static public function delRecordById(\$id)
	{
		self::getDao()->delRecordById(\$id);
	}

}";
		return $svc_file;
	}

	public function createDaoFile($f_type,$f_name,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start)
	{
		$dao_file = "<?php\nclass ".$entity_ucfirst."Dao extends BaseDao
{
	const TABLE_NAME = '".$entity."';

	private function getTableName()
	{
		return self::TABLE_NAME;
	}
	
	public function getRecord(\$sql_condition = array(),\$sql_param = array(),\$options = array())
	{
		\$sql = \"select SQL_CALC_FOUND_ROWS * \";
		\$sql.= \"from \".self::getTableName().\" \";
		if(!empty( \$sql_condition )){
			\$sql.= 'where '. implode(' and ', \$sql_condition);
		}
		if(\$options['orderby']){
			\$sql.= \" order by \".\$options['orderby'].\" \";
		}else{
			\$sql.= \" order by `id` desc \";
		}
		
		if(\$options['offset'] >=0 && \$options['len'] > 0){
			\$sql.= ' limit '.intval(\$options['offset']).','.intval(\$options['len']);
		}elseif(\$options['len'] > 0){
			\$sql.= ' limit '.intval(\$options['len']);
		}
		
		\$results = array();
		\$result = \$this->getExecutor()->querys(\$sql,\$sql_param);
		
		\$sql = \"SELECT FOUND_ROWS() as `total`;\";
		\$rs = \$this->getExecutor()->query(\$sql);
		
		\$results = array(
			'total'=>\$rs['total'],
			'record'=>(is_array(\$result)?\$result:array()),
		);
		return \$results;
	}
	
	public function delRecordById(\$id)
	{
		\$sql = \"delete \";
		\$sql.= \"from \".self::TABLE_NAME.\" \";
		\$sql.= \"where `id` = ? \";
		\$this->getExecutor()->exeNoQuery(\$sql,array(\$id));
	}
}";
		return $dao_file;
	}

	public function createAdminIndexFile($f_type,$f_name,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start)
	{
		$admin_controller = "<?php
require_once dirname(dirname(dirname(__FILE__))).'/header.php';

\$request = array();
\$request['id']  = RequestSvc::Request('id');

\$results = ".$entity_ucfirst."Svc::lists(\$request,array('page'=>RequestSvc::Request('p',1,'int'),'baseurl'=>'/".$entity."/?'));
//var_dump($results);die();

LoaderSvc::loadSmarty()->assign('request',\$request);
LoaderSvc::loadSmarty()->assign('results',\$results);
LoaderSvc::loadSmarty()->display('".$entity."/index.tpl');
";
		return $admin_controller;
	}
	
	public function createAdminAddFile($f_type,$f_name,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start)
	{
		$admin_controller = "<?php\n";
		
		$admin_controller .= "require_once dirname(dirname(dirname(__FILE__))).'/header.php';\n";
		
		$admin_controller .= "\n\$action = isset(\$_GET['action']) ? \$_GET['action'] : null;";
		$admin_controller .= "\n\$info = '';";
		
		$admin_controller .= "\nif('save' == \$action){";
		
		foreach($f_type as $k=>$type)
		{
			$name = $f_name[$k];
			switch($type)
			{
				case "int unsigned":
				case "int":
					$admin_controller .= "\n\t$$name = intval(\$_POST['$name']);";
					break;
				case "float":
				case "decimal":
				case "tinyint unsigned":
				case "char":
				case "varchar":
				case "datetime":
				case "date":
				case "time":
				case "text":
				case "mediumtext":
					$admin_controller .= "\n\t$$name = \$_POST['$name'];";
					break;
				default:
					break;
			}
		}

		$admin_controller .= "\n\t\$params = array(";
		foreach($f_name as $name){
			$admin_controller .= "\n\t\t'$name'=>\$$name,";
		}
		$admin_controller .= "\n\t);";
		$admin_controller .= "\n\t\$obj = ".$entity_ucfirst."Svc::add(\$params);";
			
		$admin_controller .= "\n\tif(is_object(\$obj)){
\t\t\$info = '操作成功';	
\t}else{
\t\t\$info = '操作失败';
\t}";
$admin_controller .= "
}
";
		$admin_controller .= "\nLoaderSvc::loadSmarty()->assign('info',\$info);
LoaderSvc::loadSmarty()->display('".$entity."/add.tpl');
";
		return $admin_controller;
	}
	
	
	public function createAdminEditFile($f_type,$f_name,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start)
	{
		$admin_controller = "<?php
require_once dirname(dirname(dirname(__FILE__))).'/header.php';

\$id = \$_REQUEST['id']>0 ? \$_REQUEST['id'] : 0;
\$record = ".$entity_ucfirst."Svc::getById(\$id);

\$info = '';
if(is_object(\$record)){
	\$action = isset(\$_GET['action']) ? \$_GET['action'] : null;
	if('save' == \$action){";
	foreach($f_type as $k=>$type){
		$name = $f_name[$k];
		switch($type)
		{
			case "int unsigned":
			case "int":
				$admin_controller .= "\n\t\t$$name = intval(\$_POST['$name']);";
				break;
			case "float":
			case "decimal":
			case "tinyint unsigned":
			case "char":
			case "varchar":
			case "datetime":
			case "date":
			case "time":
			case "text":
			case "mediumtext":
				$admin_controller .= "\n\t\t\$$name = \$_POST['$name'];";
				break;
			default:
				break;
		}
	}
	$admin_controller .= "\n\t\t\$params = array(";
	foreach($f_name as $name){
		$admin_controller .= "\n\t\t\t'$name'=>\$$name,";
	}
	
	$admin_controller .= "\n\t\t);";
	$admin_controller .= "\n\t\t".$entity_ucfirst."Svc::updateById(\$id,\$params);";
	$admin_controller .= "\n\t\t\$record = ".$entity_ucfirst."Svc::getById(\$id);";
$admin_controller .= "\n\t\t\$info = '操作成功';
	}
}else{
	\$info = '数据异常';
}
LoaderSvc::loadSmarty()->assign('info',\$info);
LoaderSvc::loadSmarty()->assign('record',\$record);
LoaderSvc::loadSmarty()->display('".$entity."/edit.tpl');
";
		return $admin_controller;
	}
	
	public function createAdminIndexTplFile($f_type,$f_name,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start)
	{
		$tpl = '{include file="header.tpl"}
<!--<script type="text/javascript" src="{$_STATIC_}js/public/'.$entity.'-index.js"></script>-->
	<!-- 标题栏 -->
	<div class="main-title">
		<h2>呼叫客户</h2>
	</div>
	
	<div class="table-responsive">
		<style>
			.table .th{ width:150px;text-align:right;line-height:2;}
			.table-responsive { border:1px; }
		</style>
		<form  class="form-horizontal" action="/'.$entity.'/index/" method="get">
		<table class="table table-striped table-bordered bootstrap-datatable">
	      <tbody>
	        <tr>
	          <th class="th">任务类型</th>
	          <td>
				<select id="f_type" name="type" class="text">
					<option {if $request.status == ""}selected="selected"  {/if} value="">全选</option>
							<option {if $request.type == $request.TYPE_CONF_STOV.TYPE_WCZ}selected="selected"  {/if} value="{$request.TYPE_CONF_STOV.TYPE_WCZ}">{$request["TYPE_CONF"][$request["TYPE_CONF_STOV"]["TYPE_WCZ"]]["NAME"]}</option>
	    					<option {if $request.type == $request.TYPE_CONF_STOV.TYPE_WTZ}selected="selected"  {/if} value="{$request.TYPE_CONF_STOV.TYPE_WTZ}">{$request["TYPE_CONF"][$request["TYPE_CONF_STOV"]["TYPE_WTZ"]]["NAME"]}</option>
	  					</select>
	          </td>

	          <th class="th">注册时间</th>
	          <td><input type="text" class="text  datetime" value="{$get.statime}" name="statime"  /> - 
	          	  <input type="text" class="text  datetime" value="{$get.endtime}" name="endtime"  />　
	          </td>
  	        </tr>	          
            <tr>
              <th class="th">分配状态</th>
	          <td>				
                  <select  name="auid" class="text">
                           <option {if $request.auid == ""} selected="selected"{/if} value="">全部</option>
                           <option {if $request.auid == 1} selected="selected"{/if} value="1">等待分配</option>
                           <option {if $request.auid == 2} selected="selected"{/if} value="2">已分配</option>
                  </select>
	          </td>
  	        </tr>
	        <tr>
	          <th colspan="4"><button type="submit" class="btn btn-info" style="margin-left:200px;">搜索</button></th>
	        </tr>
	      </tbody>
	    </table>
	    </form>
	</div>
	
	<div class="cf">
		 <div class="fl">
            <a class="btn" href="/'.$entity.'/add/">新 增</a> 
        </div>
    </div>
    <!-- 数据列表 -->
    <div class="data-table table-striped">
	<table>
    	<thead>
			<tr>
				<th class="row-selected row-selected"><input class="check-all" type="checkbox"></th>
				<th colspan="1" rowspan="1">编号</th>
				<th >手机号</th>
				<th >类型</th>
				<th >注册时间</th>
				<th >最近充值时间</th>
				<th >账户余额</th>
				<th >分配状态</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$results.record item=item}
			<tr id="tr_{$item.id}">
				<td><input class="ids" type="checkbox" value="{$item.id}-{$item.uid}" name="id[]"></td>
				<td>{$item.id}</td>
				<td>{$item.mobile}</td>
				<td>{$item[\'type\']}</td>
				<td>{$item.regtime}</td>
				<td>{if $item.type==2}-{else}{$item.paytime}{/if}</td>
				<td>{$item.money}</td>
				<td>{$item[\'ass_status\']}</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>
<div class="page">
	<div>  
	{$results.pages} 
	<span class="rows">共 {$results.total} 条记录</span>
	</div>
</div>
		
		
<link href="{$_STATIC_}/datetimepicker/css/datetimepicker.css" rel="stylesheet" type="text/css">
<link href="{$_STATIC_}/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="{$_STATIC_}/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="{$_STATIC_}/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
<script type="text/javascript">
    $(function () {
        // 时间控件
        $(".datetime").datetimepicker({
                    format: "yyyy-mm-dd",
                    language: "zh-CN",
                    autoclose: true,
                    startView: 2,
                    minView:2,
        });
    });
</script>
{include file="footer.tpl"}';
		return $tpl;
	}
	
	public function createAdminAddTplFile($f_type,$f_name,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start)
	{
		$tpl = '{include file="header.tpl"}
<!-- 标题栏 -->
	<div class="main-title">
		<h2>用户列表</h2>
	</div>
	{$info}
	<!-- 表单内容 -->
	<div class="tab-wrap">
		<div class="tab-content">
			<form  class="form-horizontal" action="/'.$entity.'/add/?action=save" method="post">
			<input type="hidden" name="id" value="{$record->id}" />
				<div class="form-item">
		            <label class="item-label">真实姓名</label>
					<div class="controls">
								<input type="text" autofocus="true" value=""  class="text input-large" name="name" id="name">
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">英文或拼音名称</label>
					<div class="controls">
								<input type="text" autofocus="true" value="" class="text input-large" name="ename" id="ename">
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">登陆账号(email)</label>
					<div class="controls">
								<input type="text" autofocus="true" value="" class="text input-large" name="email" id="email">
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">登录密码</label>
					<div class="controls">
								<input type="text" autofocus="true" value="" class="text input-large" name="passwd" id="passwd">
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">部门</label>
					<div class="controls">
								<input type="text" autofocus="true" value="" class="text input-large" name="depart" id="depart">
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">职务</label>
					<div class="controls">
								<input type="text" autofocus="true" value="" class="text input-large" name="position" id="position">
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">状态</label>
					<div class="controls">
								<select id="status" name="status" class=" input-large">
									<option selected="selected" value="1">启用</option>
									<option value="0">禁用</option>
								</select>
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">所属角色</label>
					<div class="controls">
								<select id="rid" name="rid" class="input-large">
									<option value="0">角色未划分</option>
									{foreach from=$request.RID_CONF key=k item=item}
									{if $k eq $request.RID_STV.RID_ROOT}
										{if $rid eq $request.RID_STV.RID_ROOT}
											<option value="{$k}">{$item.NAME}</option>
										{/if}
									{else}
										<option value="{$k}">{$item.NAME}</option>
									{/if}
									{/foreach}
								</select>
				     </div>
		        </div>
		        <div class="form-item">
		            <label class="item-label">坐席号</label>
					<div class="controls">
							<input type="text" value="{$record->seatnum}" name="seatnum" id="seatnum" class="text input-large"/>
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">描述</label>
					<div class="controls">
								<textarea style="width:400px; height:150px;" id="remark" name="remark"></textarea>
				     </div>
		        </div>
		      
				<div class="form-item">
					 <div class="controls">
					 		<button type="submit" class="btn">保存</button>
							<a class="btn btn-info" href="/'.$entity.'/index">返回</a>
				     </div>
		        </div>
			</form>
		</div>
		</div>

{include file="footer.tpl"}
	 ';
		return $tpl;
	}
	
	public function createAdminEditTplFile($f_type,$f_name,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start)
	{
		$tpl = '{include file="header.tpl"}
<!-- 标题栏 -->
	<div class="main-title">
		<h2>编辑用户</h2>
	</div>
			{$info}
	<!-- 表单内容 -->
	<div class="tab-wrap">
		<div class="tab-content">
			<form action="/'.$entity.'/edit/?type=save" method="post">
			<input type="hidden" name="id" value="{$record->id}" />
				<div class="form-item">
		            <label class="item-label">真实姓名</label>
					<div class="controls">
							<td><input type="text" value="{$record->name}" name="name" id="name" class="text input-large"></td>
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">英文或拼音名称</label>
					<div class="controls">
							<td><input type="text" value="{$record->ename}" name="ename" id="ename" class="text input-large"></td>
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">登陆账号(email)</label>
					<div class="controls">
							<td><input type="text" value="{$record->email}" name="email" id="email" class="text input-large"></td>
		        </div>
				<div class="form-item">
		            <label class="item-label">登录密码</label>
					<div class="controls">
							<td><input type="text" name="passwd" id="passwd" class="text input-large"></td>
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">部门</label>
					<div class="controls">
							 <input type="text" value="{$record->depart}" name="depart" id="depart" class="text input-large">
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">职务</label>
					<div class="controls">
								<input type="text" value="{$record->position}" name="position" id="position" class="text input-large">
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">状态</label>
					<div class="controls">
								<select id="status" name="status" class="input-large">
			    					<option {if $record->status == 1}selected="selected"{/if}  value="1">启用</option>
									<option {if $record->status == 0}selected="selected"{/if}  value="0">禁用</option>
								</select>
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">所属角色</label>
					<div class="controls">
								<select id="rid" name="rid" class="input-large">
									<option {if $record->rid == 0}selected="selected"{/if}  value="0">角色未划分</option>
									{foreach from=$request.RID_CONF key=k item=item}
										<option {if $k == $record->rid}selected="selected"{/if} value="{$k}">{$item.NAME}</option>
									{/foreach}
								</select>
				     </div>
		        </div>
		        <div class="form-item">
		            <label class="item-label">坐席号</label>
					<div class="controls">
								<input type="text" value="{$record->seatnum}" name="seatnum" id="seatnum" class="text input-large"/>
				     </div>
		        </div>
				<div class="form-item">
		            <label class="item-label">描述</label>
					<div class="controls">
								<textarea style="width:600px; height:150px;" id="remark" name="remark">{$record->remark}</textarea>
				     </div>
		        </div>
		       
				<div class="form-item">
							<button type="submit" class="btn">保存</button>
							<a class="btn btn-info" href="/'.$entity.'/index">返回</a>
				</div>
			</form>

		</div>
	</div>
</div>
{include file="footer.tpl"}
';
		return $tpl;
	}

}