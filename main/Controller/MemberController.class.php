<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author assnr <ycassnr@gmail.com>
 */

/**
 * Default Controller of webapp.
 */

// 部门管理
class MemberController extends BaseController
{
    public function init()
    {
        parent::init();

        if (!arModule('Lib.Auth')->hasSysRights(CompanySchoolMemberModel::ROLE_YZ)) :
            echo '权限不足';
            exit;
        endif;
        // 设置布局文件
        $this->setLayoutFile('content');
        $headnavs = array(
            '用户管理' => arU('memberManagement'),
        );
        $this->assign(array('headnavs' => $headnavs));

    }

    // 成员管理
    public function memberManagementAction()
    {
        if ($data = arPost()) :
            if ($mid = arRequest('mid')) :
                if ($data['pwd']) :
                    // 修改面
                    $data['account_pwd'] = CompanySchoolMemberModel::gPwd($data['pwd']);
                endif;
                // 写入数据库
                $insertResult = CompanySchoolMemberModel::model()
                    ->getDb()
                    ->where(array('mid' => $mid))
                    ->update($data, 1);
            else :
                $data['reg_time'] = time();
                // $data['status'] = CompanySchoolMemberModel::STATUS_APPROVE;
                $data['rgid'] = CompanySchoolMemberModel::ROLE_STAFF;
                if ($pwd = $data['pwd']) :
                    // 加密通用方法
                    $pwd = CompanySchoolMemberModel::gPwd($pwd);
                else :
                    // 默认密码123456
                    $pwd = CompanySchoolMemberModel::gPwd('123456');
                endif;
                $data['account_pwd'] = $pwd;

                // 写入数据库
                $insertResult = CompanySchoolMemberModel::model()->getDb()->insert($data, 1);
            endif;
            if ($insertResult) :
                $this->redirectSuccess();
            else :
                $this->redirectError();
            endif;
        endif;

        // 搜索条件
        $condition = array(
        );
        if ($searchtext = urldecode(arRequest('searchtext'))) :
            $condition['account_name like '] = '%' . $searchtext . '%';
        endif;
        if ($cid = arRequest('cid')) :
            $condition['cid'] = $cid;
        endif;

        // 限制条件
        if (arCfg('current_member.rgid') == CompanySchoolMemberModel::ROLE_YZ) :
            $condition['sid'] = arCfg('current_member.sid');
        endif;
        // 只能查看比自己权限低的用户
        $condition['rgid <'] = arCfg('current_member.rgid');

        // 分页计算
        $total = CompanySchoolMemberModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 20);

        // 获取数据
        $members = CompanySchoolMemberModel::model()
            ->getDb()
            ->where($condition)
            ->order('mid desc')
            ->limit($page->limit())
            ->queryAll();

        if (arCfg('current_member.rgid') <= CompanySchoolMemberModel::ROLE_YZ) :
            // 学校
            $schools = CompanySchoolModel::model()
                ->getDb()
                ->where(array('sid' => arCfg('current_member.sid')))
                ->queryAll();
        else :
            // 学校
            $schools = CompanySchoolModel::model()->getDb()->queryAll();
        endif;

        // 获取关联表详细信息
        $members = CompanySchoolMemberModel::model()->getDetailInfo($members);

        // 分配模板
        $this->assign(array('members' => $members, 'schools' => $schools, 'page' => $page));

        $this->display('');

    }

    // 删除园区
    public function memberDelAction()
    {
        if ($mid = arRequest('mid')) :
            $delResult = CompanySchoolMemberModel::model()
                ->getDb()
                ->where(array('mid' => $mid))
                ->delete();
            // 删除档案表
            CompanySchoolMemberProfileModel::model()
                ->getDb()
                ->where(array('mid' => $mid))
                ->delete();
            if ($delResult) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError('');
            endif;
        else :
            $this->showJsonError('参数错误');
        endif;

    }

    // 部门分配
    public function departmentsAssignAction()
    {
        if ($data = arPost()) :
            if ($mid = $data['mid']) :
                $condition = array(
                    'mid' => $mid,
                );
                $dids = implode(',', $data['dids']);
                $rightsData = array(
                    'mid' => $mid,
                    'dids' => $dids,
                );
                $exist = CompanySchoolMemberProfileModel::model()->getDb()->where($condition)->count();
                if ($exist > 0) :
                    CompanySchoolMemberProfileModel::model()->getDb()->where($condition)->update($rightsData);
                else :
                    CompanySchoolMemberProfileModel::model()->getDb()->insert($rightsData);
                endif;
                $this->redirectSuccess(array('', array('mid' => $mid)));
            else :
                $this->redirectError('');
            endif;
        endif;

        if ($mid = arGet('mid')) :
            $hasRights = CompanySchoolMemberProfileModel::model()->getDb()
                ->where(array('mid' => $mid))
                ->queryRow();
            if ($hasRights['dids']) :
                $hasRights = explode(',', $hasRights['dids']);
            else :
                $hasRights = array();
            endif;
            $this->assign(array('hasRights' => $hasRights));
        else :
            $this->redirectError('ar_up', 'mid参数错误');
        endif;

        $departSet = CompanySchoolDepartmentModel::model()->getAllMenuByDid(1, true);
        $departDetail = CompanySchoolDepartmentModel::model()->getDetailInfo($departSet);

        $this->assign(array('nonav' => true, 'departDetail' => $departDetail));
        $this->display();

    }

    // 岗位分配
    public function postsAssignAction()
    {
        if ($data = arPost()) :
            if ($mid = $data['mid']) :
                $condition = array(
                    'mid' => $mid,
                );
                if ($mid == arCfg('current_member.mid')) :
                    $this->redirectError('ar_up', '不能为自己分配岗位');
                endif;
                $postids = implode(',', $data['postids']);
                $rightsData = array(
                    'mid' => $mid,
                    'postids' => $postids,
                );
                $exist = CompanySchoolMemberProfileModel::model()->getDb()->where($condition)->count();
                if ($exist > 0) :
                    CompanySchoolMemberProfileModel::model()->getDb()->where($condition)->update($rightsData);
                else :
                    CompanySchoolMemberProfileModel::model()->getDb()->insert($rightsData);
                endif;
                $this->redirectSuccess(array('', array('mid' => $mid)));
            else :
                $this->redirectError('');
            endif;
        endif;

        if ($mid = arGet('mid')) :
            $hasRights = CompanySchoolMemberProfileModel::model()->getDb()
                ->where(array('mid' => $mid))
                ->queryRow();
            if ($hasRights['postids']) :
                $hasRights = explode(',', $hasRights['postids']);
            else :
                $hasRights = array();
            endif;
            $this->assign(array('hasRights' => $hasRights));
        else :
            $this->redirectError('ar_up', 'mid参数错误');
        endif;

        $postSet = CompanySchoolPostModel::model()->getAllMenuByPid(0, true, array('sid' => arCfg('current_member.sid')));
        $postDetail = CompanySchoolPostModel::model()->getDetailInfo($postSet);
        // 无导航
        $this->assign(array('nonav' => true, 'postDetail' => $postDetail));
        $this->display();

    }

}
