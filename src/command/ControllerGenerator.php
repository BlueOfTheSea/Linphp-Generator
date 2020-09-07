<?php

namespace Linphp\Generator\command;

use app\BaseController;
use Nette\PhpGenerator\PhpFile;
use think\console\input\Option;
use think\facade\Db;

/**
 * Class ControllerGenerator
 * @package Linphp\ServiceController\command
 */
class ControllerGenerator
{

    /**
     * @param string $modular
     * @param string $controller
     * @param string $tableName
     * @author Administrator
     */
    public function command($modular = '', $controller = '', $tableName = '')
    {

        $file = new PhpFile;
        if ($tableName) {
            $annotation = Db::query("show table status");
            foreach ($annotation as $v) {
                if ($tableName == $v['Name']) {
                    $file->addComment($v['Comment']);
                }
            }
        }
        $tableName_public_name = $controller . 'Service';

        $file->setStrictTypes(); // adds declare(strict_types=1)
        $namespace = $file->addNamespace('app\\' . $modular . '\controller');
        $namespace->addUse('app\\' . $modular . '\service\\' . ucfirst($tableName_public_name));
        $namespace->addUse('think\annotation\Inject');

        $class = $namespace->addClass(ucfirst($controller));
        $class->addExtend('app\\' . $modular . '\\controller\\' . 'Base');
        #class内部注解

        $class->addProperty($tableName_public_name)
            ->addComment('@Inject()')
            ->addComment("@var " . $tableName_public_name);

        $class->addMethod('index')
            ->addComment('显示资源列表')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('return $this->' . $tableName_public_name . '->index();');

        $class->addMethod('save')
            ->addComment('保存新建的资源.')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('return $this->' . $tableName_public_name . '->save();');
        $class->addMethod('read')
            ->addComment('显示指定的资源')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('return $this->' . $tableName_public_name . '->read();');
        $class->addMethod('update')
            ->addComment('保存更新的资源')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('return $this->' . $tableName_public_name . '->update();');
        $class->addMethod('delete')
            ->addComment('删除指定资源')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('return $this->' . $tableName_public_name . '->delete();');
        $dir = app_path() . $modular . '\\controller';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $this->basecontroller($modular);
        $path = $dir . '\\' . ucfirst($controller) . '.php';
        if (!file_exists($path)) {
            echo '创建成功   ' . $path . "\n";
            @file_put_contents($path, $file);
        }
    }

    public function basecontroller($modular='')
    {
        $file = new PhpFile;
        $file->setStrictTypes(); // adds declare(strict_types=1)
        $file->addComment("controller公共类");
        $namespace = $file->addNamespace('app\\' . $modular . '\controller');
        $namespace->addUse('app\BaseController');
        $class = $namespace->addClass('Base');
        $class->addExtend(BaseController::class);
        $class->addProperty('middleware',[])->setProtected();
        $dir = app_path() . $modular . '\\controller';
        $path = $dir . '\\' .   'Base.php';
        if (!file_exists($path)) {
            echo '创建成功   ' . $path . "\n";
            @file_put_contents($path, $file);
        }
    }
}