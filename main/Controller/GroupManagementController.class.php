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
class GroupManagementController extends BaseController
{
    public function init()
    {
        parent::init();

        if (!arModule('Lib.Auth')->hasSysRights(CompanySchoolMemberModel::ROLE_COMPANY_ADMIN)) :
            echo '权限不足';
            exit;
        endif;

        // 设置布局文件
        $this->setLayoutFile('content');

        $headnavs = array(
            '公司管理' => arU('companyManagement'),
            '园区管理' => arU('schoolManagement'),
        );
        $this->assign(array('headnavs' => $headnavs));

    }

    // 公司管理
    public function companyManagementAction()
    {
        if ($data = arPost()) :
            if ($cid = arRequest('cid')) :
                // 写入数据库
                $insertResult = CompanyModel::model()
                    ->getDb()
                    ->where(array('cid' => $cid))
                    ->update($data, 1);
            else :
                $data['addtime'] = time();
                // 写入数据库
                $insertResult = CompanyModel::model()->getDb()->insert($data, 1);
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
            $condition['name like '] = '%' . $searchtext . '%';
        endif;

        // 限制条件
        if (arCfg('current_member.rgid') == CompanySchoolMemberModel::ROLE_COMPANY_ADMIN) :
            $condition['cid'] = arCfg('current_member.cid');
        endif;

        // 分页计算
        $total = CompanyModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 10);

        // 获取数据
        $companys = CompanyModel::model()
            ->getDb()
            ->where($condition)
            ->order('cid desc')
            ->limit($page->limit())
            ->queryAll();

        // 获取关联表详细信息
        $companys = CompanyModel::model()->getDetailInfo($companys);

        // 分配模板
        $this->assign(array('companys' => $companys, 'page' => $page));

        $this->display('companyManagement');

    }

    // 删除部门
    public function companyDelAction()
    {
        if ($cid = arRequest('cid')) :
            $delResult = CompanyModel::model()
                ->getDb()
                ->where(array('cid' => $cid))
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

    // 添加管理员
    public function addCompanyManagerAction()
    {
        $cid = arRequest('cid');
        $account_name = arRequest('adminuser');
        $account_pwd = arRequest('adminpwd');
        if ($cid && $account_pwd && $account_name) :
            $existNameCount = CompanySchoolMemberModel::model()
                ->getDb()
                ->where(array('account_name' => $account_name))
                ->count();
            if ($existNameCount > 0) :
                return $this->showJsonError('用户名重复');
            else :
                // 清理之前有的账户
                CompanySchoolMemberModel::model()->getDb()
                    ->where(array('cid' => $cid))
                    ->delete();
                $member = array(
                    'cid' => $cid,
                    'account_name' => $account_name,
                    // 全站统一加密登录
                    'account_pwd' => CompanySchoolMemberModel::gPwd($account_pwd),
                    // 超级管理员
                    'rgid' => CompanySchoolMemberModel::ROLE_COMPANY_ADMIN,
                    // 注册时间
                    'reg_time' => time(),
                );
                $insertResult = CompanySchoolMemberModel::model()->getDb()
                    ->insert($member);
                if ($insertResult) :
                    return $this->showJsonSuccess('');
                else :
                    return $this->showJsonError('数据更新错误');
                endif;
            endif;
        else :
            return $this->showJsonError('参数错误');
        endif;

    }

    // 校园管理
    public function schoolManagementAction()
    {
        if ($data = arPost()) :
            if ($sid = arRequest('sid')) :
                // 写入数据库
                $insertResult = CompanySchoolModel::model()
                    ->getDb()
                    ->where(array('sid' => $sid))
                    ->update($data, 1);
            else :
                $data['addtime'] = time();
                // 写入数据库
                $insertResult = CompanySchoolModel::model()->getDb()->insert($data, 1);
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
            $condition['name like '] = '%' . $searchtext . '%';
        endif;
        if ($cid = arRequest('cid')) :
            $condition['cid'] = $cid;
        endif;

         // 限制条件
        if (arCfg('current_member.rgid') == CompanySchoolMemberModel::ROLE_COMPANY_ADMIN) :
            $condition['cid'] = arCfg('current_member.cid');
            $companys = CompanyModel::model()->getDb()->where($condition)->queryAll();
        else :
            // 公司列表
            $companys = CompanyModel::model()->getDb()->queryAll();
        endif;

        // 分页计算
        $total = CompanySchoolModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 10);

        // 获取数据
        $schools = CompanySchoolModel::model()
            ->getDb()
            ->where($condition)
            ->order('cid desc')
            ->limit($page->limit())
            ->queryAll();

        // 获取关联表详细信息
        $schools = CompanySchoolModel::model()->getDetailInfo($schools);

        // 分配模板
        $this->assign(array('schools' => $schools, 'companys' => $companys, 'page' => $page));

        $this->display('schoolManagement');

    }

    // 删除园区
    public function schoolDelAction()
    {
        if ($sid = arRequest('sid')) :
            $delResult = CompanySchoolModel::model()
                ->getDb()
                ->where(array('sid' => $sid))
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

    // 添加园长
    public function addSchoolManagerAction()
    {
        $sid = arRequest('sid');
        $account_name = arRequest('adminuser');
        $account_pwd = arRequest('adminpwd');
        if ($sid && $account_pwd && $account_name) :
            // 清理之前有的账户
            CompanySchoolMemberModel::model()->getDb()
                ->where(array('rgid' => CompanySchoolMemberModel::ROLE_YZ, 'sid' => $sid))
                ->delete();
            $post = arPost();

            $member = array(
                // 学校id
                'sid' => $sid,
                // 公司id
                'cid' => CompanySchoolModel::model()->getDb()
                    ->where(array('sid' => $sid))
                    ->queryColumn('cid'),
                'account_name' => $account_name,
                // 全站统一加密登录
                'account_pwd' => CompanySchoolMemberModel::gPwd($account_pwd),
                // 超级管理员
                'rgid' => CompanySchoolMemberModel::ROLE_YZ,
                // 注册时间
                'reg_time' => time(),
                // 状态 默认通过
                'status' => CompanySchoolMemberModel::STATUS_APPROVE,
            );
            $member = array_merge($post, $member);
            $insertResult = CompanySchoolMemberModel::model()->getDb()
                ->insert($member, true);
            if ($insertResult) :
                return $this->showJsonSuccess('');
            else :
                return $this->showJsonError('数据更新错误');
            endif;
        else :
            return $this->showJsonError('参数错误');
        endif;

    }

}
