<?php
namespace app\common\validate;
use think\Validate;
class Join extends Validate {
	protected $rule = [
		['join_specialty','require','我的特长不能为空'],
		['join_reason','require','申请加入理由不能为空'],
	];
}