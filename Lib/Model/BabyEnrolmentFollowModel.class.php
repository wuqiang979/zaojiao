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
class BabyEnrolmentFollowModel extends ArModel
{
    // 集团学校部门表
    public $tableName = 'u_baby_enrolment_follow';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 类型
    static $TYPE_MAP = array(
         0 => '电话',
         1 => '短信',
         2 => 'qq/微信',
         3 => '邮件',
         4 => '其他',
    );

    // 状态
    static $STATUS_MAP = array(
         0 => '未跟进',
         1 => '跟进中',
         2 => '已跟进',
    );

    // 获取产品详细信息
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
             * to do
             */
            $enrolment = BabyEnrolmentModel::model()
                ->getDb()
                ->where(array('eid' => $bundle['eid']))
                ->queryRow();
            $enrolment = BabyEnrolmentModel::model()->getDetailInfo($enrolment);
            $bundle['enrolment'] = $enrolment;

            // 添加人
            $bundle['follow_user'] = CompanySchoolMemberModel::model()->getDb()
                ->where(array('mid' => $bundle['mid']))
                ->queryRow();


            // 获取超级管理员
            return $bundle;
        endif;

        return $bundles;

    }

}
