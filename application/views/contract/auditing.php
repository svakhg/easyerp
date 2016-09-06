 <!-- 右侧主体开始-->
    <div class="center-div">
        <div class="center-div-header">当前位置：【 合同处理  >  合同审核 】</div>
        <div class="customrecordiv comct">
            <form id="demand_search" class="easyui-form" method="get" data-options="novalidate:true">
                <table class="systemlogtb">
                    <tbody>
                    <tr>
                        <td class="tbfield">合同状态：</td>
                        <td>
                            <select id="con_type" name="contract_status" class="selectbox com-width150">
                                <option value="">全部</option>
                                <?php foreach ($contractstatus as $key => $val) : ?>
								<option value="<?php echo $val;?>" <?php echo $val == $contractstatus['to_confirm'] ? 'selected' : '' ?> ><?php echo $contractstatus_explan[$key];?></option>
								<?php endforeach;?>
                            </select>
                        </td>


                        <td class="tbfield">提交时间：</td>
                        <td><input id="upload_time_start" name="upload_time_start" class="datetimebox com-width150"></td>
                        <td class="singlefw">至</td>
                        <td><input id="upload_time_end" name="upload_time_end" class="datetimebox com-width150"></td>

                        <td class="tbfield">运营：</td>
                        <td>
                            <select id="operate_uid" name="operate_uid" class="selectbox com-width150 c_consultant">
                                <option value="">全部</option>
                                <?php foreach ($operater as $key => $value): ?>
                                    <option value="<?php echo $value['id']; ?>"><?php echo $value['username']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>


                    </tr>
                    <tr>
                        <td class="tbfield">商家昵称：</td>
                        <td>
                            <input id="nickname" class="textbox condition_text com-width150" name="shopper_name">
                        </td>

                        <td class="tbfield">条件选项：</td>
                        <td colspan="3">
                            <select id="options1" class="selectbox com-width100">
								 <option value="">--请选择--</option>
                                <option selected value="contract_num">合同编号</option>
                                <option value="tradeno">交易编号</option>
                            </select>
                            <input class="textbox condition_text com-width150" id="options2">
                        </td>

                    </tr>
                    <input id="con_type" type="hidden" class="textbox condition_text com-width150" name="type" value="1">
                    <tr>
                        <td colspan="10">
                            <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c7" data-options="iconCls:'icon-search'">查询</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton c7 auth-none" data-options="iconCls:'icon-redo'" id="exportBtn" data-auth="api/contract/exportAudit">导 出</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <input type="hidden" name="shopper_alias" id="filter">
            </form>

            <table id="contractlist" class="datagrid"></table>
        </div>
    </div>
    <!-- 右侧主体结束-->
</div>

<!--各种弹窗
    通过JS调用.
-->

<!-- 添加收支记录的弹层 -->
<div id="distlayer" class="recommend">
    <form class="easyuiform" method="post">
        <table>
            <tr>
                <td class="tbfield">跟进顾问：</td>
                <td>
                    <select class="selectbox com-width150" name="dist_consultant" id="dist_consultant" data-options="required:true">
                        <option value="">--请选择--</option>
                        <option value='定金'>定金</option>
                        <option value='首款'>首款</option>
                        <option value='尾款'>尾款</option>
                    </select>
                </td>
            </tr>
        </table>
    </form>
</div>
<!-- 添加收支记录的弹层 -->

<!--驳回弹层-->
<div id="rejectlayer" class="examinedlg">
    <form class="easyuiform" id="add_memo_form" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td colspan="2" class="pb10" style="color: red;font-weight: bold">提示：驳回原因商家可见！</td>
            </tr>
            <tr>
                <td class="vert-top tl">驳回内容：</td>
                <td>
                    <textarea id="rejectText" class="custextarea com-width300" style="height: 70px;" data-options="required:true" name="memo_text" maxlength="500" placeholder="请输入驳回原因"></textarea>
                </td>
            </tr>
        </table>
    </form>
</div>
<!--查看合同弹层-->
 <div id="check-contract" class="contract-confirm-dialog">
     <div><a href="javascript:void(0)" class="easyui-linkbutton check-contract-ok" data-options="iconCls:'icon-ok'" data-auth="api/contract/confirmContract">确认</a>
         <a href="javascript:void(0)" class="easyui-linkbutton check-contract-no" data-options="iconCls:'icon-no'" data-auth="api/contract/rejectContract">驳回</a>
     </div>
     <div class="tablebox">
         <table>
             <tr>
                 <td class="tbfield">合同编号：</td>
                 <td data-name="contract_num"></td>
                 <td class="tbfield">签约三方：</td>
                 <td data-name="type_text"></td>
             </tr>
             <tr>
                 <td class="tbfield">商家昵称：</td>
                 <td data-name="shopper_name"></td>
                 <td class="tbfield">新人名称：</td>
                 <td data-name="username"></td>
             </tr>
             <tr>
                 <td class="tbfield">婚礼日期：</td>
                 <td data-name="wed_date"></td>
                 <td class="tbfield">婚礼地点：</td>
                 <td><span data-name="wed_location"></span>-<span data-name="wed_place"></span></td>
             </tr>
             <tr>
                 <td class="tbfield">初始金额：</td>
                 <td data-name="wed_amount"></td>
             </tr>
         </table>
     </div>
     <div>
         <h3>合同图片：</h3>
         <p>(编号页)</p>
         <p><img data-name="number_img" src="" alt=""></p>
         <p>(签字页)</p>
         <p><img data-name="sign_img" src="" alt=""></p>
     </div>
 </div>



<input type="hidden" id="cur_page" value="1" />
<input type="hidden" id="cur_pagesize" value="20" />

<?php $this->load->view('header/footer_view.php');?>

<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/contract/auditing");
</script>
</body>
</html>