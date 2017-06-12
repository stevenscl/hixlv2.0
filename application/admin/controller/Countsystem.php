<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Countsystem extends Controller
{
    public function community()
    {
        $result = Db::name('community')
            ->where('community_status', '=', 1)
            ->column('community_name');
        $count = Db::name('Join')

            ->where('join_status', '=', 1)
            ->field('count(community_id)')
            ->group('community_id')
            ->column('count(community_id)');

        return $this->fetch('', [
            'result' => json_encode($result),
            'count'  => json_encode($count),
        ]);
    }
    public function communityac()
    {
        $result = Db::name('community')
            ->where('community_status', '=', 1)
            ->column('community_name');
        $count = Db::name('activity')

            ->where('activity_id', '>=', 0)
            ->field('count(community_id)')
            ->group('community_id')
            ->column('count(community_id)');

        return $this->fetch('', [
            'result' => json_encode($result),
            'count'  => json_encode($count),
        ]);
    }
    public function member()
    {
        $countm        = Db::name('member')
            ->where('member_sex', '=', '男')

            ->count();
        $countw = Db::name('member')
            ->where('member_sex', '=', '女')
            ->count();
        return $this->fetch('', [
            'countm'        => $countm,
            'countw'        => $countw,
            
        ]);
    }



    

    public function relationship()
    {
        $result = Db::name('community')
            ->where('community_id', '>=', 0)
            ->column('community_name');
        $count = Db::name('member')

            ->where('registered_id', '>=', 0)
            ->field('count(community_id)')
            ->group('community_id')
            ->column('count(community_id)');
        $source = Db::name('member')
            ->where('registered_id', '>=', 0)
            ->column('member_name');
        $target = Db::name('member')
            ->where('registered_id', '>=', 0)
            ->column('community_id');

        return $this->fetch('', [
            'result' => json_encode($result),
            'count'  => json_encode($count),
            'source' => json_encode($source),
            'target' => json_encode($target),

        ]);
    }
    public function activity()
    {
        $result = Db::view('joinactivity', 'joinactivity_id,activity_id')
            ->view('activity', 'activity_id,activity_name', 'joinactivity.activity_id=activity.activity_id')
            ->where('joinactivity.joinactivity_status', '=', 1)
            ->column('activity.activity_name');
        $count = Db::view('joinactivity', 'joinactivity_id,activity_id')
            ->view('activity', 'activity_id,activity_name', 'joinactivity.activity_id=activity.activity_id')
            ->where('joinactivity.joinactivity_id', '>=', 0)
            ->field('count(joinactivity.activity_id)')
            ->group('joinactivity.activity_id')
            ->column('count(joinactivity.activity_id)');
        return $this->fetch('', [
            'result' => json_encode(array_merge(array_unique($result))),
            'count'  => json_encode($count),

        ]);
    }

}
