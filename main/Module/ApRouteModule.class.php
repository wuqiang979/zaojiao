<?php
// 集成 edu 配置路由检查
class ApRouteModule
{
    // 检测配置
    public function checkConfig()
    {
        // ucenter 会 put :80 格式
        $currentHostName = 'http://' . str_replace(':80','', $_SERVER['HTTP_HOST']);

        $currentHost = DomainDbModel::model()->getDb()
            ->where(array('domain' => $currentHostName))
            ->queryRow();
        if (!$currentHost) :
            exit('current domain ' . $currentHostName . ' is not registered');
        else :
            $sign = $currentHost['sign'];
            if (!$sign) :
                exit('current domain ' . $currentHostName . ' sign is not setted');
            else :
                // 设置全局数据库配置文件
                $dbConfigFile = arCfg('EDU_DB_PREFIX_PATH') . 'parameters_' . $sign . '.yml';
                if (file_exists($dbConfigFile)) :
                    $this->init_db($dbConfigFile);
                else :
                    exit($dbConfigFile . ' is not existed');
                endif;
            
                Ar::setConfig('AP_EDU_DB_FILE', $dbConfigFile);
                Ar::setConfig('AP_EDU_HOST_SIGN', $sign);
            endif;
        endif;
        // var_dump(arCfg());
        // exit;

    }

    // 初始化数据库
    public function init_db($dbFile)
    {
        $pdoStr = 'mysql:host={database_host};dbname={database_name};port={database_port}';
        $dbUser = '{database_user}';
        $dbPass = '{database_password}';
        $handle = fopen($dbFile, "r");
        if ($handle) :
            while (($buffer = fgets($handle, 4096)) !== false) {
                // echo $buffer;
                list($key, $value) = explode(':', $buffer);
                $key = trim($key);
                $value = trim($value);
                $pdoStr = str_replace('{' . $key . '}', $value, $pdoStr);
                $dbUser = str_replace('{' . $key . '}', $value, $dbUser);
                $dbPass = str_replace('{' . $key . '}', $value, $dbPass);
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        endif;
        // 初始化edu数据库链接
        $dbConfig = array(
            'dsn' => $pdoStr,
            'user' => $dbUser,
            'pass' => $dbPass,
            'prefix' => '',
            'option' => array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
            )
        );
        // edu 为标志位
        Ar::setConfig('components.db.mysql.config.read.edu', $dbConfig);
        $dbAll = arCfg('components.db.mysql.config');
        // 重新分配数据库配置
        arComp('db.mysql')->setConfig($dbAll);
        // 查询数据库方法
        // $users = arComp('db.mysql')->read('edu')->table('user')->queryAll();
        // var_dump($users);

    }

}
