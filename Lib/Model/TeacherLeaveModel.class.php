<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ck <yunshaohunzhu@sina.com>
 */

/**
 * user 数据库模型.
 */
class TeacherLeaveModel extends ArModel
{
    // 表名
    public $tableName = 'u_company_school_member_leave';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 获取bundle详细信息 万能方法
    public function getDetailInfo(array $bundles)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($bundles)) :
            foreach ($bundles as &$bundle) :
                $bundle = $this->getDetailInfo($bundle);
            endforeach;
        else :
            $bundle = $bundles;
        	// 获取成员姓名
            $bundle['mname'] = CompanySchoolMemberModel::model()
                ->getDb()
                ->select('realname')
                ->where(array('mid' => $bundle['mid']))
                ->queryRow();

            return $bundle;
        endif;

        return $bundles;

    }

}