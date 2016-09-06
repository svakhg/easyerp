<!-- 右侧主体开始-->
    <div class="center-div">
        <div class="customrecordiv comct">
            <form id="demand_search" class="easyui-form" method="get" data-options="novalidate:true">
                <table class="systemlogtb">
                    <tbody>
                    <tr>
                        <td class="tbfield">商机类型：</td>
                        <td>
                            <select id="ordertype" name="ordertype" class="selectbox com-width150 c_type">
                                <option value="">--请选择--</option>
                                <?php foreach ($ordertype as $key => $val): ?>
                                <option value="<?php echo $val; ?>"><?php echo $ordertype_explan[$key]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="tbfield">商机来源：</td>
                        <td>
                            <select id="source" name="source" class="selectbox com-width150 c_form">
                                <option value="">--请选择--</option>
                                <?php foreach ($source as $key => $val): ?>
                                <option value="<?php echo $val; ?>"><?php echo $source_explan[$key]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="tbfield">关键字：</td>
                        <td>
                            <input id="hmsr" name="hmsr" class="com-width100" value="" />
                        </td>
                        <td class="tbfield">提交页面：</td>
                        <td>
                            <input id="" name="source_url" class="com-width100" value="" />
                        </td>
                        <td class="tbfield">提交时间：</td>
                        <td><input id="add_date" name="add_date" class="datebox com-width150 creat_time_start"></td>
                    </tr>
                    <tr>
                        <td colspan="10">
                            <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c7" data-options="iconCls:'icon-search'">查询</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton c7" id="exportBtn">导 出</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <input type="hidden" name="shopper_alias" id="filter">
            </form>

            <table id="businesslist" class="datagrid"></table>
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


<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/business/sem-export");
</script>
</body>
</html>