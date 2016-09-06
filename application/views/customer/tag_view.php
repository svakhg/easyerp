<div class="center-div">
	<div class="center-div-header">当前位置：【 客户关系管理  >  客户标签 】</div>
    <div class="customarkerdiv comct">
		<input class="cuslabel-searchbox">&nbsp;&nbsp;
<!--		<a href="#" class="easyui-linkbutton com-width100">导出</a>-->
<!--		<a href="#" class="easyui-linkbutton com-width100">导入</a><br /><br />-->
		<a href="#" class="easyui-linkbutton addcuslabel" data-options="iconCls:'icon-add'">添加客户标签</a>
		<a href="#" class="easyui-linkbutton unused" data-options="iconCls:'icon-no'">删除</a><br /><br />
		<table id="cuslabel" class="datagrid" style="width:100%;"></table>
	</div>
</div>
</div>
<div id="edit" class="editbasicse">
	<form class="easyuiform" method="post">
		<table>
			<tr>
				<td>标签编号：</td>
				<td><input class="textbox" data-options="required:true,validType:'number'" name="order" maxlength="11" placeholder="请输入标签编号"></td>
			</tr>
			<tr>
				<td>标签名称：</td>
				<td><input class="textbox" data-options="required:true" name="tag_name" maxlength="50" placeholder="请输入标签名称"></td>
			</tr>
			<tr>
				<td>说明：</td>
				<td><textarea class="custextarea com-width200" name="comment" maxlength="200" placeholder="请输入说明"></textarea></td>
			</tr>
		</table>
		<input type="hidden" name="id" />
	</form>
</div>
    <?php $this->load->view('header/footer_view.php');?>
    <script type="text/javascript">
        seajs.use("<?php echo $config['srcPath'];?>/js/customer/customarker");
    </script>
</body>
</html>
