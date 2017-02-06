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
class CompanySchoolMemberProfileModel extends ArModel
{
    // 集团学校部门表
    public $tableName = 'u_company_school_member_profile';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    CONST ROLE_YZ = 1;
    CONST ROLE_COMPANY_ADMIN = 3;
    CONST ROLE_SUPER_ADMIN = 9;
    // 类型
    static $ROLE_MAP = array(
        0 => '普通用户',
        1 => '园长',
        2 => '职工',
        3 => '公司管理员',
        4 => '运营管理员',
        8 => '超级管理员',
        9 => '系统管理员',
    );


    CONST STATUS_APPROVE = 1;
    // 状态
    static $STATUS_MAP = array(
        0 => '审核中',
        1 => '通过',
        2 => '禁止',
    );

}
