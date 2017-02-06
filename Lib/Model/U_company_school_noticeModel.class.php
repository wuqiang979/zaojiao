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
class U_company_school_noticeModel extends ArModel
{
    // 表名
    public $tableName = 'u_company_school_notice';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 分类 classify
    static $CLASSIFY_MAP = array(
         1 => '行政管理',
         2 => '教师通知',
         3 => '学生通知'
    );

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

            $bundle['sid'] = U_company_schoolModel::model()
                ->getDb()
                ->where(array('sid' => $bundle['sid']))
                ->queryAll();

            return $bundle;
        endif;

        return $bundles;

    }

}