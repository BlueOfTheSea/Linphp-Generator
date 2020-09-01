<?php
namespace Linphp\ServiceController;
use Linphp\ServiceController\command\StartGenerator;

/**
 * Class Service
 * @package Linphp\ServiceController
 */
class Service extends \think\Service {
    /**
     * @author Administrator
     */
    public function boot()
    {

        $this->commands(StartGenerator::class);
    }
}