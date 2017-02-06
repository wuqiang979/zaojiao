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
class BabyReturnChargeModel extends ArModel
{
    // 招生表
    public $tableName = 'u_baby_returncharge';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

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
            // 幼儿姓名
            $bname = BabyModel::model()->getDb()
                ->select('name')
                ->where(array('bid' => $bundles['bid']))
                ->queryRow();
            $bundle['bname'] = $bname['name'];
            // 经办人
            $operator = CompanySchoolMemberModel::model()->getDb()
                ->where(array('mid' => $bundle['operator']))
                ->queryRow();
            $bundle['opname'] = $operator['realname'];
            // 状态
            $bundle['statusH'] = BabyChargeModel::$STATUS_MAP[$bundles['status']];
            
            // 学期
            if ($bundles['studyyear']):
                $schoolYear = EduSchoolYearModel::model()
                    ->getDb()
                    ->where(array('sid' => arCfg('current_member.sid'), 'yid' => $bundles['studyyear'], 'status' => EduSchoolYearModel::STATUS_APPROVED))
                    ->queryAll();
                $schoolYear = EduSchoolYearModel::model()->getDetailInfo($schoolYear);
                $bundle['xueqi'] = $schoolYear[0]['school_year'];
            else:
                $bundle['xueqi'] = '';
            endif;

            return $bundle;
        endif;

        return $bundles;

    }

}
