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
class SystemConfigurationController extends BaseController
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
            '部门管理' => arU('departmentManagement'),
            '岗位管理' => arU('postManagement'),
        );
        $this->assign(array('headnavs' => $headnavs));

    }

    // 权限分配
    public function rightsAssignAction()
    {
        if ($data = arPost()) :
            if ($pid = $data['pid']) :
                $condition = array(
                    'pid' => $pid,
                );
                $lids = implode(',', $data['lids']);
                $rightsData = array(
                    'pid' => $pid,
                    'lids' => $lids,
                );
                $exist = CompanySchoolPostAuthorityModel::model()->getDb()->where($condition)->count();
                if ($exist > 0) :
                    CompanySchoolPostAuthorityModel::model()->getDb()->where($condition)->update($rightsData);
                else :
                    CompanySchoolPostAuthorityModel::model()->getDb()->insert($rightsData);
                endif;
                $this->redirectSuccess(array('', array('pid' => $pid)));
            else :
                $this->redirectError('');
            endif;
        endif;

        if ($pid = arGet('pid')) :
            $hasRights = CompanySchoolPostAuthorityModel::model()->getDb()
                ->where(array('pid' => $pid))
                ->queryRow();
            if ($hasRights['lids']) :
                $hasRights = explode(',', $hasRights['lids']);
            else :
                $hasRights = array();
            endif;
            $this->assign(array('hasRights' => $hasRights));
        else :
            $this->redirectError('ar_up', 'pid参数错误');
        endif;

        $authSet = AuthSetModel::model()->getAllMenuBysid(1, true, array('school_id' => arCfg('current_member.sid')));
        $authDetail = AuthSetModel::model()->getDetailInfo($authSet);
        // var_dump($authDetail);
        // exit;
        // 无导航
        $this->assign(array('nonav' => true, 'authDetail' => $authDetail));
        $this->display();

    }

    // 部门管理
    public function departmentManagementAction()
    {
        if ($data = arPost()) :
            $data['sid'] = arCfg('current_member.sid');
            if ($did = arRequest('did')) :
                if ($did == '1') :
                    unset($data['pdid']);
                endif;
                // 写入数据库
                $insertResult = CompanySchoolDepartmentModel::model()
                    ->getDb()
                    ->where(array('did' => $did))
                    ->update($data, 1);
            else :
                // 写入数据库
                $insertResult = CompanySchoolDepartmentModel::model()->getDb()->insert($data, 1);
            endif;
            if ($insertResult) :
                $this->redirectSuccess();
            else :
                $this->redirectError();
            endif;
        endif;
        $condition = array('sid' => arCfg('current_member.sid'));
        // 获取数据
        $departMents = CompanySchoolDepartmentModel::model()->getDb()
            ->where($condition)
            ->order('sorder desc')
            ->queryAll();
        // 获取分组明细 一维数组及多维数组可传入
        $departMents = CompanySchoolDepartmentModel::model()->getDetailInfo($departMents);

        // 资源列表
        $departLevel = CompanySchoolDepartmentModel::model()->sortAll($condition);

        // exit;
        // 分配模板
        $this->assign(array('departMents' => $departMents, 'departLevel' => $departLevel));

        $this->display('departmentManagement');

    }

    // 删除部门
    public function departmentDelAction()
    {
        if ($did = arRequest('did')) :

            if ($did == '1') :
                return $this->showJsonError('根节点不能删除');
            endif;
            $parentExist = CompanySchoolDepartmentModel::model()->getDb()->where(array('pdid' => $did))->count();

            if ($parentExist) :
                $this->showJsonError('此部门下面还存在子部门');
            else :
                $delResult = CompanySchoolDepartmentModel::model()
                    ->getDb()
                    ->where(array('did' => $did))
                    ->delete();
                if ($delResult) :
                    $this->showJsonSuccess();
                else :
                    $this->showJsonError('此部门下面还存在子部门，不能删除');
                endif;
            endif;
        else :
            $this->showJsonError('参数错误');
        endif;

    }

    // 岗位管理
    public function postManagementAction()
    {
        if ($data = arPost()) :
            $data['sid'] = arCfg('current_member.sid');
            if ($pid = arRequest('pid')) :
                if ($pid == '1') :
                    unset($data['ppid']);
                endif;
                // 写入数据库
                $insertResult = CompanySchoolPostModel::model()
                    ->getDb()
                    ->where(array('pid' => $pid))
                    ->update($data, 1);
            else :
                // 写入数据库
                $insertResult = CompanySchoolPostModel::model()->getDb()->insert($data, 1);
            endif;
            if ($insertResult) :
                $this->redirectSuccess();
            else :
                $this->redirectError();
            endif;
        endif;

        $condition = array('sid' => arCfg('current_member.sid'));
        // 获取数据
        $posts = CompanySchoolPostModel::model()->getDb()
            ->where($condition)
            ->order('sorder desc')
            ->queryAll();
        // 获取分组明细 一维数组及多维数组可传入
        $posts = CompanySchoolPostModel::model()->getDetailInfo($posts);

        // 资源列表
        $postLevel = CompanySchoolPostModel::model()->sortAll($condition);
        // exit;
        // 分配模板
        $this->assign(array('posts' => $posts, 'postLevel' => $postLevel));

        $this->display('');

    }

    // 删除职位
    public function postDelAction()
    {
        if ($pid = arRequest('pid')) :
            if ($pid == '1') :
                return $this->showJsonError('根节点不能删除');
            endif;
            $parentExist = CompanySchoolPostModel::model()->getDb()->where(array('ppid' => $pid))->count();

            if ($parentExist) :
                $this->showJsonError('此职位下面还存在子职位');
            else :
                $delResult = CompanySchoolPostModel::model()
                    ->getDb()
                    ->where(array('pid' => $pid))
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

    // 家园设置
    public function homelandSetAction()
    {
        $this->display('homelandSet');
    }

    // 短信设置
    public function messageSetAction()
    {
        $this->display('messageSet');
    }

    // 呼叫中心设置
    public function callcenterSetAction()
    {
        $this->display('callcenterSet');
    }

    // 开放日类型设置
    public function openingtypeAction()
    {
        $this->display('openingtype');
    }

    // 活动类型设置
    public function activitytypeAction()
    {
        $this->display('activitytype');
    }

    // 幼儿设置 家庭收入
    public function familyIncomeAction()
    {
        // $income = ParametSetModel::getData(array_keys(ParametSetModel::$paramet_type)[0]);
        // $this->assign(array('income' => $income));
        // $this->display('@InfantSet/familyIncome');
    }

    // 统一 post处理
    public function handlePost($module, $parameter = array())
    {
        // 预先处理下 post数据 方便统一可处理，
        $post = $module::formPost(arPost());
        // 处理 post 请求 编辑操作 返回一条编辑数据
        $result = arModule('User')->doPost($post);
        $data = $module::getData($parameter);
        $this->assign(array('data' => $data, 'result' => $result));

    }

     // 幼儿设置 幼儿类型
    public function infantTypeAction()
    {
        $this->handlePost('MemberTypeModel');
        $this->display('@InfantSet/infantType');
    }

    // 幼儿设置 来源渠道
    public function sourceCanalAction()
    {
        $this->handlePost('MemberSourceModel');
        $this->display('@InfantSet/sourceCanal');
    }

     // 幼儿设置 跟进方式
    public function followUpAction()
    {
        // $this->handlePost('ParametSetModel',array_keys(ParametSetModel::$paramet_type)[1]);
        // $this->display('@InfantSet/followUp');
    }

    // 幼儿设置 跟进方式
    public function guardianAction()
    {
        $this->display('@InfantSet/guardian');
    }

    // 幼儿设置 幼儿字段
    public function  infantfieldsAction()
    {
        $this->display('@InfantSet/infantfields');
    }

    // 幼儿设置 幼儿点评
    public function  infantCommentAction()
    {
        $this->display('@InfantSet/infantComment');
    }

     // 幼儿设置 幼儿标签设置
    public function  infantLabelsAction()
    {
        $this->display('@InfantSet/infantLabels');
    }

    // 教务设置 学期设置
    public function  termSetAction()
    {
        $this->display('@EducationSet/termSet');
    }

    // 教务设置 年级设置
    public function  gradeSetAction()
    {
        $this->display('@EducationSet/gradeSet');
    }

    // 教务设置 课程设置
    public function  courseSetAction()
    {
        $this->display('@EducationSet/courseSet');
    }

    // 教务设置 才艺课程设置
    public function  ArtcourseSetAction()
    {
        $this->display('@EducationSet/ArtcourseSet');
    }

     // 教务设置 教室设置
    public function  classroomSetAction()
    {
        $this->display('@EducationSet/classroomSet');
    }

    // 教务设置 教室设置
    public function  holidaySetAction()
    {
        $this->display('@EducationSet/holidaySet');
    }

    // 教务设置 课程体系设置
    public function  CourseSystemSetAction()
    {
        $this->display('@EducationSet/CourseSystemSet');
    }

     // 教务设置 上课节点
    public function  CourseTimeAction()
    {
        $this->display('@EducationSet/CourseTime');
    }

    // 教务设置 课程其它设置
    public function  CourseOthersSetAction()
    {
        $this->display('@EducationSet/CourseOthersSet');
    }

    // 教务设置 请假类型设置
    public function  holidaytypeSetAction()
    {
        $this->display('@EducationSet/holidaytypeSet');
    }

    // 教务设置 请假设置
    public function  ApholidaySetAction()
    {
        $this->display('@EducationSet/ApholidaySet');
    }

    // 财务设置 收入科目
    public function  IncomeAccountAction()
    {
        $this->display('@Finance/IncomeAccount');
    }

    // 财务设置 支出科目
    public function  CostAccountAction()
    {
        $this->display('@Finance/CostAccount');
    }

    // 财务设置 财务账号
    public function  financeAccountAction()
    {
        $this->display('@Finance/financeAccount');
    }

    // 财务设置 收费科目
    public function  chargeAccountAction()
    {
        $this->display('@Finance/chargeAccount');
    }

    // 财务设置 审核设置
    public function  checkSetAction()
    {
        $this->display('@Finance/checkSet');
    }

    // 财务设置 工资构成
    public function  SalaryConstituteAction()
    {
        $this->display('@Finance/SalaryConstitute');
    }

    // 办公设置 公告类别
    public function  AnnouncementTypeAction()
    {
        $this->display('@Officalwork/AnnouncementType');
    }

    // 办公设置 请假类型
    public function  AskVacationTypeAction()
    {
        $this->display('@Officalwork/AskVacationType');
    }

    // 办公设置 园务类型
    public function  WorkTypeAction()
    {
        $this->display('@Officalwork/WorkType');
    }

    // 办公设置 费用类型
    public function  CostTypeAction()
    {
        $this->display('@Officalwork/CostType');
    }

    // 办公设置 费用类型
    public function  CheckSetsAction()
    {
        $this->display('@Officalwork/CheckSets');
    }

    // 办公设置 费用类型
    public function   ratedSetsAction()
    {
        $this->display('@Officalwork/ratedSets');
    }

    // 办公设置 教师评价表
    public function   TeacherratedListAction()
    {
        $this->display('@Officalwork/TeacherratedList');
    }

    // 办公设置 星级设置
    public function   CounterStarSetAction()
    {
        $this->display('@Officalwork/CounterStarSet');
    }

    // 办公设置 考勤规则设置
    public function   CheckAttendanceSetsAction()
    {
        $this->display('@Officalwork/CheckAttendanceSets');
    }

    // 办公设置 达标程度设置
    public function   ReachDegreeSetsAction()
    {
        $this->display('@Officalwork/ReachDegreeSets');
    }

    // 卫生设置
    public function healthSetsAction()
    {
        $this->display('healthSets');
    }

    //饮食设置 食材类型
    public function   foodTypeAction()
    {
        $this->display('@DietSets/foodType');
    }

     //饮食设置 餐点管理
    public function   mealManagementAction()
    {
        $this->display('@DietSets/mealManagement');
    }

    //饮食设置 常规设置
    public function   regularSetsAction()
    {
        $this->display('@DietSets/regularSets');
    }

    //资产设置 物品类型
    public function   goodsTypeAction()
    {
        $this->display('@AssetSets/goodsType');
    }

    //资产设置 仓库信息
    public function   WharehouseInfoAction()
    {
        $this->display('@AssetSets/WharehouseInfo');
    }

    //资产设置 物品单位
    public function   goodsUnitAction()
    {
        $this->display('@AssetSets/goodsUnit');
    }

     // 连锁设置 物料分类
    public function materialClassifyAction()
    {
        $this->display('materialClassify');
    }

    // 连锁设置 物料单位
    public function materialUnitAction()
    {
        $this->display('materialUnit');
    }

}
