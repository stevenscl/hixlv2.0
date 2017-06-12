<?php
namespace app\admin\controller;

use think\Controller;

class Members extends Controller
{
    private $obj;
    public function _initialize()
    {
        $this->obj = model('member');
    }
    public function member()
    {
        $count  = $this->obj->countdata();
        $member = $this->obj->getMember();
        for ($x = 0; $x < intval($count); $x++) {
            $member[$x]['registered_name'] = $this->obj->get(intval($member[$x]['registered_id']))->registeredinfo->registered_name;
            $member[$x]['create_time']     = $this->obj->get(intval($member[$x]['registered_id']))->registeredinfo->create_time;
            $member[$x]['root_name']       = empty($this->obj->get(intval($member[$x]['registered_id']))->rootinfo->root_name) ? null : $this->obj->get(intval($member[$x]['registered_id']))->rootinfo->root_name;
            $member[$x]['community_name']  = empty($this->obj->get(intval($member[$x]['registered_id']))->communityinfo->community_name) ? null : $this->obj->get(intval($member[$x]['registered_id']))->communityinfo->community_name;
        }
        return $this->fetch('', [
            'member' => $member,
            'count'  => $count,
        ]);
    }
    public function edit($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $member = $this->obj->get($id);
        return $this->fetch('', [
            'member' => $member,
        ]);
    }
    public function update()
    {
        if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data = input('post.');
        // $validate = validate('Registered');
        // if (!$validate->check($data)) {
        //     $this->error($validate->getError());
        // }
        $res                  = $this->obj->save($data, ['registered_id' => intval($data['registered_id'])]);
        $data['member_birth'] = $this->obj->get(intval($data['registered_id']))->member_birth;
        if ($res) {
            session('Member', $data, 'index');
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
        $data['member_status'] = -1;
        $res                   = $this->obj->save($data, ['registered_id' => intval($id)]);
        if ($res) {
            $this->success('已加入删除列表');
        } else {
            $this->error('删除失败');
        }
    }
    public function memberdel()
    {
        $count  = $this->obj->countdeldata();
        $member = $this->obj->getMemberdel();
        for ($x = 0; $x < intval($count); $x++) {
            $member[$x]['registered_name'] = $this->obj->get(intval($member[$x]['registered_id']))->registeredinfo->registered_name;
            $member[$x]['create_time']     = $this->obj->get(intval($member[$x]['registered_id']))->registeredinfo->create_time;
            $member[$x]['root_name']       = empty($this->obj->get(intval($member[$x]['registered_id']))->rootinfo->root_name) ? null : $this->obj->get(intval($member[$x]['registered_id']))->rootinfo->root_name;
            $member[$x]['community_name']  = empty($this->obj->get(intval($member[$x]['registered_id']))->communityinfo->community_name) ? null : $this->obj->get(intval($member[$x]['registered_id']))->communityinfo->community_name;
        }
        return $this->fetch('', [
            'member' => $member,
            'count'  => $count,
        ]);
    }
    public function status($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $data = [
            'registered_id' => $id,
        ];
        $res = $this->obj->where($data)->update(['member_status' => '0']);
        if ($res) {
            $this->success('已还原');
        } else {
            $this->error('还原失败');
        }
    }
    public function delmember($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $data = [
            'registered_id' => $id,
        ];
        $del = $this->obj->get($id);

        $res = $this->obj->where($data)->delete();
        $del->registeredinfo->delete();
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    // 以下移植
    public function eachmember()
    {
        $account   = session('registered', '', 'admin');
        $community = model('member')->get(intval($account->registered_id));
        $thisregistered_id = $account->registered_id;
        $join      = model('join')->where(['community_id' => $community->community_id, 'join_status' => 1])->column('registered_id');
        $member    = model('member')->where('registered_id', 'IN', $join)->select();
        $count     = model('join')->where(['community_id' => $community->community_id, 'join_status' => 1])->count();
        for ($x = 0; $x < $count; $x++) {
            $member[$x]['registered_name'] = $this->obj->get(intval($member[$x]['registered_id']))->registeredinfo->registered_name;
            $member[$x]['create_time']     = $this->obj->get(intval($member[$x]['registered_id']))->registeredinfo->create_time;
            $member[$x]['root_name']       = empty($this->obj->get(intval($member[$x]['registered_id']))->rootinfo->root_name) ? null : $this->obj->get(intval($member[$x]['registered_id']))->rootinfo->root_name;
            $member[$x]['community_name']  = model('community')->get($community->community_id)->community_name;
            $member[$x]['community_id']    = $community->community_id;
        }

        return $this->fetch('', [
            'member' => $member,
            'count'  => $count,
            'thisregistered_id'  => $thisregistered_id,
        ]);
    }
    public function eachmemberdel()
    {
        $account   = session('registered', '', 'admin');
        $community = model('member')->get(intval($account->registered_id));
        $join      = model('join')->where(['community_id' => $community->community_id, 'join_status' => -1])->column('registered_id');
        $member    = model('member')->where('registered_id', 'IN', $join)->select();
        $count     = model('join')->where(['community_id' => $community->community_id, 'join_status' => -1])->count();
        for ($x = 0; $x < $count; $x++) {
            $member[$x]['registered_name'] = $this->obj->get(intval($member[$x]['registered_id']))->registeredinfo->registered_name;
            $member[$x]['create_time']     = $this->obj->get(intval($member[$x]['registered_id']))->registeredinfo->create_time;
            $member[$x]['root_name']       = empty($this->obj->get(intval($member[$x]['registered_id']))->rootinfo->root_name) ? null : $this->obj->get(intval($member[$x]['registered_id']))->rootinfo->root_name;
            $member[$x]['community_name']  = model('community')->get($community->community_id)->community_name;
        }
        return $this->fetch('', [
            'member' => $member,
            'count'  => $count,
        ]);
    }
    public function eachdel($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $account   = session('registered', '', 'admin');
        $community = model('member')->get(intval($account->registered_id));
        // $joinstatus=model('join')->getstatus($id,$community->community_id);
        $root = model('member')->get($id)->root_id;
        $res  = model('join')->where(['community_id' => $community->community_id, 'registered_id' => $id])->update(['join_status' => -1]);
        if ($root == 1) {
            $count = model('join')->where(['registered_id' => $id, 'join_status' => 1])->count();
            // $joinstatus['join_status'] = -1;
            if ($count == 0) {
                $data['root_id'] = null;
                $this->obj->save($data, ['registered_id' => intval($id)]);
            }
        }
        if ($res) {
            $this->success('已删除');
        } else {
            $this->error('删除失败');
        }
    }
    public function reeachdel($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $account   = session('registered', '', 'admin');
        $community = model('member')->get(intval($account->registered_id));
        // $joinstatus=model('join')->getstatus($id,$community->community_id);
        $root = model('member')->get($id)->root_id;
        $res = model('join')->where(['community_id' => $community->community_id, 'registered_id' => $id])->update(['join_status' => 1]);

        // $joinstatus['join_status'] = -1;
        if (is_null($root)) {
            $data['root_id'] = 1;
        $this->obj->save($data, ['registered_id' => intval($id)]);
        }
        

        if ($res) {
            $this->success('已撤销');
        } else {
            $this->error('撤销失败');
        }
    }
}
