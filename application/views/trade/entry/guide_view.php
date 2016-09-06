<div class="center-div">
	<div class="center-div-header">当前位置：【 婚礼需求管理  >  录入客户需求 】</div>
	<div id="guide" class="comct">
		<form method="post" id="guide_form" action="/trade/entry/guide">
			<table>
				<tr>
					<td class="vert-top">请选择需求类型：</td>
					<td class="chosetype">
						<label><input type="radio" class="radchk onest" name="wedtype" value="1" checked="checked" />一站式婚礼</label><br />
						<label><input type="radio" class="radchk mult" name="wedtype" value="2" />单项婚礼服务</label>
						<div>
							<label><input type="checkbox" class="radchk sinserv" value="1424" />找主持人</label>
							<label><input type="checkbox" class="radchk sinserv" value="1425" />找化妆师</label>
							<label><input type="checkbox" class="radchk sinserv" value="1426" />找摄像师</label>
							<label><input type="checkbox" class="radchk sinserv" value="1423" />找摄影师</label>
							<label><input type="checkbox" class="radchk sinserv" value="1427" />找场地布置</label>
						</div>
						<input type="hidden" name="multvalue" class="multvalue" />
					</td>
				</tr>
			</table>
		<a href="javascript:" class="easyui-linkbutton nextstep c1" data-options="iconCls:'icon-save'">下一步</a>
		</form>
	</div>
</div>
</div>
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
	seajs.use("<?php echo $config['srcPath'];?>/js/trademanage/includguide");
</script>
</body>
</html>