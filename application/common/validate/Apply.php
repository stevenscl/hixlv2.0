<?php
namespace app\common\validate;
use think\Validate;
class Apply extends Validate {
	protected $rule = [
		['community_name','require|max:20','社团名称不能为空|社团名称不能超过20个字符'],
		['community_type','require|max:10','社团类型不能为空|社团类型不能超过10个字符'],
		['community_introduce','require','社团简介不能为空'],
		['community_reason','require','社团申请理由不能为空'],
	];

	protected $scene = [
		'applycommunity' => ['community_name','community_type','community_introduce','community_reason'],
	];
}