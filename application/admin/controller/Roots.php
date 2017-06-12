<?php
namespace app\admin\controller;

use think\Controller;

class Roots extends Controller
{
    private $obj;
    public function _initialize()
    {
        $this->obj = model('Root');
    }
    public function role()
    {
        $role  = $this->obj->getRoot();
        $count = $this->obj->countdata();
        return $this->fetch('', [
            'role'  => $role,
            'count' => $count,
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
        $validate = validate('Root');
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }
        if (!empty($data['root_id'])) {
            return $this->update($data);
        }
        $res = $this->obj->add($data);
        if ($res) {
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
        $role = $this->obj->get($id);
        return $this->fetch('', [
            'role' => $role,
        ]);
    }
    public function update($data)
    {
        $res = $this->obj->save($data, ['root_id' => intval($data['root_id'])]);
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
            'root_id' => $id,
        ];
        $res = $this->obj->where($data)->delete();
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}
