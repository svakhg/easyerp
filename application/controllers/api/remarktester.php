<?php
    /**
     * Created by PhpStorm.
     * User: jackwang
     * Date: 15/11/30
     * Time: 上午11:39
     */
    class Remarktester extends Base_Controller
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function index()
        {
            // 给商机表添加测试字段
            $this->_alterTable('business');

            // 给商家表单添加测试字段
            $this->_alterTable('business_shop_map');

            // 给签约表添加测试字段
            $this->_alterTable('sign_contract');

            // 给签约中间合同表添加测试字段
            $this->_alterTable('sign_contract_ext');

            // 给支付信息添加测试字段
            $this->_alterTable('sign_contract_payment_details');
        }

        public function _alterTable($table_name)
        {
            $query = $this->erp_conn->query('SHOW FULL COLUMNS FROM `ew_'.$table_name.'`');
            $exist_fields = array();
            foreach($query->result() as $row)
            {
                $exist_fields[] = $row->Field;
            }
            if(!in_array('is_test', $exist_fields))
            {
                echo '给表（'.$table_name.'）插入测试字段：';
                $this->erp_conn->query('ALTER TABLE `ew_'.$table_name.'` ADD `is_test` tinyint(4) NOT NULL DEFAULT 0 COMMENT \'是否是测试数据（1：是，0：不是）\'');
                echo $this->erp_conn->affected_rows();

                echo '<br /><br />';
            }
        }
    }