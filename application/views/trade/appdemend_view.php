<div class="center-div">
	<div class="center-div-header">当前位置：【 婚礼需求管理  >  app需求草稿预览 】</div>
    <div class="customrecordiv comct">
        <table id="appDemandInfo" class="datagrid"  ></table>
    </div>
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
    seajs.use("<?php echo $config['srcPath'];?>/js/trademanage/appdemand");
</script>
</body>
</html>
