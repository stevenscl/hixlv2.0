<?php
namespace app\admin\controller;

use think\Controller;

class common extends Controller
{
    public function user($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        
        $registered                 = model('registeredinfo')->get(intval($id));
        $member                     = model('member')->get(intval($id));
        $member['registered_name']  = $registered['registered_name'];
        $member['registered_email'] = $registered['registered_email'];
        $member['create_time']      = $registered['create_time'];
        $member['root_name']        = empty($member->rootinfo->root_name) ? null : $member->rootinfo->root_name;
        $member['community_name']   = empty($member->communityinfo->community_name) ? null : $member->communityinfo->community_name;
        $joincommunity = model('join')->joincommunity($id);
        $joincommunitycount = model('join')->joincommunitycount($id);
        for ($i=0; $i < $joincommunitycount; $i++) { 
            $memberjoincommunity[$i] = model('community')->get($joincommunity[$i])->community_name;
        }
        if (!empty($memberjoincommunity)) {
            $c = implode(',',$memberjoincommunity);
        }else{
            $c=null;
        }
        $member['joincommunity'] = $c;
        return $this->fetch('', [
            'member' => $member,
        ]);
    }
    public function community($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $community = model('community')->get(intval($id));
        $moreinfo = model('communityinfo')->get(intval($id));
        $community['community_logo']     = $moreinfo['community_logo'];
        $community['community_star'] = $moreinfo['community_star'];
        $community['deputy_name']     = empty($moreinfo->deputyname->registered_name) ? null : $moreinfo->deputyname->registered_name;
        $community['registered_name'] = $community->communityCreater->registered_name;
        $community['community_membernum'] = model('join')->where('community_id','=',$id)->count();
        return $this->fetch('', [
            'community' => $community,
        ]);
    }
}
