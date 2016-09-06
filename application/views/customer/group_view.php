<div class="center-div">
	<div class="center-div-header">当前位置：【 客户关系管理  >  客户分组 】</div>
        <div class="customgroupdiv comct">
            <input class="cusgroup-searchbox">&nbsp;&nbsp;
<!--            <a href="javascript:void(0)" class="easyui-linkbutton com-width100">导出</a>-->
<!--            <a href="javascript:void(0)" class="easyui-linkbutton com-width100">导入</a><br /><br />-->
            <a href="javascript:void(0)" class="easyui-linkbutton addcusgroup" data-options="iconCls:'icon-add'">添加客户分组</a>
            <a href="javascript:void(0)" class="easyui-linkbutton unused" data-options="iconCls:'icon-no'">删除</a><br /><br />
            <table id="cusgroup" class="datagrid" style="width:100%;"></table>
        </div>
    </div>
    </div>
    <div id="edit" class="editbasicse">
        <form class="easyuiform" method="post">
            <table>
                <tr>
                    <td>分组编号：</td>
                    <td><input class="textbox" data-options="required:true" name="team_num" maxlength="18" placeholder="请输入分组编号"></td>
                </tr>
                <tr>
                    <td>分组名称：</td>
                    <td><input class="textbox" data-options="required:true" name="team_name" maxlength="50" placeholder="请输入分组名称"></td>
                </tr>
                <tr>
                    <td>说明：</td>
                    <td><textarea class="custextarea com-width200" data-options="required:true" name="comment" maxlength="200" placeholder="请输入说明"></textarea></td>
                </tr>
            </table>
            <input type="hidden" name="id" />
        </form>

    </div>

<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
	seajs.use("<?php echo $config['srcPath'];?>/js/customer/customgroup");
</script>
</body>
</html>
