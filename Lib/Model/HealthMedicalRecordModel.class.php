<?php
/**
 * 用药记录表.
 */
class HealthMedicalRecordModel extends ArModel {

    // 表名
    public $tableName = 's_health_medical_record';

    static public function model($class = __CLASS__) {

        return parent::model($class);

    }

     // 获取关联表信息（u_baby）
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
            $res['class'] = EduClassroomModel::model()->getDb()
                ->where(array('cid' => $res['cid']))
                ->queryRow();
            $res['baby'] = BabyModel::model()->getDb()
                ->where(array('bid' => $res['bid']))
                ->queryRow();
            return $res;
        endif;

        return $arr;
    }

}
