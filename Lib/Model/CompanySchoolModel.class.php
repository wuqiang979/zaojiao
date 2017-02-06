<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 *  数据库模型.
 */
class CompanySchoolModel extends ArModel
{
    // 类型
    static $TYPE_MAP = array(
         0 => '幼教',
         1 => '早教',
         2 => '培训机构',
         3 => '其他',
    );

    // 状态
    static $STATUS_MAP = array(
         0 => '审核中',
         1 => '通过',
         2 => '停运',
    );

    // 集团学校表
    public $tableName = 'u_company_school';

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
            /**
             * to do what you want
             * $bundle['????'] = '???';
             */
            // 获取超级管理员
            $bundle['adminuser'] = CompanySchoolMemberModel::model()
                ->getDb()
                ->select('account_name, mobile, realname')
                ->where(array('sid' => $bundle['sid'], 'rgid' => CompanySchoolMemberModel::ROLE_YZ))
                ->queryRow();

            $bundle['company'] = CompanyModel::model()
                ->getDb()
                ->where(array('cid' => $bundle['cid']))
                ->queryRow();
            return $bundle;
        endif;

        return $bundles;

    }

}
