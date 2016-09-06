 <!-- 左侧导航结束-->
    <!-- 右侧主体开始-->
    <div class="center-div">
        <div class="center-div-header">当前位置：【 合同处理  >  收支明细查询 】</div>
        <div class="customrecordiv comct">
            <form id="demand_search" class="easyui-form" method="get" data-options="novalidate:true">
                <table class="systemlogtb">
                    <tbody>
                    <tr>
                        <td class="tbfield">支付时间：</td>
                        <td><input id="pay_time_start" name="pay_time_start" class="datetimebox com-width150"></td>
                        <td class="singlefw">至</td>
                        <td><input id="pay_time_end" name="pay_time_end" class="datetimebox com-width150"></td>
                        <td class="tbfield">收支类型：</td>
                        <td>
                            <select id="pay_type" name="pay_type" class="selectbox com-width100">
							
                                <option selected value="">--请选择--</option>
                                <option value="colse">收</option>
                                <option value="pay">支</option>
                            </select>
                        </td>
                        <td class="tbfield">支付方式：</td>
                        <td>
                            <select id="pay_by" name="pay_by" class="selectbox com-width100">
                                <option selected value="">--请选择--</option>
                                <?php foreach ($mode as $key => $val): ?>
                                <option value="<?php echo $val; ?>"><?php echo $mode_explan[$key]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="tbfield">签约方式：</td>
                        <td>
                            <select id="balance_type" name="sign_type" class="selectbox com-width100">
                                <option selected value="">--请选择--</option>
                                <?php foreach ($sign_types as $key => $val): ?>
                                <option value="<?php echo $val; ?>"><?php echo $sign_types_explan[$key]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="tbfield">条件选项：</td>
                        <td colspan="8">
                            <select id="options1" class="selectbox com-width100">
                                <option value="">--请选择--</option>
                                <option value="mobile" selected>流水号</option>
                                <option value="username">合同编号</option>
                            </select>
                            <input class="textbox condition_text com-width150" id="options2">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10">
                            <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c7" data-options="iconCls:'icon-search'">查询</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton c7" data-options="iconCls:'icon-redo'" id="exportBtn">导 出</a>
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

<!-- 添加收支记录的弹层 -->
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
	seajs.use("<?php echo $config['srcPath'];?>/js/contract/balanceList");
</script>
</body>
</html>
