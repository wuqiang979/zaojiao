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
class DepartmentModel extends ArModel
{
    // 表名
    public $tableName = 's_department_list';

    // 开通幼儿园
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    static function getData()
    {
    	$result = DepartmentModel::model()->getDb()->queryAll();
        return $result;

    }

}
