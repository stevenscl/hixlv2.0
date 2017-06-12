<?php
namespace app\index\controller;

use think\Controller;

class Index extends Base
{
    public function index()
    {
        return $this->fetch();
    }
    public function news()
    {
        $bananer = model('news')->getbananer();
        $count   = model('news')->getpublishnews();
        $news    = model('news')->publishnews();
        for ($x = 0; $x < intval($count); $x++) {
            $news[$x]['community_logo'] = model('communityinfo')->get($news[$x]['community_id'])->community_logo;
            $news[$x]['community_name'] = empty(model('news')->get(intval($news[$x]['news_id']))->news->community_name) ? null : model('news')->get(intval($news[$x]['news_id']))->news->community_name;
        }
        return $this->fetch('', [
            'news'    => $news,
            'bananer' => $bananer,
        ]);
    }
    public function login()
    {
        if (request()->isPost()) {
            $data     = input('post.');
            $validate = validate('Login');
            if (!$validate->scene('login')->check($data)) {
                $this->error($validate->getError());
            }
            $accountinfo = model('registeredinfo')->get(['registered_id' => $data['registered_id']]);
            if (!$accountinfo || $accountinfo->registered_status != 1) {
                $this->error('该帐号不存在，或者该帐号正在审核');
            } elseif ($accountinfo->registered_password != md5($data['registered_password'] . $accountinfo->registered_code)) {
                $this->error('密码不正确');
            } else {
                $memberinfo = model('member')->get(['registered_id' => $data['registered_id']]);
                session('Account', $accountinfo, 'index');
                session('Member', $memberinfo, 'index');
                return $this->success('登入成功', url('index/news'));
            }
        } else {
            $account = session('Account', '', 'index');
            $member  = session('Member', '', 'index');
            if ($account && $account->registered_id) {
                return $this->redirect(url('index/news'));
            }
            return $this->fetch();
        }
    }
    public function logout()
    {
        session(null, 'index');
        $this->redirect(url('index/index'));
    }
    public function registered()
    {
        if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data     = input('post.');
        $validate = validate('Login');
        if (!$validate->scene('registered')->check($data)) {
            $this->error($validate->getError());
        }
        $accountexist = model('registeredinfo')->get(['registered_id' => $data['registered_id']]);
        if ($accountexist) {
            $this->error('该帐号已存在，请重新输入');
        }
        if (!captcha_check($data['verifycode'])) {
            $this->error('验证码输入不正确，请重新输入');
        }
        unset($data['verifycode']);
        $data['registered_code']     = mt_rand(100, 10000);
        $data['registered_password'] = md5($data['registered_password'] . $data['registered_code']);
        $res                         = model('registeredinfo')->registered($data);
        if ($res) {
            $url     = request()->domain() . url('index/index/waiting', ['id' => $data['registered_id']]);
            $title   = "欢迎加入小联2.0";
            $content = "你好，" . $data['registered_name'] . "，您提交的帐号申请已递交管理员审核，审核需要一天时间，您可以点击以下链接<a href='" . $url . "' target='_blank' />查看当前审核状态</a>";
            \Email::send($data['registered_email'], $title, $content);
            $this->success('帐号申请成功', url('index/waiting', ['id' => $data['registered_id']]));
        } else {
            $this->error('申请失败');
        }
    }
    public function waiting($id)
    {
        if (empty($id)) {
            $this->error('error');
        }
        $status = model('registeredinfo')->get($id);
        return $this->fetch('', [
            'status' => $status,
        ]);
    }
    public function editinfo()
    {
        if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data    = input('post.');
        $res     = model('member')->save($data, ['registered_id' => $this->getLoginUser()->registered_id]);
        $newdata = model('member')->get($this->getLoginUser()->registered_id);
        if ($res) {
            session('Member', $newdata, 'index');
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }
    }
    public function community()
    {
        if (session('Account', '', 'index')) {
            $my          = session('Account', '', 'index')->registered_id;
            $myjoin      = model('join')->joincommunity($my);
            $myjoincount = model('join')->joincommunitycount($my);
            $mycommunity = model('community')->where('community_id', 'IN', $myjoin)->select();
            for ($i = 0; $i < $myjoincount; $i++) {
                $mycommunity[$i]['registered_id'] = model('community')->get($mycommunity[$i]->community_id)->registered_id;
                $mycommunity[$i]['deputy_id']     = model('communityinfo')->get($mycommunity[$i]->community_id)->community_deputyid;
                if ($mycommunity[$i]['deputy_id']) {
                    $mycommunity[$i]['deputy_name'] = model('registeredinfo')->get($mycommunity[$i]['deputy_id'])->registered_name;
                } else {
                    $mycommunity[$i]['deputy_name'] = null;
                }
                $mycommunity[$i]['registered_name'] = model('registeredinfo')->get($mycommunity[$i]['registered_id'])->registered_name;
                $mycommunity[$i]['community_star']  = model('communityinfo')->get($mycommunity[$i]->community_id)->community_star;
                $mycommunity[$i]['community_logo']  = empty(model('communityinfo')->get($mycommunity[$i]->community_id)->community_logo) ? null : model('communityinfo')->get($mycommunity[$i]->community_id)->community_logo;
                $mycommunity[$i]['editorValue']     = empty(model('communityinfo')->get($mycommunity[$i]->community_id)->editorValue) ? null : model('communityinfo')->get($mycommunity[$i]->community_id)->editorValue;
                 $mycommunity[$i]['community_img']     = empty(model('communityimg')->getimg($mycommunity[$i]['community_id']))?null:model('communityimg')->getimg($mycommunity[$i]['community_id']);
            }
        } else {
            $mycommunity = null;
            $myjoincount = null;
        }
        $count     = model('community')->count();
        $community = model('community')->communityinfo();
        
        for ($x = 0; $x < intval($count); $x++) {
            $community[$x]['registered_name'] = model('community')->get(intval($community[$x]['community_id']))->communityCreater->registered_name;
            $community[$x]['deputy_id']       = empty(model('communityinfo')->get(intval($community[$x]['community_id']))->deputyname->registered_id) ? null : model('communityinfo')->get(intval($community[$x]['community_id']))->deputyname->registered_id;
            $community[$x]['deputy_name']     = empty(model('communityinfo')->get(intval($community[$x]['community_id']))->deputyname->registered_name) ? null : model('communityinfo')->get(intval($community[$x]['community_id']))->deputyname->registered_name;
            $community[$x]['community_star']  = model('community')->get(intval($community[$x]['community_id']))->info->community_star;
            $community[$x]['community_logo']  = model('communityinfo')->get(intval($community[$x]['community_id']))->community_logo;
            $community[$x]['editorValue']     = model('communityinfo')->get(intval($community[$x]['community_id']))->editorValue;
             
            $community[$x]['community_img']     = empty(model('communityimg')->getimg($community[$x]['community_id']))?null:model('communityimg')->getimg($community[$x]['community_id']);

        }
      
        return $this->fetch('', [
            'community'   => $community,
            'mycommunity' => $mycommunity,
            'myjoincount' => $myjoincount,
        ]);

    }

    public function setdel($id)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $my                    = session('Account', '', 'index');
        $data = [
            'community_id' => $id,
            'registered_id' => $my->registered_id,
        ];
        $res = model('join')->where($data)->update(['join_status' => '-1']);
        if ($res) {
            $this->success('申请退社成功');
        } else {
            $this->error('申请退社失败');
        }
    }

    public function applyjoincommunity()
    {
         if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data     = input('post.');
        $validate = validate('Join');
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }
        if (!captcha_check($data['verifycode'])) {
            $this->error('验证码输入不正确，请重新输入');
        }
        unset($data['verifycode']);
        $my                    = session('Account', '', 'index');
        $data['registered_id'] = $my->registered_id;

        print_r($data['community_id']);

        $joining = model('join')->where(['registered_id' => $data['registered_id'], 'join_status' => 0])->column('community_id');
        foreach ($joining as $value) {
            if ($value == $data['community_id']) {
                return $this->error('您的申请已提交，无须重复申请，请耐心等候社团管理员审核，审核结果将发送至您邮箱');
            }
        }

        $joined = model('join')->where(['registered_id' => $data['registered_id'], 'join_status' => 1])->column('community_id');
        foreach ($joined as $value) {
            if ($value == $data['community_id']) {
                return $this->error('您已是该社成员，无须重复加入');
            }
        }

        $joindel = model('join')->where(['registered_id' => $data['registered_id'], 'join_status' => -1])->column('community_id');
        foreach ($joindel as $value) {
            if ($value == $data['community_id']) {
                return $this->error('非常抱歉，您已被该社拒绝');
            }
        }
            $res = model('join')->apply($data);
            if ($res) {
                $title   = "加入社团申请成功";
                $communityname = model('community')->get($data['community_id'])->community_name;
                $content = "你好，" . $my['registered_name'] . "，您提交的加入" . $communityname . "的申请已递交社团管理员审核，请耐心等待！客服QQ：2193261037";
                \Email::send($my['registered_email'], $title, $content);
                $this->success('加入社团申请已递交，请耐心等待！请留意邮箱动态');
            } else {
                $this->error('申请失败');
            }
        }
    public function applycommunity()
    {
        if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data     = input('post.');
        $validate = validate('Apply');
        if (!$validate->scene('applycommunity')->check($data)) {
            $this->error($validate->getError());
        }
        if (!captcha_check($data['verifycode'])) {
            $this->error('验证码输入不正确，请重新输入');
        }
        unset($data['verifycode']);
        $my                    = session('Account', '', 'index');
        $data['registered_id'] = $my->registered_id;
        $isexist               = model('member')->get(intval($data['registered_id']));
        if (($isexist->root_id == 2) && ($isexist->community_id)) {
            $communityname = model('community')->get($isexist->community_id)->community_name;
            $this->error("新增失败，根据社团联合会管理条例，社团成员不可在多社团任职社长，该社长已在" . $communityname . "任职社长");
        } else {
            $res = model('community')->applycommunity($data);
            if ($res) {
                $title   = "社团注册申请成功";
                $content = "你好，" . $my['registered_name'] . "，您提交的社团注册申请已递交管理员审核，审核需要一天时间，请耐心等待！客服QQ：2193261037";
                \Email::send($my['registered_email'], $title, $content);
                $this->success('社团申请已递交，请耐心等待！请留意邮箱动态');
            } else {
                $this->error('申请失败');
            }
        }

    }
    public function myaccount()
    {
        return $this->fetch();
    }
    public function mymsg()
    {
        if (session('Account', '', 'index')) {
            $my  = session('Account', '', 'index')->registered_id;
            $msg = model('msg')->where('recive_uid', '=', $my)->limit(10)->order('create_time', 'desc')->select();
        } else {
            $msg = null;
        }
        return $this->fetch('', [
            'msg' => $msg,
        ]);
    }
    public function activity()
    {
        $contstandby     = model('activity')->standbyactivity();
        $activitystandby = model('activity')->getstandbyactivity();
        $count           = model('activity')->publishactivity();
        $activity        = model('activity')->getpublishactivity();
        for ($x = 0; $x < intval($count); $x++) {
            $activity[$x]['community_name'] = empty(model('activity')->get(intval($activity[$x]['activity_id']))->activity->community_name) ? null : model('activity')->get(intval($activity[$x]['activity_id']))->activity->community_name;
        }
        for ($x = 0; $x < intval($contstandby); $x++) {
            $activitystandby[$x]['community_name'] = empty(model('activity')->get(intval($activitystandby[$x]['activity_id']))->activity->community_name) ? null : model('activity')->get(intval($activitystandby[$x]['activity_id']))->activity->community_name;
        }
        // print_r($activity);
        return $this->fetch('', [
            'activity'        => $activity,
            'activitystandby' => $activitystandby,
        ]);
    }
    public function joinactivity()
    {
        if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data   = input('post.');
        $joined = model('joinactivity')->where(['registered_id' => $data['registered_id'], 'joinactivity_status' => 1])->column('activity_id');
        foreach ($joined as $value) {
            if ($value == $data['activity_id']) {
                return $this->error('您已报名过此活动，无须重复报名');
            }
        }
        $res = model('joinactivity')->join($data);
        if ($res) {
            $this->success('您已成功报名,您可以到我的活动查看已报名活动');
        } else {
            $this->error('报名失败');
        }
    }
    public function alertlogin()
    {
        return $this->fetch();
    }
    public function alertperfect()
    {
        return $this->fetch();
    }
    public function myactivity()
    {
        if (session('Account', '', 'index')) {
            $my       = session('Account', '', 'index')->registered_id;
            $activity = model('joinactivity')->where('registered_id', '=', $my)->column('activity_id');
            $count    = model('joinactivity')->where('registered_id', '=', $my)->count();

        } else {
            $activity = null;
            $count =null;
        }

        $myactivityinfo = model('activity')->where('activity_id', 'IN', $activity)->select();

        for ($x = 0; $x < intval($count); $x++) {
            $myactivityinfo[$x]['community_name'] = empty(model('activity')->get(intval($myactivityinfo[$x]['activity_id']))->activity->community_name) ? null : model('activity')->get(intval($myactivityinfo[$x]['activity_id']))->activity->community_name;
        }

        return $this->fetch('', [
            'activity' => $myactivityinfo,
        ]);
    }
    public function cancelactivity()
    {
        if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data   = input('post.');
        $cancel = model('joinactivity')->where(['registered_id' => $data['registered_id'], 'joinactivity_status' => 1, 'activity_id' => $data['activity_id']])->delete();
        if ($cancel) {
            $this->success('取消成功');
        } else {
            $this->error('取消失败');
        }
    }
    public function view()
    {
        if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $my   = session('Account', '', 'index')->registered_id;
        $data = input('post.');
        print_r($data);
        if (!empty($data['noname'])) {
            $msg = [
                'uid'        => 4,
                'recive_uid' => 0,
                'msg_title'  => '意见箱',
                'msg_text'   => $data['msg_text'],
            ];
        } else {
            $msg = [
                'uid'        => 4,
                'recive_uid' => 0,
                'send_uid'   => $my,
                'msg_title'  => '意见箱',
                'msg_text'   => $data['msg_text'],
            ];
        }
        $res = model('msg')->sendmsg($msg);
        if ($res) {
            $this->success('意见反馈成功');
        }else{
            $this->error('意见反馈失败');
        }
    }
    public function service()
    {
        return $this->fetch();
    }
}
