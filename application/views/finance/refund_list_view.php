<!-- 右侧主体开始-->
<div class="center-div">
    <div class="customrecordiv comct">
        <form id="rebatesForm" class="easyui-form" method="get" data-options="novalidate:true">
            <table class="systemlogtb">
                <tbody>
                <tr>
                    <td class="tbfield">款项状态：</td>
                    <td>
                        <select id="balance_status" name="refund_status" class="selectbox com-width150">
                            <option value="">全部</option>
                            <?php foreach($wd_status as $key => $val):?>
                                <option value="<?php echo $val;?>" <?php if($key=='wait_refund'){?>selected<?php }?>><?php echo $wd_status_explan[$key];?></option>
                            <?php endforeach;?>
						</select>
                    </td>
                    <td class="tbfield">签约方式：</td>
                    <td>
                        <select id="signed-type" name="sign_type" class="selectbox com-width150">
                            <option selected value="">全部</option>
                            <?php foreach($wd_types as $key => $val):?>
                                <option value="<?php echo $val;?>"><?php echo $wd_types_explan[$key];?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                    <td class="tbfield">提交时间：</td>
                    <td><input id="create_time_start" name="commit_begin_time" class="datetimebox com-width150"></td>
                    <td class="singlefw">至</td>
                    <td><input id="create_time_end" name="commit_begin_time" class="datetimebox com-width150"></td>
                    <td class="tbfield">商家昵称：</td>
                    <td>
                        <input id="nickname" class="textbox condition_text com-width150" name="shopper_name">
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">合同编号：</td>
                    <td>
                        <input id="contractno" class="textbox condition_text com-width150" name="contract_num">
                    </td>
                </tr>
                <tr>
                    <td colspan="10">
                        <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c7" data-options="iconCls:'icon-search'">查询</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" name="shopper_alias" id="filter">
        </form>

        <table id="contractlist" class="datagrid"></table>
    </div>
</div>
<!-- 右侧主体结束-->
</div>

<!--各种弹窗
    通过JS调用.
-->
<!--window 弹窗-->
<div id="easyui-w"></div>

<!-- 确认收款的弹层 -->
<div id="balancelayer" class="recommend">
    <form class="easyuiform" method="post" data-options="novalidate:true">
        <table>
            <tr>
                <td class="tbfield">支付方式：</td>
                <td>
                    <select id="pay-type" name="pay_mode" class="selectbox com-width150">
                        <?php foreach($pay_modes as $val):?>
                            <option value="<?php echo $val;?>"><?php echo $pay_modes_explan[$val];?></option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="tbfield">支付时间：</td>
                <td>
                    <input id="pay-time" name="pay_time" class="datetimebox com-width150" data-options="required:true">
                </td>
            </tr>
            <tr>
                <td class="tbfield">备注说明：</td>
                <td>
                    <textarea id="remark" class="custextarea com-width300" name="pay_note" maxlength="500" placeholder="请输入备注说明"></textarea>
                </td>
            </tr>
        </table>
    </form>
</div>
<!-- 确认收款的弹层 -->

<!-- 查看合同详情弹层 -->
<div id="contractlayer" class="editbsns">
    <form class="easyuiform" method="post">
        <table class="bsnstb">
            <tr>
                <td>返款状态：<span class="rebateStatus"></span></td>
                <td>商家昵称：<span class="busNikename"></span></td>
                <td>商家店铺：<span class="busShopname"></span></td>
            </tr>
        </table>
        <table id="withdrawlist" class="datagrid"></table>
    </form>
</div>
<!-- 查看合同详情弹层 -->

<input type="hidden" id="cur_page" value="<?php echo isset($page) ? $page : 1; ?>" />
<input type="hidden" id="cur_pagesize" value="<?php echo isset($pagesize) ? $pagesize : 10; ?>" />
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/finance/rebatesList");
</script>
</body>
</html>