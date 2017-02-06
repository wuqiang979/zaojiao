<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ck <yushaohunzhu@sina.com>
 */

/**
 * Default Controller of webapp.
 */

// 部门管理
class AuthController extends BaseController
{
    public function init()
    {
        parent::init();

        if (!arModule('Lib.Auth')->hasSysRights(CompanySchoolMemberModel::ROLE_YZ, true)) :
            echo '权限不足';
            exit;
        endif;

        // 设置布局文件
        $this->setLayoutFile('content');

        $headnavs = array(
            '权限管理' => arU('authList'),
            '权限分组管理' => arU('authSet'),
        );
        $this->assign(array('headnavs' => $headnavs));

    }

    // 权限分组管理
    public function authSetAction()
    {
        if ($data = arPost()) :
            $data['school_id'] = arCfg('current_member.sid');
            if (!$data['name']) :
                $this->redirectError('权限分组名不能为空');
            endif;

            if ($sid = arRequest('sid')) :
                if ($sid == '1') :
                    unset($data['psid']);
                endif;
                // 写入数据库
                $insertResult = AuthSetModel::model()
                    ->getDb()
                    ->where(array('sid' => $sid))
                    ->update($data, 1);
            else :
                // 写入数据库
                $sid = $insertResult = AuthSetModel::model()->getDb()->insert($data, 1);
            endif;
            // 添加一个显示栏目权限
            $listCondition = array(
                'sid' => $sid,
                'name' => $data['name'] . '栏目查看',
                'action' => $data['name'] . 'show',
                'school_id' => arCfg('current_member.sid'),
                'sorder' => '99',
            );
            if (AuthListModel::model()->getDb()->where($listCondition)->count() == 0) :
                AuthListModel::model()->getDb()->insert($listCondition);
            endif;

            if ($insertResult) :
                $this->redirectSuccess();
            else :
                $this->redirectError();
            endif;
        endif;

        // 搜索条件
        $condition = array('school_id' => arCfg('current_member.sid'));

        // 获取数据
        $posts = AuthSetModel::model()->getDb()
            ->where($condition)
            ->order('sorder desc')
            ->queryAll();
        // 获取分组明细 一维数组及多维数组可传入
        $posts = AuthSetModel::model()->getDetailInfo($posts);

        // 资源列表
        $postLevel = AuthSetModel::model()->sortAll($condition);
        // exit;
        // 分配模板
        $this->assign(array('posts' => $posts, 'postLevel' => $postLevel));

        $this->display('');

    }

    // 删除分组
    public function setDelAction()
    {
        if ($sid = arRequest('sid')) :
            if ($sid == '1') :
                return $this->showJsonError('根节点不能删除');
            endif;
            $parentExist = AuthSetModel::model()->getDb()->where(array('psid' => $sid))->count();

            if ($parentExist) :
                $this->showJsonError('此职位下面还存在子职位');
            else :
                $delResult = AuthSetModel::model()
                    ->getDb()
                    ->where(array('sid' => $sid))
                    ->delete();
                if ($delResult) :
                    $this->showJsonSuccess();
                else :
                    $this->showJsonError('此职位下面还存在子职位，不能删除');
                endif;
            endif;
        else :
            $this->showJsonError('参数错误');
        endif;

    }


    // 权限管理
    public function authListAction()
    {
        if ($data = arPost()) :
            $data['school_id'] = arCfg('current_member.sid');
            if ($lid = arRequest('lid')) :
                // 写入数据库
                $insertResult = AuthListModel::model()
                    ->getDb()
                    ->where(array('lid' => $lid))
                    ->update($data, 1);
            else :
                // $data['addtime'] = time();
                // 写入数据库
                $insertResult = AuthListModel::model()->getDb()->insert($data, 1);
            endif;
            if ($insertResult) :
                $this->redirectSuccess();
            else :
                $this->redirectError();
            endif;
        endif;

        // 搜索条件
        $condition = array('school_id' => arCfg('current_member.sid'));
        if ($searchtext = urldecode(arRequest('searchtext'))) :
            $condition['name like '] = '%' . $searchtext . '%';
        endif;
        if ($sid = arRequest('sid')) :
            // 根节点
            if ($sid != 1) :
                $condition['sid'] = $sid;
            endif;
        endif;

        // set
        $sets = AuthSetModel::model()->sortAll(array('school_id' => arCfg('current_member.sid')));

        // 分页计算
        $total = AuthListModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 200);

        // 获取数据
        $auths = AuthListModel::model()
            ->getDb()
            ->where($condition)
            ->order('sid asc, sorder desc')
            ->limit($page->limit())
            ->queryAll();

        // 获取关联表详细信息
        $auths = AuthListModel::model()->getDetailInfo($auths);

        // 分配模板
        $this->assign(array('auths' => $auths, 'sets' => $sets, 'page' => $page));

        $this->display('');

    }

    // 删除园区
    public function authDelAction()
    {
        if ($lid = arRequest('lid')) :
            $delResult = AuthListModel::model()
                ->getDb()
                ->where(array('lid' => $lid))
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

}
