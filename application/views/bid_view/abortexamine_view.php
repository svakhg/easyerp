    <div class="center-div">
        <div class="center-div-header">当前位置：【 弃单原因管理 】</div>
        <div class="customrecordiv comct">
            <form id="demand_search" class="easyui-form" method="post" data-options="novalidate:true">
                <table class="systemlogtb">
                    <tr>
                        <td class="tbfield">提交时间：</td>
                        <td>
                            <input id="time_97_from" name="time_97_from" class="datetimebox time_97_from">
                        </td>
                        <td class="singlefw">至</td>
                        <td>
                            <input id="time_97_to" name="time_97_to" class="datetimebox time_97_to">
                        </td>
                        <td class="tbfield">需求类型：</td>
                        <td>
                        <select id="shopper_alias" name="shopper_alias" class="selectbox com-width150 shopper_alias">
                            <option value="">--请选择--</option>
                            <option value="wedplanners">找策划</option>
                            <option value="wedmaster">找主持</option>
                            <option value="wedphotoer">找摄影</option>
                            <option value="wedvideo">找摄像</option>
                            <option value="sitelayout">找场布</option>
                        </select>
                        </td>
                        </tr>
                    <tr>
                        <td class="tbfield">关键字：</td>
                        <td colspan="3">
                            <input class="textbox keywords com-width300" name="keywords" style="margin-right:30px;" maxlength="50" placeholder="客户昵称/手机号码/店铺名称">
                        </td>
                        <td>
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c8 pl10 pr10 ml10" data-options="iconCls:'icon-search'">查 询</a>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="reason_status" id="filter" value="dai" />
            </form>
            <a href="javascript:void(0)" class="easyui-linkbutton examinesure" data-options="iconCls:'icon-ok'">审核通过</a>
            <a href="javascript:void(0)" class="easyui-linkbutton examineno" data-options="iconCls:'icon-no'">审核不通过</a>
            <label style="display:inline-block;vertical-align:middle;height:26px;"></label>
            <div class="filtertb">
                <ul>
                    <li class="filtertb-on" id="dai">待审核(<?php echo $count['dai'];?>)</li>
                    <li class="filtertb-off" id="yes">审核通过(<?php echo $count['yes'];?>)</li>
                    <li class="filtertb-off" id="no">审核不通过(<?php echo $count['no'];?>)</li>
                </ul>
            </div>
            <table id="cusrecord" class="datagrid"></table>
        </div>
    </div>
</div>

<div class="editpanel">
    <form class="easyuiform mult" method="post">
        <table class="ltr" style="width:600px;min-height:315px;">
            <tr>
                <td colspan="4" class="pl10">
                    <a href="javascript:" class="easyui-linkbutton save c1 mb10 mr10 mt10 saveletter" data-options="iconCls:'icon-save'">保存</a> 
                    <a href="javascript:" class="easyui-linkbutton mb10 mr10 mt10 closepanel" data-options="iconCls:'icon-back'">关闭</a>
                </td>
            </tr>
            <tr>
                <td class="tbfield" style="width:90px;">意向书编号：</td>
                <td>
                    <input class="textbox com-width150 id" name="id" readonly="readonly">
                </td>
            </tr>
            <tr>
                <td class="tbfield">商家名称：</td>
                <td>
                    <input class="textbox com-width150 nickname" name="nickname" disabled="disabled">
                </td>
                <td class="tbfield">提交时间：</td>
                <td>
                    <input class="textbox com-width150 time_97" name="time_97" disabled="disabled">
                </td>
            </tr>
            <tr>
                <td class="tbfield">需求类型：</td>
                <td>
                    <input class="textbox com-width150 shopper_alias" name="shopper_alias" disabled="disabled">
                </td>
                <td class="tbfield">意向书状态：</td>
                <td>
                    <input class="textbox com-width150 status" name="status" disabled="disabled">
                </td>
            </tr>
            <tr>
                <td class="tbfield">接单意愿：</td>
                <td>
                    <select id="wish" name="wish" class="selectbox com-width150 wish" disabled="disabled">
                        <option value="非常强烈">非常强烈</option>
                        <option value="希望争取">希望争取</option>
                        <option value="意愿一般">意愿一般</option>
                    </select>
                </td>
                <td class="tbfield">易结店铺：</td>
                <td>
                    <input class="textbox com-width150 studio_name" name="studio_name" disabled="disabled">
                </td>
            </tr>
            <tr>
                <td style="width:70px;text-align:right;">弃单原因：</td>
                <td colspan="3">
                    <textarea class="custextarea abort_reason" style="width:508px;" name="abort_reason"></textarea>
                </td>
            </tr>
        </table>
    </form>
</div>
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
       seajs.use("<?php echo $config['srcPath'];?>/js/bsnsbidmanage/giveupbcsmg");
</script>
</body>
</html>
