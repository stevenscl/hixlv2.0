<?php
namespace app\admin\Validate;

use think\Validate;

class Activity extends Validate
{
    protected $rule = [
         ['activity_status','number|in:-1,0,1','状态字必须是数字|状态范围不合法'],
         ['activity_name','require|max:50','必须填活动名|活动名过长'],
         ['activity_starttime','require','必须填活动时间'],
         ['activity_endtime','require','必须填活动时间'],
         ['activity_location','require|max:50','必须填活动地点|活动地点过长'],
         ['activity_image','require','必须上传活动图片'],
         ['activity_cehua','require','必须上传活动策划书'],
         ['activity_abstract','require|max:100','必须填活动简介|活动简介过长'],
         ['editorValue','require|max:65535','必须填活动内容|活动内容过长'],


    ];
    protected $scene =[
        'Activity_status'=>['news_id','news_status'],
	];
}

