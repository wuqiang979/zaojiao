<?php
// 个人主页
class MineController extends BaseController
{
    public function init()
    {
        parent::init();

        // if (!arModule('Lib.Auth')->hasSysRights(CompanySchoolMemberModel::ROLE_YZ)) :
        //     echo '权限不足';
        //     exit;
        // endif;
        // 设置布局文件
        $this->setLayoutFile('content');
        $headnavs = array(
            '用户管理' => arU('info'),
            '密码设置' => arU('account'),
        );
        $this->assign(array('headnavs' => $headnavs));

    }

    // 显示用户
    public function infoAction()
    {
        if ($mid = arRequest('mid')) :
            if (!arModule('Lib.Auth')->isYz()) :
                exit("权限不足");
            endif;
            $member = CompanySchoolMemberModel::model()->getDb()->where(array('mid' => $mid))->queryRow();
            $member = CompanySchoolMemberModel::model()->getDetailInfo($member);
        else :
            $mid = arCfg('current_member.mid');
            $member = arCfg('current_member');
        endif;

        if ($data = arPost()) :
            // 安全问题 attention
            unset($data['account_name']);
            unset($data['account_pwd']);
            unset($data['rgid']);
            unset($data['mid']);
            $data['sid'] = arCfg('current_member.sid');

            $update = CompanySchoolMemberModel::model()
                ->getDb()
                ->where(array('mid' => $mid))
                ->update($data, true);
            if ($update) :
                $this->redirectSuccess();
            endif;
        endif;

        $this->assign(array('member' => $member));
        $this->display();

    }

    // 账号设置
    public function accountAction()
    {
        if ($data = arPost()) :
            if ($data['oripass'] && $data['newpass'] && $data['rnewpass']) :
                if ($data['newpass'] != $data['rnewpass']) :
                    $this->redirectError('', '两次密码不一致');
                else :
                    $newMember = array(
                        'account_pwd' => CompanySchoolMemberModel::gPwd($data['newpass']),
                    );
                    $condition = array(
                        'mid' => arCfg('current_member.mid'),
                        'account_pwd' => CompanySchoolMemberModel::gPwd($data['oripass']),
                    );
                    if (CompanySchoolMemberModel::model()->getDb()->where($condition)->update($newMember)) :
                        $this->redirectSuccess('Account/loginOut', '请重新登录');
                    else :
                        $this->redirectError('', '原密码错误');
                    endif;
                endif;
            endif;
        endif;
        $this->display();

    }

}
