<!-- 右侧主体开始-->
<div class="center-div">

	<div class="center-div-header">当前位置：【 分单管理  >  分单详情  >  服务过程 】</div>
	<div class="customrecordiv comct">

		<div id="systemlogtab" class="easyui-tabs" style="overflow:visible;width:99%;">
			<a href="javascript:history.go(-1);" data-link="" class="easyui-linkbutton mb10 mr10 mt10" data-options="iconCls:'icon-back'" id="goback">返 回</a>
			
			<table class="label-tdstyle">
				<tbody>
					<tr>
						<td class="tbfield">商家昵称：</td>
						<td><?php echo $shopper_info['nickname']?></td>
						
						<td class="tbfield">跟进状态：</td>
						<td><?php echo ($shop_map_info['face_status'] == 2) ? "已见面" : "未见面" ?></td>
					</tr>
				</tbody>
			</table>
			<br />

			<div title="沟通灵感记录" class="lftab">
				<table id="record" class="datagrid"></table>
			</div>
			<div title="人员通讯录" class="lftab">
				<table id="contact" class="datagrid"></table>
			</div>

		</div>

	</div>
</div>
<!-- 右侧主体结束-->
</div>

<?php $this->load->view('header/footer_view.php');?>

<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/distorder/serviceprocess");
</script>
</body>
</html>