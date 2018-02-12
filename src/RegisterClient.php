<?php
/**
 * 注册中心客户端
 */

namespace UniondrugRegister;

class RegisterClient extends Client
{
    /**
     * 注册一个Node
     *
     * @param string $serviceName 服务名称
     * @param string $upstream    服务地址
     * @param int    $weight      服务权重，0~20
     *
     * @return bool
     */
    public function addNode($serviceName, $upstream, $weight = 10)
    {
        $res = $this->cmd('node', 'add', $serviceName, $upstream, $weight)->recv();

        return $res->success;
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
        $res = $this->cmd('node', 'get', $serviceName)->recv(true);

        if ($res->success) {
            return $res->data[0];
        }

        return false;
    }

    /**
     * 获取一个服务下的所有Node
     *
     * @param string $serviceName 服务名称
     *
     * @return array
     */
    public function getNodes($serviceName)
    {
        $res = $this->cmd('node', $serviceName)->recv(true);

        $nodes = [];
        if ($res->success) {
            $data = $res->data;
            array_shift($data); // 表头移除
            foreach ($data as $row) {
                $cells = preg_split("/\s+/", $row);
                $nodes[] = $cells;
            }
        }

        return $nodes;
    }

    /**
     * 移除一个Node
     *
     * @param $serviceName
     * @param $upstream
     *
     * @return bool
     */
    public function delNode($serviceName, $upstream)
    {
        $res = $this->cmd('node', 'del', $serviceName, $upstream)->recv();

        return $res->success;
    }
}
