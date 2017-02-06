<?php
/**
 * 幼儿请假管理表.
 */
class BabyLeaveModel extends ArModel {

    // 表名
    public $tableName = 'u_baby_leave';
    // 初始化
    static public function model($class = __CLASS__) {

        return parent::model($class);
    }   
}
