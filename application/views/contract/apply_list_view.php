 <!-- 右侧主体开始-->
    <div class="center-div">
        <div class="center-div-header">当前位置：【 合同处理  >  商家回款申请 】</div>
        <div class="customrecordiv comct">
            <form id="refundApplyForm" class="easyui-form" method="get" data-options="novalidate:true">
                <table class="systemlogtb">
                    <tbody>
                    <tr>
                        <td class="tbfield">款项状态：</td>
                        <td>
                            <select id="balance_status" name="refund_status" class="selectbox com-width150">
                                <option value="">全部</option>
                                <?php foreach($refund_status as $key => $val):?>
                                <option value="<?php echo $val;?>"><?php echo $refund_status_explan[$key];?></option>
                                <?php endforeach;?>
                            </select>
                        </td>
                        <td class="tbfield">运营：</td>
                        <td>
                            <select id="balance_status" name="operator_uid" class="selectbox com-width150">
                                <option value="">全部</option>
                                <?php foreach($operater as $val):?>
                                    <option value="<?php echo $val['id'];?>"><?php echo $val['username'];?></option>
                                <?php endforeach;?>
                            </select>
                        </td>
                        <td class="tbfield">婚礼日期：</td>
                        <td><input id="create_time_start" name="wed_date_start" class="datebox com-width150"></td>
                        <td class="singlefw">至</td>
                        <td><input id="create_time_end" name="wed_date_end" class="datebox com-width150"></td>
                        <td class="tbfield">商家昵称：</td>
                        <td>
                            <input id="nickname" class="textbox condition_text com-width150" name="shop_name">
                        </td>
                    </tr>
                    <tr>
                        <td class="tbfield">合同编号：</td>
                        <td>
                            <input id="contractno" class="textbox condition_text com-width150" name="contract_num">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10">
                            <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c7" data-options="iconCls:'icon-search'">查询</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <input type="hidden" name="shopper_alias" id="filter">
            </form>

            <table id="refundapplylist" class="datagrid"></table>
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
                <td colspan="2" class="pb10"><p></p></td>
            </tr>
            <tr>
                <td class="vert-top tl">驳回内容：</td>
                <td>
                    <textarea id="rejectText" class="custextarea com-width300" style="height: 70px;" data-options="required:true" name="reason" maxlength="200" placeholder="请输入驳回原因"></textarea>
                </td>
            </tr>
        </table>
    </form>
</div>

 <!--确认回款比例弹层-->
 <div id="refund-confirm-layer" class="examinedlg">
     <form class="easyuiform" id="refund_confirm_form" method="post" data-options="novalidate:true">
         <table class="bsnstb">
             <tr>
                 <td colspan="3" class="pb10"><span></span></td>
             </tr>
             <tr>
                 <td class="vert-top tl">婚礼前回款金额：</td>
                 <td>
                     <input id="brefore_amount" class="textbox condition_text com-width150" name="before_amount" data-options="required:true">
                 </td>
                 <td>
                     <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-sum'">自动计算80%</a>
                 </td>
             </tr>
             <tr>
                 <td class="vert-top tl">婚礼后回款金额：</td>
                 <td>
                     <input id="after_amount" class="textbox condition_text com-width150" name="after_amount" data-options="required:true">
                 </td>
                 <td></td>
             </tr>
         </table>
     </form>
 </div>

 <!--通知财务-->
 <div id="notify-finance-layer" class="editbsns">
     <div></div>
     <table id="apply-detail-list" class="datagrid"></table>
 </div>

 <input type="hidden" id="cur_page" value="<?php echo isset($page) ? $page : 1; ?>" />
 <input type="hidden" id="cur_pagesize" value="<?php echo isset($pagesize) ? $pagesize : 20; ?>" />

<?php $this->load->view('header/footer_view.php');?>

<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/contract/refundapply");
</script>
</body>
</html>