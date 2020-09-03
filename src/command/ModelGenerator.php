<?php

namespace Linphp\Generator\command;

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
     *
     */
    public function command($modular = '', $controller = '')
    {
        $file               = new PhpFile;
        $prefix             = config('database.connections.mysql.prefix');
        $annotation         = Db::query("show table status");
        $class_name         = $controller;
        $cc_format          = $prefix . lcfirst($this->cc_format($class_name));
        $cc_format_is_table = false;

        foreach ($annotation as $v) {

            if ($cc_format == $v['Name']) {
                $file->addComment($v['Comment']);
                $cc_format_is_table = true;
            }
        }

        $namespace  = $file->addNamespace('app\\' . $modular . '\model');
        $controller = $controller . 'Model';
        $file->setStrictTypes(); // adds declare(strict_types=1)
        $class = $namespace->addClass(ucfirst($controller));


        #表不存在就集成普通的model
        if ($cc_format_is_table) {
            $namespace->addUse('app\model\entity\\' . ucfirst($class_name) . 'Entity');
            $class->addExtend('app\model\entity\\' . ucfirst($class_name) . 'Entity');
        } else {
            $namespace->addUse('think\Model');
            $class->addExtend(Model::class);
        }


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

    #还原表明
    public function cc_format($name)
    {
        $temp_array = array();
        for ($i = 0; $i < strlen($name); $i++) {
            $ascii_code = ord($name[$i]);
            if ($ascii_code >= 65 && $ascii_code <= 90) {
                if ($i == 0) {
                    $temp_array[] = chr($ascii_code + 32);
                } else {
                    $temp_array[] = '_' . chr($ascii_code + 32);
                }
            } else {
                $temp_array[] = $name[$i];
            }
        }
        return implode('', $temp_array);
    }


}