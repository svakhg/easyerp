<div class="center-div">
	<div class="center-div-header">当前位置：【 婚礼需求管理  >  客户需求详情 】</div>
	<div class="comct">
		<div id="maincontent">
				<div id="systemlogtab" class="easyui-tabs" style="overflow:visible; width: 99%;">
						<a href="<?php echo $backurl?>" class="easyui-linkbutton mb10 mr10 mt10" data-options="iconCls:'icon-back'">返 回</a>&nbsp;&nbsp;&nbsp;&nbsp;
						<?php if($pageflag=="search"){?>

						<?php }else{?>
						<a href="javascript:" class="easyui-linkbutton copysend c1 mb10 mr10 mt10">复制</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<?php }?>

                    <?php if($send_button != '' && ($send_button == 1 || $send_button == 2)) : ?>
                        <a href="javascript:" class="easyui-linkbutton addsend c1 mb10 mr10 mt10" data-options="iconCls:'icon-add'">分配商家</a>
                    <?php endif;?>

                    <?php if($confirm_button != ''):?>

                            <a href="javascript:" class="easyui-linkbutton addbsns c1 mb10 mr10 mt10" >
                            <?php
                                if($confirm_button == 1){

                                    echo '初选商家';
                                }elseif($confirm_button == 2){

                                    echo '确认商家';
                                }
                            ?>
                            </a>
                        <input type="hidden" id="confirm" value="<?php echo $confirm_button;?>">
                    <?php endif;?>

                    <?php if($base["status"] == 80) : ?>
                            <!-- <a href="javascript:" class="easyui-linkbutton addsign c1 mb10 mr10 mt10">签约</a> -->
                    <?php endif;?>

						找商家方式：
							<?php if(isset($base["mode"]) && $base["mode"] == 1) : ?>
							<label>招投标</label>
							<?php elseif(isset($base["mode"]) && $base["mode"] == 2) : ?>
							<label>指定商家</label>
							<?php else :?>
							<label></label>
							 
							<?php endif;?>				
							<input type="hidden" name="mode" value="<?php echo isset($base["mode"]) ? $base["mode"] : ""?>">
&nbsp;&nbsp;&nbsp;&nbsp;
						新人顾问：
							<label><?php echo $base["consultant_name"]?></label>
							<input type="hidden" name="counselor_uid" value="<?php echo isset($base["consultant_id"]) ? $base["consultant_id"] : ""?>">

						
					<div title="基本信息" class="lftab">
						<form class="easyui-form" method="post" id="baseinfo" data-options="novalidate:true">
						<table class="bigtb">
							<tr>
								<td class="tbfield" style="width:100px;"><span class="flag">*</span> 客户姓名：</td>
								<td style="width:160px;"><label><?php echo isset($base["cli_name"]) ? $base["cli_name"] : ""?></label>
								<input type="hidden" name="cli_name" value="<?php echo isset($base["cli_name"]) ? $base["cli_name"] : ""?>">
								</td>
								<td class="tbfield" style="width:100px;"><span class="flag">*</span> 客户来源：</td>
								<td style="width:160px;">
								<label><!-- <?php echo isset($cli_source["name"]) ? $base["name"] : ""?></label> -->
								<?php if(isset($base["cli_source"])):?>
										<?php foreach ($cli_source as $sou) :?>									
			                         	<?php if($sou["id"] == $base["cli_source"]) : ?><label><?php echo $sou["name"]?></label>
										<input type="hidden" name="cli_source" value="<?php echo isset($sou["id"]) ? $sou["id"] : ""?>">
										<?php endif;?>
										<?php endforeach;?>
										<?php endif;?>
								</td>
								<td class="tbfield" style="width:100px;"><span class="flag">*</span> 获知渠道：</td>
								<td>
									<?php if(isset($base["channel"])):?>
										<?php foreach ($channel as $chan) : ?>										
										<?php if($chan["id"] == $base["channel"]) : ?>
										<label>
										  <?php echo $chan["name"]?>
										</label>
										<input type="hidden" name="channel" value="<?php echo isset($chan["id"]) ? $chan["id"] : ""?>">
										<?php endif;?>
										<?php endforeach;?>
										<?php endif;?>
									<label><?php echo '（'.$base['channel_phone'].'）';?></label>
								</td>
							</tr>
							<!-- <tr>
								<td class="tbfield">性别：</td>
								<td class="radiogroup">
									<?php //if(isset($base["cli_gender"])) : ?>
									<label><?php //echo $base["cli_gender"]?></label>
									<input type="hidden" name="cli_gender" value="<?php //echo isset($base["cli_gender"]) ? $base["cli_gender"] : ""?>">
									<?php //endif;?>
								</td>
								<td class="tbfield">出生日期：</td>
								<td><label><?php //echo isset($base["cli_birth"]) ? $base["cli_birth"] : ""?></label>
								<input type="hidden" name="cli_birth" value="<?php //echo isset($base["cli_birth"]) ? $base["cli_birth"] : ""?>">
								</td>
								<td class="tbfield">学历：</td>
								<td>
									<?php //if(isset($base["cli_edu"])) : ?>
										<label><?php //echo $base["cli_edu"]?></label>
										<input type="hidden" name="cli_edu" value="<?php //echo isset($base["cli_edu"]) ? $base["cli_edu"] : ""?>">
									<?php //endif;?>
								</td>
							</tr> -->
							<tr>
								<td class="tbfield">手机号码：</td>
								<td>
									<label><?php echo isset($base["cli_mobile"]) ? $base["cli_mobile"] : ""?>
									<input type="hidden" name="cli_mobile" value="<?php echo isset($base["cli_mobile"]) ? $base["cli_mobile"] : ""?>">
								</td>
								<td class="tbfield">固定电话：</td>
								<td>
									<label><?php echo isset($base["cli_tel"]) ? $base["cli_tel"] : ""?>
									<input type="hidden" name="cli_tel" value="<?php echo isset($base["cli_tel"]) ? $base["cli_tel"] : ""?>">
								</td>
								<td class="tbfield">民族：</td>
								<td>
									
										<?php if(isset($base["cli_nation"])) : ?>
										<label><?php echo $base["cli_nation"]?></label>
										<input type="hidden" name="cli_nation" value="<?php echo isset($base["cli_nation"]) ? $base["cli_nation"] : ""?>">
										<?php endif;?>
									
								</td>
							</tr>
							<tr>
								<td class="tbfield">微信：</td>
								<td>
									<label><?php echo isset($base["cli_weixin"]) ? $base["cli_weixin"] : ""?>
									<input type="hidden" name="cli_weixin" value="<?php echo isset($base["cli_weixin"]) ? $base["cli_weixin"] : ""?>">
								</td>
								<td class="tbfield">QQ：</td>
								<td>
									<label><?php echo isset($base["cli_qq"]) ? $base["cli_qq"] : ""?>
									<input type="hidden" name="cli_qq" value="<?php echo isset($base["cli_qq"]) ? $base["cli_qq"] : ""?>">
								</td>
								<td class="tbfield">微博：</td>
								<td>
									<label><?php echo isset($base["cli_weibo"]) ? $base["cli_weibo"] : ""?>
									<input type="hidden" name="cli_weibo" value="<?php echo isset($base["cli_weibo"]) ? $base["cli_weibo"] : ""?>">
								</td>
							</tr>
							<tr>
								<td class="tbfield">邮编：</td>
								<td>
									<label><?php echo isset($base["cli_postcode"]) ? $base["cli_postcode"] : ""?>
									<input type="hidden" name="cli_postcode" value="<?php echo isset($base["cli_postcode"]) ? $base["cli_postcode"] : ""?>">
								</td>
								<td class="tbfield">电子邮箱：</td>
								<td>
									<label><?php echo isset($base["cli_email"]) ? $base["cli_email"] : ""?>
									<input type="hidden" name="cli_email" value="<?php echo isset($base["cli_email"]) ? $base["cli_email"] : ""?>">
								</td>
								<td class="tbfield">其他联系方式：</td>
								<td>
								  <label><?php echo isset($base["cli_othercontect"]) ? $base["cli_othercontect"] : ""?>
								  <input type="hidden" name="cli_othercontect" value="<?php echo isset($base["cli_othercontect"]) ? $base["cli_othercontect"] : ""?>">
								</td>
							</tr>
							<tr>
								<td class="tbfield">通讯地址：</td>
								<td class="linkage">
									 <label><input type="hidden" name="cli_location" value="<?php echo isset($base["cli_location"]) ? $base["cli_location"] : ""?>"></label>
								</td>
								<td class="tbfield">详细地址：</td>
								<td colspan="3">
									 <label><?php echo isset($base["cli_address"]) ? $base["cli_address"] : ""?></label>
									 <input type="hidden" name="cli_address" value="<?php echo isset($base["cli_address"]) ? $base["cli_address"] : ""?>">
								</td>
								
							</tr>
							<tr>
								<td class="tbfield">希望联系时间：</td>
								<td>
									<label><?php echo isset($base["cli_hope_contect_time"]) ? $base["cli_hope_contect_time"] : ""?></label>
									<input type="hidden" name="cli_hope_contect_time" value="<?php echo isset($base["cli_hope_contect_time"]) ? $base["cli_hope_contect_time"] : ""?>">
								</td>
								<td class="tbfield" style="vertical-align:top;">客户备注：</td>
								<td colspan="3">
									<label><?php echo isset($base["comment"]) ? $base["comment"] : ""?></label>
									<input type="hidden" name="comment" value="<?php echo isset($base["comment"]) ? $base["comment"] : ""?>">

								</td>
							</tr>
                            <tr>
								<td class="tbfield">期望联系方式：</td>
								<td colspan="5">
									<?php 
                                      $way = explode(",",$base['cli_hope_contect_way']); 
                                      if(in_array(1,$way)){
                                      	echo "手机&nbsp;&nbsp;&nbsp;";
                                     ?>
                                      	<input type="hidden" name="cli_hope_contect_way" value="1">
                                    <?php  }
                                      if(in_array(2,$way)){
                                      	echo " QQ&nbsp;&nbsp;&nbsp;&nbsp;";
                                     ?>
                                      	<input type="hidden" name="cli_hope_contect_way" value="2">
                                     <?php }
                                      if(in_array(3,$way)){
                                      	echo " 微信&nbsp;&nbsp;&nbsp;";
                                     ?>
                                      	<input type="hidden" name="cli_hope_contect_way" value="3">
                                     <?php }
                                      if(in_array(4,$way)){
                                      	echo " 其他联系方式";
                                      ?>
                                      	<input type="hidden" name="cli_hope_contect_way" value="4">
                                     <?php }

                                     ?>
									
								</td>
							</tr>


							<tr>
								<td class="tbfield">客户标签：</td>
								<td class="text-left"><input type="hidden" name="tag" id="cli_tag" value="<?php echo isset($base["cli_tag"]) ? $base["cli_tag"] : ""?>"/></td>
								<td colspan="4" class="tag-container" width="519"></td>
			
							</tr>
						</table>
						</form>
					</div>
					<?php if($mode == 1) : ?>
					<div title="一站式婚礼需求" class="lftab wedneed" id="onetstation">
						<div class="tabcont">
							<form class="easyui-form" method="post" id="wedbaseinfo" data-options="novalidate:true">
							<div class="modulars">
								<h3>婚礼基础信息：</h3>
								<table>
									<tr>
										<td class="vert-top">婚礼日期：</td>
										<td>
											
											<?php if(isset($base["wed_date_sure"])) : ?>
												<?php if($base["wed_date_sure"] == 1) : ?>
												<label>已确定</label>
												<?php else:?>
												<label>未确定</label>
												<?php endif;?>
												<label><?php echo $base["wed_date"]?></label>
												<input type="hidden" name="wed_date_sure" value="<?php echo isset($base["wed_date_sure"]) ? $base["wed_date_sure"] : ""?>">

												<input type="hidden" name="wed_date" value="<?php echo isset($base["wed_date"]) ? $base["wed_date"] : ""?>">
											<?php endif;?>
										</td>
									</tr>
									<tr>
										<td class="vert-top">婚礼地点：</td>
										<td>
											<input type="hidden" name="wed_location" value="<?php echo isset($base["wed_location"]) ? $base["wed_location"] : ""?>">
										</td>
									</tr>
									<tr>
										<td class="vert-top">婚礼场地：</td>
										<td>
											<label><?php echo isset($base["wed_place"]) ? $base["wed_place"] : ""?></label>
											<input type="hidden" name="wed_place" value="<?php echo isset($base["wed_place"]) ? $base["wed_place"] : ""?>">
										</td>
									</tr>
									<tr>
										<td class="vert-top">婚宴类型：</td>
										<td>
											<label><?php echo isset($base["wed_party_type"]) ? $base["wed_party_type"] : ""?></label>
											<input type="hidden" name="wed_party_type" value="<?php echo isset($base["wed_party_type"]) ? $base["wed_party_type"] : ""?>">
										</td>
									</tr>
									<tr>
										<td class="vert-top">来宾人数：</td>
										<td>
											<label><?php echo isset($base["people_num"]) ? $base["people_num"] : ""?></label>
											<input type="hidden" name="people_num" value="<?php echo isset($base["people_num"]) ? $base["people_num"] : ""?>">
										</td>
									</tr>
									<tr>
										<td class="vert-top">婚礼预算：</td>
										<td>
											<div class="question">对于婚礼现场的场地布置，您的预算是？</div>
											<label><?php echo isset($base["budget"]) ? $base["budget"] : ""?></label>
											<input type="hidden" name="budget" value="<?php echo isset($base["budget"]) ? $base["budget"] : ""?>">
										</td>
									</tr>
								</table>
							</div>
							</form>
							<form class="easyui-form" method="post" id="onestation" data-options="novalidate:true">
								<div class="modulars">
								<h3>对理想婚礼的要求：</h3>
								<table>
								<?php foreach($wedplanners as $key => $planners){
									echo "<tr>";
									if(strpos($key, '_diy') === false){
										$answer = $planners['answer'];
									$keyDiy = $key.'_diy';
									if(isset($wedplanners[$keyDiy])){
										$answer = $wedplanners[$keyDiy]['answer'];
									}
									echo "<td>".$planners["word"]."</td>"."<td>".$answer."</td>";
									}
									echo "</tr>";
								}
								?>
								</table>

								</div>
								<div class="modulars" style="display: none">
								<h3>对理想婚礼的要求：</h3>
								 <table>
									<tr>
										<td>
											<div class="question">关于婚礼，一下哪种描述更符合您的要求？</div>
											
											<label><input type="radio" name="description" class="radchk" value="婚礼只是一个形式，我希望简单方便就好" <?php if($wedplanners[0]["answer"] == "婚礼只是一个形式，我希望简单方便就好"):?>checked="checked"<?php endif;?>/>婚礼只是一个形式，我希望简单方便就好</label><br />

											<label><input type="radio" name="description" class="radchk" value="我对婚礼有梦想，但梦想与预算矛盾时，预算更重要" <?php if($wedplanners[0]["answer"] == "我对婚礼有梦想，但梦想与预算矛盾时，预算更重要"):?>checked="checked"<?php endif;?>/>我对婚礼有梦想，但梦想与预算矛盾时，预算更重要</label><br />	
											
											<label><input type="radio" name="description" class="radchk" value="我对婚礼有梦想，但梦想与预算矛盾时，梦想更重要" <?php if($wedplanners[0]["answer"] == "我对婚礼有梦想，但梦想与预算矛盾时，梦想更重要"):?>checked="checked"<?php endif;?>/>我对婚礼有梦想，但梦想与预算矛盾时，梦想更重要</label>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">您期待自己的婚礼现场是？</div>
											<label><input type="radio" name="style" class="radchk" value="传统中式婚礼" <?php if($wedplanners[1]["answer"] == "传统中式婚礼"):?>checked="checked"<?php endif;?>/>传统中式婚礼</label>
											<label><input type="radio" name="style" class="radchk" value="浪漫西式婚礼" <?php if($wedplanners[1]["answer"] == "浪漫西式婚礼"):?>checked="checked"<?php endif;?>/>浪漫西式婚礼</label>
											<label><input type="radio" name="style" class="radchk" value="中西结合婚礼" <?php if($wedplanners[1]["answer"] == "中西结合婚礼"):?>checked="checked"<?php endif;?>/>中西结合婚礼</label>
											<label><input type="radio" name="style" class="radchk" value="无特殊要求" <?php if($wedplanners[1]["answer"] == "无特殊要求"):?>checked="checked"<?php endif;?>/>无特殊要求</label>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">您希望自己婚礼现场的主色系是？</div>
											<?php if($wedplanners[2]["answer"] == "红色"):?>
											<label><input type="radio" name="color" class="radchk" value="红色" checked="checked"/>红色</label>
											<label><input type="radio" name="color" class="radchk" value="粉色"/>粉色</label>
											<label><input type="radio" name="color" class="radchk" value="紫色"/>紫色</label>
											<label><input type="radio" name="color" class="radchk" value="蓝色"/>蓝色</label>
											<label><input type="radio" name="color" class="radchk" value="香槟色"/>香槟色</label>
											<label><input type="radio" name="color" class="radchk" value="白绿色"/>白绿色</label>
											<label><input type="radio" name="color" class="radchk" value="咖啡色"/>咖啡色</label>
											<label><input type="radio" name="color" class="radchk" value="五彩缤纷"/>五彩缤纷</label>
											<label><input type="radio" name="color" class="radchk" value=""/>自定义</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您的婚礼颜色">
											</label>
											<?php elseif($wedplanners[2]["answer"] == "粉色"):?>
											<label><input type="radio" name="color" class="radchk" value="红色"/>红色</label>
											<label><input type="radio" name="color" class="radchk" value="粉色" checked="checked"/>粉色</label>
											<label><input type="radio" name="color" class="radchk" value="紫色"/>紫色</label>
											<label><input type="radio" name="color" class="radchk" value="蓝色"/>蓝色</label>
											<label><input type="radio" name="color" class="radchk" value="香槟色"/>香槟色</label>
											<label><input type="radio" name="color" class="radchk" value="白绿色"/>白绿色</label>
											<label><input type="radio" name="color" class="radchk" value="咖啡色"/>咖啡色</label>
											<label><input type="radio" name="color" class="radchk" value="五彩缤纷"/>五彩缤纷</label>
											<label><input type="radio" name="color" class="radchk" value=""/>自定义</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您的婚礼颜色">
											</label>
											<?php elseif($wedplanners[2]["answer"] == "紫色"):?>
											<label><input type="radio" name="color" class="radchk" value="红色"/>红色</label>
											<label><input type="radio" name="color" class="radchk" value="粉色"/>粉色</label>
											<label><input type="radio" name="color" class="radchk" value="紫色" checked="checked"/>紫色</label>
											<label><input type="radio" name="color" class="radchk" value="蓝色"/>蓝色</label>
											<label><input type="radio" name="color" class="radchk" value="香槟色"/>香槟色</label>
											<label><input type="radio" name="color" class="radchk" value="白绿色"/>白绿色</label>
											<label><input type="radio" name="color" class="radchk" value="咖啡色"/>咖啡色</label>
											<label><input type="radio" name="color" class="radchk" value="五彩缤纷"/>五彩缤纷</label>
											<label><input type="radio" name="color" class="radchk" value=""/>自定义</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您的婚礼颜色">
											</label>
											<?php elseif($wedplanners[2]["answer"] == "蓝色"):?>
											<label><input type="radio" name="color" class="radchk" value="红色"/>红色</label>
											<label><input type="radio" name="color" class="radchk" value="粉色"/>粉色</label>
											<label><input type="radio" name="color" class="radchk" value="紫色"/>紫色</label>
											<label><input type="radio" name="color" class="radchk" value="蓝色" checked="checked"/>蓝色</label>
											<label><input type="radio" name="color" class="radchk" value="香槟色"/>香槟色</label>
											<label><input type="radio" name="color" class="radchk" value="白绿色"/>白绿色</label>
											<label><input type="radio" name="color" class="radchk" value="咖啡色"/>咖啡色</label>
											<label><input type="radio" name="color" class="radchk" value="五彩缤纷"/>五彩缤纷</label>
											<label><input type="radio" name="color" class="radchk" value=""/>自定义</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您的婚礼颜色">
											</label>
											<?php elseif($wedplanners[2]["answer"] == "香槟色"):?>
											<label><input type="radio" name="color" class="radchk" value="红色"/>红色</label>
											<label><input type="radio" name="color" class="radchk" value="粉色"/>粉色</label>
											<label><input type="radio" name="color" class="radchk" value="紫色"/>紫色</label>
											<label><input type="radio" name="color" class="radchk" value="蓝色"/>蓝色</label>
											<label><input type="radio" name="color" class="radchk" value="香槟色" checked="checked"/>香槟色</label>
											<label><input type="radio" name="color" class="radchk" value="白绿色"/>白绿色</label>
											<label><input type="radio" name="color" class="radchk" value="咖啡色"/>咖啡色</label>
											<label><input type="radio" name="color" class="radchk" value="五彩缤纷"/>五彩缤纷</label>
											<label><input type="radio" name="color" class="radchk" value=""/>自定义</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您的婚礼颜色">
											</label>
											<?php elseif($wedplanners[2]["answer"] == "白绿色"):?>
											<label><input type="radio" name="color" class="radchk" value="红色"/>红色</label>
											<label><input type="radio" name="color" class="radchk" value="粉色"/>粉色</label>
											<label><input type="radio" name="color" class="radchk" value="紫色"/>紫色</label>
											<label><input type="radio" name="color" class="radchk" value="蓝色"/>蓝色</label>
											<label><input type="radio" name="color" class="radchk" value="香槟色"/>香槟色</label>
											<label><input type="radio" name="color" class="radchk" value="白绿色" checked="checked"/>白绿色</label>
											<label><input type="radio" name="color" class="radchk" value="咖啡色"/>咖啡色</label>
											<label><input type="radio" name="color" class="radchk" value="五彩缤纷"/>五彩缤纷</label>
											<label><input type="radio" name="color" class="radchk" value=""/>自定义</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您的婚礼颜色">
											</label>
											<?php elseif($wedplanners[2]["answer"] == "咖啡色"):?>
											<label><input type="radio" name="color" class="radchk" value="红色"/>红色</label>
											<label><input type="radio" name="color" class="radchk" value="粉色"/>粉色</label>
											<label><input type="radio" name="color" class="radchk" value="紫色"/>紫色</label>
											<label><input type="radio" name="color" class="radchk" value="蓝色"/>蓝色</label>
											<label><input type="radio" name="color" class="radchk" value="香槟色"/>香槟色</label>
											<label><input type="radio" name="color" class="radchk" value="白绿色"/>白绿色</label>
											<label><input type="radio" name="color" class="radchk" value="咖啡色" checked="checked"/>咖啡色</label>
											<label><input type="radio" name="color" class="radchk" value="五彩缤纷"/>五彩缤纷</label>
											<label><input type="radio" name="color" class="radchk" value=""/>自定义</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您的婚礼颜色">
											</label>
											<?php elseif($wedplanners[2]["answer"] == "五彩缤纷"):?>
											<label><input type="radio" name="color" class="radchk" value="红色"/>红色</label>
											<label><input type="radio" name="color" class="radchk" value="粉色"/>粉色</label>
											<label><input type="radio" name="color" class="radchk" value="紫色"/>紫色</label>
											<label><input type="radio" name="color" class="radchk" value="蓝色"/>蓝色</label>
											<label><input type="radio" name="color" class="radchk" value="香槟色"/>香槟色</label>
											<label><input type="radio" name="color" class="radchk" value="白绿色"/>白绿色</label>
											<label><input type="radio" name="color" class="radchk" value="咖啡色"/>咖啡色</label>
											<label><input type="radio" name="color" class="radchk" value="五彩缤纷" checked="checked"/>五彩缤纷</label>
											<label><input type="radio" name="color" class="radchk" value=""/>自定义</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您的婚礼颜色">
											</label>
											<?php else:?>
											<label><input type="radio" name="color" class="radchk" value="红色"/>红色</label>
											<label><input type="radio" name="color" class="radchk" value="粉色"/>粉色</label>
											<label><input type="radio" name="color" class="radchk" value="紫色"/>紫色</label>
											<label><input type="radio" name="color" class="radchk" value="蓝色"/>蓝色</label>
											<label><input type="radio" name="color" class="radchk" value="香槟色"/>香槟色</label>
											<label><input type="radio" name="color" class="radchk" value="白绿色"/>白绿色</label>
											<label><input type="radio" name="color" class="radchk" value="咖啡色"/>咖啡色</label>
											<label><input type="radio" name="color" class="radchk" value="五彩缤纷"/>五彩缤纷</label>
											<label><input type="radio" name="color" class="radchk" value="" checked="checked"/>自定义</label>
											<label class="optional">
												<input <?php echo isset($wedplanners[2]["answer"]) ? $wedplanners[2]["answer"] : ""?> class="textbox customer"  maxlength="30" placeholder="请输入您的婚礼颜色">
											</label>
											<?php endif;?>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">请选择2-5个词描述您理想的婚礼？</div>
											<label><input type="checkbox" name="ideal" class="radchk" value="喜庆" <?php if(stristr($wedplanners[3]["answer"], "喜庆") ):?>checked="checked"<?php endif;?>/>喜庆</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="有趣" <?php if(stristr($wedplanners[3]["answer"], "有趣") ):?>checked="checked"<?php endif;?>/>有趣</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="浪漫" <?php if(stristr($wedplanners[3]["answer"], "浪漫") ):?>checked="checked"<?php endif;?>/>浪漫</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="动情" <?php if(stristr($wedplanners[3]["answer"], "动情") ):?>checked="checked"<?php endif;?>/>动情</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="经典" <?php if(stristr($wedplanners[3]["answer"], "经典") ):?>checked="checked"<?php endif;?>/>经典</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="特别" <?php if(stristr($wedplanners[3]["answer"], "特别") ):?>checked="checked"<?php endif;?>/>特别</label>
											<label><input type="checkbox" name="ideal"  class="radchk" value="传统" <?php if(stristr($wedplanners[3]["answer"], "传统") ):?>checked="checked"<?php endif;?>/>传统</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="热闹" <?php if(stristr($wedplanners[3]["answer"], "热闹") ):?>checked="checked"<?php endif;?>/>热闹</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="简约" <?php if(stristr($wedplanners[3]["answer"], "简约") ):?>checked="checked"<?php endif;?>/>简约</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="温馨" <?php if(stristr($wedplanners[3]["answer"], "温馨") ):?>checked="checked"<?php endif;?>/>温馨</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="雅致" <?php if(stristr($wedplanners[3]["answer"], "雅致") ):?>checked="checked"<?php endif;?>/>雅致</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="贵气" <?php if(stristr($wedplanners[3]["answer"], "贵气") ):?>checked="checked"<?php endif;?>/>贵气</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="时髦" <?php if(stristr($wedplanners[3]["answer"], "时髦") ):?>checked="checked"<?php endif;?>/>时髦</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="另类" <?php if(stristr($wedplanners[3]["answer"], "另类") ):?>checked="checked"<?php endif;?>/>另类</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="轻松" <?php if(stristr($wedplanners[3]["answer"], "轻松") ):?>checked="checked"<?php endif;?>/>轻松</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="正式" <?php if(stristr($wedplanners[3]["answer"], "正式") ):?>checked="checked"<?php endif;?>/>正式</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="简约" <?php if(stristr($wedplanners[3]["answer"], "简约") ):?>checked="checked"<?php endif;?>/>简约</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="休闲" <?php if(stristr($wedplanners[3]["answer"], "休闲") ):?>checked="checked"<?php endif;?>/>休闲</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="可爱" <?php if(stristr($wedplanners[3]["answer"], "可爱") ):?>checked="checked"<?php endif;?>/>可爱</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="复古" <?php if(stristr($wedplanners[3]["answer"], "复古") ):?>checked="checked"<?php endif;?>/>复古</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="有品位" <?php if(stristr($wedplanners[3]["answer"], "有品位") ):?>checked="checked"<?php endif;?>/>有品位</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="超现实" <?php if(stristr($wedplanners[3]["answer"], "超现实") ):?>checked="checked"<?php endif;?>/>超现实</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="充满爱" <?php if(stristr($wedplanners[3]["answer"], "充满爱") ):?>checked="checked"<?php endif;?>/>充满爱</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="主题突出" <?php if(stristr($wedplanners[3]["answer"], "主题突出") ):?>checked="checked"<?php endif;?>/>主题突出</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="庄严神圣" <?php if(stristr($wedplanners[3]["answer"], "庄严神圣") ):?>checked="checked"<?php endif;?>/>庄严神圣</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="当地风俗" <?php if(stristr($wedplanners[3]["answer"], "当地风俗") ):?>checked="checked"<?php endif;?>/>当地风俗</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="组织有序" <?php if(stristr($wedplanners[3]["answer"], "组织有序") ):?>checked="checked"<?php endif;?>/>组织有序</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="设施齐全" <?php if(stristr($wedplanners[3]["answer"], "设施齐全") ):?>checked="checked"<?php endif;?>/>设施齐全</label>
											<label><input type="checkbox" name="ideal" class="radchk" value="" />自定义</label>
											<label class="optional">
												<input value="" class="textbox customer"  maxlength="30" placeholder="请输入您理想的婚礼形容词">
											</label>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">在婚礼筹备时，您希望重点筹备的是？</div>
											<label><input type="checkbox" name="emphasis" class="radchk" value="场地布置" <?php if(stristr($wedplanners[4]["answer"], "场地布置") ):?>checked="checked"<?php endif;?>/>场地布置</label>
											<label><input type="checkbox" name="emphasis" class="radchk" value="主持人" <?php if(stristr($wedplanners[4]["answer"], "主持人") ):?>checked="checked"<?php endif;?>/>主持人</label>
											<label><input type="checkbox" name="emphasis" class="radchk" value="化妆师" <?php if(stristr($wedplanners[4]["answer"], "化妆师") ):?>checked="checked"<?php endif;?>/>化妆师</label>
											<label><input type="checkbox" name="emphasis" class="radchk" value="摄影师" <?php if(stristr($wedplanners[4]["answer"], "摄影师") ):?>checked="checked"<?php endif;?>/>摄影师</label>
											<label><input type="checkbox" name="emphasis" class="radchk" value="摄像师" <?php if(stristr($wedplanners[4]["answer"], "摄像师") ):?>checked="checked"<?php endif;?>/>摄像师</label>
											<label><input type="checkbox" name="emphasis" class="radchk" value="无特殊要求" <?php if(stristr($wedplanners[4]["answer"], "无特殊要求") ):?>checked="checked"<?php endif;?>/>无特殊要求</label>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">在婚礼过程中，您最看重的是？</div>
											<?php if($wedplanners[5]["answer"] == "婚礼场面"):?>
											<label><input type="radio" name="importance" class="radchk" value="婚礼场面" checked="checked"/>婚礼场面</label>
											<label><input type="radio" name="importance" class="radchk" value="花费合理"/>花费合理</label>
											<label><input type="radio" name="importance" class="radchk" value="两人的感受"/>两人的感受</label>
											<label><input type="radio" name="importance" class="radchk" value="父母和亲朋的感受"/>父母和亲朋的感受</label><br />
											<label><input type="radio" name="importance" class="radchk" value=""/>其它</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您的想法">
											</label>
											<?php elseif($wedplanners[5]["answer"] == "花费合理"):?>
											<label><input type="radio" name="importance" class="radchk" value="婚礼场面"/>婚礼场面</label>
											<label><input type="radio" name="importance" class="radchk" value="花费合理" checked="checked"/>花费合理</label>
											<label><input type="radio" name="importance" class="radchk" value="两人的感受"/>两人的感受</label>
											<label><input type="radio" name="importance" class="radchk" value="父母和亲朋的感受"/>父母和亲朋的感受</label><br />
											<label><input type="radio" name="importance" class="radchk" value=""/>其它</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您的想法">
											</label>
											<?php elseif($wedplanners[5]["answer"] == "两人的感受"):?>
											<label><input type="radio" name="importance" class="radchk" value="婚礼场面"/>婚礼场面</label>
											<label><input type="radio" name="importance" class="radchk" value="花费合理"/>花费合理</label>
											<label><input type="radio" name="importance" class="radchk" value="两人的感受" checked="checked"/>两人的感受</label>
											<label><input type="radio" name="importance" class="radchk" value="父母和亲朋的感受"/>父母和亲朋的感受</label><br />
											<label><input type="radio" name="importance" class="radchk" value=""/>其它</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您的想法">
											</label>
											<?php elseif($wedplanners[5]["answer"] == "父母和亲朋的感受"):?>
											<label><input type="radio" name="importance" class="radchk" value="婚礼场面"/>婚礼场面</label>
											<label><input type="radio" name="importance" class="radchk" value="花费合理"/>花费合理</label>
											<label><input type="radio" name="importance" class="radchk" value="两人的感受"/>两人的感受</label>
											<label><input type="radio" name="importance" class="radchk" value="父母和亲朋的感受" checked="checked"/>父母和亲朋的感受</label><br />
											<label><input type="radio" name="importance" class="radchk" value=""/>其它</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您的想法">
											</label>
											<?php else:?>
											<label><input type="radio" name="importance" class="radchk" value="婚礼场面"/>婚礼场面</label>
											<label><input type="radio" name="importance" class="radchk" value="花费合理"/>花费合理</label>
											<label><input type="radio" name="importance" class="radchk" value="两人的感受"/>两人的感受</label>
											<label><input type="radio" name="importance" class="radchk" value="父母和亲朋的感受"/>父母和亲朋的感受</label><br />
											<label><input type="radio" name="importance" class="radchk" value="" checked="checked"/>其它</label>
											<label class="optional">
												<input value="<?php echo isset($wedplanners[5]["answer"]) ? $wedplanners[5]["answer"] : ""?>" class="textbox customer"  maxlength="30" placeholder="请输入您的想法">
											</label>
											<?php endif;?>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">请描述您的喜好，以便策划师更好地了解您并为您提供更满意的婚礼方案：（选填）</div>
											<textarea name="moreinfo" class="custextarea com-width300" data-options="required:true" maxlength="30" placeholder="请输入其他的要求或喜好"><?php echo isset($wedplanners[6]["answer"]) ? $wedplanners[6]["answer"] : ""?></textarea>
										</td>
									</tr>
									<tr>
										<td>
											<div class="question">有喜欢的真实婚礼案例，请输入该案例链接地址：（选填，可填3个）</div>
											<?php 
											   foreach($wedplanners as $key => $val){
												   if($val['alias']=="opus"){
													   $opus = $val['answer'];
												   }
											    
											   }
											   if(isset($opus)){
													$opus = $opus;
											   }else{
											        $opus = "";
											   }
											   
											  ?>
											<input name="opus" class="textbox" maxlength="30" placeholder="请输入案例链接地址" value="<?php echo $opus?>">
										</td>
									</tr>
								</table> 
								<input type="hidden" name="shopper_ids" value=""/>
							</div>
							</form>
							<form class="easyui-form" method="post" id="discount_img" data-options="novalidate:true">
								<div class="modulars">
									<h3>优惠信息</h3>
									<img src="<?php echo !empty($base['discount_img']) ? $this->config->config['img_url'].$base['discount_img'] : '' ; ?>"/>
								</div>
							</form>
						</div>
					</div>
					<?php endif;?>
					<?php if($mode == 2) : ?>
					<div title="婚礼需求" id="multstation" class="lftab wedneed">
						<div class="tabcont">
							<form class="easyui-form" method="post" id="wedbaseinfo" data-options="novalidate:true">
							<div class="modulars">
								<h3>婚礼基础信息：</h3>
								<table>
									<tr>
										<td class="vert-top" style="width:90px;">婚礼日期：</td>
										<td>
											<?php if(isset($base["wed_date_sure"])) : ?>
												<?php if($base["wed_date_sure"] == 1) : ?>
												<label>已确定</label>
												<?php else:?>
												<label>未确定</label>
												<?php endif;?>
												<label><?php echo $base["wed_date"]?></label>
												<input type="hidden" name="wed_date_sure" value="<?php echo isset($base["wed_date_sure"]) ? $base["wed_date_sure"] : ""?>">

												<input type="hidden" name="wed_date" value="<?php echo isset($base["wed_date"]) ? $base["wed_date"] : ""?>">
											<?php endif;?>
										</td>
									</tr>
									<tr>
										<td class="vert-top">婚礼地点：</td>
										<td>
											<input type="hidden" name="wed_location" value="<?php echo $base["wed_location"] ? $base["wed_location"] : ""?>">
										</td>
									</tr>
									<tr>
										<td class="vert-top">婚礼场地：</td>
										<td>
											<label><?php echo isset($base["wed_place"]) ? $base["wed_place"] : ""?></label>
											<input type="hidden" name="wed_place" value="<?php echo isset($base["wed_place"]) ? $base["wed_place"] : ""?>">
										</td>
									</tr>
									<tr>
										<td class="vert-top">婚宴类型：</td>
										<td>
											<label><?php echo isset($base["wed_party_type"]) ? $base["wed_party_type"] : ""?></label>
											<input type="hidden" name="wed_party_type" value="<?php echo isset($base["wed_party_type"]) ? $base["wed_party_type"] : ""?>">
										</td>
									</tr>
								</table>
							</div>
							</form>
							<form class="easyui-form" method="post" id="multwedneed" data-options="novalidate:true">
							<?php if($wedmaster) : ?>
								<div class="modulars compere">
								<h3>对主持人的要求：</h3>
								<table>
								<?php foreach($wedmaster as $key => $master){
									echo "<tr>";
									if(strpos($key, '_diy') === false){
										$answer = $master['answer'];
									$keyDiy = $key.'_diy';
									if(isset($wedmaster[$keyDiy])){
										$answer = $wedmaster[$keyDiy]['answer'];
									}
									echo "<td>".$master["word"]."</td>"."<td>".$answer."</td>";
									}
									echo "</tr>";
								}
								?>
								</table>
								</div>
								<div class="modulars compere" style="display: none">
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
									<tr class="contrsex">
										<td>
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
										
											<textarea name="wedmaster_remark" class="custextarea com-width300" maxlength="500" placeholder="请输入其他的要求或喜好"><?php echo $remark?></textarea>
										</td>
									</tr>
								</table>
							</div>
							<?php endif;?>
							<?php if($makeup) : ?>
							<div class="modulars makeup">
							<h3>对化妆师的要求：</h3>
								<table>
								<?php foreach($makeup as $key => $make){
									echo "<tr>";
									if(strpos($key, '_diy') === false){
										$answer = $make['answer'];
									$keyDiy = $key.'_diy';
									if(isset($makeup[$keyDiy])){
										$answer = $makeup[$keyDiy]['answer'];
									}
									echo "<td>".$make["word"]."</td>"."<td>".$answer."</td>";
									}
									echo "</tr>";
								}
								?>
								</table>
							</div>
							<div class="modulars makeup" style="display: none">
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
								<div class="modulars photoman">
								<h3>对摄影师的要求：</h3>
								<table>
								<?php foreach($wedphotoer as $key => $photoer){
									echo "<tr>";
									if(strpos($key, '_diy') === false){
										$answer = $photoer['answer'];
									$keyDiy = $key.'_diy';
									if(isset($wedphotoer[$keyDiy])){
										$answer = $wedphotoer[$keyDiy]['answer'];
									}
									echo "<td>".$photoer["word"]."</td>"."<td>".$answer."</td>";
									}
									echo "</tr>";
								}
								?>
								</table>
								</div>
							<div class="modulars photoman" style="display: none">
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
											<label><input type="checkbox" name="wedphotoer_service" class="radchk" id="wed_ps" value="婚纱照拍摄" <?php if(in_array("婚纱照拍摄",$service)){?> checked="checked" <?php }?>/>婚纱照拍摄</label>
											<label><input type="checkbox" name="wedphotoer_service" class="radchk" id="wed_gp" value="婚礼当天跟拍" <?php if(in_array("婚礼当天跟拍",$service)){?> checked="checked" <?php }?>/>婚礼当天跟拍</label>
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
								<div class="modulars cameraman">
								<h3>对摄像师的要求：</h3>
								<table>
									
								<?php foreach($wedvideo as $key => $video){
									echo "<tr>";
									if(strpos($key, '_diy') === false){
										$answer = $video['answer'];
									$keyDiy = $key.'_diy';
									if(isset($wedvideo[$keyDiy])){
										$answer = $wedvideo[$keyDiy]['answer'];
									}
									echo "<td>".$video["word"]."</td>"."<td>".$answer."</td>";
									}
									echo "</tr>";
								}
								?>
								</table>
								</div>
							<div class="modulars cameraman" style="display: none">
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
											<label><input type="checkbox" name="wedvideo_service" class="radchk" id="wed_wdy" value="婚礼前的爱情微电影" <?php if(in_array("婚礼前的爱情微电影",$service)){?> checked="checked" <?php }?>/>婚礼前的爱情微电影</label>
											<label><input type="checkbox" name="wedvideo_service" class="radchk" id="wedvideo_gp" value="婚礼当天跟拍" <?php if(in_array("婚礼当天跟拍",$service)){?> checked="checked" <?php }?>/>婚礼当天跟拍</label>
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
								<div class="modulars sitelayout">
								<h3>对场地布置的要求：</h3>
								<table>
								<?php foreach($sitelayout as $key => $site){
									echo "<tr>";
									if(strpos($key, '_diy') === false){
										$answer = $site['answer'];
									$keyDiy = $key.'_diy';
									if(isset($sitelayout[$keyDiy])){
										$answer = $sitelayout[$keyDiy]['answer'];
									}
									echo "<td>".$site["word"]."</td>"."<td>".$answer."</td>";
									}
									echo "</tr>";
								}
								?>
								</table>
								</div>
							<div class="modulars sitelayout" style="display: none">
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
											<label><input type="checkbox" name="ideal" class="radchk" value="自定义" <?php if(in_array("自定义",$color)){?> checked="checked" <?php }?>/>自定义</label>
											<label class="optional">
											<input class="textbox customer"  maxlength="30" placeholder="请输入您理想的婚礼形容词" value="<?php echo end($color)?>">
											</label>
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
							<form class="easyui-form" method="post" id="discount_img" data-options="novalidate:true">
								<div class="modulars">
									<h3>优惠信息</h3>
									<img src="<?php echo !empty($base['discount_img']) ? $this->config->config['img_url'].$base['discount_img'] : '' ; ?>"/>
								</div>
							</form>
						</div>
					</div>	
					<?php endif;?>
					<div title="商家信息" class="lftab">
					
    <form class="easyuiform" id="bsnsforms" method="post" data-options="novalidate:true">
        <table class="bsnstb" style="height:90px;width:1000px;">
           <tr>
               <td colspan="7">
				   投标时间：<input class="datetimebox time_21" style="width:120px;" name="time_21">&nbsp;至&nbsp;<input class="datetimebox endtime_21" style="width:120px;" name="endtime_21">
				   出方案时间：<input class="datetimebox time_46" style="width:120px;" name="time_46">&nbsp;至&nbsp;<input class="datetimebox endtime_46" style="width:120px;" name="endtime_46">
				   商铺名称：<input class="textbox studio_name com-width100" name="studio_name" maxlength="50" placeholder="请输入商铺名称">
				   名称：<input class="textbox nickname com-width100" name="nickname" maxlength="50" placeholder="请输入名称">
			   </td>
           </tr>
           <tr>
                <td class="tbfield">投标状态：</td>
                <td>
                    <select id="shoper_status" name="shoper_status" class="selectbox com-width150 shoper_status"> 
                        <option value="">--请选择--</option>
                        <option value="11">待投标</option>
                        <option value="21">已投标，待审核</option>
                        <option value="31">已投标，待初选</option>
                        <option value="41">初选中标，待出方案</option>
                        <option value="46">已出方案，待确认</option>
                        <option value="51">已中标</option>
                        <option value="99">未中标</option>
               
                   </select>
               </td>
                <td class="tbfield">所在地区：</td>
                <td class="linkage">
					<select class="com-width100 selectbox country_dlgs" name="country_dlgs" id="country_dlgs"></select>
					<select class="com-width100 selectbox province_dlgs" name="province_dlgs" id="province_dlgs"></select>
					<select class="com-width100 selectbox city_dlgs" name="city_dlgs" id="city_dlgs"></select>
               </td>
               <td class="tbfield">手机：</td>
               <td><input class="textbox phone com-width100" name="phone" maxlength="50" placeholder="请输入手机"></td>
               <td>
               <a href="javascript:void(0)" class="easyui-linkbutton searchdlgbtns c8 pl10 pr10" data-options="iconCls:'icon-search'">查 询</a>
               <a href="javascript:" class="easyui-linkbutton resetbtn c7">重 置</a>
               </td>
            </tr>
        </table>
    </form>
						<div class="tabcont">
	  						<a href="javascript:void(0)" class="easyui-linkbutton del" style="margin-left: 0;margin-bottom: 10px;" data-options="iconCls:'icon-no'">移除</a>
                            <?php if($order_status == 21):?>
							<a href="javascript:void(0)" class="easyui-linkbutton addbsnsinfo" data-options="iconCls:'icon-add'">确认审核</a><br /><br />
                            <?php endif;?>
							<table id="bsnsinfo" class="datagrid"></table>
							<input type="hidden" class="dmid" value="<?php echo isset($base["id"]) ? $base["id"] : 0?>"/>
                            <input type="hidden" id="be_status" value="<?php echo $be_status;?>"/>
                            <input type="hidden" id="order_status" value="<?php echo $order_status;?>"/>
						</div>
					</div>
				<div title="沟通记录" class="lftab">
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
				<!-- <div title="婚礼团队" class="lftab">
					<div class="tabcont">
						<a href="javascript:void(0)" class="easyui-linkbutton addteam" data-options="iconCls:'icon-add'">添加
						</a>
						<a href="javascript:void(0)" class="easyui-linkbutton delteam" data-options="iconCls:'icon-no'">取消合作
						</a>
						<a href="javascript:void(0)" class="easyui-linkbutton readdteam" data-options="iconCls:'icon-add'">重新合作
						</a>
						<br /><br />
						<table id="teamtb" class="datagrid"></table>
					</div>
				</div> -->
				<!-- <div title="交易优惠" class="lftab">
					<div class="tabcont">
						<a href="javascript:void(0)" class="easyui-linkbutton addcheap" data-options="iconCls:'icon-add'">添加优惠
						</a>
						<br /><br />
						<table id="cheaptb" class="datagrid"></table>
					</div>
				</div> -->
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
				</div>
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
		<input type="hidden" name="dmid" class="dmid" value="<?php echo isset($base["id"]) ? $base["id"] : 0?>"/>
        <input type="hidden" name="id" />
	</form>
</div>


<div id="contract" class="recommend">
	<form class="easyuiform" method="post">
		<table>
			<tr>
				<td class="tbfield">交易编号：</td>
				<td colspan="4">
					<label><?php echo $bingoShopper['order_id']?></label>
				</td>
			</tr>
			<tr>
				<td class="tbfield">商家姓名：</td>
				<td colspan="4">
					<label><?php echo $bingoShopper['nickname']?></label>
				</td>
			</tr>
			<tr>
				<td class="tbfield">商家电话：</td>
				<td colspan="4">
					<label><?php echo $bingoShopper['phone']?></label>
				</td>
			</tr>
			<tr>
				<td class="tbfield">签约编号：</td>
				<td colspan="4">
					<input class="textbox com-width150 contract_code" name="contract_code" data-options="required:true" maxlength="30" placeholder="请输入签约编号" value="<?php echo $contract['contract_code']?>" />
				</td>
			</tr>
			<tr>
				<td class="tbfield">签约金额：</td>
				<td colspan="4">
					<input class="textbox com-width150 money" name="money" data-options="required:true,validType:'money'" maxlength="30" placeholder="请输入签约金额" value="<?php echo $contract['money']?>" />
				</td>
			</tr>
			<tr>
				<td class="tbfield">婚礼日期：</td>
				<td colspan="4">
					<input class="datebox com-width150 wed_date" name="wed_date" data-options="required:true" value="<?php echo $contract['wed_date']?>" />
				</td>
			</tr>
			<tr>
				<td class="tbfield">婚礼地点：</td>
				<td colspan="4">
					<select class="com-width100 selectbox wed_country" name="wed_country" id="wed_country"></select>
					<select class="com-width100 selectbox wed_province" name="wed_province" id="wed_province"></select>
					<select class="com-width150 selectbox wed_city" name="wed_city" id="wed_city"></select>
					<input type="hidden" class="textbox-value" id="contract_wed_location" value="<?php echo $contract['wed_location']?>">
				</td>
			</tr>
			<tr>
				<td class="tbfield">婚礼场地：</td>
				<td colspan="4">
					<input class="textbox com-width300 wed_place" name="wed_place" data-options="required:true" maxlength="30" placeholder="请输入婚礼地点" value="<?php echo $contract['wed_place']?>" />
				</td>
			</tr>
			<tr>
				<td class="tbfield">备注：</td>
				<td colspan="4">
					<textarea class="custextarea com-autowidth comment" name="comment" maxlength="30" placeholder="请输入备注"><?php echo $contract['comment']?></textarea>
				</td>
			</tr>
		</table>
		<input type="hidden" name="demand_id" class="demand_id" value="<?php echo isset($base["id"]) ? $base["id"] : 0?>"/>
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
						 <?php foreach($list as $it){?>
							<option value="<?php echo $it['id']?>"><?php echo $it["name"]?></option>
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
		<input type="hidden" name="dmid" class="dmid" value="<?php echo isset($base["id"]) ? $base["id"] : 0?>"/>
       
        <input type="hidden" name="flagid"/>
	</form>
</div>
<div id="edit_shopper" class="editbsns">
    <form class="easyuiform" id="bsnsform" method="post" data-options="novalidate:true">
        <table class="bsnstb" style="width:500px">
            <tr>
                <td class="tbfield">商家类型：</td>
                <td>
                    <select id="shoper_mode" name="shoper_mode" class="selectbox com-width150 shoper_mode">
                        <option value="1">个人</option>
                        <option value="2">没有注册公司的工作室</option>
                        <option value="3">正式注册的公司</option>
                    </select>
                </td>
                <td class="tbfield">关键字：</td>
                <td>
                    <input class="textbox keywords com-width200" name="keywords" maxlength="50" placeholder="姓名/工作室名/手机号码">
                </td>
                <td class="tbfield">投标状态：</td>
                <td>
                    <select id="has_status" name="has_status" class="selectbox com-width150 has_status">
                        <option value="">--请选择--</option>
                        <option value="11">待投标</option>
                        <option value="21">已投标，待审核</option>
                        <option value="31">已投标，待初选</option>
                        <option value="41">初选中标，待出方案</option>
                        <option value="46">已出方案，待确认</option>
                        <option value="51">已中标</option>
                        <option value="99">未中标</option>

                    </select>
                </td>
            </tr>
            <tr>
                <td class="tbfield">所在地区：</td>
                <td colspan="5" class="linkage">
                    <select class="com-width100 selectbox province" name="province" id="province"></select>
                    <select class="com-width150 selectbox city" name="city" id="city"></select>
                    <a href="javascript:void(0)" class="easyui-linkbutton searchdlgbtnd c8 pl10 pr10" data-options="iconCls:'icon-search'">查 询</a>
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
		<input type="hidden" name="dmid" class="dmid" value="<?php echo isset($base["id"]) ? $base["id"] : 0?>"/>
	</form>
</div>
<div id="edit" class="recommend">
    <div id="container"></div>
</div>

<div id="edit_send" class="editbsns">
    <form class="easyuiform" id="bsnsformd" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td class="tbfield">服务报价：</td>
                <td><input name="price_start" data-options="validType:'number'" class="textbox com-width100 price_start" maxlength="9"></td>
                <td class="singlefw">至</td>
                <td><input name="price_end" data-options="validType:'number'" class="textbox com-width100 price_end" maxlength="9"></td>
                <td class="tbfield">商家类型：</td>
                <td>
                    <select id="shoper_modes" name="shoper_mode" class="selectbox com-width150 shoper_mode">
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
                <td><input name="pous_num_end" data-options="validType:'number'" class="textbox com-width100 pous_num_end" maxlength="9"></td>
                <td class="tbfield">所在地区：</td>
                <td colspan="3" class="linkage">
                    <select class="com-width100 selectbox province_dlg" name="province_dlg" id="province_dlg"></select>
                    <select class="com-width150 selectbox city_dlg" name="city_dlg" id="city_dlg"></select>
                    <a href="javascript:void(0)" class="easyui-linkbutton searchdlgbtn c8 pl10 pr10" data-options="iconCls:'icon-search'">查 询</a>
                </td>
            </tr>
        </table>
    </form>
    <input type="hidden" name="serves" id="serves" value="<?php echo isset($qa_info['alias_code']) ? $qa_info['alias_code'] : '';?>"/>
    <div class="easyuitabs" id="tbs" style="width:100%;height:284px;">
        <?php if(isset($qa_info['alias_code']) && $qa_info['alias_code'] == 1424) :?>
        <div title="主持人" style="padding:10px">
            <table id="wedmaster" data="1424" class="datagrid bsnstb" style="height:240px;"></table>
        </div>
        <?php endif;?>
        <?php if(isset($qa_info['alias_code']) && $qa_info['alias_code'] == 1425) :?>
        <div title="化妆师" style="padding:10px">
            <table id="makeup" data="1425" class="datagrid bsnstb" style="height:240px;"></table>
        </div>
        <?php endif;?>
        <?php if(isset($qa_info['alias_code']) && $qa_info['alias_code'] == 1423) :?>
        <div title="摄影师" style="padding:10px">
            <table id="wedphotoer" data="1423" class="datagrid bsnstb" style="height:240px;"></table>
        </div>
        <?php endif;?>
        <?php if(isset($qa_info['alias_code']) && $qa_info['alias_code'] == 1426) :?>
        <div title="摄像师" style="padding:10px">
            <table id="wedvideo" data="1426" class="datagrid bsnstb" style="height:240px;"></table>
        </div>
        <?php endif;?>
        <?php if(isset($qa_info['alias_code']) && $qa_info['alias_code'] == 1427) :?>
        <div title="场地布置" style="padding:10px">
            <table id="sitelayout" data="1427" class="datagrid bsnstb" style="height:240px;"></table>
        </div>
        <?php endif;?>
        <?php if(isset($qa_info['alias_code']) && $qa_info['alias_code'] == 1435) :?>
        <div title="策划师" style="padding:10px">
            <table id="wedplanners" data="1435" class="datagrid bsnstb" style="height:240px;"></table>
        </div>
        <?php endif;?>
    </div>
</div>

<!-- 选择婚礼团队的弹层 -->
<div id="team_edit_send" class="editbsns">
    <form class="easyuiform" id="team_bsnsformd" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td class="tbfield">服务报价：</td>
                <td><input name="price_start" data-options="validType:'number'" class="textbox com-width100 price_start" maxlength="9"></td>
                <td class="singlefw">至</td>
                <td><input name="price_end" data-options="validType:'number'" class="textbox com-width100 price_end" maxlength="9"></td>
                <td class="tbfield">商家类型：</td>
                <td>
                    <select id="team_shoper_modes" name="shoper_mode" class="selectbox com-width150 shoper_mode">
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
                <td><input name="pous_num_end" data-options="validType:'number'" class="textbox com-width100 pous_num_end" maxlength="9"></td>
                <td class="tbfield">所在地区：</td>
                <td colspan="3" class="linkage">
                    <select class="com-width100 selectbox province_dlg" name="province_dlg" id="team_province_dlg"></select>
                    <select class="com-width150 selectbox city_dlg" name="city_dlg" id="team_city_dlg"></select>
                    <a href="javascript:void(0)" class="easyui-linkbutton searchdlgbtn c8 pl10 pr10" data-options="iconCls:'icon-search'">查 询</a>
                </td>
            </tr>
        </table>
    </form>
    <input type="hidden" name="serves" id="team_serves" value="1424,1425,1423,1426,1427"/>
    <div class="easyuitabs" id="team_tbs" style="width:100%;height:284px;">
        <div title="主持人" style="padding:10px">
            <table id="team_wedmaster" data="1424" class="datagrid bsnstb" style="height:240px;"></table>
        </div>
        <div title="化妆师" style="padding:10px">
            <table id="team_makeup" data="1425" class="datagrid bsnstb" style="height:240px;"></table>
        </div>
        <div title="摄影师" style="padding:10px">
            <table id="team_wedphotoer" data="1423" class="datagrid bsnstb" style="height:240px;"></table>
        </div>
        <div title="摄像师" style="padding:10px">
            <table id="team_wedvideo" data="1426" class="datagrid bsnstb" style="height:240px;"></table>
        </div>
        <div title="场地布置" style="padding:10px">
            <table id="team_sitelayout" data="1427" class="datagrid bsnstb" style="height:240px;"></table>
        </div>
    </div>
</div>
<!-- 选择婚礼团队的弹层 -->

<!-- 添加编辑交易优惠的弹层 -->
<div id="edit_cheap" class="recommend">
	<form class="easyuiform" method="post" id="cheap_form">
		<table>
			<tr>
				<td class="tbfield">优惠对象：</td>
				<td colspan="2">
					<label>
						<select name="dis_target">
							<option value="1">新人</option>
							<option value="2">商家</option>
						</select>
					</label>
				</td>
			</tr>
			<tr>
				<td class="tbfield">优惠类型：</td>
				<td colspan="2">
					<label>
						<input class="textbox keywords com-width200" name="dis_type" maxlength="50" placeholder="请输入优惠类型">
					</label>
				</td>
			</tr>
			<tr>
				<td class="tbfield">优惠金额：</td>
				<td colspan="2">
					<label>
						<input class="textbox keywords com-width200" name="dis_amount" maxlength="50" placeholder="请输入优惠金额，为数字类型">元
					</label>
				</td>
			</tr>
			<tr>
				<td class="tbfield">优惠说明：</td>
				<td colspan="2">
					<label>
						<textarea class="custextarea com-autowidth content" name="comment" maxlength="500" placeholder="请输入优惠说明"></textarea>
					</label>
				</td>
			</tr>
		</table>
		<input type="hidden" name="demand_id" class="dmid" value="<?php echo isset($base["id"]) ? $base["id"] : 0?>"/>
	</form>
</div>
<!-- 添加编辑交易优惠的弹层 -->


<div id="cuslabel" class="cuslabel">
	<input class="cuslabel-searchbox" style="width:80%">
	<div><a class="checkall">全选</a> <a class="uncheckall">反选</a></div>
	<ul class="checkbox-tag"></ul>
</div>
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
	seajs.use("<?php echo $config['srcPath'];?>/js/trademanage/detailreview");
</script>
</body>
</html>