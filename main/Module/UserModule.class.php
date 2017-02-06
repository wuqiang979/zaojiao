<?php
class UserModule
{
    /*
        统一处理 排序，编辑，，删除
        __EVENTTARGET 包含 模型名，数据id,操作类型
    */
	public function doPost($post)
    {
        if (!$post) return;
        // 处理编辑后提交的数据
        $module = $post['__EVENTTARGET'];
        if ($post['editId'] != '' && $post['updateData'] != '') :
            $module::model()->getDb()->where(array('id' => $post['editId']))->update($post['updateData']);
            return;
        endif;

        // 添加数据 请先处理好插入数据 这里只统一处理
        if ($post['btnAdd'] != '' && $insertData = $post['insertData']) :
            $module::model()->getDb()->insert($insertData);
            return;
        endif;
        // 非添加数据
        if (isset($post['__EVENTTARGET']) && $target = $post['__EVENTTARGET']) :
            list($module,$id,$type) = explode('$', $target);
            switch ($type) {
                case 'lbtnDel':
                    $module::model()->getDb()->where(array('id' => $id))->limit(1)->delete();
                    break;
                // 编辑显示页
                case 'lbtnEdit':
                    $result = $module::model()->getDb()->where(array('id' => $id))->queryRow();
                    return $result;
                    break;
                default:
                    # code...
                    break;
            }
        endif;

    }

}
