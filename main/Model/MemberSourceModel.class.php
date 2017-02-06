<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

class MemberSourceModel extends ArModel
{
    // 表名
    public $tableName = 's_member_source';

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    static function getData()
    {
    	$result = MemberSourceModel::model()->getDb()->queryAll();
        return $result;

    }

    static function formPost($post)
    {
        // 编辑显示页 添加数据
        if (isset($post['btnAdd']) || isset($post['btnEdit'])) :
            $post['insertData'] = $post['updateData'] = array('source' => $post['txtSourceName']);
        endif;

        return $post;

    }



}
