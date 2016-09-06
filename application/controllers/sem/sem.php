<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sem extends App_Controller {

	public function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		$this->load->model('business/business_model', 'business');
		$this->load->model('sem/sem_model', 'sm');

		list($source, $source_explan) = $this->business->getBusinessSource();	
		list($ordertype, $ordertype_explan) = $this->business->getBusinessType();
		list($status, $status_explan) = $this->business->getBusinessStatus();

		$this->_data['source'] = $source;
		$this->_data['source_explan'] = $source_explan;
		$this->_data['ordertype'] = $ordertype;
		$this->_data['ordertype_explan'] = $ordertype_explan;
		$this->_data['status'] = $status;
		$this->_data['status_explan'] = $status_explan;
		$this->_data['channel'] = $this->sm->getChannel();

		$this->load->view('sem/index', $this->_data);
	}

	private function _setdata($data)
	{
		$this->load->model('business/business_model', 'bbm');
		$this->load->model('sem/sem_model', 'sm');

		// 状态处理
		list($source, $source_explan) = $this->bbm->getBusinessSource();
		list($ordertype, $ordertype_explan) = $this->bbm->getBusinessType();
		list($status, $status_explan) = $this->bbm->getBusinessStatus();

		$rso = array_flip($source);
		$ror = array_flip($ordertype);
		$rst = array_flip($status);

		$list = array();

		foreach ($data as $key => $value)
		{
			$list[$key]['id'] = $value['id'];
			$list[$key]['mobile'] = $value['mobile'];
			$list[$key]['add_date'] = date('Y-m-d H:i:s', $value['createtime']);
			$list[$key]['hmsr'] = $value['hmsr'];
			$list[$key]['source_url'] = $this->sm->getPageName($value['source_url']);
			$list[$key]['source'] = isset($rso[$value['source']]) ? $source_explan[$rso[$value['source']]] : '';
			$list[$key]['status'] = isset($rst[$value['status']]) ? $status_explan[$rst[$value['status']]] : '';
		}
		return $list;
	}

	/**
	 *列表
	 */
	public function semlist($export = false)
	{
		$this->load->model('sem/sem_model', 'sm');
		$params = $this->input->get();
		empty($params) && $params = array();

		// 分页处理
		$limit = array();
		if(!empty($params['pagesize']) && !empty($params['page']))
        {
            if(is_numeric($params['pagesize']) && is_numeric($params['page']))
            {

                $start = ($params['page'] - 1) * $params['pagesize'];
                $limit = array('nums' => $params['pagesize'], 'start' => $start);
            }
        }

        // 获取记录
		$total = $this->sm->getOne($params, array('count(id) AS num'));
		$list = $this->sm->getAll($params, $limit, array('id' => 'desc'), 'id, source, mobile, createtime, status, hmsr, source_url');

		$data = $this->_setdata($list);

		if($export)
		{
			return array('total' => $total['num'], 'rows' => $data);
		}
		return success(array('total' => $total['num'], 'rows' => $data));
	}

	/**
	 * 导出
	 */
	public function export()
	{
		$this->load->helper('excel_tools');
		$exporter = new ExportDataExcel('browser', date('Y-m-d') . '_sem.xls');
		$exporter->initialize();
		$exporter->addRow(array(
			'ID', '商机来源', '手机号', '添加日期', '状态', '推广标识'	
		));

		$result = $this->semlist(true);

		$params = $this->input->get();

		$pagesize = !empty($params['pagesize']) ? $params['pagesize'] : 50;
		$page = ceil($result['total'] / $pagesize);

		for($i = 1; $i <= $page; $i++)
		{
			$_GET['pagesize'] = $pagesize;
			$_GET['page'] = $i;
			$business = $this->semlist(true);
			foreach ($business['rows'] as $b)
			{
				$exporter->addRow(array(
					$b['id'], $b['source'], $b['mobile'], $b['add_date'], $b['status'], $b['hmsr']
				));
			}
		}
		$exporter->finalize(); exit();
	}
}