<?php
/**
 * 活动签到表.
 */
class ActivitySignRecordModel extends ArModel {

    // 表名
    public $tableName = 's_activity_sign_record';

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
            // 获取幼儿信息
            $res['baby'] = BabyModel::model()
                ->getDb()
                ->where(array('bid'=>$res['bid']))
                ->queryRow();
            // 获取活动信息
            $res['activity'] = ActivityModel::model()
                ->getDb()
                ->where(array('aid'=>$res['aid']))
                ->queryRow();
            $res['activity']['man'] = ActivityModel::model()->getDetailInfo($res['activity']);

            return $res;
        endif;
        return $arr;
    }
}
