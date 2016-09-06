<div class="center-div">
<div class="center-div-header">当前位置：【 商家招投标  >  婚礼需求详情页 】</div>
<div class="comct">
<div id="maincontent">
<div id="systemlogtab" class="easyui-tabs" style="overflow:visible; width: 99%;">
<a href="<?php echo $backurl?>" class="easyui-linkbutton mb10 mr10 mt10" data-options="iconCls:'icon-back'">返 回</a>
<?php if(isset($content["status"]) && $content["status"] == 1) :?>
    <!-- <a href="javascript:" class="easyui-linkbutton addbsns c1 mb10 mr10 mt10" data-options="iconCls:'icon-add'">分配商家</a> -->
<?php elseif(isset($content["status"]) && $content["status"] == 4): ?>
    <!-- <a href="javascript:" class="easyui-linkbutton addbsns c1 mb10 mr10 mt10" data-options="iconCls:'icon-add'">分配商家</a> -->
    <!-- |&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:" class="easyui-linkbutton addexam c1 mb10 mr10 mt10" data-options="iconCls:'icon-add'">审核通过</a> -->
<?php endif;?>

<?php if($content["status"] != 80 && $content["status"] != 99):?>
    <!-- |&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:" class="easyui-linkbutton addclose c1 mb10 mr10 mt10" data-options="iconCls:'icon-add'">关闭交易</a> -->
<?php endif;?>

<input type="hidden" name="bid_id" id="bid_id" value="<?php echo isset($content['id']) ? $content['id'] : '';?>" />

|&nbsp;&nbsp;&nbsp;&nbsp;找商家方式：
<?php if(isset($content["mode"]) && $content["mode"] == 1) : ?>
    <label>招投标</label>&nbsp;&nbsp;&nbsp;&nbsp;
<?php elseif(isset($content["mode"]) && $content["mode"] == 2) : ?>
    <label>指定商家</label>&nbsp;&nbsp;&nbsp;&nbsp;
<?php endif;?>
|&nbsp;&nbsp;&nbsp;&nbsp;需求类型：
<label>找<?php if(isset($content['shopper_service_name'])): echo $content['shopper_service_name'];endif;?></label>&nbsp;&nbsp;&nbsp;&nbsp;
|&nbsp;&nbsp;&nbsp;&nbsp;交易提示：
<label><?php if(isset($remander)): echo $remander;endif;?></label>&nbsp;&nbsp;&nbsp;&nbsp;

<div title="基本信息" class="lftab">
    <form class="easyui-form" method="post" id="baseinfo" data-options="novalidate:true">
        <table class="bigtb">
            <tr>
                <td class="tbfield"><span class="flag">*</span> 客户ID：</td>
                <td>
					<label><?php echo isset($content["id"]) ? $content["id"] : ""?></label>
				</td>
                <td class="tbfield"><span class="flag">*</span> 客户昵称：</td>
                <td colspan="3">
					<label><?php echo isset($content["nickname"]) ? $content["nickname"] : ""?></label>
				</td>
            </tr>
            <tr>
                <td class="tbfield" style="width:100px;"><span class="flag">*</span> 客户姓名：</td>
                <td style="width:160px;">
					<label><?php echo isset($content["realname"]) ? $content["realname"] : ""?></label>
				</td>
                <td class="tbfield" style="width:100px;"> 客户来源：</td>
                <td style="width:160px;">
                    <?php if(isset($base["cli_source"])):?>
                            <?php foreach ($cli_source as $sou) :?>
                                <label> <?php if($sou["id"] == $base["cli_source"]) : ?><?php endif;?><?php echo $sou["name"]?></label>
                            <?php endforeach;?>
                        <?php endif;?>
                </td>
                <td class="tbfield" style="width:100px;"> 获知渠道：</td>
                <td>
                    <?php if(isset($base["channel"])):?>
                            <?php foreach ($channel as $chan) : ?>
                                <label><?php if($chan["id"] == $base["channel"]) : ?><?php endif;?><?php echo $chan["name"]?></label>
                            <?php endforeach;?>
                        <?php endif;?>
                </td>
            </tr>
            <tr>
                <td class="tbfield"><span class="flag">*</span> 客户性别：</td>
                <td class="radiogroup">
                        <?php if(!empty($content["sex"])):?>
                        <label><?php echo $content["sex"];?></label>
                        <?php endif;?>


                </td>
                <td class="tbfield">出生日期：</td>
                <td>
				  	<label><?php echo isset($content["cli_birth"]) ? $content["cli_birth"] : ""?></label>
				</td>
                <td class="tbfield">学历：</td>
                <td>
                    <?php if(isset($content["cli_edu"])) : ?>
						<label><?php echo $content["cli_edu"]?></label>
					<?php endif;?>

                </td>
            </tr>
            <tr>
                <td class="tbfield"><span class="flag">*</span>手机号码：</td>
                <td>
					<label><?php echo isset($content["phone"]) ? $content["phone"] : ""?></label>

				</td>
                <td class="tbfield">固定电话：</td>
                <td>
					<label><?php echo isset($content["cli_tel"]) ? $content["cli_tel"] : ""?></label>

				</td>
                <td class="tbfield">民族：</td>
                <td>
					<?php if(isset($content["cli_nation"])) : ?>
						<label><?php echo $content["cli_nation"]?></label>
					<?php endif;?>
                </td>
            </tr>
            <tr>
                <td class="tbfield">微信：</td>
                <td>
					<label><?php echo isset($content["cli_weixin"]) ? $content["cli_weixin"] : ""?></label>
				</td>
                <td class="tbfield">QQ：</td>
                <td>
					<label><?php echo isset($content["cli_qq"]) ? $content["cli_qq"] : ""?></label>
				</td>
                <td class="tbfield">微博：</td>
                <td>
					<label><?php echo isset($content["cli_weibo"]) ? $content["cli_weibo"] : ""?></label>
				</td>
            </tr>
            <tr>
                <td class="tbfield">邮编：</td>
                <td>
					<label><?php echo isset($content["cli_postcode"]) ? $content["cli_postcode"] : ""?></label>
				</td>
                <td class="tbfield"><span class="flag">*</span>电子邮箱：</td>
                <td>
					<label><?php echo isset($content["email"]) ? $content["email"] : ""?></label>
				</td>
                <td class="tbfield">其他联系方式：</td>
                <td>
					<label><?php echo isset($content["cli_othercontect"]) ? $content["cli_othercontect"] : ""?></label>
				</td>
            </tr>
            <tr>
                <td class="tbfield"><span class="flag">*</span>通讯地址：</td>
                <td class="linkage">
					 <label><input type="hidden" name="cli_location" value="<?php echo isset($content["address"]) ? $content["address"] : ""?>"></label>
                </td>
				<td class="tbfield">详细地址：</td>
                <td colspan="3">
				<label><?php echo isset($content["address_detail"]) ? $content["address_detail"] : ""?></label>
				</td>

            </tr>
            <tr>
                <td class="tbfield">希望联系时间：</td>
                <td colspan="5">
					<label><?php echo isset($content["cli_hope_contect_time"]) ? $content["cli_hope_contect_time"] : ""?></label>
				</td>
            </tr>
            <tr>
                <td class="tbfield">添加时间：</td>
                <td colspan="5"><label><?php echo isset($content["create_time"]) ? $content["create_time"] : ""?></label></td>
            </tr>
            <?php if(!empty($cur_status["sent_time"])):?>
            <tr>
                <td class="tbfield">分配时间：</td>
                <td colspan="5"><label><?php echo $cur_status["sent_time"]; ?></label></td>
            </tr>
            <?php endif;?>

            <?php if(isset($content["verify_time"]) && $content['verify_time'] != '0000-00-00 00:00:00'):?>
                <tr>
                    <td class="tbfield">审核时间：</td>
                    <td colspan="5"><label><?php echo $content["verify_time"]; ?></label></td>
                </tr>
            <?php endif;?>

            <?php if(!empty($cur_status["answer_time"])):?>
                <tr>
                    <td class="tbfield">商家响应时间：</td>
                    <td colspan="5"><label><?php echo $cur_status["answer_time"]; ?></label></td>
                </tr>
            <?php endif;?>

            <?php if(!empty($cur_status["wonbid_time"])):?>
                <tr>
                    <td class="tbfield">招投标完成时间：</td>
                    <td colspan="5"><label><?php echo $cur_status["wonbid_time"]; ?></label></td>
                </tr>
            <?php endif;?>

            <?php if(!empty($cur_status["complete_time"])):?>
                <tr>
                    <td class="tbfield">服务完成时间：</td>
                    <td colspan="5"><label><?php echo $cur_status["complete_time"]; ?></label></td>
                </tr>
            <?php endif;?>

            <?php if(!empty($cur_status["close_time"])):?>
                <tr>
                    <td class="tbfield">需求关闭时间：</td>
                    <td colspan="5"><label><?php echo $cur_status["close_time"]; ?></label></td>
                </tr>
            <?php endif;?>

        </table>
    </form>
</div>

<div title="婚礼需求" id="multstation" class="lftab wedneed">
    <div class="tabcont">
    <form class="easyui-form" method="post" id="wedbaseinfo" data-options="novalidate:true">
        <div class="modulars">
            <h3>婚礼基础信息：</h3>
            <table>
                <tr>
                    <td class="vert-top" style="width:90px;">婚礼日期：</td>
                    <td>
                            <label><?php echo isset($content["wed_date"]) ? $content["wed_date"] : ""?></label>
                    </td>
                </tr>
                <tr>
                    <td class="vert-top">婚礼地点：</td>
                    <td>
                        <input type="hidden" name="wed_location" value="<?php echo isset($content["wed_location"]) ? $content["wed_location"] : ""?>">
                    </td>
                </tr>
                <tr>
                    <td class="vert-top">婚礼场地：</td>
                    <td>
                        <label><?php echo isset($content["wed_place"]) ? $content["wed_place"] : ""?></label>
                    </td>
                </tr>
                <tr>
                    <td class="vert-top">婚宴类型：</td>
                    <td>
                        <label><?php echo isset($content["wed_party_type"]) ? $content["wed_party_type"] : ""?></label>
                    </td>
                </tr>
                <?php if(!empty($content['service_type'])):?>
                    <tr>
                        <td class="vert-top">所需服务：</td>
                        <td>
                            <label><?php echo $demand_info["service_type"]?></label>
                        </td>
                    </tr>

                    <tr>
                        <td class="vert-top">婚礼预算：</td>
                        <td>
                            <label class="optional">
                                <input value="<?php echo isset($content["lowe_amount"]) ? $content["lowe_amount"] : ""?>"/>元
                                ---
                                <input value="<?php echo isset($content["high_amount"]) ? $content["high_amount"] : ""?>"/>元
                            </label>
                        </td>
                    </tr>
                <?php endif;?>
                <tr>
                    <td class="vert-top">其他要求：</td>
                    <td>
                        <label><?php echo isset($content["other_requirement"]) ? $content["other_requirement"] : ""?></label>
                    </td>
                </tr>
            </table>
        </div>
    </form>
    </div>
</div>

<div title="投标商家" class="lftab">
    <form class="easyuiform" id="bsnsforms" method="post" data-options="novalidate:true">
        <table class="bsnstb" style="height:90px;">
            <tr>
                <td class="tbfield">投标状态：</td>
                <td>
                    <select id="order_status" name="order_status" class="selectbox com-width150 order_status" <?php if($cur_status['result']['bid_status'] == 0 || $cur_status['result']['bid_status'] == 1): echo " disabled='true'" ; endif;?>>
                        <option value="">--选择--</option>
                        <option value="unbiding">未应标</option>
                        <option value="bidding">已应标,未中标</option>
                        <option value="wonbid">中标</option>
                    </select>
                </td>
                <td class="tbfield">所在地区：</td>
                <td class="linkage">
                    <select class="com-width100 selectbox country_dlgs" name="country_dlgs" id="country_dlgs"></select>
                    <select class="com-width100 selectbox province_dlgs" name="province_dlgs" id="province_dlgs"></select>
                    <select class="com-width100 selectbox city_dlgs" name="city_dlgs" id="city_dlgs"></select>
                </td>
            </tr>
            <tr>
                <td class="tbfield">意向书状态：</td>
                <td>
                    <select id="letter_status" name="letter_status" class="selectbox com-width150 letter_status" <?php if($cur_status['result']['bid_status'] == 0 || $cur_status['result']['bid_status'] == 1): echo " disabled='true'" ; endif;?>>
                        <option value="">--选择--</option>
                        <option value="pending">待审核</option>
                        <option value="yes">通过</option>
                        <option value="no">未通过</option>
                    </select>
                </td>
                <td colspan="2" class="pl30">
                    <input class="textbox condition_text com-width200" name="condition_text" maxlength="50" placeholder="商家昵称 店铺名称 商家手机">
                    <a href="javascript:void(0)" class="easyui-linkbutton searchdlgbtns c8 pl10 pr10" data-options="iconCls:'icon-search'">查 询</a>
                    <a href="javascript:" class="easyui-linkbutton resetbtn c7">重 置</a>
                </td>
            </tr>
        </table>
    </form>

    <a href="javascript:void(0)" class="easyui-linkbutton del" style="margin-left: 0;margin-bottom: 10px;" data-options="iconCls:'icon-no'">移除</a>
    <table id="bsnsinfo" class="datagrid"></table>
</div>
		
<!-- <div title="沟通记录" class="lftab">
	<div class="tabcont">
		<a href="javascript:void(0)" class="easyui-linkbutton addrecord" data-options="iconCls:'icon-add'">添加记录</a><br /><br />
		<table id="recordtb" class="datagrid"></table>
	</div>
</div>
<div title="内部便签" class="lftab">
	<div class="tabcont">
		<a href="javascript:void(0)" class="easyui-linkbutton addtag" data-options="iconCls:'icon-add'">添加便签</a><br /><br />
		<table id="tagtb" class="datagrid"></table>
	</div>
</div>
<div title="收支记录" class="lftab">
	<div class="tabcont">
		<a href="javascript:void(0)" class="easyui-linkbutton payee" data-options="iconCls:'icon-add'">收款</a>
		<a href="javascript:void(0)" class="easyui-linkbutton payment" data-options="iconCls:'icon-add'">付款</a><br /><br />
		<table id="incometb" class="datagrid"></table>
	</div>
</div>
<div title="查看日志" class="lftab">
	<div class="tabcont">
		<table id="dmlogtb" class="datagrid"></table>
	</div>
</div> -->

</div>
</div>
</div>
</div>
</div>

<div id="editlink" class="recommend">
	<form class="easyuiform" method="post">
		<table>
			<tr>
				<td class="tbfield">沟通对象：</td>
				<td>
					<select class="easyui-combobox com-width150 client" name="client" id="client" data-options="required:true">
						<option value=''>--请选择--</option>
						<option value='新人'>新人</option>
						<option value='商家'>商家</option>
					</select>
				</td>
				<td class="tbfield">沟通人：</td>
				<td><select class="selectbox com-width150 service_uid" name="service_uid" id="service_uid" data-options="required:true"></select></td>
				<td class="tbfield">沟通时间：</td>
				<td><input class="datetimebox com-width150 start_time" id="start_time" name="start_time" data-options="required:true"></td>
			</tr>
			<tr>
				<td class="tbfield">沟通主题：</td>
				<td colspan="5"><input class="textbox com-width300 title" id="title" name="title" data-options="required:true" maxlength="50" placeholder="请输入沟通主题"></td>
			</tr>
			<tr>
				<td class="tbfield">沟通内容：</td>
				<td colspan="5">
					<textarea class="custextarea com-autowidth content" data-options="required:true" name="content" maxlength="500" placeholder="请输入沟通内容"></textarea>
				</td>
			</tr>
		</table>
		<input type="hidden" name="dmid" class="dmid" value="<?php echo isset($content['id']) ? $content['id'] : '';?>" />
        <input type="hidden" name="id" />
	</form>
</div>


<div id="editincome" class="recommend">
	<form class="easyuiform" method="post">
		<table>
			<tr>
				<td class="tbfield">款项类型：</td>
				<td>
					<select class="easyui-combobox com-width150" name="fund_type" data-options="required:true">
						<option value="">--请选择--</option>
						<option value='定金'>定金</option>
						<option value='首款'>首款</option>
						<option value='尾款'>尾款</option>
						<option value='四大金刚中间款'>四大金刚中间款</option>
						<option value='场地布置中间款'>场地布置中间款</option>
					</select>
				</td>
				<td class="tbfield">支付方式：</td>
				<td>
				
					<select class="easyui-combobox com-width150" name="pay_set_id" data-options="required:true">
						<option value="">--请选择--</option>
						<?php foreach($crr as $it){?>
							<option value="<?php echo $it['id']?>"><?php echo $it["name"];?></option>
				        <?php };?>
						   
						 
						
					</select>
				</td>
				<td class="tbfield">支付金额：</td>
				<td>
                    <input class="textbox" name="pay_amount" data-options="required:true,validType:'money'" maxlength="20" placeholder="请输入金额">
                </td>
			</tr>
			<tr>
				<td id="payman" class="tbfield">收款人：</td>
				<td colspan="5"><input class="textbox com-width300" name="pay_man" data-options="required:true" maxlength="50" placeholder="请输入收款人姓名"></td>
			</tr>
			<tr>
				<td class="tbfield">备注：</td>
				<td colspan="5">
					<textarea class="custextarea com-autowidth"  name="comments" maxlength="500" placeholder="请输入备注"></textarea>
				</td>
			</tr>
		</table>
		<input type="hidden" name="dmid" class="dmid" value="<?php echo isset($content['id']) ? $content['id'] : '';?>"/>
        <input type="hidden" name="flagid"/>
	</form>
</div>


<div id="edit_shopper" class="editbsns">
    <form class="easyuiform" id="bsnsform" method="post" data-options="novalidate:true">
        <input type="hidden" name="type" value="<?php echo isset($content['shoper_alia_code']) ? $content['shoper_alia_code'] : '';?>" />
        <table class="bsnstb">
            <tr>
                <td class="tbfield">服务报价：</td>
                <td><input name="price_start" data-options="validType:'number'" class="textbox com-width100 price_start" maxlength="9"></td>
                <td class="singlefw">至</td>
                <td><input name="price_end" data-options="validType:'number'" class="textbox com-width100 price_end" maxlength="9"></td>
                <td class="tbfield">商家类型：</td>
                <td>
                    <select id="shoper_mode" name="shoper_mode" class="selectbox com-width150 shoper_mode">
                        <option value="1">个人</option>
                        <option value="2">没有注册公司的工作室</option>
                        <option value="3">正式注册的公司</option>
                    </select>
                </td>
                <td colspan="2">
                    <input class="textbox keywords com-width200" name="keywords" maxlength="50" placeholder="姓名/工作室名/手机号码">
                </td>
            </tr>
            <tr>
                <td class="tbfield">案例数量：</td>
                <td><input name="opus_num_start" data-options="validType:'number'" class="textbox com-width100 opus_num_start" maxlength="9"></td>
                <td class="singlefw">至</td>
                <td><input name="opus_num_end" data-options="validType:'number'" class="textbox com-width100 pous_num_end" maxlength="9"></td>
                <td class="tbfield">所在地区：</td>
                <td colspan="3" class="linkage">
                    <select class="com-width100 selectbox province" name="province" id="province"></select>
                    <select class="com-width150 selectbox city" name="city" id="city"></select>
                    <a href="javascript:void(0)" class="easyui-linkbutton searchdlgbtn c8 pl10 pr10" data-options="iconCls:'icon-search'">查 询</a>
                </td>
            </tr>
        </table>
    </form>
    <table id="shoper" class="datagrid" style="height:240px;"></table>
</div>


<div id="editag" class="recommend">
	<form class="easyuiform" method="post">
		<table>
			<tr>
				<td class="tbfield" style="width: 78px;">便签内容：</td>
				<td><textarea class="custextarea com-autowidth content" data-options="required:true" name="content" maxlength="500" placeholder="请输入便签内容"></textarea></td>
			</tr>
		</table>
		<input type="hidden" name="dmid" class="dmid" value="<?php echo isset($content['id']) ? $content['id'] : '';?>"/>
	</form>
</div>
	

<div id="closetrad" class="examinedlg">
    <div class="singledl">
        <label>关闭原因：</label>
        <textarea id="closedemand" class="custextarea com-width300 validatebox-text" name="comment" maxlength="30" placeholder="请输入关闭原因"></textarea>
    </div>
</div>

<div id="edit" class="recommend">
    <div id="container">
        <table class="chcntet prices_p" style="width:668px;margin:10px;">
            <tbody>
                <tr>
                    <td colspan="4">
                        推荐服务：
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table class="prices">
                            <tr>
                                <th>服务类型</th>
                                <th>抢单价</th>
                                <th>服务报价</th>
                                <th>服务内容</th>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="chcntet customservice_p" style="width:668px;margin:10px;">
            <tbody>
                <tr>
                    <td colspan="4">
                        定制服务：
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table class="customservice">
                            <tr>
                                <th>服务报价</th>
                                <th>服务内容</th>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="chcntet work_description_p" style="width:768px;margin:10px;">
            <tbody>
                <tr>
                    <td style="width:70px;">合作说明：</td>
                    <td colspan="3" class="work_description"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div id="cuslabel" class="cuslabel">
    <input class="cuslabel-searchbox" style="width:80%">
    <div><a class="checkall">全选</a> <a class="uncheckall">反选</a></div>
    <ul class="checkbox-tag"></ul>
</div>
<input type="hidden" id="cur_page" value="<?php echo $page?>" />
<input type="hidden" id="cur_pagesize" value="<?php echo $pagesize?>" />
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/bsnsbidmanage/detailreview");
</script>
</body>
</html>