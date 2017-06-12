<?php
namespace app\admin\controller;

use think\Controller;

class Registered extends Controller
{
    private $obj;
    public function _initialize()
    {
        $this->obj = model('registeredinfo');
    }
    public function account()
    {
        $registered = $this->obj->getRegistered();
        $count      = $this->obj->countdata();
        return $this->fetch('', [
            'registered' => $registered,
            'count'      => $count,
        ]);
    }
    public function add()
    {
        return $this->fetch();
    }
    public function save()
    {
        if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data     = input('post.');
        $validate = validate('Registered');
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }
        $res = $this->obj->add($data);
        if ($res) {
            $member['registered_id'] = $data['registered_id'];
            $this->obj->member()->save($member);
            $this->success('新增成功');
        } else {
            $this->error('新增失败');
        }
    }
    public function edit($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $registered = $this->obj->get($id);
        return $this->fetch('', [
            'registered' => $registered,
        ]);
    }
    public function update()
    {
        if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data     = input('post.');
        $validate = validate('Registered');
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }
        $res = $this->obj->save($data, ['registered_id' => intval($data['registered_id'])]);
        if ($res) {
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }
    }
    public function del($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $data = [
            'registered_id' => $id,
        ];
        $del = $this->obj->get($id);
        $del->member->delete();
        $res = $this->obj->where($data)->delete();
        if ($res) {

            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
    public function delregister($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $data = [
            'registered_id' => $id,
        ];
        $res = $this->obj->where($data)->delete();
        if ($res) {

            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
    public function register()
    {
        $register = $this->obj->getRegister();
        $count    = $this->obj->countregister();
        return $this->fetch('', [
            'registered' => $register,
            'count'      => $count,
        ]);
    }
    public function status($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $data = [
            'registered_id' => intval($id),
        ];
        $res = $this->obj->where($data)->update(['registered_status' => '1']);
        
        if ($res) {
            model('member')->save($data);
            $data = model('registeredinfo')->get($id);
            $url     = request()->domain() . url('index/index/login');
            $title   = "小联2.0帐号申请审核通知";
            $content = "你好，" . $data['registered_name'] . "，您提交的帐号申请已通过审核，您可以点击链接<a href='" . $url . "' target='_blank'>登入</a>";
            \Email::send($data['registered_email'], $title, $content);
            $this->success('已成功批准');
        } else {
            $this->error('批准失败');
        }
    }
}
