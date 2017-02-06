<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 *  数据库模型.
 */
class CompanyModel extends ArModel
{
    // 集团学校部门表
    public $tableName = 'u_company';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 类型
    static $TYPE_MAP = array(
         0 => '幼教',
         1 => '早教',
         2 => '培训机构',
         3 => '其他',
    );

    // 状态
    static $STATUS_MAP = array(
         0 => '审核中',
         1 => '通过',
         2 => '停运',
    );

    // 员工数量
    static $STAFF_TYPE_MAP = array(
        0 => '1-10人',
        1 => '10-30人',
        2 => '30-50人',
        3 => '50-100人',
        4 => '100人以上',
    );

    // 教室数量
    static $CLASS_ROOM_TYPE_MAP = array(
        0 => '5个以内',
        1 => '5-9个',
        2 => '9个以上',
    );

    // 学校数量
    static $SCHOOL_NUM_TYPE_MAP = array(
        0 => '1个',
        1 => '2-5个',
        2 => '5个以上',
        3 => '全国连锁',
    );

    // 获取所有
    public function sortAll()
    {
        $sortData = array();
        $menu = $this->getAllMenuByPid(0, true);
        $titlePadMap = array(
            '│', '├─', '└─',
        );
        if ($menu) :
            $padStringUnit = '&nbsp;&nbsp;&nbsp;&nbsp;';
            // parent 代表当前母体
            $sortData[] = $menu[0];
            if (!is_array($menu[0]['sub_menu'])) :
                return $sortData;
            endif;
            // 精确到两级
            foreach ($menu[0]['sub_menu'] as $sub_menu) :
                $sub_menu['name'] = $padStringUnit . $titlePadMap[1] . $sub_menu['name'];
                $sortData[] = $sub_menu;
                if (is_array($sub_menu['sub_menu'])) :
                    foreach ($sub_menu['sub_menu'] as $second_menu) :
                        $second_menu['name'] = $padStringUnit . $padStringUnit . $titlePadMap[1] . $second_menu['name'];
                        $sortData[] = $second_menu;
                    endforeach;
                endif;
            endforeach;
        endif;
        return $sortData;

    }

    // 获取所有分类 默认获取所有分类
    public function getAllMenuByPid($pid = 0, $getall = false)
    {
        $con['ppid'] = $pid;
        $submenu = self::model()
            ->getDb()
            ->order('sorder desc')
            ->where($con)
            ->queryAll();
        if (!$submenu) :
            return false;
        endif;

        if (!$getall) :
            return $submenu;
        else :
            if (!empty($submenu)) :
                foreach ($submenu as &$sub_menu) :
                    $sub_menu['sub_menu'] = $this->getAllMenuByPid($sub_menu['pid'], $getall);
                endforeach;
            endif;
        endif;
        return $submenu;

    }

    // 获取产品详细信息
    public function getDetailInfo(array $bundles)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($bundles)) :
            foreach ($bundles as &$bundle) :
                $bundle = $this->getDetailInfo($bundle);
            endforeach;
        else :
            $bundle = $bundles;
            /**
             * to do
             */
            // 获取超级管理员
            $bundle['adminuser'] = CompanySchoolMemberModel::model()
                ->getDb()
                ->select('account_name')
                ->where(array('cid' => $bundle['cid'], 'rgid' => CompanySchoolMemberModel::ROLE_COMPANY_ADMIN))
                ->queryRow();
            // 公司下边的学校
            $bundle['register_schools'] = CompanySchoolModel::model()
                ->getDb()
                ->where(array('cid' => $bundle['cid']))
                ->queryAll();
            return $bundle;
        endif;

        return $bundles;

    }

}
