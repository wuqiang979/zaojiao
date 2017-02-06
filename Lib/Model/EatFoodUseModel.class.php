<?php
/**
 * 食材库存表.
 */
class EatFoodUseModel extends ArModel {

    // 表名
    public $tableName = 's_eat_food_use';

    // 初始化
    static public function model($class = __CLASS__) {

        return parent::model($class);
    }   

    // 获取关联表信息
    public function getDetailInfo(array $arr)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($arr)) :// 检测是否对维数组
            foreach ($arr as &$res) :
                $res = $this->getDetailInfo($res);
            endforeach;
        else :
            $res = $arr;
            /**
             * to do 追加数组数据
             */
            // 获取食材信息
            $res['food'] = EatFoodInventoryModel::model()
                ->getDb()
                ->where(array('fiid'=>$res['fiid']))
                ->queryRow();
            // 获取取材人信息
            $res['man'] = CompanySchoolMemberModel::model()
                ->getDb()
                ->select('realname')
                ->where(array('mid'=>$res['mid']))
                ->queryRow();
            return $res;
        endif;

        return $arr;

    }
}
