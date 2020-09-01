<?php

namespace Linphp\ServiceController\command;

use Nette\PhpGenerator\PhpFile;
use think\console\input\Option;
use think\facade\Db;
use think\Model;

/**
 * Class ModelGenerator
 * @package Linphp\ServiceController\command
 */
class ModelGenerator
{

    /**
     * @author Administrator
     * @param string $modular
     * @param string $controller
     * @param string $tableName
     */
    public function command($modular = '', $controller = '', $tableName = '')
    {
        $file = new PhpFile;
        if ($tableName) {
            $prefix     = config('database.connections.mysql.prefix');
            $annotation = Db::query("show table status");
            foreach ($annotation as $v) {
                if ($tableName == $v['Name']) {
                    $file->addComment($v['Comment']);
                }
            }
        }
        $class_name=$controller;
        $controller=$controller .'Model';
        $file->setStrictTypes(); // adds declare(strict_types=1)
        $namespace = $file->addNamespace('app\\' . $modular . '\model');
        $namespace->addUse('app\model\entity\\'.$class_name.'Entity');

        $class = $namespace->addClass(ucfirst($controller));
        $model_class=$class_name.'Entity';
        $class->addExtend('app\model\entity\\'.$class_name.'Entity');
        $dir = app_path() . $modular . '\\model';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $path = $dir . '\\' . ucfirst($controller) . '.php';
        if (!file_exists($path)) {
            echo '创建成功   ' . $path . "\n";
            @file_put_contents($path, $file);
        }


    }
}