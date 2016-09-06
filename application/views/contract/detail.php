<!-- 右侧主体开始-->
    <div class="center-div">

        <div class="center-div-header">当前位置：【 合同处理  >  合同详情 】</div>
        <div class="customrecordiv comct">

            <div id="systemlogtab" class="easyui-tabs" style="overflow:visible;width:99%;">
                <a id="goback" href="javascript:history.go(-1)" class="easyui-linkbutton mb10 mr10 mt10" data-options="iconCls:'icon-back'">返 回</a>
<!--                --><?php //if($base_info["archive_status"] == $archivestatus["no_archive"]) : ?>
<!--                <a id="file-btn" href="javascript:" class="easyui-linkbutton save c1 mb10 ml20 mt10 auth-none" data-options="iconCls:'icon-save'" data-auth="api/contract/archive">合同归档</a>-->
<!--                --><?php //endif;?>
<!--                <a href="javascript:void(0)" class="easyui-linkbutton c7 auth-none" data-options="iconCls:'icon-ok'" id="sureBothContract" data-auth="contract/confirm/confirmBoth">确认合同</a>-->
<!--                <a href="javascript:void(0)" class="easyui-linkbutton c7 auth-none" data-options="iconCls:'icon-ok'" id="rejectBothContract" data-auth="contract/confirm/confirmBoth">驳回合同</a>-->
                <?php if($base_info['contract_status']==$contractstatus['confirmed'] && $base_info['type'] == 2
                 //&& in_array($base_info["fundstatus_serial"],array($fundstatus["already_first_back"],$fundstatus["remainder_back"],$fundstatus["all_back"]))
                 ){ ?>
                <a href="javascript:;" class="easyui-linkbutton auth-none" id="finish-contract" data-options="iconCls:'icon-add'"  data-auth="api/contract/completedContract">合同完成</a>
                <?php } ?>
                <?php if($base_info["contract_status"] == $contractstatus["confirmed"]) : ?>
                <a id="stop-btn" href="javascript:" class="easyui-linkbutton save c1 mb10 ml20 mt10 auth-none" data-options="iconCls:'icon-no'" data-auth="api/contract/stopContract">中止合同</a>
                <?php endif;?>
                <table class="label-tdstyle">
                    <tbody>
                        <tr>
                            <td class="tbfield">合同编号：</td>
                            <td><?php echo $base_info["contract_num"]?></td>
                            <td class="tbfield">合同状态：</td>
                            <td id="contract_status"><?php echo $base_info["contract_status_detail"]?></td>
							<td class="tbfield">付款状态：</td>
                            <td id="archive"><?php echo $third_contract["payment_status"]?></td>
                            <td class="tbfield">归档状态：</td>
                            <td id="archive"><?php echo $base_info["archive_status_detail"]?></td>
                            <td class="tbfield">返款状态：</td>
                            <td id="funds_status"><?php echo $base_info["funds_status"]?></td>
                            
                        </tr>
                        <tr>
							<td class="tbfield">合同类型：</td>
                            <td><?php echo $base_info["ctype"]?></td>
                            <td class="tbfield">交易编号：</td>
                            <td><?php echo $base_info["tradeno"]?></td>
                            <!-- <td class="tbfield">商机编号：</td>
                            <td><?php echo $base_info["business_num"]?></td> -->
                            <td class="tbfield">新人顾问：</td>
                            <td><?php echo $base_info["follower"]?></td>
                            <td class="tbfield">合同渠道：</td>
                            <td><?php echo $base_info["offline_text"]?></td>
                            <td class="tbfield">运营：</td>
                            <td><?php echo $base_info["operate"]?></td>
                        </tr>
                    </tbody>
                </table>
                <br />
                <div title="合同信息" class="lftab">
                    <h3 class="easyui-titleline"><span><?php echo $base_info['type']==2 ? "双方" : "三方" ;?>合同</span></h3>
                    <table class="contract-tdstyle">
                        <tbody>
                            <tr>
                                <td class="tbfield">商家昵称：</td>
                                <td><?php echo $third_contract["shopper_name"]?></td>
                                <td class="tbfield">商家类型：</td>
                                <td><?php echo $third_contract["shoper_type"]?></td>
                                <td class="tbfield">&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                            <td class="tbfield">婚礼日期：</td>
                            <td colspan="7"><?php echo $third_contract["wed_date"]?></td>
                        </tr>
                        <tr>
                            <td class="tbfield">婚礼地点：</td>
                            <td colspan="7"><?php echo $third_contract["wed_place"]?></td>
                        </tr>
                        <tr>
                            <td class="tbfield">初始预算：</td>
                            <td colspan="7"><?php echo $third_contract["wed_amount"]?>元</td>
                        </tr>
                        <tr>
                            <td class="tbfield">合同图片：</td>
                            <td colspan="7">
                                <a class="pic-window mr15" href="javascript:;"><img class="e-pic" width="70" height="100" title="编号页" src="<?php echo $third_contract["number_img"]?>" alt="编号页" /></a>
                                <a class="pic-window mr15" href="javascript:;"><img class="e-pic" width="70" height="100" title="签字页" src="<?php echo $third_contract["sign_img"]?>" alt="签字页" /></a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <s class="dashedline"></s>
                    <table class="contract-tdstyle">
                        <tbody>
						<tr>
								<td class="tbfield">申请时间：</td>
                                <td><?php echo $third_contract["create_time"]?></td>
                                <td class="tbfield">提交时间：</td>
                                <td><?php echo $third_contract["upload_time"]?></td>
                                <td class="tbfield">确认时间：</td>
                                <td><?php echo $third_contract["sign_time"]?></td>
                                <td class="tbfield">归档时间：</td>
                                <td><?php echo $third_contract["archive_time"]?></td>
                                
                                
                            </tr>
							<tr>
								<td class="tbfield">完成时间：</td>
                                <td><?php echo $third_contract["finish_time"]?></td>
							    <td class="tbfield">中止时间：</td>
                                <td><?php echo $third_contract["stop_time"]?></td>
								<td class="tbfield">驳回时间：</td>
                                <td><?php echo $third_contract["refuse_time"]?></td>
							</tr>
                        
                        </tbody>
                    </table>
                    <!-- <h3 class="easyui-titleline mb10"><span>中间合同</span></h3>
                    <table id="contract-mid" class="datagrid" style="width:950px;"></table> -->
                </div>
                <div title="款项信息" class="lftab">
                  
    
<!--                    <h3 class="easyui-titleline"><span>款项总览</span></h3>-->
<!--                    <table class="contract-tdstyle error mb10 fz16 ml20">-->
<!--                        <tbody>-->
<!--                            <tr>-->
<!--                                <td class="tbfield">最终合同金额：</td>-->
<!--                                <td>--><?php //echo $fund_info["contract_sum"]?><!--元</td>-->
<!--                                <td class="tbfield">优惠金额：</td>-->
<!--                                <td>--><?php //echo $fund_info["discount_amount"]?><!--元</td>-->
<!--                                <td class="tbfield">应收金额</td>-->
<!--                                <td>--><?php //echo $fund_info["should_amount"]?><!--元</td>-->
<!--                                <td class="tbfield">已收金额</td>-->
<!--                                <td>--><?php //echo $fund_info["gained_sum"]?><!--元</td>-->
<!--                            </tr>-->
<!--                        </tbody>-->
<!--                    </table>-->
                    <h3 class="easyui-titleline mt10 mb10"><span>收款明细</span></h3>

                    <table id="getmoneyInfo" class="datagrid"></table>
                      <?php
                       if($base_info['contract_status'] == $contractstatus['confirmed'] && $base_info['type']==1){ ?>
                        <br />
                        <a href="javascript:;" class="easyui-linkbutton" id="pay-chargedbtn" data-options="iconCls:'icon-add'">收款录入</a>
                    
                    <?php
                       }
                    ?>
                      
                    <h3 class="easyui-titleline mt10 mb10"><span>付款明细</span></h3>
                    <table id="paymoneyInfo" class="datagrid"></table>
                    <br />
                     <?php
                       if($base_info['contract_status'] == $contractstatus['confirmed'] && $base_info['type']==1){ ?>
                        <a href="javascript:;" class="easyui-linkbutton" id="pay-recordbtn" data-options="iconCls:'icon-add'" data-type="<?php echo $base_info['archive_status']?>">付款录入</a>
                        <a href="javascript:;" class="easyui-linkbutton" id="finish-all" data-options="iconCls:'icon-add'">全部返款完成</a>
                    <?php
                       }
                    ?>
<!--                    --><?php //if($base_info["contract_status"] == $contractstatus["confirmed"]) : ?>
<!--                    <a href="javascript:;" class="easyui-linkbutton auth-none" id="pay-recordbtn" data-options="iconCls:'icon-add'" data-auth="api/contract/entryPayment"-->
<!--                       --><?php //if($base_info["archive_status"] == $archivestatus["archived"]) : ?>
<!--                       data-type="1"-->
<!--                       --><?php //else : ?>
<!--                       data-type="0"-->
<!--                       --><?php //endif;?>
<!--                       >付款录入</a>-->
<!--                    <a href="javascript:;" class="easyui-linkbutton auth-none" id="finish-all" data-options="iconCls:'icon-add'"  data-auth="api/contract/completedContract">全部返款完成</a>-->
<!--                    --><?php //endif;?>
                </div>

                <div title="基本信息" class="lftab">
                    <form class="easyui-form" method="post" id="baseinfo" data-options="novalidate:true">
                        <table class="bigtb">
                            <tr>
                                <td><h3 style="font-weight:bolder; font-size: 14px;">基本信息</h3></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td class="tbfield">客户类型：</td>
                                <td><?php echo $info["usertype"]?></td>
                            </tr>

                            <tr>
                                <td class="tbfield">客户姓名：</td>
                                <td><input class="textbox com-width150" readonly name="username" maxlength="30" placeholder="请输入客户姓名" value="<?php echo $info["username"]?>"></td>
                                <td class="tbfield">客户身份：</td>
                                <td>
                                    <select id="userpart" disabled name="userpart" class="selectbox com-width150 userpart">
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
                                <td><input class="textbox com-width150" readonly name="mobile" data-options="validType:'mobile'" maxlength="11" placeholder="请输入手机号码" value="<?php echo $info["mobile"]?>"></td>
                                <td class="tbfield">客户电话：</td>
                                <td><input class="textbox com-width150" readonly name="tel" data-options="validType:'phone'" maxlength="30" placeholder="请输入固定电话" value="<?php echo $info["tel"]?>"></td>
                            </tr>
                            <tr>
                                <td class="tbfield">微信：</td>
                                <td><input class="textbox com-width150" readonly name="weixin" maxlength="100" placeholder="请输入微信号" value="<?php echo $info["weixin"]?>"></td>
                                <td class="tbfield">QQ：</td>
                                <td><input class="textbox com-width150" readonly name="qq" data-options="validType:'QQ'" maxlength="15" placeholder="请输入QQ号" value="<?php echo $info["qq"]?>"></td>
                            </tr>
                            <tr>
                                <td class="tbfield">其他联系方式：</td>
                                <td><input class="textbox" name="other_contact" readonly maxlength="100" placeholder="请输入其他联系方式" value="<?php echo $info["other_contact"]?>"></td>
                            </tr>

                        </table>
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
                                            <select id="ordertype" disabled name="ordertype" data-options="required:true" class="selectbox com-width150 ordertype">
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
                                            <label class="optional"><?php echo $info["wed_date_detail"]?></label><br />
                                            <label><input type="radio" name="is_wed_date" value="0" class="radchk" 
                                                          <?php if(empty($info["wed_date"])){ echo "checked";}?>
                                                          />还未确定</label>
                                            <label class="optional"><input name="weddate_note" class="textbox com-width200" data-options="" maxlength="200" placeholder="婚礼时间描述" value="<?php echo $info_extra["weddate_note"]?>"></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tbfield">婚礼地点：</td>
                                        <td>
                                            <input type="hidden" name="wed_location" value="<?php echo $info_extra["location"]?>">
                                        </td>
                                    </tr>
                                    <tr class="wed-place-1">
                                        <td class="tbfield">婚礼场地：</td>
                                        <td>
                                            <label><input type="radio" value="1" name="is_wed_place" class="radchk" 
                                                          <?php if(!empty($info_extra["wed_place"])){ echo "checked";}?>
                                                          />已确定</label>
                                            <label class="optional"><input name="wed_place" class="textbox com-width150" data-options="" maxlength="200" placeholder="请输入具体场地名称" value="<?php echo $info_extra["wed_place"]?>"></label><br />
                                            <label><input type="radio" value="0" name="is_wed_place" class="radchk" 
                                                          <?php if(empty($info_extra["wed_place"])){ echo "checked";}?>
                                                          />还未确定</label>
                                            <label class="optional"><input name="wed_place_area" class="textbox com-width150" data-options="" maxlength="200" placeholder="请输入具体场地名称" value="<?php echo $info_extra["wed_place_area"]?>"></label>
                                        </td>
                                    </tr>
                                    <tr class="wed-place-2">
                                        <td class="tbfield">场地区域：</td>
                                        <td>
                                            <input name="place_area" class="textbox com-width300" data-options="" maxlength="30" placeholder="场地区域范围要求" value="<?php echo $info_extra["wed_place_area"]?>">
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
<!--                                    <tr>
                                        <td class="tbfield">预计桌数：</td>
                                        <td>
                                            <input class="textbox customer" name="desk_from" maxlength="30" placeholder="">
                                            至
                                            <input class="textbox customer" name="desk_to" maxlength="30" placeholder="">
                                        </td>
                                    </tr>-->
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
                                            <input id="" maxlength="30" name="findnote" class="textbox findnote" placeholder="推荐商家数量" value="<?php echo $info_extra["findnote"]?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tbfield">期望联系时间及方式：</td>
                                        <td><input id="wish_contact" maxlength="30" name="wish_contact" class="textbox com-width300" value="<?php echo $info_extra["wish_contact"]?>"></td>
                                    </tr>
                                    <tr>
                                        <td  class="tbfield">
                                            更多描述：<br />
                                            <span class="flag">(商家可见)</span>
                                        </td>
                                        <td>
                                            <textarea class="custextarea com-width300 validatebox-text" name="moredesc" maxlength="500" placeholder="请输入客户备注"><?php echo $info_extra["moredesc"]?></textarea>
                                        </td>
                                    </tr>

                                </table>
                            </div>
                            <input type="hidden" name="id" id="userid" />
                        </form>

                    </div>
                </div>

                <div title="分配商家" class="lftab">
                    <!--分配商家弹层-->

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
                    <?php }elseif($info["status"]== $status['parted'] && $info["trade_status"] != $tradestatus['ordered']){?>
                       <label for="status_all" class="">
                            <input type="radio" name="status" value="all" id="status_all" >应标商家（n进4）</label>
                        <label for="status_date" class="on">
                            <input type="radio" name="status" value="date" id="status_date" checked>约见商家（4进2）
                        </label>
                        <label for="" class="disabled">
                            <input type="radio" name="status" value="sign" id="status_sign" >签约商家（2进1）
                        </label> 
                    <?php }elseif($info["status"] == $status['parted'] && $info["trade_status"] == $tradestatus['ordered']){?>
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
                    <table id="cusrecord" class="datagrid"></table>
                </div>
                <div title="内部备注" class="lftab">
                    <table id="memo" class="datagrid"></table>
                </div>

            </div>

        </div>
    </div>
    <!-- 右侧主体结束-->
</div>


<!--各种弹窗
    通过JS调用.
-->
<!--收款录入-->
<div id="add_charged" class="add_pay_td add_charged_td">
    <form class="easyuiform" id="add_charged_form" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td class="tbfield">到账时间：</td>
                <td><input id="charged_time" name="pay_time" class="datetimebox com-width150 charged_time"></td>
            </tr>
            <tr>
                <td class="tbfield">支付方式：</td>
                <td>
                    <select id="charged_way" name="pay_mode" class="selectbox com-width150 charged_way" data-options="required:true">
                       <option value="">--请选择--</option>
                        <option value="<?php echo $paymode["e_bank"]?>"><?php echo $paymode_explan["e_bank"]?></option>
                        <option value="<?php echo $paymode["alipay"]?>"><?php echo $paymode_explan["alipay"]?></option>
                        <option value="<?php echo $paymode["cash"]?>"><?php echo $paymode_explan["cash"]?></option>
                        <option value="<?php echo $paymode["other"]?>"><?php echo $paymode_explan["other"]?></option>

                    </select>
                </td>
            </tr>
            <tr>
                <td class="tbfield">支付金额：</td>
                <td><input id="charged_money" data-options="required:true" maxlength="30" name="amount" class="textbox pay_money com-width150">&nbsp;元</td>
            </tr>
            <tr>
                <td class="tbfield">款项类型：</td>
                <td><input id="charged_type" data-options="required:true" maxlength="20" name="fund_describe" class="textbox pay_money com-width150"></td>
            </tr>
              <tr>
                <td class="tbfield" style="vertical-align:top">备&nbsp;&nbsp;注：</td>
                <td>
                    <textarea id="charged_note" class="custextarea com-width300" style="height: 100px;" name="note" maxlength="500" placeholder=""></textarea>
                </td>
            </tr>


        </table>
    </form>
</div>

<!--付款录入-->
<div id="add_pay" class="add_pay_td">
    <form class="easyuiform" id="add_pay_form" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td class="tbfield">付款时间：</td>
                <td><input id="pay_time" name="pay_time" class="datetimebox com-width150 pay_time"></td>
            </tr>
            <tr>
                <td class="tbfield">支付方式：</td>
                <td>
                    <select id="pay_way" name="pay_mode" class="selectbox com-width150 pay_way" data-options="required:true">
                        <option value="">--请选择--</option>
                        <option value="<?php echo $paymode["e_bank"]?>"><?php echo $paymode_explan["e_bank"]?></option>
                        <option value="<?php echo $paymode["alipay"]?>"><?php echo $paymode_explan["alipay"]?></option>
                        <option value="<?php echo $paymode["cash"]?>"><?php echo $paymode_explan["cash"]?></option>
                        <option value="<?php echo $paymode["other"]?>"><?php echo $paymode_explan["other"]?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="tbfield">支付金额：</td>
                <td><input id="pay_money" data-options="required:true" maxlength="8" name="amount" class="textbox pay_money com-width150">元</td>
            </tr>

            <tr>
                <td class="tbfield" style="vertical-align:top">备&nbsp;&nbsp;注：</td>
                <td>
                    <textarea id="pay_note" class="custextarea com-width300" style="height: 100px;" name="note" maxlength="500" placeholder=""></textarea>
                </td>
            </tr>

           
        </table>
    </form>
</div>

<!--添加内部备注-->
<div id="add_memo" class="examinedlg">
    <form class="easyuiform" id="add_memo_form" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
			<tr>
        		<td class="tbfield vert-top">备注时间：</td>
        		<td>
        			<input name="record_time" class="datetimebox com-width120 record_time" value="<?php echo date('Y-m-d H:i:s')?>">
        		</td>
        	</tr>
                <td class="tbfield vert-top">备注内容：</td>
                <td>
                    <textarea class="custextarea com-width300" style="height: 100px;" data-options="required:true" name="content" maxlength="500" placeholder="请输入客户备注"></textarea>
                </td>
            </tr>
        </table>
        <input type="hidden" name="type" value="<?php echo 1;?>"/>
    </form>
</div>
<!--中止合同-->
<div id="stop_contract_reason" class="examinedlg">
    <form class="easyuiform" id="stop_contract_reason_form" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td>
                    <p>确认要中止合同!请填写中止原因.</p>
                    <textarea class="custextarea com-width300" style="height: 100px;" data-options="required:true" maxlength="200" placeholder="请输入原因，不超过200字" name="reason"></textarea>
                </td>
            </tr>
        </table>
        <input type="hidden" name="cid" value="<?php echo $cid;?>"/>
    </form>
</div>

<!--驳回弹层-->
<div id="rejectBothWindow" class="add_pay_td">
    <form class="easyuiform" id="rejectBothForm" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td colspan="2" class="pb10" style="color: red;font-weight: bold">提示：驳回原因商家可见！</td>
            </tr>
            <tr>
                <td class="vert-top tl">驳回内容：</td>
                <td>
                    <textarea id="rejectText" class="custextarea com-width300" style="height: 70px;" data-options="required:true" name="memo_text" maxlength="500" placeholder="请输入驳回原因"></textarea>
                </td>
            </tr>
        </table>
    </form>
</div>

<!--window 弹窗-->
<div id="easyui-w"></div>

<input type="hidden" name="" id="tab_id" value="1"/>
<input type="hidden" id="bid" name="bid" value="<?php echo $bid;?>" />
<input type="hidden" id="cid" name="cid" value="<?php echo $cid;?>" />
<input type="hidden" id="cur_page" value="1" />
<input type="hidden" id="cur_pagesize" value="20" />
<input type="hidden" id="contract_status_num" value="<?php echo $base_info['contract_status'];?>" />
<input type="hidden" id="contract_type" value="<?php echo $base_info['type'];?>" />

<?php $this->load->view('header/footer_view.php');?>

<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/contract/detail");
</script>
</body>
</html>