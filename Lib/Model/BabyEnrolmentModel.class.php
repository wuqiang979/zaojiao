<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 *  招生数据库模型.
 */
class BabyEnrolmentModel extends ArModel
{
    // 招生表
    public $tableName = 'u_baby_enrolment';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 来源
    static $SOURCE_TYPE_MAP = array(
         0 => '网上咨询',
         1 => '搜索引擎',
         2 => '内部推荐',
         3 => '系统',
         4 => '其他',
    );

    // 来源
    static $ATTENTION_TYPE_MAP = array(
         0 => '新来了解',
         1 => '已交定金',
         2 => '报名咨询',
         3 => '其他',
    );

    // 类型
    static $SEX_MAP = array(
         0 => '男',
         1 => '女',
         2 => '保密',
    );

    // 推荐级别
    static $RECOMMENT_LEVEL_MAP = array(
         0 => '一般推荐',
         1 => '重点推荐',
         2 => '强烈推荐',
    );

    // 状态
    static $STATUS_MAP = array(
         0 => '审核中',
         1 => '通过',
         2 => '禁止',
    );

    // 收入
    static $INCOME_TYPE = array(
         0 => '保密',
         1 => '0-4000',
         2 => '4000-8000',
         3 => '8000-12000',
         4 => '12000+',
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
            $condition = array(
                'eid' => $bundle['eid'],
            );
            // 获取父母信息
            $bundle['parents'] = BabyEnrolmentParentModel::model()
                ->getDb()
                ->where($condition)
                ->queryAll();
            // 档案
            $profile = BabyEnrolmentProfileModel::model()
                ->getDb()
                ->where($condition)
                ->queryRow();
            $profile = BabyEnrolmentProfileModel::model()->getDetailInfo($profile);
            $bundle['profile'] = $profile;

            // 添加人
            $bundle['add_user'] = CompanySchoolMemberModel::model()->getDb()
                ->where(array('mid' => $bundle['amid']))
                ->queryRow();

            // 跟踪信息
            $bundle['follow'] = BabyEnrolmentFollowModel::model()
                ->getDb()
                ->where($condition)
                ->queryRow();

            return $bundle;
        endif;

        return $bundles;

    }

}
