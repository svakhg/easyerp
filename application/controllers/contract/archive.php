<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/30
 * Time: 15:34
 */
class Archive extends App_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('contract/contract_model', 'contract');
        $this->load->model('contract/contract_ext_model', 'contract_ext');
        $this->load->model('contract/contract_payment_details_model', 'payment');
        $this->load->model('sys_user_model', 'sum');
        $this->load->model('system/department_model', 'sdm');
        $this->load->helper('array');
    }

    /**
     * 合同归档列表
     */
    public function index()
    {
        list($archivestatus, $archivestatus_explan) = $this->contract->getArchiveStatus();
        list($contractstatus, $contractstatus_explan) = $this->contract->getContractStatus();
        // 运营人员
        $operater_department = $this->sdm->getOperater();
        $operater = $this->sum->findAll(array('department' => $operater_department['id'] , 'num_code <' => '99999000'), array(), array());

        $this->_data["archivestatus"] = $archivestatus;
        $this->_data["archivestatus_explan"] = $archivestatus_explan;
        $this->_data["contractstatus"] = $contractstatus;
        $this->_data["contractstatus_explan"] = $contractstatus_explan;
        $this->_data['operater'] = $operater;

        $this->load->view('contract/archive', $this->_data);
    }
}