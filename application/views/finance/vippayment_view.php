<!-- 右侧主体开始-->
<div class="center-div">
    <div class="customrecordiv comct">
        <form id="rebatesForm" class="easyui-form" method="get" data-options="novalidate:true">
            <table class="systemlogtb">
                <tbody>
                <tr>
                    <td class="tbfield">支付时间：</td>
                    <td><input id="create_time_start" name="create_time_start" class="datetimebox com-width150"></td>
                    <td class="singlefw">至</td>
                    <td><input id="create_time_end" name="create_time_end" class="datetimebox com-width150"></td>

                    <td class="tbfield">签约套餐：</td>
                    <td>
                        <select id="package" name="package" class="selectbox com-width150">
                            <option value="">全部</option>
                            <option value="3">3个月</option>
                            <option value="6">6个月</option>
                            <option value="12">12个月</option>
                        </select>
                    </td>

                    <td class="tbfield">分站：</td>
                    <td>
                        <select id="site" name="site" class="selectbox com-width150">
                            <option value="">全部</option>
                            <?php foreach($sitelists as $val):?>
                                <option value="<?php echo $val['id'];?>"><?php echo $val['name'];?></option>
                            <?php endforeach;?>
                        </select>
                    </td>

                </tr>
                <tr>
                    <td class="tbfield">支付状态：</td>
                    <td>
                        <select id="pay_status" name="pay_status" class="selectbox com-width150">
                            <option value="">全部</option>
                            <option value="1">待支付</option>
                            <option value="2">已支付</option>
                            <option value="3">已过期</option>
                            <option value="6">支付失败</option>
                        </select>
                    </td>
                    <td class="tbfield">支付方式：</td>
                    <td>
                        <select id="pay_type" name="pay_type" class="selectbox com-width150">
                            <option value="">全部</option>
                            <option value="3">支付宝支付</option>
                            <option value="2">微信支付</option>
                            <option value="4">网银转账</option>
                            <option value="1">线下支付</option>
                        </select>
                    </td>
                    <td class="tbfield">手机号：</td>
                    <td>
                        <input id="phone" class="textbox condition_text com-width150" name="phone">
                    </td>
                    <td class="tbfield">流水号：</td>
                    <td>
                        <input id="numbers" class="textbox condition_text com-width150" name="numbers">
                    </td>
                </tr>
                <tr>
                    <td colspan="10">
                        <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c7" data-options="iconCls:'icon-search'">查询</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton insertbtn c7">录 入</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>

        <table id="contractlist" class="datagrid"></table>
    </div>
</div>
<!--window 弹窗-->
<div id="easyui-w"></div>
<!-- 录入弹层 -->
<div id="balancelayer" class="recommend">
    <form class="easyuiform" method="post" data-options="novalidate:true">
        <table>
            <tr>
                <td class="tbfield">商家类型：</td>
                <td>
                    <input type="radio" name="shopperType" checked="checked" value="2" />策划师<input type="radio" name="shopperType" value="1" />策划机构
                </td>
            </tr>
            <tr>
                <td class="tbfield">商家手机：</td>
                <td>
                    <input id="phone_add" class="textbox condition_text com-width150" name="phone" data-options="required:true">
                </td>
            </tr>
            <tr>
                <td class="tbfield">付款时间：</td>
                <td>
                    <input id="paytime_add" name="pay_time" class="datetimebox com-width150" data-options="required:true">
                </td>
            </tr>
            <tr>
                <td class="tbfield">付款套餐：</td>
                <td>
                    <select id="package_add" name="package" class="selectbox com-width150" data-options="required:true">
                        <option value="3">3个月</option>
                        <option value="6">6个月</option>
                        <option value="12">12个月</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="tbfield">到期时间：</td>
                <td>
                    <input id="validuntil_add" name="valid_until" class="datetimebox com-width150" data-options="required:true">
                </td>
            </tr>
            <tr>
                <td class="tbfield">付款金额：</td>
                <td>
                    <input id="amount_add" class="textbox condition_text com-width150" name="amount" data-options="required:true">
                </td>
            </tr>
        </table>
    </form>
</div>
<!-- 录入弹层 -->

<input type="hidden" id="cur_page" value="<?php echo isset($page) ? $page : 1; ?>" />
<input type="hidden" id="cur_pagesize" value="<?php echo isset($pagesize) ? $pagesize : 10; ?>" />
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/finance/vippaymentList");
</script>
</body>
</html>