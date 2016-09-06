<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model 基础设置表
 */

class Sys_trademarksetting_model extends MY_Model{

    /**
     * 数据库表名
     * @var string
     */
	const TBL = 'erp_sys_trademarksetting';

    //启用状态
    const ENABLE_ON = 1;

    //关闭状态
    const ENABLE_OFF = 2;


    /**
     * 根据setting_id获取基本设置信息
     * @return array
     */
    public function getAllInfosByMark_id($mark_id)
    {
        $this->erp_conn->where('mark_id', $mark_id);
        $query = $this->erp_conn->where('enable',self::ENABLE_ON)->get(self::TBL)->result_array();
        $result = (! empty($query)) ? $query : array();
        return $result;
    }

    /**
     * 根据id获取数据
     */
    public function getInfoById($id)
    {
        $result = $this->erp_conn->where('id', $id)->get(self::TBL)->row_array();
        return (! empty($result)) ? $result : array();
    }
    /**
     * 添加基本设置信息记录
     */
    public function add($data)
    {
        if(empty($data))
        {
            return failure('参数错误');
        }
        $result = $this->erp_conn->insert(self::TBL, $data);
        return $result ? "添加成功" : "添加失败";
    }

    /**
     * 编辑
     */
    public function edit($id, $data)
    {
        $result = $this->erp_conn->where('id', $id)->update(self::TBL, $data);
        return $result ? "修改成功" : "修改失败";
    }

    /**
     * 删除
     */
    public function delById($id)
    {
        $result = $this->erp_conn->where('id', $id)->delete(self::TBL);
        return $result ? "删除成功" : "删除失败";
    }

    /**
     * 启用操作
     * param $id
     */
    public function OpenButton($id)
    {
        return $this->erp_conn->update(self::TBL, array('enable' => self::ENABLE_ON), array('id' => $id));

    }

    /**
     * 禁用操作
     * param $id
     */
    public function OffButton($id)
    {
        return $this->erp_conn->update(self::TBL, array('enable' => self::ENABLE_OFF), array('id' => $id));
    }

    /**
     * 分页获取数据
     * @param $id
     * @param $pagesize
     * @param int $page
     * @return array
     */
    public function getPageList($id, $pagesize, $page = 1, $keywords = '')
    {
        $offset = '';

        if($page == 1 || $page < 1 )
        {
            $offset = 1;
        }

        $offset = ($page - 1)*$pagesize;

        $where = array();
        $like = array();
        $where['mark_id']= $id;
        if($keywords != '')
        {
            $like['name'] = $keywords;
        }

        if($offset == 1)
        {
            $this->erp_conn->offset($offset);
        }

        $result = $this->erp_conn->select('trade.*, color.color_name')->from(self::TBL." as trade")->join('ew_erp_colors as color', 'color.id = trade.color')->where($where)->like($like)->limit($pagesize)->get()->result_array();
        $result = (! empty($result)) ? $result : array();

        $total = $this->erp_conn->select('trade.*, color.color_name')->from(self::TBL." as trade")->join('ew_erp_colors as color', 'color.id = trade.color')->where($where)->like($like)->count_all_results();

        $infos = array(
            'total' => $total,
            'rows' => $result,
        );
        return $infos;
    }
}