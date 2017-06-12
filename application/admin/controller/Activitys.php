<?php
namespace app\admin\controller;

use think\Controller;


class Activitys extends Controller
{
    private $obj;
    public function _initialize()
    {
        $this->obj = model('community');
    }
    public function index()
    {

        $activitylist = model('activity')->getactivity();
        $count        = model('activity')->countactivity();
        for ($x = 0; $x < $count; $x++) {
            $activitylist[$x]['community_name'] = empty(model('activity')->get(intval($activitylist[$x]['activity_id']))->activity->community_name) ? null : model('activity')->get(intval($activitylist[$x]['activity_id']))->activity->community_name;
        }
        return $this->fetch('', [
            'activitylist' => $activitylist,
            'count'        => $count,
        ]);
    }
    public function add()
    {
        $community = $this->obj->communityinfo();
        return $this->fetch('', [
            'community' => $community,
        ]);
    }

    public function activity()
    {
        
        
        if (!request()->isPost()) {
            $this->error('请求错误');
        }
        $data     = input('post.');

       
        
        $validate = validate('Activity');
       
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }
        if (empty($data['activity_listorder'])) {
            $data['activity_listorder']=0;
        }
        
        if (!empty($data['activity_id'])) {
            return $this->update($data);
        //新增发送消息
            }
            
             
        $res = model('Activity')->add($data);
        if ($res) {
             $communityid = intval($data['community_id']);
             $creater = model('member')->where('community_id','=',$communityid)->column('registered_id');
             $user = model('registeredinfo')->get($creater[0])->registered_name;
             $useremail = model('registeredinfo')->get($creater[0])->registered_email;
             $community_name= model('community')->get($communityid)->community_name;
             $msg = [
        'uid'=>4,
        'recive_uid'=>$creater[0],
        'msg_title'=>'系统消息',
        'msg_text'=>"尊敬的社长你好，".$user."，您的".$community_name."的".$data['activity_name']."活动已提交至管理员审核，请耐心等待，审核结果将会通过邮件发送至您邮箱！",
        ];
        model('msg')->sendmsg($msg);

        $title   = "社团活动申请成功";
            $content = "尊敬的社长你好，".$user."，您的".$community_name."的".$data['activity_name']."活动已提交至管理员审核，请耐心等待，审核结果将会通过邮件发送至您邮箱！";
            \Email::send($useremail, $title, $content);
             print_r('上传成功');
        }else{
             print_r('上传失败');

        }
            
        
    }

    //修改状态
   public function status($activity_id,$activity_status,$community_id)
    {
        
        /* $validate= validate('Activity');
        if(!$validate->scene('activity_status')->check($data)){
        $this->error($validate->getError());
        }*/
        if ($activity_status==1) {
            $activity_name = model('activity')->get($activity_id)->activity_name;
            $creater = model('community')->get($community_id)->registered_id;
            $community_name= model('community')->get($community_id)->community_name;
            $user = model('registeredinfo')->get($creater)->registered_name;
            $useremail = model('registeredinfo')->get($creater)->registered_email;
        $msg = [
        'uid'=>4,
        'recive_uid'=>$creater,
        'msg_title'=>'系统消息',
        'msg_text'=>"尊敬的社长你好，".$user."，您的".$community_name."的".$activity_name."活动已审批通过！",
        ];
        model('msg')->sendmsg($msg);
        $title   = "社团活动审核通过";
            $content = "尊敬的社长你好，".$user."，您的".$community_name."的".$activity_name."活动已审批通过！现在可以进行活动报名。";
            \Email::send($useremail, $title, $content);
        }
        $res =model('activity')->save(['activity_status' => $activity_status], ['activity_id' => $activity_id]);
        if ($res) {
            $this->success('状态跟新成功');
        } else {
            $this->error('状态跟新失败');
        }
    }

    public function statusdel($activity_id,$activity_status,$community_id)
    {
            $activity_name = model('activity')->get($activity_id)->activity_name;
            $creater = model('community')->get($community_id)->registered_id;
            $community_name= model('community')->get($community_id)->community_name;
            $user = model('registeredinfo')->get($creater)->registered_name;
            $useremail = model('registeredinfo')->get($creater)->registered_email;
        $msg = [
        'uid'=>4,
        'recive_uid'=>$creater,
        'msg_title'=>'系统消息',
        'msg_text'=>"尊敬的社长你好，".$user."，您的".$community_name."的".$activity_name."活动已被驳回！",
        ];
        model('msg')->sendmsg($msg);
         $title   = "社团活动审核通过";
            $content = "尊敬的社长你好，".$user."，您的".$community_name."的".$activity_name."活动审核未通过，详情请联系社联QQ公众号：2193261037";
            \Email::send($useremail, $title, $content);
        
        $res =model('activity')->save(['activity_status' => $activity_status], ['activity_id' => $activity_id]);
        if ($res) {
            $this->success('状态更新成功');
        } else {
            $this->error('状态更新失败');
        }
    }

    public function close($activity_id,$activity_status)
    {
         $res =model('activity')->save(['activity_status' => $activity_status], ['activity_id' => $activity_id]);
        if ($res) {
            $this->success('活动已被关闭');
        } else {
            $this->error('活动关闭失败');
        }
    }

    public function activitydelete()
    {

        $activitylist = model('activity')->getactivitydelete();
        $count        = model('activity')->countactivitydelete();
        for ($x = 0; $x < $count; $x++) {
            $activitylist[$x]['community_name'] = empty(model('activity')->get(intval($activitylist[$x]['activity_id']))->activity->community_name) ? null : model('activity')->get(intval($activitylist[$x]['activity_id']))->activity->community_name;
        }
        return $this->fetch('', [
            'activitylist' => $activitylist,
            'count'        => $count,
        ]);
    }
    public function detail($activity_id=0)
    {
        
        if (intval($activity_id) < 1) {
             $this->error('ID错误');

        }
        
        $activitylist = model('activity')->get($activity_id);
        $community    = $this->obj->communityinfo();
      
        return $this->fetch('', [
            'community'    => $community,
            'activitylist' => $activitylist,
        ]);
    }
    public function update($data){
        {
            $this->obj = model('activity');
        }
        $res = $this->obj->save($data,['activity_id'=>intval($data['activity_id'])]);
        if($res) {
            $this->success('更新成功');
        } else{
            $this->error('更新失败');
        }
   }


   public function eachadd()
    {
         $account = session('registered', '', 'admin');
        $communityid = intval(model('member')->get(intval($account->registered_id))->community_id);
        // print_r($communityid);
        return $this->fetch('', [
            'communityid' => $communityid,
        ]);
    }



    public function joinactivitylist()
    {
        $count     = model('joinactivity')->count();
        $member    = model('joinactivity')->getlist();
        for ($x = 0; $x < intval($count); $x++) {
            $member[$x]['member_name']     = model('member')->get(intval($member[$x]['registered_id']))->registeredinfo->registered_name;
            $member[$x]['activity_name']   = model('activity')->get(intval($member[$x]['activity_id']))->activity_name;
            $member[$x]['member_qq']       = empty(model('member')->get(intval($member[$x]['registered_id']))->member_qq) ? null : model('member')->get(intval($member[$x]['registered_id']))->member_qq;
            $member[$x]['member_phonenum'] = empty(model('member')->get(intval($member[$x]['registered_id']))->member_phonenum) ? null : model('member')->get(intval($member[$x]['registered_id']))->member_phonenum;
           $member[$x]['community_name']  = empty(model('activity')->get(intval($member[$x]['activity_id']))->communityinfo->community_name) ? null : model('activity')->get(intval($member[$x]['activity_id']))->communityinfo->community_name;
           $member[$x]['community_id']  = empty(model('activity')->get(intval($member[$x]['activity_id']))->communityinfo->community_id) ? null : model('activity')->get(intval($member[$x]['activity_id']))->communityinfo->community_id;
        }
        return $this->fetch('', [
            'member' => $member,
            'count'  => $count,
        ]);

    }
        public function eachjoinactivitylist()
    { 
        $account = session('registered', '', 'admin');
        $community = model('member')->get(intval($account->registered_id));
        $activity_id= model('activity')->where('community_id','=',$community->community_id)->column('activity_id');
        
        $count     = model('joinactivity')->eachcount($activity_id);
        $member    = model('joinactivity')->eachgetlist($activity_id);
        for ($x = 0; $x < intval($count); $x++) {
            $member[$x]['member_name']     = model('member')->get(intval($member[$x]['registered_id']))->registeredinfo->registered_name;
            $member[$x]['activity_name']   = model('activity')->get(intval($member[$x]['activity_id']))->activity_name;
            $member[$x]['member_qq']       = empty(model('member')->get(intval($member[$x]['registered_id']))->member_qq) ? null : model('member')->get(intval($member[$x]['registered_id']))->member_qq;
            $member[$x]['member_phonenum'] = empty(model('member')->get(intval($member[$x]['registered_id']))->member_phonenum) ? null : model('member')->get(intval($member[$x]['registered_id']))->member_phonenum;
            $member[$x]['community_id']  = empty(model('activity')->get(intval($member[$x]['activity_id']))->communityinfo->community_id) ? null : model('activity')->get(intval($member[$x]['activity_id']))->communityinfo->community_id;
            $member[$x]['community_name']  = empty(model('activity')->get(intval($member[$x]['activity_id']))->communityinfo->community_name) ? null : model('activity')->get(intval($member[$x]['activity_id']))->communityinfo->community_name;
        }
        return $this->fetch('', [
            'member' => $member,
            'count'  => $count,
        ]);

    }
}
