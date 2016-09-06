<div class="center-div">
	<div class="center-div-header">当前位置：【 婚礼需求管理  >  待完善客户需求 】</div>
    <div class="customrecordiv comct">
        <form id="demand_search" class="easyui-form" method="post" data-options="novalidate:true">
            <table class="systemlogtb">
                <tr>
                    <td class="tbfield">添加时间：</td>
                    <td style="width:100px;"><input id="add_from" name="add_from" class="datetimebox add_from"></td>
                    <td class="singlefw">至</td>
                    <td><input id="add_to" name="add_to" class="datetimebox add_to"></td>
                    <td>
                        <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c8 p110 ml30 pr110" data-options="iconCls:'icon-search'">查 询</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
                    </td>
                </tr>
            </table>
        </form>
        <table id="cusrecord" class="datagrid"  ></table>
    </div>
</div>
</div>
<div id="edit" class="editrecord">
    <form class="easyuiform" method="post">
        <table>
            <tr>
                <td class="tbfield"><span class="flag">*</span> 客户姓名：</td>
                <td><input class="textbox" data-options="required:true" maxlength="30" placeholder="请输入客户姓名"></td>
                <td class="tbfield"><span class="flag">*</span> 客户来源：</td>
                <td>
                    <select class="selectbox com-width150">
                        <option value="1">开启</option>
                        <option value="2">关闭</option>
                    </select>
                </td>
                <td class="tbfield"><span class="flag">*</span> 获知渠道：</td>
                <td>
                    <select class="selectbox com-width150">
                        <option value="1">开启1</option>
                        <option value="2">关闭2</option>
                    </select>
                </td>
            </tr>
           <!--  <tr>
               <td class="tbfield">性别：</td>
               <td class="radiogroup"><label><input type="radio" name="sex" />：男</label><label><input type="radio" name="sex" />：女</label></td>
               <td class="tbfield">出生日期：</td>
               <td><input data-options="required:true" class="datebox com-width100"> <label>白羊座</label></td>
               <td class="tbfield">学历：</td>
               <td>
                   <select class="selectbox com-width150">
                       <option value="1">高中</option>
                       <option value="2">大专大专大专</option>
                       <option value="3">本科</option>
                       <option value="4">硕士</option>
                       <option value="5">博士</option>
                   </select>
               </td>
           </tr>
           <tr>
               <td class="tbfield">昵称：</td>
               <td><input class="textbox" data-options="required:true" maxlength="30" placeholder="请输入昵称"></td>
               <td class="tbfield">民族：</td>
               <td>
                   <select class="selectbox com-width150">
                       <option value="1">开启开</option>
                       <option value="2">关闭</option>
                   </select>
               </td>
               <td class="tbfield">血型：</td>
               <td>
                   <select class="selectbox com-width150">
                       <option value="1">开启1</option>
                       <option value="2">关闭2</option>
                   </select>
               </td>
           </tr> -->
            <tr>
                <td class="tbfield">手机号码：</td>
                <td><input class="textbox" data-options="required:true,validType:'mobile'" maxlength="30" placeholder="请输入手机号码"></td>
                <td class="tbfield">QQ：</td>
                <td><input class="textbox" data-options="required:true,validType:'QQ'" maxlength="30" placeholder="请输入QQ号"></td>
                <td class="tbfield">微信：</td>
                <td><input class="textbox" data-options="required:true,validType:'UNCHS'" maxlength="30" placeholder="请输入微信号"></td>
            </tr>
            <tr>
                <td class="tbfield">微博：</td>
                <td colspan="2"><input class="textbox com-width200" data-options="required:true,validType:'UNCHS'" maxlength="30" placeholder="请输入微博"></td>
                <td class="tbfield width100">电子邮箱：</td>
                <td colspan="2"><input class="textbox com-width200" data-options="required:true,validType:'email'" maxlength="50" placeholder="请输入电子邮箱"></td>
            </tr>
            <tr>
                <td class="tbfield">通讯地址：</td>
                <td colspan="5" class="linkage">
                    <select class="com-width60 selectbox">
                        <option value="AL">中国</option>
                        <option value="AK">日本</option>
                        <option value="AK">韩国</option>
                    </select>
                    <select class="com-width100 selectbox">
                        <option value="AL">北京</option>
                        <option value="AK">上海</option>
                        <option value="A2">广州</option>
                    </select>
                    <select class="com-width150 selectbox">
                        <option value="AL">昌平区</option>
                        <option value="AK">回龙观</option>
                        <option value="AK">朝阳区</option>
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input class="textbox com-width250" data-options="required:true" maxlength="100" placeholder="详细地址">
                </td>
            </tr>
            <tr>
                <td class="tbfield vert-top">邮编：</td>
                <td class="vert-top"><input class="textbox" data-options="required:true,validType:'ZIP'" maxlength="6" placeholder="请输入邮编"></td>
                <td class="tbfield vert-top">备注：</td>
                <td colspan="3"><textarea class="custextarea com-width300" data-options="required:true" maxlength="30" placeholder="请输入备注"></textarea></td>
            </tr>
            <tr>
                <td style="vertical-align:top;">角色权限：</td>
                <td colspan="5">
                    <div class="rolepanel cusrecord">
                        <ul class="roleitem">
                            <li><label><input type="checkbox" value="100101" /> 销售经理</label></li>
                            <li><label><input type="checkbox" value="100102" /> 财务人员</label></li>
                            <li><label><input type="checkbox" value="100103" /> 客服</label></li>
                            <li><label><input type="checkbox" value="100104" /> 销售经理</label></li>
                            <li><label><input type="checkbox" value="100105" /> 财务人员</label></li>
                            <li><label><input type="checkbox" value="100106" /> 客服</label></li>
                            <li><label><input type="checkbox" value="100107" /> 销售经理</label></li>
                            <li><label><input type="checkbox" value="100108" /> 财务人员</label></li>
                            <li><label><input type="checkbox" value="100109" /> 客服</label></li>
                            <li><label><input type="checkbox" value="100110" /> 财务人员</label></li>
                            <li><label><input type="checkbox" value="100111" /> 客服</label></li>
                            <li><label><input type="checkbox" value="100112" /> 销售经理</label></li>
                            <li><label><input type="checkbox" value="100113" /> 财务人员</label></li>
                            <li><label><input type="checkbox" value="100114" /> 客服</label></li>
                        </ul>
                        <input type="hidden" name="role" />
                    </div>
                </td>
            </tr>
            <tr>
                <td class="tbfield">标签：</td>
                <td colspan="2" class="text-left"><a class="abtn addlabel">+选择</a></td>
            </tr>
        </table>
    </form>
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
    seajs.use("<?php echo $config['srcPath'];?>/js/trademanage/perfectdemand");
</script>
</body>
</html>
