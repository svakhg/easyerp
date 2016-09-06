<div class="center-div">
	<div class="center-div-header">当前位置：【 客户关系管理  >  客户档案 】</div>
	<div class="customrecordiv comct">
		<form id="record_search" class="easyui-form" method="post" data-options="novalidate:true">
			<table class="systemlogtb">
				<tr>
					<td class="tbfield">客户来源：</td>
					<td>
						<select class="selectbox com-width150 cli_source" name="cli_source"></select>
					</td>
					<td class="tbfield">获知渠道：</td>
					<td>
						<select class="selectbox com-width150 channel" name="channel"></select>
					</td>
					
					<td class="tbfield">添加时间：</td>
					<td style="width:100px;"><input class="datetimebox" name="start_time"></td>
					<td class="singlefw">至</td>
					<td><input class="datetimebox" name="end_time"></td>
				</tr>
				<tr>
					<td class="tbfield">所在省市：</td>
					<td colspan="3">
						<select class="selectbox com-width150 province" name="province"></select>&nbsp;&nbsp;&nbsp;&nbsp;
						<select class="selectbox com-width150 city" name="city"></select>
					</td>
					<td class="tbfield">关键字：</td>
					<td colspan="3">
						<input name="keywords" class="textbox" style="margin-right:30px;width:95%;" maxlength="50" placeholder="客户姓名//客户昵称/客户ID">
					</td>
				</tr>
				<tr>
					<td colspan="8" style="padding-left:38px;">
						<a href="javascript:void(0)" class="easyui-linkbutton searchbtn c8" data-options="iconCls:'icon-search'">查 询</a>
						<a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
<!--						<a href="javascript:void(0)" class="easyui-linkbutton" style="width:80px;">导 出</a>-->
					</td>
				</tr>
			</table>
		</form><br />
		<a href="javascript:void(0)" class="easyui-linkbutton addcusrecord" data-options="iconCls:'icon-add'">添加员工</a>
		<a href="javascript:void(0)" class="easyui-linkbutton unused" data-options="iconCls:'icon-no'">删除</a><br /><br />
		<table id="cusrecord" class="datagrid"></table>
	</div>
</div>
</div>
<div id="edit" class="editrecord">
	<form class="easyuiform" method="post">
		<table>
			<tr>
				<td class="tbfield"><span class="flag">*</span> 客户姓名：</td>
				<td><input class="textbox" data-options="required:true" maxlength="30" placeholder="请输入客户姓名" name="cli_name"></td>
				<td class="tbfield"><span class="flag">*</span> 客户来源：</td>
				<td>
					<select id="cli_source" class="selectbox com-width150 cli_source" name="cli_source"></select>
				</td>
				<td class="tbfield"><span class="flag">*</span> 获知渠道：</td>
				<td>
					<select id="channel" class="selectbox com-width150 channel" name="channel"></select>
				</td>
			</tr>
			<tr>
				<td class="tbfield">性别：</td>
				<td class="radiogroup"><label><input type="radio" name="cli_gender" value="1" checked="checked" />：男</label><label><input type="radio" name="cli_gender" value="2" />：女</label></td>
				<td class="tbfield">出生日期：</td>
				<td><input data-options="required:true" class="datebox com-width150 cli_birth" name="cli_birth" id="cli_birth"></td>
				<td class="tbfield">星座</td>
				<td><input class="textbox cli_constellation" readonly="readonly" maxlength="6" placeholder="请输入星座" name="cli_constellation" title="请选择出生日期"></td>
				
			</tr>
			<tr>
				<td class="tbfield">学历：</td>
				<td>
					<select id="cli_edu" class="selectbox com-width150 cli_edu" name="cli_edu"></select>
				</td>
				<td class="tbfield">昵称：</td>
				<td><input class="textbox" data-options="required:true" maxlength="30" placeholder="请输入昵称" name="cli_nick"></td>
				<td class="tbfield">民族：</td>
				<td>
					<select id="cli_race" class="selectbox com-width150 cli_race" name="cli_race"></select>
				</td>
			</tr>
			<tr>
				<td class="tbfield">血型：</td>
				<td>
					<select id="cli_blood" class="selectbox com-width150 cli_blood" name="cli_blood"></select>
				</td>
				<td class="tbfield">手机号码：</td>
				<td><input name="cli_mobile" class="textbox" data-options="required:true,validType:'mobile'" maxlength="30" placeholder="请输入手机号码"></td>
				<td class="tbfield">QQ：</td>
				<td><input name="cli_qq" class="textbox" data-options="required:true,validType:'QQ'" maxlength="30" placeholder="请输入QQ号"></td>
				
			</tr>
			<tr>
				<td class="tbfield">微信：</td>
				<td><input name="cli_weixin" class="textbox" data-options="required:true" maxlength="30" placeholder="请输入微信号"></td>
				<td class="tbfield">微博：</td>
				<td><input name="cli_weibo" class="textbox" data-options="required:true" maxlength="30" placeholder="请输入微博"></td>
				<td class="tbfield width100">电子邮箱：</td>
				<td><input name="cli_email" class="textbox" data-options="required:true,validType:'email'" maxlength="50" placeholder="请输入电子邮箱"></td>
			</tr>
			<tr>
				<td class="tbfield">通讯地址：</td>
				<td colspan="5" class="linkage">
					<select id="country" class="com-width100 selectbox country" name="country"></select>
					<select id="province" class="com-width100 selectbox province" name="province"></select>
					<select id="city" class="com-width150 selectbox city" name="city"></select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input name="street" class="textbox com-width250" data-options="required:true" maxlength="180" placeholder="详细地址">
				</td>
			</tr>
			<tr>
				<td class="tbfield vert-top">邮编：</td>
				<td class="vert-top"><input name="postcode" class="textbox" data-options="required:true,validType:'ZIP'" maxlength="6" placeholder="请输入邮编"></td>
				<td class="tbfield vert-top">备注：</td>
				<td colspan="3"><textarea name="comment" class="custextarea com-width300" data-options="required:true" maxlength="30" placeholder="请输入备注"></textarea></td>
			</tr>
			<tr>
				<td class="tbfield">标签：</td>
				<td class="text-left"><a class="abtn addlabel">+选择</a></td>
				<td colspan="4" class="tag-container" width="519"></td>
			</tr>
		</table>
		<input type="hidden" name="tag" id="tag"/>
		<input type="hidden" name="id"/>
	</form>
</div>
<div id="seeveiw">
	<table class="tradetb" border="1">
		<thead>
			<tr>
				<th>ID</th>
				<th>交易编号</th>
				<th>服务项目</th>
				<th>新人预算</th>
				<th>交易状态</th>
				<th>交易金额</th>
				<th>签单日期</th>
				<th>提交时间</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>1</td>
				<td>A001111</td>
				<td>大型婚庆</td>
				<td>231人</td>
				<td>已交易</td>
				<td>3215.21</td>
				<td>2015-02-05 04:12:43</td>
				<td>2015-02-05 04:12:43</td>
			</tr>
			<tr>
				<td>2</td>
				<td>A001111</td>
				<td>大型婚庆</td>
				<td>231人</td>
				<td>已交易</td>
				<td>3215.21</td>
				<td>2015-02-05 04:12:43</td>
				<td>2015-02-05 04:12:43</td>
			</tr>
			<tr>
				<td>3</td>
				<td>A001111</td>
				<td>大型婚庆</td>
				<td>231人</td>
				<td>已交易</td>
				<td>3215.21</td>
				<td>2015-02-05 04:12:43</td>
				<td>2015-02-05 04:12:43</td>
			</tr>
			<tr>
				<td>4</td>
				<td>A001111</td>
				<td>大型婚庆</td>
				<td>231人</td>
				<td>已交易</td>
				<td>3215.21</td>
				<td>2015-02-05 04:12:43</td>
				<td>2015-02-05 04:12:43</td>
			</tr>
		</tbody>
	</table>
</div>
<div id="cuslabel" class="cuslabel">
	<input class="cuslabel-searchbox" style="width:80%">
	<div><a class="checkall">全选</a> <a class="uncheckall">反选</a></div>
	<ul class="checkbox-tag"></ul>
</div>

    <?php $this->load->view('header/footer_view.php');?>
    <script type="text/javascript">
        seajs.use("<?php echo $config['srcPath'];?>/js/customer/customrecord");
    </script>
</body>
</html>
