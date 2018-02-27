# Register client for uniondrug/framework

## 安装

```shell
$ cd project-home
$ composer require uniondrug/register
$ cp vendor/uniondrug/register/register.php config/
```

修改 `app.php` 配置文件，加上RegisterClient服务

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
 * host: 注册中心服务器地址
 * port: 注册中心服务器端口
 * timeout: 连接超时时间，单位 秒，默认 30
 */
return [
    'default' => [
        'timeout' => 30,
        'host'    => '127.0.0.1',
        'port'    => 9530,
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
    $data = $this->getDI()->getShared('registerClient')->addNode($serviceName, $upstream, $weight);
    var_dump($data);
```

### 删除一个节点。

从注册服务器删除一个节点

```php
    $data = $this->getDI()->getShared('registerClient')->delNode($serviceName, $upstream);
    var_dump($data);
```

### 获取一个服务的所有节点

返回一个数组，包括指定服务下的所有节点及其权重。

```php
    $data = $this->getDI()->getShared('registerClient')->getNodes($serviceName);
    var_dump($data);
```
