<div class="center-div">
    <div class="center-div-header">当前位置：【 商家招投标管理  >  招投标列表 】</div>
    <div class="customrecordiv comct">
        <form id="demand_search" class="easyui-form" method="post" data-options="novalidate:true">
            <table class="systemlogtb" style="width:900px;">
                <tr>
                    <td class="tbfield">交易状态：</td>
                    <td>
                        <select id="status" name="status" class="selectbox com-width100 status">
                            <option value="">--请选择--</option>
                            <?php foreach($status_flip as $k => $v){ ?>
                                <option value="<?php echo $k?>"><?php echo $status_explan[$v];?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td class="tbfield">需求类型：</td>
                    <td>
                        <select id="remander_id" name="remander_id" class="selectbox com-width150 remander_id">
                            <option value="">--请选择--</option>
                            <?php foreach($shopper_alias as $k => $v){ ?>
                                <option value="<?php echo $k?>"><?php echo $v;?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td class="tbfield">提交日期：</td>
                    <td colspan="3" style="max-width:none;">
                        <input id="submit_time_start" name="submit_time_start" class="datetimebox submit_time_start">
                        至
                        <input id="submit_time_end" name="submit_time_end" class="datetimebox submit_time_end">
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">条件：</td>
                    <td colspan="3">
                        <select id="condition" name="condition" class="selectbox com-width100">
                            <option value="">--请选择--</option>
							<option value="number">交易编号</option>
                            <option value="nickname">发标商家</option>
                            <option value="studio_name">店铺名称</option>
                            <option value="phone">手机号码</option>
                        </select>
                        <input class="textbox condition_text com-width200" id="condition_text" name="condition_text" style="margin-right:30px;" maxlength="50" placeholder="交易编号/发标商家/店铺名称/手机号码">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <a href="javascript:void(0)" id="search_form" class="easyui-linkbutton searchbtn c8 pl10 pr10 ml40" data-options="iconCls:'icon-search'">查 询</a>
                        <a href="javascript:void(0)" id="reset_form" class="easyui-linkbutton resetbtn c7 pl10 pr10">重 置</a>
                    </td>
                </tr>
            </table>
        </form>
        <table id="cusrecord" class="datagrid"></table>
    </div>
</div>
</div>

<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/bsnsbidmanage/shopperbid2016");
</script>
</body>
</html>
