<div class="center-div">
	<div class="center-div-header">当前位置：【 系统管理  >  系统日志 】</div>
	<div id="systemlogtab" class="easyui-tabs comct">
		<div title="操作日志" class="lftab">
			<form class="easyui-form" id="logform">
				<table class="systemlogtb">
					<tr>
						<td class="tbfield">操作员：</td>
						<td><input class="textbox" name="user" maxlength="30" placeholder="请输入操作员"></td>
						<td class="tbfield">操作类型：</td>
                        <td><select class="selectbox com-width150" id="param2" name="do_type">
                                <option value="">--请选择--</option>
                                <option value="1">添加</option>
                                <option value="2">修改</option>
                                <option value="3">删除</option>
                        </select></td>
						<td class="tbfield">功能模块：</td>
						<td><select class="selectbox com-width150" id="param3" name="module">
                                <option value="">--请选择--</option>
                                <option value="1">系统管理</option>
                                <option value="4">客户管理</option>
						</select></td>
						<td class="tbfield">操作时间：</td>
						<td style="width:100px;"><input name="from" class="datetimebox"></td>
						<td class="singlefw">至</td>
						<td><input name="to" class="datetimebox"></td>
					</tr>
					<tr>
						<td class="tbfield">相关单据：</td>
						<td><input class="textbox" name="order_bill" maxlength="30" placeholder="请输入相关单据"></td>
						<td class="tbfield">日志内容：</td>
						<td colspan="3">
							<input class="textbox com-width300" name="content" maxlength="30" placeholder="请输入日志内容">
						</td>
						<td colspan="4">
							<a href="javascript:void(0)" class="easyui-linkbutton subbtn c8" data-options="iconCls:'icon-search'">查询</a>
<!--							<a href="javascript:void(0)" class="easyui-linkbutton" style="width:80px;">导出</a>-->
						</td>
					</tr>
				</table>
			</form><br />
			<table class="tb datagrid" id="operatelog" style="width:100%"></table>
		</div>
		<div title="系统访问日志" class="lftab">
			<form class="easyui-form" method="post" data-options="novalidate:true">
				<table class="systemlogtb">
					<tr>
						<td class="tbfield">操作员：</td>
						<td><input class="textbox" name="user" maxlength="30" placeholder="请输入操作员"></td>
						<td class="tbfield">操作类型：</td>
						<td><input class="textbox" name="content" maxlength="30" placeholder="请输入操作类型"></td>
						<td class="tbfield">操作时间：</td>
						<td style="width:100px;"><input name="from" class="datebox"></td>
						<td class="singlefw">至</td>
						<td><input name="to" class="datebox"></td>
						<td colspan="4">
							<a href="javascript:void(0)" class="easyui-linkbutton subbtn c8" data-options="iconCls:'icon-search'">查询</a>
							<a href="javascript:void(0)" class="easyui-linkbutton c5" style="width:80px;">导出</a>
						</td>
					</tr>
				</table> 
			<table class="tb datagrid" id="cusnotice" style="width:100%"></table>
		</div>
	</div>
</div>
</div>
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/systemanage/systemlog");
</script>
</body>
</html>