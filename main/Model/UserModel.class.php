<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ck <yunshaohunzhu@sina.com>
 */

/**
 * user 数据库模型.
 */
class UserModel extends ArModel
{
    // 表名
    public $tableName = 'u_user';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

}
