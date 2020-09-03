<?php
namespace Linphp\Generator;
use Linphp\Generator\command\StartGenerator;

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