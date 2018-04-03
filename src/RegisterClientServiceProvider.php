<?php

namespace Uniondrug\Register;

use Phalcon\Di\ServiceProviderInterface;

class RegisterClientServiceProvider implements ServiceProviderInterface
{
    public function register(\Phalcon\DiInterface $di)
    {
        $di->set(
            'registerClient',
            function () {
                $client = new RegisterClient();
                return $client;
            }
        );

        // Under Swoole, auto register
        if (function_exists('app')) {
            if ($di->getConfig()->path('register.autoRegister')) {
                $res = $di->getShared('registerClient')->addNode(app()->getConfig()->path('app.appName'), app()->getConfig()->path('server.host'));
            }
        }
    }
}
