<?php

namespace UniondrugRegister;

use Phalcon\Di\ServiceProviderInterface;

class RegisterClientServiceProvider implements ServiceProviderInterface
{
    public function register(\Phalcon\DiInterface $di)
    {
        $di->set(
            'registerClient',
            function () {
                $client = new RegisterClient(
                    $this->getConfig()->path('register.host', '127.0.0.1'),
                    $this->getConfig()->path('register.port', 9530),
                    true,
                    (int) $this->getConfig()->path('register.timeout', 30)
                );

                return $client;
            }
        );
    }
}
