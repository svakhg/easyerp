<div class="center-div">
	<div class="center-div-header">当前位置：【 系统管理  >  员工管理 】</div>
    <div class="employmagagediv comct">
        <form id="searchform" class="easyui-form" method="post" data-options="novalidate:true">
            <table class="systemlogtb">
                <tr>
                    <td class="tbfield">员工姓名：</td>
                    <td><input class="textbox" name="name" maxlength="30" placeholder="请输入员工姓名"></td>
                    <td class="tbfield">员工编号：</td>
                    <td><input class="textbox" name="code" maxlength="30" placeholder="请输入员工编号"></td>
                    <td class="tbfield">归属部门：</td>
                    <td>
                        <select class="selectbox department com-width100" name="department"></select>
                    </td>
                    <td class="tbfield">账号状态：</td>
                    <td>
                        <select class="selectbox com-width100" name="status">
                            <option value="1">开启</option>
                            <option value="0">停用</option>
                        </select>
                    </td>
                    <td class="tbfield">
                        <a href="javascript:void(0)" class="easyui-linkbutton subsearch c8" data-options="iconCls:'icon-search'">查询</a>
                    </td>
                </tr>
            </table>
        </form><br />
        <a href="javascript:void(0)" class="easyui-linkbutton addempmanagetb" data-options="iconCls:'icon-add'">添加员工</a>
        <a href="javascript:void(0)" class="easyui-linkbutton used" data-options="iconCls:'icon-ok'">启用</a>
        <a href="javascript:void(0)" class="easyui-linkbutton unused" data-options="iconCls:'icon-no'">停用</a><br /><br />
        <table id="empmanagetb" class="datagrid"  ></table>
    </div>
</div>
</div>
<div id="edit" class="editemploy">
    <form class="easyuiform" method="post" enctype="multipart/form-data">
        <table>
			 <tr>
                <td>员工头像：</td>
                <td>
                    <img id="head_img" src="" name="head_img" height="100" width="100" />
                    <input type="hidden" id="img_value" name="img_value" value="" />
                    <input type="file" id="head_file" />
                </td>
            </tr> 
            <tr>
                <td>员工姓名：</td>
                <td><input class="textbox" name="username" data-options="required:true" maxlength="30" placeholder="请输入员工姓名"></td>
            </tr>
            <tr>
                <td>员工编号：</td>
                <td><input class="textbox" name="num_code" data-options="required:true" maxlength="8" placeholder="请输入员工编号"></td>
            </tr>
            <tr>
                <td>员工手机：</td>
                <td><input class="textbox" name="mobile" maxlength="13" placeholder="请输入员工手机"></td>
            </tr>
            <tr>
                <td>是否主管：</td>
                <td>
                    <label><input type="radio" name="satrap" class="radchk" value="1" />是</label>
                    <label><input checked type="radio" name="satrap" class="radchk" value="0" />否</label>
                </td>
            </tr>
			<tr>
                <td>是否测试账号：</td>
                <td>
                    <label><input type="radio" name="is_test" class="radchk" value="1" />是</label>
                    <label><input checked type="radio" name="is_test" class="radchk" value="0" />否</label>
                </td>
            </tr>
            <tr>
                <td>归属部门：</td>
                <td>
                    <select class="selectbox department com-width100" data-options="required:true" id="department" name="department"></select>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:top;">员工角色：</td>
                <td>
                    <div class="rolepanel">
                        <ul class="roleitem"></ul>
                        <input type="hidden" name="role_id" />
                    </div>
                </td>
            </tr>
        </table>
        <input type="hidden" name="id">
    </form>
    <table class="container">
        <tr class="add">
            <td>登录密码：</td>
            <td><input class="textbox" name="newpwd" data-options="required:true,validType:'safepass'" maxlength="16" placeholder="请输入登录密码"></td>
        </tr>
        <tr class="edit">
            <td>登录密码：</td>
            <td>
                <i class="ipwd">*********************</i><a class="changepwd">重改密码</a>
            </td>
        </tr>
    </table>
</div>
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/systemanage/employmagage");
</script>
</body>
</html>