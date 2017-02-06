<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 所有支持的园区管理表.
 */
class ClassroomModel extends ArModel
{
    // 表名
    public $tableName = 's_classroom';

    // 开通幼儿园
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    static function getData()
    {
    	$result = ClassroomModel::model()->getDb()->queryAll();
        return $result;

    }

}
