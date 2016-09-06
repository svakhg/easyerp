<div class="center-div">
	<div class="center-div-header">当前位置：【 婚礼需求管理  >  待完善需求 】</div>
	<div class="comct">
		<div id="maincontent">
				<div id="systemlogtab" class="easyui-tabs" style="overflow:visible; width: 99%;">
						<a href="javascript:" class="easyui-linkbutton c1 mb10 mr10 mt10 save" data-options="iconCls:'icon-save'">保存</a>
						 <!-- <a href="javascript:" class="easyui-linkbutton c8 mb10 mr10 mt10 draft" data-options="iconCls:'icon-save'">保存为草稿</a>
 -->						<a href="/trade/examine/index?page=<?php echo $page?>&pagesize=<?php echo $pagesize?>" class="easyui-linkbutton mb10 mr10 mt10" data-options="iconCls:'icon-back'">返 回</a>
						找商家方式：<select class="com-width100 mode" id="mode" name="mode" disabled='true'>
								        <option value="">--请选择--</option>
										<option value="1" <?php if($base['mode']==1){?> selected="selected" <?php }?>>招投标</option>
										<option value="2" <?php if($base['mode']==2){?> selected="selected" <?php }?>>指定商家</option>
						</select><label></label>
						新人顾问：<select class="com-width100 easyui-combobox counselor_uid" id="counselor_uid" name="counselor_uid">
								        <option value="">--请选择--</option>
										<?php foreach ($consultant as $sou) :?>
										<option value="<?php echo $sou["id"]?>" <?php if($sou["id"] == $base["counselor_uid"]) : ?>selected="selected"<?php endif;?>><?php echo $sou["username"]?></option>
										<?php endforeach;?>
						</select>
					<div title="基本信息" class="lftab">
						<form class="easyui-form" method="post" id="baseinfo" data-options="novalidate:true">
						<input type="hidden" class="demand_id" name="demand_id" value="<?php echo isset($base["id"]) ? $base["id"] : 0?>"/>
						<table class="bigtb">
							<tr>
								<td class="tbfield"><span class="flag">*</span> 客户姓名：</td>
								<td><input class="textbox" name="cli_name" data-options="required:true" maxlength="30" placeholder="请输入客户姓名" value="<?php echo isset($base["cli_name"]) ? $base["cli_name"] : ""?>"></td>
								<td class="tbfield"><span class="flag">*</span> 客户来源：</td>
								<td>
									<select class="easyui-combobox com-width100 cli_source" id="cli_source" name="cli_source" disabled='true'>
										<?php if(isset($base["cli_source"])):?>
										<?php foreach ($cli_source as $sou) :?>
										<option value="<?php echo $sou["id"]?>" <?php if($sou["id"] == $base["cli_source"]) : ?>selected="selected"<?php endif;?>><?php echo $sou["name"]?></option>
										<?php endforeach;?>
										<?php endif;?>
									</select>
								</td>
								<td class="tbfield">获知渠道：</td>
								<td>
									 <select class="easyui-combobox com-width100 channel" id="channel" name="channel" disabled='true' >
										<?php if(isset($base["channel"])):?>
										<?php foreach ($channel as $chan) : ?>
										<option value="<?php echo $chan["id"]?>" <?php if($chan["id"] == $base["channel"]) : ?>selected="selected"<?php endif;?>><?php echo $chan["name"]?></option>
										<?php endforeach;?>
										<?php endif;?>
									</select> 
								</td>
							</tr>
							<!-- <tr>
								<td class="tbfield">性别：</td>
								<td class="radiogroup">
									<?php //if(isset($base["cli_gender"])) : ?>
									<label><input type="radio" class="radchk" name="cli_gender" value="1" <?php  //if($base["cli_gender"] == "男"):?>checked="checked"<?php //endif;?> />：男</label>
									<label><input type="radio" class="radchk" name="cli_gender" value="2" <?php  //if($base["cli_gender"] == "女"):?>checked="checked"<?php //endif;?> />：女</label>
									<?php //else:?>
									<label><input type="radio" class="radchk" name="cli_gender" value="1" />：男</label>
									<label><input type="radio" class="radchk" name="cli_gender" value="2" />：女</label>
									<?php //endif;?></td>
								<td class="tbfield">出生日期：</td>
								<td><input class="datebox" class="cli_birth" id="cli_birth" name="cli_birth" value="<?php //echo isset($base["cli_birth"]) ? $base["cli_birth"] : ""?>"></td>
								<td class="tbfield">学历：</td>
								<td>
									<select class="easyui-combobox com-width100 cli_edu" id="cli_edu" name="cli_edu" >
										    <option value="">--请选择--</option>					
											<option value="小学" <?php //if($base["cli_edu"]=='小学'){?> selected="selected" <?php //}?>>小学</option>
											<option value="初中" <?php //if($base["cli_edu"]=='初中'){?> selected="selected" <?php// }?>>初中</option>
											<option value="高中" <?php //if($base["cli_edu"]=='高中'){?> selected="selected" <?php //}?>>高中</option>
											<option value="专科" <?php //if($base["cli_edu"]=='专科'){?> selected="selected" <?php// }?>>专科</option>
											<option value="本科" <?php //if($base["cli_edu"]=='本科'){?> selected="selected" <?php// }?>>本科</option>
											<option value="硕士" <?php //if($base["cli_edu"]=='硕士'){?> selected="selected" <?php// }?>>硕士</option>
											<option value="博士" <?php //if($base["cli_edu"]=='博士'){?> selected="selected" <?php// }?>>博士</option>
											<option value="博士后" <?php //if($base["cli_edu"]=='博士后'){?> selected="selected" <?php //}?>>博士后</option>
											
									</select>
								</td>
							</tr> -->
							<tr>
								<td class="tbfield">手机号码：</td>
								<td><input class="textbox" name="cli_mobile" data-options="required:true,validType:'mobile'" maxlength="11" disabled='true' placeholder="请输入手机号码" value="<?php echo isset($base['cli_mobile']) ? $base['cli_mobile'] : "";?>"></td>
								<td class="tbfield">固定电话：</td>
								<td><input class="textbox" name="cli_tel" maxlength="20" placeholder="请输入固定电话" value="<?php echo isset($base['cli_tel']) ? $base['cli_tel'] : "";?>"></td>
								<td class="tbfield">民族：</td>
								<td>
									<select class="easyui-combobox com-width100 cli_nation" id="cli_nation" name="cli_nation" >
										<option value="">--请选择--</option>
										<?php if(isset($base["cli_nation"])):?>
										<?php foreach ($nation as $sou) :?>
										<option value="<?php echo $sou["id"]?>" <?php if($sou["nation"] == $base["cli_nation"]) : ?>selected="selected"<?php endif;?>><?php echo $sou["nation"]?></option>
										<?php endforeach;?>
										<?php endif;?>
									</select>
								</td>
							</tr>
							<tr>
								<td class="tbfield">微信：</td>
								<td><input class="textbox" name="cli_weixin" data-options="validType:'UNCHS'" maxlength="30" placeholder="请输入微信号" value="<?php echo isset($base['cli_weixin']) ? $base['cli_weixin'] : "";?>"></td>
								<td class="tbfield">QQ：</td>
								<td><input class="textbox" name="cli_qq" data-options="validType:'QQ'" maxlength="30" placeholder="请输入QQ号" value="<?php echo isset($base['cli_weixin']) ? $base['cli_qq'] : "";?>"></td>
								<td class="tbfield">微博：</td>
								<td><input class="textbox" name="cli_weibo" data-options="validType:'UNCHS'" maxlength="30" placeholder="请输入微博号" value="<?php echo isset($base['cli_weixin']) ? $base['cli_weibo'] : "";?>"></td>
							</tr>
							<tr>
								<td class="tbfield">邮编：</td>
								<td><input class="textbox" name="cli_postcode" data-options="validType:'ZIP'" maxlength="6" placeholder="请输入邮编" value="<?php echo isset($base['cli_weixin']) ? $base['cli_postcode'] : "";?>"></td>
								<td class="tbfield">电子邮箱：</td>
								<td><input class="textbox" name="cli_email" data-options="validType:'email'" maxlength="50" placeholder="请输入电子邮箱" value="<?php echo isset($base['cli_weixin']) ? $base['cli_email'] : "";?>"></td>
								<td class="tbfield">其他联系方式：</td>
								<td><input class="textbox" name="cli_othercontect" maxlength="30" placeholder="请输入其他联系方式" value="<?php echo isset($base['cli_weixin']) ? $base['cli_othercontect'] : "";?>"></td>
							</tr>
							<tr>
								<td class="tbfield">通讯地址：</td>
								<td colspan="3" class="linkage">
									<select class="com-width100 selectbox cli_country" name="cli_country" id="cli_country"></select>
									<select class="com-width100 selectbox cli_province" name="cli_province" id="cli_province"></select>
									<select class="com-width100 selectbox cli_city" name="cli_city" id="cli_city"></select>
									<input type="hidden" name="cli_location" value="<?php echo isset($base["cli_location"]) ? $base["cli_location"] : ""?>">
								</td>
								<td colspan="2"><input class="textbox com-autowidth" name="cli_address" maxlength="100" placeholder="详细地址" value="<?php echo isset($base["cli_address"]) ? $base["cli_address"] : ""?>"></td>
							</tr>
							<tr>
								<td class="tbfield">希望联系时间：</td>
								<td><input class="textbox" name="cli_hope_contect_time" maxlength="30" placeholder="请输入希望联系时间" value="<?php echo isset($base['cli_hope_contect_time']) ? $base['cli_hope_contect_time'] : "";?>"></td>
								<td class="tbfield" style="vertical-align:top;">客户备注：</td>
								<td colspan="3"><textarea class="custextarea com-width300" name="comment" maxlength="500" placeholder="请输入客户备注"><?php echo isset($base["comment"]) ? $base["comment"] : ""?></textarea></td>
							</tr>


                             <tr>
								<td class="tbfield">期望联系方式：</td>
								<td colspan="5">
									<?php 
                                      $way = explode(",",$base['cli_hope_contect_way']);   
                                     ?>
									<input type="checkbox" name="cli_hope_contect_way" value="1" <?php if(in_array(1,$way)){?> checked="checked" <?php }?>/>手机&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="checkbox" name="cli_hope_contect_way" value="2" <?php if(in_array(2,$way)){?> checked="checked" <?php }?>/>QQ&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="checkbox" name="cli_hope_contect_way" value="3" <?php if(in_array(3,$way)){?> checked="checked" <?php }?>/>微信&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="checkbox" name="cli_hope_contect_way" value="4" <?php if(in_array(4,$way)){?> checked="checked" <?php }?>/>其他联系方式&nbsp;&nbsp;&nbsp;&nbsp;
								
								</td>
							</tr>




							<tr>
								<td class="tbfield">客户标签：</td>
								<td class="text-left"><a class="abtn addlabel">+选择</a>
						<input type="hidden" name="cli_tag" id="cli_tag" value="<?php echo isset($base["cli_tag"]) ? $base["cli_tag"] : ""?>"/></td>
								<td colspan="4" class="tag-container" width="519"></td>
							</tr>
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
										<td class="vert-top" style="width:75px;">婚礼日期：</td>
										
										<td>
										  <?php if(isset($base['wed_date_sure'])){
										  	if($base['wed_date_sure']==1){
										  	?>
											<label><input type="radio" name="wed_date_sure" value="1" class="radchk" checked="checked"/>已确定</label>
											<label class="optional"><input name="wed_date" id="wed_date" class="datebox wed_date" value="<?php echo $base['wed_date']?>"></label><br />
											<label><input type="radio" name="wed_date_sure" value="0" class="radchk" />还未确定</label>
											<label class="optional"><input name="wed_date_notsure" class="textbox com-width300"  maxlength="30" placeholder="为了更好的服务您，请您说下您举办婚礼的大致时间"></label>

										 

											
											<?php } 
											if($base['wed_date_sure']==0){?>
										    <label><input type="radio" name="wed_date_sure" value="1" class="radchk" />已确定</label>
											<label class="optional"><input name="wed_date" id="wed_date" class="datebox wed_date" value=""></label><br />
											<label><input type="radio" name="wed_date_sure" value="0" class="radchk" checked="checked"/>还未确定</label>
											<label class="optional"><input name="wed_date_notsure" class="textbox com-width300"  maxlength="30" placeholder="为了更好的服务您，请您说下您举办婚礼的大致时间" value="<?php echo $base['wed_date']?>"></label>
											
										 <?php }}else{?>
											

											<label><input type="radio" name="wed_date_sure" value="1" class="radchk" />已确定</label>
											<label class="optional"><input name="wed_date" id="wed_date" class="datebox wed_date" value=""></label><br />
											<label><input type="radio" name="wed_date_sure" value="0" class="radchk" />还未确定</label>
											<label class="optional"><input name="wed_date_notsure" class="textbox com-width300"  maxlength="30" placeholder="为了更好的服务您，请您说下您举办婚礼的大致时间"></label>
										<?php }?>

										</td>

									</tr>
									<tr>
										<td class="vert-top">婚礼地点：</td>
										<td>
											<select class="com-width100 selectbox wed_country" name="wed_country" id="wed_country"></select>
											<select class="com-width100 selectbox wed_province" name="wed_province" id="wed_province"></select>
											<select class="com-width100 selectbox wed_city" name="wed_city" id="wed_city"></select>
									
											<input type="hidden" name="wed_location" value="<?php echo isset($base["wed_location"]) ? $base["wed_location"] : ""?>">
										</td>
									</tr>
									<tr>
										<td class="vert-top">婚礼场地：</td>
										<td>
                                           <?php
										   if($base['wed_place']=="还未确定"){ ?>
										      <label>
											     <input type="radio" name="wed_place" class="radchk" value="还未确定"  checked="checked" />还未确定
											</label>
											<label>
											    <input type="radio" name="wed_place" class="radchk" value="已确定"   />已确定
											</label>
                                            <label class="optional">
											      <input class="textbox customer"  maxlength="30" placeholder="请输入具体场地名称" value=""><br />
											</label> 
										 <?php  }elseif(!empty($base['wed_place'])&&$base['wed_place']!="还未确定"){ ?>

										         <label>
											     <input type="radio" name="wed_place" class="radchk" value="还未确定"   />还未确定
											</label>
											<label>
											    <input type="radio" name="wed_place" class="radchk" value="已确定" checked="checked"  />已确定
											</label>
                                            <label class="optional">
											      <input class="textbox customer"  maxlength="30" placeholder="请输入具体场地名称" value="<?php echo isset($base["wed_place"]) ? $base["wed_place"] : ""?>"><br />
											</label> 

										<?php }elseif(empty($base['wed_place'])){ ?>
                                                <label>
											     <input type="radio" name="wed_place" class="radchk" value="还未确定"    checked="checked" />还未确定
											</label>
											<label>
											    <input type="radio" name="wed_place" class="radchk" value="已确定"   />已确定
											</label>
                                            <label class="optional">
											      <input class="textbox customer"  maxlength="30" placeholder="请输入具体场地名称" value=""><br />
											</label> 

										 <?php  }?>

											
											
										</td>
									</tr>
									<tr>
										<td class="vert-top">婚宴类型：</td>
										<td>
											<label><input type="radio" name="wed_party_type" class="radchk" value="午宴"  <?php if($base['wed_party_type']=="午宴"){?> checked="checked" <?php }?>/>午宴</label>
											<label><input type="radio" name="wed_party_type" class="radchk" value="晚宴"   <?php if($base['wed_party_type']=="晚宴"){?> checked="checked" <?php }?>/>晚宴</label>
											<label><input type="radio" name="wed_party_type" class="radchk" value="还未确定" <?php if($base['wed_party_type']=="还未确定"){?> checked="checked" <?php }?>/>还未确定</label>
										</td>
									</tr>
								</table>
							</div>
							</form>
							<form class="easyui-form" method="post" id="multwedneed" data-options="novalidate:true">
							<?php if($wedmaster) : ?>
								<div class="modulars compere" id="wedmaster_f">
								<h3>对主持人的要求：</h3>
								<table>

									<tr>
										<td>
											<div class="question">预定主持人，您的预算是？</div>
											<?php 
											   foreach($wedmaster as $key => $val){
												   if($val['alias']=="amount"){
													   $amount = $val['answer'];
												   }
											    
											   }
											   if(isset($amount)){
													$amount = $amount;
											   }else{
													$amount = "";
											   }
											 
											?>
											<label>
												<input type="radio"<?php if($amount=="2000以下"){?> checked="checked" <?php }?> name="wedmaster_amount" class="radchk" value="2000以下"/>2000以下
											</label>
											<label>
											  <input type="radio" <?php if($amount=="2001-4000"){?> checked="checked" <?php }?> name="wedmaster_amount" class="radchk" value="2001-4000"/>2001-4000
											</label>
											<label>
												<input type="radio" <?php if($amount=="4001-6000"){?> checked="checked" <?php }?> name="wedmaster_amount" class="radchk" value="4001-6000"/>4001-6000
											</label>
											<label>
												<input type="radio" <?php if($amount=="6000以上"){?> checked="checked" <?php }?> name="wedmaster_amount" class="radchk" value="6000以上"/>6000以上
											</label><br />
											<label>
												<input type="radio" <?php if($amount=="自定义"){?> checked="checked" <?php }?> name="wedmaster_amount" class="radchk" value=""/>自定义
											</label>
											<label class="optional">
												<input class="textbox customer" maxlength="30" placeholder="请输入您的预算" value="<?php echo $amount?>">（元）
											</label>
											
										</td>
									</tr>
							<?php if($base['mode']==1){?>
									<tr class="contrsex">
										<td>
											<div class="question">对于为您服务的婚礼主持人，您的要求是？</div>
											性别：
											<?php 
											   foreach($wedmaster as $key => $val){
												   if($val['alias']=="people"){
													   $people = $val['answer'];
												   }
											    
											   }
											   //echo $people;
											 $people = explode('||', @$people);
											// echo $people;
											?>
											<label><input type="radio" name="wedmaster_people" class="radchk" value="男" <?php if($people[0]=="男"){?> checked="checked" <?php }?>/>男</label>
											<label><input type="radio" name="wedmaster_people" class="radchk" value="女" <?php if($people[0]=="女"){?> checked="checked" <?php }?>/>女</label>
											<label><input type="radio" name="wedmaster_people" class="radchk" value="无特殊要求" <?php if($people[0]=="无特殊要求"){?> checked="checked" <?php }?>/>无特殊要求</label><br />
											<div class="mangroup hidden">
											身高(男):	<label><input type="radio" name="height" class="radchk" value="170以下" <?php if($people[0]=="男"&&$people[1]=="170以下"){?> checked="checked" <?php }?>/>170以下</label>
													<label><input type="radio" name="height" class="radchk" value="170-175" <?php if($people[0]=="男"&&$people[1]=="170-175"){?> checked="checked" <?php }?>/>170-175</label>
													<label><input type="radio" name="height" class="radchk" value="175-185" <?php if($people[0]=="男"&&$people[1]=="175-185"){?> checked="checked" <?php }?>/>175-185</label>
													<label><input type="radio" name="height" class="radchk" value="185以上" <?php if($people[0]=="男"&&$people[1]=="185以上"){?> checked="checked" <?php }?>/>185以上</label>
													<label><input type="radio" name="height" class="radchk" value="无特殊要求" <?php if($people[0]=="男"&&$people[1]=="无特殊要求"){?> checked="checked" <?php }?>/>无特殊要求</label>
											</div>
											<div class="womangroup hidden">
											身高(女):		<label><input type="radio" name="height" class="radchk" value="160以下" <?php if($people[0]=="女"&&$people[1]=="160以下"){?> checked="checked" <?php }?>/>160以下</label>
													<label><input type="radio" name="height" class="radchk" value="160-165" <?php if($people[0]=="女"&&$people[1]=="160-165"){?> checked="checked" <?php }?>/>160-165</label>
													<label><input type="radio" name="height" class="radchk" value="165-170" <?php if($people[0]=="女"&&$people[1]=="165-170"){?> checked="checked" <?php }?>/>165-170</label>
													<label><input type="radio" name="height" class="radchk" value="170以上" <?php if($people[0]=="女"&&$people[1]=="170以上"){?> checked="checked" <?php }?>/>170以上</label>
													<label><input type="radio" name="height" class="radchk" value="无特殊要求" <?php if($people[0]=="女"&&$people[1]=="无特殊要求"){?> checked="checked" <?php }?>/>无特殊要求</label>
											</div>
										</td>
									</tr>
									<?php }?>
									<tr>
										<td>
											<div class="question">对于主持人及其服务，您是否还有其他的要求或喜好？（选填）</div>
												<?php 
											   foreach($wedmaster as $key => $val){
												   if($val['alias']=="remark"){
													   $remark = $val['answer'];
												   }
											    
											   }
											   if(isset($remark)){
													$remark = $remark;
											   }else{
													$remark = "";
											   }
											?>
											<textarea name="wedmaster_remark" class="custextarea com-width300" maxlength="500" placeholder="请输入其他的要求或喜好"><?php echo $remark;?></textarea>
										</td>
									</tr>




								</table>
								</div>
								
							<?php endif;?>
							<?php if($makeup) : ?>
							<div class="modulars makeup" id="makeup_f">
							<h3>对化妆师的要求：</h3>
								<table>
										<tr>
										<td>
											<div class="question">预定化妆师，您的预算是？</div>
												<?php 
											   foreach($makeup as $key => $val){
												   if($val['alias']=="amount"){
													   $amount = $val['answer'];
												   }
											    
											   }
											   if(isset($amount)){
													$amount = $amount;
											   }else{
													$amount = "";
											   }
											 
											?>
											<label><input type="radio" name="makeup_amount" class="radchk" value="2000以下" <?php if($amount=="2000以下"){?> checked="checked" <?php }?>/>2000以下</label>
											<label><input type="radio" name="makeup_amount" class="radchk" value="2001-4000" <?php if($amount=="2001-4000"){?> checked="checked" <?php }?>/>2001-4000</label>
											<label><input type="radio" name="makeup_amount" class="radchk" value="4001-6000" <?php if($amount=="4001-6000"){?> checked="checked" <?php }?>/>4001-6000</label>
											<label><input type="radio" name="makeup_amount" class="radchk" value="6000以上" <?php if($amount=="6000以上"){?> checked="checked" <?php }?>/>6000以上</label><br />
											<label><input type="radio" name="makeup_amount" class="radchk" value="" <?php if($amount=="自定义"){?> checked="checked" <?php }?>/>自定义</label>
											<label class="optional">
											<input class="textbox customer" maxlength="30" placeholder="请输入您的预算">（元）
											</label>
										</td>
									</tr>
							<?php if($base["mode"] == 1){?>
									<tr class="contrsex">
										<td>
											<div class="question">对于为您服务的婚礼化妆师，您的要求是？</div>
											
											性别
											<?php 
											   foreach($makeup as $key => $val){
												   if($val['alias']=="people"){
													   $people = $val['answer'];
												   }
											    
											   }
											   if(isset($people)){
													$people = $people;
											   }else{
													$people = "";
											   }
											 
											?>
											<label><input type="radio"  <?php if($people=="男"){?> checked="checked" <?php }?> name="makeup_people" class="radchk" value="男"/>男</label>
											<label><input type="radio" name="makeup_people" class="radchk" value="女" <?php if($people=="女"){?> checked="checked" <?php }?>/>女</label>
											<label><input type="radio" name="makeup_people" class="radchk" value="无特殊要求" <?php if($people=="无特殊要求"){?> checked="checked" <?php }?>/>无特殊要求</label>
										</td>
									</tr>
								<?php }?>
									<tr>
										<td>
											<div class="question">您计划婚礼当天选几套造型？</div>
											<?php 
											   foreach($makeup as $key => $val){
												   if($val['alias']=="modeling"){
													   $modeling = $val['answer'];
												   }
											    
											   }
											   if(isset($modeling)){
													$modeling = $modeling;
											   }else{
													$modeling = "";
											   }
										    //echo $modeling;
											?>				
											<label><input type="radio" checked="checked" name="modeling" class="radchk" value="1套" <?php if($modeling=="1套"){?> checked="checked" <?php }?>/>1套</label>
											<label><input type="radio" name="modeling" class="radchk" value="2套"  <?php if($modeling=="2套"){?> checked="checked" <?php }?>/>2套</label>
											<label><input type="radio" name="modeling" class="radchk" value="3套"  <?php if($modeling=="3套"){?> checked="checked" <?php }?>/>3套</label>
											<label><input type="radio" name="modeling" class="radchk" value="4套"  <?php if($modeling=="4套"){?> checked="checked" <?php }?>/>4套</label>
											<label><input type="radio" name="modeling" class="radchk" value="需要和化妆师沟通"  <?php if($modeling=="需要和化妆师沟通"){?> checked="checked" <?php }?>/>需要和化妆师沟通</label>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">对于化妆师及其服务，您是否还有其他的要求或喜好？（选填）</div>
											<?php 
											   foreach($makeup as $key => $val){
												   if($val['alias']=="remark"){
													   $remark = $val['answer'];
												   }
											    
											   }
											   if(isset($remark)){
													$remark = $remark;
											   }else{
													$remark = "";
											   }
											?>
											<textarea name="makeup_remark" class="custextarea com-width300" maxlength="500" placeholder="请输入其他的要求或喜好"><?php echo $remark?></textarea>
										</td>
									</tr>
								</table>
							</div>
							
							<?php endif;?>
							<?php if($wedphotoer) : ?>
								<div class="modulars photoman" id="wedphotoer_f">
								<h3>对摄影师的要求：</h3>
								<table>
										<tr>
										<td>
											<div class="question">您需要的摄影服务是？（可多选）</div>
											<?php 
											   foreach($wedphotoer as $key => $val){
												   if($val['alias']=="service"){
													   $service[] = $val['answer'];
												   }
											    
											   }
											?>
											<?php if($wedphotoer['service']['answer']=="婚礼当天跟拍"){?>
											<input type="checkbox" name="wedphotoer_service" class="radchk" id="wed_ps" value="婚纱照拍摄" <?php if(in_array("婚纱照拍摄",$service)){?> checked="checked" <?php }?> disabled='true'/>婚纱照拍摄</label>
													<label><input type="checkbox" name="wedphotoer_service" class="radchk" id="wed_gp" value="婚礼当天跟拍" <?php if(in_array("婚礼当天跟拍",$service)){?> checked="checked" <?php }?> />婚礼当天跟拍</label>
											<?php }else if($wedphotoer['service']['answer']=="婚纱照拍摄"){?>
												<label><input type="checkbox" name="wedphotoer_service" class="radchk" id="wed_ps" value="婚纱照拍摄" <?php if(in_array("婚纱照拍摄",$service)){?> checked="checked" <?php }?>/>婚纱照拍摄</label>
												<label><input type="checkbox" name="wedphotoer_service" class="radchk" id="wed_gp" value="婚礼当天跟拍" <?php if(in_array("婚礼当天跟拍",$service)){?> checked="checked" <?php }?> disabled/>婚礼当天跟拍</label>
											<?php }?>											
										</td>
									</tr>
									<tr class="wed_ps_target hidden">
										<td>
											<div class="question">对于婚纱照拍摄，您的预算是？</div>
												<?php 
											   foreach($wedphotoer as $key => $val){
												   if($val['alias']=="hspz_amount"){
													   $hspz_amount = $val['answer'];
												   }
											    
											   }
											   if(isset($hspz_amount)){
													$hspz_amount = $hspz_amount;
											   }else{
													$hspz_amount = "";
											   }
											?>
											 <label><input type="radio" name="hspz_amount" class="radchk" value="5000以下" <?php if($hspz_amount=='5000以下'){?> checked="checked" <?php }?>/>5000以下</label>
											<label><input type="radio" name="hspz_amount" class="radchk" value="5000-8000" <?php if($hspz_amount=='5000-8000'){?> checked="checked" <?php }?>/>5000-8000</label>
											<label><input type="radio" name="hspz_amount" class="radchk" value="8000-13000" <?php if($hspz_amount=='8000-13000'){?> checked="checked" <?php }?>/>8000-13000</label>
											<label><input type="radio" name="hspz_amount" class="radchk" value="13000以上" <?php if($hspz_amount=='13000以上'){?> checked="checked" <?php }?>/>13000以上</label><br />
											<label><input type="radio" name="hspz_amount" class="radchk" value="" <?php if($hspz_amount=='自定义'){?> checked="checked" <?php }?>/>自定义</label>
											<label class="optional">
											<input class="textbox customer" maxlength="30" placeholder="请输入您的预算" value="<?php echo $hspz_amount?>">（元）
											</label> 
										</td>
									</tr>
									<tr class="wed_gp_target hidden">
										<td>
											<div class="question">对于婚礼当天跟拍，您的预算是？</div>
											<?php 
											   foreach($wedphotoer as $key => $val){
												   if($val['alias']=="hlgp_amount"){
													   $hlgp_amount = $val['answer'];
												   }
											    
											   }
											   if(isset($hlgp_amount)){
													$hlgp_amount = $hlgp_amount;
											   }else{
													$hlgp_amount = "";
											   }
									
											?>
											<label><input type="radio" name="wedphotoer_hlgp_amount" class="radchk" value="3000以下" <?php if($hlgp_amount=="3000以下"){?> checked="checked" <?php }?>/>3000以下</label>
											<label><input type="radio" name="wedphotoer_hlgp_amount" class="radchk" value="3001-5000"  <?php if($hlgp_amount=="3001-5000"){?> checked="checked" <?php }?>/>3001-5000</label>
											<label><input type="radio" name="wedphotoer_hlgp_amount" class="radchk" value="5001-9000"  <?php if($hlgp_amount=="5001-9000"){?> checked="checked" <?php }?>/>5001-9000</label>
											<label><input type="radio" name="wedphotoer_hlgp_amount" class="radchk" value="9000以上"  <?php if($hlgp_amount=="9000以上"){?> checked="checked" <?php }?>/>9000以上</label><br />
											<label><input type="radio" name="wedphotoer_hlgp_amount" class="radchk" value=""  <?php if($hlgp_amount=="自定义"){?> checked="checked" <?php }?>/>自定义</label>
											<label class="optional">
											<input class="textbox customer" maxlength="30" placeholder="请输入您的预算" value="<?php echo $hlgp_amount?>">（元）
											</label>
										</td>
									</tr>
									<tr class="wed_gp_target hidden">
										<td>
											<div class="question">希望选择哪种跟拍方案？</div>
											<?php 
											   foreach($wedphotoer as $key => $val){
												   if($val['alias']=="hlgp_scheme"){
													   $hlgp_scheme = $val['answer'];
												   }
											    
											   }
											   if(isset($hlgp_scheme)){
													$hlgp_scheme = $hlgp_scheme;
											   }else{
													$hlgp_scheme = "";
											   }
									
											?>

											<label><input type="radio" checked="checked" name="wedphotoer_hlgp_scheme" class="radchk" value="单机位" <?php if($hlgp_scheme=="单机位"){?> checked="checked" <?php }?>/>单机位</label>
											<label><input type="radio" name="wedphotoer_hlgp_scheme" class="radchk" value="双机位" <?php if($hlgp_scheme=="双机位"){?> checked="checked" <?php }?>/>双机位</label>
											<label><input type="radio" name="wedphotoer_hlgp_scheme" class="radchk" value="三机位以上" <?php if($hlgp_scheme=="三机位以上"){?> checked="checked" <?php }?>/>三机位以上</label>
											<label><input type="radio" name="wedphotoer_hlgp_scheme" class="radchk" value="需要和摄影师沟通" <?php if($hlgp_scheme=="需要和摄影师沟通"){?> checked="checked" <?php }?>/>需要和摄影师沟通</label>
										</td>
									</tr>
									<?php if($base['mode'] == 1){?>
									<tr class="contrsex">
										<td>
											<div class="question">对于为您服务的婚礼摄像师，您的要求是？</div>
											<?php 
											   foreach($wedphotoer as $key => $val){
												 
												   if($val['alias']=="people"){
													   $people = $val['answer'];
												   }
											    
											   }
											   if(isset($people)){
													$people = $people;
											   }else{
													$people = "";
											   }
											
											?>
											性别：<label><input type="radio" name="wedphotoer_people" class="radchk" value="男" <?php if($people=="男"){?> checked="checked" <?php }?>/>男</label>
											<label><input type="radio" name="wedphotoer_people" class="radchk" value="女" <?php if($people=="女"){?> checked="checked" <?php }?>/>女</label>
											<label><input type="radio" name="wedphotoer_people" class="radchk" value="无特殊要求" <?php if($people=="无特殊要求"){?> checked="checked" <?php }?>/>无特殊要求</label>
										</td>
									</tr>
									<?php }?>
									<tr>
										<td>
											<div class="question">对于摄像师及其服务，您是否还有其他的要求或喜好？（选填）</div>
												<?php 
											   foreach($wedphotoer as $key => $val){
												   if($val['alias']=="remark"){
													   $remark = $val['answer'];
												   }
											    
											   }
									
											?>
											<textarea name="wedphotoer_remark" class="custextarea com-width300" maxlength="500" placeholder="请输入其他的要求或喜好"><?php echo $remark?></textarea>
										</td>
									</tr>
								</table>
								</div>
							
							<?php endif;?>
							<?php if($wedvideo) : ?>	
								<div class="modulars cameraman" id="wedvideo_f">
								<h3>对摄像师的要求：</h3>
								<table>
									<tr>
										<td>
											<div class="question">您需要的摄像服务是？（可多选）</div>
												<?php 
											   foreach($wedvideo as $key => $val){
												   if($val['alias']=="service"){
													   $service[] = $val['answer'];
												   }
											    
											   }
											?>
											<?php if($wedvideo['service']['answer']=="婚礼当天跟拍"){?>
											<label><input type="checkbox" name="wedvideo_service" class="radchk" id="wed_wdy"  value="婚礼前的爱情微电影" <?php if(in_array("婚礼前的爱情微电影",$service)){?> checked="checked" <?php }?> disabled='true'/>婚礼前的爱情微电影</label>
											<label><input type="checkbox" name="wedvideo_service" class="radchk" id="wedvideo_gp" value="婚礼当天跟拍" <?php if(in_array("婚礼当天跟拍",$service)){?> checked="checked" <?php }?>/>婚礼当天跟拍</label>
											<?php } else if($wedvideo['service']['answer']=="婚礼前的爱情微电影"){?>
													<label><input type="checkbox" name="wedvideo_service" class="radchk" id="wed_wdy"  value="婚礼前的爱情微电影" <?php if(in_array("婚礼前的爱情微电影",$service)){?> checked="checked" <?php }?> />婚礼前的爱情微电影</label>
											<label><input type="checkbox" name="wedvideo_service" class="radchk" id="wedvideo_gp" value="婚礼当天跟拍" <?php if(in_array("婚礼当天跟拍",$service)){?> checked="checked" <?php }?> disabled='true'/>婚礼当天跟拍</label>

											<?php }?>
											
										</td>
									</tr>
									<tr class="wed_wdy_target hidden">
										<td>
											<div class="question">对于爱情微电影，您的预算是？</div>
											<?php 
											   foreach($wedvideo as $key => $val){
												   if($val['alias']=="wdy_amount"){
													   $wdy_amount = $val['answer'];
												   }
											    
											   }
											   if(isset($wdy_amount)){
													$wdy_amount = $wdy_amount;
											   }else{
													$wdy_amount = "";
											   }
											   
									
											?>
											<label><input type="radio" checked="checked" name="wdy_amount" class="radchk" value="10000以下" <?php if($wdy_amount=="10000以下"){?> checked="checked" <?php }?>/>10000以下</label>
											<label><input type="radio" name="wdy_amount" class="radchk" value="10001-20000" <?php if($wdy_amount=="10001-20000"){?> checked="checked" <?php }?>/>10001-20000</label>
											<label><input type="radio" name="wdy_amount" class="radchk" value="21000-30000" <?php if($wdy_amount=="21000-30000"){?> checked="checked" <?php }?>/>21000-30000</label>
											<label><input type="radio" name="wdy_amount" class="radchk" value="30000以上" <?php if($wdy_amount=="30000以上"){?> checked="checked" <?php }?>/>30000以上</label><br />
											<label><input type="radio" name="wdy_amount" class="radchk" value="" <?php if($wdy_amount=="自定义"){?> checked="checked" <?php }?>/>自定义</label>
											<label class="optional">
											<input class="textbox customer" maxlength="30" placeholder="请输入您的预算" value="<?php echo $wdy_amount?>">（元）
											</label>
										</td>
									</tr>
									<tr class="wedvideo_gp_target hidden">
										<td>
											<div class="question">对于婚礼当天跟拍，您的预算是？</div>
											<?php 
											   foreach($wedvideo as $key => $val){
												   if($val['alias']=="hlgp_amount"){
													   $hlgp_amount = $val['answer'];
												   }
											    
											   }
											   if(isset($hlgp_amount)){
													$hlgp_amount = $hlgp_amount;
											   }else{
													$hlgp_amount = "";
											   }
											   
									
											?>
											<label><input type="radio" checked="checked" name="wedvideo_hlgp_amount" class="radchk" value="4000以下" <?php if($hlgp_amount=="4000以下"){?> checked="checked" <?php }?>/>4000以下</label>
											<label><input type="radio" name="wedvideo_hlgp_amount" class="radchk" value="4001-8000" <?php if($hlgp_amount=="4001-8000"){?> checked="checked" <?php }?>/>4001-8000</label>
											<label><input type="radio" name="wedvideo_hlgp_amount" class="radchk" value="8001-15000" <?php if($hlgp_amount=="8001-15000"){?> checked="checked" <?php }?>/>8001-15000</label>
											<label><input type="radio" name="wedvideo_hlgp_amount" class="radchk" value="15000以上" <?php if($hlgp_amount=="15000以上"){?> checked="checked" <?php }?>/>15000以上</label><br />
											<label><input type="radio" name="wedvideo_hlgp_amount" class="radchk" value="" <?php if($hlgp_amount=="自定义"){?> checked="checked" <?php }?>/>自定义</label>
											<label class="optional">
											<input class="textbox customer" maxlength="30" placeholder="请输入您的预算" value="<?php echo $hlgp_amount;?>">（元）
											</label>
										</td>
									</tr>
									<tr class="wedvideo_gp_target hidden">
										<td>
											<div class="question">希望选择哪种跟拍方案？</div>
											<?php 
											   foreach($wedvideo as $key => $val){
												   if($val['alias']=="hlgp_scheme"){
													   $hlgp_scheme = $val['answer'];
												   }
											    
											   }
											   if(isset($hlgp_scheme)){
													$hlgp_scheme = $hlgp_scheme;
											   }else{
													$hlgp_scheme = "";
											   }
									
											?>
											<label><input type="radio" checked="checked" name="wedvideo_hlgp_scheme" class="radchk" <?php if($hlgp_scheme=="单机位"){?> checked="checked" <?php }?> value="单机位"/>单机位</label>
											<label><input type="radio" name="wedvideo_hlgp_scheme" class="radchk" <?php if($hlgp_scheme=="双机位"){?> checked="checked" <?php }?> value="双机位"/>双机位</label>
											<label><input type="radio" name="wedvideo_hlgp_scheme" class="radchk" <?php if($hlgp_scheme=="三机位以上"){?> checked="checked" <?php }?> value="三机位以上"/>三机位以上</label>
											<label><input type="radio" name="wedvideo_hlgp_scheme" class="radchk" <?php if($hlgp_scheme=="需要和摄像师沟通"){?> checked="checked" <?php }?> value="需要和摄像师沟通"/>需要和摄像师沟通</label>
										</td>
									</tr>
									<?php if($base['mode'] == 1){?>
									<tr class="contrsex">
										<td>
											<div class="question">对于为您服务的婚礼摄影师，您的要求是？</div>
											<?php 
											   foreach($wedvideo as $key => $val){
												   if($val['alias']=="people"){
													   $people = $val['answer'];
												   }
											    
											   }
											   if(isset($people)){
													$people = $people;
											   }else{
													$people = "";
											   }
									
											?>
											性别：<label><input type="radio" checked="checked" name="wedvideo_people" class="radchk" value="男" <?php if($people=="男"){?> checked="checked" <?php }?>/>男</label>
											<label><input type="radio" name="wedvideo_people" class="radchk" value="女" <?php if($people=="女"){?> checked="checked" <?php }?>/>女</label>
											<label><input type="radio" name="wedvideo_people" class="radchk" value="无特殊要求" <?php if($people=="无特殊要求"){?> checked="checked" <?php }?>/>无特殊要求</label>
										</td>
									</tr>
									<?php }?>
									<tr>
										<td>
											<div class="question">对于摄影师及其服务，您是否还有其他的要求或喜好？（选填）</div>
											<?php 
											   foreach($wedvideo as $key => $val){
												   if($val['alias']=="remark"){
													   $remark = $val['answer'];
												   }
											    
											   }
											   if(isset($remark)){
													$remark = $remark;
											   }else{
													$remark = "";
											   }
									
											?>
											<textarea name="wedvideo_remark" class="custextarea com-width300" maxlength="500" placeholder="请输入其他的要求或喜好"><?php echo $remark;?></textarea>
										</td>
									</tr>
										
								</table>
								</div>
							
							<?php endif;?>
							<?php if($sitelayout) : ?>
								<div class="modulars sitelayout" id="sitelayout_f">
								<h3>对场地布置的要求：</h3>
								<table>
										<tr>
										<td>
											<div class="question">对于婚礼现场的场地布置，您的预算是？</div>
											<?php 
											   foreach($sitelayout as $key => $val){
												   if($val['alias']=="amount"){
													   $amount = $val['answer'];
												   }
											    
											   }
											    if(isset($amount)){
													$amount = $amount;
											   }else{
													$amount = "";
											   }
									
											
											?>
											<label><input type="radio" name="sitelayout_amount" class="radchk" value="1.5万以下" <?php if($amount=="1.5万以下"){?> checked="checked" <?php }?>/>1.5万以下</label>
												<label><input type="radio" name="sitelayout_amount" class="radchk" value="1.5-3万"  <?php if($amount=="1.5-3万"){?> checked="checked" <?php }?>/>1.5-3万</label>
												<label><input type="radio" name="sitelayout_amount" class="radchk" value="3-5万"  <?php if($amount=="3-5万"){?> checked="checked" <?php }?>/>3-5万</label>
												<label><input type="radio" name="sitelayout_amount" class="radchk" value="5-10万"  <?php if($amount=="5-10万"){?> checked="checked" <?php }?>/>5-10万</label>
												<label><input type="radio" name="sitelayout_amount" class="radchk" value="10万以上"  <?php if($amount=="10万以上"){?> checked="checked" <?php }?>/>10万以上</label>
												 <label><input type="radio" name="sitelayout_amount" class="radchk" value="" <?php if($amount=="自定义"){?> checked="checked" <?php }?>/>自定义</label>
												<label class="optional">
												<input class="textbox customer" maxlength="30" placeholder="请输入您的预算" value="<?php echo $amount?>">（元） 
												</label>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">您期待自己的婚礼现场是？</div>
											<?php 
											   foreach($sitelayout as $key => $val){
												   if($val['alias']=="style"){
													   $style = $val['answer'];
												   }
											    
											   }
											    if(isset($style)){
													$style = $style;
											   }else{
													$style = "";
											   }
											?>
											<label><input type="radio" name="style" class="radchk" value="传统中式婚礼" <?php if($style=="传统中式婚礼"){?> checked="checked" <?php }?>/>传统中式婚礼</label>
											<label><input type="radio" name="style" class="radchk" value="浪漫西式婚礼" <?php if($style=="浪漫西式婚礼"){?> checked="checked" <?php }?>/>浪漫西式婚礼</label>
											<label><input type="radio" name="style" class="radchk" value="中西结合婚礼" <?php if($style=="中西结合婚礼"){?> checked="checked" <?php }?>/>中西结合婚礼</label>
											<label><input type="radio" name="style" class="radchk" value="无特殊要求" <?php if($style=="无特殊要求"){?> checked="checked" <?php }?>/>无特殊要求</label>

										</td>
									</tr>
									<tr>
										<td>
											<div class="question">您希望自己婚礼现场的主色系是？</div>
                                            <?php 
											   foreach($sitelayout as $key => $val){
												   if($val['alias']=="color"){
													   $color = $val['answer'];
												   }
											    
											   }
											$color = explode("||",$color);
											?>
											
											<label><input type="checkbox" name="color" class="radchk" value="蓝色" <?php if(in_array("蓝色",$color)){?> checked="checked" <?php }?>/>蓝色</label>
											<label><input type="checkbox" name="color" class="radchk" value="紫色" <?php if(in_array("紫色",$color)){?> checked="checked" <?php }?>/>紫色</label>
											<label><input type="checkbox" name="color" class="radchk" value="粉色" <?php if(in_array("粉色",$color)){?> checked="checked" <?php }?>/>粉色</label>
											<label><input type="checkbox" name="color" class="radchk" value="红色" <?php if(in_array("红色",$color)){?> checked="checked" <?php }?>/>红色</label>
											<label><input type="checkbox" name="color" class="radchk" value="香槟色" <?php if(in_array("香槟色",$color)){?> checked="checked" <?php }?>/>香槟色</label>
											<label><input type="checkbox" name="color" class="radchk" value="白绿色" <?php if(in_array("白绿色",$color)){?> checked="checked" <?php }?>/>白绿色</label>
											<label><input type="checkbox" name="color" class="radchk" value="咖啡色" <?php if(in_array("咖啡色",$color)){?> checked="checked" <?php }?>/>咖啡色</label>
											<label><input type="checkbox" name="color" class="radchk" value="五彩缤纷" <?php if(in_array("五彩缤纷",$color)){?> checked="checked" <?php }?>/>五彩缤纷</label>
											<label><input type="checkbox" name="color" class="radchk" value="" <?php if(in_array("自定义",$color)){?> checked="checked" <?php }?>/>自定义</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您的婚礼颜色" value="<?php echo end($color);?>">
											</label>
											
										</td>
									</tr>
									<tr>
										<td>
										
											<div class="question">请选择2-5个词描述您理想的婚礼？</div>
											<?php foreach($sitelayout as $key => $val){
												   if($val['alias']=="ideal"){
													   $color = $val['answer'];
												   }
											    
											   }
											$color = explode("||",$color);?>
											<label><input type="checkbox" name="ideal" class="radchk" value="喜庆" <?php if(in_array("喜庆",$color)){?> checked="checked" <?php }?>/>喜庆</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="有趣" <?php if(in_array("有趣",$color)){?> checked="checked" <?php }?>/>有趣</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="浪漫" <?php if(in_array("浪漫",$color)){?> checked="checked" <?php }?>/>浪漫</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="动情" <?php if(in_array("动情",$color)){?> checked="checked" <?php }?>/>动情</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="经典" <?php if(in_array("经典",$color)){?> checked="checked" <?php }?>/>经典</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="特别" <?php if(in_array("特别",$color)){?> checked="checked" <?php }?>/>特别</label>
											<label><input type="checkbox" name="ideal"  class="radchk" value="传统" <?php if(in_array("传统",$color)){?> checked="checked" <?php }?>/>传统</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="热闹" <?php if(in_array("热闹",$color)){?> checked="checked" <?php }?>/>热闹</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="简约" <?php if(in_array("简约",$color)){?> checked="checked" <?php }?>/>简约</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="温馨" <?php if(in_array("温馨",$color)){?> checked="checked" <?php }?>/>温馨</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="雅致" <?php if(in_array("雅致",$color)){?> checked="checked" <?php }?>/>雅致</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="贵气" <?php if(in_array("贵气",$color)){?> checked="checked" <?php }?>/>贵气</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="时髦" <?php if(in_array("时髦",$color)){?> checked="checked" <?php }?>/>时髦</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="另类" <?php if(in_array("另类",$color)){?> checked="checked" <?php }?>/>另类</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="轻松" <?php if(in_array("轻松",$color)){?> checked="checked" <?php }?>/>轻松</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="正式" <?php if(in_array("正式",$color)){?> checked="checked" <?php }?>/>正式</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="简约" <?php if(in_array("简约",$color)){?> checked="checked" <?php }?>/>简约</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="休闲" <?php if(in_array("休闲",$color)){?> checked="checked" <?php }?>/>休闲</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="可爱" <?php if(in_array("可爱",$color)){?> checked="checked" <?php }?>/>可爱</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="复古" <?php if(in_array("复古",$color)){?> checked="checked" <?php }?>/>复古</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="有品位" <?php if(in_array("有品位",$color)){?> checked="checked" <?php }?>/>有品位</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="超现实" <?php if(in_array("超现实",$color)){?> checked="checked" <?php }?>/>超现实</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="充满爱" <?php if(in_array("充满爱",$color)){?> checked="checked" <?php }?>/>充满爱</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="主题突出" <?php if(in_array("主题突出",$color)){?> checked="checked" <?php }?>/>主题突出</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="庄严神圣" <?php if(in_array("庄严神圣",$color)){?> checked="checked" <?php }?>/>庄严神圣</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="当地风俗" <?php if(in_array("当地风俗",$color)){?> checked="checked" <?php }?>/>当地风俗</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="组织有序" <?php if(in_array("组织有序",$color)){?> checked="checked" <?php }?>/>组织有序</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="设施齐全" <?php if(in_array("设施齐全",$color)){?> checked="checked" <?php }?>/>设施齐全</label>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">对于场地布置及其服务，您是否还有其他的要求或喜好？（选填）</div>
											<?php foreach($sitelayout as $key => $val){
												   if($val['alias']=="remark"){
													   $remark = $val['answer'];
												   }
											    
											   }
											   if(empty($remark)){
											      $remark = "";
											   }else{
											       $remark =  $remark;
											   }
											   ?>
											<textarea name="sitelayout_remark" class="custextarea com-width300" maxlength="500" placeholder="请输入其他的要求或喜好"><?php echo $remark?></textarea>
										</td>
									</tr>

								</table>
								</div>
							
							<?php endif;?>
							</form>
						






						</div>
					</div>
					<div title="商家信息" class="lftab">
						<div class="tabcont">
							<a href="javascript:void(0)" class="easyui-linkbutton addbsnsinfo" data-options="iconCls:'icon-add'">添加</a>
							<a href="javascript:void(0)" class="easyui-linkbutton del" data-options="iconCls:'icon-no'">删除</a><br /><br />
							<table id="bsnsinfo" class="datagrid"></table>
							<input type="hidden" class="dmid" value="<?php echo isset($base["id"]) ? $base["id"] : 0?>"/>
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
		</form>
	<input type="hidden" name="serves" id="serves" value="<?php echo $serves?>"/>
    <div class="easyuitabs" id="tbs" style="width:100%;height:284px;">
		<?php if(stristr($serves, "1424")) : ?>
		<div title="主持人" style="padding:10px">
			<table id="wedmaster" data="1424" class="datagrid bsnstb" style="height:240px;"></table>
		</div>
		<?php endif;?>
		<?php if(stristr($serves, "1425")) : ?>
		<div title="化妆师" style="padding:10px">
			<table id="makeup" data="1425" class="datagrid bsnstb" style="height:240px;"></table>
		</div>
		<?php endif;?>
		<?php if(stristr($serves, "1423")) : ?>
		<div title="摄影师" style="padding:10px">
			<table id="wedphotoer" data="1423" class="datagrid bsnstb" style="height:240px;"></table>
		</div>
		<?php endif;?>
		<?php if(stristr($serves, "1426")) : ?>	
		<div title="摄像师" style="padding:10px">
			<table id="wedvideo" data="1426" class="datagrid bsnstb" style="height:240px;"></table>
		</div>
		<?php endif;?>
		<?php if(stristr($serves, "1427")) : ?>
		<div title="场地布置" style="padding:10px">
			<table id="sitelayout" data="1427" class="datagrid bsnstb" style="height:240px;"></table>
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
	seajs.use("<?php echo $config['srcPath'];?>/js/trademanage/examiemultdm");
</script>
</body>
</html>