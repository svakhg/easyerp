<div class="center-div">
	<div class="center-div-header">当前位置：【 系统管理  >  交易标记设置 】</div>
    <div id="basicsetab" class="easyui-tabs comct">
        <?php foreach($infos as $v):?>
            <div id="<?php echo $v['id']?>" title="<?php echo $v['func_name']?>" class="lftab">
                <input class="trademark-searchbox" style="width:20%">
                <a href="javascript:void(0)" class="easyui-linkbutton addtrademark" data-options="iconCls:'icon-add'">新建</a>
                <a href="javascript:void(0)" class="easyui-linkbutton used" data-options="iconCls:'icon-ok'">启用</a>
                <a href="javascript:void(0)" class="easyui-linkbutton unused" data-options="iconCls:'icon-no'">停用</a><br /><br />
                <table class="tb datagrid" style="width:100%;"></table>
            </div>
        <?php endforeach;?>
    </div>
</div>
</div>
<div id="edit" class="editbasicse">
    <form class="easyuiform" method="post">
        <table>
            <tr>
                <td>标记名称：</td>
                <td><input class="textbox" data-options="required:true" name="name" maxlength="30" placeholder="请输入标记名称"></td>
            </tr>
            <tr>
                <td>填充颜色：</td>
                <td><select id="color" name="color" class="selectbox com-width150 color"></select><label class="colorview"></label></td>
            </tr>
            <tr>
                <td></td>
                <td><label><input type="radio" name="enable" checked="checked" value="1" />启用</label><label><input type="radio" name="enable" value="0" />停用</label></td>
            </tr>
            <tr>
                <td>说明：</td>
                <td><textarea class="custextarea com-width200" name="comment" maxlength="30" placeholder="请输入客户备注"></textarea></td>
            </tr>
        </table>
        <input type="hidden" name="id">
    </form>
</div>
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/systemanage/trademark");
</script>
</body>
</html>