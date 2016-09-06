<div class="center-div">
    <div class="center-div-header">当前位置：【 财务管理  >  收支明细查询 】</div>
    <div class="customrecordiv comct">
        <form id="demand_search" class="easyui-form" method="post" data-options="novalidate:true">
            <table class="systemlogtb">
                <tr>
                    <td class="tbfield">支付日期：</td>
                    <td><input id="wed_from" name="wed_from" class="datebox wed_from"></td>
                    <td class="singlefw">至</td>
                    <td><input id="wed_to" name="wed_to" class="datebox wed_to"></td>
                    <td class="tbfield">支付方式：</td>
                    <td>
                        <select id="pay_set_id" name="pay_set_id" class="selectbox com-width150 pay_set_id">
                            <option value="">--请选择--</option>
                            <?php foreach($pay_set as $k => $v){ ?>
                                <option value="<?php echo $v['id']?>"><?php echo $v['name']?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td class="tbfield">款项类型：</td>
                    <td>
                        <input id="fund_type" name="fund_type" class="textbox com-width150 fund_type" />
                    </td>
                    <td class="tbfield">收支类型：</td>
                    <td>
                        <select id="inorout" name="inorout" class="selectbox com-width150 inorout">
                            <option value="">--请选择--</option>
                            <option value="1">收入</option>
                            <option value="2">支出</option>
                        </select>
                    </td>
                    <!-- <td class="tbfield">交易类型：</td>
                    <td>
                        <select id="channel" name="channel" class="selectbox com-width150 channel"></select>
                    </td> -->
                </tr>
                <tr>
                    <td class="tbfield">付款人：</td>
                    <td colspan="2">
                        <input class="textbox com-width300 pay_man" name="pay_man" style="margin-right:30px;" maxlength="50" placeholder="">
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">收款人：</td>
                    <td colspan="2">
                        <input class="textbox com-width300 gain_man" name="gain_man" style="margin-right:30px;" maxlength="50" placeholder="">
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c8 pl10 pr10" data-options="iconCls:'icon-search'">查 询</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="shopper_alias" id="filter">
        </form>

        <!--<div class="filtertb"><ul><li class="filtertb-on">全部</li><li class="filtertb-off" id="wedplanners_1">一站式</li><li class="filtertb-off" id="wedmaster_1">找主持</li><li class="filtertb-off" id="makeup_1">找化妆</li><li class="filtertb-off" id="wedphotoer_1">找摄影</li><li class="filtertb-off" id="wedvideo_1">找摄像</li><li class="filtertb-off" id="sitelayout_1">找场布</li></ul></div>-->
        <div class="filtertb"><a href="javascript:void(0)" class="easyui-linkbutton" id="add_btn">添 加</a></div>
        <table id="cusrecord" class="datagrid"  ></table>
    </div>
</div>
</div>

<div id="editincome" class="recommend">
    <form class="easyuiform" method="post">
        <table>
            <tr>
                <td class="tbfield">支付方式：</td>
                <td>
                    <select class="selectbox com-width150" name="pay_set_id" data-options="required:true">
                        <option value="">--请选择--</option>
                        <?php foreach($pay_set as $k => $v){ ?>
                            <option value="<?php echo $v['id']?>"><?php echo $v['name']?></option>
                        <?php } ?>
                    </select>
                </td>
                <td class="tbfield">账号：</td>
                <td>
                    <input type="text" name="pay_account" />
                </td>
                <td class="tbfield">支付金额：</td>
                <td>
                    <input class="textbox" name="pay_amount" data-options="required:true,validType:'money'" maxlength="20" placeholder="请输入金额">
                </td>
            </tr>
            <tr>
                <td id="payman" class="tbfield">付款人：</td>
                <td>
                    <input class="textbox" name="pay_man" maxlength="50" placeholder="请输入付款人姓名">
                </td>
                <td id="payman" class="tbfield">收款人：</td>
                <td>
                    <input class="textbox" name="gain_man" maxlength="50" placeholder="请输入收款人姓名">
                </td>
                <td id="paytime">支付时间：</td>
                <td>
                    <input class="datetimebox com-width150 start_time" id="start_time" name="start_time" data-options="required:true">
                </td>
            </tr>
            <tr>
                <td id="payman" class="tbfield">收入or支出：</td>
                <td>
                    <select class="selectbox com-width150" name="inorout" data-options="required:true">
                        <option value="1">收入</option>
                        <option value="2">支出</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="tbfield">备注：</td>
                <td colspan="6">
                    <textarea class="custextarea com-autowidth"  name="comments" maxlength="500" placeholder="请输入备注"></textarea>
                </td>
            </tr>
        </table>
    </form>
</div>

<div id="related" class="recommend">
    <form class="easyuiform" method="post">
        <table>
            <tr>
                <td class="tbfield">合同编号：</td>
                <td>
                    <input class="textbox" name="contract_num" data-options="required:true" maxlength="20" placeholder="请输入合同编号">
                </td>
            </tr>
            <tr>
                <td class="tbfield">付款账号：</td>
                <td>
                    <input type="text" name="pay_account" class="textbox" readonly/>
                </td>
            </tr>
            <tr>
                <td class="tbfield">支付金额：</td>
                <td>
                    <input type="text" name="pay_amount" class="textbox" readonly/>
                </td>
            </tr>
        </table>
        <input type="hidden" name="p_id"/>
    </form>
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
    seajs.use("<?php echo $config['srcPath'];?>/js/inandout/search");
</script>
</body>
</html>