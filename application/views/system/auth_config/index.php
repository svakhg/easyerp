<div class="center-div">
	<div class="center-div-header">当前位置：【 系统管理  >  权限配置 】</div>
    <div class="clearfix atycontent comct">
        <div class="multselect modularlevel">
            <div class="text-left mg-bottom10">模块权限：</div>
            <div class="operate">
                <a href="javascript:void(0)" class="easyui-linkbutton addml" data-options="iconCls:'icon-add'">新增</a>
                <a href="javascript:void(0)" class="easyui-linkbutton editml" data-options="iconCls:'icon-edit'">修改</a>
                <a href="javascript:void(0)" class="easyui-linkbutton delml" data-options="iconCls:'icon-no'">删除</a>
                <input type="hidden" class="level" value="1" />
                <input type="hidden" class="pid" value="0" />
            </div>
            <select multiple="multiple"></select>
        </div>
        <div class="multselect linklevel">
            <div class="text-left mg-bottom10"><span></span>链接权限：</div>
            <div class="operate">
                <a href="javascript:void(0)" class="easyui-linkbutton addll" data-options="iconCls:'icon-add',disabled:true">新增</a>
                <a href="javascript:void(0)" class="easyui-linkbutton editll" data-options="iconCls:'icon-edit',disabled:true">修改</a>
                <a href="javascript:void(0)" class="easyui-linkbutton delll" data-options="iconCls:'icon-no',disabled:true">删除</a>
                <input type="hidden" class="level" value="2" />
                <input type="hidden" class="pid" />
            </div>
            <select multiple="multiple"></select>
        </div>
        <div class="multselect pagelevel">
            <div class="text-left mg-bottom10"><span></span>页面权限：</div>
            <div class="operate">
                <a href="javascript:void(0)" class="easyui-linkbutton addpl" data-options="iconCls:'icon-add',disabled:true">新增</a>
                <a href="javascript:void(0)" class="easyui-linkbutton editpl" data-options="iconCls:'icon-edit',disabled:true">修改</a>
                <a href="javascript:void(0)" class="easyui-linkbutton delpl" data-options="iconCls:'icon-no',disabled:true">删除</a>
                <input type="hidden" class="level" value="3" />
                <input type="hidden" class="pid" />
            </div>
            <select multiple="multiple"></select>
        </div>
    </div>
</div>
</div>
<div id="edit" class="editbasicse">
    <form class="easyuiform" method="post">
        <table>
            <tr>
                <td>权限名称：</td>
                <td><input class="textbox" data-options="required:true" name="func_name" maxlength="30" placeholder="请输入权限名称"></td>
            </tr>
            <tr>
                <td></td>
                <td><label><input type="radio" name="enable" checked="checked" value="1" />启用</label><label><input type="radio" name="enable" value="0" />停用</label></td>
            </tr>
            <tr>
                <td>说明：</td>
                <td><textarea class="custextarea com-width200" data-options="required:true" name="func_comment" maxlength="30" placeholder="请输入客户备注"></textarea></td>
            </tr>
        </table>
        <input type="hidden" name="id" />
        <input type="hidden" name="level" />
        <input type="hidden" name="pid" />
    </form>
    <table id="container" style="display:none;">
        <tr class="linkaddress potype">
            <td>链接地址：</td>
            <td><input class="textbox" data-options="required:true,validType:'url'" name="url" maxlength="200" placeholder="请输入链接地址"></td>
        </tr>
        <tr class="potype dropdown">
            <td>权限类别：</td>
            <td>
                <select class="roletype com-width100" name="is_button" id="is_button">
                    <option value="1">按钮</option>
                    <option value="0">选项卡</option>
                    <option value="2">接口</option>
                </select>
            </td>
        </tr>
        <tr class="linkaddress">
            <td>图标：</td>
            <td><input class="textbox" data-options="required:true,validType:'en'" name="style" maxlength="30" placeholder="请输入图标Class"></td>
        </tr>
        <tr class="linkaddress">
            <td>是否显示：</td>
            <td><label><input type="radio" name="is_show" checked="checked" value="1" />是</label><label><input type="radio" name="is_show" value="0" />否</label></td>
        </tr>
    </table>
</div>
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/systemanage/authorityconfig");
</script>
</body>
</html>
