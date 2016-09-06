<div class="center-div">
    <div class="center-div-header">当前位置：【 财务管理  >  收支明细查询 】</div>
    <div class="customrecordiv comct">
        <form id="demand_search" class="easyui-form" method="post" data-options="novalidate:true">
            <table class="systemlogtb">
                <tr>
                    <td class="tbfield">添加时间：</td>
                    <td><input id="create_time_start" name="create_time_start" class="datebox create_time_start"></td>
                    <td class="singlefw">至</td>
                    <td><input id="create_time_end" name="create_time_end" class="datebox create_time_end"></td>
                    <td class="tbfield">交易编号：</td>
                    <td>
                        <input class="textbox c_demand_num com-width150" name="c_demand_num" style="margin-right:30px;" maxlength="50" placeholder="">
                    </td>
                    <td class="tbfield">合同编号：</td>
                    <td>
                        <input class="textbox contract_num com-width150" name="contract_num" style="margin-right:30px;" maxlength="50" placeholder="">
                    </td>
                    <td class="tbfield">商家姓名：</td>
                    <td style="width:80px;">
                        <input class="textbox shopper_name com-width150" name="shopper_name" style="margin-right:30px;" maxlength="50" placeholder="">
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">婚礼日期：</td>
                    <td><input id="wed_date_start" name="wed_date_start" class="datebox wed_date_start"></td>
                    <td class="singlefw">至</td>
                    <td><input id="wed_date_end" name="wed_date_end" class="datebox wed_date_end"></td>
                    <td class="tbfield">订单状态：</td>
                    <td colspan="2">
                        <select id="o_status" name="o_status" class="selectbox com-width300 o_status">
                            <option value="">--请选择--</option>
                            <?php foreach($order_status as $k => $v){ ?>
                                <option value="<?php echo $k?>"><?php echo $v?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td class="tbfield">签约状态：</td>
                    <td colspan="2">
                        <select id="s_status" name="s_status" class="selectbox com-width300 s_status">
                            <option value="">--请选择--</option>
                            <?php foreach($sign_status as $k => $v){ ?>
                                <option value="<?php echo $k?>"><?php echo $v?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">线上or线下</td>
                    <td colspan="2">
                        <select id="offline" name="offline" class="selectbox com-width150 offline">
                            <option value="">--请选择--</option>
                            <option value="0">线上</option>
                            <option value="1">线下</option>
                        </select> 
                    </td>
                    <td>
                        <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c8 pl10 pr10" data-options="iconCls:'icon-search'">查 询</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="shopper_alias" id="filter">
        </form>

        <table id="cusrecord" class="datagrid"  ></table>
    </div>
</div>
</div>

<div id="cuslabel" class="cuslabel">
    <input class="cuslabel-searchbox" style="width:80%">
    <div><a class="checkall">全选</a> <a class="uncheckall">反选</a></div>
    <ul class="checkbox-tag"></ul>
</div>
<input type="hidden" id="cur_page" value="<?php echo isset($page) ? $page : 1; ?>" />
<input type="hidden" id="cur_pagesize" value="<?php echo isset($pagesize) ? $pagesize : 10; ?>" />
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/followup/search");
</script>
</body>
</html>
