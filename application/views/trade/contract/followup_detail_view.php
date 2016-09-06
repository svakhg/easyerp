<div class="center-div">
    <div class="center-div-header">当前位置：【 需求跟进管理  >  合同详情 】</div>
    <div class="comct">
        <div id="maincontent">
            <div id="systemlogtab" class="easyui-tabs" style="overflow:visible; width: 99%;">
                <div title="基本信息" class="lftab">
                    <form class="easyui-form" method="post" id="baseinfo" data-options="novalidate:true">
                    <table class="bigtb">
                        <tr>
                            <td class="tbfield" style="width:100px;">新人姓名：</td>
                            <td style="width:160px;">
                                <label><?php echo $user_info['username']?></label>
                            </td>
                            <td class="tbfield" style="width:100px;">新人电话：</td>
                            <td style="width:160px;">
                                <label><?php echo $user_info['phone']?></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="tbfield">商家姓名：</td>
                            <td>
                                <label><?php echo $shopper_info['realname'] ?></label>
                            </td>
                            <td class="tbfield">合同编号：</td>
                            <td>
                                <label><?php echo $contract['contract_num'] ?></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="tbfield">婚期：</td>
                            <td>
                                <label><?php echo $contract['wed_date']?></label>
                            </td>
                            <td class="tbfield">婚礼地点：</td>
                            <td>
                                <label><?php echo $contract['wed_place']?></label>
                            </td>
                        </tr>
                    </table>
                    </form>
                </div>
                    
                <div title="婚礼团队" class="lftab">
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
                </div>

                <div title="交易优惠" class="lftab">
                    <div class="tabcont">
                        <a href="javascript:void(0)" class="easyui-linkbutton addcheap" data-options="iconCls:'icon-add'">添加优惠
                        </a>
                        <br /><br />
                        <table id="cheaptb" class="datagrid"></table>
                    </div>
                </div>

                <div title="收支记录" class="lftab">
                    <div class="tabcont">
                        <a href="javascript:void(0)" class="easyui-linkbutton payee" data-options="iconCls:'icon-add'">收款</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton payment" data-options="iconCls:'icon-add'">付款</a><br /><br />
                        <table id="incometb" class="datagrid"></table>
                    </div>
                </div>
            </div>
        </div>
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
                    <select class="com-width100 selectbox province_dlg" name="province_dlg" id="province_dlg"></select>
                    <select class="com-width150 selectbox city_dlg" name="city_dlg" id="city_dlg"></select>
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
                        <select class="selectbox com-width150" name="dis_target" data-options="required:true">
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
                        <input class="textbox keywords com-width200" data-options="required:true" name="dis_type" maxlength="50" placeholder="请输入优惠类型">
                    </label>
                </td>
            </tr>
            <tr>
                <td class="tbfield">优惠金额：</td>
                <td colspan="2">
                    <label>
                        <input class="textbox keywords com-width200" data-options="required:true,validType:'money'" name="dis_amount" maxlength="50" placeholder="请输入优惠金额，为数字类型">元
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
    </form>
</div>
<!-- 添加编辑交易优惠的弹层 -->

<!-- 添加收支记录的弹层 -->
<div id="editincome" class="recommend">
    <form class="easyuiform" method="post">
        <table>
            <tr>
                <td class="tbfield">款项类型：</td>
                <td>
                    <select class="selectbox com-width150" name="fund_type" data-options="required:true">
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
                
                    <select class="selectbox com-width150" name="pay_set_id" data-options="required:true">
                        <option value="">--请选择--</option>
                         <?php foreach($pay_set as $it){?>
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
        <input type="hidden" name="flagid" />
    </form>
</div>
<!-- 添加收支记录的弹层 -->

<div id="cuslabel" class="cuslabel">
    <input class="cuslabel-searchbox" style="width:80%">
    <div><a class="checkall">全选</a> <a class="uncheckall">反选</a></div>
    <ul class="checkbox-tag"></ul>
</div>
</div>
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/followup/detailreview");
</script>
</body>
</html>