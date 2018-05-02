<?php
/**
 * 注册中心客户端
 */

namespace Uniondrug\Register;

use Uniondrug\Framework\Injectable;

class RegisterClient extends Injectable
{
    /**
     * 采集服务
     *
     * @var string
     */
    protected $service = null;

    /**
     * 投递超时
     *
     * @var int
     */
    protected $timeout = 1;

    /**
     * TraceClient constructor.
     */
    public function __construct()
    {
        if ($service = $this->config->path('register.service')) {
            $this->service = $service;
        }
        if ($timeout = $this->config->path('register.timeout', 30)) {
            $this->timeout = $timeout;
        }
    }

    /**
     * 注册一个Node
     *
     * @param string $serviceName 服务名称
     * @param        $name
     * @param int    $weight      服务权重，0~20
     * @param int    $connectTimeout
     *
     * @return bool
     */
    public function addNode($serviceName, $name, $weight = 8, $connectTimeout = 30)
    {
        $data = [
            'serviceName'    => $serviceName,
            'name'           => $name,
            'weight'         => $weight,
            'connectTimeout' => $connectTimeout,
        ];
        $res = $this->cmd('post', '/node/add', ['json' => $data]);

        return is_object($res);
    }

    /**
     * 获取一个Node
     *
     * @param string $serviceName 服务名称
     *
     * @return bool|string
     */
    public function getNode($serviceName)
    {
        $data = [
            'serviceName' => $serviceName,
        ];
        $res = $this->cmd('post', '/node/get', ['json' => $data]);
        if (is_object($res)) {
            return $res->name;
        }

        return false;
    }

    /**
     * 获取一个服务下的所有Node
     *
     * @param string $serviceName 服务名称
     *
     * @return array|bool
     */
    public function getNodes($serviceName)
    {
        $data = [
            'serviceName' => $serviceName,
            'limit'       => 50,
        ];
        $res = $this->cmd('get', '/node/list', ['query' => $data]);
        if ($res) {
            $list = [];
            foreach ($res->body as $item) {
                $list[] = (array) $item;
            }

            return $list;
        }

        return null;
    }

    /**
     * 移除一个Node
     *
     * @param $name
     *
     * @return bool
     */
    public function delNode($name)
    {
        $data = [
            'name' => $name,
        ];
        $res = $this->cmd('post', '/node/del', ['json' => $data]);

        return is_object($res);
    }

    /**
     * 通过HTTP方式发送
     *
     * @param $method
     * @param $path
     * @param $options
     *
     * @return bool
     */
    public function cmd($method, $path, $options = [])
    {
        /**
         * @var \GuzzleHttp\Client $client
         */
        if ('tcp' === strtolower(substr($this->service, 0, 3))) {
            if ($this->di->has('tcpClient')) {
                $client = $this->di->getShared('tcpClient');
            } else {
                $this->di->getLogger('register')->error(sprintf("[TraceClient] TcpClient not installed."));
                return false;
            }
        } else {
            if ($this->di->has('httpClient')) {
                $client = $this->di->getShared('httpClient');
            } else {
                $client = new \GuzzleHttp\Client();
            }
        }

        try {
            $options = array_merge($options, [
                'timeout' => $this->timeout,
            ]);
            $res = $client->$method($this->service . $path, $options);
            $json = (string) $res->getBody();
            $std = json_decode($json);
            if (json_last_error()) {
                $error = json_last_error_msg();
                throw new \Exception("invalid json response: $error", 400);
            }
            if (isset($std->errno) && 0 !== (int) $std->errno) {
                throw new \Exception($std->error, $std->errno);
            }
            if (isset($std->data)) {
                return $std->data;
            }

            return true;
        } catch (\Exception $e) {
            $this->di->getLogger('register')->error(sprintf("[RegisterClient] Send data to server failed: %s", $e->getMessage()));

            return false;
        }
    }
}
