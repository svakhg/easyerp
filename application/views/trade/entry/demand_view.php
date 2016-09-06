<div class="center-div">
	<div class="center-div-header">当前位置：【 婚礼需求管理  >  录入客户需求 】</div>
	<div class="comct">
		<div id="maincontent">
				<div id="systemlogtab" class="easyui-tabs" style="overflow:visible; width: 99%;">
						<a href="javascript:" class="easyui-linkbutton c1 mb10 mr10 mt10 save" data-options="iconCls:'icon-save'">保存</a>
                       <a href="javascript:" class="easyui-linkbutton c8 mb10 mr10 mt10 draft" data-options="iconCls:'icon-save'">保存为草稿</a>
						<a href="javascript:history.go(-1)" class="easyui-linkbutton mb10 mr10 mt10" data-options="iconCls:'icon-back'">返 回</a>
						找商家方式：<select class="com-width100 selectbox mode" id="mode" name="mode"></select>
						新人顾问：<select class="com-width100 selectbox counselor_uid" id="counselor_uid" name="counselor_uid"></select>
						
					<div title="基本信息" class="lftab">
						<form class="easyui-form" method="post" id="baseinfo" data-options="novalidate:true">
						<table class="bigtb">
							<tr>
								<td class="tbfield"><span class="flag">*</span> 客户姓名：</td>
								<td><input class="textbox" name="cli_name" data-options="required:true" maxlength="30" placeholder="请输入客户姓名"></td>
								<td class="tbfield"><span class="flag">*</span> 客户来源：</td>
								<td>
									<select class="selectbox com-width100 cli_source" id="cli_source" name="cli_source"></select>
								</td>
								<td class="tbfield"><span class="flag">*</span> 获知渠道：</td>
								<td>
									<select class="selectbox com-width100 channel" id="channel" name="channel"></select>
								</td>
							</tr>
							<tr>
								<td class="tbfield"> &nbsp;&nbsp;&nbsp;&nbsp;期望联系方式：</td>
								<td colspan="5">
									<label><input type="checkbox" name="cli_hope_contect_way" value="1" checked = "checked">手机</label>
									<label><input type="checkbox" name="cli_hope_contect_way" value="2">QQ</label>
									<label><input type="checkbox" name="cli_hope_contect_way" value="3">微信</label>
									<label><input type="checkbox" name="cli_hope_contect_way" value="4">其他联系方式</label>
								
								</td>
							</tr>
							<!--<tr>
								<<td class="tbfield">性别：</td>
								<td class="radiogroup">
									<label><input type="hidden" class="radchk" name="cli_gender" value="1"/>：男</label>
									<label><input type="hidden" class="radchk" name="cli_gender" value="2" />：女</label></td>
								<td class="tbfield">出生日期：</td>
								<td><input class="datebox cli_birth" id="cli_birth" name="cli_birth"></td>
								<td class="tbfield">学历：</td>
								<td>
									<select class="selectbox com-width100 cli_edu" id="cli_edu" name="cli_edu"></select>
								</td>
							</tr>!-->
							<tr>
								<td class="tbfield">手机号码：</td>
								<td><input class="textbox" name="cli_mobile" data-options="required:true,validType:'mobile'" maxlength="11" placeholder="请输入手机号码"></td>
								<td class="tbfield">固定电话：</td>
								<td><input class="textbox" name="cli_tel" maxlength="20" placeholder="请输入固定电话"></td>
								<td class="tbfield">民族：</td>
								<td>
									<select class="selectbox com-width100 cli_nation" id="cli_nation" name="cli_nation"></select>
								</td>
							</tr>
							<tr>
								<td class="tbfield">微信：</td>
								<td><input class="textbox" name="cli_weixin" data-options="validType:'UNCHS'" maxlength="30" placeholder="请输入微信号"></td>
								<td class="tbfield">QQ：</td>
								<td><input class="textbox" name="cli_qq" data-options="validType:'QQ'" maxlength="30" placeholder="请输入QQ号"></td>
								<td class="tbfield">微博：</td>
								<td><input class="textbox" name="cli_weibo" data-options="validType:'UNCHS'" maxlength="30" placeholder="请输入微博号"></td>
							</tr>
							<tr>
								<td class="tbfield">邮编：</td>
								<td><input class="textbox" name="cli_postcode" data-options="validType:'ZIP'" maxlength="6" placeholder="请输入邮编"></td>
								<td class="tbfield">电子邮箱：</td>
								<td><input class="textbox" name="cli_email" data-options="validType:'email'" maxlength="50" placeholder="请输入电子邮箱"></td>
								<td class="tbfield">其他联系方式：</td>
								<td><input class="textbox" name="cli_othercontect"· maxlength="30" placeholder="请输入其他联系方式"></td>
							</tr>
							<tr>
								<td class="tbfield">通讯地址：</td>
								<td colspan="4" class="linkage">
									<select class="com-width100 selectbox cli_country" name="cli_country" id="cli_country"></select>
									<select class="com-width100 selectbox cli_province" name="cli_province" id="cli_province"></select>
									<select class="com-width150 selectbox cli_city" name="cli_city" id="cli_city"></select>
								</td>
								<td><input class="textbox com-autowidth" name="cli_address" maxlength="100" placeholder="详细地址"></td>
							</tr>
							<tr>
								<td class="tbfield">希望联系时间：</td>
								<td><input class="textbox" name="cli_hope_contect_time" maxlength="30" placeholder="请输入希望联系时间"></td>
                                <!--  <td class="tbfield">期望联系方式：</td>
								<td><input class="textbox" name="cli_hope_contect_way" maxlength="30" placeholder="请输入期望联系方式"></td>-->
								<td class="tbfield" style="vertical-align:top;">客户备注：</td>
								<td colspan="3"><textarea class="custextarea com-width300" name="comment" maxlength="500" placeholder="请输入客户备注"></textarea></td>
							</tr>
							
							
							<tr>
								<td class="tbfield">客户标签：</td>
								<td class="text-left"><a class="abtn addlabel">+选择<input type="hidden" name="tag" id="cli_tag"/></a></td>
								<td colspan="4" class="tag-container" width="519"></td>
							</tr>
						</table>
						</form>
					</div>
						
					<div title="一站式婚礼需求" class="lftab wedneed">
						<div class="tabcont">
							<form class="easyui-form" method="post" id="wedbaseinfo" data-options="novalidate:true">
							<div class="modulars">
								<h3>婚礼基础信息：</h3>
								<table>
									<tr>
										<td class="vert-top">婚礼日期：</td>
										<td>
											<label><input type="radio" name="wed_date_sure" value="1" class="radchk" />已确定</label>
											<label class="optional"><input name="wed_date" id="wed_date" class="datebox wed_date"></label><br />
											<label><input type="radio" name="wed_date_sure" value="0" class="radchk" />还未确定</label>
											<label class="optional"><input name="wed_date_notsure" class="textbox com-width300"  maxlength="30" placeholder="为了更好的服务您，请您说下您举办婚礼的大致时间"></label>
										</td>
									</tr>
									<tr>
										<td class="vert-top">婚礼地点：</td>
										<td>
											<select class="com-width100 selectbox wed_country" name="wed_country" id="wed_country"></select>
											<select class="com-width100 selectbox wed_province" name="wed_province" id="wed_province"></select>
											<select class="com-width150 selectbox wed_city" name="wed_city" id="wed_city"></select>
										</td>
									</tr>
									<tr>
										<td class="vert-top">婚礼场地：</td>
										<td>
											<label><input type="radio" name="wed_place" class="radchk" value="还未确定"/>还未确定</label>
											<label><input type="radio" name="wed_place" class="radchk" value=""/>已确定</label>
											<label class="optional"><input class="textbox customer"  maxlength="30" placeholder="请输入具体场地名称"><br /></label>
										</td>
									</tr>
									<tr>
										<td class="vert-top">婚宴类型：</td>
										<td>
											<label><input type="radio" name="wed_party_type" class="radchk" value="午宴" />午宴</label>
											<label><input type="radio" name="wed_party_type" class="radchk" value="晚宴" />晚宴</label>
											<label><input type="radio" name="wed_party_type" class="radchk" value="还未确定" />还未确定</label>
										</td>
									</tr>
									<tr>
										<td class="vert-top">来宾人数：</td>
										<td>
                                            <select class="com-width100 easyui-combobox people_num" name="people_num" id="people_num">
                                                <option value="">--请选择--</option>
                                                <option value="50人以下">50人以下</option>
                                                <option value="51-100人">51-100人</option>
                                                <option value="101-150人">101-150人</option>
                                                <option value="151-200人">151-200人</option>
                                                <option value="201-250人">201-250人</option>
                                                <option value="251-300人">251-300人</option>
                                                <option value="301-400人">301-400人</option>
                                                <option value="401-500人">401-500人</option>
                                                <option value="500人以上">500人以上</option>
                                                <option value="未确定">未确定</option>
                                            </select>
										</td>
									</tr>
									<tr>
										<td class="vert-top">婚礼预算：</td>
										<td>
											<div class="question">对于婚礼，您的预算是？</div>
											<label><input type="radio" name="budget" class="radchk" value="2万以下"/>2万以下</label>
											<label><input type="radio" name="budget" class="radchk" value="2-4万"/>2-4万</label>
											<label><input type="radio" name="budget" class="radchk" value="4-7万"/>4-7万</label>
											<label><input type="radio" name="budget" class="radchk" value="7-10万"/>7-10万</label>
                                            <label><input type="radio" name="budget" class="radchk" value="10万以上"/>10万以上</label>
											<label><input type="radio" name="budget" class="radchk" value="没有概念，先聊聊"/>没有概念，先聊聊</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="8" placeholder="请输入您的预算">（元）
											</label>
										</td>
									</tr>
								</table>
							</div>
							</form>
							<form class="easyui-form" method="post" id="onestation" data-options="novalidate:true">
							<div class="modulars">
								<h3>对理想婚礼的要求：</h3>
								<table>
									<tr>
										<td>
											<div class="question">关于婚礼，一下哪种描述更符合您的要求？</div>
											<label><input type="radio" name="description" class="radchk" value="婚礼只是一个形式，我希望简单方便就好" />婚礼只是一个形式，我希望简单方便就好</label><br />
											<label><input type="radio" name="description" class="radchk" value="我对婚礼有梦想，但梦想与预算矛盾时，预算更重要" />我对婚礼有梦想，但梦想与预算矛盾时，预算更重要</label><br />
											<label><input type="radio" name="description" class="radchk" value="我对婚礼有梦想，但梦想与预算矛盾时，梦想更重要" />我对婚礼有梦想，但梦想与预算矛盾时，梦想更重要</label>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">您期待自己的婚礼现场是？</div>
											<label><input type="radio" name="style" class="radchk" value="传统中式婚礼" />传统中式婚礼</label>
											<label><input type="radio" name="style" class="radchk" value="浪漫西式婚礼" />浪漫西式婚礼</label>
											<label><input type="radio" name="style" class="radchk" value="中西结合婚礼" />中西结合婚礼</label>
											<label><input type="radio" name="style" class="radchk" value="无特殊要求" />无特殊要求</label>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">您希望自己婚礼现场的主色系是？</div>
                                            <?php foreach($color as $val):?>
                                            <label><input type="checkbox" name="color" class="radchk" value="<?php echo $val['name']?>"/><?php echo $val['name']?></label>
                                            <?php endforeach;?>
											<label><input type="checkbox" name="color" class="radchk" value=""/>自定义</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="50" placeholder="请输入您的婚礼颜色">
											</label>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">请选择2-5个词描述您理想的婚礼？</div>
                                            <?php foreach($adj as $val):?>
											<label><input type="checkbox" name="ideals" class="radchk" value="<?php echo $val['name']?>"/><?php echo $val['name']?></label>
							                <?php endforeach;?>
											<label><input type="checkbox" name="ideals" class="radchk" value=""/>自定义</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您理想的婚礼形容词">
											</label>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">在婚礼筹备时，您希望重点筹备的是？</div>
											<label><input type="checkbox" name="emphasis" class="radchk" value="场地布置"/>场地布置</label>
											<label><input type="checkbox" name="emphasis" class="radchk" value="主持人"/>主持人</label>
											<label><input type="checkbox" name="emphasis" class="radchk" value="化妆师"/>化妆师</label>
											<label><input type="checkbox" name="emphasis" class="radchk" value="摄影师"/>摄影师</label>
											<label><input type="checkbox" name="emphasis" class="radchk" value="摄像师"/>摄像师</label>
											<label><input type="checkbox" name="emphasis" class="radchk" value="无特殊要求"/>无特殊要求</label>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">在婚礼过程中，您最看重的是？</div>
											<label><input type="radio" name="importance" class="radchk" value="婚礼场面"/>婚礼场面</label>
											<label><input type="radio" name="importance" class="radchk" value="花费合理"/>花费合理</label>
											<label><input type="radio" name="importance" class="radchk" value="两人的感受"/>两人的感受</label>
											<label><input type="radio" name="importance" class="radchk" value="父母和亲朋的感受"/>父母和亲朋的感受</label><br />
											<label><input type="radio" name="importance" class="radchk" value=""/>其它</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="50" placeholder="请输入您的想法">
											</label>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">请描述您的喜好，以便策划师更好地了解您并为您提供更满意的婚礼方案：（选填）</div>
											<textarea name="moreinfo" class="custextarea com-width300" maxlength="1000" placeholder="请输入其他的要求或喜好"></textarea>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">有喜欢的真实婚礼案例，请输入该案例链接地址：（选填，可填3个）</div>
											<input name="opus" class="textbox" maxlength="30" placeholder="请输入案例链接地址">
										</td>
									</tr>
								</table>
								<input type="hidden" name="shopper_ids" value=""/>
							</div>
							</form>
						</div>
					</div>
						
					<div title="商家信息" class="lftab">
						<div class="tabcont">
							<a href="javascript:void(0)" class="easyui-linkbutton addbsnsinfo" data-options="iconCls:'icon-add'">添加</a>
							<a href="javascript:void(0)" class="easyui-linkbutton del" data-options="iconCls:'icon-no'">删除</a><br /><br />
							<table id="bsnsinfo" class="datagrid"></table>
						</div>
					</div>
				</div>
		</div>
	</div>
</div>
</div>
<div id="edit" class="editbsns">
	<form class="easyuiform" id="bsnsform" method="post" data-options="novalidate:true">
		<table class="bsnstb">
			<tr>
				<td class="tbfield">服务报价：</td>
				<td><input name="price_start" data-options="validType:'number'" class="textbox com-width100 price_start"></td>
				<td class="singlefw">至</td>
				<td><input name="price_end" data-options="validType:'number'" class="textbox com-width100 price_end"></td>
				<td class="tbfield">商家类型：</td>
				<td>
					<select id="shoper_mode" name="shoper_mode" class="selectbox com-width150 shoper_mode">
						<option value="1">个人</option>
						<option value="2">没有注册公司的工作室</option>
						<option value="3">正式注册的公司</option>
					</select>
				</td>
				<td colspan="2">
					<input class="textbox keywords com-width200" name="keywords" maxlength="50" placeholder="交易编号/客户姓名//客户昵称/手机号码//QQ/微信">
				</td>
			</tr>
			<tr>
				<td class="tbfield">案例数量：</td>
				<td><input name="opus_num_start" data-options="validType:'number'" class="textbox com-width100 opus_num_start"></td>
				<td class="singlefw">至</td>
				<td><input name="pous_num_end" data-options="validType:'number'" class="textbox com-width100 pous_num_end"></td>
				<td class="tbfield">所在地区：</td>
				<td colspan="3" class="linkage">
					<select class="com-width100 selectbox province" name="province" id="province"></select>
					<select class="com-width150 selectbox city" name="city" id="city"></select>
					<a href="javascript:void(0)" class="easyui-linkbutton searchbtn c8 pl10 pr10" data-options="iconCls:'icon-search'">查 询</a>
				</td>
			</tr>
		</table>
		<input type="hidden" name="serves" id="serves" value="<?php echo $serves?>"/>
	</form>
	<div class="easyuitabs" id="tbs" style="width:100%;height:284px;">
		<?php if(stristr($serves, "1424")) : ?>
		<div title="主持人" style="padding:10px">
			<table id="wedmaster" class="datagrid bsnstb" style="height:240px;"></table>
		</div>
		<?php endif;?>
		<?php if(stristr($serves, "1425")) : ?>
		<div title="化妆师" style="padding:10px">
			<table id="makeup" class="datagrid bsnstb" style="height:240px;"></table>
		</div>
		<?php endif;?>
		<?php if(stristr($serves, "1423")) : ?>
		<div title="摄影师" style="padding:10px">
			<table id="wedphotoer" class="datagrid bsnstb" style="height:240px;"></table>
		</div>
		<?php endif;?>
		<?php if(stristr($serves, "1426")) : ?>	
		<div title="摄像师" style="padding:10px">
			<table id="wedvideo" class="datagrid bsnstb" style="height:240px;"></table>
		</div>
		<?php endif;?>
		<?php if(stristr($serves, "1427")) : ?>
		<div title="场地布置" style="padding:10px">
			<table id="sitelayout" class="datagrid bsnstb" style="height:240px;"></table>
		</div>
		<?php endif;?>
		<?php if(stristr($serves, "1435")) : ?>
		<div title="策划师" style="padding:10px">
			<table id="wedplanners" class="datagrid bsnstb" style="height:240px;"></table>
		</div>
		<?php endif;?>
	</div>
</div>
<div id="cuslabel" class="cuslabel">
	<input class="cuslabel-searchbox" style="width:80%">
	<div><a class="checkall">全选</a> <a class="uncheckall">反选</a></div>
	<ul class="checkbox-tag"></ul>
</div>
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
	seajs.use("<?php echo $config['srcPath'];?>/js/trademanage/includemand");
</script>
</body>
</html>