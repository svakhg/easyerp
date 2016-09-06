<!-- 右侧主体开始-->
<div class="center-div">

	<div class="center-div-header">当前位置：【 商机管理  >  商机详情 】</div>
	<div class="customrecordiv comct">

		<div id="systemlogtab" class="easyui-tabs" style="overflow:visible;width:99%;">
			<a href="javascript:;" <?php if($detailType == 1 || $tab_value == 2){?>
			data-link="/business/demand/index"
			<?php }else{?>
			data-link="/business/partorder/index"
			<?php }?>
			class="easyui-linkbutton mb10 mr10 mt10" data-options="iconCls:'icon-back'" id="goback">返 回</a>
			
			<?php if(in_array($info["status"],array($status["build"],$status["parted"],$status["parted_n_4"]))) { ?>
			<?php if($allow_modify == 1) { ?>
			<a id="save-btn" href="javascript:" class="easyui-linkbutton save c1 mb10 mr10 mt10" data-options="iconCls:'icon-save'">保存</a>
			<?php if(in_array($info["trade_status"],array($tradestatus["faced"],$tradestatus["no_faced"],0)) && $info['operate_uid'] != 0) { ?>
			<a id="invalid-btn" href="javascript:" class="easyui-linkbutton save c1 mb10 mr10 mt10" data-options="iconCls:'icon-save'">无效订单</a>
			<a id="discard-btn" href="javascript:" class="easyui-linkbutton save c1 mb10 mr10 mt10" data-options="iconCls:'icon-save'">丢单</a>
			<?php } ?>
			<?php } ?>
			<?php }else{ ?>
			<a id="save-btn" href="javascript:" class="easyui-linkbutton save c1 mb10 mr10 mt10" data-options="iconCls:'icon-save'">保存</a>
			<?php } ?>

			<?php if($info["status"] == $status["newadd"] || $info["status"] == $status["follow_next"] || $info["status"] == $status["build"]){?>
			<a id="update-btn" href="javascript:" class="easyui-linkbutton c1 mb10 mr10 mt10 save" data-options="iconCls:'icon-save'">更改商机状态</a>
			<?php }?>

			<?php if(in_array($info['status'], array($status['follow_noanswer'],$status['garbage_invalid_info'],$status['garbage_three_times'],$status['garbage_other'],$status['garbage_repeat'],$status['3days_ago'])) ) { ?>
			<a id="active-btn" href="javascript:" class="easyui-linkbutton c1 mb10 mr10 mt10" data-options="iconCls:'icon-save'">激活</a>
			<?php } ?>
			<font color='red'>提示：请把基础信息和客户需求填写完整并且保存，建单后才能为新人分配商家！！</font>

			<table class="label-tdstyle">
				<tbody>
				<?php if($info["status"] == $status["parted"]) { ?>
					<tr>
						<td class="tbfield">交易编号：</td>
						<td><?php echo $info["tradeno"]?></td>
						
						<td class="tbfield">分单时间：</td>
						<td><?php echo $info['signletime']?></td>
						
						<?php if($info["trade_status"] == $tradestatus["ordered"]) : ?>
						<td class="tbfield">成单时间：</td>
						<td><?php echo $info['updatetime']?></td>
						<?php endif;?>
						
						<?php if($info["trade_status"] == $tradestatus["discard"]) : ?>
						<td class="tbfield">丢单时间：</td>
						<td><?php echo $info['updatetime']?></td>
						<?php endif;?>
						
						<?php if($info["trade_status"] == $tradestatus["invalid"]) : ?>
						<td class="tbfield">处理时间：</td>
						<td><?php echo $info['updatetime']?></td>
						<?php endif;?>
					</tr>
					<tr>
						<?php if($info["trade_status"] == $tradestatus["discard"]) : ?>
						<td class="tbfield">丢单原因：</td>
						<td colspan="3"><?php echo $info["status_note"]?></td>
						<?php endif;?>
						
						<?php if($info["trade_status"] == $tradestatus["invalid"]) : ?>
						<td class="tbfield">无效说明：</td>
						<td colspan="3"><?php echo $info["status_note"]?></td>
						<?php endif;?>
					</tr>
					<tr>
						<td class="tbfield">建单来源：</td>
						<td colspan="3"><?php echo $info["build_source"]?></td>
						
						<td class="tbfield">新人顾问：</td>
						<td colspan="3"><?php echo $info["follower"]?></td>
					</tr>
				<?php }else{ ?>
					<tr>
						<td class="tbfield">交易编号：</td>
						<td><?php echo $info["tradeno"]?></td>

						<td class="tbfield">商机状态：</td>
						<td id="trade_status" data-bus="<?php $status_text = array_flip($status);echo $status_explan[$status_text[$info["status"]]]?>"><?php $status_text = array_flip($status);echo $status_explan[$status_text[$info["status"]]]?></td>

						<td class="tbfield">新人顾问：</td>
						<td><?php echo $info["follower"]?></td>

						<td class="tbfield">添加人员：</td>
						<td><?php echo isset($admin["username"]) ? $admin["username"] : "";?></td>

						<td class="tbfield">提交时间：</td>
						<td><?php echo $info["createtime"]?></td>
					</tr>
				<?php } ?>
					<tr>
						<td class="tbfield">交易状态：</td>
						<td id="trade_status" data-bus="<?php $status_text = array_flip($status);echo $status_explan[$status_text[$info["status"]]]?>"><?php echo $info["trade_status_detail"]?></td>

						<td class="tbfield">运营：</td>
						<td><?php echo $info["operator"]?></td>
						
						<td class="tbfield">n进4时间：</td>
						<td><?php echo $info["ordertime"];?></td>
					</tr>
					
					<!-- <tr>
						<td class="tbfield">活动说明：</td>
						<td colspan="7"><?php //echo $info["activity_desc"]?></td>
					</tr> -->
				</tbody>
			</table>
			<br />

			<div title="基本信息" class="lftab">
				<form class="easyui-form" method="post" id="baseinfo" data-options="novalidate:true">
					<table class="bigtb">
						<tr>
							<td><h3 style="font-weight:bolder; font-size: 14px;">基本信息</h3></td>
							<td></td>
						</tr>
						
						<?php if($flag == 1) : ?>
						<tr>
							<td class="tbfield">客户类型：</td>
							<td id="usertype"><?php echo $info["usertype"]?></td>
						</tr>
						<?php endif;?>
						
						<tr>
							<td class="tbfield">客户姓名：</td>
							<td><input class="textbox com-width150" name="username" maxlength="30" placeholder="请输入客户姓名" value="<?php echo $info["username"]?>"></td>
							<td class="tbfield">客户身份：</td>
							<td>
								<select id="userpart" name="userpart" class="selectbox com-width150 userpart">
									<option value="">--请选择--</option>
									
									<?php foreach ($customer as $key => $val): ?>
									<option value="<?php echo $val; ?>"
											<?php if($val == $info["userpart"]){ echo "selected";}?>
											><?php echo $customer_explan[$key]; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="tbfield">客户手机：</td>
							<td><input class="textbox com-width150" name="mobile" data-options="validType:'mobile'" maxlength="11" placeholder="请输入手机号码" value="<?php echo $info["mobile"]?>" data-options="required:true"></td>
							<td class="tbfield">客户电话：</td>
							<td><input class="textbox com-width150" name="tel" data-options="validType:'phone'" maxlength="30" placeholder="请输入固定电话" value="<?php echo $info["tel"]?>"></td>
						</tr>
						<tr>
							<td class="tbfield">微信：</td>
							<td><input class="textbox com-width150" name="weixin" maxlength="100" placeholder="请输入微信号" value="<?php echo $info["weixin"]?>"></td>
							<td class="tbfield">QQ：</td>
							<td><input class="textbox com-width150" name="qq" data-options="validType:'QQ'" maxlength="15" placeholder="请输入QQ号" value="<?php echo $info["qq"]?>"></td>
						</tr>
						<tr>
							<td class="tbfield">其他联系方式：</td>
							<td><input class="textbox" name="other_contact" maxlength="100" placeholder="请输入其他联系方式" value="<?php echo $info["other_contact"]?>"></td>
						</tr>

					</table>
					<table class="bigtb" <?php if($flag == 1) { echo 'style="display:none"';}?>>
						
						<tr>
							<td><h3 style="font-weight:bolder; font-size: 14px;">处理标记</h3></td>
							<td></td>
						</tr>
						
						<tr>
							<td class="tbfield com-width150"><span class="flag">*</span> 商机来源：</td>
							<td>
								<!--
								因商机来源不可编辑. 生成的时候请添加 disabled;
								-->
								<select id="source" disabled  data-options="required:true" name="source" class="selectbox com-width150 source">
									<option value="">--请选择--</option>
									<?php foreach ($source as $key => $val): ?>
									<option value="<?php echo $val; ?>"
											<?php if($val == $info["source"]){ echo "selected";}?>
											><?php echo $source_explan[$key]; ?></option>
									<?php endforeach; ?>
								</select>
								<!-- <input <?php if (!$source_note_edit):?>readonly<?php endif;?> id="source_note" <?php if($flag != 1) { echo 'data-options="required:true"';}?> maxlength="100" name="source_note" value="<?php echo $info["source_note"]?>" class="textbox source_note" style="width:450px;"> -->
								<input readonly data-options="required:true" maxlength="100" name="source_note" value="<?php echo $info["source_note"]?>" class="textbox source_note" style="width:450px;">
							</td>
						</tr>
						<!-- <tr>
							<td class="tbfield">商机状态：</td>
							<td>
								<select id="status_choose" name="status_choose" class="selectbox com-width100">
									<option value="">--请选择--</option>
									<option  disabled="" value="1">新增</option>
									<option value="2">跟进中</option>
									<option value="3">废商机</option>
									<option disabled="" value="4">已分单</option>
								</select>
								<select id="status" name="status" class="selectbox com-width100 status"></select>
								<input type="hidden" name="status_val" value="<?php //echo $info["status"]?>"/>
							</td>
						</tr> -->
						<tr>
							<td class="tbfield">客户类型： </td>
							<td>
								<select class="selectbox com-width150 usertype" id="usertype" name="usertype">
									<option value="">--请选择--</option>
									<?php foreach($usertype as $val): ?>
									<option value="<?php echo $val; ?>"
											<?php if($val == $info["usertype"]){ echo "selected";}?>
											><?php echo $val; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					</table>
					<input type="hidden" name="bid" value="<?php echo $bid?>"/>
				</form>
			</div>

			<div title="客户需求" class="lftab wedneed">
				<div class="tabcont">
					<form class="easyui-form" method="post" id="wedbaseinfo" data-options="novalidate:true">
						<div class="modulars">
							<table>
								<tr>
									<td class="tbfield">商机类型：</td>
									<td>
										<select id="ordertype" name="ordertype" class="selectbox com-width150 ordertype">
											<option value="">--请选择--</option>
											<?php foreach ($ordertype as $key => $val): ?>
											<option value="<?php echo $val; ?>"
													<?php if($val == $info["ordertype"]){ echo "selected";}?>
													><?php echo $ordertype_explan[$key]; ?></option>
											<?php endforeach; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td><h3 style="font-weight:bolder; font-size: 14px;">基础信息</h3></td>
									<td></td>
								</tr>
								<tr>
									<td class="tbfield">婚礼日期：</td>
									<td>
										<label><input type="radio" name="is_wed_date" value="1" class="radchk" 
													  <?php if(!empty($info["wed_date"])){ echo "checked";}?>
													  />已确定</label>
										<label class="optional"><input name="wed_date" id="wed_date" class="datebox wed_date" value="<?php echo $info["wed_date"]?>"></label><br />
										<label><input type="radio"  name="is_wed_date" value="0" class="radchk"
													  <?php if(empty($info["wed_date"])){ echo "checked";}?>
													  />还未确定</label>
										<label class="optional"><input name="weddate_note" class="textbox com-width200" maxlength="200" placeholder="婚礼时间描述" value="<?php echo $info_extra["weddate_note"]?>"></label>
									</td>
								</tr>
								<tr>
									<td class="tbfield">婚礼地点：</td>
									<td>
										<select class="com-width100 selectbox wed_country" name="wed_country" id="wed_country"></select>
										<select class="com-width100 selectbox wed_province" name="wed_province" id="wed_province"></select>
										<select class="com-width150 selectbox wed_city" name="wed_city" id="wed_city"></select>
										<input type="hidden" name="wed_location" value="<?php echo $info_extra["location"]?>">
									</td>
								</tr>
								<tr class="wed-place-1">
									<td class="tbfield">婚礼场地：</td>
									<td>
										<label><input type="radio" name="is_wed_place" value="1" class="radchk" 
													  <?php if(!empty($info_extra["wed_place"])){ echo "checked";}?>
													  />已确定</label>
										<label class="optional"><input name="wed_place" class="textbox com-width150" maxlength="200" placeholder="请输入具体场地名称" value="<?php echo $info_extra["wed_place"]?>"></label><br />
										<label><input type="radio" name="is_wed_place"  value="0" class="radchk" 
													  <?php if(empty($info_extra["wed_place"])){ echo "checked";}?>
													  />还未确定</label>
										<label class="optional"><input name="wed_place_area" class="textbox com-width150" maxlength="200" placeholder="请输入具体场地名称" value="<?php echo $info_extra["wed_place_area"]?>"></label>
									</td>
								</tr>
								<tr class="wed-place-2">
									<td class="tbfield">场地区域：</td>
									<td>
										<input name="place_area" class="textbox com-width300" maxlength="30" placeholder="场地区域范围要求" value="<?php echo $info_extra["wed_place_area"]?>">
									</td>
								</tr>
								<tr>
									<td class="tbfield">婚宴类型：</td>
									<td>
										<label><input type="radio" name="wed_type" class="radchk" value="<?php echo $wedtype['type_noon']; ?>" 
													  <?php if($wedtype['type_noon'] == $info_extra["wed_type"]){ echo "checked";}?>
													  /><?php echo $wedtype_explan['type_noon']; ?></label>
										<label><input type="radio" name="wed_type" class="radchk" value="<?php echo $wedtype['type_night']; ?>" 
													  <?php if($wedtype['type_night'] == $info_extra["wed_type"]){ echo "checked";}?>
													  /><?php echo $wedtype_explan['type_night']; ?></label>
										<label><input type="radio" name="wed_type" class="radchk" value="<?php echo $wedtype['type_no']; ?>" 
													  <?php if($wedtype['type_no'] == $info_extra["wed_type"]){ echo "checked";}?>
													  /><?php echo $wedtype_explan['type_no']; ?></label>
									</td>
								</tr>
								<tr>
									<td class="tbfield">来宾人数：</td>
									<td>
										<input class="textbox customer" name="guest_from" maxlength="30" placeholder="" value="<?php echo $info_extra["guest_from"]?>">
										至
										<input class="textbox customer" name="guest_to" maxlength="30" placeholder="" value="<?php echo $info_extra["guest_to"]?>">
									</td>
								</tr>
								<!-- <tr>
									<td class="tbfield">预计桌数：</td>
									<td>
										<input class="textbox customer" name="desk_from" maxlength="30" placeholder="" value="<?php //echo $info_extra["desk_from"]?>">
										至
										<input class="textbox customer" name="desk_to" maxlength="30" placeholder="" value="<?php //echo $info_extra["desk_to"]?>">
									</td>
								</tr> -->
								<tr>
									<td class="tbfield">婚宴餐标：</td>
									<td>
										<input class="textbox customer" name="price_from" maxlength="30" placeholder="" value="<?php echo $info_extra["price_from"]?>">
										至
										<input class="textbox customer" name="price_to" maxlength="30" placeholder="" value="<?php echo $info_extra["price_to"]?>">
									</td>
								</tr>
								<tr>
									<td class="tbfield">婚礼预算：</td>
									<td>
										<select id="budget" name="budget" class="selectbox com-width150 budget">
											<option value="">--请选择--</option>
											<?php foreach ($budget as $key => $val): ?>
											<option value="<?php echo $val; ?>"
													<?php if($val == $info_extra["budget"]){ echo "selected";}?>
													><?php echo $budget_explan[$key]; ?></option>
											<?php endforeach; ?>
										</select>
									</td>
								</tr>
								<tr>
                                    <td class="tbfield">婚礼预算备注：</td>
                                    <td>
                                        <textarea class="custextarea com-width300 validatebox-text" name="budget_note" maxlength="500" placeholder=""><?php echo $info_extra['budget_note'];?></textarea>
                                    </td>
                                </tr>

								<tr>
									<td><h3 style="font-weight:bolder; font-size: 14px;">其他信息</h3></td>
									<td></td>
								</tr>

								<tr>
									<td class="tbfield">找商家方式：</td>
									<td>
										<select id="findtype" name="findtype" class="selectbox com-width150 findtype">
											<option value="">--请选择--</option>
											<?php foreach ($findtype as $key => $val): ?>
											<option value="<?php echo $val; ?>"
													<?php if($val == $info_extra["findtype"]){ echo "selected";}?>
													><?php echo $findtype_explan[$key]; ?></option>
											<?php endforeach; ?>
										</select>
										<input id="" maxlength="200" name="findnote" class="textbox findnote" placeholder="推荐商家数量" value="<?php echo $info_extra["findnote"]?>">
									</td>
								</tr>
								<tr>
									<td class="tbfield">期望联系时间及方式：</td>
									<td><input id="wish_contact" maxlength="300" name="wish_contact" class="textbox com-width300" value="<?php echo $info_extra["wish_contact"]?>"></td>
								</tr>
								<tr>
									<td  class="tbfield">
										更多描述：<br />
										<span class="flag">(商家可见)</span>
									</td>
									<td>
										<textarea class="custextarea com-width300 validatebox-text" name="moredesc" maxlength="10000" placeholder="请输入客户备注"><?php echo $info_extra["moredesc"]?></textarea>
									</td>
								</tr>

							</table>
						</div>
						<input type="hidden" name="bid" value="<?php echo $bid?>"/>
					</form>
				</div>
			</div>

			<div title="分配商家" class="lftab">
                <p class="label_box">
                   <?php if($info["status"]== $status['build'] || $info["status"]== $status['parted_n_4']){?>
					<label for="status_all" class="on">
						<input type="radio" name="status" value="all" id="status_all" checked>应标商家（n进4）</label>
                    <label for="" class="disabled">
						<input type="radio" name="status" value="date" id="status_date" >约见商家（4进2）
					</label>
                    <label for="" class="disabled">
						<input type="radio" name="status" value="sign" id="status_sign" >签约商家（2进1）
					</label> 
					<?php }elseif($info["status"]== $status['parted'] && $info["trade_status_detail"] != $tradestatus_explan['ordered']){?>
					   <label for="status_all" class="">
							<input type="radio" name="status" value="all" id="status_all" >应标商家（n进4）</label>
						<label for="status_date" class="on">
							<input type="radio" name="status" value="date" id="status_date" checked>约见商家（4进2）
						</label>
						<label for="" class="disabled">
							<input type="radio" name="status" value="sign" id="status_sign" >签约商家（2进1）
						</label> 
					<?php }elseif($info["status"] == $status['parted'] && $info["trade_status_detail"] == $tradestatus_explan['ordered']){?>
						<label for="status_all" class="">
							<input type="radio" name="status" value="all" id="status_all" >应标商家（n进4）</label>
						<label for="status_date" class="">
							<input type="radio" name="status" value="date" id="status_date" >约见商家（4进2）
						</label>
						<label for="status_sign" class="on">
							<input type="radio" name="status" value="sign" id="status_sign" checked>签约商家（2进1）
						</label> 
					<?php }?>
				</p>
               
				<table id="allot" class="datagrid"></table>
			</div>
			<div title="新人沟通记录" class="lftab">
				<span style="color:red">提示：商家可见！！</span>
				<table id="cusrecord" class="datagrid"></table>
			</div>
			<div title="内部备注" class="lftab">
				<table id="memo" class="datagrid"></table>
			</div>
			<div title="顾问工具" class="lftab">
				<table id="tool" class="datagrid"></table>
			</div>
		</div>

	</div>
</div>
<!-- 右侧主体结束-->
</div>

<!--更改商机状态-->
<div id="changeBusinessStatus" class="examinedlg" style="height:140px;">
	<form class="easyuiform" id="status_formd" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td class="tbfield" style="width:120px;height:70px;">商机状态：</td>
                <td colspan="2">
                    <select id="status_choose" name="status_choose" class="selectbox com-width100" data-options="required:true">
						<option value="">--请选择--</option>
						<?php //foreach ($status as $kg => $vg) : ?>
						<!-- <option value="<?php //echo $vg?>"><?php //echo $status_explan[$kg];?></option> -->
						<?php //endforeach;?>
						<?php if(in_array($info['status'],array(1))){?>
						<option value="follow_next">跟进中</option>
						<?php } ?>
						<?php if(in_array($info['status'],array(1,3))){?>
						<option value="build">已建单</option>
						<?php } ?>
						<?php if(in_array($info['status'],array(1,3,7))){?>
						<option value="invalid">无效</option>
						<?php } ?>
					</select>
					<select id="status" name="status" class="selectbox com-width100 status"></select>
					<input type="hidden" name="status_val" value="<?php $status_text = array_flip($status);echo $status_text[$info["status"]]?>"/>
                </td>
            </tr>
            <tr>
                <td class="tbfield">温馨提示：</td>
                <td colspan="4" id="notice">
                    
                </td>
            </tr>
        </table>
		<input type="hidden" name="bid" value="<?php echo $bid?>"/>
    </form>
</div>


<!--添加商家-->
<div id="team_edit_send" class="editbsns">
    <form class="easyuiform" id="team_bsnsformd" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td class="tbfield">所在地区：</td>
                <td class="linkage">
                    <select class="com-width100 selectbox province_dlg" name="province" id="province_dlg"></select>
                    <select class="com-width150 selectbox city_dlg" name="city" id="city_dlg"></select>
                </td>
                <td class="tbfield">商家等级：</td>
                <td colspan="2">
                    <select id="team_shoper_level" name="grade" class="selectbox com-width150 team_shoper_level">
						<?php foreach ($grade as $vg) : ?>
						<option value="<?php echo $vg["id"]?>"><?php echo $vg["grade_name"]?></option>
						<?php endforeach;?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="tbfield">关键字：</td>
                <td colspan="4">
                    <input name="keywords" class="textbox com-width300 price_start" placeholder="姓名/工作室名/手机号码">
                    <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c8 pl10 pr10" data-options="iconCls:'icon-search'">查 询</a>
                </td>
            </tr>
        </table>
    </form>
    <table id="add_team" class="datagrid"></table>
</div>

<!--添加商家二次窗口-->
<div id="team_confirm" class="editbsns">
   <table id="confirm_team" class="datagrid"></table>
</div>

<!--添加新人沟通记录-->
<div id="add_record" class="examinedlg">
    <form class="easyuiform" id="add_record_form" method="post" data-options="novalidate:true">
        <table class="bsnstb">
			<tr id="shopper_ids_visible_tr">
				<td class="tbfield vert-top">可见商家：</td>
				<td id="shopper_ids_visible_td"></td>
			</tr>
			<tr>
				<td class="tbfield vert-top">沟通时间：</td>
				<td>
					<input name="record_time" class="datetimebox com-width120 record_time" value="<?php echo date('Y-m-d H:i:s')?>">
				</td>
			</tr>
            <tr>
                <td class="tbfield vert-top">沟通内容：</td>
                <td>
                    <textarea class="custextarea com-width300" style="height: 100px;" data-options="required:true" maxlength="2000" placeholder="请输入沟通记录" name="content"></textarea>
                </td>
            </tr>
        </table>
		<input type="hidden" name="type" value="<?php echo 0;?>"/>
		<input type="hidden" id="trade_status_alias" value="<?php echo $info['trade_status_alias']?>">
    </form>
</div>
<!--添加内部备注-->
<div id="add_memo" class="examinedlg">
    <form class="easyuiform" id="add_memo_form" method="post" data-options="novalidate:true">
        <table class="bsnstb">
        	<tr>
        		<td class="tbfield vert-top">备注时间：</td>
        		<td>
        			<input name="record_time" class="datetimebox com-width120 record_time" value="<?php echo date('Y-m-d H:i:s')?>">
        		</td>
        	</tr>
            <tr>
                <td class="tbfield vert-top">备注内容：</td>
                <td>
                    <textarea class="custextarea com-width300" style="height: 100px;" data-options="required:true" maxlength="1000" placeholder="请输入内部备注" name="content"></textarea>
                </td>
            </tr>
        </table>
		<input type="hidden" name="type" value="<?php echo 1;?>"/>
    </form>
</div>
<!--无效订单-->
<div id="invalid_reason" class="examinedlg">
    <form class="easyuiform" id="invalid_reason_form" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td>
                    <textarea class="custextarea com-width300" style="height: 100px;" data-options="required:true" maxlength="200" placeholder="请输入原因，不超过200字" name="status_note"></textarea>
                </td>
            </tr>
        </table>
		<input type="hidden" name="status" value="<?php echo $tradestatus["invalid"]?>"/>
    </form>
</div>
<!--商机丢单-->
<div id="discard_reason" class="examinedlg">
    <form class="easyuiform" id="discard_reason_form" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
				<td>丢单说明<br/>(商家可见):</td>
                <td>
                    <textarea class="custextarea com-width300" style="height: 100px;" data-options="required:true" maxlength="200" placeholder="请输入原因，不超过200字" name="status_note"></textarea>
                </td>
            </tr>
        </table>
		<input type="hidden" name="status" value="<?php echo $tradestatus["discard"]?>"/>
    </form>
</div>
<!--商家丢单-->
<div id="shoper_discard_reason" class="examinedlg">
    <form class="easyuiform" id="shoper_discard_reason_form" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td>
                    <textarea class="custextarea com-width300" style="height: 100px;" data-options="required:true" maxlength="200" placeholder="请输入原因，不超过200字" name="reason"></textarea>
                </td>
            </tr>
        </table>
		<input type="hidden" name="id" value=""/>
    </form>
</div>

<!--发送短信弹层-->
<div id="send_record" class="examinedlg">
    <form class="easyuiform" id="send_record_form" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td class="tbfield vert-top">发送内容：</td>
                <td>
                    <textarea class="custextarea com-width300" style="height: 100px;" data-options="required:true" maxlength="2000" placeholder="请输入发送内容" name="content"></textarea>
                </td>
            </tr>
        </table>
        <input type="hidden" name="bus_id" value="<?php echo $info['id']?>"/>
   </form>
</div>


<!--咨询评分弹层-->
<div id="consult_mark_div" class="examinedlg">
    <form class="easyuiform" id="consult_mark_form" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td class="tbfield vert-top">商家昵称：</td>
                <td id="mark_shop_uname"></td>
            </tr>
            <tr>
                <td class="tbfield vert-top">店铺名称：</td>
                <td id="mark_studio_name"></td>
            </tr>
            <tr>
                <td class="tbfield vert-top">咨询评分：</td>
                <td>
					<select id="consult_mark" name="consult_mark" class="selectbox com-width150 userpart">
						<option value="">--请选择--</option>
						<option value="6">6</option>
						<option value="5">5</option>
						<option value="4">4</option>
						<option value="3">3</option>
						<option value="2">2</option>
						<option value="1">1</option>
					</select>
                </td>
            </tr>
            <tr>
                <td class="tbfield vert-top">评分说明：</td>
                <td>
                    <textarea class="custextarea com-width300" style="height: 100px;" maxlength="2000" placeholder="请输入评分说明" name="mark_description"></textarea>
                </td>
            </tr>
        </table>
        <input type="hidden" name="shop_map_id"/>
        <input type="hidden" name="shop_id"/>
    </form>
</div>

<!--顾问发送短信弹层1-->
<div id="send-sms-1" class="examinedlg">
    <form class="easyuiform" id="send_sms_form1" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td class="tbfield vert-top">短信内容：</td>
                <td>
                    <textarea class="custextarea com-width300" style="height: 100px;" data-options="required:true" name="content" maxlength="500" placeholder="请输入短信内容"><?php echo $tools['tools_guest']?></textarea>
                </td>
            </tr>
            <tr><input type="hidden" name="type" value="1"/><input type="hidden" name="mobile" class="newMobile" value="<?php echo $info["mobile"]?>"/><input type="hidden" name="bid" value="<?php echo $bid?>"/></tr>
        </table>
    </form>
</div>
<!--顾问发送短信弹层2-->
<div id="send-sms-2" class="examinedlg">
    <form class="easyuiform" id="send_sms_form2" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td class="tbfield vert-top">手机号：</td>
                <td>
                    <input type="text" name="mobile" id="sendmobile" data-options="required:true,validType:'mobile'"  maxlength="11" class="textbox com-width150" placeholder="请输入手机号">
                </td>
            </tr>
            <tr>
                <td class="tbfield vert-top">短信内容：</td>
                <td>
                    <textarea class="custextarea com-width300" style="height: 100px;" data-options="required:true" name="content" maxlength="500" placeholder="请输入短信内容"><?php echo $tools['tools_sales']?></textarea>
                </td>
            </tr>
            <tr><input type="hidden" name="type" value="2"/><input type="hidden" name="bid" value="<?php echo $bid?>"/></tr>
        </table>
    </form>
</div>
<!--顾问发送短信弹层3-->
<div id="send-sms-3" class="examinedlg">
    <form class="easyuiform" id="send_sms_form3" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td class="tbfield vert-top">短信内容：</td>
                <td>
                    <textarea class="custextarea com-width300" style="height: 100px;" data-options="required:true" readonly="readonly" name="content" maxlength="500" placeholder="请输入客户备注"><?php echo $tools['tools_save']?></textarea>
                </td>
            </tr>
            <tr>
                <td class="tbfield vert-top">&nbsp;</td>
                <td style="color:#f7893a">挽救短信为固定内容，暂不支持修改</td>
            </tr>
            <tr><input type="hidden" name="type" value="3"/><input type="hidden" name="mobile" class="newMobile" value="<?php echo $info["mobile"]?>"/><input type="hidden" name="bid" value="<?php echo $bid?>"/></tr>
        </table>

    </form>
</div>
<!--顾问发送短信详情预览-->
<div id="sms-info" class="examinedlg">
    <table class="bsnstb">
        <tr>
            <td class="tbfield vert-top com-width60">短信类型：</td>
            <td class="vert-top com-width250" id="sms-type"></td>
        </tr>
        <tr>
            <td class="tbfield vert-top com-width60">收信人电话：</td>
            <td class="vert-top com-width250" id="sms-mobile"></td>
        </tr>
        <tr>
            <td class="tbfield vert-top com-width60">短信内容：</td>
            <td class="vert-top com-width250" id="sms-text"></td>
        </tr>
        <tr>
            <td class="tbfield vert-top com-width60">发送人：</td>
            <td class="vert-top com-width250" id="sms-senduser"></td>
        </tr>
        <tr>
            <td class="tbfield vert-top com-width60">发送时间：</td>
            <td class="vert-top com-width250" id="sms-time"></td>
        </tr>
    </table>
</div>

<input type="hidden" id='business_status' <?php if($info["status"] == $status["parted"]){?>value="1"<?php }else{?>value="0"<?php }?>/>


<input type="hidden" name="type3" id='type3' value="<?php echo $tools["type_3"]?>"/>
<input type="hidden" name="operate_name" value="<?php echo $info["operator"]?>"/>
<input type="hidden" name="operate_mobile" value="<?php echo $info["operator_phone"]?>"/>
<input type="hidden" name="planner_url" value="<?php echo isset($operate_url) ? $operate_url : ""?>"/>


<input type="hidden" id="cur_page" value="1" />
<input type="hidden" id="cur_pagesize" value="10" />

<!--签页值-->
<input type="hidden" name="tab_value" id="tab_id" value="<?php echo $tab_value?>"/>
<!--商机id-->
<input type="hidden" name="bid" id="bid" value="<?php echo $bid?>"/>
<!--详情页类型（商机详情(2)或分单详情(1)）-->
<input type="hidden" name="flag_type" id="flag_type" value="<?php echo $flag?>"/>
<!--商机基本信息是否完善-->
<input type="hidden" name="is_perfect" id="is_perfect" value="<?php echo $is_perfect?>"/>
<!--页面元素是否可以修改，1可以修改，0禁止修改-->
<input type="hidden" name="allow_modify" id="allow_modify" value="<?php echo $allow_modify?>"/>
<?php $this->load->view('header/footer_view.php');?>

<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/business/detail");
</script>
<!--二维码-->

<div id="qr-window" class="delaybd"><div id="qrcode"></div></div>


</body>
</html>