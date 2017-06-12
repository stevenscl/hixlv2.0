<?php
namespace app\admin\controller;

use think\Controller;

class Communitys extends Controller
{
    private $obj;
    public function _initialize()
    {
        $this->obj = model('community');
    }
    public function index()
    {
        $count     = $this->obj->countdata();
        $community = $this->obj->getCommunity();
        for ($x = 0; $x < intval($count); $x++) {
            $community[$x]['registered_name'] = $this->obj->get(intval($community[$x]['community_id']))->communityCreater->registered_name;
        }
        return $this->fetch('', [
            'count'     => $count,
            'community' => $community,
        ]);
    }
    public function create($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        // $validate = validate('Community');
        // if (!$validate->check($data)) {
        //     $this->error($validate->getError());
        // }
        $data = [
            'community_id' => $id,
        ];
        $creater = $this->obj->get($data)->registered_id;
        $my = model('registeredinfo')->get($creater);

        $user = model('registeredinfo')->get($creater)->registered_name;
        $msg = [
        'uid'=>4,
        'recive_uid'=>$creater,
        'msg_title'=>'系统消息',
        'msg_text'=>"你好，".$user."，您的社团申请已通过，您被任命为社长，您现在可以登录系统后台",
        ];


        $res = $this->obj->where($data)->update(['community_status' => '1']);
        if ($res) {
            model('member')->where('registered_id','=',$creater)->update(['root_id' => '2']);
            model('member')->where('registered_id','=',$creater)->update(['community_id' => $id]);
            model('communityinfo')->save($data);
            $title   = "社团注册成功";
            $content = "你好，" . $my['registered_name'] . "，您提交的社团注册申请已通过，欢迎加入小联的大家庭>.<";
            \Email::send($my['registered_email'], $title, $content);
            model('msg')->sendmsg($msg);
            $this->success('已同意审批');
        } else {
            $this->error('审批失败');
        }
    }
    public function delcreate($id = 0)
    {
        
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $data = [
            'community_id' => $id,
        ];
        $creater = $this->obj->get(intval($id))->registered_id;
        $user = model('registeredinfo')->get($creater)->registered_name;
        $useremail = model('registeredinfo')->get($creater)->registered_email;
        $msg = [
        'uid'=>4,
        'recive_uid'=>$creater,
        'msg_title'=>'系统消息',
        'msg_text'=>"你好，".$user."，您的社团申请被拒绝，可联系小联QQ：2193261037",
        ];
        $res = $this->obj->where($data)->delete();
        if ($res) {
            model('msg')->sendmsg($msg);
            $title   = "社团注册失败";
            $content = "你好，" . $user . "，您的社团申请被拒绝，可联系小联QQ：2193261037";
            \Email::send($useremail, $title, $content);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}
