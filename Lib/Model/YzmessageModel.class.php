<?php
class YzmessageModel extends ArModel {

    // 表名
    public $tableName = 'u_yz_messages';

    // 初始化
    static public function model($class = __CLASS__) {

        return parent::model($class);
    }  

    // 发送状态
    static $STATUS_MAP = array(
         1 => '编辑',
         2 => '发送失败',
         3 => '发送成功',
    );
    //  消息处理状态
    static $STATUSBACK_MAP = array(
         1 => '未查看',
         2 => '已查看',
    );
    // 类型
    static $TYPE_MAP = array(
         1 => '个人',
         2 => '留言',
         3 => '其它',
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
            // 类型
            $bundle['typeH'] = $this::$TYPE_MAP[$bundle['type']];
            // 状态
            $bundle['statusH'] = $this::$STATUS_MAP[$bundle['status']];
            // 处理状态
            if ($bundle['statusback']) :
                $bundle['statusbackH'] = $this::$STATUSBACK_MAP[$bundle['statusback']];
            endif;
            // 发送人
            $peo = CompanySchoolMemberModel::model()
                ->getDb()
                ->select('realname')
                ->where(array('mid' => $bundle['frommid']))
                ->queryRow();
            $bundle['fromname'] = $peo['realname'];

            $bundle['sid'] = U_company_schoolModel::model()
                ->getDb()
                ->where(array('sid' => $bundle['sid']))
                ->queryAll();
            return $bundle;
        endif;

        return $bundles;

    }
}
