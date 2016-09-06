    <div class="center-div">
        <div class="center-div-header">当前位置：【 意向书管理 】</div>
        <div class="customrecordiv comct">
            <form id="demand_search" class="easyui-form" method="post" data-options="novalidate:true">
                <table class="systemlogtb">
                    <tr>
                        <td class="tbfield">提交时间：</td>
                        <td>
                            <input id="time_21_from" name="time_21_from" class="datetimebox time_21_from">
                        </td>
                        <td class="singlefw">至</td>
                        <td>
                            <input id="time_21_to" name="time_21_to" class="datetimebox time_21_to">
                        </td>
                        <td class="tbfield">关键字：</td>
                        <td colspan="3">
                            <input class="textbox keywords com-width300" name="keywords" style="margin-right:30px;" maxlength="50" placeholder="客户昵称/手机号码/店铺名称">
                        </td>
                    </tr>
                    <tr>
                        <td class="tbfield">审核时间：</td>
                        <td>
                            <input id="time_31_from" name="time_31_from" class="datetimebox time_31_from">
                        </td>
                        <td class="singlefw">至</td>
                        <td>
                            <input id="time_31_to" name="time_31_to" class="datetimebox time_31_to">
                        </td>
                        <td class="tbfield">需求类型：</td>
                        <td>
                        <select id="shopper_alias" name="shopper_alias" class="selectbox com-width150 shopper_alias">
                            <option value="">--请选择--</option>
                            <option value="wedplanners">找策划</option>
                            <option value="wedmaster">找主持</option>
                            <option value="wedphotoer">找摄影</option>
                            <option value="wedvideo">找摄像</option>
                            <option value="sitelayout">找场布</option>
                        </select>
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c8 pl10 pr10 ml10" data-options="iconCls:'icon-search'">查 询</a>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="letter_status" id="filter" value="dai" />
            </form>
            <a href="javascript:void(0)" class="easyui-linkbutton examinesure" data-options="iconCls:'icon-ok'">审核通过</a>
            <a href="javascript:void(0)" class="easyui-linkbutton examineno" data-options="iconCls:'icon-no'">审核不通过</a>
            <label style="display:inline-block;vertical-align:middle;height:26px;"></label>
            <div class="filtertb">
                <ul>
                    <li class="filtertb-on" id="dai">待审核(<?php echo $count['dai'];?>)</li>
                    <li class="filtertb-off" id="yes">审核通过(<?php echo $count['yes'];?>)</li>
                    <li class="filtertb-off" id="no">审核不通过(<?php echo $count['no'];?>)</li>
                </ul>
            </div>
            <table id="cusrecord" class="datagrid"></table>
        </div>
    </div>
</div>

<div class="editpanel">
    <form class="easyuiform mult" method="post">
        <table class="ltr" style="width:600px;min-height:140px;">
            <tr>
                <td colspan="4" class="pl10">
                    <a href="javascript:" class="easyui-linkbutton save c1 mb10 mr10 mt10 saveletter" data-options="iconCls:'icon-save'">保存</a> 
                    <a href="javascript:" class="easyui-linkbutton mb10 mr10 mt10 closepanel" data-options="iconCls:'icon-back'">关闭</a>
                </td>
            </tr>
            <tr>
                <td class="tbfield">商家名称：</td>
                <td>
                    <input class="textbox com-width150 nickname" name="nickname" disabled="disabled">
                </td>
                <td class="tbfield">提交时间：</td>
                <td>
                    <input class="textbox com-width150 time_21" name="time_21" disabled="disabled">
                </td>
            </tr>
            <tr>
                <td class="tbfield">需求类型：</td>
                <td>
                    <input class="textbox com-width150 shopper_alias" name="shopper_alias" disabled="disabled">
                </td>
                <td class="tbfield">意向书状态：</td>
                <td>
                    <input class="textbox com-width150 status" name="status" disabled="disabled">
                </td>
            </tr>
            <tr>
                <td class="tbfield">接单意愿：</td>
                <td>
                    <select id="wish" name="wish" class="selectbox com-width150 wish">
                        <option value="非常强烈">非常强烈</option>
                        <option value="希望争取">希望争取</option>
                        <option value="意愿一般">意愿一般</option>
                    </select>
                </td>
                <td class="tbfield">意向书编号：</td>
                <td>
                    <input class="textbox com-width150 id" name="id" readonly="readonly">
                </td>
            </tr>
            <tr>
                <td class="tbfield">易结店铺：</td>
                <td colspan="3">
                    <input class="textbox com-width150 studio_name" name="studio_name" disabled="disabled">
                </td>
            </tr>
        </table>
        <input type="hidden" name="recommend_letter_json" />
    </form>

    <table class="chcntet prices_p">
        <tbody>
            <tr>
                <td colspan="4">
                    推荐服务：
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <table class="prices">
                        <tr>
                            <th>服务类型</th>
                            <th>抢单价</th>
                            <th>服务报价</th>
                            <th>服务内容</th>
                        </tr>
                        <tr>
                            <td><input class="textbox service_type validatebox-text" style="width:80px;" name="service_type" value="婚纱照拍摄"></td>
                            <td><input class="textbox grabprice validatebox-text" style="width:40px;" name="grabprice" value="4500"></td>
                            <td><input class="textbox price validatebox-text" style="width:50px;" name="price" value="5000"></td>
                            <td>
                                <textarea class="custextarea service validatebox-text" style="width:360px;" name="service">多套服装可选,多种颜色,多种风格.....</textarea>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="width:70px;">附加说明：</td>
                <td colspan="3">
                    <textarea class="custextarea additional_remark validatebox-text" style="width:508px;" name="additional_remark"></textarea>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="chcntet customservice_p">
        <tbody>
            <tr>
                <td colspan="4">
                    定制服务：
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <table class="customservice" style="width:506px;">
                        <tr>
                            <th>服务报价</th>
                            <th>服务内容</th>
                        </tr>
                        <tr>
                            <td><input class="textbox price validatebox-text" style="width:50px;" name="price" value="5000"></td>
                            <td>
                                <textarea class="custextarea service validatebox-text" style="width:506px;" name="service">多套服装可选,多种颜色,多种风格.....</textarea>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="width:70px;">附加说明：</td>
                <td colspan="3">
                    <textarea class="custextarea additional_remark validatebox-text" style="width:508px;" name="additional_remark"></textarea>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="chcntet work_description_p">
        <tbody>
            <tr>
                <td style="width:70px;">合作说明：</td>
                <td colspan="3">
                    <textarea class="custextarea work_description validatebox-text" style="width:508px;" name="work_description"></textarea>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
       seajs.use("<?php echo $config['srcPath'];?>/js/bsnsbidmanage/intentexamine");
</script>
</body>
</html>
