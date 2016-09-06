<div class="center-div">
	<div class="center-div-header">当前位置：【 系统管理  >  角色管理 】</div>
	<div class="rolemagagediv comct">
		<form id="searchform">
			<table class="systemlogtb">
				<tr>
					<td class="tbfield">角色名称：</td>
					<td><input class="textbox" name="role_name" maxlength="30" placeholder="请输入角色名称"></td>
					<td class="tbfield">创建时间：</td>
					<td style="width:100px;"><input class="datetimebox" name="start_time"></td>
					<td class="singlefw">至</td>
					<td><input class="datetimebox" name="end_time"></td>
					<td class="tbfield">
						<a href="javascript:void(0)" class="easyui-linkbutton subbtn c8" data-options="iconCls:'icon-search'">查询</a>
					</td>
				</tr>
			</table>
		</form><br />
		<a href="javascript:void(0)" class="easyui-linkbutton addroletb" data-options="iconCls:'icon-add'">添加角色</a>
		<a href="javascript:void(0)" class="easyui-linkbutton unused" data-options="iconCls:'icon-no'">删除</a><br /><br />
		<table id="roletb" class="datagrid"  >
			<thead>
				<tr>
					<th data-options="field:'id',width:30"></th>
					<th data-options="field:'',width:80,checkbox:true"></th>
					<th data-options="field:'rolename',width:100,sortable:true">角色名称</th>
					<th data-options="field:'founder',width:80,sortable:true">创建人</th>
					<th data-options="field:'creatime',width:220">创建时间</th>
					<th data-options="field:'explain',width:250">说明</th>
					<th data-options="field:'perfectneedDt',width:150">操作</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
</div>
<div id="edit" class="editemploy sm">
	<form class="easyuiform" method="post">
		<table>
			<tr>
				<td>角色名称：</td>
				<td><input class="textbox" name="role_name" data-options="required:true" maxlength="50" placeholder="请输入角色名称"></td>
			</tr>
			<tr>
				<td>角色说明：</td>
				<td><input class="textbox" name="role_comment" data-options="required:true" maxlength="500" placeholder="请输入角色说明"></td>
			</tr>
		</table>
		<input type="hidden" name="id" />
	</form>
</div>
<div id="grantauth" class="editemploy">
		<table>
			<tr>
				<td style="vertical-align:top;min-width:66px;">角色权限：</td>
				<td>
					<div class="createrole">
						<ul id="tree" class="tree"></ul>
					</div>
				</td>
			</tr>
		</table>
</div>
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/systemanage/rolemanage");
</script>
</body>
</html>