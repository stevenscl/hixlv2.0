<?php
namespace app\admin\controller;

use think\Controller;

class Communitysinfo extends Controller
{
    private $obj;
    public function _initialize()
    {
        $this->obj  = model('community');
        $this->obj2 = model('communityinfo');
        $this->obj3 = model('join');
    }
    public function community()
    {
        $count     = $this->obj->count();
        $community = $this->obj->communityinfo();
        for ($x = 0; $x < intval($count); $x++) {
            $community[$x]['registered_name'] = $this->obj->get(intval($community[$x]['community_id']))->communityCreater->registered_name;
            $community[$x]['deputy_id']       = empty($this->obj2->get(intval($community[$x]['community_id']))->deputyname->registered_id) ? null : $this->obj2->get(intval($community[$x]['community_id']))->deputyname->registered_id;
            $community[$x]['deputy_name']     = empty($this->obj2->get(intval($community[$x]['community_id']))->deputyname->registered_name) ? null : $this->obj2->get(intval($community[$x]['community_id']))->deputyname->registered_name;
            $community[$x]['community_star']  = $this->obj->get(intval($community[$x]['community_id']))->info->community_star;
        }
        return $this->fetch('', [
            'count'     => $count,
            'community' => $community,
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
        $data = input('post.');

        $validate = validate('Community');
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }
        $member['community_id']              = $data['community_id'];
        $member['root_id'] = 2;
        $deputy['deputy_id']                 = $data['deputy_id'];
        $communityinfo['community_deputyid'] = $data['deputy_id'];
        $communityinfo['community_logo']     = $data['community_logo'];
        $communityinfo['editorValue']        = $data['editorValue'];
        unset($data['editorValue']);
        unset($data['community_logo']);
        unset($data['deputy_id']);
        $isexist = model('member')->get(intval($data['registered_id']));
        
        if (($isexist->root_id==2)&&($isexist->community_id)) {
            $communityname=model('community')->get($isexist->community_id)->community_name;
            $this->error("新增失败，根据社团联合会管理条例，社团成员不可在多社团任职社长，该社长已在".$communityname."任职社长");
        }else{
            $res = $this->obj->add($data);
            if ($res) {
                $communityinfo['community_id'] = $data['community_id'];
                model('member')->get(intval($data['registered_id']))->save($member);
                if ($deputy['deputy_id']!='') {
                    model('member')->where('registered_id','=',$deputy['deputy_id'])->update(['root_id' => 1]);
                }
                if ($deputy['deputy_id']=='') {
                unset($communityinfo['community_deputyid']);
                }
                $this->obj->info()->save($communityinfo);
                $this->success('新增成功');
            } else {
                $this->error('新增失败');
            }
        }
        
    }
    public function edit($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $community                   = $this->obj->get($id);
        $community['deputy_id']      = empty($this->obj->get(intval($community['community_id']))->info->community_deputyid) ? null : $this->obj->get(intval($community['community_id']))->info->community_deputyid;
        $community['community_logo'] = empty($this->obj->get(intval($community['community_id']))->info->community_logo) ? null : $this->obj->get(intval($community['community_id']))->info->community_logo;
        $community['editorValue']    = empty($this->obj->get(intval($community['community_id']))->info->editorValue) ? null : $this->obj->get(intval($community['community_id']))->info->editorValue;
        return $this->fetch('', [
            'community' => $community,
        ]);
    }
    public function update()
    {
        if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data     = input('post.');
        $validate = validate('Community');
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }
        $oldregistered_id = $data['oldregistered_id'];
        $member['community_id']              = $data['community_id'];
        $member['root_id'] = 2;
        $deputy['deputy_id']                 = $data['deputy_id'];
        $communityinfo['community_deputyid'] = $data['deputy_id'];
        $communityinfo['community_star']     = $data['community_star'];
        $communityinfo['community_logo']     = $data['community_logo'];
        $communityinfo['editorValue']        = $data['editorValue'];
        unset($data['editorValue']);
        unset($data['oldregistered_id']);
        unset($data['community_logo']);
        unset($data['deputy_id']);
        unset($data['community_star']);
        $isexist = model('member')->get(intval($data['registered_id']));
        
        if (($isexist->root_id==2)&&($isexist->community_id)&&($oldregistered_id!=$data['registered_id'])) {
            $communityname=model('community')->get($isexist->community_id)->community_name;
            $this->error("新增失败，根据社团联合会管理条例，社团成员不可在多社团任职社长，该社长已在".$communityname."任职社长");
        }else{
                   if ($oldregistered_id==$data['registered_id']) {
           $res = $this->obj->get(intval($data['community_id']))->update($data); 
           if ($res) {
                if ($deputy['deputy_id']=='') {
               unset($communityinfo['community_deputyid']);
               model('communityinfo')->where('community_id','=',$data['community_id'])->update(['community_deputyid' => null]);
            }
            if ($deputy['deputy_id']!='') {
                model('member')->where('registered_id','=',$communityinfo['community_deputyid'])->update(['root_id' => 1]);
            }
                $this->obj->get(intval($data['community_id']))->info->save($communityinfo);
                
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
        }else{
            model('member')->where('registered_id','=',$oldregistered_id)->update(['community_id' => null,'root_id' => 1]);
            $res = $this->obj->get(intval($data['community_id']))->update($data); 
            
            if ($res) {
                 if ($deputy['deputy_id']!='') {
                model('member')->where('registered_id','=',$communityinfo['community_deputyid'])->update(['root_id' => 1]);
            }
                 model('member')->get(intval($data['registered_id']))->save($member);
                 
                 if ($deputy['deputy_id']=='') {
               unset($communityinfo['community_deputyid']);
            }
                $this->obj->get(intval($data['community_id']))->info->save($communityinfo);
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
        }
        
        }
  
    }

       public function updatemy()
    {
        if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data     = input('post.');
        // $validate = validate('Community');
        // if (!$validate->check($data)) {
        //     $this->error($validate->getError());
        // }
        $communityinfo['community_logo']     = $data['community_logo'];
        $communityinfo['editorValue']        = $data['editorValue'];
        unset($data['editorValue']);
        unset($data['community_logo']);
           $res = $this->obj->get(intval($data['community_id']))->update($data); 
           if ($res) {
                $this->obj->get(intval($data['community_id']))->info->save($communityinfo);
                
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
        }
    public function cancel($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $data = [
            'community_id' => $id,
        ];
        $res = $this->obj->where($data)->update(['community_status' => '-1']);
        if ($res) {
            $this->success('已注销');
        } else {
            $this->error('注销失败');
        }
    }
    public function communitycancel()
    {
        $count     = $this->obj->countdel();
        $community = $this->obj->delcommunityinfo();
        for ($x = 0; $x < intval($count); $x++) {
            $community[$x]['registered_name'] = $this->obj->get(intval($community[$x]['community_id']))->communityCreater->registered_name;
            $community[$x]['deputy_id']       = empty($this->obj2->get(intval($community[$x]['community_id']))->deputyname->registered_id) ? null : $this->obj2->get(intval($community[$x]['community_id']))->deputyname->registered_id;
            $community[$x]['deputy_name']     = empty($this->obj2->get(intval($community[$x]['community_id']))->deputyname->registered_name) ? null : $this->obj2->get(intval($community[$x]['community_id']))->deputyname->registered_name;
            $community[$x]['community_star']  = $this->obj->get(intval($community[$x]['community_id']))->info->community_star;
        }
        return $this->fetch('', [
            'count'     => $count,
            'community' => $community,
        ]);
    }
    public function status($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $data = [
            'community_id' => $id,
        ];
        $res = $this->obj->where($data)->update(['community_status' => '1']);
        if ($res) {
            $this->success('已还原');
        } else {
            $this->error('还原失败');
        }
    }
    public function del($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $data = [
            'community_id' => $id,
        ];
        $creater = $this->obj->get($id)->registered_id;
        $deputy = model('communityinfo')->get($id)->community_deputyid;
        $user = model('registeredinfo')->get($creater)->registered_name;
        $useremail = model('registeredinfo')->get($creater)->registered_email;
        $msg = [
        'uid'=>4,
        'recive_uid'=>$creater,
        'msg_title'=>'系统消息',
        'msg_text'=>"你好，".$user."，您的社团已被注销，详情可联系小联QQ：2193261037",
        ];
        $del = $this->obj->get($id);
        $del->info->delete();
        $res = $this->obj->where($data)->delete();

        
        if ($res) {
            model('msg')->sendmsg($msg);
            model('member')->where('registered_id','=',$creater)->update(['root_id' => null,'community_id'=>null]);
            model('member')->where('registered_id','=',$deputy)->update(['root_id' => null]);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
    public function memberapply()
    {
        $count = $this->obj3->countjoin();
        $join  = $this->obj3->getjoin();
        for ($x = 0; $x < intval($count); $x++) {
            $join[$x]['registered_name'] = $this->obj3->get(intval($join[$x]['join_id']))->applyuserinfo->registered_name;
            $join[$x]['community_name']  = $this->obj3->get(intval($join[$x]['join_id']))->applycommunityinfo->community_name;
        }
        return $this->fetch('', [
            'count' => $count,
            'join'  => $join,
        ]);
    }
    public function applystatus($id = 0,$registered_id)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $data = [
            'join_id' => $id,
        ];
        $community_id = $this->obj3->get(intval($id))->community_id;
        $community_name= model('community')->get($community_id)->community_name;
        $user = model('registeredinfo')->get($registered_id)->registered_name;
        $useremail = model('registeredinfo')->get($registered_id)->registered_email;
        $msg = [
        'uid'=>4,
        'recive_uid'=>$registered_id,
        'msg_title'=>'系统消息',
        'msg_text'=>"你好，".$user."，您申请加入".$community_name."已被批准，您可以到我的社团查看！",
        ];
         $title   = "加入社团成功";
                $content = "你好，" . $user . "，您已经成功加入" . $community_name . "，欢迎成为" . $community_name . "的一份子，您可以到我的社团查看社团相关信息";
                
        $res          = $this->obj3->where($data)->update(['join_status' => '1']);
        if ($res) {
            // $this->obj3->get(intval($id))->modifycommunity->save(['community_id' => $community_id]);
            model('msg')->sendmsg($msg);
            \Email::send($useremail, $title, $content);
            $userroot = model('member')->get($registered_id)->root_id;
            if ($userroot < 2) {
                model('member')->where('registered_id','=',$registered_id)->update(['root_id' => 1]);
            }
            $this->success('已批准');
        } else {
            $this->error('批准失败');
        }
    }
    public function setdel($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $data = [
            'join_id' => $id,
        ];
        $res = $this->obj3->where($data)->update(['join_status' => '-1']);
        if ($res) {
            $this->success('已加入删除队列');
        } else {
            $this->error('加入失败');
        }
    }
    public function delapply()
    {
        $count = $this->obj3->countdel();
        $join  = $this->obj3->getdel();
        for ($x = 0; $x < intval($count); $x++) {
            $join[$x]['registered_name'] = $this->obj3->get(intval($join[$x]['join_id']))->applyuserinfo->registered_name;
            $join[$x]['community_name']  = $this->obj3->get(intval($join[$x]['join_id']))->applycommunityinfo->community_name;
        }
        return $this->fetch('', [
            'count' => $count,
            'join'  => $join,
        ]);
    }
    public function canceldel($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $data = [
            'join_id' => $id,
        ];
        $res = $this->obj3->where($data)->update(['join_status' => '0']);
        if ($res) {
            $this->success('已撤回');
        } else {
            $this->error('撤回失败');
        }
    }
    public function applydel($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $data = [
            'join_id' => intval($id),
        ];
        $creater = $this->obj3->get(intval($id))->registered_id;
        $community_id = $this->obj3->get(intval($id))->community_id;
        $community_name= model('community')->get($community_id)->community_name;
        $user = model('registeredinfo')->get($creater)->registered_name;
        $useremail = model('registeredinfo')->get($creater)->registered_email;
        $msg = [
        'uid'=>4,
        'recive_uid'=>$creater,
        'msg_title'=>'系统消息',
        'msg_text'=>"你好，".$user."，您申请加入".$community_name."已被驳回，真的抱歉！",
        ];
        $title   = "加入社团失败";
                $content = "你好，" . $user . "，您加入" . $community_name . "已被驳回，您可再次申请，或者联系客服，客服QQ：2193261037";
        $res = $this->obj3->where($data)->delete();
        if ($res) {
            model('msg')->sendmsg($msg);
            \Email::send($useremail, $title, $content);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }



    // 以下移植
    public function eachmemberapply()
    {
        $account = session('registered', '', 'admin');
        $community = model('member')->get(intval($account->registered_id));
        $count = $this->obj3->counteachjoin($community->community_id);
        $join  = $this->obj3->geteachjoin($community->community_id);
         for ($x = 0; $x < intval($count); $x++) {
            $join[$x]['registered_name'] = $this->obj3->get(intval($join[$x]['join_id']))->applyuserinfo->registered_name;
            $join[$x]['community_name'] = $this->obj3->get(intval($join[$x]['join_id']))->applycommunityinfo->community_name;
        }
        return $this->fetch('', [
            'count' => $count,
            'join'  => $join,
        ]);
    }

    public function eachdelapply()
    {
         $account = session('registered', '', 'admin');
        $community = model('member')->get(intval($account->registered_id));
        $count = $this->obj3->counteachdel($community->community_id);
        $join  = $this->obj3->geteachdel($community->community_id);
         for ($x = 0; $x < intval($count); $x++) {
            $join[$x]['registered_name'] = $this->obj3->get(intval($join[$x]['join_id']))->applyuserinfo->registered_name;
            $join[$x]['community_name'] = $this->obj3->get(intval($join[$x]['join_id']))->applycommunityinfo->community_name;
        }
        return $this->fetch('', [
            'count' => $count,
            'join'  => $join,
        ]);
    }

    public function basic()
    {
        $account = session('registered', '', 'admin');
        $id = model('member')->get(intval($account->registered_id))->community_id;
        $community                   = $this->obj->get($id);
        $community['community_logo'] = empty($this->obj->get(intval($community['community_id']))->info->community_logo) ? null : $this->obj->get(intval($community['community_id']))->info->community_logo;
        $community['editorValue']    = empty($this->obj->get(intval($community['community_id']))->info->editorValue) ? null : $this->obj->get(intval($community['community_id']))->info->editorValue;
        return $this->fetch('', [
            'community' => $community,
        ]);
    }

    public function img()
    {
        $account = session('registered', '', 'admin');
        $id = model('member')->get(intval($account->registered_id))->community_id;
        $count = model('communityimg')->countimg();
        $img = model('communityimg')->imginfo($id);
        return $this->fetch('', [
            'img' => $img,
            'count' => $count,
        ]);
    }

    public function addimg()
    {
        return $this->fetch();
    }

    public function saveimg()
    {
         if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data = input('post.');

        $account = session('registered', '', 'admin');
        $id = model('member')->get(intval($account->registered_id))->community_id;
        $data['community_id'] = $id;
        // $validate = validate('Community');
        // if (!$validate->check($data)) {
        //     $this->error($validate->getError());
        // }
        
            $res = model('communityimg')->save($data);
            if ($res) {
                $this->success('新增成功');
            } else {
                $this->error('新增失败');
            }
    }

    public function updateimg()
    {
         if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data = input('post.');
        $res = model('communityimg')->where('community_id','=',$data['community_id'])->update($data);
            if ($res) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
    }

    public function editimg($id)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $communityimg                   = model('communityimg')->get($id);
        return $this->fetch('', [
            'communityimg' => $communityimg,
        ]);
    }

    public function delimg($id)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $res                   = model('communityimg')->get($id)->delete();
        if ($res) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
    }
}
