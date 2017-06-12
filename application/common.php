<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function registererinfo($registered_status)
{
    if ($registered_status == 1) {
        $url = request()->domain() . url('index/index/login');
        $str = "帐号申请成功，点击<a href='" . $url . "'>登入</a>";
    } else if ($registered_status == 0) {
    	$index = request()->domain() . url('index/index/news');
        $str = "<div class='wait-line'>小联正在审核</div><div class='wait-line'>审核后系统会发送邮件通知</div><div class='wait-line'>请关注邮箱</div><div class='wait-line'>点击<a href='" . $index . "'>返回</a>首页</div>";
    } else {
        $str = "<div class='wait-line'>非常抱歉，申请已被驳回！</div>";
    }
    return $str;
}
function news_status($news_status){
    if($news_status == 1){
        $str = "<span class = 'label label-success radius'>正常</span>";
    }elseif($news_status == 0){
        $str = "<span class = 'label label-warning radius'>待审</span>";
    }else{
        $str = "<span class = 'label label-danger radius'>删除</span>";
    }
    return $str;
}
function activity_status($activity_status){
    if($activity_status == 1){
        $str = "<span class = 'label label-success radius'>正常</span>";
    }elseif($activity_status == 0){
        $str = "<span class = 'label label-warning radius'>待审</span>";
    }elseif($activity_status == 2){
        $str = "<span class = 'label label-danger radius'>已关闭</span>";
    }else{
        $str = "<span class = 'label label-danger radius'>删除</span>";
    }
    return $str;
}