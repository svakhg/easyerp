<div class="center-div">
    <div class="center-div-header">当前位置：【 商机管理  >  酒店商机管理 】</div>
    <div class="customrecordiv comct">
        <form id="demand_search" class="easyui-form" method="post" data-options="novalidate:true">
            <table class="systemlogtb">
                <tr>
                    <td class="tbfield">合作酒店名称：</td>
                    <td>
                        <input id="hotel_name" name="hotel_name" class="hotel_name">
                    </td>
                    <td class="tbfield">商机状态：</td>
                    <td>
                        <select id="status_1" name="status_1" class="selectbox com-width60 status_1">
                            <option value="">请选择</option>
                            <option selected value="0">全部</option>
                            <option value="1">新增</option>
                            <option value="3">跟进中</option>
                            <option value="101">无效信息</option>
						    <option value="7">已建单</option>
                            <option value="6">已分单</option>
                        </select>
                        <select id="status_2" name="status_2" class="selectbox com-width100 status_2"></select>
                    </td>
                    <?php if($is_satrap):?>
                    <td class="tbfield">酒店运营：</td>
                    <td>
                        <select id="sys_username" name="sys_username" class="selectbox com-width100 status_1">
                            <option value="">请选择</option>
                            <?php foreach($hotel_opers as $oper):?>
                            <option value="<?php echo $oper['id']?>"><?php echo $oper['username'];?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                    <?php endif;?>
                    <td>&nbsp;</td>
                    <td>
                        <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c8 pl10 pr10" data-options="iconCls:'icon-search'">查 询</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
                    </td>
                </tr>

            </table>
            <input type="hidden" name="shopper_alias" id="filter">
        </form>
        <table id="cusrecord" class="datagrid"></table>
    </div>
</div>

<div id="add_list" class="recommend">
    <form class="easyuiform" method="post" id="add_list_form" data-options="novalidate:true">
        <table>
            <tr>
                <td class="tbfield">客户姓名：</td>
                <td colspan="2">
                    <label>
                        <input class="textbox keywords com-width200" name="username" maxlength="50" placeholder="请输入客户姓名">
                    </label>
                </td>
            </tr>
            <tr>
                <td class="tbfield">客户手机：</td>
                <td colspan="2">
                    <label>
                        <input class="textbox keywords com-width200" data-options="required:true,validType:'mobile'" name="mobile" maxlength="50" placeholder="请输入客户手机">
                    </label>
                </td>
            </tr>
            <tr>
                <td class="tbfield">婚礼日期：</td>
                <td colspan="2">
                    <label>
                        <input class="datebox keywords com-width200" name="wed_date" maxlength="50" placeholder="请输入婚礼日期">
                    </label>
                </td>
            </tr>

            <tr>
                <td class="tbfield">合作酒店名称：</td>
                <td colspan="2">
                    <label>
                        <input class="textbox keywords com-width200" data-options="required:true" name="hotel_name" maxlength="50" placeholder="请输入合作酒店名称">
                    </label>
                </td>
            </tr>
            <tr>
                <td class="tbfield">备注：</td>
                <td colspan="2">
                    <label>
                        <textarea class="custextarea com-autowidth note" name="note" maxlength="500" placeholder="请输入备注"></textarea>
                    </label>
                </td>
            </tr>
            <tr>
                <td class="tbfield">测试数据：</td>
                <td colspan="2">
                    <select name="is_test">
                        <option value="0">否</option>
                        <option value="1">是</option>
                    </select>
                </td>
            </tr>
        </table>
    </form>
</div>

<input type="hidden" id="is_manage" value="<?php echo $is_satrap ? 1 : 0; ?>" />
<input type="hidden" id="cur_page" value="<?php echo isset($page) ? $page : 1; ?>" />
<input type="hidden" id="cur_pagesize" value="<?php echo isset($pagesize) ? $pagesize : 10; ?>" />
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/business/hotel");
</script>
</body>
</html>