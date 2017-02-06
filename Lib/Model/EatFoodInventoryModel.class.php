<?php
/**
 * 食材库存表.
 */
class EatFoodInventoryModel extends ArModel {

    // 表名
    public $tableName = 's_eat_food_inventory';

    // 初始化
    static public function model($class = __CLASS__) {

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
            // 获取**

            return $bundle;
        endif;
        return $bundles;
    }
}
