<?php

class AccountController extends ArController
{
	// 初始加载
	public function init()
    {
         arSeg(array(
                'loader' => array(
                    'plugin' => 'layer',
                    'this' => $this
                )
            )

            );
    }

	// 注册
	public function registerAction()
	{
		if ($data = arPost()):
			// 发送手机验证码
			if(isset($data['sign']) && $data['sign'] == 'sendcode'):
				if(isset($data['phone']) && $data['phone'] ):
					$mobllestr = arModule('Lib.PhoneCode')->random(6, 1);
					if(arModule('Lib.PhoneCode')->sendPhoneCode($mobllestr, $data['phone'])): 
						$send = 1;
						setcookie('PhoneCode', $mobllestr, time()+120);
					else:
						$send = 0;
					endif;
				else:
					$send = 2;
				endif;
				
				echo json_encode($send);
				exit;
			endif;
			// 手机验证码验证
			if(!empty($_COOKIE['PhoneCode'])):
				if($data['PhoneCode'] != $_COOKIE['PhoneCode']):
					$this->redirectError('Account/register', '验证码错误');
				endif;
			else:
				$this->redirectError('Account/register', '手机验证码失效，请重新获取验证码');
			endif;
			if ($data['phone'] && $data['password']) :
			else :
				$this->redirectError('Account/register', '注册失败,账号或密码不能为空');
			endif;
			$repeat = CompanySchoolMemberModel::model()->getDb()->where(array('mobile' => $data['phone']))->queryRow();
			if ($repeat):
				$this->redirectError('Account/login','此手机号已经注册,请直接登录');
			else:
				$cid = CompanyModel::model()->getDb()->insert($data, true);
				$data['account_name'] = $data['phone'];
				$data['account_pwd'] =CompanySchoolMemberModel::gPwd($data['password']);
				$data['cid'] = $cid;
				$data['rgid'] = CompanySchoolMemberModel::ROLE_COMPANY_ADMIN;
				$insert = CompanySchoolMemberModel::model()->getDb()->insert($data, true);
				if($insert):
					$this->redirectSuccess('Account/login', '注册成功');
				else:
					$this->redirectError('Account/register', '注册失败,请重新注册');
				endif;
			endif;
		endif;
        $this->setLayOutfile('');
		$this->display('register');
	}

	// 登陆
	public function indexAction()
	{
		$this->redirect('login');

	}

	// 登陆
	public function loginAction()
	{
 		if (!arModule('Lib.Auth')->isLoginIn()):
			$this->display('login');
		else:
			$this->redirectSuccess('Layout/index', '您已经登录');
		endif;

	}

	// 登录验证
	public function loginMangAction()
	{
		if (!arModule('Lib.Auth')->isLoginIn()):
			if ($data = arPost()):
				$username = $data['username'];
				$password = $data['password'];
				if ($username && $password) :
					$pass = CompanySchoolMemberModel::gPwd($password);
					$member = CompanySchoolMemberModel::model()
						->getDb()
						->where(array('account_name' => $username, 'account_pwd' => $pass))
						->queryRow();
					if ($member):
						if (!arModule('Lib.Auth')->isValidMember($member['mid'])) :
							$this->redirectError('Account/login', '此用户已被禁止');
						else :
							// 登录初始化
							arModule('Lib.Member')->onUserLoginInItialization($member['mid']);

							$this->redirectSuccess('Layout/index', '登录成功');
						endif;
					else:
						$this->redirectError('Account/login', '用户名或密码错误');
					endif;
				else :
					$this->redirectError('Account/login', '用户名或密码为空');
				endif;
			endif;
		else:
			$this->redirectSuccess('Layout/index','您已经登录');
		endif;
	}

	// 退出登录
	public function loginOutAction()
	{
		// 登出
		arModule('Lib.Member')->onUserLogoutInItialization();
	    $this->redirectSuccess('Account/login','注销登录成功！');

	}

}
