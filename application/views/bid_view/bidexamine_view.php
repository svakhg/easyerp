<div class="center-div">
    <div class="center-div-header">当前位置：【 婚礼需求管理  >  待审核需求 】</div>
      <div class="customrecordiv comct">
        <form id="demand_search" class="easyui-form" method="post" data-options="novalidate:true">
            <table class="systemlogtb" style="width:900px;">
                <tr>
                    <td class="tbfield" style="width:80px;">查找商家：</td>
                    <td style="width:175px;">
                        <select id="mode" name="mode" class="selectbox com-width150 mode"></select>
                    </td>
                    <td class="tbfield">婚礼日期：</td>
                    <td colspan="7"><input id="wed_from" name="wed_from" class="datebox wed_from"> 至 <input id="wed_to" name="wed_to" class="datebox wed_to">
                        
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">交易提示：</td>
                    <td>
                        <select id="remander_id" name="remander_id" class="selectbox com-width150 remander_id"></select>
                    </td>
                    <td class="tbfield">条件：</td>
                    <td  colspan="7">
                        <select id="condition" name="condition" class="selectbox com-width100">
                             <option value="">--请选择--</option>
                             <option value="交易编号">交易编号</option>
                             <option value="客户姓名">客户姓名</option>
                             <option value="手机号码">手机号码</option>
                        </select>
                        <input class="textbox condition_text com-width200" name="condition_text" style="margin-right:30px;" maxlength="50" placeholder="交易编号/客户姓名/手机号码">
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">婚礼预算：</td>
                    <td colspan="9">
                        <input id="lowe_amount" name="lowe_amount" class="textbox com-width100 lowe_amount" /> 至
                        <input id="high_amount" name="high_amount" class="textbox com-width100 high_amount" />
                        &nbsp;&nbsp;&nbsp;&nbsp;时间条件：
                        <select id="timecon" name="timecon" class="selectbox com-width100 timecon">
                             <option value="">--请选择--</option>
                             <option value="create_time">添加时间</option>
                             <option value="time_11">审核时间</option>
                             <option value="time_21">响应时间</option>
                             <option value="time_41">中标时间</option>
                             <option value="time_51">完成时间</option>
                             <option value="time_61">评价时间</option>
                             <option value="time_99">关闭时间</option>
                        </select>
                        <input id="start_time" name="start_time" class="datetimebox start_time"> 至 <input id="time_end" name="time_end" class="datetimebox time_end">
                    </td>           
                </tr>
                <tr>
                    <td class="tbfield">婚礼地点：</td>
                    <td colspan="9" class="linkage">
                        <select class="com-width100 selectbox country" name="country" id="country"></select>
                        <select class="com-width100 selectbox province" name="province" id="province"></select>
                        <select class="com-width150 selectbox city" name="city" id="city"></select>
                        <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c8 pl10 pr10 ml40" data-options="iconCls:'icon-search'">查 询</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7 pl10 pr10">重 置</a>
                    </td>
                </tr>
               
            </table>
            <input type="hidden" name="shopper_alias" id="filter">
        </form>
        <!--<a href="javascript:void(0)" class="easyui-linkbutton addadviser" data-options="iconCls:'icon-add'">分配顾问</a>
        <a href="javascript:void(0)" class="easyui-linkbutton batchexamine" data-options="iconCls:'icon-ok'">批量审核</a>
        <a href="javascript:void(0)" class="easyui-linkbutton batchmark" data-options="iconCls:'icon-tip'">批量标记</a>
        <a href="javascript:void(0)" class="easyui-linkbutton changetag" data-options="iconCls:'icon-redo'">转招投标</a>
        <a href="javascript:void(0)" class="easyui-linkbutton closetrad" data-options="iconCls:'icon-no'">关闭交易</a>-->
      <div class="filtertb"><ul><li class="filtertb-on">全部</li><li class="filtertb-off" id="wedplanners_1">找策划</li><li class="filtertb-off" id="wedmaster_1">找主持</li><li class="filtertb-off" id="makeup_1">找化妆</li><li class="filtertb-off" id="wedphotoer_1">找摄影</li><li class="filtertb-off" id="wedvideo_1">找摄像</li><li class="filtertb-off" id="sitelayout_1">找场布</li></ul></div>
        <table id="cusrecord" class="datagrid"></table>
    </div>
</div>
</div>
<div id="addadviser" class="examinedlg">
    <div class="singleds">新人顾问：<select name="counselor_uid" id="counselor_uid_d" class="selectbox com-width150 counselor_uid"></select></div>
</div>
<div id="batchmark" class="examinedlg">
    <div class="singleds">提示标记：<select name="remander_id" id="remander_id_d" class="selectbox com-width150 remander_id"></select></div>
</div>
<div id="changetag" class="examinedlg">
    <div class="singled">
        <h4>是否确认把选中的需求转换为自动匹配商家，转换后将不能再对需求人工指定商家！</h4>
        <label>转移原因：</label>
        <textarea id="changemode" class="custextarea com-width300" name="comment" maxlength="30" placeholder="请输入转移原因"></textarea>
    </div>
</div>
<div id="closetrad" class="examinedlg">
    <div class="singledl">
        <label>关闭原因：</label>
        <textarea id="closedemand" class="custextarea com-width300" name="comment" maxlength="30" placeholder="请输入关闭原因"></textarea>
    </div>
</div>
<div id="edit" class="editbsns">
    <form class="easyuiform" id="bsnsform" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td class="tbfield">服务报价：</td>
                <td><input name="price_start" data-options="validType:'number'" class="textbox com-width100 price_start" maxlength="9"></td>
                <td class="singlefw">至</td>
                <td><input name="price_end" data-options="validType:'number'" class="textbox com-width100 price_end" maxlength="9"></td>
                <td class="tbfield">商家类型：</td>
                <td>
                    <select id="shoper_mode" name="shoper_mode" class="selectbox com-width150 shoper_mode">
                        <option value="1">个人</option>
                        <option value="2">没有注册公司的工作室</option>
                        <option value="3">正式注册的公司</option>
                    </select>
                </td>
                <td colspan="2">
                    <input class="textbox keywords com-width200" name="keywords" maxlength="50" placeholder="姓名/工作室名/手机号码">
                </td>
            </tr>
            <tr>
                <td class="tbfield">案例数量：</td>
                <td><input name="opus_num_start" data-options="validType:'number'" class="textbox com-width100 opus_num_start" maxlength="9"></td>
                <td class="singlefw">至</td>
                <td><input name="pous_num_end" data-options="validType:'number'" class="textbox com-width100 pous_num_end" maxlength="9"></td>
                <td class="tbfield">所在地区：</td>
                <td colspan="3" class="linkage">
                    <select class="com-width100 selectbox province_dlg" name="province_dlg" id="province_dlg"></select>
                    <select class="com-width150 selectbox city_dlg" name="city_dlg" id="city_dlg"></select>
                    <a href="javascript:void(0)" class="easyui-linkbutton searchdlgbtn c8 pl10 pr10" data-options="iconCls:'icon-search'">查 询</a>
                </td>
            </tr>
        </table>
    </form>
    <input type="hidden" name="serves" id="serves" value="1435,1424,1425,1423,1426,1427"/>
    <div class="easyuitabs" id="tbs" style="width:100%;height:284px;">
            <div title="主持人" style="padding:10px">
                <table id="wedmaster" data="1424" class="datagrid bsnstb" style="height:240px;"></table>
            </div>
            <div title="化妆师" style="padding:10px">
                <table id="makeup" data="1425" class="datagrid bsnstb" style="height:240px;"></table>
            </div>
            <div title="摄影师" style="padding:10px">
                <table id="wedphotoer" data="1423" class="datagrid bsnstb" style="height:240px;"></table>
            </div>
            <div title="摄像师" style="padding:10px">
                <table id="wedvideo" data="1426" class="datagrid bsnstb" style="height:240px;"></table>
            </div>
            <div title="场地布置" style="padding:10px">
                <table id="sitelayout" data="1427" class="datagrid bsnstb" style="height:240px;"></table>
            </div>
            <div title="策划师" style="padding:10px">
                <table id="wedplanners" data="1435" class="datagrid bsnstb" style="height:240px;"></table>
            </div>
    </div>
</div>
<div id="seeveiw">
    <table class="tradetb" border="1">
        <thead>
        <tr>
            <th>ID</th>
            <th>交易编号</th>
            <th>服务项目</th>
            <th>新人预算</th>
            <th>交易状态</th>
            <th>交易金额</th>
            <th>签单日期</th>
            <th>提交时间</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>1</td>
            <td>A001111</td>
            <td>大型婚庆</td>
            <td>231人</td>
            <td>已交易</td>
            <td>3215.21</td>
            <td>2015-02-05 04:12:43</td>
            <td>2015-02-05 04:12:43</td>
        </tr>
        <tr>
            <td>2</td>
            <td>A001111</td>
            <td>大型婚庆</td>
            <td>231人</td>
            <td>已交易</td>
            <td>3215.21</td>
            <td>2015-02-05 04:12:43</td>
            <td>2015-02-05 04:12:43</td>
        </tr>
        <tr>
            <td>3</td>
            <td>A001111</td>
            <td>大型婚庆</td>
            <td>231人</td>
            <td>已交易</td>
            <td>3215.21</td>
            <td>2015-02-05 04:12:43</td>
            <td>2015-02-05 04:12:43</td>
        </tr>
        <tr>
            <td>4</td>
            <td>A001111</td>
            <td>大型婚庆</td>
            <td>231人</td>
            <td>已交易</td>
            <td>3215.21</td>
            <td>2015-02-05 04:12:43</td>
            <td>2015-02-05 04:12:43</td>
        </tr>
        </tbody>
    </table>
</div>
<div id="cuslabel" class="cuslabel">
    <input class="cuslabel-searchbox" style="width:80%">
    <div><a class="checkall">全选</a> <a class="uncheckall">反选</a></div>
    <ul class="checkbox-tag"></ul>
</div>
<input type="hidden" id="cur_page" value="<?php echo $page?>" />
<input type="hidden" id="cur_pagesize" value="<?php echo $pagesize?>" />
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/bsnsbidmanage/examinedemand");
</script>
</body>
</html>
