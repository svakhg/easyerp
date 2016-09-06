<div class="center-div">
	<div class="center-div-header">当前位置：【 系统管理  >  提醒人员设置 】</div>
    <div class="employmagagediv comct">
<!--        <form id="searchform" class="easyui-form" method="post" data-options="novalidate:true">-->
<!--            <table class="systemlogtb">-->
<!--                <tr>-->
<!--                    <td class="tbfield">员工姓名：</td>-->
<!--                    <td><input class="textbox" name="name" maxlength="30" placeholder="请输入员工姓名"></td>-->
<!--                    <td class="tbfield">员工编号：</td>-->
<!--                    <td><input class="textbox" name="code" maxlength="30" placeholder="请输入员工编号"></td>-->
<!--                    <td class="tbfield">归属部门：</td>-->
<!--                    <td>-->
<!--                        <select class="selectbox department com-width100" name="department"></select>-->
<!--                    </td>-->
<!--                    <td class="tbfield">-->
<!--                        <a href="javascript:void(0)" class="easyui-linkbutton subsearch c8" data-options="iconCls:'icon-search'">查询</a>-->
<!--                    </td>-->
<!--                </tr>-->
<!--            </table>-->
<!--        </form><br />-->
        <a href="javascript:void(0)" class="easyui-linkbutton addsetperson mb10" data-options="iconCls:'icon-add'">添加</a>
        <a href="javascript:void(0)" class="easyui-linkbutton delsetperson mb10" data-options="iconCls:'icon-no'">移除</a>
        <table id="empmanagetb" class="datagrid mt20"  ></table>
    </div>
</div>
</div>
<div id="edit_shopper" class="editbsns" style="height:296px;margin-top:5px;">
<!--    <form class="easyuiform" id="bsnsform" method="post" data-options="novalidate:true">-->
<!--        <table class="bsnstb" style="width:500px">-->
<!--            <tr>-->
<!--                <td class="tbfield">商家类型：</td>-->
<!--                <td>-->
<!--                    <select id="shoper_mode" name="shoper_mode" class="selectbox com-width150 shoper_mode">-->
<!--                        <option value="1">个人</option>-->
<!--                        <option value="2">没有注册公司的工作室</option>-->
<!--                        <option value="3">正式注册的公司</option>-->
<!--                    </select>-->
<!--                </td>-->
<!--                <td class="tbfield">关键字：</td>-->
<!--                <td>-->
<!--                    <input class="textbox keywords com-width200" name="keywords" maxlength="50" placeholder="姓名/工作室名/手机号码">-->
<!--                </td>-->
<!--                <td class="tbfield">投标状态：</td>-->
<!--                <td>-->
<!--                    <select id="has_status" name="has_status" class="selectbox com-width150 has_status">-->
<!--                        <option value="">--请选择--</option>-->
<!--                        <option value="11">待投标</option>-->
<!--                        <option value="21">已投标，待审核</option>-->
<!--                        <option value="31">已投标，待初选</option>-->
<!--                        <option value="41">初选中标，待出方案</option>-->
<!--                        <option value="46">已出方案，待确认</option>-->
<!--                        <option value="51">已中标</option>-->
<!--                        <option value="99">未中标</option>-->
<!---->
<!--                    </select>-->
<!--                </td>-->
<!--            </tr>-->
<!--            <tr>-->
<!--                <td class="tbfield">所在地区：</td>-->
<!--                <td colspan="5" class="linkage">-->
<!--                    <select class="com-width100 selectbox province" name="province" id="province"></select>-->
<!--                    <select class="com-width150 selectbox city" name="city" id="city"></select>-->
<!--                    <a href="javascript:void(0)" class="easyui-linkbutton searchdlgbtnd c8 pl10 pr10" data-options="iconCls:'icon-search'">查 询</a>-->
<!--                </td>-->
<!--            </tr>-->
<!--        </table>-->
<!--    </form>-->
    <table id="shoper" class="datagrid" style="height:240px;"></table>
</div>
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/systemanage/remindpersonset");
</script>
</body>
</html>