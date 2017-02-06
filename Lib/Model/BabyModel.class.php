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
class BabyModel extends ArModel
{
    // 招生表
    public $tableName = 'u_baby';

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
         0 => '在园幼儿',
         1 => '毕业幼儿',
         2 => '其他',
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
         1 => '正常',
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
                'bid' => $bundle['bid'],
            );
            // 获取父母信息
            $bundle['parents'] = BabyParentModel::model()
                ->getDb()
                ->where($condition)
                ->queryAll();
            // 档案
            $profile = BabyProfileModel::model()
                ->getDb()
                ->where($condition)
                ->queryRow();
            $profile = BabyProfileModel::model()->getDetailInfo($profile);
            $bundle['profile'] = $profile;

            // 添加人
            $bundle['add_user'] = CompanySchoolMemberModel::model()->getDb()
                ->where(array('mid' => $bundle['amid']))
                ->queryRow();

            return $bundle;
        endif;

        return $bundles;

    }

}
