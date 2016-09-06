    <div class="center-div">

        <div class="center-div-header">当前位置：【 商机管理  >  顾问商机录入 】</div>
        <div class="customrecordiv comct">

            <div id="systemlogtab" class="easyui-tabs" style="overflow:visible;width:99%;">
                <a href="javascript:history.go(-1)" class="easyui-linkbutton mb10 mr10 mt10" data-options="iconCls:'icon-back'">返 回</a>
                <a id="save-btn" href="javascript:" class="easyui-linkbutton save c1 mb10 mr10 mt10" data-options="iconCls:'icon-save'">保存</a>

                <div title="基本信息" class="lftab">
                    <form class="easyui-form" method="post" id="baseinfo" data-options="novalidate:true">
                        <table class="bigtb">
                            <tr>
                                <td class="tbfield com-width150"><span class="flag">*</span> 商机来源：</td>
                                <td class="com-width300">
                                    <select id="source" data-options="required:true" name="source" class="selectbox com-width150 source">
                                        <option value="">--请选择--</option>
                                        <option value="<?php echo $source['mike'];?>"><?php echo $source_explan['mike'];?></option>
                                        <option value="<?php echo $source['internal_rec'];?>"><?php echo $source_explan['internal_rec'];?></option>
                                        <option value="<?php echo $source['weibo'];?>"><?php echo $source_explan['weibo'];?></option>
                                        <option value="<?php echo $source['channel_spread'];?>"><?php echo $source_explan['channel_spread'];?></option>
                                        <option value="<?php echo $source['hunbohui'];?>"><?php echo $source_explan['hunbohui'];?></option>
                                        <option value="<?php echo $source['callcenter'];?>"><?php echo $source_explan['callcenter'];?></option>
                                        <option value="<?php echo $source['live800'];?>"><?php echo $source_explan['live800'];?></option>
                                        <option value="<?php echo $source['youzan'];?>"><?php echo $source_explan['youzan'];?></option>
                                        <option value="<?php echo $source['53kf'];?>"><?php echo $source_explan['53kf'];?></option>
                                        <option value="<?php echo $source['qudaobao'];?>"><?php echo $source_explan['qudaobao'];?></option>
                                        <option value="<?php echo $source['other'];?>"><?php echo $source_explan['other'];?></option>
                                    </select>
                                    <input id="source_note" data-options="required:true" maxlength="30" name="source_note" class="textbox source_note">
                                </td>
                            </tr>
                            <!-- <tr>
                                <td class="tbfield">商机状态：</td>
                                <td>
                                    <select id="status_choose" name="" class="selectbox com-width100">
                                        <option value="">--请选择--</option>
                                        <option selected value="1">新增</option>
                                        <option value="2">跟进中</option>
                                        <option value="3">废商机</option>
                                    </select>
                                    <select id="status" name="status" class="selectbox com-width100 status"></select>
                                </td>
                            </tr> -->
                            <tr>
                                <td class="tbfield">客户类型： </td>
                                <td>
                                    <select class="selectbox com-width150 usertype" id="usertype" name="usertype">
                                        <option value="">--请选择--</option>
                                        <?php foreach($usertype as $val): ?>
                                        <option value="<?php echo $val; ?>"><?php echo $val; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="tbfield">客户姓名：</td>
                                <td><input class="textbox com-width150" name="username" maxlength="30" placeholder="请输入客户姓名"></td>
                            </tr>
                            <tr>
                                <td class="tbfield">客户身份：</td>
                                <td>
                                    <select id="userpart" name="userpart" class="selectbox com-width150 userpart">
                                        <option value="">--请选择--</option>
                                        <option value="<?php echo $customer['bridegroom'];?>"><?php echo $customer_explan['bridegroom']; ?></option>
                                        <option value="<?php echo $customer['bride'];?>"><?php echo $customer_explan['bride']; ?></option>
                                        <option value="<?php echo $customer['bridegroom_family'];?>"><?php echo $customer_explan['bridegroom_family']; ?></option>
                                        <option value="<?php echo $customer['bride_family'];?>"><?php echo $customer_explan['bride_family']; ?></option>
                                        <option value="<?php echo $customer['bridegroom_friend'];?>"><?php echo $customer_explan['bridegroom_friend']; ?></option>
                                        <option value="<?php echo $customer['bride'];?>"><?php echo $customer_explan['bride']; ?></option>
                                        <option value="<?php echo $customer['other'];?>"><?php echo $customer_explan['other']; ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="tbfield">客户手机：</td>
                                <td><input class="textbox com-width150" name="mobile" data-options="validType:'mobile'" maxlength="11" placeholder="请输入手机号码"></td>
                                <td class="tbfield">客户电话：</td>
                                <td><input class="textbox com-width150" name="tel" data-options="validType:'phone'" maxlength="20" placeholder="请输入固定电话"></td>
                            </tr>
                            <tr>
                                <td class="tbfield">微信：</td>
                                <td><input class="textbox com-width150" name="weixin" data-options="validType:'UNCHS'" maxlength="30" placeholder="请输入微信号"></td>
                                <td class="tbfield">QQ：</td>
                                <td><input class="textbox com-width150" name="qq" data-options="validType:'QQ'" maxlength="30" placeholder="请输入QQ号"></td>
                            </tr>
                            <tr>
                                <td class="tbfield">其他联系方式：</td>
                                <td><input class="textbox" name="other_contact" maxlength="30" placeholder="请输入其他联系方式"></td>
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
                                            <select id="ordertype" name="ordertype" data-options="required:true" class="selectbox com-width150 ordertype">
                                                <option value="">-请选择-</option>
                                                <option selected value="<?php echo $ordertype['wed_plan']; ?>"><?php echo $ordertype_explan['wed_plan']; ?></option>
                                                <option value="<?php echo $ordertype['wed_place']; ?>"><?php echo $ordertype_explan['wed_place']; ?></option>
                                                <option value="<?php echo $ordertype['plan_place']; ?>"><?php echo $ordertype_explan['plan_place']; ?></option>
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
                                            <label><input type="radio" name="is_wed_date" value="1" class="radchk" />已确定</label>
                                            <label class="optional"><input name="wed_date" id="wed_date" class="datebox wed_date"></label><br />
                                            <label><input type="radio" checked name="is_wed_date" value="0" class="radchk" />还未确定</label>
                                            <label class="optional"><input name="weddate_note" class="textbox com-width200" data-options="" maxlength="30" placeholder="婚礼时间描述"></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tbfield">婚礼地点：</td>
                                        <td>
                                            <select class="com-width100 selectbox wed_country" name="wed_country" id="wed_country"></select>
                                            <select class="com-width100 selectbox wed_province" name="wed_province" id="wed_province"></select>
                                            <select class="com-width150 selectbox wed_city" name="wed_city" id="wed_city"></select>
                                        </td>
                                    </tr>
                                    <tr class="wed-place-1">
                                        <td class="tbfield">婚礼场地：</td>
                                        <td>
                                            <label><input type="radio" name="is_wed_place" value="1" class="radchk" />已确定</label>
                                            <label class="optional"><input name="wed_place" class="textbox com-width150" data-options="" maxlength="30" placeholder="请输入具体场地名称"></label><br />
                                            <label><input type="radio" checked name="is_wed_place" value="0" class="radchk" />还未确定</label>
                                            <label class="optional"><input name="wed_place_area" class="textbox com-width150" data-options="" maxlength="30" placeholder="请输入具体场地名称"></label>
                                        </td>
                                    </tr>
                                    <tr class="wed-place-2">
                                        <td class="tbfield">场地区域：</td>
                                        <td>
                                            <input name="place_area" class="textbox com-width300" data-options="" maxlength="30" placeholder="场地区域范围要求">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tbfield">婚宴类型：</td>
                                        <td>
                                            <label><input checked type="radio" name="wed_type" class="radchk" value="<?php echo $wedtype['type_noon']; ?>" /><?php echo $wedtype_explan['type_noon']; ?></label>
                                            <label><input type="radio" name="wed_type" class="radchk" value="<?php echo $wedtype['type_night']; ?>" /><?php echo $wedtype_explan['type_night']; ?></label>
                                            <label><input type="radio" name="wed_type" class="radchk" value="<?php echo $wedtype['type_no']; ?>" /><?php echo $wedtype_explan['type_no']; ?></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tbfield">来宾人数：</td>
                                        <td>
                                            <input class="textbox customer" name="guest_from" maxlength="30" placeholder="">
                                            至
                                            <input class="textbox customer" name="guest_to" maxlength="30" placeholder="">
                                        </td>
                                    </tr>
                                    <!-- <tr>
                                        <td class="tbfield">预计桌数：</td>
                                        <td>
                                            <input class="textbox customer" name="desk_from" maxlength="30" placeholder="">
                                            至
                                            <input class="textbox customer" name="desk_to" maxlength="30" placeholder="">
                                        </td>
                                    </tr> -->
                                    <tr>
                                        <td class="tbfield">婚宴餐标：</td>
                                        <td>
                                            <input class="textbox customer" name="price_from" maxlength="30" placeholder="">
                                            至
                                            <input class="textbox customer" name="price_to" maxlength="30" placeholder="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tbfield">婚礼预算：</td>
                                        <td>
                                            <select id="budget" name="budget" class="selectbox com-width150 budget">
                                                <option value="">--请选择--</option>
                                                <option value="<?php echo $budget['lg_2']; ?>"><?php echo $budget_explan['lg_2']; ?></option>
                                                <option value="<?php echo $budget['2_4']; ?>"><?php echo $budget_explan['2_4']; ?></option>
                                                <option value="<?php echo $budget['4_7']; ?>"><?php echo $budget_explan['4_7']; ?></option>
                                                <option value="<?php echo $budget['7_10']; ?>"><?php echo $budget_explan['7_10']; ?></option>
                                                <option value="<?php echo $budget['gt_10']; ?>"><?php echo $budget_explan['gt_10']; ?></option>
                                                <option value="<?php echo $budget['not_sure']; ?>"><?php echo $budget_explan['not_sure']; ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tbfield">婚礼预算备注：</td>
                                        <td>
                                            <textarea class="custextarea com-width300 validatebox-text" name="budget_note" maxlength="500" placeholder="婚礼预算备注"></textarea>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><h3 style="font-weight:bolder; font-size: 14px;">基础信息</h3></td>
                                        <td></td>
                                    </tr>

                                    <tr>
                                        <td class="tbfield">找商家方式：</td>
                                        <td>
                                            <select id="findtype" name="findtype" class="selectbox com-width150 findtype">
                                                <option value="">--请选择--</option>
                                                <option selected value="<?php echo $findtype['easywed_recommand']; ?>"><?php echo $findtype_explan['easywed_recommand']; ?></option>
                                                <option value="<?php echo $findtype['people_self']; ?>"><?php echo $findtype_explan['people_self']; ?></option>
                                            </select>
                                            <input id="" maxlength="30" name="findnote" class="textbox findnote" placeholder="推荐商家数量">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tbfield">期望联系时间及方式：</td>
                                        <td><input id="wish_contact" maxlength="30" name="wish_contact" class="textbox com-width300"></td>
                                    </tr>
                                    <tr>
                                        <td  class="tbfield">
                                            更多描述：<br />
                                            <span class="flag">(商家可见)</span>
                                        </td>
                                        <td>
                                            <textarea class="custextarea com-width300 validatebox-text" name="moredesc" maxlength="500" placeholder="请输入客户备注"></textarea>
                                        </td>
                                    </tr>

                                </table>
                            </div>
                            <input type="hidden" name="id" id="userid" />
                        </form>

                    </div>
                </div>


            </div>

        </div>
    </div>
    <!-- 右侧主体结束-->
</div>


<?php $this->load->view('header/footer_view.php');?>

<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/business/add");
</script>
</body>
</html>