<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use think\File;
class Image extends Controller
{
	public function upload(){
		$file = Request::instance()->file('file');
        //给定目录
        $info = $file->move('avatar');
        if($info && $info->getPathname()){
        	return show(1,'success','/'.$info->getPathname());
        }
        return show(0,'upload error');

	}
	
	public function uploadifive(){
		$file = Request::instance()->file('file');
		$info = $file->move('avataruser');
		if($info && $info->getPathname()){
			return show(1,'success','/'.$info->getPathname());
        }
        return show(0,'upload error');
	}

	public function communitylogo(){
		$file = Request::instance()->file('file');
        //给定目录
        $info = $file->move('communitylogo');
        if($info && $info->getPathname()){
        	return show(1,'success','/'.$info->getPathname());
        }
        return show(0,'upload error');

	}

        public function news(){
                $file = Request::instance()->file('file');
        //给定目录
        $info = $file->move('news');
        if($info && $info->getPathname()){
                return show(1,'success','/'.$info->getPathname());
        }
        return show(0,'upload error');

        }

        public function activity(){
                $file = Request::instance()->file('file');
        //给定目录
        $info = $file->move('activity');
        if($info && $info->getPathname()){
                return show(1,'success','/'.$info->getPathname());
        }
        return show(0,'upload error');

        }

        public function file(){
                $file = Request::instance()->file('file');
        //给定目录
        $info = $file->move('cehua');
        if($info && $info->getPathname()){
                return show(1,'success','/'.$info->getPathname());
        }
        return show(0,'upload error');

        }

         public function communityimg(){
                $file = Request::instance()->file('file');
        //给定目录
        $info = $file->move('community');
        if($info && $info->getPathname()){
                return show(1,'success','/'.$info->getPathname());
        }
        return show(0,'upload error');

        }
}
