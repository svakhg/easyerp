<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


    /*
     * 跑数据脚本
     */

    class RunData extends Base_Controller
    {
        private $exec = 0;
        public function __construct()
        {
            parent::__construct();
            $get_params = $this->input->get();
            $allow = $this->_ag($get_params, 'allow');
            $exec = $this->_ag($get_params, 'exec', 0);
            $this->exec = $exec == 1 ? 1 : 0;
            if($allow != 'ye$')
            {
                echo '无权限';
                exit();
            }
        }

        public function handleContract()
        {
            $this->load->model('contract/contract_model', 'contract');
            $this->load->model('contract/contract_payment_details_model', 'payment');

            // 合同审核通过, 但是未付款的
            $contract_list = $this->erp_conn->where('contract_status IN (20, 30)')->where('funds_status <', 10)->select(array('id', 'contract_status', 'funds_status', 'contract_num'))->get('ew_sign_contract')->result_array();
            $this->_out($this->erp_conn->last_query(), '合同查询SQL');
            $this->_out($contract_list, '获取的合同列表');

            if(!empty($contract_list))
            {
                $contract_ids = array();
                foreach($contract_list as $contract)
                {
                    $contract_ids[] = $contract['id'];
                }
            }

            $get_payment_details = $this->erp_conn->where_in('cid', $contract_ids)->where('status', 5)->select(array('cid', 'status', 'fund_type'))->get('ew_sign_contract_payment_details')->result_array();
            $this->_out($this->erp_conn->last_query(), '支付查询SQL');
            $this->_out($get_payment_details, '支付信息');

            $payment_details = array();
            if(!empty($get_payment_details))
            {
                foreach($get_payment_details as $p)
                {
                    $payment_details[$p['cid']][] = $p;
                }
            }
            $update_contract_ids = array();
            if(!empty($payment_details) && !empty($contract_list))
            {
                foreach($contract_list as $contract)
                {
                    if(array_key_exists($contract['id'], $payment_details))
                    {
                        $update_contract_ids[] = $contract['id'];
                    }
                }
            }
            $this->_out($update_contract_ids, '要修改资金状态的合同ID');
            if(!empty($update_contract_ids) && $this->exec == 1)
            {
                $this->erp_conn->where_in('id', $update_contract_ids);
                 $this->erp_conn->update('ew_sign_contract', array('funds_status' => 10));
            }
            $this->_out($this->erp_conn->last_query(), '上一条sql');
            $this->_out('数据完成<br /><br /<br /');

        }

        private function _out($content, $header = '')
        {
            if(is_string($header) && !empty($header))
            {
                echo '<h2>'.$header.'</h2>';
            }
            if(!empty($content))
            {
                if(is_string($content))
                {
                    echo $content.'<br />';
                }
                else if(is_array($content))
                {
                    echo '<pre>'.print_r($content, true).'</pre>';
                }
                else
                {
                    var_dump($content);
                }
            }
        }


        private function _ag($array, $key, $default = '')
        {
            return isset($array[$key]) ? $array[$key] : $default;
        }
    }