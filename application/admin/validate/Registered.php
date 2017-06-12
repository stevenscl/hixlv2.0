<?php
namespace app\admin\validate;
use think\Validate;
class Registered extends Validate {
	protected $rule = [
		['registered_name','require|max:20','角色名称不能为空|角色名称长度不能超过20个字符'],
		['registered_password','require|max:20','角色名称不能为空|角色名称长度不能超过20个字符'],
		['registered_email','require|max:20','角色名称不能为空|角色名称长度不能超过20个字符'],
	];
}