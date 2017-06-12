<?php
namespace app\common\validate;
use think\Validate;
class Login extends Validate {
	protected $rule = [
		['registered_id','require|max:11','学号不能为空|学号不能超过11个字符'],
		['registered_name','require|max:20','姓名不能为空|姓名长度不能超过11个字符'],
		['registered_password','require|max:20','密码不能为空|密码长度不能超过11个字符'],
		['registered_email','require|email','邮箱不能为空|请输入合法的邮箱格式'],
	];

	protected $scene = [
		'registered' => ['registered_id','registered_name','registered_password','registered_email'],
		'login' => ['registered_id','registered_password'],
	];
}