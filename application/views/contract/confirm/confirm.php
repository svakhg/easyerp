<!-- 右侧主体开始-->
    <div class="center-div">
        <div class="center-div-header">当前位置：【 合同处理  >  财务收款确认 】</div>
        <div class="customrecordiv comct">
            <form id="demand_search" class="easyui-form" method="get" data-options="novalidate:true">
                <table class="systemlogtb">
                    <tbody>
                    <tr>
                        <td class="tbfield">款项状态：</td>
                        <td>
                            <select id="balance_status" name="balance_status" class="selectbox com-width150">
							<option value="">全部</option>
                                <?php foreach ($pay_status as $key => $val): ?>
                                <option value="<?php echo $val; ?>" <?php if($val==1){?> selected <?php }?>><?php echo $pay_status_explan[$key]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="tbfield">签约方式：</td>
                        <td>
                            <select id="balance_type" name="sign_type" class="selectbox com-width150">
                                <option selected value="">全部</option>
                                <?php foreach ($sign_types as $key => $val): ?>
                                    <option value="<?php echo $val; ?>"><?php echo $sign_types_explan[$key]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="tbfield">支付方式：</td>
                        <td>
                            <select id="pay_by" name="pay_by" class="selectbox com-width150">
                                <option selected value="">全部</option>
                                <?php foreach ($mode as $key => $val): ?>
                                <option value="<?php echo $val; ?>"><?php echo $mode_explan[$key]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="tbfield">提交时间：</td>
                        <td><input id="create_time_start" name="create_time_start" class="datetimebox com-width150"></td>
                        <td class="singlefw">至</td>
                        <td><input id="create_time_end" name="create_time_end" class="datetimebox com-width150"></td>
                    </tr>
                    <tr>
                        <td class="tbfield">商家昵称：</td>
                        <td>
                            <input id="nickname" class="textbox condition_text com-width150" name="nickname">
                        </td>
                        <td class="tbfield">合同编号：</td>
                        <td>
                            <input id="contractno" class="textbox condition_text com-width150" name="contractno">
                        </td>
                        <!-- <td class="tbfield">商家运营：</td>
                        <td>
                            <input id="sjyy" class="textbox condition_text com-width150" name="sjyy">
                        </td> -->
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
<div id="balancelayer" class="examinedlg">
    <form class="easyuiform" method="post" data-options="novalidate:true">
        <table>
            <tr>
                <td class="tc pt10" style="color: orange;line-height: 35px;font-weight: bold;font-size: 16px;"><div class="messager-icon messager-warning"></div>确认已经收到款项，请填写钱款到账时间！</td>
            </tr>
            <tr>
                <td class="tc">到账时间：<input id="receiveTime" name="receiveTime" class="datetimebox com-width150"></td>
            </tr>
        </table>
    </form>
</div>
<!-- 确认收款的弹层 -->

<!--驳回弹层-->
<div id="rejectlayer" class="examinedlg">
    <form class="easyuiform" id="add_memo_form" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td colspan="2" class="pb10" style="color: red;font-weight: bold">提示：驳回原因商家可见！</td>
            </tr>
            <tr>
                <td class="vert-top tl">驳回内容：</td>
                <td>
                    <textarea id='rejectText' class="custextarea com-width300" style="height: 70px;" data-options="required:true" name="memo_text" maxlength="500" placeholder="请输入客户备注"></textarea>
                </td>
            </tr>
        </table>
    </form>
</div>

<!-- 查看合同详情弹层 -->
<div id="contractlayer" class="recommend" style="width:780px;height:600px;overflow-y:scroll">
    <form class="easyuiform" method="post">
        <table>
            <tr>
                <td>商家昵称：<span class="bnickName">刘贤</span></td>
                <td>新人名称：<span class="username">刘贤</span></td>
            </tr>
            <tr>
                <td>婚礼日期：<span class="wedDate">2015-05-05</span></td>
                <td>合同金额：<span class="wedPrice">998</span></td>
            </tr>
            <tr>
                <td colspan="2" style="padding-bottom: 20px;">婚礼地点：<span class="wedPlace">测试地址</span></td>
            </tr>
            <tr>
                <td colspan="2">合同图片：</td>
            </tr>
            <tr>
                <td colspan="2" class="tc">签字页：</td>
            </tr>
            <tr>
                <td colspan="2" class="tc"><img class="signImg" src="http://static.dev.amazingday.cn/res/images/p1.jpg" width="90%" alt=""/></td>
            </tr>
            <tr>
                <td colspan="2" class="tc">金额页：</td>
            </tr>
            <tr>
                <td colspan="2" class="tc"><img class="numberImg" src="http://static.dev.amazingday.cn/res/images/p2.jpg" width="90%" alt=""/></td>
            </tr>
        </table>
    </form>
</div>
<!-- 确认收款的弹层2 -->
<div id="balancelayer2" class="examinedlg">
    <form class="easyuiform" method="post" data-options="novalidate:true">
        <table>
            <tr>
                <td class="pt10" style="color: orange;line-height: 17px;font-weight: bold;font-size: 16px;"><div class="messager-icon messager-warning"></div>已确认收到客户尾款，请及时把已收款的 80%，返给服务商家！</td>
            </tr>
            <tr>
                <td class="tc pt10">（返款请进入合同处理--根据合同号查询出需要返款的商家--录入商家返款）</td>
            </tr>
        </table>
    </form>
</div>
<!-- 查看合同详情弹层 -->
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/contract/financeList");
</script>

</body>
</html>
