<?php
/**
 * Powerd by ArPHP.
 *
 * Module.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */
namespace Lib\Module;
/**
 * useage arModule('Lib.Auth')->isLoginIn();
*/
// 用户调度类
class MemberModule
{
    // 登录初始化操作
    public function onUserLoginInItialization($mid)
    {
        $member = \CompanySchoolMemberModel::model()->getDb()
            ->where(array('mid' => $mid))
            ->queryRow();
        $memberDetail = \CompanySchoolMemberModel::model()->getDetailInfo($member);
        // 设置登录session
        arComp('list.session')->set('member', $memberDetail);
        // 初始化用户登录信息
        $updateMember = array(
            'last_logintime' => time(),
            'logintimes' => $member['logintimes'] + 1,
            'loginip' => arComp('tools.util')->getClientIp(),
            'isonline' => 1,
            'last_activitytime' => time(),
        );
        return \CompanySchoolMemberModel::model()->getDb()
            ->where(array('mid' => $mid))
            ->update($updateMember);

    }

    // 登录初始化操作
    public function onUserLogoutInItialization()
    {
        $mid = arComp('list.session')->get('member.mid');
        $member = \CompanySchoolMemberModel::model()->getDb()
            ->where(array('mid' => $mid))
            ->queryRow();
        // 初始化用户登录信息
        $updateMember = array(
            'isonline' => 0,
            'last_activitytime' => time(),
        );
        // 清除session
        arComp('list.session')->set('member', null);
        return \CompanySchoolMemberModel::model()->getDb()
            ->where(array('mid' => $mid))
            ->update($updateMember);

    }

    // 操作初始化操作
    public function onUserOperationInItialization()
    {
        $mid = arComp('list.session')->get('member.mid');
        $member = \CompanySchoolMemberModel::model()->getDb()
            ->where(array('mid' => $mid))
            ->queryRow();
        $member = \CompanySchoolMemberModel::model()->getDetailInfo($member);
        // 存入配置
        \Ar::setConfig('current_member', $member);
        if ($member['last_activitytime']) :
            $thisTimeOnlinetime = time() - $member['last_activitytime'];
        else :
            $thisTimeOnlinetime = 0;
        endif;
        // 初始化用户登录信息
        $updateMember = array(
            'onlinetime' => $member['onlinetime'] + $thisTimeOnlinetime,
            'isonline' => 1,
            'last_activitytime' => time(),
        );
        return \CompanySchoolMemberModel::model()->getDb()
            ->where(array('mid' => $mid))
            ->update($updateMember);

    }

}
