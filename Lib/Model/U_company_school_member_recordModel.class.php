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
class U_company_school_member_recordModel extends ArModel
{
    // 表名
    public $tableName = 'u_company_school_member_record';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 组别
    static $GROUP_MAP = array(
         0 => '管理组',
         1 => '教师组',
    );

    // 在职标志 isonjob
    static $ISONJOB_MAP = array(
         0 => '在职',
         1 => '兼职',
         2 => '实习',
         3 => '离职',
    );

    // 保险办理 isinsurance
    static $ISINSERANCE_MAP = array(
         0 => '未办理',
         1 => '已办理',
    );

    // 职务定级 jobgrading
    static $JOBGRADING_MAP = array(
         0 => 'A级',
         1 => 'B级',
         2 => 'C级',
         3 => 'D级',
    );

    // 聘用方式 hiringway
    static $HIRINGWAY_MAP = array(
         0 => '长期聘用',
         1 => '临时聘用',
    );

    // 政治面貌 politicsstatus
    static $POLITICSSTATUS_MAP = array(
         0 => '中共党员',
         1 => '中共预备党员',
         2 => '共青团员',
         3 => '民革党员',
         4 => '民盟盟员',
         5 => '民建会员',
         6 => '民进会员',
         7 => '农工党党员',
         8 => '致公党党员',
         9 => '九三学社社员',
         10 => '台盟盟员',
         11 => '无党派民主人士',
         12 => '群众',
    );

    // 资格证 certification
    static $CERTIFICATION_MAP = array(
         0 => '教师资格证A级',
         1 => '教师资格证B级',
    );

    // 普通话 mandarin
    static $MANDARIN_MAP = array(
         0 => '普通话资格证A级',
         1 => '普通话资格证B级',
         2 => '普通话资格证C级',
    );

    // 年度考核情况 year
    static $YEAR_MAP = array(
         0 => '非常优秀',
         1 => '优秀',
         2 => '一般',
         3 => '合格',
         4 => '不合格',
    );

    // 培训情况 training
    static $TRAINING_MAP = array(
         0 => '非常优秀',
         1 => '优秀',
         2 => '一般',
         3 => '合格',
         4 => '不合格',
    );

    // 学历 education
    static $EDUCATION_MAP = array(
         0 => '博士',
         1 => '硕士',
         2 => '一本',
         3 => '二本',
         4 => '三本',
         5 => '大专',
         6 => '专科',
         7 => '高中及以下',
    );

    // 婚否 marrageed
    static $MARRAGEED_MAP = array(
         0 => '未婚',
         1 => '已婚',
    );

    // 性别 sex
    static $SEX_MAP = array(
         0 => '男',
         1 => '女',
    );

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
            $bundle['member'] = CompanySchoolMemberModel::model()->getDb()
                ->where(array('mid' => $bundle['mid']))
                ->queryRow();
            $bundle['member'] = CompanySchoolMemberModel::model()->getDetailInfo($bundle['member']);

            return $bundle;
        endif;

        return $bundles;

    }


}