<?php
namespace app\admin\controller;
use think\Controller;
class Login extends  Controller
{
    public function index()
    {
        if(request()->isPost()) {
            // 登录的逻辑
            //获取相关的数据
            $data = input('post.');
            // 通过用户名 获取 用户相关信息
            // 严格的判定
            $ret = model('Registeredinfo')->get(['registered_id'=>$data['registered_id']]);

            if(!$ret || $ret->registered_status !=1) {
                $this->error('改用户不存在，或者用户没有相应权限');
            }

            if($ret->registered_password != md5($data['registered_password'].$ret->registered_code)) {
                $this->error('密码不正确');
            }
            $root = model('Member')->get(['registered_id'=>$data['registered_id']])->root_id;
            if($root!=2 && $root!=3) {
                $this->error('改用户不存在，或者用户没有相应权限');
            }

           /* model('BisAccount')->updateById(['last_login_time'=>time()], $ret->id);*/
            // 保存用户信息  bis是作用域

            session('registered', $ret, 'admin');

            if($root == 2){
            return $this->success('登录成功', url('index/index2'));
           }else{
            return $this->success('登录成功', url('index/index'));
        }


        }else {
            // 获取session
            $account = session('registered', '', 'admin');
            if($account && $account->registered_id) {
                $community = model('member')->get(intval($account->registered_id));
                if ($community->root_id==3) {
                return $this->redirect(url('index/index'));
            }
                return $this->redirect(url('index/index2'));
            }
        
            return $this->fetch();
        }
    }

    public function logout() {
        // 清除session
        session(null, 'admin');
        // 跳出
        $this->redirect('login/index');
    }
}