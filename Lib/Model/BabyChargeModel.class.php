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
class BabyChargeModel extends ArModel
{
    // 招生表
    public $tableName = 'u_baby_charge';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 收费类型 chargetype
    static $CHARGETYPE_MAP = array(
         1 => '现金支付',
         2 => '银行卡支付',
    );

    // 定金 earnest
    static $EARNEST_MAP = array(
         1 => '使用定金',
         2 => '不使用定金',
    );

    // 代扣 withholdingsingle
    static $DAIKOU_MAP = array(
         1 => '代扣',
         2 => '非代扣',
    );

    // 收费模式 chargeway
    static $CHARGEWAY_MAP = array(
         1 => '按次',
         2 => '按学期',
    );

    // 收费状态 status
    static $STATUS_MAP = array(
        1 => '未审核',
        2 => '审核中',
        3 => '审核未通过',
        4 => '正常',
        5 => '已结算',
        6 => '已审核',
        7 => '代扣中'
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
            $bundle['statusH'] = $this::$STATUS_MAP[$bundles['status']];
            //是否代扣
            if ($bundles['withholdingsingle']):
                $bundle['daikou'] = $this::$DAIKOU_MAP[$bundles['withholdingsingle']];
            else:
                $bundle['daikou'] = '';
            endif;
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
