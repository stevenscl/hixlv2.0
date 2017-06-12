<?php
namespace app\admin\validate;
use think\Validate;
class Community extends Validate {
	protected $rule = [
		['community_id','require|max:11','社团ID不能为空|社团ID长度不能超过11个字符'],
		['community_name','require|max:50','社团名称不能为空|社团名称长度不能超过50个字符'],
		['community_introduction','max:200','社团简介长度不能超过200个字符'],
	];
}