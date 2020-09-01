<?php

namespace Linphp\ServiceController\command;
use Nette\PhpGenerator\PhpFile;
use think\console\input\Option;
use think\facade\Db;
use think\facade\Request;
use app\BaseController;
/**
 * Class ControllerGenerator
 * @package Linphp\ServiceController\command
 */
class ServiceGenerator
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


        $tableName_public_name = $controller . 'Service';
        $file->setStrictTypes(); // adds declare(strict_types=1)
        $namespace = $file->addNamespace('app\\' . $modular . '\service');
        $namespace->addUse('app\BaseController');
        $namespace->addUse('\think\facade\Request');
        $namespace->addUse('\think\facade\Db');
        $class = $namespace->addClass($tableName_public_name);
        $class->addExtend(BaseController::class);
        #class内部注解
        $class->addMethod('index')
            ->addComment('显示资源列表')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('$list = Db::name(\'user\')->where(\'status\',1)->order(\'id\', \'desc\')->paginate(10); if($list){return true;}else{return false;}')
            ->addParameter('request')
            ->setType(Request::class); // it will resolve to \Bar\OtherClass
        $class->addMethod('create')
            ->addComment('显示创建资源表单页.')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('#逻辑自己编写')
            ->addParameter('request')
            ->setType(Request::class); // it will resolve to \Bar\OtherClass;

        $class->addMethod('save')
            ->addComment('保存新建的资源.')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('return $this->' . $tableName_public_name . '->save();')
            ->addParameter('request')
            ->setType(Request::class); // it will resolve to \Bar\OtherClass;

        $class->addMethod('read')
            ->addComment('显示指定的资源')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('return $this->' . $tableName_public_name . '->read();')
            ->addParameter('request')
            ->setType(Request::class); // it will resolve to \Bar\OtherClass;

        $class->addMethod('edit')
            ->addComment('显示编辑资源表单页')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('return $this->' . $tableName_public_name . '->edit();')
            ->addParameter('request')
            ->setType(Request::class); // it will resolve to \Bar\OtherClass;

        $class->addMethod('update')
            ->addComment('保存更新的资源')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('return $this->' . $tableName_public_name . '->update();')
            ->addParameter('request')
            ->setType(Request::class); // it will resolve to \Bar\OtherClass;

        $class->addMethod('delete')
            ->addComment('删除指定资源')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('return $this->' . $tableName_public_name . '->delete();')
            ->addParameter('request')
            ->setType(Request::class); // it will resolve to \Bar\OtherClass;

        $dir = app_path() . $modular . '\\service';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $path = $dir . '\\' . ucfirst($tableName_public_name) . '.php';
        if (!file_exists($path)) {
            echo '创建成功   ' . $path . "\n";
            @file_put_contents($path, $file);
        }
    }
}