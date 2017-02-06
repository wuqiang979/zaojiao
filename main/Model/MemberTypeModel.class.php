<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

class MemberTypeModel extends ArModel
{
    // 表名
    public $tableName = 's_member_type';
    static $memberStatus = array(
            '1' => '潜在幼儿',
            '2' => '在园幼儿',
            '3' => '离园幼儿',
        );

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    static function getData()
    {
    	$result = MemberTypeModel::model()->getDb()->queryAll();
        return $result;

    }

    static function formPost($post)
    {
        // 编辑显示页 添加数据
        if (isset($post['btnAdd']) || isset($post['btnEdit'])) :
            $post['insertData'] = $post['updateData'] = array('type' => $post['ad_type'], 'status' => (int)$post['ad_status'], 'discount' => (float)$post['ad_discount'], 'description' => $post['description'], 'order' => 0);
        endif;

        return $post;

    }



}
