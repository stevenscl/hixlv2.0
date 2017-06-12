<?php
namespace app\common\model;

use think\Model;

class Communityimg extends Model
{
	public function countimg()
	{
		$result = $this->count();
		return $result;
	}
	public function imginfo($id)
	{
		$result = $this->where('community_id','=',$id)->select();
		return $result;
	}
	public function getimg($id)
	{
		$result = $this->where('community_id','=',$id)->order('img_listorder','asc')->column('community_image');
		return $result;
	}  
}