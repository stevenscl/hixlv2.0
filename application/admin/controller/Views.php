<?php
namespace app\admin\controller;

use think\Controller;

class Views extends Controller
{
    public function index()
    {
        $views = model('msg')->where(['recive_uid' => 0, 'status' => 1])->order('create_time', 'desc')->select();
        $count = model('msg')->where(['recive_uid' => 0, 'status' => 1])->order('create_time', 'desc')->count();
        return $this->fetch('', [
            'count' => $count,
            'views' => $views,
        ]);
    }
    public function del()
    {
        $views = model('msg')->where(['recive_uid' => 0, 'status' => 0])->order('create_time', 'desc')->select();
        $count = model('msg')->where(['recive_uid' => 0, 'status' => 0])->order('create_time', 'desc')->count();
        return $this->fetch('', [
            'count' => $count,
            'views' => $views,
        ]);
    }
    public function delview($id = 0)
    {
    	if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $data['status'] = 0;
        $res                   = model('msg')->save($data, ['msg_id' => intval($id)]);
        if ($res) {
            $this->success('已加入删除列表');
        } else {
            $this->error('删除失败');
        }
    }
}
