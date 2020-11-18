<?php

namespace Linphp\Generator\command;

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
        $namespace   = $file->addNamespace('app\\' . $modular . '\service');
        $model_class = $controller . 'Model';
        $namespace->addUse('app\\' . $modular . '\model\\' . ucfirst($model_class));
        $namespace->addUse('\think\facade\Request');
        $namespace->addUse('\think\exception\ValidateException');
        $namespace->addUse('\Linphp\Generator\notice\\SendMsg');
        $class = $namespace->addClass(ucfirst($tableName_public_name));
        $class->addExtend('app\\' . $modular . '\\Service\\BaseService');
        #class内部注解
        $class->addMethod('index')
            ->addComment('显示资源列表')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('
        try {
            $where=array_filter(Request::except([\'page\',\'limit\']));
            $limit=Request::param(\'limit\');
            $' . $model_class . '=new ' . ucfirst($model_class) . '();
            $data=$' . $model_class . '->where($where)->paginate($limit)->toArray();
            return SendMsg::jsonData($data);
        } catch (ValidateException $e) {
                // 这是进行验证异常捕获
             return json($e->getError());
        } catch (\Exception $e) {
                // 这是进行异常捕获
             return json($e->getMessage());
        }');

        $class->addMethod('save')
            ->addComment('保存新建的资源.')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('
        try {
            $' . $model_class . '=new ' . ucfirst($model_class) . '();
            $info=$' . $model_class . '->save(Request::param());
                if($info){
                return SendMsg::jsonData(true);
                }
                return SendMsg::jsonThrow("保存失敗");
            } catch (ValidateException $e) {
                // 这是进行验证异常捕获
                return json($e->getError());
            } catch (\Exception $e) {
                // 这是进行异常捕获
                return json($e->getMessage());
            }');


        $class->addMethod('read')
            ->addComment('显示指定的资源')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody(' 
        try {
            $' . $model_class . '=new ' . ucfirst($model_class) . '();
            $data=$' . $model_class . '->where(\'id\',Request::param(\'id\'))->find();
            return SendMsg::jsonData($data);
        } catch (ValidateException $e) {
            // 这是进行验证异常捕获
            return json($e->getError());
        } catch (\Exception $e) {
            // 这是进行异常捕获
            return json($e->getMessage());
        }');


        $class->addMethod('update')
            ->addComment('保存更新的资源')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('
        try {
            $info' . '= ' . ucfirst($model_class) . '::update(array_filter(Request::param()));
            if($info)
            {
               return SendMsg::jsonData(true);
            }
            return SendMsg::jsonThrow("修改失敗");
        } catch (ValidateException $e) {
            // 这是进行验证异常捕获
            return json($e->getError());
        } catch (\Exception $e) {
            // 这是进行异常捕获
            return json($e->getMessage());
        }');

        $class->addMethod('delete')
            ->addComment('删除指定资源')
            ->addComment('@author Administrator')
            ->addComment('@return mixed')
            ->setPublic()
            ->setBody('  
        try {
            $' . $model_class . '=new ' . ucfirst($model_class) . '();
            $info=$' . $model_class . '::destroy(Request::param(\'id\'));
            if($info)
            {
                return SendMsg::jsonData(true);
            }
            return SendMsg::jsonThrow("刪除失敗");
        } catch (ValidateException $e) {
            // 这是进行验证异常捕获
            return json($e->getError());
        } catch (\Exception $e) {
            // 这是进行异常捕获
            return json($e->getMessage());
        }');


        $dir = app_path() . $modular . '\\service';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $path = $dir . '\\' . ucfirst($tableName_public_name) . '.php';
        if (!file_exists($path)) {
            echo '创建成功   ' . $path . "\n";
            @file_put_contents($path, $file);
        }
        $this->baseservice($modular);
    }

    public function baseservice($modular = '')
    {
        $file = new PhpFile;
        $file->setStrictTypes(); // adds declare(strict_types=1)
        $file->addComment("service公共类");
        $namespace = $file->addNamespace('app\\' . $modular . '\service');
        $namespace->addUse('app\BaseController');
        $class = $namespace->addClass('BaseService');
        $class->addExtend(BaseController::class);
        $dir  = app_path() . $modular . '\\service';
        $path = $dir . '\\' . 'BaseService.php';
        if (!file_exists($path)) {
            @file_put_contents($path, $file);
        }
    }
}