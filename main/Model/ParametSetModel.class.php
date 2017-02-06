<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

class ParametSetModel extends ArModel
{
    // 表名
    public $tableName = 's_param_set';
    static $paramet_type = array(
            '1' => '家庭收入',
            '2' => '跟进方式',
            '3' => '家长关系',
        );

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    static function getData($type = '')
    {
    	$result = ParametSetModel::model()->getDb()->where(array('type' => $type))->queryAll();
        return $result;

    }

    static function formPost($post)
    {
        // 编辑显示页 添加数据
        if (isset($post['btnAdd']) || isset($post['btnEdit'])) :
            $post['insertData'] = $post['updateData'] = array('name' => $post['tbParamName'], 'order' => 0, 'description' => $post['tbParamValue'], 'type' => $post['type']);
        endif;

        return $post;

    }

}
