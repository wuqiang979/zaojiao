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
// 验证权限类
class AuthModule
{
    // 是否登录
    public function isLoginIn()
    {
        return !!arComp('list.session')->get('member');

    }

    // 检查用户是否正常状态
    public function isValidMember($mid)
    {
        $member = \CompanySchoolMemberModel::model()->getDb()
            ->where(array('mid' => $mid))
            ->queryRow();
        if ($member['status'] == \CompanySchoolMemberModel::STATUS_APPROVE) :
            return true;
        else :
            return false;
        endif;

    }

    // 获取用户权限
    public function getMemberAllAuthOrity($mid)
    {
        $member = \CompanySchoolMemberModel::model()->getDb()
            ->where(array('mid' => $mid))
            ->queryRow();
        $memberDetail = \CompanySchoolMemberModel::model()->getDetailInfo($member);
        return $memberDetail['posts']['auths'];

    }

    // 是否有权限
    public function hasRights($action = '', $auths, $checkAdmin = false)
    {
        // var_dump($action, $auths);
        // exit;
        if ($checkAdmin) :
            $ROLE_ID = arComp('list.session')->get('member.rgid');
            switch ($ROLE_ID) {
                case \CompanySchoolMemberModel::ROLE_YZ:
                case \CompanySchoolMemberModel::ROLE_COMPANY_ADMIN:
                case \CompanySchoolMemberModel::ROLE_PLATFORM_ADMIN:
                case \CompanySchoolMemberModel::ROLE_SUPER_ADMIN:
                case \CompanySchoolMemberModel::ROLE_SYSTEM_ADMIN:
                    return true;
                    break;
                default:
                    break;
            }
        endif;

        $hasResult = false;
        if (!$action) :
            $action = arCfg('requestRoute.a_m') . '/'
                    . arCfg('requestRoute.a_c') . '/'
                    . arCfg('requestRoute.a_a');
        endif;
        if (is_array($auths)) :
            foreach ($auths as $auth) :
                if ($auth['action'] == $action) :
                    $hasResult = true;
                    break;
                endif;
            endforeach;
        else :
        endif;
        return $hasResult;

    }

    // 检查是否有系统权限
    public function hasSysRights($rightLevel = '', $only = false)
    {
        if (!$rightLevel) :
            $rightLevel = \CompanySchoolMemberModel::ROLE_COMPANY_ADMIN;
        endif;

        if ($only) :
            if (arCfg('current_member.rgid') == $rightLevel) :
                return true;
            else :
                return false;
            endif;
        else:
            if (arCfg('current_member.rgid') >= $rightLevel) :
                return true;
            else :
                return false;
            endif;

        endif;

    }

    // 是否系统角色
    public function isSysRole()
    {
        if (arCfg('current_member.rgid') >= \CompanySchoolMemberModel::ROLE_COMPANY_ADMIN):
            return true;
        else :
            return false;
        endif;

    }

    // 是否用户角色
    public function isUserRole()
    {
        return !$this->isSysRole();

    }

    // 是否园长
    public function isYz()
    {
        // 获取用户的系统组id
        $rgid = arCfg('current_member.rgid');
        if ($rgid == \CompanySchoolMemberModel::ROLE_YZ) :
            return true;
        else :
            return false;
        endif;

    }

    // 是否公司
    public function isCompany()
    {
        // 获取用户的系统组id
        $rgid = arCfg('current_member.rgid');
        if ($rgid == \CompanySchoolMemberModel::ROLE_COMPANY_ADMIN) :
            return true;
        else :
            return false;
        endif;

    }

    // 获取班级组合名称select列表 使用seg显示 cid:班级ID  cname:select显示内容
    public function getClassSelect()
    {
        $sid = arCfg('current_member.sid');
        $class = \EduClassroomModel::model()->getDb()
            ->where(array('sid'=>$sid))
            ->order('class_type desc,cid asc')
            ->queryAll();
        $classSelect = \EduClassroomModel::model()->getClassNameSelect($class);
        return $classSelect;
    }

}
