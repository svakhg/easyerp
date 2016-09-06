    <div class="center-div">
		<div class="center-div-header">当前位置：【 系统管理  >  修改密码 】</div>
        <div class="updatepwdiv comct">
            <form id="subform" class="easyuiform" method="post">
                <table cellpadding="5">
                    <tr>
                        <td>原密码:</td>
                        <td><input class="easyui-validatebox textbox com-width200" type="text" name="oldpwd" data-options="required:true,validType:'safepass',novalidate:true" maxlength="16"></td>
                    </tr>
                    <tr>
                        <td>新密码:</td>
                        <td><input class="easyui-validatebox textbox com-width200" type="password" id="newpwd" name="newpwd" data-options="required:true,validType:'safepass',novalidate:true" maxlength="16"></td>
                    </tr>
                    <tr>
                        <td>确认密码:</td>
                        <td><input class="easyui-validatebox textbox com-width200" type="password" name="comparepwd" data-options="required:true,validType:'equalTo[\'#newpwd\']',novalidate:true" maxlength="16"></td>
                    </tr>
                </table>
            </form>
            <div>
                <a href="javascript:void(0)" class="easyui-linkbutton subbtn c1">提交</a>
                <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重置</a>
            </div>
        </div>
    </div>
    </div>
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/systemanage/updatepwd");
</script>
</body>
</html>