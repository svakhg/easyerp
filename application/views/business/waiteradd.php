<div class="center-div">
    <div class="center-div-header">当前位置：【 商机管理  >  客服商机录入 】</div>
    <div class="customrecordiv comct">

        <div class="tabcont">
            <form class="easyui-form" method="post" id="customer_form"  data-options="novalidate:true">
                <table class="label-tdstyle">
                    <tr>
                        <td class="tbfield">商机来源：</td>
                        <td colspan="5">
                            <select id="source" data-options="required:true" name="source" class="selectbox com-width150 source">
                                <option value="">--请选择--</option>
                                <?php foreach($bsource as $status => $desc):?>
                                    <option value="<?php echo $status?>"><?php echo $desc;?></option>
                                <?php endforeach;?>
                            </select>
                            <input id="source_note" data-options="required:true" maxlength="30" name="source_note" class="textbox source_note">
                        </td>
                    </tr>
                    <tr>
                        <td class="tbfield">客户姓名：</td>
                        <td colspan="5">
                            <input id="username" name="username" class="textbox username">
                        </td>
                    </tr>
                    <tr>
                        <td class="tbfield">客户身份：</td>
                        <td colspan="5">
                            <select id="identify" name="identify" class="selectbox com-width150 identify">
                                <option value="">--请选择--</option>
                                <?php foreach($customer as $key => $status):?>
                                    <option value="<?php echo $status;?>"><?php echo $customer_explan[$key];?></option>
                                <?php endforeach;?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="tbfield">客户手机：</td>
                        <td colspan="5">
                            <input id="mobile" data-options="validType:'mobile'" name="mobile" class="textbox mobile">
                        </td>
                    </tr>
                    <tr>
                        <td class="tbfield">客户电话：</td>
                        <td>
                            <input id="tel" name="tel" class="textbox tel">
                        </td>
                        <td class="tbfield">微信：</td>
                        <td>
                            <input id="weixin" name="weixin" class="textbox weixin">
                        </td>
                        <td class="tbfield">QQ：</td>
                        <td>
                            <input id="qq" name="qq" class="textbox qq">
                        </td>
                    </tr>
                    <tr>
                        <td class="tbfield">备注：</td>
                        <td colspan="5">
                            <textarea class="custextarea com-width300 validatebox-text" name="note" maxlength="500" placeholder="请输入客户备注"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="tbfield">测试数据：</td>
                        <td colspan="5">
                            <select name="is_test">
                                <option value="0">否</option>
                                <option value="1">是</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="tbfield">&nbsp;</td>
                        <td colspan="5">
                            <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c8 pl10 pr10" data-options="iconCls:'icon-save'">保存</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
                        </td>
                    </tr>

                </table>
                <input type="hidden" name="shopper_alias" id="filter">
            </form>


        </div>



    </div>
</div>
</div>

<input type="hidden" id="cur_page" value="<?php echo isset($page) ? $page : 1; ?>" />
<input type="hidden" id="cur_pagesize" value="<?php echo isset($pagesize) ? $pagesize : 10; ?>" />
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/business/customer");
</script>
</body>
</html>