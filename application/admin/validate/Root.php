<?php
namespace app\admin\validate;
use think\Validate;
class Root extends Validate {
	protected $rule = [
		['root_name','require|max:20','角色名称不能为空|角色名称长度不能超过20个字符'],
	];
}