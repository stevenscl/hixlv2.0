<?php
namespace app\admin\controller;

use think\Controller;

class Admins extends Controller
{
    private $obj;
    public function _initialize()
    {
        $this->obj = model('member');
    }
    public function admin()
    {
        $count = $this->obj->countadmin();
        $admin = $this->obj->getAdmin();
        for ($x = 0; $x < intval($count); $x++) {
            $admin[$x]['registered_name'] = $this->obj->get(intval($admin[$x]['registered_id']))->registeredinfo->registered_name;
            $admin[$x]['root_name']       = empty($this->obj->get(intval($admin[$x]['registered_id']))->rootinfo->root_name) ? null : $this->obj->get(intval($admin[$x]['registered_id']))->rootinfo->root_name;
            $admin[$x]['community_name']  = empty($this->obj->get(intval($admin[$x]['registered_id']))->communityinfo->community_name) ? null : $this->obj->get(intval($admin[$x]['registered_id']))->communityinfo->community_name;
        }
        return $this->fetch('', [
            'count' => $count,
            'admin' => $admin,
        ]);
    }
    public function add()
    {
        return $this->fetch();
    }
    public function grant()
    {
        if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data = input('post.');
        $user = model('registeredinfo')->get(intval($data['registered_id']))->registered_name;
        $rootname = model('root')->get(intval($data['root_id']))->root_name;
        $msg = [
        'uid'=>4,
        'recive_uid'=>$data['registered_id'],
        'msg_title'=>'系统消息',
        'msg_text'=>"恭喜，".$user."，您被任命为".$rootname."，您现在可以登录系统后台",
        ];

        // $validate = validate('Registered');
        // if (!$validate->check($data)) {
        //     $this->error($validate->getError());
        // }
        
        $registered_id = intval($data['registered_id']);
        $res           = $this->obj->where('registered_id', $registered_id)->update(['root_id' => intval($data['root_id'])]);
        if ($res) {
            model('msg')->sendmsg($msg);
            $this->success('授权成功');
        } else {
            $this->error('授权失败');
        }
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
    public function update($id = 0)
    {
        if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data = input('post.');
        $user = model('registeredinfo')->get(intval($data['registered_id']))->registered_name;
        $rootname = model('root')->get(intval($data['root_id']))->root_name;
        $msg = [
        'uid'=>4,
        'recive_uid'=>$data['registered_id'],
        'msg_title'=>'系统消息',
        'msg_text'=>"你好，".$user."，您被任命为".$rootname."，您现在可以登录系统后台",
        ];
        
        $res  = $this->obj->save($data, ['registered_id' => intval($data['registered_id'])]);
        if ($res) {
            model('msg')->sendmsg($msg);
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }
    public function del($id = 0)
    {
        $registered_id = intval($id);
        $user = model('registeredinfo')->get($registered_id)->registered_name;
        $msg = [
        'uid'=>4,
        'recive_uid'=>$registered_id,
        'msg_title'=>'系统消息',
        'msg_text'=>"你好，".$user."，您的权限已被收回",
        ];
       
        $res           = $this->obj->where('registered_id', $registered_id)->update(['root_id' => 1]);
        if ($res) {
             model('msg')->sendmsg($msg);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}
