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
class CompanySchoolMemberModel extends ArModel
{
    // 集团学校部门表
    public $tableName = 'u_company_school_member';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // user privileges
    CONST ROLE_NORMAL = 0;
    CONST ROLE_SCHOOL_PARENT = 1;
    CONST ROLE_STAFF = 2;
    CONST ROLE_YZ = 8;
    CONST ROLE_COMPANY_ADMIN = 9;

    // system privileges
    CONST ROLE_PLATFORM_ADMIN = 16;
    CONST ROLE_SUPER_ADMIN = 32;
    // 40 司令
    CONST ROLE_SYSTEM_ADMIN = 40;
    // 类型
    static $ROLE_MAP = array(
        0 => '普通用户',
        1 => '家长',
        2 => '职工',
        8 => '园长',
        9 => '公司',

        16 => '运营',
        32 => '超级管理员',
        40 => '系统管理员',
    );


    CONST STATUS_APPROVE = 1;
    // 状态
    static $STATUS_MAP = array(
        0 => '审核中',
        1 => '通过',
        2 => '禁止',
    );

    // 生成密码
    static public function gPwd($pwd)
    {
        return md5(md5($pwd . 'aphellosalt'));

    }

    // 获取bundle详细信息 万能方法
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
             * to do what you want
             * $bundle['????'] = '???';
             */
            // 学校信息
            $bundle['school'] = CompanySchoolModel::model()
                ->getDb()
                ->where(array('sid' => $bundle['sid']))
                ->queryRow();

            // 档案
            $profile = CompanySchoolMemberProfileModel::model()
                ->getDb()
                ->where(array('mid' => $bundle['mid']))
                ->queryRow();
            // 职位
            if ($profile['postids']) :
                $postids = explode(',', $profile['postids']);
                $posts = CompanySchoolPostModel::model()
                        ->getDb()
                        ->where(array('pid' => $postids))
                        ->queryAll();
                // 包含权限的职位
                $posts = CompanySchoolPostModel::model()->getDetailInfo($posts);
                $memberAuths = array();
                foreach ($posts as $post) :
                    foreach ($post['auths'] as $pauth) :
                        $memberAuths[] = $pauth;
                    endforeach;
                endforeach;
                $profile['posts'] = $posts;
                $profile['auths'] = $memberAuths;
            endif;

            // 部门
            if ($profile['dids']) :
                $dids = explode(',', $profile['dids']);
                $profile['departs'] = CompanySchoolDepartmentModel::model()
                    ->getDb()
                    ->where(array('did' => $dids))
                    ->queryAll();
            endif;

            $bundle['profile'] = $profile;

             // 学校信息
            $bundle['school'] = CompanySchoolModel::model()
                ->getDb()
                ->where(array('sid' => $bundle['sid']))
                ->queryRow();

            return $bundle;
        endif;

        return $bundles;

    }

}
