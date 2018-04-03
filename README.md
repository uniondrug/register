# Register client for uniondrug/framework

服务注册客户端

## 安装

```shell
$ cd project-home
$ composer require uniondrug/register
$ cp vendor/uniondrug/register/register.php config/
```

修改 `app.php` 配置文件，加上RegisterClient服务。服务名称：`registerClient`。

> 注意，如果要注册中心以`tcp://`提供服务，本服务需要`tcpClient`支持，并且下面配置需要放在`TcpClientServiceProvider`下面。

```php
return [
    'default' => [
        ......
        'providers'           => [
            ......
            \Uniondrug\Register\RegisterClientServiceProvider::class,
        ],
    ],
];
```

## 配置

配置文件在 `register.php` 中，可以根据环境设置不同的缓存方案

```php
<?php
/**
 * 服务注册中心的配置文件。
 *
 * autoRegister: 是否自动注册到服务器，true，则在应用启动时自动向注册中心注册。（仅在Swoole环境下支持）
 * service: 注册中心服务器地址
 * timeout: 连接超时时间，单位 秒，默认 30
 */
return [
    'default' => [
        'autoRegister' => true,
        'service' => 'http://127.0.0.1:8001',
        'timeout' => 30,
    ],
];

```

## 使用

### 获取一个节点。

`$serviceName` 是服务名称。成功则返回一个服务地址，失败返回false。

获取节点的时候，会根据权重随机返回一个。权重为0的时候节点无效，不会返回。权重在服务注册后，由注册服务器自动维护。

```php
    $data = $this->getDI()->getShared('registerClient')->getNode($serviceName);
    echo $data;

    // should be: http://10.0.0.1:8300
```

### 添加一个节点。

`$serviceName` 是服务名称，`$upstream` 是服务地址，`$weight` 是权重，不传模式是10。成功则返回true，失败返回false。

`$weight` 权重是0~20之间的整数。

```php
    $data = $this->getDI()->getShared('registerClient')->addNode($serviceName, $node, $weight, $connectTimeout);
    var_dump($data);
```

### 删除一个节点。

从注册服务器删除一个节点

```php
    $data = $this->getDI()->getShared('registerClient')->delNode($node);
    var_dump($data);
```

### 获取一个服务的所有节点

返回一个数组，包括指定服务下的所有节点及其权重。

```php
    $data = $this->getDI()->getShared('registerClient')->getNodes($serviceName);
    var_dump($data);

result:
Array
(
    [0] => Array
        (
            [createdDate] => 2018-04-03
            [serviceName] => order
            [name] => http://10.6.7.8:8012
            [desc] =>
            [weight] => 10
            [connectTimeout] => 23
        )

    [1] => Array
        (
            [createdDate] => 2018-04-03
            [serviceName] => order
            [name] => http://10.6.7.8:8013
            [desc] =>
            [weight] => 10
            [connectTimeout] => 23
        )

)
```
