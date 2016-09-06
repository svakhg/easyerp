<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model 员工表
 * author by Abel
 */

class Sys_user_model extends MY_Model{
    protected $_table = 'erp_sys_user';
    /**
     * 数据库表名
     * @var string
     */
	const TBL = 'erp_sys_user';

    //启用状态
    const STATUS_NORMAL = 1;

    //关闭状态
    const STATUS_STOP = 0;

    /**
     * 添加
     * return array
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
     *
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
     * 根据id获取数据
     */
    public function getInfoById($id)
    {
        $result = $this->erp_conn->where('id', $id)->get(self::TBL)->row_array();
        return (! empty($result)) ? $result : array();
    }

    /**
     * 根据username获取数据
     */
    public function getInfoByUserName($username)
    {
        $result = $this->erp_conn->where('username', $username)->get(self::TBL)->row_array();
        return (! empty($result)) ? $result : array();
    }

    /**
     * 根据num_code获取数据
     */
    public function getInfoByNumCode($num_code)
    {
        $result = $this->erp_conn->where('num_code', $num_code)->get(self::TBL)->row_array();
        return (! empty($result)) ? $result : array();
    }

    /**
     * 获取所有数据
     * @return array
     */
    public function getAllInfos()
    {
        $result = $this->erp_conn->get(self::TBL)->result_array();
        return (! empty($result))? $result : array();
    }

    /**
     * 启用操作
     * parameter $id
     */
    public function OpenButton($id)
    {
        return $this->erp_conn->update(self::TBL, array('status' => self::STATUS_NORMAL), array('id' => $id));
    }

    /**
     * 禁用操作
     * parameter $id
     */
    public function OffButton($id)
    {

        return $this->erp_conn->update(self::TBL, array('status' => self::STATUS_STOP), array('id' => $id));
    }

    /**
     * 分页获取数据
     * @param $id
     * @param $pagesize
     * @param int $page
     * @return array
     */
    public function getPageList($pagesize, $page = 1, $key = array('name' => '','code' => '','department' => '','status' => ''))
    {
        $offset = '';
        if($page == 1 || $page < 1 )
        {
            $offset = 1;
        }else{
            $offset = ($page -1)*$pagesize;
        }

        $like = array();
        $where = array();
        if($key['name']!= '')
        {
            $like['username'] = $key['name'];
        }

        if($key['code'] != '')
        {
            $like['num_code'] = $key['code'];
        }

        if(! empty($key['department']))
        {
            $where['department'] = $key['department'];
        }

        if(isset($key['status']) && $key['status'] != '')
        {
            $where['status'] = intval($key['status']);
        }
        if($offset != 1)
        {
            $this->erp_conn->offset($offset);
        }

        $result = $this->erp_conn->where($where)->like($like)->limit($pagesize)->get(self::TBL)->result_array();
        $result = (! empty($result)) ? $result : array();

        $total = $this->erp_conn->where($where)->like($like)->count_all_results(self::TBL);

        $infos = array(
            'total' => $total,
            'rows' => $result,
        );
        return $infos;
    }
	
	//获取用户权限
	public function getUserPermisssions($uid)
	{
		if(empty($uid))
		{
			return array();
		}
		//取用户的角色数组
		$user_info = $this->getInfoById($uid);
		if(!empty($user_info))
		{
			$role_arr = $user_info["role_id"] ? explode(",", $user_info["role_id"]) : array();
		}
		//取角色对应的权限
		$this->load->model("system/roles_model",'roles');
		$auth_arr = array();
		foreach ($role_arr as $key => $role)
		{
			//判断如果是超级管理员，获取全部权限id
			if($role == Roles_model::ADMIN_ROLE)
			{
				$list = $this->func->getAllInfos();
				foreach ($list as $auth_lst)
				{
					$auth_arr[] = $auth_lst["id"];
				}
			}else
			{
				$role_arr = $this->roles->getAuth($role);
				if($role_arr)
				{
					$auth_arr[]= $role_arr["func_id"];
				}
			}
		}
		$auth = implode(",", $auth_arr);
		//print_r($auth);
		//处理权限数组
		$auth_info = (!empty($auth)) ? explode(",", $auth) : array();
		$auth_info = array_unique($auth_info);

		return $auth_info;
	}

    /**
     * 获取通知消息人员分页数据
     * @param $id
     * @param $pagesize
     * @param int $page
     * @return array
     */
    public function getMessage_membersPageList($pagesize = 10, $page = 1, $key = array('name' => '','code' => '','department' => '','accept_message' => ''))
    {
        $offset = '';
        if($page == 1 || $page < 1 )
        {
            $offset = 1;
        }else{
            $offset = ($page -1)*$pagesize;
        }

        $like = array();
        $where = array();
        if($key['name']!= '')
        {
            $like['username'] = $key['name'];
        }

        if($key['code'] != '')
        {
            $like['num_code'] = $key['code'];
        }

        if(! empty($key['department']))
        {
            $where['department'] = $key['department'];
        }

        if(isset($key['accept_message']) && $key['accept_message'] != '')
        {
            $where['accept_message'] = intval($key['accept_message']);
        }
        if($offset != 1)
        {
            $this->erp_conn->offset($offset);
        }
        $result = $this->erp_conn->where($where)->like($like)->limit($pagesize)->get(self::TBL)->result_array();

        $result = (! empty($result)) ? $result : array();

        $total = $this->erp_conn->where($where)->like($like)->count_all_results(self::TBL);

        $infos = array(
            'total' => $total,
            'rows' => $result,
        );
        return $infos;
    }

    /**
     * 获取用户列表
     * @param array $conditions
     * @return array
     */
    public function findUsers($conditions = array())
    {
        $result = array();
        $query_data = $this->findAll($conditions , array() , array());
        foreach($query_data as $user)
        {
            $result[$user['id']] = $user;
        }
        return $result;
    }

    /**
     * 获取包含$role权限的所有用户
     * @param $role 权限id
     * @return array|void
     */
    public function getAllByRoleIds($role)
    {
        if(!$role)return;
        $result = array();

        $all_user = $this->getAllInfos();
        foreach($all_user as $v)
        {
            $tp_role = explode(',' , $v['role_id']);
            if(in_array($role , $tp_role))
            {
                $result[] = $v;
            }
        }

        return $result;
    }

}