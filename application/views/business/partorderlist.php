<div class="center-div-header">当前位置：【 分单管理  >  分单管理 】</div>
<div class="center-div">
    <div class="customrecordiv comct">
        <form id="demand_search" class="easyui-form" method="get" data-options="novalidate:true">
            <table class="systemlogtb">
                <tbody>
                <tr>
                    <td class="tbfield">交易状态：</td>
                    <td colspan="2">
                        <select id="trade_status" name="trade_status" class="selectbox com-width150 o_status">
                            <option value="">全部</option>
                            <?php foreach($trade_status as $key => $status):?>
                            <option value="<?php echo $status;?>"><?php echo $trade_status_explan[$key];?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                    <td class="tbfield">客户类型：</td>
                    <td>
                        <select id="usertype" name="usertype" class="selectbox com-width150 o_type">
                            <option value="">全部</option>
                            <?php foreach($usertype as $utype):?>
                            <option value="<?php echo $utype;?>"><?php echo $utype;?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                    <td class="tbfield">新人顾问：</td>
                    <td>
                        <select id="adviser" name="adviser" class="selectbox com-width150 consultant">
                            <option value="">全部</option>
                            <?php foreach($adviser_list as $adviser):?>
                            <option value="<?php echo $adviser['id'];?>"><?php echo $adviser['username'];?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                    <td class="tbfield">分单时间：</td>
                    <td><input id="ordertime_from" name="ordertime_from" class="datebox com-width100 dist_time_start"></td>
                    <td class="singlefw">至</td>
                    <td><input id="ordertime_to" name="ordertime_to" class="datebox com-width100 dist_time_end"></td>
                </tr>
                <tr>
                    <td class="tbfield">条件选择：</td>
                    <td>
                        <select id="cond_type" name="cond_type" class="selectbox com-width150 s_status">
                            <option value="mobile">客户手机</option>
                            <option value="username">客户姓名</option>
                            <option value="tradeno">交易编号</option>
                            <!-- <option value="wed_place">婚礼地点</option>
                            <option value="bid">商机编号</option> -->
                        </select>
                    </td>
                    <td>
                        <input class="textbox condition_text com-width150" name="cond_value" id="cond_value" placeholder="">
                    </td>
                    <td class="tbfield">建单来源：</td>
                    <td>
                        <select id="source" name="source" class="selectbox com-width150 o_form">
                            <option value="">全部</option>
                            <?php foreach($source as $key => $val):?>
                            <option value="<?php echo $val;?>"><?php echo $source_explan[$key];?></option>
                            <?php endforeach;?>
                        </select>
                    </td>

                    <td class="tbfield">跟进策划师：</td>
                    <td>
                        <input class="textbox condition_text com-width150" name="planer" id="planer" placeholder="">
                    </td>
                    <td class="tbfield">婚礼日期：</td>
                    <td><input id="weddate_from" name="weddate_from" class="datebox com-width100 wed_time_start"></td>
                    <td class="singlefw">至</td>
                    <td><input id="weddate_to" name="weddate_to" class="datebox com-width100 wed_time_end"></td>
                </tr>
                <tr>
                    <td class="tbfield">运营：</td>
                    <td>
                        <select id="operate_uid" name="operate_uid" class="selectbox com-width80 c_consultant">
                            <option value="">全部</option>
                            <?php foreach ($operater as $key => $value): ?>
                            <option value="<?php echo $value['id']; ?>"><?php echo $value['username']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="10">
                        <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c7" data-options="iconCls:'icon-search'">查 询</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton c7" id="exportBtn" data-options="iconCls:'icon-redo'">导 出</a>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" name="shopper_alias" id="filter">
        </form>

        <table id="distlist" class="datagrid"></table>
    </div>
</div>
<!-- 右侧主体结束-->
</div>

<?php $this->load->view('header/footer_view.php');?>

<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/distorder/ordermanage");
</script>
</body>
</html>