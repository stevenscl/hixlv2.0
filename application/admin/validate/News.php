<?php
namespace app\admin\Validate;

use think\Validate;

class News extends Validate
{
    protected $rule = [
         ['news_status','number|in:-1,0,1','状态字必须是数字|状态范围不合法'],
         ['news_sources','require|max:20','必须填新闻来源|新闻来源长度过长'],
         ['news_author','require|max:20','必须填新闻作者|新闻作者长度过长'],
         ['news_image','require','必须上传新闻图片'],
         ['news_title','require|max:50','必须填新闻标题|新闻标题过长'],
         ['news_title2','require|max:20','必须填新闻简略标题|新闻简略标题过长'],
         ['news_abstract','require|max:200','必须填新闻摘要|新闻摘要长度过长'],
         ['news_key','require|max:20','必须填关键字|关键字长度过长'],
         ['editorValue','require|max:65535','必须填新闻内容|新闻内容过长'],

    ];
    protected $scene =[
        'news_status'=>['news_id','news_status'],
	];
}


