<?php

namespace Linphp\Generator\command;

use Nette\PhpGenerator\PhpFile;
use think\console\input\Option;
use think\facade\Db;
use think\Model;

/**
 * 初始化模型
 * Class EntityModelGenerator
 * @package Linphp\ServiceController\command
 */
class EntityModelGenerator
{

    private $typeMaps = [
        'int'    => ['int', 'tinyint', 'smallint', 'mediumint'],
        'string' => ['timestamp', 'char', 'varchar', 'text'],
        'float'  => ['decimal', 'double', 'float'],
    ];


    /**
     * @param string $modular
     * @param string $controller
     * @param string $tableName
     * @author Administrator
     */

    public function command()
    {
        echo "正在生成实体类\n";
        $database = config('database.connections.mysql.database');
        $result   = Db::query('show tables');
        foreach ($result as $k => $v) {
            $tableName    = $v['Tables_in_' . $database];
            $prefix       = config('database.connections.mysql.prefix');
            $prefixSum    = strlen($prefix); #字符串长度,准备裁剪数据表结构前缀
            $str          = substr($tableName, $prefixSum);
            $str_array    = explode('_', $str);
            $tableNameVal = '';
            for ($a = 0; $a < count($str_array); $a++) {
                $tableNameVal .= ucfirst($str_array[$a]);
            }
            $columns = Db::query(
                'SELECT COLUMN_NAME,COLUMN_COMMENT,DATA_TYPE ,COLUMN_KEY,TABLE_NAME
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME=? AND TABLE_SCHEMA=?',
                [$tableName, $database]
            );
            $file = new PhpFile;
            $file->setStrictTypes(); // adds declare(strict_types=1)
            $namespace = $file->addNamespace('app\model\entity');
            $namespace->addUse('think\Model');
            $class = $namespace->addClass(ucfirst($tableNameVal.'Entity'));
            $schema = [];   // 设置模型的 schema 字段信息
            foreach ($columns as $v) {

                if($v['COLUMN_KEY']=='PRI')
                {
                    $class->addProperty('pk', $v['COLUMN_NAME'])->setProtected();
                    $class->addProperty('table', $v['TABLE_NAME'])->setProtected();
                }
                $class->addComment('@property '.$this->checkType($v['DATA_TYPE'])." $".$v['COLUMN_NAME']." {$v['COLUMN_COMMENT']}");
                $schema[$v['COLUMN_NAME']] = $v['DATA_TYPE'];
            }
            $class->addProperty('schema', $schema)->setProtected();
            $class->addExtend(Model::class);
            $dir = app_path() . 'model\\entity';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $path = $dir . '\\' . ucfirst($tableNameVal.'Entity') . '.php';
            @file_put_contents($path, $file);
            echo '生成实体Model层'.$path."\n";
        }
    }

    /**
     * @param string $type
     * @return int|string
     * @author Administrator
     */
    private function checkType($type = '')
    {
        $phpType = 'string';
        foreach ($this->typeMaps as $key => $typeMap) {
            if (in_array($type, $typeMap)) {
                $phpType = $key;
                break;
            }
        }
        return $phpType;
    }

}